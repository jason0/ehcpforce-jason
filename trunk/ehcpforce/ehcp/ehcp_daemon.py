#!/usr/bin/env python

import cherrypy
import os,sys
import MySQLdb,datetime


conf={
	'logintable':{			
		'tablename':'panelusers',
		'passwordtype':'md5',
		'usernamefield':'panelusername',
		'passwordfield':'password'
	}
}



def isEmpty(input):
	if input==None:
		return True		
	if input=='':
		return True			
	else:
		return False


class Application:
	# main Application class, for modular design... 
	input=output=''
	filefinished=False
	dbhost=''
	dbname=''
	dbusername=''
	dbuserpassword=''

	activeuser=''
	logedin_username=''


	def zamanBas(self):
		return datetime.datetime.now().strftime("%Y-%m-%d %H:%M:%S")
	
	def checkDuplicateProgram(self):
		pid=os.getpid()
		# bu programin ismini de al.
		progname=commands.getoutput("ps ax | grep -v grep | grep "+pid.__str__()+" | awk '{print $6}'")
		progname=progname.strip()
		progname=progname.split('/').pop()  # path ile beraber yazinca boylece, sondaki programi aliyor. 
		print "Progname:",progname
		
		# ismi ayni olan, ancak pidi farkli baska program var mi ?
		otherprog=commands.getoutput("ps ax | grep -v grep | grep '"+progname+"' | grep -v "+pid.__str__()+" | awk '{print $5 \" \" $6}'")
		otherprog=otherprog.strip()	
		
		if otherprog !='':
			progs=commands.getoutput("ps ax | grep -v grep | grep '"+progname+"'")
			print progs,"\n"
			print "program zaten calisiyor. ",pid," cikiyor..."		
			
			sys.exit()

	
	def checkFileOpen(self,file):		
		# dosyanin acik olup olmadigini ogrenmenin daha pratik bir yolunu simdilik bulamadim.
		# suanda, dosyanin son modify zamanina bakiyor. bir saatten fazladir yazilmiyorsa, ozaman acik degil kabul et... 
		filemodifytime=os.stat(file).st_mtime # in epoch
		now=time.time()
		fark=now-filemodifytime
		if fark>3600:
			return 1 # dosya 1 saatten fazladir yazilmiyor. 
		else:
			return 0
			

	def moveFile(self,src,dst,comment=''):
		if comment!='':
			str="("+comment+") "
		#print  str+"move-rename: ",src,"--->",dst
		os.rename(src,dst)

	def dosyaAdiTemizle(self,dosya):
		dosya=dosya.replace('*','')
		return dosya
	
	def digitOrNumber(self,input,number):
		if input.isdigit():
			res=int(input)
		else:
			res=number
		return res
	
	def readConfig(self):
		global kacproses,bitir,maxproses,cpuustlimit,cpualtlimit
		try:
			conf=ConfigParser()
			conf.read('config.txt')
			#kacproses=self.digitOrNumber(conf.get('isleme','kacproses').strip(),1)
			maxproses=self.digitOrNumber(conf.get('isleme','maxproses').strip(),5)
			cpuustlimit=self.digitOrNumber(conf.get('isleme','cpuustlimit').strip(),500)
			cpualtlimit=self.digitOrNumber(conf.get('isleme','cpualtlimit').strip(),300)			
			
			bitir=conf.get('isleme','bitir').strip()
			
		except:
			print "config okurken hata olustu. varsayilan degerler kullaniliyor."
			kacproses=3
		
	def myprint(self,str):
		global logfile
		print str
		if logfile.closed==False:
			logfile.write(str+"\n")
			logfile.flush()
		
	def getFilename(self,path):
		return path.strip().split('/').pop()		

	def strtemizle(self,str):
		return str.strip(" \n\r")

	def usage(self):
		print "Parametre eksik verildi. "
		print "Kullanim:",sys.argv[0], "dosyaadi \n\n"
		sys.exit(1)

	def initialize(self):
		print "python-> ehcp_daemon.py->Application class olusturuluyor.."
		self.connecttodb()


	def __init__(self):
		global conf
		
		self.initialize()
		self.conf=conf


	def echoln(self,str):
		print "\n"+str

	def echoln2(self,str):
		print "\n"+str+"\n"
		

	def findValueInStr(self,str,find):
	    if str.count(find)<0:
			return ""
	    ret=str[len(find)+1:]
	    return ret
	    

	def readDbConfig(self):
	    print 'reading config...'
	    ehcpdir=''
	    try:
	    	dosya='/etc/ehcp/ehcp.conf'
	    	f=open(dosya)
	    except:
	    	print "Error:dosya acarken hata olustu.",dosya
	    	return
	    	
	    for i in f:
		i=i.strip()
		if i.count('ehcpdir=')>0:		    
		    ehcpdir=self.findValueInStr(i,'ehcpdir')
		    
	    if ehcpdir!='':
		print "found ehcpdir setting:",ehcpdir
		
		try:
			dosya=ehcpdir+'/config.php'
			f=open(dosya)
		except:
			print "Error:dosya acarken hata olustu.",dosya
			return
			
		for i in f:
		    i=i.strip()
		    if i.count('$dbhost')>0:
		    	self.dbhost=self.findValueInStr(i,'$dbhost')[1:-2]
		    if i.count('$dbname')>0:
		    	self.dbname=self.findValueInStr(i,'$dbname')[1:-2]
		    if i.count('$dbusername')>0:
		    	self.dbusername=self.findValueInStr(i,'$dbusername')[1:-2]
		    if i.count('$dbpass')>0:
		    	self.dbuserpassword=self.findValueInStr(i,'$dbpass')[1:-2]
		print "db settings:",self.dbhost,self.dbname,self.dbusername
		    
	    


	def connecttodb(self):		
		self.echoln("connecting to db.. ")
		self.readDbConfig()
		try:
			conn = MySQLdb.connect(self.dbhost, self.dbusername, self.dbuserpassword, self.dbname)
			self.conn=conn.cursor()
			self.connected=True
			self.executequery("SET NAMES 'utf8'")
			print "Baglanti tamam."
		except MySQLdb.Error, e:     
			print "Veritabanina baglanirken hata olustu.",e
		
		
	def query(self,q):
		try:
			self.conn.execute(q)
			return self.conn.fetchall()	
		except:
			print "query islenirken hata olustu:",q
			return 

	def alanal(self,tablo,alan,where):
		q="select "+alan+" from "+tablo+" where "+where+" limit 1"
		#print "\nquery:",q
		res=self.query(q)
		print res
		if res.__len__()>0:
			return res[0][0]
		else:
			return ""
	
	def kayitsayisi(self,tablo,where):
		q="select count(*) as sayi from "+tablo
		if where!='':
			q=q+" where "+where
		res=self.query(q)
		if res==None:
			app.output+="Kayit sayisi alinamadi:"+q
			return 0
		else:
			return res[0][0]
	
	def executequery(self,q):
		try:
			self.conn.execute(q)
			print "query calistirildi:",q
		except:
			print "query islerken hata olustu:",q
		
	

	def error_occured(self,sender):
		self.output+=sender+' An error occured..'
		return False

	def buildinsertsql(self,alanlar):
		# sql alanadi, sql tipi, yerel deger
		if alanlar.__len__()==0:
			print "hic alan belirtilmemis..."
			return 
			
		al='('; deg='values ('
		
		for i in alanlar:
			if i.__len__<3:
				print "Alanlarda eksiklik var: # sql alanadi, sql tipi, yerel deger"
				return
			if al.__len__()>1:
				al=al+','
				deg=deg+','
				
			al=al+i[0]
			deger=i[2]
			
			if i[1]=='int':
				deg=deg+deger.__str__().replace(',','.')
			elif i[1]=='str':
				deger=deger.replace("'",'').replace("\"","").replace("\\",'') # burda ayni zamanda sql injection controlu var bir miktar.
				deg+="'"+deger+"'"
			else:
				print "Bilinmeyen sql tipi, str veya int olmali"
		
		al=al+')'
		deg+=')'
		
		return al+deg
		
	def buildupdatesql(self,alanlar):
		# sql alanadi, sql tipi, yerel deger
		if alanlar.__len__()==0:
			print "hic alan belirtilmemis..."
			return 
			
		upd=''
		
		for i in alanlar:
			if i.__len__<3:
				print "Alanlarda eksiklik var: # sql alanadi, sql tipi, yerel deger"
				return
			if upd.__len__()>1:
				upd+=','
			
			alan=i[0].__str__().replace("'",'').replace("\"","").replace("\\",'')
			deger=i[2].__str__().replace("'",'').replace("\"","").replace("\\",'')
						
			if i[1]=='int':
				upd+=alan+'='+deger
			elif i[1]=='str':
				upd+=alan+"='"+deger+"'"
			else:
				print "Bilinmeyen sql tipi, str veya int olmali"
				
		return upd
		
	def htmlekle(self,id):
		return self.alanal('html','htmlkodu',"id='"+id+"'")
		
		
	def isPasswordOk(self,username,password,usernamefield='',passwordfield=''):
		if (isEmpty(usernamefield)):
			usernamefield=self.conf['logintable']['usernamefield'];
		if (isEmpty(passwordfield)):
			passwordfield=self.conf['logintable']['passwordfield'];
		if (isEmpty(usernamefield)):
			usernamefield='username';
		if (isEmpty(passwordfield)):
			passwordfield='password';
		if (self.conf['logintable']['passwordtype']=='md5'):
			where=""+usernamefield+"='"+username+"' and md5('"+password+"')="+passwordfield+"";
		else:
			where=""+usernamefield+"='"+username+"' and '"+password+"'="+passwordfield+"";
		sayi=self.kayitsayisi(self.conf['logintable']['tablename'],where);
		if (sayi==False):
			#echo "<hr>buraya geldiii..</hr>";
			self.error_occured("dologin2");
			return False;
		if (sayi==0):
			return False;
		elif (sayi>0):
			return True;
			
	def doLogin2(self,username,password):
		if self.isPasswordOk(username,password):
			self.logedin_username=username; # burdaki logedin_username app classinin.
			self.islogedin=True;
			return True
		else:
			self.logedin_username='';
			self.islogedin=False;
			return False


			

app = Application()
print "\n ehcp-python daemon starting... \n This is ehcp_daemon.py, An experimental daemon backend for ehcp, written in python, \n To run real/current ehcp daemon, run '/etc/init.d/ehcp start' command instead \n You may connect to http://yourip:8080 to see this server backend.."



class OnePage(object):
	def index(self,username='',password=''):
		return "one page!, userpass:",username,password
	index.exposed = True
	
class twoPage(object):
	def index(self,a='5'):
		return "two page!"+a
	index.exposed = True

 
class HelloWorld(object):
	activeuser=''
	logedin_username=''
	sayi=0	
	

	def index(self,displayBottom=True):		
		out='Hi, this is ehcp-python background proses, under development now... you will find usefull tools here soon... <br>check back later... <br>on future versions of ehcp <br>'
		out+='<b>Planned tools here:</b><br> server stats, apache reset/restart in case a configuration failure in apache... <br>'
		out+="<hr>Login to ehcp backend using admin password:<br><form action=login method=post>username: <input type=text name=username><br>password: <input type=password name=password><br><input type=submit></form>"
		if displayBottom: out+=self.bottom()
		
		print "ehcp index (user:%s)\n"  % (self.logedin_username)

		return out
		
	index.exposed = True
	
	def bottom(self):
		self.sayi+=1
		return "<br><a href='/mainMenu'>Home/Anamenu</a> - \
		<a href='/operations?op=logout'>logout</a> - \
		<a href='/index'>login</a><br>\
		<hr>This is a very basic controlling way for your ehcp, in case you cannot reach your webserver otherwise.(activeuser:%s) sayi:%s\n"  % (self.logedin_username,self.sayi)
	
	def checkLogin(self):
		if self.logedin_username=='':
			print "not loged in"
			return False
		else:
			return True
		
	
	def mainMenu(self):
		if not self.checkLogin():
			return self.index()

		ret="<br>Mini Main Menu:<br>\
		<a href='/operations?op=dofixapacheconfignonssl'>fixapacheconfignonssl (rebuild apache config with ssl disabled, repairs many things)</a><br>\
		<a href='/operations?op=dosyncdomains'>sync Domains</a><br>"+self.bottom()
		return ret
		
	mainMenu.exposed=True
	
	def secureString(self,str):
		return str.replace("'",'').replace('%','').replace("\\",'')
	
	def login(self,username='',password=''):
		username=self.secureString(username)
		password=self.secureString(password)
		
		if app.doLogin2(username,password):
			self.logedin_username=username
			self.activeuser=True
			print "ehcp login success (user:%s)\n"  % (self.logedin_username)
			ret="<br>pass OK. you logged in. <br> "+self.mainMenu()
		else:
			self.logedin_username=''
			self.activeuser=False

			print "ehcp login fail (user:%s)\n"  % (self.logedin_username)
			ret="<br>yanlis sifre, <a href=/>tekrar deneyiniz.. </a> "
			
		return " userpass:",username,ret
	
	login.exposed = True
	
	def default(self,args):
		return "yanlis url girdiniz:  ",args

	default.exposed=True

	def operations(self,op):
		if not self.checkLogin():
			return self.index()
			
		msg=''
		if op=="dosyncdomains":
			self.addDaemonOp('syncdomains','','','')
		elif op=='logout':
			self.logedin_username=''
			self.activeuser=''
			msg+="<b>Logout complete </b><br>"+self.index(False)
		elif op=='dofixapacheconfignonssl':
			self.addDaemonOp('fixapacheconfignonssl','','','')
		else:
			msg+=" <b>Bilinmeyen talimat/komut- Unknown command:"+op+" </b><br>"
		
		msg+="<br>("+op+" komutu calistirildi)<br>"
		print msg + "(user:%s)\n"  % (self.logedin_username)
		
		return msg+" <hr>"+self.bottom()
		
		
	operations.exposed=True
		

	def addDaemonOp(self,op,action,info,info2='',opname=''):
		#return $this->executeQuery("insert into operations (op,action,info,info2,tarih) values ('$op','$action','$info','$info2','".date("Y-m-d H:i:s")."')",' sending info to daemon ('.$opname.')'); # date fonksiyonu hatasindan dolayi iptal
		query="insert into operations (op,action,info,info2,tarih) values ('%s','%s','%s','%s','')" % (op,action,info,info2);
		app.executequery(query); 
		
	
	



root = HelloWorld()
print app.conf
cherrypy.quickstart(root)
