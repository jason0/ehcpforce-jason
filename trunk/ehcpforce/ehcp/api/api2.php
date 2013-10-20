<?
# ehcp.net: example, add ftp, paneluser and domain all in one
# Easy Hosting Control Panel (ehcp)

require("ehcpfiles/classapp.php");
$domainname = "d2omainsite.com";
$panelusername = "resell";
$ftppassword=$paneluserpass="123";
$ftpusername = "ftpusername2";
$status="active";

$app = new Application();
$app->connectTodb(); # fill config.php with db user/pass for things to work..
$app->activeuser=$panelusername;

$ret=$app->addDomainDirect($domainname,$panelusername,$paneluserpass,$ftpusername,$ftppassword,$status,$email='',$quota=0);

if($ret){
    echo "Success";
} else {
    echo $app->output;
} 

echo "($ret)";

?>
