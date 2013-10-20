<?php
// burasi dzsoft php editor ile kullanmak icin yazildi. editor icinde run-options da httpuseragent icine testediliyor yaziliyor. sonra calisinca ,
// windowsda d:\ yi gösteriyor, ama linux makineye yükleyince ayný kod /var www yi gosteriyor.
$ua=getenv("HTTP_USER_AGENT");
$path=getenv("PATH");
// echo "path: $path<br>";
if(strstr($path,"WINNT")or strstr($ua,'testediliyor')or strstr($path,'WINDOWS')) $ortam="test";
//if(strstr($ua,'testediliyor')) $ortam="test";
else $ortam="gercek";


$confdir=dirname(__FILE__)."/";
$root=$confdir."../";
//echo "user agent:$ua";
//echo "root:$root";


//if($ortam=="test")$confdir="C:/Program Files/EasyPHP1-7/www/vidinli/config/";
//else $confdir="/var/www/html/vidinli.com/config/"; //ayni degisken dbutil icinde de var. tekrar var.
// eskiden yukardakiler kullaniliyordu. ama asagidakikodu yazdim. eger asagidakikod cuvallarsa ozaman yukardakini tekrar kullan.


ini_set("display_errors","1");
include_once($confdir."dbconf.php");
include_once($confdir."dbutil.php");

$ip = getenv ("REMOTE_ADDR");

$baglanti=mysql_connect("localhost", "$mysqlkullaniciadi", "$mysqlsifre");
if(!$baglanti){echo mysql_error();die ("<br>(dbbaglan)Baglanilamadi-confdir: $confdir, username: $mysqlkullaniciadi,  cagirandosya:".$_SERVER['PHP_SELF']);};



?>
