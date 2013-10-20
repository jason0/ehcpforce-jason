#!/bin/bash
# checks apache and warns you if it does not run...

# replace your email here:
myemail="bvidinli@gmail.com";


apachecount=`ps aux | grep -v grep | grep apache | wc -l`
if [ $apachecount -lt 3 ] ; then
	echo "*** your apache is not working...trying to restart `date`"
	echo "*** your apache is not working...trying to restart `date`" | sendmail $myemail
	/etc/init.d/apache2 restart
	
	
else 
	echo "`date` your apache is normal..: $apachecount"

fi
