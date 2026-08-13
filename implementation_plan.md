# Deploy 3 Alcess Branch Clones + Monitoring Dashboard on the Same VPS

> **Goal:** Clone the existing Alcess GenSan system to create 3 new instances — **Davao**, **Cebu**, and **CDO** — all running on the same VPS (72.61.142.94). Set up everything **without domains first** (access via IP + port), then add domains later. Also build a **central monitoring dashboard** for the owner to view combined analytics across all branches.

## Architecture Overview

```
VPS 72.61.142.94 (Hostinger KVM 2 — 8GB RAM, 100GB Disk, Ubuntu 24.04)
│
├── Nginx (reverse proxy)
│     ├── :443  alcessgensan.online  → /var/www/alcess           (existing — GenSan)
│     ├── :8001 (temp, no domain)   → /var/www/alcess-davao     (NEW)
│     ├── :8002 (temp, no domain)   → /var/www/alcess-cebu      (NEW)
│     ├── :8003 (temp, no domain)   → /var/www/alcess-cdo       (NEW)
│     └── :8080 (temp, no domain)   → /var/www/alcess-monitor   (NEW — Dashboard)
│
│     ↓ (later, when domains are ready)
│     ├── :443  alcess-davao.online  → /var/www/alcess-davao
│     ├── :443  alcess-cebu.online   → /var/www/alcess-cebu
│     ├── :443  alcess-cdo.online    → /var/www/alcess-cdo
│     └── :443  monitor.alcess.online→ /var/www/alcess-monitor
│
├── MariaDB (port 3306)
│     ├── alcess_db         (existing — GenSan)
│     ├── alcess_davao_db   (NEW)
│     ├── alcess_cebu_db    (NEW)
│     ├── alcess_cdo_db     (NEW)
│     └── alcess_monitor_db (NEW — dashboard users/sessions only)
│
├── Docker Containers (shared)
│     ├── Redis (port 6379) — isolated by Redis DB index (0–3)
│     └── Soketi (port 6001) — isolated by unique app IDs
│
└── Supervisor
      ├── alcess-worker         (existing — GenSan)
      ├── alcess-davao-worker   (NEW)
      ├── alcess-cebu-worker    (NEW)
      └── alcess-cdo-worker     (NEW)
```

---

## User Review Required

> [!IMPORTANT]
> **No Domains Needed to Start:** We'll use IP + port access (e.g., `http://72.61.142.94:8001`) for initial setup and testing. Domains/SSL are added later as a simple cutover step.

> [!IMPORTANT]
> **Firewall Update Needed:** Ports `8001`, `8002`, `8003`, and `8080` must be opened in **both** UFW and the Hostinger panel firewall before the port-based sites are accessible. These ports will be closed again once domains are set up.

> [!WARNING]
> **Resource Planning:** Running 5 Laravel instances (4 branches + 1 monitor) on 8GB RAM / 2 cores is feasible but tight. The monitoring dashboard is lightweight (read-only queries, no queue worker). Monitor memory with `free -h` after setup.

---

## Open Questions

> [!IMPORTANT]
> 1. **Monitoring Dashboard — Who logs in?** Is this only the owner, or multiple admin users? This affects whether we need role-based access.
> 2. **What analytics does the dashboard need?** I'm planning for: total sales (daily/weekly/monthly), inventory levels, order counts/status, and revenue — all per-branch and combined. Anything else?
> 3. **Email per branch?** Will each branch have its own Gmail + App Password, or share `alcessdavao@gmail.com`?
> 4. **Branch display names?** "Alcess Davao", "Alcess Cebu", "Alcess CDO" — correct?

---

## Part 1: Git Branch Strategy

> **Strategy: Git Branches — one deploy branch per site**

### Branch Structure

```
main                 ← core codebase (development happens here)
├── deploy/gensan    ← GenSan production deployment
├── deploy/davao     ← Davao production deployment
├── deploy/cebu      ← Cebu production deployment
└── deploy/cdo       ← CDO production deployment
```

> The monitoring dashboard will be a **separate repo** (or a `monitor/` folder on `main`) since it's a different application.

### Step 1 — Create Deployment Branches (on your local machine)

```bash
cd /d/alcess_spa

# Make sure main is up to date
git checkout main
git pull

# Create deployment branches from main
git checkout -b deploy/gensan
git push -u origin deploy/gensan

git checkout main
git checkout -b deploy/davao
git push -u origin deploy/davao

git checkout main
git checkout -b deploy/cebu
git push -u origin deploy/cebu

git checkout main
git checkout -b deploy/cdo
git push -u origin deploy/cdo

# Go back to main for daily development
git checkout main
```

### Step 2 — Daily Git Workflow

#### Scenario A: Push a shared update to ALL branches

```bash
# 1. Develop on main
git checkout main
# ... make changes ...
git add .
git commit -m "feat: add new report feature"
git push

# 2. Merge main into each deployment branch
git checkout deploy/gensan && git merge main && git push
git checkout deploy/davao  && git merge main && git push
git checkout deploy/cebu   && git merge main && git push
git checkout deploy/cdo    && git merge main && git push

# 3. Go back to main
git checkout main

# 4. Deploy on VPS (SSH in, then for each site):
# ssh root@72.61.142.94
# cd /var/www/alcess && git pull
# cd /var/www/alcess-davao && git pull
# ... etc
```

#### Scenario B: Push a change to ONLY ONE branch (e.g., Davao)

```bash
git checkout deploy/davao
# ... make Davao-specific changes ...
git add .
git commit -m "fix: davao-specific pricing"
git push

# Deploy only Davao on VPS:
# cd /var/www/alcess-davao && git pull
```

#### Scenario C: Cherry-pick a fix from one branch to another

```bash
# Get the commit hash from Davao
git log deploy/davao --oneline -5
# Example: abc1234 fix: order notification bug

# Apply to Cebu
git checkout deploy/cebu
git cherry-pick abc1234
git push
```

#### Helper Script — Merge main into all branches at once

Create `merge-all.sh` on your **local machine**:

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

```bash
chmod +x merge-all.sh
```

---

## Part 2: VPS Server Setup (SSH Commands)

> All commands below are run on the VPS: `ssh root@72.61.142.94`

---

### Phase 1: Open Temporary Ports (Firewall)

Since we're not using domains yet, we need to open ports for browser access:

```bash
# Open ports in UFW
ufw allow 8001/tcp comment "Alcess Davao (temp)"
ufw allow 8002/tcp comment "Alcess Cebu (temp)"
ufw allow 8003/tcp comment "Alcess CDO (temp)"
ufw allow 8080/tcp comment "Alcess Monitor (temp)"
ufw reload
ufw status
```

Also open the same ports in **Hostinger Panel Firewall**:
1. Go to [hpanel.hostinger.com](https://hpanel.hostinger.com) → VPS → Firewall
2. Edit the `alcess-firewall` rule
3. Add TCP ports: `8001`, `8002`, `8003`, `8080`
4. Save

---

### Phase 2: Create Databases & Users

```bash
mysql
```

```sql
-- Davao
CREATE DATABASE alcess_davao_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'alcess_davao'@'localhost' IDENTIFIED BY 'AlcessDavao@2026';
GRANT ALL PRIVILEGES ON alcess_davao_db.* TO 'alcess_davao'@'localhost';

-- Cebu
CREATE DATABASE alcess_cebu_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'alcess_cebu'@'localhost' IDENTIFIED BY 'AlcessCebu@2026';
GRANT ALL PRIVILEGES ON alcess_cebu_db.* TO 'alcess_cebu'@'localhost';

-- CDO
CREATE DATABASE alcess_cdo_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'alcess_cdo'@'localhost' IDENTIFIED BY 'AlcessCDO@2026';
GRANT ALL PRIVILEGES ON alcess_cdo_db.* TO 'alcess_cdo'@'localhost';

-- Monitor Dashboard (its own small DB for login/sessions only)
CREATE DATABASE alcess_monitor_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'alcess_monitor'@'localhost' IDENTIFIED BY 'AlcessMonitor@2026';
GRANT ALL PRIVILEGES ON alcess_monitor_db.* TO 'alcess_monitor'@'localhost';

-- Give monitor READ-ONLY access to all branch databases
GRANT SELECT ON alcess_db.* TO 'alcess_monitor'@'localhost';
GRANT SELECT ON alcess_davao_db.* TO 'alcess_monitor'@'localhost';
GRANT SELECT ON alcess_cebu_db.* TO 'alcess_monitor'@'localhost';
GRANT SELECT ON alcess_cdo_db.* TO 'alcess_monitor'@'localhost';

FLUSH PRIVILEGES;
EXIT;
```

> [!TIP]
> The `alcess_monitor` user has **SELECT-only** access to all 4 branch databases. It can read but never modify branch data — safe by design.

---

### Phase 3: Clone the Application (3 Branches)

#### Davao

```bash
cd /var/www
git clone -b deploy/davao https://github.com/YOUR_GITHUB_USERNAME/alcess_spa.git alcess-davao
cd alcess-davao
composer install --optimize-autoloader --no-dev --no-interaction
npm install && npm run build
chown -R www-data:www-data /var/www/alcess-davao
chmod -R 775 /var/www/alcess-davao/storage
chmod -R 775 /var/www/alcess-davao/bootstrap/cache
```

#### Cebu

```bash
cd /var/www
git clone -b deploy/cebu https://github.com/YOUR_GITHUB_USERNAME/alcess_spa.git alcess-cebu
cd alcess-cebu
composer install --optimize-autoloader --no-dev --no-interaction
npm install && npm run build
chown -R www-data:www-data /var/www/alcess-cebu
chmod -R 775 /var/www/alcess-cebu/storage
chmod -R 775 /var/www/alcess-cebu/bootstrap/cache
```

#### CDO

```bash
cd /var/www
git clone -b deploy/cdo https://github.com/YOUR_GITHUB_USERNAME/alcess_spa.git alcess-cdo
cd alcess-cdo
composer install --optimize-autoloader --no-dev --no-interaction
npm install && npm run build
chown -R www-data:www-data /var/www/alcess-cdo
chmod -R 775 /var/www/alcess-cdo/storage
chmod -R 775 /var/www/alcess-cdo/bootstrap/cache
```

---

### Phase 4: Configure Environment Files (.env)

> Each branch gets a unique `.env`. The key differences are: database, Redis DB index, Soketi app, and URLs.

#### 4A — Davao `.env`

```bash
cp /var/www/alcess-davao/.env.production.example /var/www/alcess-davao/.env
nano /var/www/alcess-davao/.env
```

```env
APP_NAME="Alcess Davao"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=http://72.61.142.94:8001

APP_LOCALE=en
APP_FALLBACK_LOCALE=en
APP_FAKER_LOCALE=en_US

APP_MAINTENANCE_DRIVER=file
PHP_CLI_SERVER_WORKERS=4
BCRYPT_ROUNDS=12

LOG_CHANNEL=daily
LOG_STACK=single
LOG_LEVEL=error

# Database
DB_CONNECTION=mariadb
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=alcess_davao_db
DB_USERNAME=alcess_davao
DB_PASSWORD=AlcessDavao@2026

SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=null

# Cache & Queue
BROADCAST_CONNECTION=pusher
FILESYSTEM_DISK=local
QUEUE_CONNECTION=redis
CACHE_STORE=redis

# Redis — DB index 1 (GenSan uses 0)
REDIS_CLIENT=phpredis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
REDIS_DB=1
REDIS_CACHE_DB=1
REDIS_QUEUE_DB=1

MAIL_MAILER=smtp
MAIL_SCHEME=null
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME="alcessdavao@gmail.com"
MAIL_PASSWORD="your_davao_app_password"
MAIL_FROM_ADDRESS="alcessdavao@gmail.com"
MAIL_FROM_NAME="${APP_NAME}"

FFMPEG=/usr/bin/ffmpeg
FFPROBE=/usr/bin/ffprobe

# Soketi — unique app ID per branch
PUSHER_APP_ID=alcess-davao
PUSHER_APP_KEY="AlcessDavao"
PUSHER_APP_SECRET="AlcessDavaoSecret@2026"
PUSHER_APP_CLUSTER=ap1
PUSHER_HOST=127.0.0.1
PUSHER_PORT=6001
PUSHER_SCHEME=http

# Frontend — use IP:port for now (change to domain later)
VITE_APP_NAME="${APP_NAME}"
VITE_API_URL="http://72.61.142.94:8001/api"
VITE_APP_URL="http://72.61.142.94:8001"

VITE_PUSHER_APP_KEY="${PUSHER_APP_KEY}"
VITE_PUSHER_APP_CLUSTER="${PUSHER_APP_CLUSTER}"
VITE_PUSHER_HOST=72.61.142.94
VITE_PUSHER_PORT=6001
VITE_PUSHER_SCHEME=http
```

```bash
cd /var/www/alcess-davao
php artisan key:generate
```

#### 4B — Cebu `.env`

Same structure as Davao, but change these values:

| Variable | Cebu Value |
|---|---|
| `APP_NAME` | `"Alcess Cebu"` |
| `APP_URL` | `http://72.61.142.94:8002` |
| `DB_DATABASE` | `alcess_cebu_db` |
| `DB_USERNAME` | `alcess_cebu` |
| `DB_PASSWORD` | `AlcessCebu@2026` |
| `REDIS_DB` | `2` |
| `REDIS_CACHE_DB` | `2` |
| `REDIS_QUEUE_DB` | `2` |
| `PUSHER_APP_ID` | `alcess-cebu` |
| `PUSHER_APP_KEY` | `AlcessCebu` |
| `PUSHER_APP_SECRET` | `AlcessCebuSecret@2026` |
| `VITE_API_URL` | `http://72.61.142.94:8002/api` |
| `VITE_APP_URL` | `http://72.61.142.94:8002` |

```bash
cp /var/www/alcess-cebu/.env.production.example /var/www/alcess-cebu/.env
nano /var/www/alcess-cebu/.env
# Paste and adjust with values from table above
cd /var/www/alcess-cebu && php artisan key:generate
```

#### 4C — CDO `.env`

| Variable | CDO Value |
|---|---|
| `APP_NAME` | `"Alcess CDO"` |
| `APP_URL` | `http://72.61.142.94:8003` |
| `DB_DATABASE` | `alcess_cdo_db` |
| `DB_USERNAME` | `alcess_cdo` |
| `DB_PASSWORD` | `AlcessCDO@2026` |
| `REDIS_DB` | `3` |
| `REDIS_CACHE_DB` | `3` |
| `REDIS_QUEUE_DB` | `3` |
| `PUSHER_APP_ID` | `alcess-cdo` |
| `PUSHER_APP_KEY` | `AlcessCDO` |
| `PUSHER_APP_SECRET` | `AlcessCDOSecret@2026` |
| `VITE_API_URL` | `http://72.61.142.94:8003/api` |
| `VITE_APP_URL` | `http://72.61.142.94:8003` |

```bash
cp /var/www/alcess-cdo/.env.production.example /var/www/alcess-cdo/.env
nano /var/www/alcess-cdo/.env
cd /var/www/alcess-cdo && php artisan key:generate
```

> [!TIP]
> **Redis DB Isolation:** GenSan=`0` (default), Davao=`1`, Cebu=`2`, CDO=`3`. This keeps each branch's cache and queue jobs completely separate even though they share one Redis server.

---

### Phase 5: Update Soketi Config (Add New Apps)

```bash
nano /var/www/alcess/soketi.json
```

Replace with:

```json
{
    "debug": true,
    "host": "0.0.0.0",
    "port": 6001,
    "appManager.array.apps": [
        {
            "id": "alcess",
            "key": "Alcess",
            "secret": "MadeByDnscStudents@DNSC1234"
        },
        {
            "id": "alcess-davao",
            "key": "AlcessDavao",
            "secret": "AlcessDavaoSecret@2026"
        },
        {
            "id": "alcess-cebu",
            "key": "AlcessCebu",
            "secret": "AlcessCebuSecret@2026"
        },
        {
            "id": "alcess-cdo",
            "key": "AlcessCDO",
            "secret": "AlcessCDOSecret@2026"
        }
    ]
}
```

```bash
cd /var/www/alcess
docker compose restart soketi
```

---

### Phase 6: Run Migrations & Setup Storage

```bash
# --- Davao ---
cd /var/www/alcess-davao
php artisan migrate --force
php artisan db:seed --force
php artisan storage:link
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

# --- Cebu ---
cd /var/www/alcess-cebu
php artisan migrate --force
php artisan db:seed --force
php artisan storage:link
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

# --- CDO ---
cd /var/www/alcess-cdo
php artisan migrate --force
php artisan db:seed --force
php artisan storage:link
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

### Phase 7: Nginx — Port-Based Setup (No Domains)

#### 7A — Davao (port 8001)

```bash
nano /etc/nginx/sites-available/alcess-davao
```

```nginx
server {
    listen 8001;
    listen [::]:8001;

    server_name _;
    root /var/www/alcess-davao/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;
    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }

    # WebSocket proxy (Soketi)
    location /app/ {
        proxy_pass http://127.0.0.1:6001;
        proxy_http_version 1.1;
        proxy_set_header Upgrade $http_upgrade;
        proxy_set_header Connection "Upgrade";
        proxy_set_header Host $host;
    }
}
```

```bash
ln -s /etc/nginx/sites-available/alcess-davao /etc/nginx/sites-enabled/
```

#### 7B — Cebu (port 8002)

```bash
nano /etc/nginx/sites-available/alcess-cebu
```

Same as Davao config but change:
- `listen 8002;` and `listen [::]:8002;`
- `root /var/www/alcess-cebu/public;`

```bash
ln -s /etc/nginx/sites-available/alcess-cebu /etc/nginx/sites-enabled/
```

#### 7C — CDO (port 8003)

```bash
nano /etc/nginx/sites-available/alcess-cdo
```

Same as Davao config but change:
- `listen 8003;` and `listen [::]:8003;`
- `root /var/www/alcess-cdo/public;`

```bash
ln -s /etc/nginx/sites-available/alcess-cdo /etc/nginx/sites-enabled/
```

#### 7D — Monitor Dashboard (port 8080)

```bash
nano /etc/nginx/sites-available/alcess-monitor
```

```nginx
server {
    listen 8080;
    listen [::]:8080;

    server_name _;
    root /var/www/alcess-monitor/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;
    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

```bash
ln -s /etc/nginx/sites-available/alcess-monitor /etc/nginx/sites-enabled/
```

#### 7E — Test & Reload

```bash
nginx -t && systemctl reload nginx
```

#### Access Your Sites

| Branch | URL |
|---|---|
| GenSan (existing) | `https://alcessgensan.online` |
| Davao | `http://72.61.142.94:8001` |
| Cebu | `http://72.61.142.94:8002` |
| CDO | `http://72.61.142.94:8003` |
| Monitor | `http://72.61.142.94:8080` |

> [!WARNING]
> **Port-based access is HTTP only (no SSL).** This is fine for testing and initial data entry. Once domains are purchased, we'll switch to HTTPS. See [Phase 11](#phase-11-domain-cutover-when-ready) below.

---

### Phase 8: Supervisor Queue Workers

#### Davao

```bash
nano /etc/supervisor/conf.d/alcess-davao-worker.conf
```

```ini
[program:alcess-davao-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/alcess-davao/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=1
redirect_stderr=true
stdout_logfile=/var/www/alcess-davao/storage/logs/worker.log
stopwaitsecs=3600
```

#### Cebu

```bash
nano /etc/supervisor/conf.d/alcess-cebu-worker.conf
```

```ini
[program:alcess-cebu-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/alcess-cebu/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=1
redirect_stderr=true
stdout_logfile=/var/www/alcess-cebu/storage/logs/worker.log
stopwaitsecs=3600
```

#### CDO

```bash
nano /etc/supervisor/conf.d/alcess-cdo-worker.conf
```

```ini
[program:alcess-cdo-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/alcess-cdo/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=1
redirect_stderr=true
stdout_logfile=/var/www/alcess-cdo/storage/logs/worker.log
stopwaitsecs=3600
```

#### Apply

```bash
supervisorctl reread
supervisorctl update
supervisorctl status
```

Expected output:

```
alcess-worker:alcess-worker_00                RUNNING
alcess-davao-worker:alcess-davao-worker_00    RUNNING
alcess-cebu-worker:alcess-cebu-worker_00      RUNNING
alcess-cdo-worker:alcess-cdo-worker_00        RUNNING
```

---

### Phase 9: Verify Everything

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
echo "Monitor: $(curl -s -o /dev/null -w '%{http_code}' http://127.0.0.1:8080)"
echo "=== ALL CHECKED ==="
```

---

### Phase 10: After VPS Reboot

```bash
cd /var/www/alcess
docker compose up -d
supervisorctl restart all
```

---

### Phase 11: Domain Cutover (When Ready)

When domains are purchased and DNS A records point to `72.61.142.94`:

#### Step 1 — Update Nginx configs to domain-based

For each branch, replace the port-based config with a domain-based one. Example for Davao:

```bash
nano /etc/nginx/sites-available/alcess-davao
```

Replace entire content with:

```nginx
server {
    server_name alcess-davao.online www.alcess-davao.online;
    root /var/www/alcess-davao/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;
    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }

    location /app/ {
        proxy_pass http://127.0.0.1:6001;
        proxy_http_version 1.1;
        proxy_set_header Upgrade $http_upgrade;
        proxy_set_header Connection "Upgrade";
        proxy_set_header Host $host;
    }

    listen 80;
    listen [::]:80;
}
```

Repeat for Cebu and CDO (change `server_name` and `root` accordingly).

#### Step 2 — Test & reload Nginx

```bash
nginx -t && systemctl reload nginx
```

#### Step 3 — Get SSL certificates

```bash
certbot --nginx -d alcess-davao.online -d www.alcess-davao.online
certbot --nginx -d alcess-cebu.online -d www.alcess-cebu.online
certbot --nginx -d alcess-cdo.online -d www.alcess-cdo.online
# Monitor (if it gets a domain)
certbot --nginx -d monitor.alcess.online
```

#### Step 4 — Update `.env` files with new URLs

For each branch, update the `.env`:

```bash
nano /var/www/alcess-davao/.env
```

Change:
```env
APP_URL=https://alcess-davao.online
VITE_API_URL="https://alcess-davao.online/api"
VITE_APP_URL="https://alcess-davao.online"
VITE_PUSHER_HOST=alcess-davao.online
VITE_PUSHER_PORT=443
VITE_PUSHER_SCHEME=https
```

#### Step 5 — Rebuild frontend (VITE_ vars are baked at build time)

```bash
cd /var/www/alcess-davao
npm run build
php artisan optimize:clear
php artisan config:cache
# Repeat for cebu and cdo
```

#### Step 6 — Close temporary ports

```bash
ufw delete allow 8001/tcp
ufw delete allow 8002/tcp
ufw delete allow 8003/tcp
ufw delete allow 8080/tcp
ufw reload
```

Also remove ports 8001–8003 and 8080 from the Hostinger panel firewall.

---

## Part 3: Monitoring Dashboard

> A separate lightweight Laravel app that connects **read-only** to all 4 branch databases and displays combined analytics.

### Architecture

```
┌─────────────────────────────────────────────────┐
│            Alcess Monitoring Dashboard           │
│          (http://72.61.142.94:8080)              │
│                                                   │
│  ┌───────────┐  ┌───────────────────────────┐    │
│  │   Login   │→ │       Dashboard            │    │
│  │   Page    │  │                             │   │
│  └───────────┘  │  ┌─────┐ ┌─────┐ ┌─────┐  │   │
│                  │  │Sales│ │Inv. │ │Order│  │   │
│                  │  │Chart│ │Lvls │ │Stats│  │   │
│                  │  └─────┘ └─────┘ └─────┘  │   │
│                  │                             │   │
│                  │  Per-branch breakdown:      │   │
│                  │  GenSan | Davao | Cebu | CDO│   │
│                  └───────────────────────────┘    │
└────────────┬──────┬──────┬──────┬─────────────────┘
             │      │      │      │
        ┌────▼─┐ ┌──▼──┐ ┌▼────┐ ┌▼───┐
        │GenSan│ │Davao│ │Cebu │ │CDO │  ← READ-ONLY
        │  DB  │ │ DB  │ │ DB  │ │ DB │     (SELECT only)
        └──────┘ └─────┘ └─────┘ └────┘
```

### Dashboard Features

| Feature | Description |
|---|---|
| **Login** | Simple auth — owner/admin only |
| **Branch Selector** | Toggle between All / GenSan / Davao / Cebu / CDO |
| **Sales Overview** | Total revenue today / this week / this month / all-time (per-branch + combined) |
| **Sales Chart** | Line/bar chart — daily sales trend across branches |
| **Inventory Levels** | Product stock counts per branch, low-stock alerts |
| **Order Statistics** | Pending / Processing / Completed / Cancelled counts |
| **Top Products** | Best-selling products per branch |

### How It Works (Multi-Database in Laravel)

The monitor app defines **4 database connections** in `config/database.php`:

```php
'connections' => [
    // Default — monitor's own DB (for users, sessions)
    'mysql' => [
        'driver' => 'mariadb',
        'database' => env('DB_DATABASE', 'alcess_monitor_db'),
        // ...
    ],

    // Branch connections (read-only)
    'gensan' => [
        'driver' => 'mariadb',
        'database' => env('DB_GENSAN_DATABASE', 'alcess_db'),
        'username' => env('DB_GENSAN_USERNAME', 'alcess_monitor'),
        'password' => env('DB_GENSAN_PASSWORD'),
        // ...
    ],
    'davao' => [
        'driver' => 'mariadb',
        'database' => env('DB_DAVAO_DATABASE', 'alcess_davao_db'),
        'username' => env('DB_DAVAO_USERNAME', 'alcess_monitor'),
        'password' => env('DB_DAVAO_PASSWORD'),
        // ...
    ],
    'cebu' => [
        'driver' => 'mariadb',
        'database' => env('DB_CEBU_DATABASE', 'alcess_cebu_db'),
        'username' => env('DB_CEBU_USERNAME', 'alcess_monitor'),
        'password' => env('DB_CEBU_PASSWORD'),
        // ...
    ],
    'cdo' => [
        'driver' => 'mariadb',
        'database' => env('DB_CDO_DATABASE', 'alcess_cdo_db'),
        'username' => env('DB_CDO_USERNAME', 'alcess_monitor'),
        'password' => env('DB_CDO_PASSWORD'),
        // ...
    ],
],
```

Querying data per branch:

```php
// Get today's sales for Davao
$davaoSales = DB::connection('davao')
    ->table('sales')
    ->whereDate('created_at', today())
    ->sum('total_amount');

// Get all branches at once
$branches = ['gensan', 'davao', 'cebu', 'cdo'];
$salesData = [];
foreach ($branches as $branch) {
    $salesData[$branch] = DB::connection($branch)
        ->table('sales')
        ->whereDate('created_at', today())
        ->sum('total_amount');
}
```

### Monitor `.env`

```env
APP_NAME="Alcess Monitor"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=http://72.61.142.94:8080

DB_CONNECTION=mariadb
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=alcess_monitor_db
DB_USERNAME=alcess_monitor
DB_PASSWORD=AlcessMonitor@2026

# Branch database connections (all read-only via alcess_monitor user)
DB_GENSAN_DATABASE=alcess_db
DB_GENSAN_USERNAME=alcess_monitor
DB_GENSAN_PASSWORD=AlcessMonitor@2026

DB_DAVAO_DATABASE=alcess_davao_db
DB_DAVAO_USERNAME=alcess_monitor
DB_DAVAO_PASSWORD=AlcessMonitor@2026

DB_CEBU_DATABASE=alcess_cebu_db
DB_CEBU_USERNAME=alcess_monitor
DB_CEBU_PASSWORD=AlcessMonitor@2026

DB_CDO_DATABASE=alcess_cdo_db
DB_CDO_USERNAME=alcess_monitor
DB_CDO_PASSWORD=AlcessMonitor@2026

SESSION_DRIVER=database
CACHE_STORE=file
QUEUE_CONNECTION=sync
```

> [!NOTE]
> The monitoring dashboard is a **separate Laravel project** (separate git repo). It has no queue worker (uses `sync`), no Redis, no Soketi — it's purely a read-only analytics panel. I can build this for you as a separate task once the branch clones are set up.

---

## Part 4: Deployment Cheatsheet

### Deploy a specific branch

```bash
ssh root@72.61.142.94

# Example: Deploy Davao
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

### Deploy ALL script (create on VPS)

```bash
nano /root/deploy-all.sh
```

```bash
#!/bin/bash
echo "=== Deploying ALL Alcess Branches ==="

for site in alcess alcess-davao alcess-cebu alcess-cdo; do
  echo ""
  echo "--- Deploying $site ---"
  cd /var/www/$site
  git pull
  composer install --optimize-autoloader --no-dev --no-interaction
  npm install && npm run build
  php artisan migrate --force
  php artisan optimize:clear
  php artisan config:cache
  php artisan route:cache
  php artisan view:cache
  echo "$site ✅ Done"
done

echo ""
supervisorctl restart all
echo "=== ALL DEPLOYMENTS COMPLETE ==="
```

```bash
chmod +x /root/deploy-all.sh
```

---

## Part 5: Database Backup (All Branches)

```bash
mkdir -p /root/backups

# Manual backup
mysqldump -u root alcess_db > /root/backups/alcess_gensan_$(date +%Y%m%d_%H%M%S).sql
mysqldump -u root alcess_davao_db > /root/backups/alcess_davao_$(date +%Y%m%d_%H%M%S).sql
mysqldump -u root alcess_cebu_db > /root/backups/alcess_cebu_$(date +%Y%m%d_%H%M%S).sql
mysqldump -u root alcess_cdo_db > /root/backups/alcess_cdo_$(date +%Y%m%d_%H%M%S).sql
```

### Automated daily backup (cron)

```bash
crontab -e
```

Add:

```cron
0 2 * * * mysqldump -u root alcess_db > /root/backups/alcess_gensan_$(date +\%Y\%m\%d).sql && mysqldump -u root alcess_davao_db > /root/backups/alcess_davao_$(date +\%Y\%m\%d).sql && mysqldump -u root alcess_cebu_db > /root/backups/alcess_cebu_$(date +\%Y\%m\%d).sql && mysqldump -u root alcess_cdo_db > /root/backups/alcess_cdo_$(date +\%Y\%m\%d).sql && find /root/backups -name "alcess_*.sql" -mtime +7 -delete
```

---

## Part 6: Quick Reference Table

| | GenSan | Davao | Cebu | CDO | Monitor |
|---|---|---|---|---|---|
| **App Root** | `/var/www/alcess` | `/var/www/alcess-davao` | `/var/www/alcess-cebu` | `/var/www/alcess-cdo` | `/var/www/alcess-monitor` |
| **Temp URL** | `alcessgensan.online` | `:8001` | `:8002` | `:8003` | `:8080` |
| **Database** | `alcess_db` | `alcess_davao_db` | `alcess_cebu_db` | `alcess_cdo_db` | `alcess_monitor_db` |
| **DB User** | `alcess_user` | `alcess_davao` | `alcess_cebu` | `alcess_cdo` | `alcess_monitor` |
| **Redis DB** | `0` | `1` | `2` | `3` | N/A |
| **Soketi App** | `alcess` | `alcess-davao` | `alcess-cebu` | `alcess-cdo` | N/A |
| **Git Branch** | `deploy/gensan` | `deploy/davao` | `deploy/cebu` | `deploy/cdo` | separate repo |
| **Worker** | `alcess-worker` | `alcess-davao-worker` | `alcess-cebu-worker` | `alcess-cdo-worker` | none |
| **Nginx Config** | `alcess` | `alcess-davao` | `alcess-cebu` | `alcess-cdo` | `alcess-monitor` |

---

## Verification Plan

### Automated Checks
- Phase 9 verification script — all services running, all sites returning HTTP 200
- `nginx -t` — all configs valid
- `supervisorctl status` — 4 workers RUNNING
- `free -h` — memory under control

### Manual Verification
1. Open each URL in browser → login page loads
2. Log in with default admin on each branch
3. Create a test sale on one branch → verify it does NOT appear on other branches
4. Check monitoring dashboard can see data from all 4 branches
5. Reboot VPS and verify recovery procedure works
