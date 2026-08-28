# Alcess VPS Maintenance Guide

> **Last Updated:** June 8, 2026
> **Domain:** [alcessgensan.online](https://alcessgensan.online)
> **VPS Provider:** Hostinger (KVM 2)
> **Server Location:** Indonesia - Jakarta
> **OS:** Ubuntu 24.04 LTS

---

## 🖥️ Server Details

| Property | Value |
|---|---|
| **IP Address** | `72.61.142.94` |
| **Hostname** | `srv1740185.hstgr.cloud` |
| **SSH User** | `root` |
| **CPU** | 2 Cores |
| **RAM** | 8 GB |
| **Storage** | 100 GB |
| **Plan Expiry** | 2026-07-08 |
| **SSL Expiry** | 2026-09-06 (auto-renews) |

---

## 🔐 Access

### SSH Access
```bash
ssh root@72.61.142.94
```

### Web Terminal (Backup)
If SSH is unreachable, use Hostinger's browser terminal:
1. Go to [hpanel.hostinger.com](https://hpanel.hostinger.com)
2. Select your VPS → **Browser Terminal**

### Important Paths
| Path | Description |
|---|---|
| `/var/www/alcess` | Application root |
| `/var/www/alcess/.env` | Environment config |
| `/var/www/alcess/storage/logs` | Application logs |
| `/etc/nginx/sites-available/alcess` | Nginx config |
| `/etc/supervisor/conf.d/alcess-worker.conf` | Queue worker config |
| `/etc/letsencrypt/live/alcessgensan.online/` | SSL certificates |

---

## 🚀 Deployment (Pushing Updates)

### Standard Deployment
Run this after pushing code to GitHub:
```bash
cd /var/www/alcess
git pull
composer install --optimize-autoloader --no-dev --no-interaction
npm install && npm run build
php artisan migrate --force
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
supervisorctl restart all
```

### Quick Deploy (Frontend Only)
If only Vue/CSS changes were made (no PHP changes):
```bash
cd /var/www/alcess
git pull
npm run build
```

### Quick Deploy (Backend Only)
If only PHP changes were made (no frontend changes):
```bash
cd /var/www/alcess
git pull
composer install --optimize-autoloader --no-dev --no-interaction
php artisan migrate --force
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
supervisorctl restart all
```

---

## 🔧 Service Management

### Nginx (Web Server)
```bash
# Check status
systemctl status nginx

# Restart
systemctl restart nginx

# Test config before restarting
nginx -t && systemctl reload nginx

# View error logs
tail -50 /var/log/nginx/error.log
```

### Docker (Redis + Soketi)
```bash
cd /var/www/alcess

# Check running containers
docker compose ps

# Restart containers
docker compose restart

# Stop and start fresh
docker compose down && docker compose up -d

# View logs
docker compose logs redis
docker compose logs soketi
```

### Supervisor (Queue Worker)
```bash
# Check status
supervisorctl status

# Restart worker
supervisorctl restart all

# Reload config (after editing .conf file)
supervisorctl reread
supervisorctl update

# View worker logs
tail -50 /var/www/alcess/storage/logs/worker.log
```

### MariaDB (Database)
```bash
# Check status
systemctl status mariadb

# Restart
systemctl restart mariadb

# Access database
mysql -u alcess_user -p alcess_db
# Password: alcess_db

# Access as root
mysql
```

### PHP-FPM
```bash
# Check status
systemctl status php8.3-fpm

# Restart
systemctl restart php8.3-fpm
```

---

## 🗄️ Database Operations

### Backup Database
```bash
# Create backup with timestamp
mysqldump -u root alcess_db > /root/backups/alcess_db_$(date +%Y%m%d_%H%M%S).sql

# Verify backup size
ls -lh /root/backups/
```

### Restore Database
```bash
mysql -u root alcess_db < /root/backups/alcess_db_YYYYMMDD_HHMMSS.sql
```

### Fresh Reset (Nuclear Option)
⚠️ **This deletes ALL data and starts fresh with only the admin account:**
```bash
cd /var/www/alcess
php artisan migrate:fresh --seed --force
```
**Default admin after reset:** `admin@gmail.com` / `Admin@123`

### Run Migrations Only
```bash
cd /var/www/alcess
php artisan migrate --force
```

### Useful Database Queries
```bash
# Check table sizes
mysql alcess_db -e "
SELECT table_name, 
       ROUND(data_length/1024/1024, 2) AS 'Size (MB)',
       table_rows AS 'Rows'
FROM information_schema.tables 
WHERE table_schema = 'alcess_db' 
ORDER BY data_length DESC;"

# Count users
mysql alcess_db -e "SELECT COUNT(*) as total_users FROM users;"

# Count products
mysql alcess_db -e "SELECT COUNT(*) as total_products FROM products;"

# Count orders
mysql alcess_db -e "SELECT COUNT(*) as total_orders FROM orders;"
```

---

## 📁 Storage & File Management

### Storage Symlink
If the public storage symlink is broken:
```bash
cd /var/www/alcess
php artisan storage:link
```

### File Permissions Fix
If you encounter permission errors:
```bash
chown -R www-data:www-data /var/www/alcess
chmod -R 775 /var/www/alcess/storage
chmod -R 775 /var/www/alcess/bootstrap/cache
```

### Clear Uploaded Files
```bash
# Product images
ls /var/www/alcess/storage/app/public/images/product/

# Profile images
ls /var/www/alcess/storage/app/public/images/profile/
```

---

## 🔒 SSL Certificate

### Check Expiry
```bash
certbot certificates
```

### Force Renewal
```bash
certbot renew --force-renewal
systemctl reload nginx
```

### Auto-Renewal Test
```bash
certbot renew --dry-run
```

> SSL auto-renews via a systemd timer. No action needed unless it fails.

---

## 🔥 Firewall

### UFW Status
```bash
ufw status verbose
```

### Current Rules
| Port | Service |
|---|---|
| 22 | SSH |
| 80 | HTTP |
| 443 | HTTPS |

### Hostinger Panel Firewall
Also configured at: **hpanel.hostinger.com → VPS → Firewall**
Firewall name: `alcess-firewall`

---

## 📊 Monitoring & Logs

### Application Logs
```bash
# Latest Laravel log
tail -100 /var/www/alcess/storage/logs/laravel.log

# Queue worker log
tail -50 /var/www/alcess/storage/logs/worker.log

# Clear old logs
rm /var/www/alcess/storage/logs/laravel-*.log
```

### System Resources
```bash
# Disk usage
df -h /

# Memory usage
free -h

# CPU & process load
htop
# or
top -bn1 | head -20

# Docker disk usage
docker system df
```

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
echo "=== Queue Worker ==="
supervisorctl status
echo ""
echo "=== Disk Usage ==="
df -h / | tail -1
echo ""
echo "=== Memory ==="
free -h | grep Mem
echo ""
echo "=== SSL ==="
certbot certificates 2>/dev/null | grep -E "Expiry|Domains"
echo "=== ALL CHECKED ==="
```

---

## 🛠️ Troubleshooting

### Site Shows 502 Bad Gateway
```bash
# Restart PHP-FPM
systemctl restart php8.3-fpm
systemctl restart nginx
```

### Site Shows 500 Error
```bash
# Check Laravel logs
tail -50 /var/www/alcess/storage/logs/laravel.log

# Fix permissions
chown -R www-data:www-data /var/www/alcess/storage
chmod -R 775 /var/www/alcess/storage

# Clear all caches
cd /var/www/alcess
php artisan optimize:clear
```

### Redis Connection Refused
```bash
# Restart Docker containers
cd /var/www/alcess
docker compose restart
docker compose ps
```

### WebSocket (Soketi) Not Working
```bash
# Check if Soketi is running
cd /var/www/alcess
docker compose logs soketi

# Restart Soketi
docker compose restart soketi
```

### Queue Jobs Not Processing
```bash
# Check worker status
supervisorctl status

# Restart worker
supervisorctl restart all

# Process jobs manually (for debugging)
cd /var/www/alcess
php artisan queue:work --once
```

### After VPS Reboot
Docker containers don't auto-start after reboot. Run:
```bash
cd /var/www/alcess
docker compose up -d
supervisorctl restart all
```

### Cannot SSH (Connection Timeout)
1. Use Hostinger's **Browser Terminal** (hpanel → VPS → Browser Terminal)
2. Check if it's your ISP blocking the IP (try a different network)
3. Check UFW: `ufw status`
4. Check Hostinger panel firewall rules

---

## 📧 Email Configuration

| Setting | Value |
|---|---|
| **SMTP Host** | smtp.gmail.com |
| **Port** | 587 |
| **Email** | alcessdavao@gmail.com |
| **App Password** | (stored in `.env` as `MAIL_PASSWORD`) |

> If emails stop sending, you may need to regenerate the Gmail App Password.

---

## 🏢 Business Information

| Property | Value |
|---|---|
| **Business Name** | Alcess Laptop and Computer Accessories |
| **Address** | Door 13, Fred-Cion Building, J. Catolico Avenue, Lagao, General Santos City |
| **Reports Signatory** | Dynamic (Configurable field / Logged-in Staff or Admin) |
| **Contact** | 09531852360 / 09651934705 |
| **Email** | alcessdavao@gmail.com |

---

## 📋 Software Versions (Installed June 8, 2026)

| Software | Version |
|---|---|
| Ubuntu | 24.04 LTS |
| PHP | 8.3.6 |
| Composer | 2.10.1 |
| Node.js | 20.20.2 |
| NPM | 10.8.2 |
| MariaDB | 10.11.14 |
| Docker | 29.5.3 |
| Docker Compose | 5.1.4 |
| Nginx | latest |
| Laravel | 12.17.0 |
| Certbot | latest |

---

## 📅 Scheduled Tasks

| Task | Schedule | Managed By |
|---|---|---|
| SSL Renewal | Auto (every 60-90 days) | Certbot systemd timer |
| Queue Processing | Continuous | Supervisor |
| Log Rotation | Daily | Laravel (daily channel) |

---

## ⚠️ Known Issues

1. **ISP Blocking**: Some ISPs in the Philippines may block the VPS IP. Use a different network or VPN to test.
2. **Docker after Reboot**: Docker containers (Redis + Soketi) need to be manually started after a VPS reboot. Run `docker compose up -d`.
3. **Hostinger Panel Firewall**: Must have `alcess-firewall` with ports 22, 80, 443 assigned to the VPS.
