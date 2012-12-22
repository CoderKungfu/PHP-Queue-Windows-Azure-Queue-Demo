#!/bin/sh

sudo yum install -y screen git php php-gd php-pear php-process

curl -s https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

git clone https://github.com/miccheng/PHP-Queue-Windows-Azure-Queue-Demo.git
cd PHP-Queue-Windows-Azure-Queue-Demo

composer install