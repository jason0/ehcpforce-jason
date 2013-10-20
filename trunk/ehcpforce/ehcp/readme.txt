This file may be old. have a look at www.ehpc.net for more recent info.


prerequisities:
only clean debian/ubuntu (apt-get) compatible linux system.


to install copy and paste following on your linux console:

wget http://www.ehcp.net/download
tar -zxvf ehcp_latest.tgz
cd ehcp
./install.sh



after installation, 
panel username: admin,
pass: whatever you set in install. 




Running ehcp:
------------------------
after you finish ehcp, it should already run as a daemon.
if daemon is not run somehow, 
you may execute 
./ehcpdaemon.sh 
in where you installed ehcp.

this will start the program in daemon mod.

the web interface will at the web server where you installed your files.
for local computer, call
http://localhost/
and you will see your login dialog.
admin account: admin

after logged in, you may add/delete users, as well as domains/email users etc.

soma files such as ehcp_postfix.sh, ehcp_postfix2.sh, install_old.sh and so, are not used anymore, only left for historical purposes.


send any comments/questions to: info@ehcp.net
msn/email:bvidinli@iyibirisi.com

Hope, you will find ehcp useful.

have a look at www.ehcp.net for ore info..

Thanks
ehcp developer