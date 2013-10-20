<?
# ehcp.net: example, add domain to existing paneluser (who has ftp already)
# Easy Hosting Control Panel (ehcp)

require("ehcpfiles/classapp.php");
$domainname = "d2omainsite.com";
$panelusername = "resell";
$selfftp = "ftpusername2";

$app = new Application();
$app->connectTodb(); # fill config.php with db user/pass for things to work..
$app->activeuser=$panelusername;

$ret=$app->addDomainDirectToThisPaneluser($domainname,$selfftp);

if($ret){
    echo "Success";
} else {
    echo $app->output;
} 

echo "($ret)";

?>
