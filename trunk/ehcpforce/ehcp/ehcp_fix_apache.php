#!/usr/bin/env php
<?php

include_once("config/dbutil.php"); # app should be removed later... 
include_once("config/adodb/adodb.inc.php"); # adodb database abstraction layer.. hope database abstracted...
include_once("classapp.php"); # real application class


$app = new Application();
$app->cerceve="standartcerceve";
$app->connecttodb();


$app->addDaemonOp('fixApacheConfigNonSsl','','','','fixApacheConfigNonSsl');
$app->addDaemonOp('syncdomains','','','','sync domains');
$app->addDaemonOp('syncftp','','','','sync ftp for nonstandard homes');
$app->addDaemonOp('syncdns','','','','sync dns');
$app->addDaemonOp('syncapacheauth','','','','sync apache auth');
passthru2("/etc/init.d/ehcp restart");

$app->showoutput();

?>
