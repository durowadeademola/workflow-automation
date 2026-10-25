#!/usr/bin/env bash
# One-time hardening + Docker install for a fresh OVHcloud VPS (Ubuntu 22.04/24.04).
# Run as root (or with sudo):
#   bash deploy/setup-server.sh <deploy-user>                    # SSH key login only
#   bash deploy/setup-server.sh <deploy-user> --allow-password   # keep password login
# Key mode refuses to run unless <deploy-user> already has an SSH key, so you
# can't lock yourself out.
set -euo pipefail

DEPLOY_USER=""
ALLOW_PASSWORD=0
for arg in "$@"; do
    case "$arg" in
        --allow-password) ALLOW_PASSWORD=1 ;;
        -*) echo "Unknown option: $arg"; exit 1 ;;
        *) DEPLOY_USER="$arg" ;;
    esac
done
[ -n "$DEPLOY_USER" ] || { echo "usage: setup-server.sh <deploy-user> [--allow-password]"; exit 1; }
[ "$(id -u)" -eq 0 ] || { echo "Run as root: sudo bash deploy/setup-server.sh $*"; exit 1; }

# Stop apt/needrestart from opening interactive "restart services?" dialogs
export DEBIAN_FRONTEND=noninteractive NEEDRESTART_MODE=a

echo "==> Updating packages..."
apt-get update && apt-get -y upgrade
apt-get install -y ca-certificates curl git ufw fail2ban unattended-upgrades

echo "==> Creating deploy user ${DEPLOY_USER}..."
if ! id "$DEPLOY_USER" >/dev/null 2>&1; then
    adduser --disabled-password --gecos "" "$DEPLOY_USER"
    usermod -aG sudo "$DEPLOY_USER"
    # Copy the invoking user's (or root's) authorized_keys so the same SSH key
    # works for the new user
    mkdir -p "/home/$DEPLOY_USER/.ssh"
    for src in "/home/${SUDO_USER:-}/.ssh/authorized_keys" /root/.ssh/authorized_keys; do
        if [ -s "$src" ]; then cp "$src" "/home/$DEPLOY_USER/.ssh/"; break; fi
    done
    chown -R "$DEPLOY_USER:$DEPLOY_USER" "/home/$DEPLOY_USER/.ssh"
    chmod 700 "/home/$DEPLOY_USER/.ssh"
fi

DEPLOY_HOME=$(getent passwd "$DEPLOY_USER" | cut -d: -f6)

if [ "$ALLOW_PASSWORD" -eq 1 ]; then
    # Password login needs the user to actually have a password ("P" status)
    if [ "$(passwd -S "$DEPLOY_USER" | awk '{print $2}')" != "P" ]; then
        echo "==> ${DEPLOY_USER} has no password yet — set one now (16+ random characters):"
        passwd "$DEPLOY_USER"
    fi
    PASSWORD_AUTH=yes
    echo "==> Hardening SSH (passwords allowed for ${DEPLOY_USER}, no root login)..."
else
    if [ ! -s "$DEPLOY_HOME/.ssh/authorized_keys" ]; then
        echo "ERROR: ${DEPLOY_USER} has no SSH key in $DEPLOY_HOME/.ssh/authorized_keys."
        echo "Disabling password login now would lock you out. Add a key first,"
        echo "or re-run with --allow-password."
        exit 1
    fi
    PASSWORD_AUTH=no
    echo "==> Hardening SSH (key-only, no root login)..."
fi

# sshd uses the FIRST value it reads for each setting, and Ubuntu's cloud image
# ships 50-cloud-init.conf — so this file must sort before it.
rm -f /etc/ssh/sshd_config.d/99-blueflow.conf
cat > /etc/ssh/sshd_config.d/01-blueflow.conf <<EOF
PasswordAuthentication ${PASSWORD_AUTH}
KbdInteractiveAuthentication no
PermitRootLogin no
MaxAuthTries 4
EOF
# 24.04 starts sshd via socket activation, so its runtime dir may not exist yet
mkdir -p /run/sshd
sshd -t
# sshd re-reads its config for every new connection, so a failed reload
# (e.g. socket-activated service not running) isn't fatal
systemctl reload ssh 2>/dev/null || systemctl reload sshd 2>/dev/null || true

echo "==> Firewall: 22, 80, 443 only..."
ufw default deny incoming
ufw default allow outgoing
ufw allow OpenSSH
ufw allow 80/tcp
ufw allow 443/tcp
ufw --force enable

echo "==> fail2ban + unattended security upgrades..."
# Ban an IP for 1 hour after 5 failed SSH logins within 10 minutes
cat > /etc/fail2ban/jail.local <<'EOF'
[sshd]
enabled  = true
maxretry = 5
findtime = 10m
bantime  = 1h
EOF
systemctl enable fail2ban
systemctl restart fail2ban
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
echo "  log out and back in (so the docker group applies)"
echo "  cd ~/blueflow && cp .env.example .env   # fill in production values"
echo "  bash deploy/deploy.sh --first-run"
