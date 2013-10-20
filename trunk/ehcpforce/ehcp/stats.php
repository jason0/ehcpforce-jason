<?php
include_once("config/dbutil.php");
include_once("config/adodb/adodb.inc.php");
include_once("classapp.php");

    $app = new Application();
    $app->requirePassword=false;

    $app->initialize();
    echo $app->smallserverstats2();
    
?>
