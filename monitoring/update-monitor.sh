#!/bin/bash
# Sync monitoring code from repository to /var/www/alcess-monitor
echo "=== Updating Alcess Monitor from Git Repo ==="

# Copy updated controller and blade view
cp /var/www/alcess/monitoring/app/Http/Controllers/DashboardController.php /var/www/alcess-monitor/app/Http/Controllers/
cp /var/www/alcess/monitoring/resources/views/dashboard.blade.php /var/www/alcess-monitor/resources/views/

# Set proper permissions
chown -R www-data:www-data /var/www/alcess-monitor/app
chown -R www-data:www-data /var/www/alcess-monitor/resources

# Clear caches
cd /var/www/alcess-monitor
php artisan optimize:clear
php artisan view:clear

echo "✅ Alcess Monitor updated and views cleared successfully!"
