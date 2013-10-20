<?
# ehcp.net: example, add an email user to system directly. 
# Easy Hosting Control Panel (ehcp)

require("ehcpfiles/classapp.php");
$mailusername = "info";
$domainname="mydomain.com"
$panelusername = "admin";
$password="123";
$quota="10";

$app = new Application();
$app->connectTodb(); # fill config.php with db user/pass for things to work..
$app->activeuser=$panelusername;

$ret=$this->addEmailDirect($mailusername,$domainname,$password,$quota,$autoreplysubject,$autoreplymessage);

if($ret=="true"){
    print "Success";
} else {
    print $app->output;
} 
echo $ret;


?>
