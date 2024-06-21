## symfony
composer install  
php bin/console doctrine:database:create  
php bin/console doc:sc:up -f  
php bin/console doctrine:fixtures:load --no-interaction  
symfony server:start
