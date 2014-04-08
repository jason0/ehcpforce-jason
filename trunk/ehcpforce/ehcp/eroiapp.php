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

	function runOp($op){ # these are like url to function mappers...  maps op variable to some functions in ehcp; This also can be seen as a controller in MVC model.
		global $id,$domainname,$op2,$_insert;
		$this->getVariable(array('id','domainname','op2','_insert'));
		$op=strtolower($op);
		$otheroperations=array('advancedsettings');


		switch ($op) {

			case 'failedlogins'				: return $this->failedlogins();break;

			#ssl related:
			case 'adjust_ssl'				: return $this->adjust_ssl();break;
			case 'pagerewrite'				: return $this->pagerewrite();break;

			# other
			case 'activate'					: return $this->activate();break;
			case 'settings'					: return $this->settings();break;
			case 'adjust_system'			: return $this->adjust_system();break;
			case 'redirect_domain'			: return $this->redirect_domain();break;
			case 'information'				: return $this->information($id);break;

			#multi-server operations:
			case 'multiserver_add_domain'	: return $this->multiserver_add_domain();break;

			case 'new_sync_all'				: return $this->new_sync_all();break;
			case 'new_sync_domains'			: return $this->new_sync_domains();break;
			case 'new_sync_dns'				: return $this->new_sync_dns();break;
			case 'multiserver_add_ftp_user_direct': return $this->gui_multiserver_add_ftp_user_direct();break;

			#single-server operations:
			case 'bulkaddemail'				: return $this->bulkAddEmail();break;
			case 'whitelist'				: return $this->whitelist();break;
			case 'fixmailconfiguration'		: return $this->fixMailConfiguration();break;
			case 'dofixmailconfiguration'	: return $this->addDaemonOp('fixmailconfiguration','','','','fix mail configuration');break;
			case 'dofixapacheconfigssl'		: return $this->addDaemonOp('fixApacheConfigSsl','','','','fixApacheConfigSsl');break;
			case 'dofixapacheconfignonssl'	: return $this->addDaemonOp('fixApacheConfigNonSsl','','','','fixApacheConfigNonSsl');break;
			case 'dofixapacheconfignonssl2'	: return $this->addDaemonOp('fixApacheConfigNonSsl2','','','','fixApacheConfigNonSsl2');break;
			case 'rebuild_webserver_configs': return $this->rebuild_webserver_configs();break;

			case 'updatediskquota'			: return $this->updateDiskQuota();break;
			case 'doupdatediskquota' 		: $this->addDaemonOp('updatediskquota','',$domainname,'','update disk quota');return $this->displayHome();break;

			#editing of dns/apache templates for domains, on ehcp db
			case 'editdnstemplate'			: return $this->editDnsTemplate();break;
			case 'editapachetemplate'		: return $this->editApacheTemplate();break;
			case 'editdomainaliases'		: return $this->editDomainAliases();break;

			case 'changedomainserverip'		: return $this->changedomainserverip();break;
			case 'warnings'					: break; # this will be written just before show..
			case 'bulkadddomain'			: return $this->bulkaddDomain();break ;
			case 'bulkdeletedomain' 		: return $this->bulkDeleteDomain();break ;
			case 'exportdomain'				: return $this->exportDomain();break;

			case 'adddnsonlydomain' 		: return $this->addDnsOnlyDomain();break;

			case 'addslavedns'				: return $this->addSlaveDNS();break;
			case 'removeslavedns'			: return $this->removeSlaveDNS();break;

			case 'addcustomftp'				: return $this->addCustomFTP();break;
			case 'removecustomftp'			: return $this->removeCustomFTP();break;

			case 'adddnsonlydomainwithpaneluser': return $this->addDnsOnlyDomainWithPaneluser();break;

			case 'getselfftpaccount'		: return $this->getSelfFtpAccount();break;
			case 'adddomaintothispaneluser'	: return $this->addDomainToThisPaneluser();break;

			case 'dodownloadallscripts'		: return $this->doDownloadAllscripts();break;
			case 'choosedomaingonextop'		: return $this->chooseDomainGoNextOp();break;

			case 'getmysqlserver'			: return $this->getMysqlServer();break;

			case 'emailforwardingsself'		: return $this->emailForwardingsSelf();break;
			case 'addemailforwardingself'	: return $this->addEmailForwardingSelf();break;

			case 'cmseditpages'				: return $this->cmsEditPages();break;
			case 'listservers' 				: return $this->listServers();break;
			case 'addserver'				: return $this->addServer();break;
			case 'addiptothisserver'		: return $this->add_ip_to_this_server();break;
			case 'setactiveserverip'		: return $this->set_active_server_ip();break;


			case 'advancedsettings'			: return $this->advancedsettings();break;
			case 'delemailforwarding'		: return $this->delEmailForwarding();break;
			case 'addemailforwarding'		: return $this->addEmailForwarding();break;
			case 'emailforwardings' 		: return $this->emailForwardings();break;
			case 'addscript'				: return $this->addScript();break;
			case 'addnewscript'	 			: return $this->addNewScript();break;

			case 'suggestnewscript' 		: return $this->suggestnewscript();break;
			case 'listselectdomain' 		: return $this->listselectdomain();break;
			case 'selectdomain'	 			: return $this->selectdomain($id);break;
			case 'deselectdomain'   		: return $this->deselectdomain();break;
			case 'otheroperations'  		: return $this->otheroperations();break;


			case 'loadconfig'	   			: return $this->loadConfig();break;
			#case 'showconf'					: return $this->showConfig();break;
			case 'changemypass'				: return $this->changeMyPass();break;

			# for mysql, stop and start is meaningless, because if mysql cannot run, then, panel also cannot be accessible or this functions do not work.
			case 'dorestartmysql'			: $this->requireAdmin(); return $this->add_daemon_op(array('op'=>'service','info'=>'mysql','info2'=>'restart')); break;

			case 'dostopapache2'			: $this->requireAdmin(); return $this->add_daemon_op(array('op'=>'service','info'=>'apache2','info2'=>'stop')); break;
					case 'dostartapache2'			: $this->requireAdmin(); return $this->add_daemon_op(array('op'=>'service','info'=>'apache2','info2'=>'start')); break;
					case 'dorestartapache2'			: $this->requireAdmin(); return $this->add_daemon_op(array('op'=>'service','info'=>'apache2','info2'=>'restart')); break;

					case 'dostopvsftpd'				: $this->requireAdmin(); return $this->add_daemon_op(array('op'=>'service','info'=>'vsftpd','info2'=>'stop')); break;
					case 'dostartvsftpd'			: $this->requireAdmin(); return $this->add_daemon_op(array('op'=>'service','info'=>'vsftpd','info2'=>'start')); break;
					case 'dorestartvsftpd'			: $this->requireAdmin(); return $this->add_daemon_op(array('op'=>'service','info'=>'vsftpd','info2'=>'restart')); break;

					case 'dostopbind'				: $this->requireAdmin(); return $this->add_daemon_op(array('op'=>'service','info'=>'bind9','info2'=>'stop')); break;
					case 'dostartbind'				: $this->requireAdmin(); return $this->add_daemon_op(array('op'=>'service','info'=>'bind9','info2'=>'start')); break;
					case 'dorestartbind'			: $this->requireAdmin(); return $this->add_daemon_op(array('op'=>'service','info'=>'bind9','info2'=>'restart')); break;

					case 'dostoppostfix'			: $this->requireAdmin(); return $this->add_daemon_op(array('op'=>'service','info'=>'postfix','info2'=>'stop')); break;
					case 'dostartpostfix'			: $this->requireAdmin(); return $this->add_daemon_op(array('op'=>'service','info'=>'postfix','info2'=>'start')); break;
					case 'dorestartpostfix'			: $this->requireAdmin(); return $this->add_daemon_op(array('op'=>'service','info'=>'postfix','info2'=>'restart')); break;


					case 'donewsyncdomains'			: $this->requireAdmin(); return $this->add_daemon_op(array('op'=>'new_sync_domains')); break;
					case 'donewsyncdns'				: $this->requireAdmin(); return $this->add_daemon_op(array('op'=>'new_sync_dns')); break;

					case 'dosyncdomains'			: return $this->addDaemonOp('syncdomains','','','','sync domains');break;
					case 'dosyncdns'				: return $this->addDaemonOp('syncdns','','','','sync dns');break;
					case 'dosyncftp' 				: return $this->addDaemonOp('syncftp','','','','sync ftp for nonstandard homes');break;
					case 'dosyncapacheauth'			: return $this->addDaemonOp('syncapacheauth','','','','sync apache auth');break;
					case 'options'		  			: return $this->options();


					case 'backups'					: return $this->backups();break;
					case 'dobackup'					: return $this->doBackup();break;
					case 'dorestore'				: return $this->doRestore();break;
					case 'listbackups'				: return $this->listBackups();break;

					# these sync functions are executed in daemon mode.
					case 'updatehostsfile'			: return $this->updateHostsFile();break;
					case 'syncdomains'				: return $this->syncDomains();break;
					case 'syncftp'					: return $this->syncFtp();break;
				#	case 'syncdns'					: return $this->syncDns();break;
					case 'syncall'					: return $this->syncAll();break;
					case 'syncapacheauth'			: return $this->syncApacheAuth();break;
					case 'fixapacheconfigssl'		: return $this->fixApacheConfigSsl();break;
					case 'fixapacheconfignonssl'	: return $this->fixApacheConfigNonSsl();break;


					#case 'syncallnew'	: return $this->syncallnew();break;
					case 'listdomains'				: return $this->listDomains();break;  # ayni zamanda domain email userlarini da listeler.
					case 'subdomains'	   			: return $this->subDomains();	break;
					case 'addsubdomain'	 			: return $this->addSubDomain();  break;
					case 'addsubdomainwithftp'		: return $this->addSubDomainWithFtp();  break;
					case 'addsubdirectorywithftp'	:return $this->addSubDirectoryWithFtp();  break;


					case 'delsubdomain'	 			: return $this->delSubDomain();  break;


					case 'editdomain'				: return $this->editdomain();
					case 'listpassivedomains'		: return $this->listDomains('',$this->passivefilt);break;
					case 'phpinfo'					: return $this->phpinfo();break;
					case 'help'						: return $this->help();break;
					case 'syncpostfix'				: return $this->syncpostfix();break;
					case 'listemailusers'			: return $this->listemailusers();break;
					case 'listallemailusers'		: return $this->listallemailusers();break;
					case 'listpanelusers'   		: return $this->listpanelusers();break;
					case 'resellers'				: return $this->resellers();break;

					case 'deletepaneluser'  		: return $this->deletepaneluser();break;

					case 'operations'	   			: $this->requireAdmin();$this->listTable('operations','operations_table','');break;

					case 'listallftpusers'  		: return $this->listAllFtpUsers();break;
					case 'listftpusersrelatedtodomains': return $this->listAllFtpUsers("domainname<>''");break;
					case 'listftpuserswithoutdomain': return $this->listAllFtpUsers("domainname='' or domainname is null");break;
					case 'listftpusers'	 			: return $this->listftpusers();break;
					case 'sifrehatirlat'			: return $this->sifreHatirlat();break;
					case 'todolist'					: return $this->todolist();break;
					case 'adddomain'				: return $this->addDomain();break;
					case 'adddomaineasy'			: return $this->addDomainEasy();break;
					case 'adddomaineasyip'			: return $this->addDomainEasyip();break;
					case 'transferdomain'	   		: return $this->transferDomain(); break;
					case 'deletedomain'				: return $this->deleteDomain();break;
					case 'addemailuser'				: return $this->addEmailUser();break;
					case 'addftpuser'				: return $this->addFtpUser();break;
					case 'addftptothispaneluser'	: return $this->addFtpToThisPaneluser();break;# added in 7.6.2009
					case 'add_ftp_special'			: return $this->add_ftp_special();break;

					case 'userop'		   			: return $this->userop();break;
					case 'domainop'		 			: return $this->domainop();break;
					case 'addmysqldb'	   			: return $this->addMysqlDb();   break;
					case 'addmysqldbtouser' 		: return $this->addMysqlDbtoUser();   break;
					case 'addpaneluser'				: return $this->addPanelUser();break;
					case 'editpaneluser'			: return $this->editPanelUser();break;
					case 'editftpuser'				: return $this->editFtpUser();break;
					case 'domainsettings'			: return $this->domainSettings();break;

					case 'logout'					: return $this->logout();break;
					case 'daemon'					: return $this->daemon();break;
					case 'test'						: return $this->test();	break;
					case 'aboutcontactus'   		: return $this->aboutcontactus();break;
					case 'applyforaccount'  		: return $this->applyforaccount();break;
					case 'applyfordomainaccount'	: return $this->applyfordomainaccount();break;
					case 'applyforftpaccount'		: return $this->applyforftpaccount();break;
					case 'setconfigvalue2'  		: return $this->setConfigValue2($id);break;
					case 'customhttp'				: return $this->customHttpSettings();break;
					case 'addcustomhttp'			: return $this->addCustomHttp();break;
					case 'deletecustom'				: return $this->deleteCustomSetting();break;
					case 'customdns'				: return $this->customDnsSettings();break;
					case 'addcustomdns'				: return $this->addCustomDns();break;
					case 'dbedituser'	   			: return $this->dbEditUser();break;
					case 'dbadduser'				: return $this->dbAddUser();break;

					case 'custompermissions'		: return $this->custompermissions();break;
					case 'addcustompermission'		: return $this->addcustompermission();break;

					case 'editemailuser'			: # same as below
					case 'editemailuserself'		: return $this->editEmailUser();break;

					case 'editemailuserautoreplyself':
					case 'editemailuserautoreply'	: return $this->editEmailUserAutoreply();break;

					case 'editemailuserpasswordself':
					case 'editemailuserpassword'	: return $this->editEmailUserPassword();break;

					case 'directories'	  			: return $this->directories();break;
					case 'listmyalldirectories'		: return $this->listMyAllDirectories();break;
					case 'adddirectory'	 			: return $this->addDirectory();break;
					case 'deletedirectory'  		: return $this->deleteDirectory();break;
					case 'changetemplate'   		: return $this->changetemplate();break;
					case 'addredirect'				: return $this->addRedirect();break;
					case 'serverstatus'				: return $this->serverStatus();break;
					case 'setlanguage'				: $this->setLanguage($id);$this->displayHome();break;
					case 'setdefaultdomain'			: $this->setDefaultDomain();$this->displayHome();break;

					case 'dologin'					: # default anasayfa, same as below:
					case ''							: $this->displayHome();break;

					# virtual machine (vps) opcodes:
					case 'vps_home'					: return $this->call_func_in_module('Vps_Module','vps_home'); break;
					case 'vps'						: return $this->call_func_in_module('Vps_Module','vps'); break;
					case 'vps_mountimage'			: return $this->call_func_in_module('Vps_Module','vps_mountimage'); break;
					case 'vps_dismountimage'		: return $this->call_func_in_module('Vps_Module','vps_dismountimage'); break;
					case 'add_vps'					: return $this->call_func_in_module('Vps_Module','add_vps'); break;


					default							: return $this->errorText("(runop) internal ehcp error: Undefined operation: $op <br> This feature may not be complete");break;

		}# switch
		return True;

	}# func runop

}