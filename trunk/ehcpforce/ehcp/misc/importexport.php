<?php
/*
EASY HOSTING CONTROL PANEL MAIN index.php FILE Version 0.27 - www.EHCP.net

IF YOU SEE THIS ON BROWSER,  IMMADIATELY STOP WEBSERVER with /etc/init.d/apache2 stop, otherwise, your passwords may be seen by others... 

IF YOU SEE THIS INSTEAD OF A WEB PAGE, THEN YOU PROBABLY DIDN'T INSTALL PHP EXTENSION, PLEASE RE-RUN EHCP INSTALL SCRIPT OR MANUALLY INSTALL APACHE2-PHP EXTENSION..

* 
by I.Bahattin Vidinli, 
mail&msn: info@ehcp.net

see classapp.php for real application.
*/
session_start();
include_once("../config/dbutil.php"); # this should be removed later... 
include_once("../config/adodb/adodb.inc.php"); # adodb database abstraction layer.. hope database abstracted...
include_once("../classapp.php"); # real application class


degiskenal(array("op"));

function fputs2($dosya,$str){
	global $dosyakaydet,$dosyacikti;
	if($dosyakaydet) fputs($dosya,$str);
	else $dosyacikti.=$str;
}

function exportDomain($domain) {
	global $app,$dosyakaydet;
	$dosyakaydet=false;
	
	
	$fname=time().".txt";
	$domain=strtolower($domain);

	$s1=$app->query2("SELECT * FROM domains WHERE domainname LIKE '$domain'");
	if(is_array($s1)) {

		$panelusername=$s1['panelusername'];
		$reseller="admin";

		$t=array('domains','directories','emailusers','forwardings','ftpaccounts','misc','mysqldb','mysqlusers','panelusers','subdomains');
		$f[$t[0]]=array('reseller','panelusername','domainname','homedir','comment','status','serverip');
		$f[$t[1]]=array('panelusername','domainname','username','password','directory','expire','comment');
		$f[$t[2]]=array('reseller','panelusername','domainname','mailusername','beforeat','password','email','quota');
		$f[$t[3]]=array('reseller','panelusername','domainname','source','destination');
		$f[$t[4]]=array('ftpusername','password','domainname','panelusername','reseller','status','type');
		$f[$t[5]]=array('panelusername','name','value','longvalue'); //misc
		$f[$t[6]]=array('domainname','host','dbname','aciklama','reseller','panelusername'); //mysqldb
		$f[$t[7]]=array('domainname','host','dbname','dbusername','password','reseller','panelusername');  //mysqlusers
		$f[$t[8]]=array('reseller','domainname','panelusername','password','email','quota','maxdomains','maxemails','maxpanelusers','maxftpusers','maxdbs','status','name'); //panelusers
		$f[$t[9]]=array('reseller','panelusername','subdomain','domainname','homedir','comment','status'); //subdomains


	if($dosyakaydet) $dosya=fopen($fname,'w'); 
	fputs2($dosya,"domain=$domain\n");	
	fputs2($dosya,"panelusername=$panelusername\n");
		
		foreach ($t AS $d) {
			if($d!=="misc") {
				$r=$app->query("SELECT * FROM $d WHERE domainname LIKE '$domain' OR panelusername LIKE '$panelusername'");
			} else {
				$r=$app->query("SELECT * FROM $d WHERE panelusername LIKE '$panelusername'");

			}
			$g="";
			if(is_array($r)) {
				
				$g="";
				for($b=0;$b<count($r);$b++) {
					$g.=strtoupper($d)."|data#####";

					for($a=0;$a<count($f["$d"]);$a++) {
						$g.=$r[$b]["".$f["$d"][$a].""]."#####";
					}
					$g.="\n";
					

				}
			
			
			}
			fputs2($dosya,$g);
			
		}

	if($dosyakaydet) fclose($dosya);

	}

}# end func

function importDomain($dosya) {
	global $app,$dosyakaydet;
	$dosyakaydet=false;


	$user=$app->activeuser;

	if($dosyakaydet) {
		$baglan=@fopen ("$dosya",'r');
		if (!$baglan) {
			echo "Backup Bulunamadý / Açýlamadý...";
			exit();
		}
	}

	$t=array('domains','directories','emailusers','forwardings','ftpaccounts','misc','mysqldb','mysqlusers','panelusers','subdomains');
	$f[$t[0]]=array('reseller','panelusername','domainname','host','homedir','comment','status','serverip');
	$f[$t[1]]=array('panelusername','domainname','username','password','directory','expire','comment');
	$f[$t[2]]=array('reseller','panelusername','domainname','mailusername','beforeat','password','email','quota');
	$f[$t[3]]=array('reseller','panelusername','domainname','source','destination');
	$f[$t[4]]=array('ftpusername','password','domainname','panelusername','reseller','status','type');
	$f[$t[5]]=array('panelusername','name','value','longvalue'); //misc
	$f[$t[6]]=array('domainname','host','dbname','aciklama','reseller','panelusername'); //mysqldb
	$f[$t[7]]=array('domainname','host','dbname','dbusername','password','reseller','panelusername');  //mysqlusers
	$f[$t[8]]=array('reseller','domainname','panelusername','password','email','quota','maxdomains','maxemails','maxpanelusers','maxftpusers','maxdbs','status','name'); //panelusers
	$f[$t[9]]=array('reseller','panelusername','subdomain','domainname','homedir','comment','status'); //subdomains


	if($dosyakaydet) {
		while (!feof ($baglan) ) {
			$oku=fgets ($baglan,1024);
			$satirlar[]=$oku;
		}
	} else {
		#$app->output.= "<textarea cols=60 rows=20>$dosya</textarea><br>";
		$satirlar=explode("\n",$dosya);
	}


	$satir=0;
	foreach($satirlar as $oku) {
		$satir++;	
		if($satir==1) {
			list($d,$domain)=explode("=",$oku);		
		} elseif($satir==2) {
			list($d,$panelusername)=explode("=",$oku);
			
		} else {
			//asýl iþlemler
			if(strlen($oku)<2){ return;}
			
			list($h,$data)=explode("|",$oku);
			$h=strtolower($h);
			$iii="";
			$icerik=explode("#####",$data);
			$size=count($f["$h"]);
			
			$alanlar='';
			foreach($f[$h] as $alan){
				if($alanlar=='') $alanlar="($alan";
				else $alanlar.=",$alan";
			}
			$alanlar.=")";

			
			if(count($icerik)===$size+2) {		
				$iii="INSERT INTO $h $alanlar VALUES(";
				for($a=0;$a<$size;$a++) {
					$fiin=$a+1;
					$icerik['fiin']=str_replace("\r","",$icerik['fiin']);
					$icerik['fiin']=str_replace("\n","",$icerik['fiin']);
					$icerik['fiin']=str_replace("\n\r","",$icerik['fiin']);
					$icerik['fiin']=trim($icerik['fiin']);							
					switch($f["$h"][$a]) {
						case "id";
							$ek="NULL";
						break;
						case "reseller";
							$ek="'$user'";
						break;
						default:
							$ek="'".$icerik[$fiin]."'";						
						break;	
					}
					if((strstr($ek,"''")) OR (strstr($ek,"' '"))) {
						$ek="NULL";
					}

					$iii.="$ek,";				
				}
				
				$iii=substr($iii,0,strlen($iii)-1);			
				$iii.=");";				
				
				$app->output.="Executing: $iii <br>";
			}
		}
	}# foreach satirlar

	if($dosyakaydet)fclose($baglan);

}# end function


function import(){
	global $app,$domainname,$dosyacikti,$impexp,$importdata;
	$app->output.=__FUNCTION__." basliyor.. <br>";
	$importdata=$_POST['importdata'];
	#$app->getVariable(array("domainname",'impexp','importdata'));	

	if($importdata){
		#$app->output.= "<textarea cols=60 rows=20>$importdata</textarea><br><br>";
		importDomain($importdata);
	} else {
		$app->output.=inputform5(array(array('importdata','tip'=>'textarea')));
	}
}

function export(){
	global $app,$domainname,$dosyacikti,$impexp;
	$app->output.=__FUNCTION__." basliyor.. <br>";
	$app->getVariable(array("domainname",'impexp'));	

	$domainname=$app->chooseDomain(__FUNCTION__,$domainname);
	$app->requireMyDomain($domainname);
	
	$app->output.="Domain export ediliyor: $domainname";
	exportDomain($domainname);
	$app->output.="<pre>$dosyacikti</pre>";	

}


function importexport(){
	global $app,$domainname,$dosyacikti,$impexp;
	$app->output.=__FUNCTION__." basliyor.. <br>";
	$app->getVariable(array("domainname",'impexp'));	
	$domainname=$app->chooseDomain(__FUNCTION__,$domainname);	
	$app->requireMyDomain($domainname);
	
	if($impexp==''){
		$app->output.="<a href='?impexp=import'>import</a><br><a href='?impexp=export'>export</a><br>";
	} elseif($impexp=='import') {
		import();
	} elseif($impexp=='export') {
		export();
	} 
    
}

$app = new Application();
$app->cerceve="standartcerceve";
$app->usertable="domainusers";
$app->userfields=array("id","domainname","username","email","quota");
$app->op=$op;

if($op=='login' or $op=='logout') $app->run();
else {
	$app->initialize();
	importexport();
	$app->show();
}


?>
