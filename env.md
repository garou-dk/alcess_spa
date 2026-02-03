chown -R www-data:www-data /var/www/alcess
chmod -R 775 /var/www/alcess/storage
chmod -R 775 /var/www/alcess/bootstrap/cache
php artisan optimize:clear