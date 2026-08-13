# Alcess Multi-Branch VPS Guide

> **Last Updated:** August 13, 2026
> **VPS Provider:** Hostinger KVM 2
> **VPS IP:** `72.61.142.94`
> **OS:** Ubuntu 24.04 LTS
> **SSH:** `ssh root@72.61.142.94`

---

## 📋 Branch Overview

| | GenSan | Davao | Cebu | CDO |
|---|---|---|---|---|
| **App Root** | `/var/www/alcess` | `/var/www/alcess-davao` | `/var/www/alcess-cebu` | `/var/www/alcess-cdo` |
| **URL (temp)** | `https://alcessgensan.online` | `http://72.61.142.94:8001` | `http://72.61.142.94:8002` | `http://72.61.142.94:8003` |
| **Database** | `alcess_db` | `alcess_davao_db` | `alcess_cebu_db` | `alcess_cdo_db` |
| **DB User** | `alcess_user` | `alcess_davao` | `alcess_cebu` | `alcess_cdo` |
| **DB Password** | `alcess_db` | `AlcessDavao@2026` | `AlcessCebu@2026` | `AlcessCDO@2026` |
| **Redis DB** | `0` (default) | `1` | `2` | `3` |
| **Soketi App ID** | `alcess` | `alcess-davao` | `alcess-cebu` | `alcess-cdo` |
| **Git Branch** | `deploy/gensan` | `deploy/davao` | `deploy/cebu` | `deploy/cdo` |
| **Worker** | `alcess-worker` | `alcess-davao-worker` | `alcess-cebu-worker` | `alcess-cdo-worker` |
| **Nginx Config** | `/etc/nginx/sites-available/alcess` | `…/alcess-davao` | `…/alcess-cebu` | `…/alcess-cdo` |
| **Supervisor Config** | `…/alcess-worker.conf` | `…/alcess-davao-worker.conf` | `…/alcess-cebu-worker.conf` | `…/alcess-cdo-worker.conf` |
| **Nginx Port (temp)** | 443 (SSL) | 8001 | 8002 | 8003 |

---

## 🔑 Access Credentials & Login Info

### 📊 Alcess Monitor (Central Analytics Dashboard)
| Property | Value |
|---|---|
| **URL** | `http://72.61.142.94:8080` |
| **Email** | `admin@alcess.online` |
| **Password** | `Admin@123` |
| **App Root** | `/var/www/alcess-monitor` |
| **Database** | `alcess_monitor_db` |
| **DB Username** | `alcess_monitor` |
| **DB Password** | `AlcessMonitor@2026` |
| **Nginx Config** | `/etc/nginx/sites-available/alcess-monitor` |

### 🏬 Branch Systems Admin Logins
| Branch | Web URL | Default Admin Email | Default Admin Password |
|---|---|---|---|
| **GenSan** | `https://alcessgensan.online` | `admin@gmail.com` | `Admin@123` |
| **Davao** | `http://72.61.142.94:8001` | `admin@gmail.com` | `Admin@123` |
| **Cebu** | `http://72.61.142.94:8002` | `admin@gmail.com` | `Admin@123` |
| **CDO** | `http://72.61.142.94:8003` | `admin@gmail.com` | `Admin@123` |

---

## 🚀 Deploying Updates

### Deploy a Single Branch

Replace `BRANCH` with the branch name (e.g., `alcess-davao`):

```bash
cd /var/www/BRANCH
git pull
composer install --optimize-autoloader --no-dev --no-interaction
npm install && npm run build
php artisan migrate --force
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
supervisorctl restart BRANCH-worker:*
```

**Examples:**

```bash
# Deploy Davao only
cd /var/www/alcess-davao
git pull
composer install --optimize-autoloader --no-dev --no-interaction
npm install && npm run build
php artisan migrate --force
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
supervisorctl restart alcess-davao-worker:*
```

```bash
# Quick deploy (frontend changes only)
cd /var/www/alcess-cebu
git pull
npm run build
```

```bash
# Quick deploy (backend changes only)
cd /var/www/alcess-cdo
git pull
composer install --optimize-autoloader --no-dev --no-interaction
php artisan migrate --force
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
supervisorctl restart alcess-cdo-worker:*
```

### Deploy ALL Branches at Once

```bash
/root/deploy-all.sh
```

---

## 🔀 Git Branch Management

### Branch Structure

```
main                 ← development (code changes happen here)
├── deploy/gensan    ← GenSan production
├── deploy/davao     ← Davao production
├── deploy/cebu      ← Cebu production
└── deploy/cdo       ← CDO production
```

### Push a Shared Update to ALL Branches

On your **local machine** (Git Bash):

```bash
cd /d/alcess_spa

# 1. Develop on main
git checkout main
# ... make changes ...
git add .
git commit -m "feat: your changes here"
git push

# 2. Merge main into each deployment branch
git checkout deploy/gensan && git merge main && git push
git checkout deploy/davao  && git merge main && git push
git checkout deploy/cebu   && git merge main && git push
git checkout deploy/cdo    && git merge main && git push

# 3. Go back to main
git checkout main
```

Then on the **VPS**:

```bash
/root/deploy-all.sh
```

### Push a Change to ONLY ONE Branch

On your **local machine**:

```bash
git checkout deploy/davao
# ... make Davao-specific changes ...
git add .
git commit -m "fix: davao-specific change"
git push
```

Then on the **VPS**:

```bash
cd /var/www/alcess-davao && git pull
# run full or quick deploy commands as needed
```

### Cherry-Pick a Fix from One Branch to Another

```bash
# Find the commit hash
git log deploy/davao --oneline -5
# Example output: abc1234 fix: order notification bug

# Apply that fix to Cebu
git checkout deploy/cebu
git cherry-pick abc1234
git push
```

### Merge Script (Optional)

Save this as `merge-all.sh` on your **local machine**:

```bash
#!/bin/bash
echo "=== Merging main into all deployment branches ==="

for branch in gensan davao cebu cdo; do
  echo ""
  echo "--- Merging into deploy/$branch ---"
  git checkout deploy/$branch
  git merge main --no-edit
  if [ $? -ne 0 ]; then
    echo "⚠️  MERGE CONFLICT on deploy/$branch! Resolve manually."
    exit 1
  fi
  git push
done

git checkout main
echo "✅ All branches updated and pushed!"
```

---

## 🔧 Service Management

### Check All Services at Once

```bash
echo "=== Service Status ==="
echo "Nginx:     $(systemctl is-active nginx)"
echo "PHP-FPM:   $(systemctl is-active php8.3-fpm)"
echo "MariaDB:   $(systemctl is-active mariadb)"
echo "Docker:    $(systemctl is-active docker)"
echo ""
echo "=== Docker Containers ==="
cd /var/www/alcess && docker compose ps --format "table {{.Name}}\t{{.Status}}"
echo ""
echo "=== Queue Workers ==="
supervisorctl status
echo ""
echo "=== Memory ==="
free -h | grep Mem
echo ""
echo "=== Disk ==="
df -h / | tail -1
echo ""
echo "=== Site Health Checks ==="
echo "GenSan:  $(curl -s -o /dev/null -w '%{http_code}' https://alcessgensan.online)"
echo "Davao:   $(curl -s -o /dev/null -w '%{http_code}' http://127.0.0.1:8001)"
echo "Cebu:    $(curl -s -o /dev/null -w '%{http_code}' http://127.0.0.1:8002)"
echo "CDO:     $(curl -s -o /dev/null -w '%{http_code}' http://127.0.0.1:8003)"
echo "=== ALL CHECKED ==="
```

### Nginx

```bash
systemctl status nginx          # Check status
nginx -t && systemctl reload nginx   # Test & reload config
systemctl restart nginx         # Full restart
tail -50 /var/log/nginx/error.log    # View error logs
```

### PHP-FPM

```bash
systemctl status php8.3-fpm
systemctl restart php8.3-fpm
```

### MariaDB

```bash
systemctl status mariadb
systemctl restart mariadb

# Access as root
mysql

# Access a specific branch database
mysql -u alcess_davao -p alcess_davao_db
# Password: AlcessDavao@2026
```

### Docker (Redis + Soketi)

```bash
cd /var/www/alcess
docker compose ps              # Check containers
docker compose restart soketi   # Restart Soketi
docker compose logs soketi      # View Soketi logs
redis-cli ping                  # Test Redis (runs as system service)
```

### Supervisor (Queue Workers)

```bash
supervisorctl status                              # Check all workers
supervisorctl restart alcess-davao-worker:*       # Restart one branch
supervisorctl restart all                          # Restart all workers
supervisorctl reread && supervisorctl update       # After editing .conf files

# View worker logs
tail -50 /var/www/alcess-davao/storage/logs/worker.log
```

---

## 🗄️ Database Operations

### Backup All Databases

```bash
mkdir -p /root/backups
mysqldump -u root alcess_db > /root/backups/alcess_gensan_$(date +%Y%m%d_%H%M%S).sql
mysqldump -u root alcess_davao_db > /root/backups/alcess_davao_$(date +%Y%m%d_%H%M%S).sql
mysqldump -u root alcess_cebu_db > /root/backups/alcess_cebu_$(date +%Y%m%d_%H%M%S).sql
mysqldump -u root alcess_cdo_db > /root/backups/alcess_cdo_$(date +%Y%m%d_%H%M%S).sql
ls -lh /root/backups/
```

### Restore a Database

```bash
mysql -u root alcess_davao_db < /root/backups/alcess_davao_20260813.sql
```

### Fresh Reset (⚠️ Deletes ALL Data)

```bash
cd /var/www/alcess-davao
php artisan migrate:fresh --seed --force
```
Default admin after reset: `admin@gmail.com` / `Admin@123`

### Useful Queries

```bash
# Count rows across all branches
for db in alcess_db alcess_davao_db alcess_cebu_db alcess_cdo_db; do
  echo "=== $db ==="
  mysql -u root $db -e "
    SELECT 'users' AS tbl, COUNT(*) AS total FROM users
    UNION ALL SELECT 'orders', COUNT(*) FROM orders
    UNION ALL SELECT 'sales', COUNT(*) FROM sales
    UNION ALL SELECT 'products', COUNT(*) FROM products;"
  echo ""
done
```

---

## 📁 File Permissions Fix

If any branch shows permission errors:

```bash
# Replace BRANCH with: alcess, alcess-davao, alcess-cebu, or alcess-cdo
chown -R www-data:www-data /var/www/BRANCH
chmod -R 775 /var/www/BRANCH/storage
chmod -R 775 /var/www/BRANCH/bootstrap/cache
```

Or fix all at once:

```bash
for site in alcess alcess-davao alcess-cebu alcess-cdo; do
  chown -R www-data:www-data /var/www/$site
  chmod -R 775 /var/www/$site/storage
  chmod -R 775 /var/www/$site/bootstrap/cache
done
```

---

## 🔒 SSL Certificates

GenSan already has SSL. For new branches (when domains are ready):

```bash
certbot --nginx -d yourdomain.com -d www.yourdomain.com
certbot certificates      # Check all certs
certbot renew --dry-run    # Test auto-renewal
```

---

## 🔥 Firewall

### Current Rules

| Port | Service |
|---|---|
| 22 | SSH |
| 80 | HTTP |
| 443 | HTTPS (GenSan SSL) |
| 8001 | Alcess Davao (temporary) |
| 8002 | Alcess Cebu (temporary) |
| 8003 | Alcess CDO (temporary) |
| 8080 | Alcess Monitor (temporary) |

```bash
ufw status verbose
```

### When Domains Are Ready — Close Temp Ports

```bash
ufw delete allow 8001/tcp
ufw delete allow 8002/tcp
ufw delete allow 8003/tcp
ufw delete allow 8080/tcp
ufw reload
```

Also remove from Hostinger Panel Firewall (hpanel → VPS → Firewall).

---

## 🌐 Domain Cutover (When Ready)

When domains are purchased and DNS A records point to `72.61.142.94`:

### Step 1 — Update Nginx Config

For each branch, edit `/etc/nginx/sites-available/alcess-BRANCH` and replace the port-based config:

**Change FROM:**
```nginx
listen 8001;
listen [::]:8001;
server_name _;
```

**Change TO:**
```nginx
listen 80;
listen [::]:80;
server_name yourdomain.com www.yourdomain.com;
```

### Step 2 — Test & Reload Nginx

```bash
nginx -t && systemctl reload nginx
```

### Step 3 — Get SSL

```bash
certbot --nginx -d yourdomain.com -d www.yourdomain.com
```

### Step 4 — Update `.env`

```bash
nano /var/www/alcess-BRANCH/.env
```

Change:
```env
APP_URL=https://yourdomain.com
VITE_API_URL="https://yourdomain.com/api"
VITE_APP_URL="https://yourdomain.com"
VITE_PUSHER_HOST=yourdomain.com
VITE_PUSHER_PORT=443
VITE_PUSHER_SCHEME=https
```

### Step 5 — Rebuild Frontend

`VITE_*` variables are baked at build time, so you must rebuild:

```bash
cd /var/www/alcess-BRANCH
npm run build
php artisan optimize:clear
php artisan config:cache
```

### Step 6 — Close Temp Ports

See Firewall section above.

---

## 🔄 After VPS Reboot

Docker containers and workers don't auto-start. Run:

```bash
cd /var/www/alcess
docker compose up -d
supervisorctl restart all
```

---

## 🛠️ Troubleshooting

### Site Shows 502 Bad Gateway

```bash
systemctl restart php8.3-fpm
systemctl restart nginx
```

### Site Shows 500 Error

```bash
# Check logs for the affected branch
tail -50 /var/www/alcess-BRANCH/storage/logs/laravel.log

# Fix permissions
chown -R www-data:www-data /var/www/alcess-BRANCH/storage
chmod -R 775 /var/www/alcess-BRANCH/storage

# Clear caches
cd /var/www/alcess-BRANCH
php artisan optimize:clear
```

### Redis Connection Refused

```bash
redis-cli ping    # Should return PONG
# If not running:
systemctl restart redis-server
```

### WebSocket (Soketi) Not Working

```bash
cd /var/www/alcess
docker compose logs soketi
docker compose restart soketi
```

### Queue Jobs Not Processing

```bash
supervisorctl status
supervisorctl restart alcess-BRANCH-worker:*

# Process one job manually (for debugging)
cd /var/www/alcess-BRANCH
php artisan queue:work --once
```

### Wrong Data Showing on a Branch

Check that each branch's `.env` has the **correct Redis DB index**:
- GenSan: `REDIS_DB=0`
- Davao: `REDIS_DB=1`
- Cebu: `REDIS_DB=2`
- CDO: `REDIS_DB=3`

If you changed it, clear the cache:
```bash
cd /var/www/alcess-BRANCH
php artisan optimize:clear
php artisan config:cache
```

---

## 📊 Soketi Configuration

The shared Soketi config is at `/var/www/alcess/soketi.json`:

```json
{
    "debug": true,
    "host": "0.0.0.0",
    "port": 6001,
    "appManager.array.apps": [
        { "id": "alcess",       "key": "Alcess",      "secret": "MadeByDnscStudents@DNSC1234" },
        { "id": "alcess-davao", "key": "AlcessDavao",  "secret": "AlcessDavaoSecret@2026" },
        { "id": "alcess-cebu",  "key": "AlcessCebu",   "secret": "AlcessCebuSecret@2026" },
        { "id": "alcess-cdo",   "key": "AlcessCDO",    "secret": "AlcessCDOSecret@2026" }
    ]
}
```

After editing, restart Soketi:
```bash
cd /var/www/alcess && docker compose restart soketi
```

---

## 📧 Email Configuration

All branches share the same Gmail account. The `APP_NAME` in each `.env` differentiates the sender name.

| Setting | Value |
|---|---|
| **SMTP Host** | smtp.gmail.com |
| **Port** | 587 |
| **Email** | alcessdavao@gmail.com |
| **App Password** | stored in each `.env` as `MAIL_PASSWORD` |

---

## 📋 Software Versions

| Software | Version |
|---|---|
| Ubuntu | 24.04 LTS |
| PHP | 8.3.6 |
| Composer | 2.10.1 |
| Node.js | 20.20.2 |
| NPM | 10.8.2 |
| MariaDB | 10.11.14 |
| Docker | 29.5.3 |
| Nginx | latest |
| Laravel | 12.17.0 |
| Redis | system service |
| Soketi | 1.4-16 (Docker) |
