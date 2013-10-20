put your images/styles under images directory in ehcp dir, 
typically /var/www/vhosts/ehcp/images/

do not place images/styles inside this templates folder.. 
this folder is only for html files.. 

the ehcp script runs from inside ehcp/ folder, so, all refs should be with respect to that root folder..

for instance, to put an image x.gif in your html, put image in ehcp/images/ folder,
then in these html: <img src='images/x.gif'>
 

