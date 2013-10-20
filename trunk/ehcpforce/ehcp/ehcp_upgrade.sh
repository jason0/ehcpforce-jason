#!/bin/bash
echo "This is upgrade from previous versions (especially from 0.27, 0.28 to 0.29), may not work completely.. see http://www.ehcp.net/?q=node/171 for details.."

cd /var/www/vhosts
backupdir="ehcp.bck.`date`"
cp -Rf ehcp $backupdir
wget www.ehcp.net/download
rm -Rf ehcp
tar -zxvf ehcp_latest.tgz

wget www.ehcp.net/download/ehcpupgrade.sql

mysql -u ehcp -p < ehcpupgrade.sql
cp $backupdir/config.php ehcp/

echo "upgrade finished.";

apt-get install webalizer php5-curl php5-xmlrpc php-pear
