#!/bin/bash
# EHCP Force Edition Pre-Installer Script
# www.ehcpforce.tk
# by earnolmartin@gmail.com

###################
#### FUNCTIONS ####
###################

function outputInfo(){
	clear
	echo "EHCP Force Edition Pre-Install Script"
	echo "Version 1.0"
	echo -e "By earnolmartin\n\n"
	echo "This pre-installation script allows you to select your EHCP Force installation mode."
	echo -e "For example, you can choose to install EHCP with extra software (Email Anti-Spam, etc).\n"
}

function generateEHCPPreInstallFile(){
  logInfoFile="/root/ehcp_info"
  if [ -e "$logInfoFile" ]; then
	CurDate=$(date +%Y_%m_%d_%s)
	mv "$logInfoFile" "/root/ehcp_info_$CurDate"
  else
	touch "$logInfoFile"
  fi
  
  generatePassword "15"
  MYSQLROOTPASS="$rPass"
  
  generatePassword
  PHPMYADMINPASS="$rPass"
  
  generatePassword
  RCUBEPASS="$rPass"
  
  generatePassword
  EHCPDBPASS="$rPass"
  
  generatePassword "20"
  EHCPADMINPASS="$rPass"
  
  echo -e "<?php\n\$mysql_root_pass=\"$MYSQLROOTPASS\";\n\$php_myadmin_pass=\"$PHPMYADMINPASS\";\n\$rcube_pass=\"$RCUBEPASS\";\n\$ehcp_mysql_pass=\"$EHCPDBPASS\";\n\$ehcp_admin_password=\"$EHCPADMINPASS\";\n?>" > "install_silently.php"
  
  echo -e "\nMySQL root user password = $MYSQLROOTPASS"
  echo "MySQL root user password = $MYSQLROOTPASS" >> "$logInfoFile"
  echo "PHPMyAdmin MySQL user password = $PHPMYADMINPASS"
  echo "PHPMyAdmin MySQL user password = $PHPMYADMINPASS" >> "$logInfoFile"
  echo "Roundcube MySQL user password = $RCUBEPASS"
  echo "Roundcube MySQL user password = $RCUBEPASS" >> "$logInfoFile"
  echo "EHCP MySQL user password = $EHCPDBPASS"
  echo "EHCP MySQL user password = $EHCPDBPASS" >> "$logInfoFile"
  echo "EHCP Admin Password = $EHCPADMINPASS"
  echo "EHCP Admin Password = $EHCPADMINPASS" >> "$logInfoFile"
  echo -e "\nThis information has been saved in $logInfoFile for you to reference later!"
  sleep 5
}

function generatePassword(){
  if [ ! -z "$1" ]; then
    PLENGTH="$1"
  else
    PLENGTH="10"
  fi
  
  #rPass=$(date +%s | sha256sum | base64 | head -c "$PLENGTH")
  rPass=$(cat /dev/urandom | tr -dc 'a-zA-Z0-9' | fold -w 32 | head -n 1 | head -c "$PLENGTH")
}

function checkRoot(){
	# Make sure only root can run our script
	if [ "$(id -u)" != "0" ]; then
	   echo "This script must be run as root" 1>&2
	   exit 1
	fi
}

###################
#### Main Code ####
###################

# Check for root
checkRoot

# Get parameters
for varCheck in "$@"
do
    if [ "$varCheck" == "unattended" ]; then
		preUnattended=1
    fi
done

if [ ! -z "$preUnattended" ]; then
	# They really want this install to be unattended, so run the installer and use default passwords of 1234
	bash install_main.sh "$@"
else

	# Echo Info
	outputInfo

	# Ready to start?
	echo -n "Install EHCP Force Edition in unattended mode (installs all software without prompts and generates passwords)? [y/n]: "
	read unattended
	unattended=$(echo "$unattended" | awk '{print tolower($0)}')
	
	if [ "$unattended" != "n" ]; then
		generateEHCPPreInstallFile
		unattendedMode="unattended"
	fi
	echo ""
	echo -n "Install extra software in addition to EHCP Force Edition (such as Amavis, SpamAssassin, ClamAV)? [y/n]: "
	read insMode
	insMode=$(echo "$insMode" | awk '{print tolower($0)}')
	
	if [ "$insMode" != "n" ]; then
		extra="extra"
	else
		extra="normal"
	fi
	
	# Run the main installer
	echo "bash install_main.sh $unattendedMode $extra"
	bash install_main.sh $unattendedMode $extra
	
fi
