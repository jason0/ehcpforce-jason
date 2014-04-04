<?php

include_once("classapp.php"); # real application class

/* this is jason's attempt at class inheritance.
 *
 *
 */
class EroiApp extends Application {


	function showSimilarFunctions($func){
		# the text here may be read from a template
		$out1="Similar/Related $func Functions:";

		switch($func){
			case 'ftp'   : $out="<a href='?op=addftpuser'>Add New ftp</a>, <a href='?op=addftptothispaneluser'>Add ftp Under My ftp</a>, <a href='?op=addsubdirectorywithftp'>Add ftp in a subDirectory Under Domainname</a>, <a href='?op=addsubdomainwithftp'>Add subdomain with ftp</a>, <a href='?op=add_ftp_special'>Add ftp under /home/xxx (admin)</a>, <a href='net2ftp' target=_blank>WebFtp (Net2Ftp)</a>, <a href='?op=addcustomftp'>Add Custom FTP Account (Admins Only)</a>, <a href='?op=removecustomftp'>Remove Custom FTP Account (Admin Only)</a>, <a href='?op=listallftpusers'>List All Ftp Users</a> ";break;
			case 'mysql' : $out="<a href='?op=domainop&amp;action=listdb'>List / Delete Mysql Db's</a>, <a href='?op=addmysqldb'>Add Mysql Db&amp;dbuser</a>, <a href='?op=addmysqldbtouser'>Add Mysql db to existing dbuser</a>, <a href='?op=dbadduser'>Add Mysql user to existing db</a>, <a href='/phpmyadmin' target=_blank>phpMyadmin</a>";break;
			// case 'email' : $out="<a href='?op=listemailusers'>List Email Users / Change Passwords</a>, <a href='?op=addemailuser'>Add Email User</a>, Email forwardings: <a href='?op=emailforwardings'>List</a> - <a href='?op=addemailforwarding'>Add</a>, <a href='?op=bulkaddemail'>Bulk Add Email</a>, <a href='?op=editEmailUserAutoreply'>edit Email Autoreply</a> ,<a href='webmail' target=_blank>Webmail (Squirrelmail)</a>";break;
			case 'domain': $out="<a href='?op=addDomainToThisPaneluser'>Add Domain To my ftp user (Most Easy)</a> - <a href='?op=adddomaineasy'>Easy Add Domain (with separate ftpuser)</a> - <a href='?op=adddomain'>Normal Add Domain (Separate ftp&panel user)</a> - <a href='?op=bulkadddomain'>Bulk Add Domain</a> - <a href='?op=adddnsonlydomain'>Add dns-only hosting</a> - <a href='?op=adddnsonlydomainwithpaneluser'>Add dns-only hosting with separate paneluser</a>-<br><a href='?op=addslavedns'>Make Domain a DNS Slave</a> - <a href='?op=removeslavedns'>Remove DNS Slave, if any</a><br>

		<br>Different IP(in this server, not multiserver): <a href='?op=adddomaineasyip'>Easy Add Domain to different IP</a> - <a href='?op=setactiveserverip'>set Active webserver IP</a><br>List Domains: <a href='?op=listselectdomain'>short listing</a> - <a href='?op=listdomains'>long listing</a>";break;
			case 'redirect': $out="<a href='?op=editdomainaliases'>Edit Domain Aliases</a>";break;
			case 'options' : $out=	"
	<br><a href='?op=options&edit=1'>Edit/Change Options</a><br>
	<br><a href='?op=changemypass'>Change My Password</a>
	<br><a href='?op=listpanelusers'>List/Add Panelusers/Resellers</a>
" .
/*
	<br><a href='?op=dosyncdns'>Sync Dns</a>
	<br><a href='?op=dosyncdomains'>Sync Domains</a><br>
	<br><a href='?op=dosyncftp'>Sync Ftp (for non standard home dirs)</a><br>
	<hr><a href='?op=advancedsettings'>Advanced Settings</a><br><br>
	<br><a href='?op=dofixmailconfiguration'>Fix Mail Configuration<br>Fix ehcp Configuration</a> (This is used after changing ehcp mysql user pass, or if you upgraded from a previous version, in some cases)<br>
	<br><br><a href='?op=dofixapacheconfigssl'>Fix apache Configuration with ssl</a>(use with caution,may be risky)<br><br>
	<br><a href='?op=dofixapacheconfignonssl'>Fix apache Configuration without ssl</a><br>
	<br><a href='?op=dofixapacheconfignonssl2'>Fix apache Configuration without ssl, way2</a> - use this if first wone does not work. this deletes custom apache configurations, if any<br>
	<br>
	<hr>
	<a href='?op=listservers'>List/Add Servers/ IP's</a><br>
	<hr>

	Experimental:
	<br><a href='?op=donewsyncdns'>New Sync Dns - Multiserver</a>
	<br><a href='?op=donewsyncdomains'>New Sync Domains - Multiserver</a><br>
	<br><a href='?op=multiserver_add_domain'>Multiserver Add Domain</a>
	<hr>

	";break;
			case 'customhttpdns': $out="Custom Http: <a href='?op=customhttp'>List</a> - <a href='?op=addcustomhttp'>Add</a>, Custom dns: <a href='?op=customdns'>List</a> - <a href='?op=addcustomdns'>Add</a> --  Custom Permissions: <a href='?op=custompermissions'>List</a> - <a href='?op=addcustompermission'>Add</a>";break;
			case 'subdomainsDirs': $out="SubDomains: <a href='?op=subdomains'>List</a> - <a href='?op=addsubdomain'>Add</a> - <a href='?op=addsubdomainwithftp'>Add subdomain with ftp</a> - <a href='?op=addsubdirectorywithftp'>Add subdirectory with ftp (Under domainname)</a>";break;
			case 'HttpDnsTemplatesAliases': $out="<a href='?op=editdnstemplate'>Edit dns template for this domain </a> - <a href='?op=editapachetemplate'>Edit apache template for this domain </a> - <a href='?op=editdomainaliases'>Edit Aliases for this domain </a>";break;
			case 'panelusers': $out="<a href='?op=listpanelusers'>List All Panelusers/Clients</a>, <a href='?op=resellers'>List Resellers</a>, <a href='?op=addpaneluser'>Add Paneluser/Client/Reseller</a>";break;
			case 'server':$out="<a href='?op=listservers'>List Servers/IP's</a> - <a href='?op=addserver'>Add Server</a> - <a href='?op=addiptothisserver'>Add ip to this server</a> - <a href='?op=setactiveserverip'>set Active webserver IP</a>";break;
			case 'backup':$out="<a href='?op=dobackup'>Backup</a> - <a href='?op=dorestore'>Restore</a> - <a href='?op=listbackups'>List Backups</a>";break;
			case 'vps': $out="<a href='?op=vps'>VPS Home</a> - <a href='?op=add_vps'>Add new VPS</a> - <a href='?op=settings&group=vps'>VPS Settings</a> - <a href='?op=vps&op2=other'>Other Vps Ops</a>";break;
			case 'pagerewrite': $out="<a href='?op=pagerewrite'>page rewrite home</a> - <a href='?op=pagerewrite&op2=add'>add page rewrite</a>";break;
			case 'custompermissions': $out="<a href='?op=custompermissions'>List Custom Permissions</a> - <a href='?op=addcustompermission'>Add Custom Permissions</a>";break;

			default	 : $out="(internal ehcp error) This similar function is not defined in ".__FUNCTION__." : ($func)"; $out1='';break;
		}

		$this->output.="<br><br>$out1".$out."<br>";
	}



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
		echo "\n\n" . __FUNCTION__ ." ignored: we don't do this here...: \n";
		return True;
	}
	function customDnsSettings(){  //stub from classapp
		echo "\n\n" . __FUNCTION__ ." ignored: we don't do this here...: \n";
		return True;
	}
	function addCustomDns(){  //stub from classapp
		echo "\n\n" . __FUNCTION__ ." ignored: we don't do this here...: \n";
		return True;
	}
	function addDnsOnlyDomainWithPaneluser(){  //stub from classapp
		echo "\n\n" . __FUNCTION__ ." ignored: we don't do this here...: \n";
		return True;
	}
	function addSlaveDNS(){  //stub from classapp
		echo "\n\n" . __FUNCTION__ ." ignored: we don't do this here...: \n";
		return True;
	}
	function removeSlaveDNS(){  //stub from classapp
		echo "\n\n" . __FUNCTION__ ." ignored: we don't do this here...: \n";
		return True;
	}
	function addDnsOnlyDomain(){  //stub from classapp
		echo "\n\n" . __FUNCTION__ ." ignored: we don't do this here...: \n";
		return True;
	}
	function checkDynDns(){  //stub from classapp
		echo "\n\n" . __FUNCTION__ ." ignored: we don't do this here...: \n";
		return True;
	}
	function getDnsServer(){  //stub from classapp
		echo "\n\n" . __FUNCTION__ ." ignored: we don't do this here...: \n";
		return True;
	}
	function dnsZoneFiles($arr){  //stub from classapp
		echo "\n\n" . __FUNCTION__ ." ignored: we don't do this here...: \n";
		return True;
	}
	function dnsNamedConfFile($arr){  //stub from classapp
		echo "\n\n" . __FUNCTION__ ." ignored: we don't do this here...: \n";
		return True;
	}
	function syncDns(){  //stub from classapp
		# stub function to override parent.  we don't do dns management here.
		$this->requireCommandLine(__FUNCTION__);
		echo "\n\n" . __FUNCTION__ ." ignored: we don't do this here...: \n";
		return True;

	}
	function send_dnsserver_files($server){  //stub from classapp
		echo "\n\n" . __FUNCTION__ ." ignored: we don't do this here...: \n";
		return True;
	}
	function get_dnsservers(){  //stub from classapp
		echo "\n\n" . __FUNCTION__ ." ignored: we don't do this here...: \n";
		return True;
	}
	function prepare_dns_files($server){  //stub from classapp
		echo "\n\n" . __FUNCTION__ ." ignored: we don't do this here...: \n";
		return True;
	}
	function prepare_dns_zone_files($server,$arr2){  //stub from classapp
		echo "\n\n" . __FUNCTION__ ." ignored: we don't do this here...: \n";
		return True;
	}
	function prepare_dns_named_conf_file($server,$arr){  //stub from classapp
		echo "\n\n" . __FUNCTION__ ." ignored: we don't do this here...: \n";
		return True;
	}
	function new_sync_dns(){  //stub from classapp
		echo "\n\n" . __FUNCTION__ ." ignored: we don't do this here...: \n";
		return True;
	}
	function restart_dnsserver2($serverip){  //stub from classapp
		echo "\n\n" . __FUNCTION__ ." ignored: we don't do this here...: \n";
		return True;
	}
	function putArrayToStrDns($arr){  //stub from classapp
		echo "\n\n" . __FUNCTION__ ." ignored: we don't do this here...: \n";
		return True;
	}

}