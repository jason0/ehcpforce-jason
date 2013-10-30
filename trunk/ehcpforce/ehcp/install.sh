#!/bin/bash
# ehcp - Easy Hosting Control Panel install/remove by info@ehcp.net (actually, no remove yet)
# this is a very basic shell installer, real installation in install_lib.php, which is called by install_1.php, install_2.php
#
# please contact me if you made any modifications.. or you need help
# msn/email: info@ehcp.net
# skype/yahoo/gtalk: bvidinli

# Marcel <marcelbutucea@gmail.com>
#	   - added initial support for yum (RedHat/CentOS)
#	   - some code ordering, documentation and cleanup
#
# by earnolmartin@gmail.com : many fixes, 

########################
# FORK INFORMATION     #
########################
# Fork created by earnolmartin@gmail.com
# http://ehcpforce.tk
# This forked version of EHCP offers new functionality and features (custom FTP accounts, proper web user permissions so that PHP scripts work as intended, and more) 
# that were not previously available in the main EHCP release.
# Also, this forked version is updated on a regular basis when a new version of Ubuntu comes out.
# You should run this fork on Ubuntu.  It will work perfectly on all supported Ubuntu versions (The ones Ubuntu officially supports such as the LTS release and latested versions).
# Debian should work as well, but if it doesn't, please let me know. 
# Based on Yeni EHCP Release
# Source code available via SVN so that multiple people can develop and track changes!
# Does not add a EHCP reseller account by default without your knowledge ensuring security
# Main Web Panel GUI theme is Ep-Ic V2 (theme I custom developed which links to all of the new functionality)
# Other themes may not have the latest and greatest operations available

ehcpversion="0.35.2"

echo
echo 
#echo "Beginning EHCP FoRcE Version Installation"
echo
echo 
chmod -Rf a+r *

for j in "noapt" "unattended" "light" ; # read some install parameters.
do
	if [ "$1" == "$j" -o "$2" == "$j" -o "$3" == "$j" ] ; then
		eval $j=$j  # set parameter noapt to that string... and so for others.
		echo "Parameter $j is set:(${!j}) "
	fi
done

if [ "$1" == "-y" -o "$2" == "-y" -o "$3" == "-y" ] ; then
	unattended="unattended"
fi

################################################################################################
# Function Definitions																		 #
################################################################################################

# Stub function for apt-get

function installaptget () {
	echo "now let's try to install apt-get on your system."
	echo "Not yet implemented"
	exit
}

# Stub function fot yum

function installyum () {
	echo "now let's try to install yum on your system."
	echo "Not yet implemented"
}

# Initial Welcome Screen

function ehcpHeader() {
	echo 
	echo
	echo "STAGE 1"
	echo "====================================================================="
	echo
	echo "--------------------EHCP PRE-INSTALLER $ehcpversion -------------------------"
	echo "-----Easy Hosting Control Panel for Ubuntu, Debian, and Alikes-------"
	echo "---------------------- FoRcE Edition (a fork) -----------------------"
	echo "---------------------- http://www.ehcpforce.tk ----------------------"
	echo "-------------------------Non-Fork Version:  www.ehcp.net-------------"
	echo "---------------------------------------------------------------------"
	echo
	echo 
	echo "Now, ehcp pre-installer begins, a series of operations will be performed and main installer will be invoked. "
	echo "if any problem occurs, refer to www.ehcp.net forum section, or contact me, mail/msn: info@ehcp.net"
	
	echo "Please be patient, press enter to continue"
	if [ "$unattended" == "" ] ; then
		read
	fi

	echo
	echo "Note that ehcp can only be installed automatically on Debian based Linux OS'es or Linux'es with apt-get enabled..(Ubuntu, Kubuntu, debian and so on) Do not try to install ehcp with this installer on redhat, centos and non-debian Linux's... To use ehcp on no-debian systems, you need to manually install.. "
	echo "this installer is for installing onto a clean, newly installed Ubuntu/Debian. If you install it on existing system, some existing packages will be removed after prompting, if they conflict with packages that are used in ehcp, so, be careful to answer yes/no when using in non-new system"
	echo "Actually, I dont like saying like, 'No warranty, I cannot be responsible for any damage.... ', But, this is just a utility.. use at your own."
	echo "ehcp also sends some usage data to developer for statistical purposes"
	echo "press enter to continue"
	if [ "$unattended" == "" ] ; then
		read
	fi
}

# Check for yum

function checkyum () {
	which yum > /dev/null 2>&1
	if [ "$?" == "0"  ]; then
		echo "yum is available"
		return 0
	else
		# This should never happen
		echo "Please install yum"
		installyum
	fi
}

# Check for apt-get

function checkAptget(){

	sayi=`which apt-get | wc -w`
	if [ $sayi -eq 0 ] ; then
		ehco "apt-get is not found."
		installaptget
	fi

	echo "apt-get seems to be installed on your system."
	aptIsInstalled=1

	sayi=`grep -v "#" /etc/apt/sources.list | wc -l`

	if [ $sayi -lt 10 ] ; then
		echo
		echo "WARNING ! Your /etc/apt/sources.list  file contains very few sources, This may cause problems installing some packages.. see http://www.ehcp.net/?q=node/389 for an example file"
		echo "This may be normal for some versions of debian"
		echo "press enter to continue or Ctrl-C to cancel and fix that file"
		if [ "$unattended" == "" ] ; then
			read
		fi
	fi

}


# Retrieve statistics
# Marcel: This freezed the installer on one of my Centos Servers (needs more investigating)
# bvidinli:answer: this infomail may be disabled, only for statistical purposes... may hang if for 10 second if user is not connected to internet, or something is wrong with wget or dns resolution...
# no hanging longer than 10 sec should occur... i think.. btw, your code is perfect, Marcel

function infoMail(){
	ip=`ifconfig | grep "inet addr" | grep -v "127.0.0.1" | awk '{print $2}' `
	wget -q -O /dev/null --timeout=10 http://www.iyibirisi.com/diger/msg.php?msg=$1.$ip > /dev/null 2>&1 &
	# echo "(infoMail) your ip is: $ip"
}

# Function to be called when installing packages, by Marcel <marcelbutucea@gmail.com>

function installPack(){
	
	if [ -n "$noapt" ] ; then  # skip install
		echo "skipping apt-get install for:$1"
		return
	fi
	
	if [ $distro == "ubuntu" ] || [ $distro == "debian" ];then
		# first, try to install without any prompt, then if anything goes wrong, normal install..
		apt-get -y --no-remove --allow-unauthenticated install $1
		if [ $? -ne 0 ]; then
				apt-get --allow-unauthenticated install $1
		fi
	else
		# Yum is nice, you don't get prompted :)
		yum -y -t install $1
	fi
}

function logToFile(){
	logfile="ehcp-apt-get-install.log"
	echo "$1" >> $logfile
}

function aptget_Update(){
	if [ -n "$noapt" ] ; then  # skip install
		echo "skipping apt-get update"
		return
	fi

	apt-get update
}

function aptgetInstall(){

	if [ -n "$noapt" ] ; then  # skip install
		echo "skipping apt-get install for:$1"
		return
	fi

	# first, try to install without any prompt, then if anything goes wrong, normal install..
	cmd="apt-get -y --no-remove --allow-unauthenticated install $1"
	logToFile "$cmd"
	$cmd
	
	if [ $? -ne 0 ]; then
		cmd="apt-get --allow-unauthenticated install $1"
		logToFile "$cmd"
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
	logToFile "$cmd"
	$cmd
	
	if [ $? -ne 0 ]; then
		cmd="apt-get remove $1"
		logToFile "$cmd"
		$cmd	
	fi 
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

# Check if the running user is root, if not restart with sudo
function checkUser() {
		if [ `whoami` != "root" ];then
				echo "you are $who, you have to be root to use ehcp installation program.  switching to root mode, please enter password  or re-run install.sh as root"
				sudo $0 # restart this with superuser-root privileges				
				exit
		fi
}

# Function to kill any running ehcp / php daemons
function killallEhcp() {
		for i in `ps aux | grep ehcpdaemon.sh | grep -v grep | awk -F " " '{ print $2 }'`;do
				kill -9 $i
		done

		for i in `ps aux | grep 'php index.php' | grep -v grep | awk -F " " '{ print $2 }'`;do
				kill -9 $i
		done
}


function checkPhp(){
	which php
	if [ $? -eq 0 ] ; then
		echo "php seems installed. This is good.."
	else
		echo "PHP IS STILL NOT INSTALLED. THIS IS A SERIOUS PROBLEM.  MOST PROBABLY, YOU WILL NOT BE ABLE TO CONTINUE. TRY TO INSTLL PHP yourself."
		echo "if rest of install is successfull, then, this is a false alarm, just ignore"
	fi
}

function launchPanel(){
	# NEVER LAUNCH FIREFOX AS THE ROOT USER.  IT WILL MESS IT UP FOR THE NORMAL USER
	if [ ! -z "$SUDO_USER" ] && [ "$SUDO_USER" != "root" ]; then
		echo 
		echo "The EHCP panel is now accessible!"
		echo "Your panel administrative login is: admin"
		echo "Attempting to load the control panel via web browser from the local machine."
		sudo -u "$SUDO_USER" sensible-browser
	fi
}

# Thanks a lot to  earnolmartin@gmail.com for fail2ban integration & vsftpd fixes.

function slaveDNSApparmorFix(){ # by earnolmartin@gmail.com
	if [ -e /etc/apparmor.d/usr.sbin.named ]; then
				echo -e "\nChanging bind apparmor rule to allow master DNS synchronization for slave setups.\n"
				sed -i 's#/etc/bind/\*\* r,#/etc/bind/\*\* rw,#g' /etc/apparmor.d/usr.sbin.named
	fi
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
			if [ "$yrelease" == "13" ] ; then
				if [ "$mrelease" == "10" ]; then
					fixApacheDefault
					removeNameVirtualHost
				fi
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

function logDirFix(){ # by earnolmartin@gmail.com
	chmod 755 log
	chmod 744 log/ehcp_failed_authentication.log
	chown vsftpd:www-data log/ehcp_failed_authentication.log
}

function fixBINDPerms(){ # by earnolmartin@gmail.com
	chmod -R 774 /etc/bind
}

function fixEHCPPerms(){ # by earnolmartin@gmail.com
	chmod a+rx /var/www/new/ehcp/
	chmod -R a+r /var/www/new/ehcp/
	find ./ -type d -exec chmod a+rx {} \;
	chown -R vsftpd:www-data /var/www/new/ehcp/webmail
	chmod 755 -R /var/www/new/ehcp/webmail
	chmod 755 /var/www/new/index.html
}

function fixPHPConfig(){ # by earnolmartin@gmail.com
	PHPConfFile="/etc/php5/cli/php.ini"
	if [ -e $PHPConfFile ]; then
		PHPConfCheck=$( cat $PHPConfFile | grep -o ";extension=mysql.so" )
		if [ -z "$PHPConfCheck" ]; then 
			sed -i "s/extension=mysql.so/;extension=mysql.so/g" $PHPConfFile
			service apache2 restart
		fi
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
		service apache2 restart
	fi
}

function updateBeforeInstall(){ # by earnolmartin@gmail.com
	# Update packages before installing to avoid errors
	
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

function restartDaemons(){ # by earnolmartin@gmail.com
	# Restart the EHCP daemon after installation is completed
	service ehcp restart
	
	# Restart MySQL service after installation is completed
	service mysql restart
}

#############################################################
# End Functions & Start Install							 #
#############################################################
installdir=$(pwd)
if [ ! -f $installdir/install.sh ] ; then
	echo "install.sh is not in install dir. Run install.sh from within ehcp installation dir."
	exit 1
fi

#echo "`date`: initializing.b"  # i added these echo's because on some system, some stages are very slow. i need to investigate that.
#infoMail "ehcp_1_installstarted_ver_$ehcpversion"
checkUser
ehcpHeader
/etc/init.d/apparmor stop & > /dev/null  # apparmor causes many problems..
/etc/init.d/apparmor teardown & > /dev/null  # apparmor causes many problems..if anybody knows a better solution, let us know.
checkDistro
killallEhcp

if [ -z "$distro" ] ; then
	echo "Your system type could not be detected or detected to be ($distro), You may not install ehcp automatically on this system, anyway, to continue press enter"
	read
fi

checkAptget

#Update the system before installation
#If your package information is out of date, MySQL and others may fail to install
updateBeforeInstall

#----------------------start some install --------------------------------------
#echo "`date`: initializing.g"
mkdir /etc/ehcp

aptget_Update

aptgetInstall python-software-properties # for add-apt-repository

# apt-get upgrade  # may be cancelled later... this may be dangerous... server owner should do it manually...
aptgetInstall php5 
aptgetInstall php5-mysql 
aptgetInstall php5-cli 
aptgetInstall sudo
aptgetInstall wget
aptgetInstall aptitude

# This is needed to provide a default answer for configuring certain packages such as mysql and phpmyadmin 
if [ ! -z "$unattended" ]; then
	aptgetInstall debconf-utils
fi


#outside_ip=`echo "" > ip ; wget -q -O ip http://ehcp.net/diger/myip.php ; echo ip`
#rm -f ip

echo
echo

checkPhp
echo
echo
echo
echo "STAGE 2"
echo "====================================================================="
echo "now running install_1.php "
#infoMail ehcp_2_install-starting-install_1.php
php install_1.php $version $distro $noapt $unattended $light

echo 
echo 
echo "STAGE 3"
echo "====================================================================="
echo "now running install_2.php "
#infoMail ehcp_2_2_install-starting-install_2.php

#Send version to avoid installing nginx on Ubuntu 12.10 --- there is a bug and it's not supported
#php install_2.php $noapt || php /etc/ehcp/install_2.php $noapt  # start install_2.php if first install is successfull at php level. to prevent many errors.
php install_2.php $version $distro $noapt $unattended $light

# Post Install Functions by Eric Arnol-Martin

mv /var/www/new/ehcp/install_?.php /etc/ehcp/   # move it, to prevent later unauthorized access of installer from web
cd "/var/www/new/ehcp"
# Run VSFTPD Fix depending on version
ubuntuVSFTPDFix
# Run SlaveDNS Fix So that DNS Zones can be transfered
slaveDNSApparmorFix
# Run log chmod fix
logDirFix
# Configure Fail2Ban for EHCP if Fail2Ban is present and configured
# fail2banCheck # done in install*php files.
# Fix EHCP Permissions
fixEHCPPerms
# Fix extra mysql module getting loaded in the PHP config printing warning messages
fixPHPConfig
# Fix /etc/bind directory permissions required for slave dns
fixBINDPerms
# Change Apache user to vsftpd to ensure chmod works via PHP and through FTP Clients
changeApacheUser
# Fix generic problems in Ubuntu
genUbuntuFixes
# Restart neccessary daemons
restartDaemons

# Launch firefox and the panel
##############################################
launchPanel
infoMail "ehcp_8_install-finished-install.sh_ver_$ehcpversion.$outside_ip"
echo "Initializing the EHCP Daemon"
cd /var/log
/etc/init.d/ehcp restart
echo "EHCP restart complete."
sleep 5 # to let ehcp log fill a little

# you may disable following lines, these are for debug/check purposes.
ps aux > debug.txt
echo "============================================"  >> debug.txt
tail -100 /var/log/syslog >> debug.txt
tail -100 /var/log/ehcp.log >> debug.txt
cat debug.txt | sendmail -s "ehcp installation debug info" info@ehcp.net > /dev/null 2>&1


echo "Thank you for installing the FoRcE Edition (a fork) of EHCP.

The following ports are used by EHCP and must be open:
20,21,22,25,53,80,110,143 (tcp+udp)

Please visit our website for updates, support, and community additions.
http://ehcpforce.tk

Your web hosting control panel can be accessed via http://yourip/

Enjoy!"
