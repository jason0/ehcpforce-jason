#!/bin/bash
# mysql root pass reset utility. by info@ehcp.net
clear
echo "This will reset your msyql root pass"
echo "Only continue if you lost mysql root pass and you know what you do"
echo "if you have other programs that use old mysql root pass, you need to fix them manually."
echo 
echo "press enter to continue or Ctrl-C to cancel"
read

echo
echo "Please wait..."
echo

/etc/init.d/mysql stop
mysqld_safe --skip-grant-tables &
sleep 5
echo
echo
echo "Enter NEW mysql root pass:"
read newpass

echo "UPDATE mysql.user SET Password=PASSWORD('$newpass') WHERE User='root'; flush privileges;" | mysql -u root

/etc/init.d/mysql restart



echo
echo
echo "mysql root pass reset COMPLETE .... "

# UPDATE mysql.user SET Password=PASSWORD('1234') WHERE User='root'; flush privileges;
