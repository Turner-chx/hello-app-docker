#/bin/sh

if [ "$1" == "--composer" ] || [ "$1" == "--c" ] || [ "$2" == "--composer" ] || [ "$2" == "--c" ] ; then
  php composer update
fi

if [ "$1" == "--doctrine" ] || [ "$1" == "--d" ] || [ "$2" == "--doctrine" ] || [ "$2" == "--d" ]   ; then
  php /var/www/intranet-web/bin/console doctrine:schema:update --force
fi

sudo chmod -R 777 /var/www/intranet-web/var
php /var/www/intranet-web/bin/console cache:clear --no-warmup
php /var/www/intranet-web/bin/console assets:install
sudo chmod -R 777 /var/www/intranet-web/var
php /var/www/intranet-web/bin/console cache:clear --no-warmup --env=prod --no-debug
php /var/www/intranet-web/bin/console assets:install --env=prod --no-debug
sudo chmod -R 777 /var/www/intranet-web/var
