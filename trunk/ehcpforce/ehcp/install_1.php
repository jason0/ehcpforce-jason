<?php
error_reporting (E_ALL ^ E_NOTICE);
//  first part of install.
//  this installs mailserver, then, install2 begins, 
//  i separated these installs because php email function does not work if i re-start php after email install... 

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
			default:
				echo __FILE__." dosyasinda bilinmeyen arguman degeri:".$argv[$i];
				break;
		}

	}
}
echo "Some install parameters for file ".__FILE__.": noapt:($noapt), unattended:".($unattended===True?"exact True":"not True")." installmode:($installmode) \n";

include_once('install_lib.php');
initialize();
echo "\n------\ninstallpath set as: $ehcpinstalldir \n";
installfiles();
// Restart MySQL before beginning installation of software that uses it.
restartMySQL();
installmailserver(); 
#infomail('_4_ehcp_mailserver,installfiles complete');
passvariablestoinstall2();

?>
