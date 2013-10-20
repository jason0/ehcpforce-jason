#!/usr/bin/env php
<?php

include_once("config/dbutil.php"); 
include_once("config/adodb/adodb.inc.php"); # adodb database abstraction layer.. hope database abstracted...
include_once("classapp.php"); # real application class



$app = new Application();
$app->requirePassword=false;
$app->initialize();

$backupname='mybackup'.date('YmdHmi');
$whattobackup="files,mysql"; # you may also include ehcp's files here..

$app->daemonBackup('',$backupname,$whattobackup);

echo "hope finished... :)";
?>
