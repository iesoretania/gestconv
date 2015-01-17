#!/usr/bin/env bash

apt-get -q update
export DEBIAN_FRONTEND=noninteractive
echo 'phpmyadmin phpmyadmin/reconfigure-webserver multiselect apache2' | debconf-set-selections
apt-get -q -y install apache2 php5 mysql-server mysql-client phpmyadmin wget php5-intl php5-mcrypt php5-xdebug php5-curl
service apache2 stop
sed -i -e "s/www\-data/vagrant/" /etc/apache2/envvars
sed -i -e "s/var\/www\/html/vagrant\/web/" /etc/apache2/sites-available/000-default.conf
sed -i -e "s/var\/www/vagrant\/web/" /etc/apache2/apache2.conf
sed -i -e "s/AllowOverride None/AllowOverride All/" /etc/apache2/apache2.conf
echo 'EnableSendfile off' > /etc/apache2/conf-available/sendfile.conf
echo '<?php
$cfg["Servers"][1]["AllowNoPassword"] = TRUE;
' > /etc/phpmyadmin/conf.d/passwordless.inc.php
a2enconf sendfile
a2enmod rewrite
echo '
zend_extension=xdebug.so
display_errors=on
date.timezone=Europe/Madrid

[XDebug]
xdebug.default_enable = 1
xdebug.idekey = "default"
xdebug.remote_enable = 1
xdebug.remote_autostart = 1
xdebug.remote_port = 9000
xdebug.remote_handler=dbgp
xdebug.max_nesting_level=250
' > /etc/php5/mods-available/xdebug.ini
php5enmod mcrypt
php5enmod xdebug
service apache2 restart
wget -nv https://getcomposer.org/composer.phar
chmod +x composer.phar
mv composer.phar /usr/bin/composer
