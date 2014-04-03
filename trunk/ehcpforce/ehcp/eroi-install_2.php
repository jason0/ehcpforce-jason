<?php
error_reporting (E_ALL ^ E_NOTICE);
//  second part of install.
//  first part installs mailserver, then, install2 begins,
//  i separated these installs because php email function does not work if i re-start php after email install...
// install functions in install_lib.php

if($argc>1){

  # Distro and version are always sent

  # Get distro version number
  $temp = trim($argv[1]);
  if(stripos($temp, ".") != FALSE){
    $version = $temp;
  }

  # Distro is needed for Ubuntu only features
  $distro = strtolower(trim($argv[2]));
}

for($i=3;$i<=5;$i++){ # accept following arguments in any of position.
	if($argc>$i) {
		print "argc:$argc\n\n";
		switch($argv[$i]) {
			case 'noapt': # for only simulating install, apt-get installs are still loged onto a file
				$noapt="noapt";
				echo "apt-get install disabled due to parameter:noapt \n";
				break;
			case 'unattended': # tries to suppress most user dialogs.. good for a quick testing. (tr: hizlica test etmek icin guzel..)
				$unattended=True;
				break;
			case 'light': # the light install, non-cruical parts are omitted. good for a quick testing. (tr: hizlica test etmek icin guzel..)
				$installmode='light';
				break;
			case 'extra': # the extra install, means more components
				$installmode='extra';
				break;
			default:
				echo __FILE__." dosyasinda bilinmeyen arguman degeri:".$argv[$i];
				break;
		}

	}
}
echo "Some install parameters for file ".__FILE__.": noapt:($noapt), unattended:".($unattended===True?"exact True":"not True")." installmode:($installmode) \n";


include_once('eroi-install_lib.php');

// Load preset installation values in install_silently.php if exists:
if(file_exists("install_silently.php")){
	include 'install_silently.php';
}

include_once('eroi-install2.1.php');

echo "System is running $version\n";

echo "\nincluded install2.1.php\nhere are variables transfered:\n";
echo
"
webdizin:$webdizin
ip:$ip
user_name:$user_name
user_email:$user_email
hostname:$hostname
installextrasoftware: $installextrasoftware
";

/*
ehcpmysqlpass:$ehcpmysqlpass
rootpass:$rootpass
newrootpass:$newrootpass
ehcpadminpass:$ehcpadminpass
*/

installsql();
fail2ban_install();

install_vsftpd_server();
#infomail('_5_vsftpd install finished');

if(isset($version) && $version != "12.10"){
  install_nginx_webserver();
}else{
  echo "Ubuntu 12.10 is not compatible with nginx due to bugs. Installing apache2 only. Upgrade to Ubuntu 13.04 for this functionality.\n";
}
installapacheserver();

# Secure apache by installing some basic Anti-DDoS Modules
if(!isset($installmode) || $installmode != 'light'){
	apache_mod_secure_install();
}

# scandb();  no more need to scan db since ver. 0.29.15
installfinish();



$message='';
exec('ifconfig',$msg);
exec('ps aux ',$msg);
foreach($msg as $m) $message.=$m."\n";
#infomail("_6_install finished.mail from inside php.user:$user_name,$user_email,$yesno",$message);
$msg="
your ehcp install finished. if you have questions or need help, please visit www.ehcp.net, ehcp forums or email me to info@ehcp.net

ehcp kurulumunuz tamamlandı. tebrikler. eğer sorularınız varsa ya da yardıma ihtiyacınız varsa, ehcp.net deki forum kısmına soru yazın veya info@ehcp.net adresine eposta gönderin.
https://launchpad.net/ehcp  bu adresi de kullanabilirsiniz.

ehcp developer..
";

if($user_email<>'') mail($user_email,'your ehcp install finished..have fun',$msg,'From: info@ehcp.net');

$realip=getlocalip2();
if(!$app->isPrivateIp($ip)) $realip.="-realip"; # change subject if this is a server with real ip...
$ip2=trim(file_get_contents("http://ehcp.net/diger/myip.php"));
$message.="\noutside Ip detected:$ip2";

infomail("_7_install finished.$realip.$ip2.mail from inside php.user:$user_name,$user_email,$yesno",$message);
#infomailusingwget("7_ehcp_install2.php.finished");

?>
