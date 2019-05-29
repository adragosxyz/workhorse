# Update Packages
apt-get update
# Upgrade Packages
apt-get upgrade

mkdir /vps
chown vagrant /vps
chmod 777 /vps

cp /vagrant/newvm.sh /vps/newvm.sh
cp /vagrant/VagrantfileModel /vps/VagrantfileModel
cp /vagrant/proxypass.txt /vps/proxypass.txt

# Basic Linux Stuff
apt-get install -y git

# Apache
apt-get install -y apache2

# Enable Apache Mods
a2enmod rewrite

#Add Onrej PPA Repo
apt-add-repository ppa:ondrej/php
apt-get update

# Install PHP
apt-get install -y php7.2

# PHP Apache Mod
apt-get install -y libapache2-mod-php7.2

# Restart Apache
service apache2 restart

# PHP Mods
apt-get install -y php7.2-common
apt-get install -y php7.2-mcrypt
apt-get install -y php7.2-zip
apt-get install -y php-mysqlnd
apt-get install -y php7.2-mysql

# Install MySQL
apt-get install -y mysql-server

# PHP-MYSQL lib
apt-get install -y php7.2-mysql

mysql -f < /vagrant/database.sql

apt-get install -y vagrant
apt-get install -y docker.io

sudo a2enmod proxy
sudo a2enmod proxy_http
sudo a2enmod proxy_balancer
sudo a2enmod lbmethod_byrequests

sudo echo "www-data ALL = NOPASSWD: /usr/bin/vagrant up" >> /etc/sudoers
sudo echo "www-data ALL = NOPASSWD: /usr/bin/docker container rm -f *" >> /etc/sudoers
sudo echo "www-data ALL = NOPASSWD: /usr/bin/vagrant destroy -f" >> /etc/sudoers
sudo echo "www-data ALL = NOPASSWD: /usr/bin/docker inspect *" >> /etc/sudoers
sudo echo "www-data ALL = NOPASSWD: /usr/sbin/service apache2 reload" >> /etc/sudoers

chown www-data /etc/apache2/sites-available/000-default.conf

# Restart Apache
sudo service apache2 restart

sudo apt-get update
sudo apt-get install -y software-properties-common
sudo add-apt-repository universe
sudo add-apt-repository ppa:certbot/certbot
sudo apt-get update
sudo apt-get install -y certbot python-certbot-apache 