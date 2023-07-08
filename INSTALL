Azote Production

Require Apache or Nginx, PHP >= 5.6, MySQL, composer and npm (from NodeJS)
PHP Extensions : OpenSSL, PDO, Mbstring
On Apache : active URL Rewrite module
On Nginx : follow PHP and Nginx configuration https://www.digitalocean.com/community/tutorials/how-to-install-laravel-with-an-nginx-web-server-on-ubuntu-14-04

- clone Website repository
- go to /{PATH_TO_WEBSITE}/api
- composer install (error may be appear at the end)
- npm install
- if .env don’t exist, copy .env.example to .env
- edit .env
    - APP_ENV=production
    - APP_DEBUG=false
    - DO NOT edit APP_KEY (generated later)
    - edit all settings with correct information
- edit app/Providers/AuthServiceProvider.php
    - change
        - return Permission::with('roles')->get();
    - to
        - return [];
- php artisan migrate
- php artisan key:generate
- backup app/Providers/AuthServiceProvider.php
- sudo chgrp -R www-data storage bootstrap/cache public/forge
- sudo chmod -R ug+rwx storage bootstrap/cache public/forge
- root document of Apache or Nginx MUST point to /{PATH_TO_WEBSITE}/api/public

composer install
npm install
npm install -g gulp
npm install -g bower
bower install
php artisan migrate
php artisan vendor:publish
gulp --production
chmod -R ug+rwx public/uploads
sudo chgrp -R www-data public/uploads public/filemanager/userfiles
sudo chmod -R ug+rwx public/uploads public/filemanager/userfiles
