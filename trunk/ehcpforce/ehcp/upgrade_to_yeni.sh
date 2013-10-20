# Check if the running user is root, if not restart with sudo
function checkUser() {
		if [ `whoami` != "root" ];then
				echo "you are $who, you have to be root to use ehcp installation program.  switching to root mode, please enter password  or re-run install.sh as root"
				sudo $0 # restart this with superuser-root privileges				
				exit
		fi
}

checkUser

grep "ehcp version 0.29.15" ehcpinfo.html
if [ $? -gt 0 ] ; then # download if current files are not new
	wget http://www.ehcp.net/ehcp_yeni.tgz
	tar -zxf ehcp_yeni.tgz
	cd ehcp
fi

cp ehcp /etc/init.d/
# backup current config.php
cat /var/www/vhosts/ehcp/config.php
cp /var/www/vhosts/ehcp/config.php /var/www/
mkdir /var/www/new
mv /var/www/vhosts/ehcp /var/www/new/
cp -Rf /var/www/new/ehcp /var/www/new/ehcp.bck

#apache default file
rm -rvf /etc/apache2/sites-enabled/*
cp etc/apache2/default /etc/apache2/sites-enabled/default
# copy new files:
cp -Rf * /var/www/new/ehcp/
# restore config.php
cp /var/www/config.php /var/www/new/ehcp/
cp /var/www/new/ehcp/wwwindex.html /var/www/new/index.html
# build redirect for old style-> new style
mkdir -p /var/www/new/vhosts/ehcp/
echo "<html><head><meta HTTP-EQUIV='REFRESH' content='0; url=/'></head></html>" > /var/www/new/vhosts/ehcp/index.html
# set default apache2
update-rc.d apache2 defaults
update-rc.d -f nginx remove
/etc/init.d/ehcp restart
# sync domains:
mysql -p -Dehcp -e "insert into operations (op)values('syncdomains')"
mysql -p -Dehcp -e "insert into operations (op)values('syncdns')"

echo "Include /var/www/new/ehcp/apachehcp.conf" >> /etc/apache2/apache2.conf
echo "Include /var/www/new/ehcp/apachehcp_subdomains.conf" >> /etc/apache2/apache2.conf
echo "Include /var/www/new/ehcp/apachehcp_auth.conf" >> /etc/apache2/apache2.conf
echo "Include /var/www/new/ehcp/apachehcp_passivedomains.conf" >> /etc/apache2/apache2.conf
echo "#edit apache2.conf manually.  go to bottom, delete lines with /var/www/vhosts/ehcp  now with new/ehcp"

/etc/init.d/apache2 restart
