#!/bin/bash

function safe_copy(){
	php -l $1
	if [ $? -eq 0 ] ; then
		cp $1 /var/www/new/ehcp/
		echo "$1 kopyalama basarili"
	else
		echo
		echo "HATA OLUSTU....."
		echo
		exit 1
	fi
}

safe_copy classapp.php
safe_copy localutils.php

> /var/log/ehcp.log
/etc/init.d/ehcp restart & tail -f /var/log/ehcp.log
/etc/init.d/apache2 restart

