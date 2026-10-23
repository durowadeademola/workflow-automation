#!/usr/bin/env bash
# Nightly backup: MySQL dump + n8n data + .env, kept 14 days locally.
# Cron (as deploy user):  0 2 * * * /home/<user>/blueflow/deploy/backup.sh >> /home/<user>/backup.log 2>&1
#
# Local copies don't survive losing the VPS — set BACKUP_REMOTE to an rclone
# remote (e.g. OVH Object Storage, configured with `rclone config`) to also
# push them off-server.
set -euo pipefail

cd "$(dirname "$0")/.."

STAMP=$(date +%Y%m%d-%H%M)
DIR=backups
KEEP_DAYS=14
mkdir -p "$DIR"

set -a; . ./.env; set +a

echo "[$STAMP] Dumping MySQL..."
docker compose exec -T db mysqldump -uroot -p"$DB_ROOT_PASSWORD" \
    --single-transaction --routines --triggers "$DB_DATABASE" \
    | gzip > "$DIR/db-$STAMP.sql.gz"

echo "[$STAMP] Archiving n8n data + .env..."
tar czf "$DIR/n8n-$STAMP.tar.gz" n8n_data .env

find "$DIR" -type f -mtime +"$KEEP_DAYS" -delete

if [ -n "${BACKUP_REMOTE:-}" ]; then
    echo "[$STAMP] Syncing to $BACKUP_REMOTE..."
    rclone copy "$DIR" "$BACKUP_REMOTE"
fi

echo "[$STAMP] Backup done."
