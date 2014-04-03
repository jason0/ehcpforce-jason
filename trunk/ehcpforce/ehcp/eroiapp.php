<?php

include_once("classapp.php"); # real application class

/* this is jason's attempt at class inheritance.
 *
 *
 */
class EroiApp extends Application {




	function adjust_webmail_dirs(){
		# stub now

		/*
		 *

		# for squirrelmail, which is bundled in ehcp dir, webmail folder.
		$localconfig="<?php
		\$data_dir				 = '$this->ehcpdir/webmail/data/';
		\$attachment_dir		   = '$this->ehcpdir/webmail/data/';
		?>";

		$success=writeoutput2("$this->ehcpdir/webmail/config/config_local.php",$localconfig,'w',false);
		passthru("chmod a+w $this->ehcpdir/webmail/data/");

		$change_pass_config="<?
		\$dbhost='localhost';
		\$dbusername='ehcp';
		\$dbpass='$this->dbpass';
		\$dbname='ehcp';
		?>";

		$success=$success && writeoutput2("$this->ehcpdir/webmail/plugins/ehcp_change_pass/config.php",$change_pass_config,'w',false);

		return $success;
		*/
	}



	function editDnsTemplate(){  //stub from classapp
	}
	function customDnsSettings(){  //stub from classapp
	}
	function addCustomDns(){  //stub from classapp
	}
	function addDnsOnlyDomainWithPaneluser(){  //stub from classapp
	}
	function addSlaveDNS(){  //stub from classapp
	}
	function removeSlaveDNS(){  //stub from classapp
	}
	function addDnsOnlyDomain(){  //stub from classapp
	}
	function checkDynDns(){  //stub from classapp
	}
	function getDnsServer(){  //stub from classapp
	}
	function dnsZoneFiles($arr){  //stub from classapp
	}
	function dnsNamedConfFile($arr){  //stub from classapp
	}
	function syncDns(){  //stub from classapp
		# stub function to override parent.  we don't do dns management here.
		$this->requireCommandLine(__FUNCTION__);
		echo "\n\nsyncdns ignored: we don't do this here...: \n";
		return True;

	}
	function send_dnsserver_files($server){  //stub from classapp
	}
	function get_dnsservers(){  //stub from classapp
	}
	function prepare_dns_files($server){  //stub from classapp
	}
	function prepare_dns_zone_files($server,$arr2){  //stub from classapp
	}
	function prepare_dns_named_conf_file($server,$arr){  //stub from classapp
	}
	function new_sync_dns(){  //stub from classapp
	}
	function restart_dnsserver2($serverip){  //stub from classapp
	}
	function putArrayToStrDns($arr){  //stub from classapp
	}

}