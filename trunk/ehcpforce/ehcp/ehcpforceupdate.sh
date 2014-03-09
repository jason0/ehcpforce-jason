#!/bin/bash
# EHCP Non-Force Edition to EHCP Force Edition Upgrade Script
# By earnolmartin@gmail.com
# http://www.ehcpforce.tk

###########
#FUNCTIONS#
###########

function aptget_Update(){
	apt-get update
}

function libldapFix(){ # by earnolmartin@gmail.com
	# install libldap, for vsftpd fix, without prompts
	#Remove originally installed libpam-ldap if it exists
	origDir=$(pwd)	
	aptgetRemove libpam-ldap
	DEBIAN_FRONTEND=noninteractive apt-get -y install libpam-ldap
	cd $patchDir
	mkdir lib32gccfix
	cd lib32gccfix
	wget -O "ldap_conf.tar.gz" http://dinofly.com/files/linux/ldap_conf_64bit_vsftpd.tar.gz
	tar -zxvf ldap_conf.tar.gz
	cp ldap.conf /etc/
	cd $origDir
}  

function fixApacheEnvVars(){
	# Check to make sure export APACHE_LOG_DIR=/var/log/apache2$SUFFIX exists
	if [ -e "/etc/apache2/envvars" ]; then
		APACHELOGCHECK=$(cat "/etc/apache2/envvars" | grep "APACHE_LOG_DIR=*")
		if [ -z "$APACHELOGCHECK" ]; then
			echo "export APACHE_LOG_DIR=/var/log/apache2\$SUFFIX" >> "/etc/apache2/envvars"
		fi
	fi
}

function slaveDNSApparmorFix(){ # by earnolmartin@gmail.com
	if [ -e /etc/apparmor.d/usr.sbin.named ]; then
				echo -e "\nChanging bind apparmor rule to allow master DNS synchronization for slave setups.\n"
				sed -i 's#/etc/bind/\*\* r,#/etc/bind/\*\* rw,#g' /etc/apparmor.d/usr.sbin.named
	fi
}

function changeApacheUser(){ # by earnolmartin@gmail.com
	# Apache should run as the vsftpd account so that FTP connections own the file and php scripts can own the file
	# Without this fix, files uploaded by ftp could not be changed by PHP scripts... AND
	# Files uploaded / created by PHP scripts could not be modified (chmod) via FTP clients
	
	if [ -e "/etc/apache2/envvars" ]; then
		sed -i "s/export APACHE_RUN_USER=.*/export APACHE_RUN_USER=vsftpd/g" "/etc/apache2/envvars"
		if [ -e "/var/lock/apache2" ]; then
			chown vsftpd "/var/lock/apache2"
		fi
	fi
	
	# Also change nginx user
	if [ -e "/etc/nginx/nginx.conf" ]; then
		sed -i "s/user .*/user vsftpd www-data;/g" "/etc/nginx/nginx.conf"
	fi
	
	# Also change php-fpm user
	if [ -e "/etc/php5/fpm/pool.d/www.conf" ]; then
		sed -i "s/user = .*/user = vsftpd/g" "/etc/php5/fpm/pool.d/www.conf"
		sed -i "s/group = .*/group = www-data/g" "/etc/php5/fpm/pool.d/www.conf"
	fi
}

function nginxRateLimit(){
	if [ -e "/etc/nginx/nginx.conf" ]; then
		NGINXHASRATELIMIT=$(cat "/etc/nginx/nginx.conf" | grep "limit_req_zone")
		if [ -z "$NGINXHASRATELIMIT" ]; then
			sed -i '/http {/a limit_req_zone $binary_remote_addr zone=one:10m rate=10r/s;' "/etc/nginx/nginx.conf"
		fi
	fi
}

# Secures BIND and prevents UDP Recursion Attacks:
# https://www.team-cymru.org/Services/Resolvers/instructions.html
# Good explanation FROM MS Forums (LOL):  http://social.technet.microsoft.com/Forums/windowsserver/en-US/24ea1094-0ae4-47b5-9b74-2f77884cce15/dns-recursion?forum=winserverNIS
function disableRecursiveBIND(){ # by earnolmartin@gmail.com
	# Get Resolv.conf and do not run this code if nameserver is set to 127.0.0.1
	RESOLVCOUNT=$(cat "/etc/resolv.conf" | grep -c "nameserver")
	RESOLVLOCAL=$(cat "/etc/resolv.conf" | grep "nameserver 127.0.0.1")
	
	if [ "$RESOLVCOUNT" == "1" ] && [ ! -z "$RESOLVLOCAL" ]; then
		echo -e "Skipping Bind Recursion Settings Due to 127.0.0.1 Nameserver"
	else
		bindOptionsFile="/etc/bind/named.conf.options"
		bindBckFile="/etc/bind/named.conf.options_backup"
		if [ -e "$bindOptionsFile" ]; then
			
			# Create a backup of the original
			if [ ! -e "$bindBckFile" ]; then
				cp "$bindOptionsFile" "$bindBckFile"
			fi
			
			# Remove all blank lines at the end of the file:
			# BINDNoEmptyLines=$(sed '/^ *$/d' "$bindOptionsFile")
			# Better code here to strip out ending lines of empty text:   http://stackoverflow.com/questions/7359527/removing-trailing-starting-newlines-with-sed-awk-tr-and-friends
			# Can also do this for leading and trailing empty lines:  sed -e :a -e '/./,$!d;/^\n*$/{$d;N;};/\n$/ba' file
			BINDNoEmptyLines=$(sed -e :a -e '/^\n*$/{$d;N;};/\n$/ba' "$bindOptionsFile")
			echo "$BINDNoEmptyLines" > "$bindOptionsFile"
		
			# Add recursion no
			RecursiveSettingCheck=$( cat "$bindOptionsFile" | grep -o "^recursion .*" | grep -o " .*$" | grep -o "[^ ].*" )
			if [ -z "$RecursiveSettingCheck" ]; then
				# Put it one line before close pattern
				sed -i '$i \recursion no;' "$bindOptionsFile"
			else
				sed -i 's/^recursion .*/recursion no;/g' "$bindOptionsFile"
			fi
			
			# Add additional-from-cache no
			RecursiveCacheCheck=$( cat "$bindOptionsFile" | grep -o "^additional-from-cache .*" | grep -o " .*$" | grep -o "[^ ].*" )
			if [ -z "$RecursiveCacheCheck" ]; then
				sed -i '$i \additional-from-cache no;' "$bindOptionsFile"
			else
				sed -i 's/^additional-from-cache .*/additional-from-cache no;/g' "$bindOptionsFile"
			fi
		fi
		
		# Extra optional step
		#if [ -e "/etc/default/bind9" ]; then
		#	sed -i 's/^RESOLVCONF=.*/RESOLVCONF=no/g' "/etc/default/bind9"
		#fi
		
		service bind9 restart
	fi
}

function getLatestEHCPFiles(){
	# Create Downloads Folder
	cd ~
	if [ ! -e "Downloads" ]; then
	  mkdir "Downloads"    
	fi
	cd ~/Downloads
	
	# If ehcp folder already exists, delete it	
	if [ -e "ehcp" ]; then
		rm -Rf "ehcp"
	fi
	
	# Get the latest snapshot files of EHCP Force
	wget -O "ehcpforce_stable_snapshot.tar.gz" -N http://sourceforge.net/projects/ehcpforce/files/ehcpforce_stable_snapshot.tar.gz/download
	tar -zxvf "ehcpforce_stable_snapshot.tar.gz"
	cd ehcp
	
	# Remove stock config.php (we want to use the existing one)
	rm config.php
	
	# Copy files over
	LATESTBACKUPDIR="/var/www/new/ehcp_nonforce"
	CurDate=$(date +%Y_%m_%d_%s)
	cd ..
	if [ ! -e "/var/www/new/ehcp_nonforce" ]; then
		cp -R "/var/www/new/ehcp" "/var/www/new/ehcp_nonforce"
	else
		cp -R "/var/www/new/ehcp" "/var/www/new/ehcp_nonforce_$CurDate"
		LATESTBACKUPDIR="/var/www/new/ehcp_nonforce_$CurDate"
	fi
	cp -R "ehcp" "/var/www/new/"
	cp "$LATESTBACKUPDIR/config.php" "/var/www/new/ehcp"
	
	# Fix permissions
	fixEHCPPerms
	logDirFix
	
	# Move the old EHCP files into backup directory (enhance security)
	mv "$LATESTBACKUPDIR" "$EHCPBACKUPDIR/ehcp_force_backup_$CurDate"
}

function updateDaemon(){
	cp /var/www/new/ehcp/ehcp /etc/init.d/
	
	# Get rid of experimental python daemon
	# It was never used to begin with
	if [ -e "/etc/init.d/ehcp_daemon.py" ]; then
		rm "/etc/init.d/ehcp_daemon.py"
	fi
}

function fail2ban(){
	apt-get install fail2ban
	service fail2ban stop

	cp "/var/www/new/ehcp/fail2ban/apache-dos.conf" "/etc/fail2ban/filter.d/apache-dos.conf"
	cp "/var/www/new/ehcp/fail2ban/ehcp.conf" "/etc/fail2ban/filter.d/ehcp.conf"

	if [ ! -e "/etc/fail2ban/jail.local" ]; then
		cp "/var/www/new/ehcp/fail2ban/jail.local" "/etc/fail2ban/jail.local"
	fi

	EHCPINF2BAN=$(cat /etc/fail2ban/jail.local | grep "[ehcp]")
	APACHEDOSINF2BAN=$(cat /etc/fail2ban/jail.local | grep "[apache-dos]")

	if [ -z "$EHCPINF2BAN" ]; then
	   echo "
	[ehcp]
	# fail2ban section for Easy Hosting Control Panel, ehcp.net
	enabled = true
	port = http,https
	filter = ehcp
	logpath = /var/www/new/ehcp/log/ehcp_failed_authentication.log
	maxretry = 10" >> "/etc/fail2ban/jail.local"
	fi

	if [ -z "$APACHEDOSINF2BAN" ]; then
	   echo "
	[apache-dos]
	# Apache Anti-DDoS Security Based Log Entries from Mod Evasive Apache Module
	enabled = true
	port = http,https
	filter = apache-dos
	logpath = /var/log/apache*/*error.log
	maxretry = 5" >> "/etc/fail2ban/jail.local"
	fi

	service fail2ban restart
}

function mysqlUseLocalHost(){
	if [ -e "/etc/mysql/my.cnf" ]; then
		sed -i "s/^bind-address.*/bind-address=localhost/g" "/etc/mysql/my.cnf"
	fi
	
	service mysql restart
}

function apacheSecurity(){
	apt-get install libapache2-mod-evasive
	apt-get install libapache-mod-security
	
	# Make sure the conf.d directory exists
	addConfDFolder

	# Shouldn't be running, but attempt to stop it anyways just in casee
	service apache2 stop
	
	# Ensures that we always get the latest security rules and apply only the latest
	if [ -e "/etc/apache2/mod_security_rules" ]; then
		rm -R "/etc/apache2/mod_security_rules"
	fi
	
	mkdir /etc/apache2/mod_security_rules
	cd ~/Downloads
	
	if [ -e "mod_security_rules_latest" ]; then
		rm -R "mod_security_rules_latest"
	fi

	mkdir "mod_security_rules_latest"
	
	# Different rules based on different versions
	if [ "$yrelease" -gt "13" ] || [ "$yrelease" == "13" ] && [ "$mrelease" == "10" ]; then
		wget -N -O "mod_security_rules.tar.gz" "http://www.dinofly.com/files/linux/mod_security_rules_13.10.tar.gz"
	else
		wget -N -O "mod_security_rules.tar.gz" "http://www.dinofly.com/files/linux/mod_security_base_rules.tar.gz"
	fi
	
	tar -zxvf "mod_security_rules.tar.gz" -C "mod_security_rules_latest"
	mv mod_security_rules_latest/* /etc/apache2/mod_security_rules
	chown -R root:root /etc/apache2/mod_security_rules

	MODSECURE="/etc/apache2/conf.d/modsecure"
	MODEVASIVE="/etc/apache2/conf.d/modevasive"

	if [ ! -e "$MODSECURE" ]; then
	   cp "/var/www/new/ehcp/mod_secure/modsecure" "$MODSECURE"
	fi

	if [ ! -e "$MODEVASIVE" ]; then
	   cp "/var/www/new/ehcp/mod_secure/modevasive" "$MODEVASIVE"
	fi
	
	if [ "$yrelease" -gt "13" ] || [ "$yrelease" == "13" ] && [ "$mrelease" == "10" ]; then
		a2enmod evasive
		a2enmod security2
	else
		a2enmod mod-evasive
		a2enmod mod-security
	fi
}

function finalize(){
	# Make sure all log files have correct group
	ERRORLOGAPACH="/var/log/apache2/access.log"
	if [ -e "$ERRORLOGAPACH" ]; then
		APACHLOGGROUPOWNER=$(ls -ali "$ERRORLOGAPACH" | awk '{print $5}')
		chown root:"$APACHLOGGROUPOWNER" -R "/var/log/apache2"
	fi
	
	cd ~/Downloads
	
	# Set apache2 as default web server (user can change this later in panel)
	wget -N -O "setehcpapache2.tar.gz" http://dinofly.com/files/linux/ehcp/setehcpapache2.tar.gz
	tar -zxvf "setehcpapache2.tar.gz"
	php setapache2.php
	
	# Sync domains
	wget -N -O "syncdomains_apiscript.tar.gz" http://dinofly.com/files/linux/ehcp/syncdomains_apiscript.tar.gz
	tar -zxvf "syncdomains_apiscript.tar.gz"
	php syncdomains.php
	
	# Killall and restart mysql
	killAllMySQLAndRestart
	
	# Restart ehcp
	service ehcp restart
	
	echo -e "\nWaiting 15 seconds before restarting apache2 daemon so that website configs are reconstructed.\n"
	sleep 15
	
	# Restart apache
	service apache2 restart
	
}

# Get distro name , by Marcel <marcelbutucea@gmail.com>, thanks to marcel for fixing whole code syntax
# No longer works in Ubuntu 13.04
# Fixed by Eric Martin <earnolmartin@gmail.com>
function checkDistro() {

		# Below code doesn't work
		
		#cat /etc/*release | grep -i ubuntu &> /dev/null && distro="ubuntu"
		#cat /etc/*release | grep -i red  &> /dev/null && distro="redhat" # not yet supported
		#cat /etc/*release | grep -i centos && distro="centos"
		#cat /etc/*release | grep -i debian &> /dev/null && distro="debian"
		
		# Get distro properly
		if [ -e /etc/issue ]; then
			distro=$( cat /etc/issue | awk '{ print $1 }' )
		fi
		
		if [ -z "$distro" ]; then
			if [ -e /etc/os-release ]; then
				distro=$( cat os-release | grep -o "^NAME=.*" | grep -o "[^NAME=\"].*[^\"]" )
			fi
		fi
		
		# Assume Ubuntu
		if [ -z "$distro" ]; then
			distro="ubuntu"
		else
			# Convert it to lowercase
			distro=$( echo $distro | awk '{print tolower($0)}' )
		fi
		 
		
		# Get actual release version information
		version=$( cat /etc/issue | awk '{ print $2 }' )
		if [ -z "$version" ]; then
			version=$( lsb_release -r | awk '{ print $2 }' )
		fi
		
		# Separate year and version
		if [[ "$version" == *.* ]]; then
			yrelease=$( echo "$version" | cut -d. -f1 )
			mrelease=$( echo "$version" | cut -d. -f2 )
		fi
		
		# Get 64-bit OS or 32-bit OS [used in vsftpd fix]
		if [ $( uname -m ) == 'x86_64' ]; then
			OSBits=64
		else
			OSBits=32
		fi 
		
		# Another way to get the version number
		# version=$(lsb_release -r | awk '{ print $2 }')
		
		echo "Your distro is $distro runnning version $version"

}

function fixVSFTPConfig(){ # by earnolmartin@gmail.com
	sed -i 's/chroot_local_user=NO/chroot_local_user=YES/g' /etc/vsftpd.conf
	allowWriteValue=$( cat /etc/vsftpd.conf | grep -o "allow_writeable_chroot=.*" | grep -o "=.*$" | grep -o "[^=].*" )
	if [ -z "$allowWriteValue" ]; then
		sh -c "echo 'allow_writeable_chroot=YES' >> /etc/vsftpd.conf"
	else
		sed -i 's/allow_writeable_chroot=NO/allow_writeable_chroot=YES/g' /etc/vsftpd.conf
	fi

	
	if [ $OSBits -eq "64" ]; then 
		#aptgetInstall libpam-ldap # this is required in buggy vsftpd installs.. ubuntu 12.04,12.10, 13.04, now... 
		libldapFix
		aptgetInstall libgcc1
		# 64-bit 500 OOPS: priv_sock_get_cmd Fix
		# seccomp_sandbox=NO
		allowSandBox=$( cat /etc/vsftpd.conf | grep -o "seccomp_sandbox=.*" | grep -o "=.*$" | grep -o "[^=].*" )
		if [ -z "$allowSandBox" ]; then
			if [ "$yrelease" == "13" ] ; then
				sh -c "echo 'seccomp_sandbox=NO' >> /etc/vsftpd.conf"
			fi
		else
			sed -i 's/seccomp_sandbox=YES/seccomp_sandbox=NO/g' /etc/vsftpd.conf
		fi		
	fi
	service vsftpd restart
}

function remove_vsftpd(){
	#Remove originally installed vsftpd
	aptgetRemove vsftpd
	# Just incase it's been installed already or another version has been installed using dpgk, let's remove it
	dpkg --remove vsftpd
}

function fixApacheDefault(){
	ApacheFile="/etc/apache2/apache2.conf"
	confStr="IncludeOptional sites-enabled/\*.conf"
	correctConfStr="IncludeOptional sites-enabled/\*"
	if [ -e "$ApacheFile" ]; then
		ConfCheck=$( cat "$ApacheFile" | grep -o "$confStr" )
		if [ ! -z "$ConfCheck" ]; then 
			sed -i "s#$confStr#$correctConfStr#g" "$ApacheFile"
			service apache2 restart
		fi
	fi
}

function removeNameVirtualHost(){
	ApacheFile="/etc/apache2/ports.conf"
	confStr="NameVirtualHost \*"
	
	if [ -e "$ApacheFile" ]; then
		ConfCheck=$( cat "$ApacheFile" | grep -o "$confStr" )
		if [ ! -z "$ConfCheck" ]; then 
			sed -i "s#$confStr##g" "$ApacheFile"
			service apache2 restart
		fi
	fi
}

function genUbuntuFixes(){
	# Ubuntu packages keep coming with new features that mess things up
	# Thanks Ubuntu for the unneccessary headaches!
	if [ ! -z "$yrelease" ]; then
		if [ "$distro" == "ubuntu" ]; then
			if [ "$yrelease" -gt "13" ] || [ "$yrelease" == "13" ] && [ "$mrelease" == "10" ]; then
				ApacheLoadConfDFolder
				fixApacheDefault
				removeNameVirtualHost
			fi
		fi
	fi
}

function ubuntuVSFTPDFix(){ # by earnolmartin@gmail.com
	# Get currently working directory
	origDir=$( pwd )
	patchDir="/root/Downloads"
	if [ ! -e $patchDir ]; then
		mkdir $patchDir
	fi
	# Ubuntu VSFTPD Fixes
	if [ ! -z "$yrelease" ]; then
		if [ "$distro" == "ubuntu" ]; then
			if [ "$yrelease" == "12" ] ; then
				 if [ "$mrelease" == "04" ]; then
					# Run 12.04 Fix
					remove_vsftpd
					echo -e "\nRunning VSFTPD fix for Ubuntu 12.04\n"
					add-apt-repository -y ppa:thefrontiergroup/vsftpd
					aptget_Update
					aptgetInstall vsftpd
					fixVSFTPConfig

				 elif [ "$mrelease" == "10" ]; then
					# Run 12.10 Fix
					remove_vsftpd
					echo -e "\nRunning VSFTPD fix for Ubuntu 12.10\n"
					#get the code
					cd $patchDir
					if [ ! -e vsftpd_2.3.5-3ubuntu1.deb ]; then
						if [ $OSBits -eq "32" ]; then 
							wget -O "vsftpd_2.3.5-3ubuntu1.deb" http://dinofly.com/files/linux/vsftpd_2.3.5-3ubuntu1_i386.deb
						else
							wget -O "vsftpd_2.3.5-3ubuntu1.deb" http://dinofly.com/files/linux/vsftpd_2.3.5-3.jme_amd64.deb
						fi
					fi
					#install
					dpkg -i vsftpd_2.3.5-3ubuntu1.deb
					cd $origDir
					fixVSFTPConfig
				 fi
			elif [ "$yrelease" == "13" ]; then
				# Ubuntu 13.04
				if [ "$mrelease" == "04" ]; then
					remove_vsftpd
					echo -e "\nRunning VSFTPD fix for Ubuntu 13.04\n"
					cd $patchDir
					if [ ! -e vsftpd_3.0.2-patched_ubuntu.deb ]; then
						if [ $OSBits -eq "32" ]; then 
							wget -O "vsftpd_3.0.2-patched_ubuntu.deb" http://dinofly.com/files/linux/vsftpd_3.0.2-patched_ubuntu_13.04_x86.deb
						else
							wget -O "vsftpd_3.0.2-patched_ubuntu.deb" http://dinofly.com/files/linux/vsftpd_3.0.2-1ubuntu1_amd64_patched.deb
						fi
					fi
					sudo dpkg -i vsftpd_3.0.2-patched_ubuntu.deb
					cd $origDir
					fixVSFTPConfig
				fi
				
				# Ubuntu 13.10
				if [ "$mrelease" == "10" ]; then
					echo -e "\nRunning VSFTPD fix for Ubuntu 13.10\n"
					fixVSFTPConfig
				fi
			fi
		fi  
	fi
}

function aptgetInstall(){

	if [ -n "$noapt" ] ; then  # skip install
		echo "skipping apt-get install for:$1"
		return
	fi

	# first, try to install without any prompt, then if anything goes wrong, normal install..
	cmd="apt-get -y --no-remove --allow-unauthenticated install $1"
	$cmd
	
	if [ $? -ne 0 ]; then
		cmd="apt-get --allow-unauthenticated install $1"
		$cmd	
	fi

}

function aptgetRemove(){
	if [ -n "$noapt" ] ; then  # skip uninstall
		echo "skipping apt-get remove for:$1"
		return
	fi 
	
	# first, try to uninstall without any prompt, then if anything goes wrong, normal uninstall..
	cmd="apt-get -y remove $1"
	$cmd
	
	if [ $? -ne 0 ]; then
		cmd="apt-get remove $1"
		$cmd	
	fi 
}

function rootCheck(){
	# Check to make sure the script is running as root
	if [ "$(id -u)" != "0" ]; then
		echo "This script must be run as root" 1>&2
		exit 1
	fi
	
	# Make EHCP Backup Directory
	EHCPBACKUPDIR="/root/Backup/EHCP_FORCE"
	if [ ! -e "$EHCPBACKUPDIR" ]; then
		mkdir -p "$EHCPBACKUPDIR"
	fi
	
	# Make nginx Backup Directory
	NGINXBACKUPDIR="/root/Backup/nginx"
	if [ ! -e "$NGINXBACKUPDIR" ]; then
		mkdir -p "$NGINXBACKUPDIR"
	fi
}

function updateBeforeInstall(){ # by earnolmartin@gmail.com
	# Update packages before installing to avoid errors
	checkAptget
	if [ "$aptIsInstalled" -eq "1" ] ; then
		echo "Updating package information and downloading package updates before installation."
		
		# Make sure the system will update and upgrade
		if [ -e "/var/lib/apt/lists/lock" ]; then
			rm "/var/lib/apt/lists/lock"
		fi
		
		# Make sure the system will update and upgrade
		if [ -e "/var/cache/apt/archives/lock" ]; then
			rm "/var/cache/apt/archives/lock"
		fi
		
		# Run update commands
		apt-key update
		apt-get update -y --allow-unauthenticated
		apt-get upgrade -y --allow-unauthenticated
	fi
}

function checkAptget(){
	sayi=`which apt-get | wc -w`
	if [ $sayi -eq 0 ] ; then
		ehco "apt-get is not found."
	else
		aptIsInstalled=1
		echo -e "apt-get seems to be installed on your system.\n"
	fi
}

function nginxOff(){
	NGINXSTAT=$(sudo service nginx status | grep "not running")
	if [ -z "$NGINXSTAT" ]; then
		# nginx is running
		# stop it
		service nginx stop
	fi
	
	# Disable nginx --- apache is the default
	update-rc.d nginx disable
}

function fixEHCPPerms(){ # by earnolmartin@gmail.com
	# Secure ehcp files
	chown -R root:root /var/www/new/ehcp
	chmod -R 755 /var/www/new/ehcp/

	# Make default index readable
	chmod 755 /var/www/new/index.html
	
	# Set proper permissions on vhosts
	chown vsftpd:www-data -R /var/www/vhosts/
	chmod 0755 -R /var/www/vhosts/
	
	# Secure webmail
	chown root:www-data -R /var/www/new/ehcp/webmail
	chmod 754 -R /var/www/new/ehcp/webmail
	chmod -R 774 /var/www/new/ehcp/webmail/data
}

function logDirFix(){ # by earnolmartin@gmail.com
	chmod 755 /var/www/new/ehcp/log
	chmod 744 /var/www/new/ehcp/log/ehcp_failed_authentication.log
	chown vsftpd:www-data /var/www/new/ehcp/log/ehcp_failed_authentication.log
}

function nginxUpdateFiles(){ # by earnolmartin@gmail.com
	if [ -e "/etc/nginx/sites-enabled/default" ]; then
		# Make backups of originals just in case
		CurDate=$(date +%Y_%m_%d_%s)
		cp "/etc/nginx/sites-enabled/default" "$NGINXBACKUPDIR/default_backup_$CurDate"
	
		# Update configuration
		cp "/var/www/new/ehcp/etc/nginx/default.nginx" "/etc/nginx/sites-enabled/default"
	fi
	
	if [ -e "/etc/nginx/nginx.conf" ]; then
		# Make backups of originals just in case
		CurDate=$(date +%Y_%m_%d_%s)
		cp "/etc/nginx/nginx.conf" "$NGINXBACKUPDIR/nginx.conf_backup_$CurDate"
		
		# Update configuration
		cp "/var/www/new/ehcp/etc/nginx/nginx.conf" "/etc/nginx/nginx.conf"
	fi
}

function CheckPreReqs(){
	aptgetInstall nginx
	aptgetInstall php5-fpm
}

function addConfDFolder(){
	
	# If the conf.d folder doesn't exist, we must create it!
	if [ -e "/etc/apache2/conf.d" ]; then
		mkdir -p "/etc/apache2/conf.d"
	fi
	
}

function ApacheLoadConfDFolder(){
	if [ -e "/etc/apache2/apache2.conf" ]; then
		APACHECONFCONTENTS=$(cat "/etc/apache2/apache2.conf" | grep "IncludeOptional conf.d")
		if [ -z "$APACHECONFCONTENTS" ]; then
			echo "IncludeOptional conf.d/*" >> "/etc/apache2/apache2.conf"
		fi
	fi
}

function killAllMySQLAndRestart(){
	# Stop service
	service mysql stop
		
	# Get each PID of mysqld and kill it --- random bug occurs sometimes after install
	ps -ef | grep mysqld | while read mysqlProcess ; do kill -9  $(echo $mysqlProcess | awk '{ print $2 }') ; done
		
	# Restart the service
	service mysql restart
}

function secureApache(){
	APACHE2Conf="/etc/apache2/apache2.conf"
	if [ -e "$APACHE2Conf" ]; then
		containsDef=$(cat "$APACHE2Conf" | grep "<Directory /var/www/>")
		if [ ! -z "$containsDef" ]; then
			sed -i "s/Options Indexes FollowSymLinks/Options -Indexes +FollowSymLinks/g" "$APACHE2Conf"
		else
			containsCorrectIndexs=$(cat "$APACHE2Conf" | grep "Options -Indexes +FollowSymLinks")
			if [ -z "$containsCorrectIndexs" ]; then
				echo "Options -Indexes +FollowSymLinks" >> "$APACHE2Conf"
			fi
		fi
	fi
}

function installExtras(){
	echo ""
	echo -n "Install extra software if it is not already installed (such as Amavis, SpamAssassin, ClamAV)? [y/n]: "
	read insMode
	
	insMode=$(echo "$insMode" | awk '{print tolower($0)}')
	
	if [ "$insMode" != "n" ]; then
		installAntiSpam
	fi
}

function installAntiSpam(){
	
	# Postfix must be installed
	CURDIR=$(pwd)	
	ANTISPAMINSTALLED=$(which "spamassassin")
	POSTFIXInstalled=$(which "postfix")
	postFixUserExists=$(grep postfix /etc/passwd)
	if [ ! -z "$POSTFIXInstalled" ] && [ ! -z "$postFixUserExists" ]; then
		# SpamAssassin is not installed / configured
		# Lets roll
		# Set variables
		SPConfig="/etc/default/spamassassin"
		PHeadChecks="/etc/postfix/header_checks"
		PostFixConf="/etc/postfix/main.cf"
		PostFixMaster="/etc/postfix/master.cf"
		CONTENTFILTER="/etc/amavis/conf.d/15-content_filter_mode"
		SPAMASSASSCONF="/etc/spamassassin/local.cf"
		AMAVISHOST="/etc/amavis/conf.d/05-node_id"
		
		# Install Anti-Spam Software
		aptgetInstall "amavisd-new spamassassin clamav-daemon"
		
		# Install individually incase some packages are not found
		aptgetInstall libnet-dns-perl
		aptgetInstall pyzor
		aptgetInstall razor
		aptgetInstall arj
		aptgetInstall bzip2
		aptgetInstall cabextract
		aptgetInstall cpio
		aptgetInstall file
		aptgetInstall gzip
		aptgetInstall lha
		aptgetInstall nomarch
		aptgetInstall pax
		aptgetInstall rar
		aptgetInstall unrar
		aptgetInstall unzip
		aptgetInstall zip
		aptgetInstall zoo
		aptgetInstall unzoo
		
		# Only keep going if we have the basic packages installed
		AMAVISINS=$(which amavisd-new)
		SPAMASSASSINS=$(which spamassassin)
				
		if [ ! -z "$AMAVISINS" ] && [ ! -z "$SPAMASSASSINS" ]; then
		
			# Add Users
			adduser clamav amavis
			adduser amavis clamav
			
			# Enable SpamAssassin
			if [ -e "$SPConfig" ]; then
				sed -i "s#ENABLED=.*#ENABLED=1#g" "$SPConfig"
				sed -i "s#CRON=.*#CRON=1#g" "$SPConfig"
				
				# More settings
				if [ -e "$SPAMASSASSCONF" ]; then
					# Rewrite the header
					sed -i "s/#rewrite_header.*/rewrite_header Subject \*\*\*\*\*SPAM\*\*\*\*\*/g" "$SPAMASSASSCONF"
					sed -i "s/# rewrite_header.*/rewrite_header Subject \*\*\*\*\*SPAM\*\*\*\*\*/g" "$SPAMASSASSCONF"
					sed -i "s#rewrite_header.*#rewrite_header Subject \*\*\*\*\*SPAM\*\*\*\*\*#g" "$SPAMASSASSCONF"
					
					# Set the spam score
					sed -i "s/#required_score.*/required_score 12.0/g" "$SPAMASSASSCONF"
					sed -i "s/# required_score.*/required_score 12.0/g" "$SPAMASSASSCONF"
					sed -i "s#required_score.*#required_score 12.0#g" "$SPAMASSASSCONF"
						
					# use bayes 1
					sed -i "s/#use_bayes.*/use_bayes 1/g" "$SPAMASSASSCONF"
					sed -i "s/# use_bayes.*/use_bayes 1/g" "$SPAMASSASSCONF"
					sed -i "s#use_bayes.*#use_bayes 1#g" "$SPAMASSASSCONF"
						
					# use bayes auto learn
					sed -i "s/#bayes_auto_learn.*/bayes_auto_learn 1/g" "$SPAMASSASSCONF"
					sed -i "s/# bayes_auto_learn.*/bayes_auto_learn 1/g" "$SPAMASSASSCONF"
					sed -i "s#bayes_auto_learn.*#bayes_auto_learn 1#g" "$SPAMASSASSCONF"
						
				fi
					
				service spamassassin restart
			fi
				
			# Integrate into postfix
			postconf -e "content_filter = smtp-amavis:[127.0.0.1]:10024"
				
			echo "use strict;

# You can modify this file to re-enable SPAM checking through spamassassin
# and to re-enable antivirus checking.

#
# Default antivirus checking mode
# Uncomment the two lines below to enable it
#

@bypass_virus_checks_maps = (
	\%bypass_virus_checks, \@bypass_virus_checks_acl, \$bypass_virus_checks_re);


#
# Default SPAM checking mode
# Uncomment the two lines below to enable it
#

@bypass_spam_checks_maps = (
	\%bypass_spam_checks, \@bypass_spam_checks_acl, \$bypass_spam_checks_re);

1;  # insure a defined return" > "$CONTENTFILTER"
			if [ -e "$PostFixMaster" ]; then
				POSTFIXMASCHECK1=$(cat "$PostFixMaster" | grep "smtp-amavis")
				if [ -z "$POSTFIXMASCHECK1" ]; then
						echo "smtp-amavis     unix    -       -       -       -       2       smtp
		-o smtp_data_done_timeout=1200
		-o smtp_send_xforward_command=yes
		-o disable_dns_lookups=yes
		-o max_use=20" >> "$PostFixMaster"
				fi
					
				POSTFIXMASCHECK2=$(cat "$PostFixMaster" | grep "127.0.0.1:10025")
				if [ -z "$POSTFIXMASCHECK2" ]; then
					echo "
127.0.0.1:10025 inet    n       -       -       -       -       smtpd
		-o content_filter=
		-o local_recipient_maps=
		-o relay_recipient_maps=
		-o smtpd_restriction_classes=
		-o smtpd_delay_reject=no
		-o smtpd_client_restrictions=permit_mynetworks,reject
		-o smtpd_helo_restrictions=
		-o smtpd_sender_restrictions=
		-o smtpd_recipient_restrictions=permit_mynetworks,reject
		-o smtpd_data_restrictions=reject_unauth_pipelining
		-o smtpd_end_of_data_restrictions=
		-o mynetworks=127.0.0.0/8
		-o smtpd_error_sleep_time=0
		-o smtpd_soft_error_limit=1001
		-o smtpd_hard_error_limit=1000
		-o smtpd_client_connection_count_limit=0
		-o smtpd_client_connection_rate_limit=0
		-o receive_override_options=no_header_body_checks,no_unknown_recipient_checks" >> "$PostFixMaster"
				fi
		
			fi
		
			#http://stackoverflow.com/questions/11694980/using-sed-insert-a-line-below-or-above-the-pattern
			POSTFIXMASCHECK3=$(cat "$PostFixMaster" | grep -A2 "pickup" | grep -v "pickup" | grep -o "\-o receive_override_options=no_header_body_checks$")
			if [ -z "$POSTFIXMASCHECK3" ]; then
				sed -i "/pickup.*/a\\\t-o receive_override_options=no_header_body_checks" "$PostFixMaster"
			fi
				
			POSTFIXMASCHECK4=$(cat "$PostFixMaster" | grep -A2 'pickup' | grep -v "pickup" | grep -o "\-o content_filter=$")
			if [ -z "$POSTFIXMASCHECK4" ]; then
				sed -i "/pickup.*/a\\\t-o content_filter=" "$PostFixMaster"
			fi
				
			# Prompt for FQDN
			echo ""
			echo -n "Please enter your Fully Qualified Domain Name (FQDN) for this mail server: "
			read FQDNName
			FQDNName=$(echo "$FQDNName" | awk '{print tolower($0)}')
			if [ -z "$FQDNName" ]; then
				# Just replace it with ehcpforce.tk
				sed -i "s/^#\$myhostname.*/\$myhostname = \"ehcpforce.tk\";/g" "$AMAVISHOST"
				sed -i "s#^\$myhostname.*#\$myhostname = \"ehcpforce.tk\";#g" "$AMAVISHOST"
			else
				sed -i "s/^#\$myhostname.*/\$myhostname = \"$FQDNName\";/g" "$AMAVISHOST"
				sed -i "s#^\$myhostname.*#\$myhostname = \"$FQDNName\";#g" "$AMAVISHOST"
			fi
				
			# Should be good to go?
				
			# Restart Amavis
			service amavis restart
				
			# Restart services
			service postfix restart
			
		fi
	fi
}

function fixSASLAUTH(){
	# Fix SASLAUTH CACHE and limit to 2 threads to prevent memory leaks
	if [ -e "/etc/default/saslauthd" ]; then
		echo "NAME=\"saslauthd\"
START=yes
MECHANISMS=\"pam\"
PARAMS=\"-s 5120 -m /var/spool/postfix/var/run/saslauthd -r\"
OPTIONS=\"-s 5120 -m /var/spool/postfix/var/run/saslauthd -r\"
THREADS=2
" > "/etc/default/saslauthd"
		
		# restart the service
		service saslauthd restart
	fi
}


###############################
###START OF SCRIPT MAIN CODE###
###############################
clear

# Check and see if this script is being executed by root
rootCheck

echo -e "Running EHCP Force Edition Update Script\n"

echo -e "Downloading and installing package updates!\n"
updateBeforeInstall

echo -e "Making Sure nginx and php5-fpm Are Installed\n"
# Checking PreReqs
CheckPreReqs

echo -e "Checking to make sure nginx is disabled!\n"
nginxOff

# Get distro info
echo -e "Retrieving Distribution Information\n"
checkDistro

echo -e "Stopping services\n"
# Stop services
service ehcp stop
service apache2 stop

echo -e "Checking Apache2 EnvVars for Errors\n"
fixApacheEnvVars

echo -e "Changing Apache User\n"
# Change Apache User
changeApacheUser

# Add Nginx Limiting
echo -e "Adding rate limiting for nginx\n"
nginxRateLimit

echo -e "Enabling Slave DNS\n"
# Allow slave DNS:
slaveDNSApparmorFix

echo -e "Retrieving Latest EHCP Force Files and Making a Backup of Original EHCP Force Files\n"
# Get EHCP Files
getLatestEHCPFiles

echo -e "Updating the EHCP Daemon\n"
# Update EHCP Daemon
updateDaemon

echo -e "Installing Fail2Ban\n"
# Install Fail2Ban
fail2ban

echo -e "Installing Apache2 Security Modules\n"
# Install Apache2 Security Modules
apacheSecurity

echo -e "Running MySQL Bind Address Fix\n"
# Fix MySQL Bind Address
mysqlUseLocalHost

echo -e "Updating Base nginx Configuration Files\n"
# Update nginx configuration files
nginxUpdateFiles

echo -e "Checking for VSFTPD Updates\n"
# Check for VSFTPD Updates
ubuntuVSFTPDFix

echo -e "Checking for Generic Fixes Depending on Ubuntu Version\n"
# Check for VSFTPD Updates
genUbuntuFixes

echo -e "Making Apache more secure by not listing files within a folder without an index page.\n"
# Make it so that strangers can't just browse folders without an index file
secureApache

echo -e "Restarting web services and synchronizing domains!\n"
# Start the services and sync domains
finalize

echo -e "Disabling BIND Recursion\n"
# Disable Bind Recursion:
disableRecursiveBIND

echo -e "Fixing SASLAuth caching and setting maximum number of threads to 2.\n"
# Prevent SASLAuth memory leaks:
fixSASLAUTH

echo -e "Presenting Additional User Options\n"
# Install extra software if users want it:
installExtras

echo -e "\nSuccessfully updated EHCP Force Edition to the latest snapshot!"
