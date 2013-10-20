# This document is a summary from: http://www.howtoforge.com/virtual_postfix_mysql_quota_courier
# This file is no longer used in ehcp, left here for historical purposes. 


apt-get install postfix postfix-mysql postfix-doc mysql-client mysql-server courier-authdaemon courier-authmysql courier-pop courier-pop-ssl courier-imap courier-imap-ssl postfix-tls libsasl2 libsasl2-modules libsasl2-modules-sql sasl2-bin libpam-mysql openssl phpmyadmin



You will be asked a few questions: 

Enable suExec? <-- Yes
Create directories for web-based administration ? <-- No
General type of configuration? <-- Internet site
Where should mail for root go? <-- NONE
Mail name? <-- server.iyibirisi.com
Other destinations to accept mail for? (blank for none) <-- server.iyibirisi.com, localhost, localhost.localdomain
Force synchronous updates on mail queue? <-- No
SSL certificate required <-- Ok
Install Hints <-- Ok
Which web server would you like to reconfigure automatically? <-- apache, apache2
Do you want me to restart apache now? <-- Yes



apt-get install build-essential dpkg-dev fakeroot debhelper libdb4.2-dev libgdbm-dev libldap2-dev libpcre3-dev libmysqlclient10-dev libssl-dev libsasl2-dev postgresql-dev po-debconf dpatch (1 line!)

cd /usr/src
apt-get source postfix
wget http://web.onda.com.br/nadal/postfix/VDA/postfix-2.1.5-trash.patch.gz
gunzip postfix-2.1.5-trash.patch.gz
cd postfix-2.1.5
patch -p1 < ../postfix-2.1.5-trash.patch
dpkg-buildpackage
cd ..

dpkg -i postfix_2.1.5-9ubuntu3_i386.deb
dpkg -i postfix-mysql_2.1.5-9ubuntu3_i386.deb

#dpkg -i postfix_2.1.5-9_i386.deb
#dpkg -i postfix-mysql_2.1.5-9_i386.deb
#dpkg -i postfix-tls_2.1.5-9_i386.deb

#---------- mysql setup
mysqladmin -u root password yourrootsqlpassword

Now we create a database called mail:

mysqladmin -u root -p create mail

# Next, we go to the MySQL shell:

mysql -u root -p

#On the MySQL shell, we create the user mail_admin with the passwort mail_admin_password (replace it with your own password) who has SELECT,INSERT,UPDATE,DELETE privileges on the mail database. This user will be used by Postfix and Courier to connect to the mail database:

GRANT SELECT, INSERT, UPDATE, DELETE ON mail.* TO 'mail_admin'@'localhost' IDENTIFIED BY 'mail_admin_password';
GRANT SELECT, INSERT, UPDATE, DELETE ON mail.* TO 'mail_admin'@'localhost.localdomain' IDENTIFIED BY 'mail_admin_password';
FLUSH PRIVILEGES;


CREATE TABLE domains (
domain varchar(50) NOT NULL,
#virtual varchar(50) NOT NULL,
PRIMARY KEY (domain) )
TYPE=MyISAM;

CREATE TABLE forwardings (
source varchar(80) NOT NULL,
destination TEXT NOT NULL,
PRIMARY KEY (source) )
TYPE=MyISAM;

CREATE TABLE users (
email varchar(80) NOT NULL,
password varchar(20) NOT NULL,
quota INT(10) DEFAULT '10485760',
PRIMARY KEY (email)
) TYPE=MyISAM;

CREATE TABLE transport (
domain varchar(128) NOT NULL default '',
transport varchar(128) NOT NULL default '',
UNIQUE KEY domain (domain)
) TYPE=MyISAM;

quit;



#Please make sure that /etc/mysql/my.cnf contains the following line:
# bind-address            = 127.0.0.1




Now let's create our six text files.

/etc/postfix/mysql-virtual_domains.cf:
user = ehcp
password = 12345
dbname = ehcp
table = domains
select_field = 'virtual'
where_field = domainname
hosts = 127.0.0.1

/etc/postfix/mysql-virtual_forwardings.cf:
user = ehcp
password = 12345
dbname = ehcp
table = forwardings
select_field = destination
where_field = source
hosts = 127.0.0.1

/etc/postfix/mysql-virtual_mailboxes.cf:
user = ehcp
password = 12345
dbname = ehcp
table = domainusers
select_field = CONCAT(SUBSTRING_INDEX(email,'@',-1),'/',SUBSTRING_INDEX(email,'@',1),'/')
where_field = email
hosts = 127.0.0.1

/etc/postfix/mysql-virtual_email2email.cf:
user = ehcp
password = 12345
dbname = ehcp
table = domainusers
select_field = email
where_field = email
hosts = 127.0.0.1

/etc/postfix/mysql-virtual_transports.cf:
user = ehcp
password = 12345
dbname = ehcp
table = transport
select_field = transport
where_field = domainname
hosts = 127.0.0.1

/etc/postfix/mysql-virtual_mailbox_limit_maps.cf:
user = ehcp
password = 12345
dbname = ehcp
table = domainusers
select_field = quota
where_field = email
hosts = 127.0.0.1

chmod o= /etc/postfix/mysql-virtual_*.cf
chgrp postfix /etc/postfix/mysql-virtual_*.cf

#Now we create a user and group called vmail with the home directory /home/vmail. This is where all mail boxes will be stored.

groupadd -g 5000 vmail
useradd -g vmail -u 5000 vmail -d /home/vmail -m


postconf -e 'myhostname = server.iyibirisi.com'
postconf -e 'mydestination = server.iyibirisi.com, localhost, localhost.localdomain, server'
postconf -e 'mynetworks = 127.0.0.0/8'
postconf -e 'virtual_alias_domains ='
postconf -e ' virtual_alias_maps = proxy:mysql:/etc/postfix/mysql-virtual_forwardings.cf, proxy:mysql:/etc/postfix/mysql-virtual_email2email.cf'
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

# configure saslauthd
mkdir -p /var/spool/postfix/var/run/saslauthd
Edit /etc/default/saslauthd. Remove the # in front of START=yes and add the line PARAMS="-m /var/spool/postfix/var/run/saslauthd -r".



vi /etc/pam.d/smtp
auth    required   pam_mysql.so user=ehcp passwd=12345 host=127.0.0.1 db=ehcp table=domainusers usercolumn=email passwdcolumn=password crypt=1
account sufficient pam_mysql.so user=ehcp passwd=12345 host=127.0.0.1 db=ehcp table=domainusers usercolumn=email passwdcolumn=password crypt=1

vi /etc/postfix/sasl/smtpd.conf
pwcheck_method: saslauthd
mech_list: plain login
allow_plaintext: true

/etc/init.d/postfix restart
postfix check
/etc/init.d/saslauthd restart



# 6 Configure Courier

#Now we have to tell Courier that it should authenticate against our MySQL database. First, edit /etc/courier/authdaemonrc and change the value of authmodulelist so that it reads
authmodulelist="authmysql"


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
