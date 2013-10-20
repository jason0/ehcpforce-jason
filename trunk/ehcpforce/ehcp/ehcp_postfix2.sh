#!/bin/bash
# This file is no longer used in ehcp, left here for historical purposes.
#  ehcp - Easy Hosting Control Panel install/remove by bvidinli@iyibirisi.com
#
#  main installation program.
#
# please contact me if you made any modifications.
# msn/email: bvidinli@iyibirisi.com
#

# This is inspired by document from: http://www.howtoforge.com/virtual_postfix_mysql_quota_courier


apt-get install postfix postfix-mysql postfix-doc mysql-client mysql-server courier-authdaemon courier-authmysql courier-pop courier-pop-ssl courier-imap courier-imap-ssl libsasl2 libsasl2-modules libsasl2-modules-sql sasl2-bin libpam-mysql openssl phpmyadmin

# apt-get remove postfix postfix-mysql postfix-doc mysql-client mysql-server courier-authdaemon courier-authmysql courier-pop courier-pop-ssl courier-imap courier-imap-ssl libsasl2 libsasl2-modules libsasl2-modules-sql sasl2-bin libpam-mysql openssl phpmyadmin


echo "You may be asked a few questions:  please answer as follows:"

echo "Enable suExec? <-- Yes"
echo "Create directories for web-based administration ? <-- No"
echo "General type of configuration? <-- Internet site"
echo "Where should mail for root go? <-- NONE"
echo "Mail name? <-- server.iyibirisi.com"
echo "Other destinations to accept mail for? (blank for none) <-- server.iyibirisi.com, localhost, localhost.localdomain"
echo "Force synchronous updates on mail queue? <-- No"
echo "SSL certificate required <-- Ok"
echo "Install Hints <-- Ok"
echo "Which web server would you like to reconfigure automatically? <-- apache, apache2"
echo "Do you want me to restart apache now? <-- Yes"


# skip quota support
# apt-get install build-essential dpkg-dev fakeroot debhelper libdb4.2-dev libgdbm-dev libldap2-dev libpcre3-dev libmysqlclient10-dev libssl-dev libsasl2-dev postgresql-dev po-debconf dpatch

# cd /usr/src
# apt-get source postfix
# wget http://web.onda.com.br/nadal/postfix/VDA/postfix-2.1.5-trash.patch.gz
# gunzip postfix-2.1.5-trash.patch.gz
# cd postfix-2.1.5
# patch -p1 < ../postfix-2.1.5-trash.patch
# dpkg-buildpackage
# cd ..
# 

# dpkg -i postfix_2.1.5-9ubuntu3_i386.deb
# dpkg -i postfix-mysql_2.1.5-9ubuntu3_i386.deb



#---------- mysql setup  # these are commented because sql setup is done at one step completely in ehcp
#mysqladmin -u root password yourrootsqlpassword

#Now we create a database called mail:

#mysqladmin -u root -p create mail

# Next, we go to the MySQL shell:

#mysql -u root -p

#On the MySQL shell, we create the user mail_admin with the passwort mail_admin_password (replace it with your own password) who has SELECT,INSERT,UPDATE,DELETE privileges on the mail database. This user will be used by Postfix and Courier to connect to the mail database:

#GRANT SELECT, INSERT, UPDATE, DELETE ON mail.* TO 'mail_admin'@'localhost' IDENTIFIED BY 'mail_admin_password';
#GRANT SELECT, INSERT, UPDATE, DELETE ON mail.* TO 'mail_admin'@'localhost.localdomain' IDENTIFIED BY 'mail_admin_password';
#FLUSH PRIVILEGES;


#CREATE TABLE domains (
#domain varchar(50) NOT NULL,
#virtual varchar(50) NOT NULL,
#PRIMARY KEY (domain) )
#TYPE=MyISAM;

#CREATE TABLE forwardings (
#source varchar(80) NOT NULL,
#destination TEXT NOT NULL,
#PRIMARY KEY (source) )
#TYPE=MyISAM;

#CREATE TABLE users (
#email varchar(80) NOT NULL,
#password varchar(20) NOT NULL,
#quota INT(10) DEFAULT '10485760',
#PRIMARY KEY (email)
#) TYPE=MyISAM;

#CREATE TABLE transport (
#domain varchar(128) NOT NULL default '',
#transport varchar(128) NOT NULL default '',
#UNIQUE KEY domain (domain)
#) TYPE=MyISAM;

#quit;



echo "Please make sure that /etc/mysql/my.cnf contains the following line:"
echo "bind-address=127.0.0.1"
echo 
echo 
echo 
echo 


echo "Now let's create our six text files."

cat > /etc/postfix/mysql-virtual_domains.cf << "EOF"

user = ehcp
password = 12345
dbname = ehcp
table = domains
select_field = 'virtual'
where_field = domainname
hosts = 127.0.0.1

EOF

cat > /etc/postfix/mysql-virtual_forwardings.cf << "EOF"
user = ehcp
password = 12345
dbname = ehcp
table = forwardings
select_field = destination
where_field = source
hosts = 127.0.0.1

EOF

cat > /etc/postfix/mysql-virtual_mailboxes.cf << "EOF"

user = ehcp
password = 12345
dbname = ehcp
table = domainusers
select_field = CONCAT(SUBSTRING_INDEX(email,'@',-1),'/',SUBSTRING_INDEX(email,'@',1),'/')
where_field = email
hosts = 127.0.0.1

EOF

cat > /etc/postfix/mysql-virtual_email2email.cf << "EOF"

user = ehcp
password = 12345
dbname = ehcp
table = domainusers
select_field = email
where_field = email
hosts = 127.0.0.1

EOF

cat > /etc/postfix/mysql-virtual_mailbox_limit_maps.cf << "EOF"

user = ehcp
password = 12345
dbname = ehcp
table = domainusers
select_field = quota
where_field = email
hosts = 127.0.0.1


EOF

chmod o= /etc/postfix/mysql-virtual_*.cf
chgrp postfix /etc/postfix/mysql-virtual_*.cf

#Now we create a user and group called vmail with the home directory /home/vmail. This is where all mail boxes will be stored.

groupadd -g 5000 vmail
useradd -g vmail -u 5000 vmail -d /home/vmail -m

hostname=`hostname`

echo "Your hostname seems to be $hostname, if it is different, enter it now, leave blank if correct "
read newhostname

if [ "$newhostname" == "" ] 
then
echo "Hostname is set as $hostname "
else
hostname=$newhostname
echo "Hostname is set as $hostname "
fi


postconf -e "myhostname = $hostname"
postconf -e "mydestination = $hostname, localhost"
postconf -e 'mynetworks = 127.0.0.0/8'
postconf -e 'virtual_alias_domains ='
postconf -e 'virtual_alias_maps = proxy:mysql:/etc/postfix/mysql-virtual_forwardings.cf, proxy:mysql:/etc/postfix/mysql-virtual_email2email.cf'
postconf -e 'virtual_mailbox_domains = proxy:mysql:/etc/postfix/mysql-virtual_domains.cf'
postconf -e 'virtual_mailbox_maps = proxy:mysql:/etc/postfix/mysql-virtual_mailboxes.cf'
postconf -e 'virtual_mailbox_base = /home/vmail'
postconf -e 'virtual_uid_maps = static:5000'
postconf -e 'virtual_gid_maps = static:5000'
postconf -e 'smtpd_sasl_auth_enable = yes'
postconf -e 'broken_sasl_auth_clients = yes'
postconf -e 'smtpd_recipient_restrictions = permit_mynetworks, permit_sasl_authenticated, reject_unauth_destination'
postconf -e 'smtpd_use_tls = yes'
postconf -e 'smtpd_tls_cert_file = /etc/postfix/smtpd.cert'
postconf -e 'smtpd_tls_key_file = /etc/postfix/smtpd.key'
# kullanmiyorum # postconf -e 'transport_maps = proxy:mysql:/etc/postfix/mysql-virtual_transports.cf'
postconf -e 'virtual_create_maildirsize = yes'
postconf -e 'virtual_mailbox_extended = yes'
postconf -e 'virtual_mailbox_limit_maps = proxy:mysql:/etc/postfix/mysql-virtual_mailbox_limit_maps.cf'
postconf -e 'virtual_mailbox_limit_override = yes'
postconf -e 'virtual_maildir_limit_message = "The user you are trying to reach is over quota."'
postconf -e 'virtual_overquota_bounce = yes'
postconf -e 'proxy_read_maps = $local_recipient_maps $mydestination $virtual_alias_maps $virtual_alias_domains $virtual_mailbox_maps $virtual_mailbox_domains $relay_recipient_maps $canonical_maps $sender_canonical_maps $recipient_canonical_maps $relocated_maps $mynetworks $virtual_mailbox_limit_maps'

#Afterwards we create the SSL certificate that is needed for TLS:

cd /etc/postfix
openssl req -new -outform PEM -out smtpd.cert -newkey rsa:2048 -nodes -keyout smtpd.key -keyform PEM -days 365 -x509

chmod o= /etc/postfix/smtpd.key

echo "configure saslauthd"
mkdir -p /var/spool/postfix/var/run/saslauthd
echo "Edit manually /etc/default/saslauthd. Remove the # in front of START=yes and add the line PARAMS=\"-m /var/spool/postfix/var/run/saslauthd -r\"."
cat "START=yes" >> /etc/default/saslauthd
cat "PARAMS=\"-m /var/spool/postfix/var/run/saslauthd -r\"" >> /etc/default/saslauthd


echo "doing: vi /etc/pam.d/smtp"
cat "auth    required   pam_mysql.so user=ehcp passwd=12345 host=127.0.0.1 db=ehcp table=domainusers usercolumn=email passwdcolumn=password crypt=1" >> /etc/pam.d/smtp
cat "account sufficient pam_mysql.so user=ehcp passwd=12345 host=127.0.0.1 db=ehcp table=domainusers usercolumn=email passwdcolumn=password crypt=1" >> /etc/pam.d/smtp

echo "doing: vi /etc/postfix/sasl/smtpd.conf"
cat "pwcheck_method: saslauthd" >> /etc/postfix/sasl/smtpd.conf
cat "mech_list: plain login" >> /etc/postfix/sasl/smtpd.conf
cat "allow_plaintext: true" >> /etc/postfix/sasl/smtpd.conf

/etc/init.d/postfix restart
postfix check
/etc/init.d/saslauthd restart

echo "finished up to now"
exit


eceho "# 6 Configure Courier"

echo "#Now we have to tell Courier that it should authenticate against our MySQL database. First, edit /etc/courier/authdaemonrc and change the value of authmodulelist so that it reads authmodulelist=\"authmysql\""

exit

vi /etc/courier/authmysqlrc
MYSQL_SERVER localhost
MYSQL_USERNAME ehcp
MYSQL_PASSWORD 12345
MYSQL_PORT 0
MYSQL_DATABASE ehcp
MYSQL_USER_TABLE domainusers
MYSQL_CRYPT_PWFIELD password
#MYSQL_CLEAR_PWFIELD password
MYSQL_UID_FIELD 5000
MYSQL_GID_FIELD 5000
MYSQL_LOGIN_FIELD email
MYSQL_HOME_FIELD "/home/vmail"
MYSQL_MAILDIR_FIELD CONCAT(SUBSTRING_INDEX(email,'@',-1),'/',SUBSTRING_INDEX(email,'@',1),'/')
#MYSQL_NAME_FIELD
MYSQL_QUOTA_FIELD quota


/etc/init.d/courier-authdaemon restart
/etc/init.d/courier-imap restart
/etc/init.d/courier-imap-ssl restart
/etc/init.d/courier-pop restart
/etc/init.d/courier-pop-ssl restart


# 7 Install Amavisd-new, SpamAssassin And ClamAV

# To install amavisd-new, spamassassin and clamav, run the following command:

apt-get install amavisd-new spamassassin clamav clamav-daemon zoo unzip unarj bzip2

You will be asked a few questions:

Virus database update method: <-- daemon
Local database mirror site: <-- db.de.clamav.net (Germany; select the mirror that is closest to you)
HTTP proxy information (leave blank for none): <-- (blank)
Should clamd be notified after updates? <-- Yes




.....

adduser clamav amavis
/etc/init.d/amavis restart
/etc/init.d/clamav-daemon restart


postconf -e 'content_filter = amavis:[127.0.0.1]:10024'
postconf -e 'receive_override_options = no_address_mappings'

# note: after installing horde, a user cannot login until its mail folder created by receiving mail.


hata:
postfix/cleanup[7216]: warning: 5D5DE1F641F: virtual_alias_maps map lookup problem for
