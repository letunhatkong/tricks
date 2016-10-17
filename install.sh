#!/bin/sh 
sudo apt-get update
sudo apt-get install apache2
sudo apt-get -y update
sudo add-apt-repository ppa:ondrej/php5-5.6
sudo apt-get -y update
sudo apt-get -y install php5 php5-common php5-mcrypt php5-curl php5-cli php5-mysql php5-gd php5-intl php5-xsl libapache2-mod-php5 libcurl3 php5-imagick zip
sudo apt-get install mysql-server-5.6 php5-mysql

