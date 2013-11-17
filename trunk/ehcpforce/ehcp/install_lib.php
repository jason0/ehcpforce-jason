<?php
$ehcpversion="0.35.2";

// Default variable used as constant
$usePrompts=TRUE;

# last modified by bvidinli on 13.11.2010 (d-m-y)
# include_once("config/dbutil.php");  # dbutil is being removed from project.
/*
notes to who want to change code, developers:

although this library uses functions and global variables,
this library is called by two different install routines, install_1.php and install_2.php
so, even global variables are not passed to install_2.php normal way.
you should put any variables that you want to pass to install_2.php in passvariablestoinstall2 function..
*/


include_once("config/adodb/adodb.inc.php");
include_once("classapp.php");
require_once('console.php');
error_reporting (E_ALL ^ E_NOTICE);
$header="From: info@ehcp.net";
if($installmode=='') $installmode='normal';

if(!function_exists("debugecho")){
function debugecho($str,$level=0) {
	$currentlevel=4;
	if($level>=$currentlevel) echo $str;

}
}

if(!function_exists("securefilename")){
function securefilename($fn){
	$ret=trim($fn);
	$ret=str_replace(array('..','%','&'),array('','',''),$fn);
	#$ret=escapeshellarg($ret);
	return $ret;
}
}


function installaptget(){
	echo "now will try to install apt-get on your system. you need internet connection for this....\n";
	echo "apt-get installation is not implemented yet \n\n";
}

function checkaptget(){
	$cikti=system("which apt-get | wc -w");
	if($cikti>0) {
		echo "apt-get seems to be installed on your system.\n";
	} else {
		echo "apt-get is not installed.. \n";
		installaptget();
	}

}

function log_to_file($str){
	writeoutput("install_log.txt",$str."\n",'a',false);
}

function aptget($arr,$forceInteraction=False){
	/* this was like:
	apt-get install build-essential dpkg-dev fakeroot debhelper libdb4.2-dev libgdbm-dev libldap2-dev libpcre3-dev libmysqlclient10-dev libssl-dev libsasl2-dev postgresql-dev po-debconf dpatch
	but, when one package is not found, whole apt-get install was cancelling.
	to avoid this, each is installed separately.

	tr: herbirisi teker teker kuruluyor. yoksa hata verme ihtimali var.
	iki tip kurulum uygulanabilir, biri hizli, tum apt ler tek seferde, digeri yavas, tek tek... ilk basta sorabilir..
	* */

	global $noapt, $unattended;

	if($noapt<>''){
		echo "apt-get install of these skipped because of noapt parameter:";
		print_r($arr);
		return true;
	}

	foreach($arr as $prog) {
		#
		# first install try
		# assumes yes, do not remove anything, allow any unauthenticated packages,
		# do not remove: this is a security concern
		$cmd="apt-get -y --no-remove --allow-unauthenticated install $prog";
		
		# If unattended, don't show configuration options
		if($unattended && $forceInteraction == FALSE){
			$cmd = "DEBIAN_FRONTEND=noninteractive " . $cmd;
		}
		
		log_to_file($cmd);

		cizgi();
		echo "Starting apt-get install for: $prog\n(cmd: $cmd)\n\n";
		passthru($cmd,$ret);
		echo "The return value for the command \"$cmd\" was $ret\n\n";
		writeoutput("ehcp-apt-get-install.log",$cmd . " command returned $ret","a",false);
		writeoutput("ehcp-apt-get-install.log",$cmd,"a",false);

		if($ret==0) continue;

		# second install try, if first fails :
		# usefull if first one has failed, for reason such as a package has to be removed, if first apt-get exited for any reason, this one executes apt-get with not options, so that user can decide...
		# if first is successfull, this actually does nothing... only prints that those packages are already installed...
		# this way a bit slower, calls apt-get twice, but most "secure and avoids user intervention"
		$cmd="apt-get install $prog";
		echo "\nTrying second installation type for: $prog (cmd: $cmd)\n";
		passthru($cmd);
		writeoutput("ehcp-apt-get-install.log",$cmd,"a",false);
	}

}//endfunc

function bosluk() {
	echo "\n\n\n";
}

function cizgi() {
	echo "\n---------------------------------------------------------------------\n";
}

function bosluk2() {
	bosluk();
	cizgi();
}

function ehcpheader() {
	global $ehcpversion;
	cizgi();
	echo "-----------------------EHCP MAIN INSTALLER---------------------------\n";
	echo "------Easy Hosting Control Panel for Ubuntu, Debian and alikes ------\n";
	echo "--------------------------www.ehcp.net-------------------------------\n";
	cizgi();
	echo "ehcp version $ehcpversion \n";
	echo "ehcp installer version $ehcpversion\n";
}

function bekle($s='') { # wait
	global $unattended;
	
	if(!$unattended) getInput("press enter to continue: $s\n");
}


function getInput($prompt='',$default='',$allowempty=False) {
	global $unattended;
	if($unattended===True and $default<>'') return $default;
	
	if($prompt<>'') echo $prompt;
	
	$giris=trim(Console::GetLine());
	
	if($giris=='') {
		if($allowempty===True) return $giris;
		else return $default; # return default, if input is empty and allowempty is not true
	} else return $giris;
}

if(!function_exists("arraytofile")){
function arraytofile($file,$lines) {
	$new_content = join('',$lines);
	$fp = fopen($file,'w');
	$write = fwrite($fp, $new_content);
	fclose($fp);
}
}

if(!function_exists("addifnotexists")){
function addifnotexists($what,$where) {
	debugecho("\naddifnotexists: ($what) -> ($where) \n ",4);
	#bekle(__FUNCTION__." basliyor..");
	$what.="\n";
	$filearr=@file($where);
	if(!$filearr) {
		echo "cannot open file, trying to setup: ($where)\n";
		$fp = fopen($where,'w');
		fclose($fp);
		$filearr=file($where);

	} //else print_r($file);

	if(array_search($what,$filearr)===false) {
		echo "dosyada bulamadı ekliyor: ($what) -> ($where)\n";
		$filearr[]=$what;
		arraytofile($where,$filearr);

	} else {
		//echo "buldu... sorun yok. \n";
		// already found, so, do not add
	}
	
	#bekle(__FUNCTION__." bitti...");

}
}

function add_if_not_exists2($what,$where,$addfile_if_not_exists=False) {
	# add a string/config value onto (at the end of) a file...
	# difference from addifnotexists: it uses arrays, this will use string, so, main.cf and similar config files will be handled better.. i hope..
	# the $what should include newline too..
	# may raise error if file too big for php strings..

	# get file
	$file=@file_get_contents($where);
	if($file===false) {
		if($addfile_if_not_exists) file_put_contents($where,'');
		else {
			echo __FUNCTION__.": cannot open file...($where ) \n";
			return false;
		}
	}

	# add if not exist:
	$bul=strstr($file,$what);
	if($bul===false) $file.=$what;

	#write back
	$ret=file_put_contents($where,$file);
	if($ret===false){
		echo __FUNCTION__.": cannot write file back: ($where) \n";
		return false;
	}

	echo __FUNCTION__.": success add strings to $where \n";
	return true;
}

function fail2ban_install(){ # thanks to  earnolmartin@gmail.com
	global $installmode;
	
	switch($installmode) {
		case 'extra': 
		case 'normal': 
	
			echo "Starting fail2ban install \n";
			aptget(array('fail2ban'));
			fail2ban_config();
			echo "Finished fail2ban install \n";
	
			break;
	
		case 'light':
			break;
		
		default: echo "Unknown installmode parameter at ".__LINE__;
	}
		
	
}

function fail2ban_config(){
		global $ehcpinstalldir,$user_email;		
		copy("$ehcpinstalldir/fail2ban/ehcp.conf","/etc/fail2ban/filter.d/ehcp.conf");
		$f="/etc/fail2ban/jail.local";
		if(!file_exists($f)) copy("$ehcpinstalldir/fail2ban/jail.local",$f);
		
		replacelineinfile("destemail","destemail = $user_email",$f);
		
		$s=file_get_contents($f);
		if(strstr($s,"[ehcp]")===false){ # if not already configured, 
			$ehcpF2Config="
[ehcp]
# fail2ban section for Easy Hosting Control Panel, ehcp.net
enabled = true
port = http,https
filter = ehcp
logpath = /var/www/new/ehcp/log/ehcp_failed_authentication.log
maxretry = 10";
			file_put_contents($f,$ehcpF2Config,FILE_APPEND);
			#append_to_file($f,$ehcpF2Config);			
		}
		
}

function replace_in_file($find,$replace,$sourcefile,$targetfile){
	# open source/sample file, find $find, replace it to $replace, write result to $target file
	# especially for editing config files, like replacing {ehcppassword} to real passwords..


	# get file
	$file=file_get_contents($sourcefile);
	if($file===false) {
		echo __FUNCTION__.": cannot open file...($sourcefile ) \n";
		return false;
	}

	# $find->$replace
	$file=str_replace($find,$replace,$file);

	#write back
	$ret=file_put_contents($targetfile,$file);
	if($ret===false){
		echo __FUNCTION__.": cannot write file back: ($targetfile) \n";
		return false;
	}

	echo __FUNCTION__.": success replace $find in $sourcefile -> $targetfile  \n";
	return true;

}



if(!function_exists('replacelineinfile')){
function replacelineinfile($find,$replace,$where) {
	// edit a line starting with $find, to edit especially conf files..

	debugecho("\nreplaceline: ($find -> $replace) in ($where) \n ");

	$filearr=@file($where);
	//if($find=='$dbrootpass=') print_r($filearr);

	if(!$filearr) {
		echo "cannot open file... returning...\n";
		return false;
	} //else print_r($file);

	$len=strlen($find);
	$newfile=array();

	foreach($filearr as $line){
		$line=trim($line)."\n";
		$sub=substr($line,0,$len);
		if($sub==$find) $line=$replace."\n";
		$newfile[]=$line;

	}

	/*if($find=='$dbrootpass=') {
		echo "yeni dosya:\n";
		print_r($newfile);
	}*/

	arraytofile($where,$newfile);
}
}


if(!function_exists("editlineinfile")){
function editlineinfile($find,$replace,$where) {
	// edit a line containing  $find, replace it... to edit especially /etc/apt/sour/sources.list file

	debugecho("\n replaceline: ($find -> $replace) in ($where) \n ");
	$filearr=@file($where);


	if(!$filearr) {
		echo "cannot open file... returning...\n";
		return false;
	} //else print_r($file);

	$newfile=array();

	foreach($filearr as $line){
		$line=trim($line)."\n";
		$line=str_replace($find,$replace,$line);
		$newfile[]=$line;
	}
	arraytofile($where,$newfile);
}
}


if(!function_exists("writeoutput")){
function writeoutput($file, $string, $mode="w",$log=true) {
	if (!($fp = fopen($file, $mode))) {
			echo "hata: dosya acilamadi: $file (writeoutput) !";
			return false;
	}
	if (!fputs($fp, $string . "\n")) {
			fclose($fp);
			echo "hata: dosyaya yazilamadi: $file (writeoutput) !";
			return false;
	}
	fclose($fp);
	if($log) echo "\n(".__FILE__.") file written successfully: $file, mode:$mode \n";
	return true;
}
}

if(!function_exists('getlocalip')){
function getlocalip($interface='eth0') {
	global $localip;
	//$interface="eth0";
	$ipline=exec("ifconfig $interface | grep \"inet addr\"");
// 	echo $ipline."\n\n";
	$ipline=strstr($ipline,"addr:");
// 	echo $ipline."\n\n";
	$pos=strpos($ipline," ");
	$ipline=trim(substr($ipline,5,$pos-5));
// 	echo "($ipline)\n\n";
	$localip=$ipline;
	# echo "(getlocalip) your ip is determined to be ($localip) using interface $interface \n";
	return $ipline;
}
}


function getlocalip2($interface='eth0') {
	global $localip;

	if($localip<>'') return $localip;

	$ip=getlocalip($interface);
	if($ip=='') $ip=getlocalip('eth1');
	if($ip=='') $ip=getlocalip('eth2');

	if($ip=='') {
		$ipline=exec("ifconfig | grep 'inet ' | grep 'dres' | grep 255.255 | grep -v '127.0.0'");
		$ipline=strstr($ipline,"addr:");
		$pos=strpos($ipline," ");
		$ip=trim(substr($ipline,5,$pos-5));

		if($ip=='') echo "Your ip cannot be determined automatically... \n";
	}
	$localip=$ip;
	return $ip;
}

function dovecot_install_configuration($params){
	# use quide: http://workaround.org/articles/ispmail-etch/
	# remove all courier
	# install dovecot using apt-get
	# configure  dovecot using mysql auth....
}

function copyPostFixConfig(){
	if(!file_exists('/etc/postfix/main.cf')) passthru2("cp ".$app->ehcpdir."/etc/postfix/main.cf.sample /etc/postfix/main.cf"); # on some systems, this is deleted somehow.
}

function mailNameFix(){
	$mailname= @file_get_contents('/etc/mailname');
	if(trim($mailname)==''){
		$mailname="mail.".gethostname();
		file_put_contents('/etc/mailname', $mailname);
	}
}

function mailconfiguration($params) {
	global $app,$ehcpinstalldir,$ip,$hostname,$user_email,$user_name,$ehcpmysqlpass,$rootpass,$newrootpass,$ehcpadminpass;
echo "configuring mail ... ".__FUNCTION__."\n";

# very similar to: https://help.ubuntu.com/community/PostfixCompleteVirtualMailSystemHowto
#print_r($params);
# echo 'var_dump($ehcpmysqlpass,$rootpass,$newrootpass,$ehcpadminpass);\n';
# var_dump($ehcpmysqlpass,$rootpass,$newrootpass,$ehcpadminpass);
/*

 courier'e alternatif: dovecot:
 *
http://www.opensourcehowto.org/how-to/mysql/mysql-users-postfixadmin-postfix-dovecot--squirrelmail-with-userprefs-stored-in-mysql.html
http://www.howtoforge.com/virtual-users-and-domains-postfix-dovecot-mysql-centos4.5
http://workaround.org/articles/ispmail-etch/

Gerekli arama: /etc/dovecot-mysql.conf

files to edit:
/etc/postfix/mysql-virtual_domains.cf
/etc/postfix/mysql-virtual_forwardings.cf
/etc/postfix/mysql-virtual_mailboxes.cf
/etc/postfix/mysql-virtual_email2email.cf
/etc/postfix/mysql-virtual_mailbox_limit_maps.cf
/etc/postfix/mysql-virtual_transports.cf

maybe we can switch to dovecot, if  i can, a good start: http://workaround.org/ispmail/etch

*/




$filecontent="
user = ehcp
password = ".$params['ehcppass']."
dbname = ehcp
table = domains
select_field = 'virtual'
where_field = domainname
hosts = localhost
additional_conditions = and domainname in (select DISTINCT domainname from emailusers union select domainname from forwardings union select domainname from emailusers)
";

writeoutput("/etc/postfix/mysql-virtual_domains.cf",$filecontent,"w");

$filecontent="
user = ehcp
password = ".$params['ehcppass']."
dbname = ehcp
table = forwardings
select_field = destination
where_field = source
hosts = localhost
";
writeoutput("/etc/postfix/mysql-virtual_forwardings.cf",$filecontent,"w");

$filecontent="
user = ehcp
password = ".$params['ehcppass']."
dbname = ehcp
table = emailusers
select_field = CONCAT(SUBSTRING_INDEX(email,'@',-1),'/',SUBSTRING_INDEX(email,'@',1),'/')
where_field = email
hosts = localhost
";
writeoutput("/etc/postfix/mysql-virtual_mailboxes.cf",$filecontent,"w");


$filecontent="
user = ehcp
password = ".$params['ehcppass']."
dbname = ehcp
table = emailusers
select_field = email
where_field = email
hosts = localhost
";
writeoutput("/etc/postfix/mysql-virtual_email2email.cf",$filecontent,"w");

$filecontent="
user = ehcp
password = ".$params['ehcppass']."
dbname = ehcp
table = emailusers
select_field = quota
where_field = email
hosts = localhost
";

writeoutput("/etc/postfix/mysql-virtual_mailbox_limit_maps.cf",$filecontent,"w");


# autoreply configuration: coded like: http://www.progression-asia.com/node/87

/*
I used ehcp's own php application, autoreply.php, instead of yaa.pl, since yaa.pl failed somehow, I wrote autoreply simply..

 required db tables:(these will be setup in sql section)

CREATE TABLE transport (
domainname varchar(128) NOT NULL default '',
transport varchar(128) NOT NULL default '',
UNIQUE KEY domainname (domainname)
) TYPE=MyISAM;

*/

$filecontent="
user = ehcp
password = ".$params['ehcppass']."
dbname = ehcp
table = transport
select_field = transport
where_field = domainname
hosts = localhost
";

writeoutput("/etc/postfix/mysql-virtual_transports.cf",$filecontent,"w");

# edit main.cf:
$add="
# ehcp: autoresponder code:
ehcp_autoreply unix - n n - - pipe
  user=vmail
  argv=".$app->ehcpdir."/misc/autoreply.php \$sender \$recipient
";

add_if_not_exists2($add,'/etc/postfix/master.cf'); # this function may also be used to setup spamassassin and related stuff. soon to implement spamassassin support in ehcp automatically. (manually always possible..)
replace_in_file("#submission inet n       -       -       -       -       smtpd","submission inet n       -       -       -       -       smtpd",'/etc/postfix/master.cf','/etc/postfix/master.cf');
add_if_not_exists2("submission inet n       -       -       -       -       smtpd",'/etc/postfix/master.cf');  # 587 kullanan yerler icin.. 

# classapp'da checktable yapılacak yenile..
# end autoreply configuration
copyPostFixConfig();


addifnotexists("bind-address=127.0.0.1","/etc/mysql/my.cnf");
addifnotexists("skip-innodb","/etc/mysql/my.cnf"); # disable innodb by default, because it consumes a lot of memory

passthru3("chmod o= /etc/postfix/mysql-virtual_*.cf");
passthru3("chgrp postfix /etc/postfix/mysql-virtual_*.cf");

#Now we setup a user and group called vmail with the home directory /home/vmail. This is where all mail boxes will be stored.

passthru3("groupdel vmail");
passthru3("userdel vmail");
echo "----------- Other user/group with uid/gid of 5000, you need to delete them, if any -----------";
passthru3("grep 5000 /etc/passwd ");
passthru3("grep 5000 /etc/group ");
echo "----------- ----------- ----------- ----------- ----------- ----------- ----------- ----------";


passthru3("groupadd -g 5000 vmail");
passthru3("useradd -g vmail -u 5000 vmail -d /home/vmail -m");
passthru3("chown -Rf vmail /home/vmail");
passthru3("adduser postfix sasl");

// burda input vardi... initialize a aktarildi..
$hostname=exec("hostname");


// ipnin ilk uc rakami alinip network alınacak
$ips=explode(".",$ip);
array_pop($ips);
$ips[]="0/24"; // calculate C class net number.
$net=implode(".",$ips);

passthru3("openssl req -new -config $ehcpinstalldir/LocalServer.cnf -outform PEM -out /etc/postfix/smtpd.cert -newkey rsa:2048 -nodes -keyout /etc/postfix/smtpd.key -keyform PEM -days 365 -x509");
passthru3("chmod o= /etc/postfix/smtpd.key");
passthru3("openssl req -passout pass:$ehcpmysqlpass -new -x509 -keyout /etc/postfix/cakey.pem -out /etc/postfix/cacert.pem -days 3650  -config $ehcpinstalldir/LocalServer.cnf"); ## yeni 13.6.2009


passthru3("postconf -e \"myhostname = $hostname\"");
passthru3("postconf -e \"relayhost = \"");
passthru3("postconf -e \"mydestination = localhost, $ip \"");
passthru3("postconf -e 'mynetworks = 127.0.0.0/8, 192.168.0.0/16, 172.16.0.0/16, 10.0.0.0/8,  $net '");
passthru3("postconf -e 'virtual_alias_domains ='");
passthru3("postconf -e 'virtual_alias_maps = proxy:mysql:/etc/postfix/mysql-virtual_forwardings.cf, proxy:mysql:/etc/postfix/mysql-virtual_email2email.cf'");
passthru3("postconf -e 'transport_maps = proxy:mysql:/etc/postfix/mysql-virtual_transports.cf'"); #autoresponder

passthru3("postconf -e 'virtual_mailbox_domains = proxy:mysql:/etc/postfix/mysql-virtual_domains.cf'");
passthru3("postconf -e 'virtual_mailbox_maps = proxy:mysql:/etc/postfix/mysql-virtual_mailboxes.cf'");
passthru3("postconf -e 'virtual_mailbox_base = /home/vmail'");
passthru3("postconf -e 'virtual_uid_maps = static:5000'");
passthru3("postconf -e 'virtual_gid_maps = static:5000'");
passthru3("postconf -e 'smtpd_sasl_auth_enable = yes'");
passthru3("postconf -e 'smtpd_sasl_security_options = noanonymous'");
passthru3("postconf -e 'broken_sasl_auth_clients = yes'");

passthru3("postconf -e 'smtpd_recipient_restrictions = permit_mynetworks,permit_sasl_authenticated,check_client_access hash:/var/lib/pop-before-smtp/hosts,reject_unauth_destination'"); // this is used with pop-before-smtp
#passthru3("postconf -e 'smtpd_recipient_restrictions = permit_mynetworks,permit_sasl_authenticated,reject_unauth_destination'"); // this is used with sasl authenticated
passthru3("postconf -e 'smtp_use_tls = yes'"); ## yeni
passthru3("postconf -e 'smtpd_use_tls = yes'");
passthru3("postconf -e 'smtpd_tls_auth_only = no'"); ## yeni
passthru3("postconf -e 'smtpd_tls_CAfile = /etc/postfix/cacert.pem'"); ## yeni 13.6, dd.mm
passthru3("postconf -e 'smtpd_tls_cert_file = /etc/postfix/smtpd.cert'");
passthru3("postconf -e 'smtpd_tls_key_file = /etc/postfix/smtpd.key'");
# this is partially taken from https://help.ubuntu.com/8.04/serverguide/C/postfix.html
passthru3("postconf -e 'smtpd_tls_loglevel = 1'"); ## yeni 13.6, dd.mm
passthru3("postconf -e 'smtpd_tls_received_header = yes'"); ## yeni 13.6, dd.mm
passthru3("postconf -e 'smtpd_tls_session_cache_timeout = 3600s'"); ## yeni 13.6, dd.mm
passthru3("postconf -e 'tls_random_source = dev:/dev/urandom'"); ## yeni 13.6, dd.mm

passthru3("postconf -e 'virtual_create_maildirsize = yes'");
passthru3("postconf -e 'virtual_mailbox_extended = yes'");
passthru3("postconf -e 'virtual_mailbox_limit_maps = proxy:mysql:/etc/postfix/mysql-virtual_mailbox_limit_maps.cf'");
passthru3("postconf -e 'virtual_mailbox_limit_override = yes'");
passthru3("postconf -e 'virtual_maildir_limit_message = \"The user you are trying to reach is over quota.\"'");
passthru3("postconf -e 'virtual_overquota_bounce = yes'");
passthru3("postconf -e 'debug_peer_list = '");
passthru3("postconf -e 'sender_canonical_maps = '");
passthru3("postconf -e 'debug_peer_level = 1'");
passthru3("postconf -e 'virtual_overquota_bounce = yes'");
passthru3("postconf -e 'proxy_read_maps = \$local_recipient_maps \$mydestination \$virtual_alias_maps \$virtual_alias_domains \$virtual_mailbox_maps \$virtual_mailbox_domains \$relay_recipient_maps \$canonical_maps \$sender_canonical_maps \$recipient_canonical_maps \$relocated_maps \$mynetworks \$virtual_mailbox_limit_maps \$transport_maps'");
passthru3("postconf -e 'smtpd_banner =\$myhostname ESMTP \$mail_name powered by Easy Hosting Control Panel (ehcp) on Ubuntu, www.ehcp.net'");

# passthru3("dpkg-statoverride --force --update --add root sasl 755 /var/run/saslauthd"); # may be required on some systems...

echo "configuring saslauthd \n";
passthru3("mkdir -p /var/spool/postfix/var/run/saslauthd");

# here, both params, options added, in case it may be changed.
$filecontent="
NAME=\"saslauthd\"
START=yes
MECHANISMS=\"pam\"
PARAMS=\"-m /var/spool/postfix/var/run/saslauthd -r\"
OPTIONS=\"-m /var/spool/postfix/var/run/saslauthd -r\"
";

writeoutput("/etc/default/saslauthd",$filecontent,"w");
replacelineinfile("PIDFILE=","PIDFILE=\"/var/spool/postfix/var/run/\${NAME}/saslauthd.pid\"",'/etc/init.d/saslauthd');

configurepamsmtp(array('ehcppass'=>$ehcpmysqlpass));


echo "editing: /etc/postfix/sasl/smtpd.conf\n";
$filecontent="
pwcheck_method: saslauthd
mech_list: plain login
allow_plaintext: true
";
writeoutput("/etc/postfix/sasl/smtpd.conf",$filecontent,"w");

echo "Configuring Courier\n";
echo "Now configuring to tell Courier that it should authenticate against our MySQL database.";
addifnotexists("authmodulelist=\"authmysql\"","/etc/courier/authdaemonrc");

//** tablo ismi degisirse, asagidaki emailusers da degismeli

configureauthmysql(array('ehcppass'=>$ehcpmysqlpass));
passthru("chown -Rvf postfix /var/lib/postfix/");
passthru("chmod -R 755 /var/spool/postfix");
passthru("chmod 1733 /var/spool/postfix/maildrop");

passthru2("newaliases"); # on some systems, aliases.db is deleted by user or somehow,  this fixes that.
passthru("cp -vf pop-before-smtp.conf /etc/pop-before-smtp/");

# adjust roundcube:
# adjust symlink for roundcube
passthru2("ln -s /usr/share/roundcube /var/www/new/ehcp/webmail2");
replacelineinfile("\$rcmail_config['default_host']","\$rcmail_config['default_host']='localhost';",'/etc/roundcube/main.inc.php');
# end adjust roundcube

foreach(array('pop-before-smtp','postfix','saslauthd','courier-authdaemon','courier-imap','courier-imap-ssl','courier-pop','courier-pop-ssl') as $service)
	passthru("/etc/init.d/$service restart");

passthru("postfix check");


}# end mailconfiguration

function configurepamsmtp($params){
echo "editing: /etc/pam.d/smtp   (".__FUNCTION__.")\n";
$filecontent="
auth	required   pam_mysql.so user=ehcp passwd=".$params['ehcppass']." host=127.0.0.1 db=ehcp table=emailusers usercolumn=email passwdcolumn=password crypt=1
account sufficient pam_mysql.so user=ehcp passwd=".$params['ehcppass']." host=127.0.0.1 db=ehcp table=emailusers usercolumn=email passwdcolumn=password crypt=1
";
writeoutput("/etc/pam.d/smtp",$filecontent,"w");
}

function configureauthmysql($params){
echo "(".__FUNCTION__.")\n";
$filecontent="
MYSQL_SERVER localhost
MYSQL_USERNAME ehcp
MYSQL_PASSWORD ".$params['ehcppass']."
MYSQL_PORT 0
MYSQL_DATABASE ehcp
MYSQL_USER_TABLE emailusers
MYSQL_CRYPT_PWFIELD password
#MYSQL_CLEAR_PWFIELD password
MYSQL_UID_FIELD 5000
MYSQL_GID_FIELD 5000
MYSQL_LOGIN_FIELD email
MYSQL_HOME_FIELD \"/home/vmail\"
MYSQL_MAILDIR_FIELD CONCAT(SUBSTRING_INDEX(email,'@',-1),'/',SUBSTRING_INDEX(email,'@',1),'/')
#MYSQL_NAME_FIELD
MYSQL_QUOTA_FIELD quota
";
writeoutput("/etc/courier/authmysqlrc",$filecontent,"w");
}

# Returns just the release year of an Ubuntu distro
function getReleaseYear($ver){ #by earnolmartin@gmail.com
	if(isset($ver) && !empty($ver) && stripos($ver, '.') != FALSE){
		$releaseYear = substr($ver, 0, stripos($ver, '.'));
		return $releaseYear;
	}
}

function mysqldebconf($rYear){ #by earnolmartin@gmail.com
	switch($rYear){
		case "10":
			$comms[] = "echo 'mysql-server-5.1 mysql-server/root_password_again password 1234' | debconf-set-selections";
			$comms[] = "echo 'mysql-server-5.1 mysql-server/root_password password 1234' | debconf-set-selections";
			break;
		case "12":
		case "13":
			$comms[] = "echo 'mysql-server-5.5 mysql-server/root_password_again password 1234' | debconf-set-selections";
			$comms[] = "echo 'mysql-server-5.5 mysql-server/root_password password 1234' | debconf-set-selections";
			break;
	}
	
	# For all versions
	$comms[] = "echo 'mysql-server mysql-server/root_password_again password 1234' | debconf-set-selections";
	$comms[] = "echo 'mysql-server mysql-server/root_password password 1234' | debconf-set-selections";
	
	return $comms;
}

function reloadAppArmorMySQL(){
	// Function needed to fix MySQL Socket Bug in Ubuntu?
	passthru3("service mysql stop");
	passthru3("killall mysqld_safe");
	passthru3("killall mysqld");
	// Even if this fails, things should keep moving smoothly.
	passthru3("/etc/init.d/apparmor reload");
	passthru3("service mysql start");
}

function installMySQLServ(){#by earnolmartin@gmail.com
	global $unattended, $distro, $version, $usePrompts;
	
	# Get distro release year
	$rYear = getReleaseYear($version);
	
	# Get question answers for installer package
	if($unattended && $distro == "ubuntu"){
		$comms = mysqldebconf($rYear);
		if(isset($comms) && is_array($comms)){
			foreach($comms as $comm){
				passthru3($comm);
			}
		}
	}

	# Install MySQL Server With Pre-Answered Prompts
	aptget(array('mysql-server'),$usePrompts);
}

function installPHPMYAdmin(){#by earnolmartin@gmail.com
	global $unattended, $usePrompts;
	
	if($unattended){
		# Answer automatic configuration questions
		# http://gercogandia.blogspot.com/2012/11/automatic-unattended-install-of.html 
		passthru3("echo 'phpmyadmin phpmyadmin/dbconfig-install boolean true' | debconf-set-selections");
		passthru3("echo 'phpmyadmin phpmyadmin/app-password-confirm password 1234' | debconf-set-selections");
		passthru3("echo 'phpmyadmin phpmyadmin/mysql/admin-pass password 1234' | debconf-set-selections");
		passthru3("echo 'phpmyadmin phpmyadmin/mysql/app-pass password 1234' | debconf-set-selections");
		passthru3("echo 'phpmyadmin phpmyadmin/reconfigure-webserver multiselect apache2' | debconf-set-selections");
		passthru3("echo 'phpmyadmin phpmyadmin/dbconfig-reinstall boolean true' | debconf-set-selections");
	}
	
	# Install PHPMyAdmin With Pre-Answered Prompts
	aptget(array('phpmyadmin'),$usePrompts);
}

function installRoundCube(){#by earnolmartin@gmail.com
	global $unattended, $usePrompts;
	
	if($unattended){
		# Answer automatic configuration questions
		# http://gercogandia.blogspot.com/2012/11/automatic-unattended-install-of.html 
		passthru3("echo 'roundcube-core roundcube/password-confirm password 1234' | debconf-set-selections");
		passthru3("echo 'roundcube-core roundcube/mysql/admin-pass password 1234' | debconf-set-selections");
		passthru3("echo 'roundcube-core roundcube/mysql/app-pass password 1234' | debconf-set-selections");
		passthru3("echo 'roundcube-core roundcube/app-password-confirm password 1234' | debconf-set-selections");
		passthru3("echo 'roundcube-core roundcube/database-type select mysql' | debconf-set-selections");
		passthru3("echo 'roundcube-core roundcube/dbconfig-install boolean true' | debconf-set-selections");
	}
	
	# Install Roundcube With Pre-Answered Prompts
	aptget(array('roundcube', 'roundcube-mysql'),$usePrompts);
}

function installmailserver(){
	global $app,$ehcpinstalldir,$ip,$hostname,$user_email,$user_name,$ehcpmysqlpass,$rootpass,$newrootpass,$ehcpadminpass,$installmode,$unattended;
	echo "starting mail server installation (postfix and related programs)\n\n";
	
	# If /etc/postfix/main.cf does not exist, it must exist before unattended install will work properly with PostFix
	# See here:
	# http://www.whatastruggle.com/postfix-non-interactive-install
	# Added by Eric Arnol-Martin <earnolmartin@gmail.com>
	if($unattended) copyPostFixConfig();
	
	# Install these packages and answer configuration questions if unattended
	# Then install the rest of the packages
	# Added by Eric Arnol-Martin <earnolmartin@gmail.com>
	# Place these functions wherever you want in your switch statement... they are currently here for testing
	
	switch($installmode) {
		case 'extra': 
		case 'normal': installRoundCube();installPHPMYAdmin();
		case 'light': aptget(array('postfix','postfix-mysql','mysql-client','courier-authdaemon','courier-authmysql','courier-authlib-mysql','courier-pop','courier-pop-ssl','courier-imap','courier-imap-ssl','libsasl2-2','libsasl2','libsasl2-modules','libsasl2-modules-sql','sasl2-bin','libpam-mysql','openssl','pop-before-smtp')); # changed libsasl2-2 to libsasl2 **
			break;
		default: echo "Unknown installmode parameter at ".__LINE__;
	}
	
	// No longer needed
	// Below lines were causing problems on latest Ubuntu distros
	// passthru2("killall mysqld_safe"); # because, after first install of mysql, this process somehow uses %100 of cpu, in an endless loop.. kill this and restart mysql..
	// passthru2("killall mysqld");
	sleep(10);
	passthru2("/etc/init.d/mysql restart ");
	passthru2("cp -rf /usr/share/phpmyadmin /var/www/new");
	
	# aptitude install postfix postfix-mysql postfix-doc mysql-client courier-authdaemon courier-authmysql courier-authlib-mysql courier-pop courier-pop-ssl courier-imap courier-imap-ssl libsasl2-2 libsasl2 libsasl2-modules libsasl2-modules-sql sasl2-bin libpam-mysql openssl phpmyadmin pop-before-smtp


	#remove:  apt-get remove postfix postfix-mysql postfix-doc mysql-client mysql-server courier-authdaemon courier-authmysql courier-pop courier-pop-ssl courier-imap courier-imap-ssl libsasl2 libsasl2-modules libsasl2-modules-sql sasl2-bin libpam-mysql openssl phpmyadmin
	bosluk2();
	# all mail configuration should be moved into this function: bvidinli, to be able to re-configure mail later
	mailconfiguration(array('ehcppass'=>$ehcpmysqlpass));
	mailNameFix();
	
	echo "\n\nfinished mail server,pop3,imap installation \n";
}

function rebuild_nginx_config2($mydir){
	global $app;
	
	passthru3("rm -rvf /etc/nginx/sites-enabled/*");
	
	#passthru2("cp $mydir/etc/nginx/nginx.conf /etc/nginx/nginx.conf");
	
	$conf=file_get_contents("$mydir/etc/nginx/nginx.conf"); # replace tags with actual values from class
	$conf=str_replace(array('{wwwuser}','{wwwgroup}'),array($app->wwwuser,$app->wwwgroup),$conf);
	file_put_contents("/etc/nginx/nginx.conf",$conf);
	
	
	passthru2("cp $mydir/etc/nginx/default.nginx /etc/nginx/sites-enabled/default");
	passthru2("cp $mydir/etc/nginx/apachetemplate.nginx $mydir/apachetemplate");
	passthru2("cp $mydir/etc/nginx/apache_subdomain_template.nginx $mydir/apache_subdomain_template");
	replacelineinfile("listen =","listen = 9000","/etc/php5/fpm/pool.d/www.conf");
	passthru2("/etc/init.d/php5-fpm restart"); # this does not work on some systems.. needs another binary to work
}

function install_nginx_webserver(){
	# thanks to webmaster@securitywonks.net for encourage of nginx integration
	echo "\nStarting nginx webserver install (not default)\n";
	#bekle();
	aptget(array('nginx','php5-fpm','php5-cgi'));  # apt-get install nginx php5-fpm php5-cgi
	copy("$mydir/etc/nginx/mime.types","/etc/nginx/mime.types");
	
	#rebuild_nginx_config2(".");	# this will be done when nginx is selected from panel. 
	
	passthru2("/etc/init.d/php5-fpm stop");
	passthru2("update-rc.d -f nginx remove");  # apache is default	
	passthru2("/etc/init.d/nginx stop");
	
	echo "\nEnd nginx install\n";
	#bekle();
}

function installapacheserver($apacheconf=''){
	global $app,$ehcpinstalldir;
	echo "\nStarting apache2 webserver install (default webserver)\n";
	#bekle(__FUNCTION__." basliyor..");
	
	aptget(array('libapache2-mod-php5','php5'));
	rebuild_apache2_config2();
}

function rebuild_apache2_config2(){
	global $app,$ehcpinstalldir;
        echo "* installpath set as: $ehcpinstalldir \n";
        
	$tarih=date("YmdHis");	
	passthru2("cp -rvf /etc/apache2 /etc/apache2.backupbyehcp.$tarih");	
	addifnotexists("Include $ehcpinstalldir/apachehcp_subdomains.conf ", "/etc/apache2/apache2.conf");
	addifnotexists("Include $ehcpinstalldir/apachehcp_auth.conf ", "/etc/apache2/apache2.conf");
	addifnotexists("Include $ehcpinstalldir/apachehcp_passivedomains.conf ", "/etc/apache2/apache2.conf");
	addifnotexists("Include $ehcpinstalldir/apachehcp.conf", "/etc/apache2/apache2.conf");
	#replacelineinfile('NameVirtualHost','NameVirtualHost *','/etc/apache2/ports.conf');
	#editlineinfile("Options Indexes","Options -Indexes","/etc/apache2/sites-enabled/000-default");


	addifnotexists("ServerName myserver", "/etc/apache2/apache2.conf");
	if(file_exists("/etc/apache2/envvars")) {
		replacelineinfile("export APACHE_RUN_USER=","export APACHE_RUN_USER=".$app->wwwuser,"/etc/apache2/envvars");
		replacelineinfile("export APACHE_RUN_GROUP=","export APACHE_RUN_GROUP=".$app->wwwgroup,"/etc/apache2/envvars");
		addifnotexists("umask 111","/etc/apache2/envvars"); # newly added files wil have rw-rw-rw- permission, allowing all to read-write them. this will enable vsftpd to delete webserver-generated files.. before this, those files could not be deleted... 
	} else {
		replacelineinfile("User ","User ".$app->wwwuser,"/etc/apache2/apache2.conf");
		replacelineinfile("User ","Group ".$app->wwwgroup,"/etc/apache2/apache2.conf");		
	}
	#replacelineinfile('DocumentRoot /','DocumentRoot /var/www','/etc/apache2/sites-available/default');

	passthru2("cp -vf /etc/apache2/sites-available/default /etc/apache2/sites-available/default.original.$tarih"); # backup original apache default conf
	passthru2("cp -vf $ehcpinstalldir/etc/apache2/default /etc/apache2/sites-available/default");	   # write new conf with new settings that has -Indexes and so on... may be disabled, may be incompatible with future versions of apache2
	copy("/etc/apache2/sites-available/default","/etc/apache2/sites-available/default.backupbyehcp.".date("YmdHis")); # backup old config..
	
	passthru2("cp -vf $ehcpinstalldir/etc/apache2/default /etc/apache2/sites-available/000-default");
	passthru2("cp -vf $ehcpinstalldir/etc/apache2/apachetemplate_ipbased $ehcpinstalldir/");
	passthru2("cp -vf $ehcpinstalldir/etc/apache2/apachetemplate $ehcpinstalldir/");
	passthru2("cp -vf $ehcpinstalldir/etc/apache2/apachetemplate_passivedomains $ehcpinstalldir/");
	passthru2("cp -vf $ehcpinstalldir/etc/apache2/apache_subdomain_template $ehcpinstalldir/");
	passthru2("cp -vf $ehcpinstalldir/etc/apache2/ports.conf /etc/apache2/");
	passthru3("rm -rvf /etc/apache2/sites-enabled/*");
	passthru2("cp -vf $ehcpinstalldir/etc/apache2/default /etc/apache2/sites-enabled/");
	
	#passthru2("ln -s /etc/apache2/mods-available/rewrite.load /etc/apache2/mods-enabled/rewrite.load");
	passthru2("a2enmod rewrite");
	passthru2("a2enmod php5");
	passthru2("a2enmod expires");
	passthru2("a2enmod headers");
	passthru2("cp -vf /etc/apache2/mods-available/php5.* /etc/apache2/mods-enabled/");

	passthru("cp ./wwwindex.html /var/www/apache2-default/index.html");
	passthru("cp -rvf ./images_default_index /var/www/apache2-default/");

	# default apache setting
	passthru("cp ./wwwindex.html /var/www/index.html");
	passthru("cp -rvf ./images_default_index /var/www/");

	# new ehcp setting since ver 0.29.15 - added at 13.11.2010
	passthru2("mkdir -p /var/www/new");
	passthru("cp ./wwwindex.html /var/www/new/index.html");
	passthru("cp -rvf ./images_default_index /var/www/new/");

	passthru("cp ./ehcp /etc/init.d/");
	passthru("cp ./ehcp_daemon.py /etc/init.d/");

	passthru("chmod a+r /var/www/apache2-default/index.html");
	passthru("chmod a+r /var/www/index.html");

	bosluk2();
}

function install_pure_ftpserver() {
	global $app;
	#----------------------start ftp install --------------------------------------
	# this works on ubuntu, but not on debian, so, disabled at the moment
	echo "Now, going to install pureftpd to your server,";
	aptget(array('pure-ftpd-mysql'));

	passthru("groupadd -g 2001 ftpgroup");
	passthru("useradd -u 2001 -s /bin/false -d /bin/null -c \"pureftpd user\" -g ftpgroup ftpuser");
	passthru("cp -rvf pureftpd_mysql.conf /etc/pure-ftpd/db/mysql.conf");
	passthru("echo yes > /etc/pure-ftpd/conf/ChrootEveryone");
	passthru("echo yes > /etc/pure-ftpd/conf/CreateHomeDir");
	bosluk();
	echo "you should manually uninstall any other ftp server from your system... \n";


	passthru("/etc/init.d/pure-ftpd-mysql start");
	echo "finished pureftpd installation. your ftp server now should be ready.\n";
	#bekle();
	bosluk2();
	#----------------------end ftp install --------------------------------------
}

function remove_pure_ftpserver(){
	// to be coded later, if needed.
}

function vsftpd_configuration($params){
	global $app,$ip; #$ehcpinstalldir,$ip,$user_email,$user_name,$ehcpmysqlpass,$rootpass,$newrootpass,$ehcpadminpass;

	# this function is written to allow changing password later, after install... it also makes configuration while install...
	echo "configuring vsftpd:  (".__FUNCTION__.")\n";
	
	# burda sorun su: mysql password( fonksiyonu, mysqlde internal kullaniliyormus, bu yuzden normal programlarda kullanilmamaliymis..
	# denedim, iki farklki mysqlde farkli sonuc uretebiliyor. bu nedenle, gercekten kullanilmamali..

	$filecontent="
	auth required pam_mysql.so user=ehcp passwd=".$params['ehcppass']." host=localhost db=ehcp table=ftpaccounts usercolumn=ftpusername passwdcolumn=password crypt=2
	account required pam_mysql.so user=ehcp passwd=".$params['ehcppass']." host=localhost db=ehcp table=ftpaccounts usercolumn=ftpusername passwdcolumn=password crypt=2
	";

	writeoutput("/etc/pam.d/vsftpd",$filecontent,"w");
	
// For PHP chmod fix
// Changed umask from 022 to 0002
// Added file_open_mode=0775
	$filecontent="
listen=YES
anonymous_enable=NO
local_enable=YES
write_enable=YES
local_umask=0002
file_open_mode=0775
dirmessage_enable=YES
xferlog_enable=YES
connect_from_port_20=YES
nopriv_user=vsftpd
chroot_local_user=YES
secure_chroot_dir=/var/run/vsftpd
pam_service_name=vsftpd
rsa_cert_file=/etc/ssl/certs/vsftpd.pem
guest_enable=YES
guest_username=".$app->ftpuser."
local_root=".$app->conf['vhosts']."/\$USER
user_sub_token=\$USER
virtual_use_local_privs=YES
user_config_dir=/etc/vsftpd_user_conf
local_max_rate=2000000 # bytes per sec, 2Mbytes per sec
max_clients=50 # to avoid DOS attack, if you have a huge server, increase this..
ftpd_banner=Welcome to vsFTPd Server, managed by EHCP (Easy Hosting Control Panel, www.ehcp.net )
";
# allow_writeable_chroot=YES : bunun uzerinde calisacagim... boyle olmuyor... bakalim... bu olabilir: http://ehcp.net/?q=comment/2905#comment-2905
	writeoutput("/etc/vsftpd.conf",$filecontent,"w");
	passthru2("usermod -g $app->ftpgroup $app->ftpuser");
	passthru("/etc/init.d/vsftpd restart");


}


function install_vsftpd_server(){
	global $app,$ehcpinstalldir,$ip,$user_email,$user_name,$ehcpmysqlpass,$rootpass,$newrootpass,$ehcpadminpass;
	passthru("apt-get remove proftpd");
	aptget(array('vsftpd'));
	passthru("useradd --home ".$app->conf['vhosts']." --gid ".$app->ftpgroup." -m --shell /bin/false ".$app->ftpuser);
	passthru("cp /etc/vsftpd.conf /etc/vsftpd.conf_orig");

	vsftpd_configuration(array('ehcppass'=>$ehcpmysqlpass));
}

function buildconfigphp(){
# to be filled later..

}

function installsql() {
	global $app,$ehcpinstalldir,$ip,$lang,$user_email,$user_name,$ehcpmysqlpass,$rootpass,$newrootpass,$ehcpadminpass;
	bosluk2();

	if($newrootpass<>'') $tmprootpass=$newrootpass;
	else $tmprootpass=$rootpass;


	echo "extracting and importing sql to mysql:\n";

	# check if ehcp db already exists...
	$baglanti=@mysql_connect("localhost", "root", $tmprootpass);
	$ret=mysql_select_db("ehcp",$baglanti);

	if($ret===true){
		echo "seems to found old ehcp db..(will try to backup existing ehcp db with timestamp... )";
		echo "\nATTENTION ! EHCP DB WILL BE DROPPED IF EXISTS, EXIT NOW (by ctrl-C) if you don't want !\n\n";
		sleep(10);

		# backup any existing ehcp db, if any
		#$ehcpdb="ehcpbackup".date("YmdHis"); # disabled because gives error on some systems: Fatal error: date(): Timezone database is corrupt - this should *never* happen! in ...
		$ehcpdb="ehcpbackup".exec('date +%Y%m%d%H%M%S');
		passthru("cp -Rf /var/lib/mysql/ehcp /var/lib/mysql/$ehcpdb");
		passthru("chown -Rf mysql:mysql /var/lib/mysql/$ehcpdb");
		#--end backup ehcp db
	}
	# end check..


	# echo 'var_dump($ehcpmysqlpass,$rootpass,$newrootpass,$ehcpadminpass);\n';
	# var_dump($ehcpmysqlpass,$rootpass,$newrootpass,$ehcpadminpass);
	# burasi replace ile olacagina, writeoutput ile yapilabilir:

	replacelineinfile('$dbrootpass=',"\$dbrootpass='$tmprootpass';",$ehcpinstalldir."/config.php");
	replacelineinfile('$dbpass=',"\$dbpass='$ehcpmysqlpass';",$ehcpinstalldir."/config.php");
	if($lang<>'en') replacelineinfile('$defaultlanguage=',"\$defaultlanguage='$lang';",$ehcpinstalldir."/config.php");


	$filecontent="
	drop database if exists ehcp;
	create database ehcp;
	grant all privileges on ehcp.* to ehcp@'localhost' identified by '$ehcpmysqlpass' with grant option;
	grant all privileges on ehcp.* to ehcp@'127.0.0.1' identified by '$ehcpmysqlpass' with grant option;
	grant all privileges on ehcp.* to ehcp@'127.0.1.1' identified by '$ehcpmysqlpass' with grant option;
	";

	if($newrootpass<>''){ # if we need to change root pass... in versions prior to 0.29, mysql root pass could be changed from within ehcp install.
		$filecontent.="SET PASSWORD FOR 'root'@'localhost'=PASSWORD('$newrootpass');";
	}

	writeoutput($ehcpinstalldir."/ehcp1.sql",$filecontent,"w");

	echo "executing: mysql -u root --password=$rootpass < $ehcpinstalldir/ehcp1.sql \n ";
	passthru("mysql -u root --password=$rootpass < $ehcpinstalldir/ehcp1.sql"); # root pass changes here... if different , disabled
	echo "importing ehcp sql: \n";
	passthru("mysql -D ehcp -u root --password=$tmprootpass < $ehcpinstalldir/ehcp.sql");
	# passthru("mysql -u root --password=$tmprootpass < $ehcpinstalldir/ehcp_html.sql"); # bu niye vardı tam hatırlamıyorum. heralde html iceren sql burdaydı... 
	passthru("cp $ehcpinstalldir/config.php ./config.php");
	passthru("rm $ehcpinstalldir/ehcp1.sql"); # removed for security, root pass was there..

	$app = new Application();
	$app->connecttodb();
	$app->set_ehcp_dir($ehcpinstalldir);
	$app->setConfigValue("ehcpdir",$ehcpinstalldir);
	$app->setConfigValue("dnsip",$ip); // this configures dns ip to be used by ehcp, may be changed if using another dns server
	$app->setConfigValue('adminname',$user_name);
	$app->setConfigValue('adminemail',$user_email); // set email to send info about ehcp install to installer(admin)...
	$app->setConfigValue('disableeditapachetemplate','Yes'); // Prevent apache2 template modifications by non-admin users...
	$app->setConfigValue('disableeditdnstemplate','Yes'); // Prevent dns template modifications by non-admin users...
	$app->executequery("UPDATE panelusers SET password=MD5('$ehcpadminpass'),email='$user_email' WHERE panelusername='admin'");
	$app->commandline=true;


}

function checkmysqlpass($user,$pass){
	echo "mysql root pass being checked ...\n";
	$baglanti=@mysql_connect("localhost", $user, $pass);
	if(!$baglanti){
		return false;
	} else return true;
}

function getGoodPassword(){
	# The sign '#' has special meaning in Linux, and some passwords are written to some files on Ubuntu, which is broken, when you use a # , so avoid using # , this function checks this...
	# i put this control, because /etc/pam.d/vsftpd is broken if # is used in ehcp pass.
	$found=true;
	while($found!==false){
		$pass=getInput("\nPlease pay attention that, you cannot use sign # in your password:");
		$found=strpos($pass,'#');
	}
	return $pass;
}

function getVerifiedInput($inputname,$defaultvalue){
	# ask an input two times, to reduce possibility of error for user input
	global $unattended;
	if($unattended and $defaultvalue<>'') return $defaultvalue;
	
	$input1='';
	$input2='-';

	while($input1<>$input2){
		$input1=getInput("Enter $inputname (default $defaultvalue):");
		
		if($input1==''){
			echo "$inputname set as ($defaultvalue)  (default) \n";
			$input1=$input2=$defaultvalue;
		} else {
			$input2=getInput("Enter $inputname AGAIN:");
			if($input1<>$input2) echo "\n\nTwo inputs are NOT THE SAME ! , Please try again \n";
		}
	}

	return $input1;
}


function getinputs(){
	global $ehcpinstalldir,$app,$ip,$hostname,$lang,$user_email,$user_name,$yesno,$ehcpmysqlpass,$rootpass,$newrootpass,$ehcpadminpass,$installextrasoftware,$unattended,$installmode;
	# all inputs should be here...

	echo "\n\n==========================================================================\n\n";
	echo "EHCP INSTALL - INPUTS/SETTINGS SECTION:\n
	THIS SECTION IS VERY IMPORTANT FOR YOUR EHCP SECURITY AND PASSWORD SETTINGS.
	PLEASE ANSWER ALL QUESTIONS CAREFULLY  \n\n";

	$user_name=getInput("Please enter your name:",'myname');
	$user_email=getInput("Please enter your/admin email (used to send your panel info, ehcp news)- Enter an already working email:",'myemail@test.com');

	#$command="wget -q -O /dev/null --timeout=15 \"http://www.ehcp.net/diger/ehcpemailregister.php?user_email=$user_email\"";
	$url="http://www.ehcp.net/diger/ehcpemailregister.php?user_email=$user_email";
	#passthru($command);
	file_get_contents($url);

	$emptypass=checkmysqlpass('root','');

	// Restart MySQL service in case of missing socket bug
	restartMySQL();
	
	if(!$unattended){
		if($emptypass){
			echo "\nYour mysql root pass is identified as empty. ";
			$rootpass='';
		} else {
			echo "\n\n=======> MYSQL PASSWORD SETTINGS IS VERY IMPORTANT - YOUR EHCP MAY NOT FUNCTION IF YOU MISS SOMETHING HERE.. ehcp related information will be stored in your local mysql server ==========\n\n";
			$passtrue=false;
			while(!$passtrue){				
				$rootpass=getInput("\nEnter your current MYSQL ROOT PASSWORD:"); # mysql root otomatik verebilirsem, burayi da 1234 default yapmaliyim.
				$passtrue=checkmysqlpass('root',$rootpass);
				if(!$passtrue) echo "\n Your mysql root password is not correct ! \nIt is impossible and useless to continue without it. You need to learn it and retry here. Look at www.ehcp.net forums section (or here: http://www.ehcp.net/?q=node/245)\nYou may also try to run ./resetmysqlrootpass.sh program in this dir to reset pass, to use, Ctrl-C this and run ./resetmysqlrootpass.sh";
			}
		}
	}else{
		$rootpass="1234";
	}

	echo "Enter NEW PASSWORD for mysql user of `ehcp` (default 1234):";
	if(!$unattended) $ehcpmysqlpass=getGoodPassword();

	if($ehcpmysqlpass==''){
		echo "ehcp mysql pass set as 1234  (default) \n";
		$ehcpmysqlpass='1234';
	}

	$ehcpadminpass=getVerifiedInput("ehcp panel admin NEW PASSWORD","1234");

	echo "\n\n============== MYSQL PASSWORD SETTINGS COMPLETE ... see troubleshoot if your ehcp does not work ============== \n\n";

	# user input for ehcp installdir , skipped for now..
	#echo "\n enter web path to install ehcp or leave blank as default (such as ".$this->ehcpdir.", You should not change this usually) :";
	#$ehcpinstalldir=getInput();
	#if($ehcpinstalldir=="")

	$ehcpinstalldir="/var/www/new/ehcp";
	# If ehcpinstalldir changed, following files needs to be updated: etc/apache*/default, ehcp (flat file in this dir), ehcpdaemon.sh, ehcpdaemon2.sh, maybe others.. will see

	#echo "(installpath is $ehcpinstalldir , vhosts:".$app->conf['vhosts']." )";
	# write ehcp main config dir/file
	passthru2('mkdir -p /etc/ehcp/');
	writeoutput('/etc/ehcp/ehcp.conf',"ehcpdir=$ehcpinstalldir","w");

	$hostname=exec("hostname");

	cizgi();
	$newhostname=getInput("Your hostname seems to be $hostname, if it is different, enter it now, leave blank if correct \n",$hostname);

	if ($newhostname <> "") {
		$hostname=$newhostname;
	}
	echo "Hostname is set as $hostname \n";

	//mynetworks e ip eklenecek.
	$ip=getlocalip2();
	if($ip=='') {
		$prompt="Your ip cannot be determined automatically, You need to enter your (server) ip manually: ";
	} else {
		$prompt="Your ip seems to be $ip, if it is different or you want to use a different (external) ip, enter it now, leave blank if correct \n";
	}

	$newip=getInput($prompt,$ip);

	if ($newip <> "") {
		$ip=$newip;
	}

	echo "ip is set as ($ip) in ehcp, (Your server's actual ip is not changed)\n";


	echo "\nLANGUAGE SELECTION: \n\nehcp currently supports English,Turkish,German,Spanish,French (some of these partial) except installation \n";
	$lang=getInput("enter language file you want to use (en/tr/german/spanish/nl/fr/lv	 [default en]):",'en');
	
	if(strtolower($lang)=='en') $lang='en';
	if(strtolower($lang)=='tr') $lang='tr';

	$installextrasoftware=getInput("\nDo you want to install some additional programs which are not essential but useful for a hosting environment, such as ffmpeg,... etc.. ? Answer no if you have small ram or you need a light/fast system (y/[n])\n",'n');
	if(strtolower($installextrasoftware)=='y') $installmode='extra';
	

	echo "\n\n INPUTS/SETTINGS/CONFIGURATION SECTION FINISHED - REST IS EHCP INSTALLATION...should be straightforward, have a cup of tea and watch.. :) \nPlease be patient... ";
	echo "\n\n=====================================================================================================================================\n\n";
	#$command="wget -q -O /dev/null --timeout=15 \"http://www.ehcp.net/diger/ehcpemailregister.php?user_email=$user_email&user_name=$user_name&ip=$ip\"";
	$url="http://www.ehcp.net/diger/ehcpemailregister.php?user_email=$user_email&user_name=$user_name&ip=$ip";
	#passthru($command);# this is sent for adding you to our emaillist and for statistical purposes...
	file_get_contents($url);

	# bekle();

}

function initialize() {
	global $ehcpinstalldir,$app,$ip,$user_email,$user_name,$yesno,$ehcpmysqlpass,$rootpass,$newrootpass,$ehcpadminpass,$installmode;


infomailusingwget("3_ehcpinstall.php-started");
passthru("/etc/init.d/apparmor stop");
passthru("mkdir -p /etc/ehcp");
#passthru("apt-get update"); # already updated in install.sh
passthru("cp /etc/apt/sources.list /etc/apt/sources.list.bck.ehcp");
editlineinfile(array('#deb','##deb','# deb'),array('deb','deb','deb'),'/etc/apt/sources.list');
# passthru("apt-get update");

	$ip=getlocalip2();
	$hostname=exec('hostname');
	if($hostname=='' or $hostname=='(none)'){ # can hostname be empty ?
		echo "hostname seems empty, setting hostname as myserver";
		passthru('hostname myserver');
	}

	bosluk();
	ehcpheader();
	bosluk();
	echo "Starting EHCP Force installation.  Please read prompts and questions carefully!
Some install/usage info and your name/email is sent to ehcp developpers for statistical purposes and for improvements.";
	bosluk();
	bekle();

	$app = new Application("localhost","ehcp",$ehcpmysqlpass,"ehcp");
	$app->commandline=true;


	installmysql();
	sleep(2);
	getinputs();


	//echo "Can we list you in the list of persons who installed ehcp ? (y/n) default y:";
	//$yesno=getInput();
	#if($yesno=='') $yesno='NA';
	echo "continuing...\n..";
	infomailusingwget("3_2_name:$user_name,mail:$user_email");

	# *** burada eksiklik var.. kodu ehcp passini sifirdan isteyecek sekilde yazacaz... burada kodda hicbir sifre birakmamam lazim... bu bir derece yapildi..


	//echo "named base is: ".$app->."\n";
	executeprog2("mkdir -p ".$app->conf['namedbase']);
	checkaptget();

	switch($installmode) {
		case 'extra': 
		case 'normal': aptget(array('unrar','rar','unzip','zip','mc','lynx','nmap'));
		case 'light': aptget(array('openssh-server','python-mysqldb','python-cherrypy3','apache2','bind9','php5-gd','libapache-mod-ssl'));
			break;
		default: echo "Unknown installmode parameter at ".__LINE__;
	}
	
	// bunlari ayri ayri yuklemek gerekiyor. yoksa apt-get hicbirini yuklemiyor..  bu nedenle ayri fonksiyon yazdim... ah bide su turkce problemi olmasaydi..
	
	addifnotexists("extension=mysql.so","/etc/php5/apache2/php.ini");
	addifnotexists("extension=mysql.so","/etc/php5/cli/php.ini");
	addifnotexists("include \"".$app->conf['namedbase']."/named_ehcp.conf\";","/etc/bind/named.conf");
	replacelineinfile("listen-on {", "listen-on { any; };", "/etc/bind/named.conf.options"); # if listen-on { 127.0.0.1; }; then, dns cannot be seen from outside..

        echo "* installpath set as: $ehcpinstalldir \n";
	executeprog2("mkdir -p $ehcpinstalldir");
	executeprog2("mkdir -p $ehcpinstalldir/../../named/");

}

function passvariablestoinstall2(){
	global $app,$ehcpinstalldir,$ip,$hostname,$lang,$user_email,$user_name,$yesno,$ehcpmysqlpass,$rootpass,$newrootpass,$ehcpadminpass,$installextrasoftware;
	// install1 and install2 is separated because install2 uses php mail function, which we need to re-run php interpreter after first install

	$file="<?php
	\$ehcpinstalldir='$ehcpinstalldir';
	\$ip='$ip';
	\$user_name='$user_name';
	\$user_email='$user_email';
	\$ehcpmysqlpass='$ehcpmysqlpass';
	\$rootpass='$rootpass';
	\$newrootpass='$newrootpass';
	\$ehcpadminpass='$ehcpadminpass';
	\$hostname='$hostname';
	\$lang='$lang';
	\$installextrasoftware='$installextrasoftware';


	?>";
	writeoutput('install2.1.php',$file,"w");// dynamically setup install2.1.php, to pass some variables to install2.php

}

function launchpanel(){
	echo "now, you should be able to navigate to your web located at, $ehcpinstalldir \n" ;
	echo "panel admin username: admin \n";
	cizgi();
	echo "now will try to launch your control panel, if it is on local computer.. \n";

	echo "\nwill use firefox as browser...\n\n";
	#bekle();
	$browser=trim(exec("which firefox "));
	if($browser) passthru("firefox http://localhost/vhosts/ehcp &");
}

function net2ftp_configuration($params){
	# update net2ftp mysql conf...
	 echo "(".__FUNCTION__.")\n";
	replacelineinfile("\$net2ftp_settings[\"dbpassword\"]","\$net2ftp_settings[\"dbpassword\"]='".$params['ehcppass']."';",$params['ehcpinstalldir']."/net2ftp/settings.inc.php");
	passthru2("chmod 777 ".$params['ehcpinstalldir']."/net2ftp/temp/");
}

function install_with_mode($params){ # complete later.
	global $installmode;
	if($installmode=='') $installmode='light';
	
	switch($installmode) {
		case 'extra':; 
		case 'normal':;
		case 'light':;
			break;
		default: echo "Unknown installmode parameter at ".__LINE__;
	}
}

function installfinish() {
	global $ehcpinstalldir,$ehcpmysqlpass,$app,$user_email,$user_name,$header,$installextrasoftware,$lightinstall,$installmode;
	
	switch($installmode) {
		case 'extra': aptget(array('postgrey','ffmpeg','php5-ffmpeg','mplayer','mencoder','nmap','listadmin','spamassassin','aptitude','gpac','clamav','clamav-freshclam','libavcodec-unstripped'));
		case 'normal': aptget(array('webalizer','php-pear','phpsysinfo','mailutils','byobu'));
		case 'light': aptget(array('bind9-host','php5-curl','php5-xmlrpc','php5-imap')); 
			break;
		default: echo "Unknown installmode parameter at ".__LINE__;
	}
	
	# gpac : includes mp4box and similar multimedia required by some media websites (clipbucket)
	# ekle();
	echo "finishing install...\n";

	# to make phpsysinfo work as in /var/www
	chdir('/var/www/new');
	passthru2('ln -s /usr/share/phpsysinfo phpsysinfo');

	chdir($ehcpinstalldir);
	# app already setupd in installsql function..



	$app->loadConfig();// loads dns ip and other thing
	$app->adjust_webmail_dirs();
	writeoutput($app->conf['namedbase']."/named_ehcp.conf","","w");

	# $app->addDaemonOp("syncdns",'','','','sync dns'); # no need to sync, since ehvp ver  0.29.15, because before it, 0 # of domains caused ehcp to crash., now not.
	# echo "syncdns finished\n";
	# $app->syncdomains($app->ehcpdir."/apachehcp.conf");

	passthru("/etc/init.d/sendmail stop");

	# phpmyadmin normalde kurmasina ragmen, bidefasinda, kurmus, ama configurasyon dosyasini atamamis. bu nedenle ekledim bunu..
	if(!file_exists("/etc/apache2/conf.d/phpmyadmin.conf")) {
		passthru2("mkdir -p /etc/apache2/conf.d");
		passthru2("cp ./phpmyadmin.conf /etc/apache2/conf.d/");
	}

	net2ftp_configuration(array('ehcppass'=>$ehcpmysqlpass,'ehcpinstalldir'=>$ehcpinstalldir));
	/*
	$filecontent="This is default ehcp-apache index file. <br><a href=/vhosts/ehcp>click here for ehcp home on your server</a><br><br>
	<a target=_blank href=http://www.ehcp.net>ehcp Home</a>";
	writeoutput("/var/www/apache2-default/index.html",$filecontent);
	writeoutput("/var/www/index.html",$filecontent);*/

	echo "\nPlease wait while services restarting...\n\n";
	passthru("/etc/init.d/apache2 restart");
	passthru("/etc/init.d/bind9 restart");
	passthru("/etc/init.d/postfix restart");
	passthru("cp /etc/apt/sources.list.bck.ehcp /etc/apt/sources.list");
	replacelineinfile("exit 0", "/etc/init.d/ehcp restart", "/etc/rc.local");
	$add="/var/log/ehcp.log /var/log/apache_common_access_log {
}";
	add_if_not_exists2($add,'/etc/logrotate.d/ehcp',True);	# adjust logrotate for ehcp logs # we need to do this for domain logs too

	sleep(2);
	passthru2("/etc/init.d/mysql restart ");
	sleep(1);
	passthru2("/etc/init.d/mysql start ");
	passthru2("update-rc.d -f nginx remove");
	passthru2("update-rc.d apache2 defaults");
	passthru("/etc/init.d/apparmor stop");


	// passthru("cd /var/www/ehcp");
	echo "now, starting panel daemon \n";
	// passthru("nohup php index.php daemon > /dev/null & ");

	#launchpanel()
	
	bosluk();
	cizgi();
	#echo "finished installation , bye ! \n"; # not finished yet, something is done in install.sh
	bosluk();
	$msg="
Congratulations !
Your ehcp (Easy Hosting Control Panel) installation completed.
now, navigate to your panel located at http://yourip, whatever is your ip.

if you need assistance, you may click troubleshoot in front page, have a look at forum section at www.ehcp.net,
or you may contact ehcp developer directly ad email/msn: info@ehcp.net

Thank you for choosing and trying ehcp !


";
	if($user_email) @mail($user_email,"your ehcp install completed.",$msg,$header);
}

function restartMySQL(){
	echo "Restarting MySQL service for best results \n please wait...";
	passthru("service mysql restart");
}

function installfiles() {
	global $ehcpinstalldir,$app;
	echo "pwd is: ".getcwd().", dest dir is: $ehcpinstalldir, will just copy ehcp files \n";
	passthru("rm -Rf $ehcpinstalldir");
	passthru("mkdir $ehcpinstalldir");
	passthru("cp -Rf * $ehcpinstalldir");
	passthru("chmod a+r -R $ehcpinstalldir");

	passthru("mkdir -p /var/www/new/vhosts/ehcp"); # redirect old style url to new one
	passthru("cp $ehcpinstalldir/misc/redirect_index.html -p /var/www/new/vhosts/ehcp/index.html");

	bosluk2();
	echo "Copying of files completed... dest dir: $ehcpinstalldir \n please wait...";
	bosluk2();
}


function scandb(){
	global $app,$ehcpmysqlpass;

	$mysqlkullaniciadi="ehcp";
	$mysqlsifre=$ehcpmysqlpass;
	$dbadi="ehcp";

	$baglanti=mysql_connect("localhost", $mysqlkullaniciadi, $mysqlsifre);
	if(!$baglanti){
		echo mysql_error();echo "cannot connect to db: username: $mysqlkullaniciadi, pass: $mysqlsifre \n"; return;
	};


	exec("mkdir ".$app->conf['vhosts']);
	echo "checking and rebuilding domains already in database:\n
	attn!: panelusername and domain main ftpusername must be same to function correctly, while rebuilding domains from db !!....\n ";

	$query="select panelusername,domainname from domains";
		$result = mysql_db_query("$dbadi", $query);
		if ($result) {
			while ($r = mysql_fetch_assoc($result)) {
				echo "domain: ".$r['domainname']." \n";
				$base1=$app->conf['vhosts'].'/'.securefilename($r['panelusername']);
				$base=$base1."/".trim($r['domainname']);
				exec("mkdir -p $base");
				exec("mkdir -p $base/logs");
				exec("mkdir -p $base/httpdocs");
				addifnotexists("Under Construction","$base/httpdocs/index.php");
				addifnotexists("","$base/logs/error_log"); // setup file if not exists
				exec("chmod a+r $base1 -Rf");
				exec("chown -Rf vsftpd $base1 ");

			}
		} else {
			echo "error occured:".mysql_error();
		}
	passthru("/etc/init.d/apache2 reload");
	echo "finished db ops\n";
	cizgi();
}

function installmysql(){
	installMySQLServ();
	sleep(2);
	aptget(array('mysql-client'));
	sleep(2);
	replacelineinfile("old_passwords","old_passwords=0","/etc/mysql/my.cnf"); # disable mysql old passwords... if enabled, vsftp auth cant work sometime.. changed 26.2.2008
	passthru("/etc/init.d/mysql restart");

}

function infomail($str,$msg=''){
	global $user_name,$user_email,$header,$ehcpversion;
	$ip=getlocalip2();
	if($msg=='') $msg=$str."\nName: $user_name \nEmail:$user_email";
	mail('bvidinli@gmail.com',"ehcp-install-$ehcpversion-$ip-".$str,$msg,$header);
}

function infomailusingwget($str){
	global $user_name,$user_email,$localip,$ehcpversion;
	echo "please wait...\n";
	$ip=getlocalip2();
	$str=str_replace(" ","_",$str); # because in url, space breaks variable msg in url
	#$command="wget -q -O /dev/null --timeout=15 \"http://www.ehcp.net/diger/msg.php?msg=$ehcpversion-ip:$ip,$str&name=$user_name&email=$user_email&ip=$ip\"";
	$url="http://www.ehcp.net/diger/msg.php?msg=$ehcpversion-ip:$ip,$str&name=$user_name&email=$user_email&ip=$ip";
	//echo "command: $command \n";
	#passthru($command);
	file_get_contents($url);
}


/*
apache start etmiyor.
zira onceden bulunan ornek domainlerin dizinleri yok.
bunları kurulumda olusturmali, ya da daemon olusturmali
hem bu sayede veritabanindaki veriler otomatik olusturulmus olur.

onu yaptim.
busefer de apache ın default sayfası yokoldu..bu da tamam.
namevirtualhost falan ayarladım.

daemon ile sorunlar var.
install.php daemon calıstırıyor ama nerde calıstıgı belli degil.
syncdns dosya olusturamıyor.

enson verdigi hata:

aemon->runop success **
<br> Domain list exported <br>
Warning: fopen(named/dene.com): failed to open stream: No such file or directory in /home/bvidinli/ehcp/config/dbutil.php on line 956
hata: dosya acilamadi: named/dene.com (writeoutput) !
daemon->runop failure **** : update operations set try=try+1,status='failed' where id=3 limit 1

daemon u php dısına cıkardım. oldu gibi,
syslog da, pure-ftpd: (?@?) [ERROR] Unable to start a standalone server: [Invalid argument]
hatası veriyor, bilen varsa beri gelsin...
vsftpd ye gectim, pureftp debiana biturlu kurulmadi, proftp de kurulmadi,
vsftpd'de de quota falan yok, ama bu debianda calisti. simdilik bunda idare edecem..

*/

function remove_ehcp(){
	global $app;  // not executed yet, left only for future purposes and to give idea..
	passthru("apt-get -y remove postfix postfix-mysql postfix-doc mysql-client mysql-server courier-authdaemon courier-authlib-mysql courier-pop courier-pop-ssl courier-imap courier-imap-ssl libsasl2 libsasl2-modules libsasl2-modules-sql sasl2-bin libpam-mysql openssl phpmyadmin pure-ftpd-mysql apache2 bind9");
	passthru("apt-get clean");
	passthru("rm -rvf ".$app->conf['vhosts']."/ehcp");

}



?>
