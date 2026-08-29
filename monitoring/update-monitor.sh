#!/bin/bash
# Sync monitoring code from repository to /var/www/alcess-monitor
echo "=== Updating Alcess Monitor from Git Repo ==="

# Copy updated controllers, routes, and views
cp /var/www/alcess/monitoring/app/Http/Controllers/DashboardController.php /var/www/alcess-monitor/app/Http/Controllers/
cp /var/www/alcess/monitoring/app/Http/Controllers/ProfileController.php /var/www/alcess-monitor/app/Http/Controllers/
cp /var/www/alcess/monitoring/routes/web.php /var/www/alcess-monitor/routes/
cp /var/www/alcess/monitoring/resources/views/dashboard.blade.php /var/www/alcess-monitor/resources/views/

# Set proper permissions
chown -R www-data:www-data /var/www/alcess-monitor/app
chown -R www-data:www-data /var/www/alcess-monitor/routes
chown -R www-data:www-data /var/www/alcess-monitor/resources

# Clear caches
cd /var/www/alcess-monitor
php artisan optimize:clear
php artisan route:clear
php artisan view:clear

echo "✅ Alcess Monitor (Dashboard & Profile) updated successfully!"
