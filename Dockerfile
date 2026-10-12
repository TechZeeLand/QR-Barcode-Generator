FROM php:8.3-apache
RUN a2enmod headers rewrite \
 && sed -ri 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/*.conf /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf \
 && printf 'ServerTokens Prod\nServerSignature Off\nHeader always set X-Content-Type-Options "nosniff"\nHeader always set Referrer-Policy "no-referrer"\nHeader always set Content-Security-Policy "default-src '"'"'self'"'"'; img-src '"'"'self'"'"' blob: data:; style-src '"'"'self'"'"'; script-src '"'"'self'"'"'; frame-ancestors '"'"'self'"'"'"\nErrorDocument 403 /403.php\nErrorDocument 404 /404.php\nErrorDocument 500 /500.php\n' > /etc/apache2/conf-available/hardening.conf \
 && a2enconf hardening
COPY . /var/www/html
EXPOSE 80
HEALTHCHECK --interval=30s CMD php -r 'exit(@file_get_contents("http://127.0.0.1/")?0:1);'
