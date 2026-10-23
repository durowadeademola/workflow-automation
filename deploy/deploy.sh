#!/usr/bin/env bash
# Deploy/update Blueflow on the VPS. Run from the repo root as the deploy user.
#   bash deploy/deploy.sh               # normal update: pull, rebuild, migrate
#   bash deploy/deploy.sh --first-run   # also issues the Let's Encrypt cert
set -euo pipefail

cd "$(dirname "$0")/.."

DOMAIN=blueflowautomation.com
CERT_EMAIL="${CERT_EMAIL:-hello@blueflowautomation.com}"

[ -f .env ] || { echo "Missing .env — copy .env.example and fill it in."; exit 1; }
grep -q '^APP_DEBUG=false' .env || echo "WARNING: APP_DEBUG is not false in .env"

if [ "${1:-}" = "--first-run" ]; then
    echo "==> Issuing SSL certificate (DNS for $DOMAIN, www and n8n must already point here)..."
    mkdir -p certbot/conf certbot/www n8n_data
    # n8n runs as uid 1000 inside its container
    sudo chown -R 1000:1000 n8n_data

    # nginx won't start without the cert files, so get them with certbot's own
    # standalone server on port 80 first.
    docker run --rm -p 80:80 \
        -v "$PWD/certbot/conf:/etc/letsencrypt" \
        -v "$PWD/certbot/www:/var/www/certbot" \
        certbot/certbot certonly --standalone --non-interactive --agree-tos \
        -m "$CERT_EMAIL" \
        -d "$DOMAIN" -d "www.$DOMAIN" -d "n8n.$DOMAIN"
else
    echo "==> Pulling latest code..."
    git pull --ff-only
fi

echo "==> Building images..."
docker compose build

echo "==> Starting containers..."
docker compose up -d --remove-orphans

echo "==> Waiting for the app container to finish its build step..."
for i in $(seq 1 60); do
    if docker compose exec -T app pgrep php-fpm >/dev/null 2>&1; then break; fi
    sleep 5
done

if ! grep -q '^APP_KEY=base64' .env; then
    echo "==> Generating APP_KEY..."
    docker compose exec -T app php artisan key:generate --force
    docker compose exec -T app php artisan config:cache
fi

# The entrypoint only caches config when the app container starts, and editing
# .env doesn't restart it — re-cache so .env changes (e.g. EMBEDDING_SERVER_URL)
# take effect.
echo "==> Refreshing config cache..."
docker compose exec -T app php artisan config:cache

echo "==> Running migrations..."
docker compose exec -T app php artisan migrate --force
docker compose exec -T app php artisan storage:link || true

echo "==> Restarting queue worker + scheduler to pick up new code..."
docker compose restart queue scheduler

docker compose ps
echo "Deployed."
