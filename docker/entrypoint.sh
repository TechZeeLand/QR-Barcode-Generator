#!/bin/sh
set -e
DATA=/var/www/html/data
mkdir -p "$DATA"
chown -R www-data:www-data "$DATA"
# first run: create the admin account (the password is printed once to the log)
su -s /bin/sh www-data -c "php /var/www/html/bin/seed.php" || true
exec docker-php-entrypoint "$@"
