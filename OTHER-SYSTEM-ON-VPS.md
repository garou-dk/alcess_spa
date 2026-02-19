# WasteXpress VPS Maintenance Guide

> **Last Updated:** February 17, 2026
> **VPS:** Hostinger KVM 2 — `72.61.148.153` — Ubuntu 24.04.3 LTS
> **Domain:** `cenro.online`

---

## Table of Contents

1. [Server Access](#1-server-access)
2. [Architecture Overview](#2-architecture-overview)
3. [Service Management](#3-service-management)
4. [Deploying Updates](#4-deploying-updates)
5. [Database Management](#5-database-management)
6. [User Management](#6-user-management)
7. [Storage Management](#7-storage-management)
8. [Logs & Monitoring](#8-logs--monitoring)
9. [SSL Certificates](#9-ssl-certificates)
10. [Backups](#10-backups)
11. [Troubleshooting](#11-troubleshooting)
12. [Important File Locations](#12-important-file-locations)
13. [Environment Variables](#13-environment-variables)
14. [Security Notes](#14-security-notes)

---

## 1. Server Access

### SSH into the VPS

```bash
ssh root@72.61.148.153
```

### Key Credentials

| Service | URL | Login |
|---------|-----|-------|
| **WasteXpress App** | https://cenro.online | User accounts |
| **Supabase Studio** | https://supabase.cenro.online | `admin` / Check DASHBOARD_PASSWORD in .env |
| **Admin Account** | https://cenro.online/login | `admin@wastexpress.ph` / `Password#123` |
| **Alcess SPA** | https://alcess.online | Separate system (untouched) |

---

## 2. Architecture Overview

```
Internet
    │
    ▼
┌─────────────────────────────────────────────┐
│               Nginx (ports 80/443)          │
│  ┌─────────────┬──────────────┬───────────┐ │
│  │cenro.online │supabase.     │alcess.    │ │
│  │             │cenro.online  │online     │ │
│  └──────┬──────┴──────┬───────┴─────┬─────┘ │
│         │             │             │       │
│    port 3001     port 8000     PHP/Laravel  │
│    (Next.js)     (Supabase)    (Alcess)     │
│    via PM2       via Docker                 │
└─────────────────────────────────────────────┘
```

### Services Running

| Service | Technology | Port | Manager |
|---------|-----------|------|---------|
| Next.js App | Node.js 20 | 3001 | PM2 |
| Supabase (13 containers) | Docker | 8000 (API), 8443, 5432, 6543 | Docker Compose |
| Alcess SPA | PHP/Laravel | N/A | Nginx + Supervisor |
| MariaDB (Alcess) | MariaDB | 3306 | systemd |

---

## 3. Service Management

### Next.js App (PM2)

```bash
# Check status
pm2 status

# Restart the app
pm2 restart waste-collection

# Stop the app
pm2 stop waste-collection

# Start the app
pm2 start waste-collection

# View real-time logs
pm2 logs waste-collection

# View error logs only
pm2 logs waste-collection --err --lines 50

# Monitor CPU/Memory in real-time
pm2 monit

# Save process list (important after changes)
pm2 save
```

### Supabase (Docker Compose)

```bash
cd /opt/supabase-project

# Check all container status
docker compose ps

# Restart ALL services
docker compose down && docker compose up -d

# Restart a SPECIFIC service (e.g., auth)
docker compose up -d --force-recreate auth

# View logs for a specific service
docker compose logs auth --tail 50
docker compose logs storage --tail 50
docker compose logs kong --tail 50
docker compose logs rest --tail 50

# Follow logs in real-time
docker compose logs -f auth

# Pull latest images (for Supabase upgrades)
docker compose pull
```

### Nginx

```bash
# Test configuration (ALWAYS do this before reloading!)
nginx -t

# Reload (apply config changes without downtime)
systemctl reload nginx

# Restart (full restart)
systemctl restart nginx

# Check status
systemctl status nginx

# View enabled sites
ls -la /etc/nginx/sites-enabled/
```

---

## 4. Deploying Updates

### Quick Deploy (⚡ Recommended)

```bash
cd /var/www/waste-collection
./deploy.sh
```

### Manual Deploy (Step by Step)

```bash
cd /var/www/waste-collection

# 1. Pull latest code from GitHub
git pull

# 2. Install dependencies (if package.json changed)
npm ci --production=false

# 3. Build the production bundle
npm run build

# 4. Copy static assets to standalone
cp -r public .next/standalone/public
cp -r .next/static .next/standalone/.next/static

# 5. Restart the app
pm2 restart waste-collection

# 6. Verify
pm2 status
curl -s -o /dev/null -w "%{http_code}\n" https://cenro.online
```

### Workflow: Local → GitHub → VPS

**On your local machine (Git Bash):**
```bash
cd /d/waste-collection-system
git add .
git commit -m "your commit message"
git push
```

**On the VPS:**
```bash
cd /var/www/waste-collection
./deploy.sh
```

### Deploying Database Migrations

```bash
cd /opt/supabase-project

# Copy migration file to VPS (from local Git Bash)
scp /d/waste-collection-system/supabase/migrations/033_new_migration.sql root@72.61.148.153:/opt/supabase-project/migrations/

# Run a single migration on VPS
docker compose exec -T db psql -U supabase_admin -d postgres < /opt/supabase-project/migrations/033_new_migration.sql

# Run ALL migrations (uses the script we created)
./run-migrations.sh
```

---

## 5. Database Management

### Access PostgreSQL Directly

```bash
cd /opt/supabase-project

# Interactive SQL shell
docker compose exec db psql -U supabase_admin -d postgres

# Run a single query
docker compose exec -e PAGER=cat db psql -U supabase_admin -d postgres -c "SELECT * FROM profiles LIMIT 5;"
```

### Common Queries

```sql
-- List all users with roles
SELECT id, email, role, status, created_at FROM profiles ORDER BY created_at DESC;

-- Count users by role
SELECT role, COUNT(*) FROM profiles GROUP BY role;

-- List all tables
\dt public.*

-- Check auth users
SELECT id, email, email_confirmed_at, created_at FROM auth.users;

-- Check table row counts
SELECT 'profiles' as table_name, COUNT(*) FROM profiles
UNION ALL
SELECT 'collection_requests', COUNT(*) FROM collection_requests
UNION ALL
SELECT 'notifications', COUNT(*) FROM notifications;
```

### Reset a User's Password

```bash
cd /opt/supabase-project

curl -X PUT "http://localhost:8000/auth/v1/admin/users/USER_ID_HERE" \
  -H "Authorization: Bearer $(grep SUPABASE_SERVICE_ROLE_KEY .env | cut -d'=' -f2-)" \
  -H "apikey: $(grep SUPABASE_SERVICE_ROLE_KEY .env | cut -d'=' -f2-)" \
  -H "Content-Type: application/json" \
  -d '{"password": "NewPassword123!"}'
```

---

## 6. User Management

### Create an Admin User

```bash
cd /opt/supabase-project

# Step 1: Create in Auth
curl -X POST 'http://localhost:8000/auth/v1/admin/users' \
  -H "Authorization: Bearer $(grep SERVICE_ROLE_KEY .env | head -1 | cut -d'=' -f2-)" \
  -H "apikey: $(grep SERVICE_ROLE_KEY .env | head -1 | cut -d'=' -f2-)" \
  -H "Content-Type: application/json" \
  -d '{
    "email": "newadmin@example.com",
    "password": "SecurePass123!",
    "email_confirm": true,
    "user_metadata": {
      "first_name": "New",
      "last_name": "Admin",
      "full_name": "New Admin",
      "role": "admin"
    }
  }'

# Step 2: Set role in profiles
docker compose exec db psql -U supabase_admin -d postgres -c "
  UPDATE profiles SET role = 'admin' WHERE email = 'newadmin@example.com';
"
```

### Change a User's Role

```bash
docker compose exec db psql -U supabase_admin -d postgres -c "
  UPDATE profiles SET role = 'staff' WHERE email = 'user@example.com';
"
```

Valid roles: `admin`, `staff`, `collector`, `client`

### Delete a User

```bash
docker compose exec db psql -U supabase_admin -d postgres -c "
  DELETE FROM profiles WHERE email = 'user@example.com';
  DELETE FROM auth.users WHERE email = 'user@example.com';
"
```

---

## 7. Storage Management

### Check Storage Buckets

```bash
cd /opt/supabase-project

curl -s 'http://localhost:8000/storage/v1/bucket' \
  -H "Authorization: Bearer $(grep SERVICE_ROLE_KEY .env | head -1 | cut -d'=' -f2-)" | python3 -m json.tool
```

### Create a New Bucket

```bash
curl -X POST 'http://localhost:8000/storage/v1/bucket' \
  -H "Authorization: Bearer $(grep SERVICE_ROLE_KEY .env | head -1 | cut -d'=' -f2-)" \
  -H "Content-Type: application/json" \
  -d '{
    "id": "bucket-name",
    "name": "bucket-name",
    "public": true,
    "file_size_limit": 10485760
  }'
```

### Check Storage Disk Usage

```bash
docker compose exec db psql -U supabase_admin -d postgres -c "
  SELECT bucket_id, COUNT(*) as files, pg_size_pretty(SUM(metadata->>'size')::bigint) as total_size
  FROM storage.objects
  GROUP BY bucket_id;
"
```

---

## 8. Logs & Monitoring

### Application Logs

```bash
# Next.js app logs (PM2)
pm2 logs waste-collection --lines 100
pm2 logs waste-collection --err --lines 50  # errors only

# Log files location
cat /var/log/waste-collection/out.log     # stdout
cat /var/log/waste-collection/error.log   # stderr
```

### Supabase Logs

```bash
cd /opt/supabase-project

# Auth service (login/signup issues)
docker compose logs auth --tail 50

# API gateway (request routing)
docker compose logs kong --tail 50

# Database
docker compose logs db --tail 50

# Storage (file uploads)
docker compose logs storage --tail 50

# Real-time (WebSocket)
docker compose logs realtime --tail 50

# ALL services
docker compose logs --tail 20
```

### Nginx Logs

```bash
# Access log
tail -50 /var/log/nginx/access.log

# Error log (most useful for debugging)
tail -50 /var/log/nginx/error.log
```

### System Monitoring

```bash
# Memory usage
free -h

# Disk usage
df -h

# CPU and processes
htop   # or: top

# Docker disk usage
docker system df

# All running containers
docker ps

# PM2 real-time monitor
pm2 monit
```

---

## 9. SSL Certificates

### Check Certificate Status

```bash
certbot certificates
```

### Renew Certificates

Certificates auto-renew via a systemd timer. To manually renew:

```bash
# Test renewal (dry run)
certbot renew --dry-run

# Force renewal
certbot renew --force-renewal

# Reload Nginx after renewal
systemctl reload nginx
```

### Add a New Subdomain

```bash
# 1. Add DNS A record pointing to 72.61.148.153
# 2. Create Nginx config in /etc/nginx/sites-available/
# 3. Enable it: ln -s /etc/nginx/sites-available/newsite /etc/nginx/sites-enabled/
# 4. Test: nginx -t
# 5. Reload: systemctl reload nginx
# 6. Get SSL: certbot --nginx -d newsubdomain.cenro.online
```

---

## 10. Backups

### Database Backup

```bash
# Create a backup
cd /opt/supabase-project
docker compose exec -e PAGER=cat db pg_dump -U supabase_admin -d postgres > /root/backups/db_backup_$(date +%Y%m%d_%H%M%S).sql

# Create backup directory if it doesn't exist
mkdir -p /root/backups
```

### Database Restore

```bash
# Restore from backup
docker compose exec -T db psql -U supabase_admin -d postgres < /root/backups/db_backup_20260217.sql
```

### Automated Daily Backup (Cron)

```bash
# Open crontab
crontab -e

# Add this line (daily backup at 2 AM, keep last 7 days):
0 2 * * * cd /opt/supabase-project && docker compose exec -T -e PAGER=cat db pg_dump -U supabase_admin -d postgres > /root/backups/db_backup_$(date +\%Y\%m\%d).sql && find /root/backups -name "db_backup_*.sql" -mtime +7 -delete
```

### Download Backup to Local Machine

```bash
# From your local Git Bash
scp root@72.61.148.153:/root/backups/db_backup_*.sql /d/backups/
```

---

## 11. Troubleshooting

### 🔴 502 Bad Gateway

**Cause:** Nginx can't reach the upstream service.

```bash
# Check if Next.js is running
pm2 status

# Check if Supabase is running
cd /opt/supabase-project && docker compose ps

# Check Nginx error log
tail -20 /var/log/nginx/error.log

# If "upstream sent too big header":
# Already fixed in /etc/nginx/conf.d/proxy-buffers.conf
```

### 🔴 App Not Loading / 500 Error

```bash
# Check app logs
pm2 logs waste-collection --err --lines 30

# Restart the app
pm2 restart waste-collection

# If persistent, do a clean rebuild
cd /var/www/waste-collection
rm -rf .next
npm run build
cp -r public .next/standalone/public
cp -r .next/static .next/standalone/.next/static
pm2 restart waste-collection
```

### 🔴 SMTP / Email Not Sending

```bash
cd /opt/supabase-project

# Check auth logs for SMTP errors
docker compose logs auth --tail 30 | grep -i smtp

# Verify SMTP settings
grep SMTP .env

# Recreate auth container (restart doesn't reload .env!)
docker compose up -d --force-recreate auth
```

### 🔴 DNS Not Resolving from Server

```bash
# Check /etc/hosts
cat /etc/hosts | grep cenro

# Should contain:
# 127.0.0.1 supabase.cenro.online cenro.online

# Test resolution
dig +short cenro.online
dig +short supabase.cenro.online
```

### 🔴 Docker Images Won't Pull (IPv6 Issue)

```bash
# The VPS has IPv6 issues. Force IPv4:
sysctl -w net.ipv6.conf.all.disable_ipv6=1
sysctl -w net.ipv6.conf.default.disable_ipv6=1

# Then retry
cd /opt/supabase-project && docker compose pull
```

### 🔴 Supabase Container Unhealthy

```bash
cd /opt/supabase-project

# Check which container is unhealthy
docker compose ps

# View its logs
docker compose logs <service_name> --tail 50

# Restart just that service
docker compose up -d --force-recreate <service_name>

# Nuclear option: restart everything
docker compose down && docker compose up -d
```

### 🔴 Out of Memory

```bash
# Check memory
free -h

# Check what's using memory
docker stats --no-stream

# Clear Docker cache
docker system prune -f

# If desperate, restart Docker
systemctl restart docker
```

### 🔴 Disk Space Running Low

```bash
# Check disk usage
df -h

# Find large files
du -sh /var/www/waste-collection/.next/
du -sh /opt/supabase-project/

# Clean Docker unused images
docker image prune -a -f

# Clean old logs
pm2 flush
truncate -s 0 /var/log/waste-collection/out.log
truncate -s 0 /var/log/waste-collection/error.log
```

### 🔴 Broken Images in Navbar

The Next.js `<Image>` component with image optimization can fail on self-hosted setups. Already fixed using `unoptimized` prop. If new image components have this issue, add `unoptimized` to the `<Image>` tag.

### 🔴 Alcess SPA Issues

The waste collection system should NOT affect Alcess. If Alcess has issues:

```bash
# Check Alcess Nginx config
cat /etc/nginx/sites-enabled/alcess

# Check Alcess services
supervisorctl status

# Alcess logs
tail -20 /var/log/nginx/error.log | grep alcess
```

---

## 12. Important File Locations

| What | Location |
|------|----------|
| **Next.js App** | `/var/www/waste-collection/` |
| **Next.js .env** | `/var/www/waste-collection/.env` |
| **PM2 Config** | `/var/www/waste-collection/ecosystem.config.js` |
| **Deploy Script** | `/var/www/waste-collection/deploy.sh` |
| **App Logs** | `/var/log/waste-collection/` |
| **Supabase Docker** | `/opt/supabase-project/` |
| **Supabase .env** | `/opt/supabase-project/.env` |
| **Supabase Compose** | `/opt/supabase-project/docker-compose.yml` |
| **Migrations** | `/opt/supabase-project/migrations/` |
| **Migration Runner** | `/opt/supabase-project/run-migrations.sh` |
| **Nginx Configs** | `/etc/nginx/sites-available/` |
| **Nginx Enabled** | `/etc/nginx/sites-enabled/` |
| **Nginx Buffers** | `/etc/nginx/conf.d/proxy-buffers.conf` |
| **SSL Certificates** | `/etc/letsencrypt/live/cenro.online/` |
| **Docker Daemon** | `/etc/docker/daemon.json` |
| **Hosts File** | `/etc/hosts` |
| **Backups** | `/root/backups/` |
| **Alcess SPA** | `/var/www/alcess/` |

---

## 13. Environment Variables

### Next.js App (.env) — `/var/www/waste-collection/.env`

| Variable | Description |
|----------|-------------|
| `NEXT_PUBLIC_SUPABASE_URL` | `https://supabase.cenro.online` |
| `NEXT_PUBLIC_SUPABASE_ANON_KEY` | Self-hosted anon key |
| `SUPABASE_SERVICE_ROLE_KEY` | Self-hosted service role key |
| `NEXT_PUBLIC_SITE_URL` | `https://cenro.online` |
| `KV_REST_API_URL` | Upstash Redis URL |
| `KV_REST_API_TOKEN` | Upstash Redis token |
| `REDIS_URL` | Redis connection string |
| `NEXT_PUBLIC_PUSHER_KEY` | Pusher key |
| `PUSHER_APP_ID` | Pusher app ID |
| `PUSHER_SECRET` | Pusher secret |
| `NEXT_PUBLIC_MAPBOX_ACCESS_TOKEN` | Mapbox token |

### Supabase (.env) — `/opt/supabase-project/.env`

| Variable | Description |
|----------|-------------|
| `POSTGRES_PASSWORD` | Database password |
| `JWT_SECRET` | JWT signing secret |
| `ANON_KEY` | Public anonymous key |
| `SERVICE_ROLE_KEY` | Admin service key |
| `DASHBOARD_USERNAME` | Studio login username |
| `DASHBOARD_PASSWORD` | Studio login password |
| `SITE_URL` | `https://cenro.online` |
| `API_EXTERNAL_URL` | `https://supabase.cenro.online` |
| `SMTP_HOST` | `smtp.gmail.com` |
| `SMTP_USER` | Gmail address |
| `SMTP_PASS` | Gmail app password |

> ⚠️ **IMPORTANT:** When changing Supabase `.env`, you must recreate containers:
> `docker compose up -d --force-recreate <service>` (NOT `restart`!)

> ⚠️ **IMPORTANT:** When changing `NEXT_PUBLIC_*` variables, you must **rebuild** the app
> because they are baked in at build time!

---

## 14. Security Notes

### Firewall

Only ports 22, 80, 443 are open. All Supabase internal ports (8000, 5432, 6543, etc.) are NOT accessible from the internet — they're only reachable through Nginx reverse proxy.

```bash
# Check firewall rules
ufw status
```

### Supabase Studio Access

The Supabase Studio dashboard at `https://supabase.cenro.online` is publicly accessible but protected by username/password. Consider restricting access to specific IPs if needed:

```bash
# In /etc/nginx/sites-available/supabase, add inside the server block:
# allow YOUR_IP;
# deny all;
```

### Keep Software Updated

```bash
# System updates
apt update && apt upgrade -y

# Node.js updates (use nvm if needed)
node -v

# Docker updates
apt update && apt install --only-upgrade docker-ce docker-ce-cli
```

### Rotate Gmail App Password

If compromised:
1. Go to https://myaccount.google.com/apppasswords
2. Revoke the old password
3. Create a new one
4. Update on VPS:
```bash
cd /opt/supabase-project
sed -i 's|SMTP_PASS=.*|SMTP_PASS=new_password_here|' .env
docker compose up -d --force-recreate auth
```

---

## Quick Reference Card 🗂️

```bash
# === SSH ===
ssh root@72.61.148.153

# === DEPLOY CODE ===
cd /var/www/waste-collection && ./deploy.sh

# === CHECK EVERYTHING ===
pm2 status && cd /opt/supabase-project && docker compose ps

# === VIEW LOGS ===
pm2 logs waste-collection --lines 30
cd /opt/supabase-project && docker compose logs auth --tail 20

# === RESTART EVERYTHING ===
pm2 restart waste-collection
cd /opt/supabase-project && docker compose down && docker compose up -d

# === BACKUP DATABASE ===
mkdir -p /root/backups
cd /opt/supabase-project && docker compose exec -T -e PAGER=cat db pg_dump -U supabase_admin -d postgres > /root/backups/db_backup_$(date +%Y%m%d).sql

# === MEMORY & DISK ===
free -h && df -h
```
