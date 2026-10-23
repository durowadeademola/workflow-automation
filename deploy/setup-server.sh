#!/usr/bin/env bash
# One-time hardening + Docker install for a fresh OVHcloud VPS (Ubuntu 22.04/24.04).
# Run as root:  bash deploy/setup-server.sh <deploy-user>
# Make sure your SSH key works for <deploy-user> BEFORE running — this disables
# password login.
set -euo pipefail

DEPLOY_USER="${1:?usage: setup-server.sh <deploy-user>}"

echo "==> Updating packages..."
apt-get update && apt-get -y upgrade
apt-get install -y ca-certificates curl git ufw fail2ban unattended-upgrades

echo "==> Creating deploy user ${DEPLOY_USER}..."
if ! id "$DEPLOY_USER" >/dev/null 2>&1; then
    adduser --disabled-password --gecos "" "$DEPLOY_USER"
    usermod -aG sudo "$DEPLOY_USER"
    # Copy root's authorized_keys so the same SSH key works for the new user
    mkdir -p "/home/$DEPLOY_USER/.ssh"
    cp /root/.ssh/authorized_keys "/home/$DEPLOY_USER/.ssh/" 2>/dev/null || true
    chown -R "$DEPLOY_USER:$DEPLOY_USER" "/home/$DEPLOY_USER/.ssh"
    chmod 700 "/home/$DEPLOY_USER/.ssh"
fi

echo "==> Hardening SSH (key-only, no root login)..."
cat > /etc/ssh/sshd_config.d/99-blueflow.conf <<'EOF'
PasswordAuthentication no
PermitRootLogin no
EOF
systemctl reload ssh || systemctl reload sshd

echo "==> Firewall: 22, 80, 443 only..."
ufw default deny incoming
ufw default allow outgoing
ufw allow OpenSSH
ufw allow 80/tcp
ufw allow 443/tcp
ufw --force enable

echo "==> fail2ban + unattended security upgrades..."
systemctl enable --now fail2ban
dpkg-reconfigure -f noninteractive unattended-upgrades

echo "==> 4GB swap (cushion for npm builds and the optional embedding model)..."
if ! swapon --show | grep -q /swapfile; then
    fallocate -l 4G /swapfile
    chmod 600 /swapfile
    mkswap /swapfile
    swapon /swapfile
    echo '/swapfile none swap sw 0 0' >> /etc/fstab
fi

echo "==> Installing Docker..."
if ! command -v docker >/dev/null; then
    curl -fsSL https://get.docker.com | sh
fi
usermod -aG docker "$DEPLOY_USER"

# Docker publishes ports by editing iptables directly, bypassing ufw. Only
# nginx publishes ports (80/443) in docker-compose.yml, so that's fine —
# just never add a `ports:` entry to db/n8n/embedding.

echo
echo "Done. Next, as ${DEPLOY_USER}:"
echo "  git clone <repo> ~/blueflow && cd ~/blueflow"
echo "  cp .env.example .env   # fill in production values"
echo "  bash deploy/deploy.sh --first-run"
