<?php
/** phpMinAdmin - Compact MySQL management
* @link http://phpminadmin.sourceforge.net
* @author Jakub Vrana, http://php.vrana.cz
* @copyright 2007 Jakub Vrana
* @license http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
*/error_reporting(E_ALL&~E_NOTICE);if(isset($_GET["file"])){$last_modified=filemtime(__FILE__);if($_SERVER["HTTP_IF_MODIFIED_SINCE"]&&$last_modified<=strtotime($_SERVER["HTTP_IF_MODIFIED_SINCE"])){header("HTTP/1.1 304 Not Modified");}else{header("Last-Modified: ".gmdate("D, d M Y H:i:s",$last_modified)." GMT");if($_GET["file"]=="favicon.ico"){header("Content-Type: image/x-icon");echo
base64_decode("AAABAAEAEBAQAAEABAAoAQAAFgAAACgAAAAQAAAAIAAAAAEABAAAAAAAwAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA////AAAA/wBhTgAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAABEQAAAAAAERExAAAAARERExEAABERMREzMQABExMRERMRAAExMRETMRAAATERERMRAAABExERExAAAAETERExEAAAATERETERERARMRETESESEBMTETESEREQExEzESEREhETMxEREhERIREREAARISIRAAAAAAERERD/4z8A/wM/APgDAADAAwAAgAMAAIAHAACADwAAgB8AAIAfAACAAQAAAAEAAAABAAAAAAAAAAAAAAcAAAD/gQAA");}elseif($_GET["file"]=="default.css"){header("Content-Type: text/css");?>body { color: #000; background-color: #fff; line-height: 1.25em; font-family: Verdana, Arial, Helvetica, sans-serif; margin: 0; font-size: 90%; }
a { color: blue; }
a:visited { color: navy; }
a:hover { color: red; }
h1 { font-size: 150%; margin: 0; padding: .8em 1em; border-bottom: 1px solid #999; font-weight: normal; background: #eee; font-style: italic; }
h1 a:link, h1 a:visited { color: #777; text-decoration: none; }
h2 { font-size: 150%; margin: 0 0 20px -18px; padding: .8em 1em; border-bottom: 1px solid #000; color: #000; font-weight: normal; background: #ddf; }
h3 { font-weight: normal; font-size: 130%; margin: .8em 0; }
table { margin: 0 20px .8em 0; border: 0; border-top: 1px solid #999; border-left: 1px solid #999; font-size: 90%; }
td, th { margin-bottom: 1em; border: 0; border-right: 1px solid #999; border-bottom: 1px solid #999; padding: .2em .3em; }
th { background: #eee; }
fieldset { float: left; padding: .5em .8em; margin: 0 .5em .5em 0; border: 1px solid #999; }
p { margin: 0 20px 1em 0; }
img { vertical-align: middle; }
.error { color: red; background: #fee; padding: .5em .8em; }
.message { color: green; background: #efe; padding: .5em .8em; }
.char { color: #007F00; }
.date { color: #7F007F; }
.enum { color: #007F7F; }
.binary { color: red; }
#menu { position: absolute; margin: 10px 0 0; padding: 0 0 30px 0; top: 2em; left: 0; width: 18em; overflow: auto; overflow-y: hidden; white-space: nowrap; }
#menu p { padding: .8em 1em; margin: 0; border-bottom: 1px solid #ccc; }
#menu form { margin: 0; }
#content { margin: 2em 0 0 21em; padding: 10px 20px 20px 0; }
#lang { position: absolute; top: 0; left: 0; line-height: 1.8em; padding: .3em 1em; }
#breadcrumb { position: absolute; top: 0; left: 21em; background: #eee; height: 2em; line-height: 1.8em; padding: 0 1em; margin: 0 0 0 -18px; }
#schema { margin-left: 60px; position: relative; }
#schema .table { border: 1px solid Silver; padding: 0 2px; cursor: move; position: absolute; }
#schema .references { position: absolute; }
<?php
}else{header("Content-Type: image/gif");switch($_GET["file"]){case"arrow.gif":echo
base64_decode("R0lGODlhCAAKAIAAAICAgP///yH5BAEAAAEALAAAAAAIAAoAAAIPBIJplrGLnpQRqtOy3rsAADs=");break;case"up.gif":echo
base64_decode("R0lGODdhEgASAKEAAO7u7gAAAJmZmQAAACwAAAAAEgASAAACIISPqcvtD00IUU4K730T9J5hFTiKEXmaYcW2rgDH8hwXADs=");break;case"down.gif":echo
base64_decode("R0lGODdhEgASAKEAAO7u7gAAAJmZmQAAACwAAAAAEgASAAACIISPqcvtD00I8cwqKb5bV/5cosdMJtmcHca2lQDH8hwXADs=");break;case"plus.gif":echo
base64_decode("R0lGODdhEgASAKEAAO7u7gAAAJmZmQAAACwAAAAAEgASAAACIYSPqcvtD00I8cwqKb5v+q8pIAhxlRmhZYi17iPE8kzLBQA7");break;case"minus.gif":echo
base64_decode("R0lGODdhEgASAKEAAO7u7gAAAJmZmQAAACwAAAAAEgASAAACGYSPqcvtD6OcFIaLM8s81A+G4hgJ5ommZwEAOw==");break;}}}exit;}if(!ini_get("session.auto_start")){session_name("phpMinAdmin_SID");session_set_cookie_params(ini_get("session.cookie_lifetime"),preg_replace('~\\?.*~','',$_SERVER["REQUEST_URI"]));session_start();}$SELF=preg_replace('~^[^?]*/([^?]*).*~','\\1?',$_SERVER["REQUEST_URI"]).(strlen($_GET["server"])?'server='.urlencode($_GET["server"]).'&':'').(strlen($_GET["db"])?'db='.urlencode($_GET["db"]).'&':'');$TOKENS=&$_SESSION["tokens"][$_GET["server"]][$_SERVER["REQUEST_URI"]];function
idf_escape($idf){return"`".str_replace("`","``",$idf)."`";}function
idf_unescape($idf){return
str_replace("``","`",$idf);}function
bracket_escape($idf,$back=false){static$trans=array(':'=>':1',']'=>':2','['=>':3');return
strtr($idf,($back?array_flip($trans):$trans));}function
optionlist($options,$selected=array()){$return="";foreach($options
as$k=>$v){if(is_array($v)){$return.='<optgroup label="'.htmlspecialchars($k).'">';}foreach((is_array($v)?$v:array($v))as$val){$checked=in_array($val,(array)$selected,true);$return.='<option'.($checked?' selected="selected"':'').'>'.htmlspecialchars($val).'</option>';}if(is_array($v)){$return.='</optgroup>';}}return$return;}function
get_vals($query){global$mysql;$result=$mysql->query($query);$return=array();while($row=$result->fetch_row()){$return[]=$row[0];}$result->free();return$return;}function
table_status($table){global$mysql;$result=$mysql->query("SHOW TABLE STATUS LIKE '".$mysql->escape_string(addcslashes($table,"%_"))."'");return$result->fetch_assoc();}function
fields($table){global$mysql;$return=array();$result=$mysql->query("SHOW FULL COLUMNS FROM ".idf_escape($table));if($result){while($row=$result->fetch_assoc()){preg_match('~^([^(]+)(?:\\((.+)\\))?( unsigned)?( zerofill)?$~',$row["Type"],$match);$return[$row["Field"]]=array("field"=>$row["Field"],"type"=>$match[1],"length"=>$match[2],"unsigned"=>ltrim($match[3].$match[4]),"default"=>$row["Default"],"null"=>($row["Null"]=="YES"),"auto_increment"=>($row["Extra"]=="auto_increment"),"collation"=>$row["Collation"],"privileges"=>array_flip(explode(",",$row["Privileges"])),"comment"=>$row["Comment"],"primary"=>($row["Key"]=="PRI"),);}$result->free();}return$return;}function
indexes($table){global$mysql;$return=array();$result=$mysql->query("SHOW INDEX FROM ".idf_escape($table));if($result){while($row=$result->fetch_assoc()){$return[$row["Key_name"]]["type"]=($row["Key_name"]=="PRIMARY"?"PRIMARY":($row["Index_type"]=="FULLTEXT"?"FULLTEXT":($row["Non_unique"]?"INDEX":"UNIQUE")));$return[$row["Key_name"]]["columns"][$row["Seq_in_index"]]=$row["Column_name"];$return[$row["Key_name"]]["lengths"][$row["Seq_in_index"]]=$row["Sub_part"];}$result->free();}return$return;}function
foreign_keys($table){global$mysql,$on_actions;static$pattern='(?:[^`]+|``)+';$return=array();$result=$mysql->query("SHOW CREATE TABLE ".idf_escape($table));if($result){$create_table=$mysql->result($result,1);$result->free();preg_match_all("~CONSTRAINT `($pattern)` FOREIGN KEY \\(((?:`$pattern`,? ?)+)\\) REFERENCES `($pattern)`(?:\\.`($pattern)`)? \\(((?:`$pattern`,? ?)+)\\)(?: ON DELETE (".implode("|",$on_actions)."))?(?: ON UPDATE (".implode("|",$on_actions)."))?~",$create_table,$matches,PREG_SET_ORDER);foreach($matches
as$match){preg_match_all("~`($pattern)`~",$match[2],$source);preg_match_all("~`($pattern)`~",$match[5],$target);$return[$match[1]]=array("db"=>idf_unescape(strlen($match[4])?$match[3]:$match[4]),"table"=>idf_unescape(strlen($match[4])?$match[4]:$match[3]),"source"=>array_map('idf_unescape',$source[1]),"target"=>array_map('idf_unescape',$target[1]),"on_delete"=>$match[6],"on_update"=>$match[7],);}}return$return;}function
view($name){global$mysql;return
array("select"=>preg_replace('~^(?:[^`]+|`[^`]*`)* AS ~U','',$mysql->result($mysql->query("SHOW CREATE VIEW ".idf_escape($name)),1)));}function
unique_idf($row,$indexes){foreach($indexes
as$index){if($index["type"]=="PRIMARY"||$index["type"]=="UNIQUE"){$return=array();foreach($index["columns"]as$key){if(!isset($row[$key])){continue
2;}$return[]=urlencode("where[".bracket_escape($key)."]")."=".urlencode($row[$key]);}return$return;}}$return=array();foreach($row
as$key=>$val){$return[]=(isset($val)?urlencode("where[".bracket_escape($key)."]")."=".urlencode($val):"null%5B%5D=".urlencode($key));}return$return;}function
where($where=null){global$mysql;if(!isset($where)){$where=$_GET;}$return=array();foreach((array)$where["where"]as$key=>$val){$return[]=idf_escape(bracket_escape($key,"back"))." = BINARY '".$mysql->escape_string($val)."'";}foreach((array)$where["null"]as$key){$return[]=idf_escape(bracket_escape($key,"back"))." IS NULL";}return$return;}function
process_length($length){global$enum_length;return(preg_match("~^\\s*(?:$enum_length)(?:\\s*,\\s*(?:$enum_length))*\\s*\$~",$length)&&preg_match_all("~$enum_length~",$length,$matches)?implode(",",$matches[0]):preg_replace('~[^0-9,]~','',$length));}function
collations(){global$mysql;$return=array();$result=$mysql->query("SHOW COLLATION");while($row=$result->fetch_assoc()){if($row["Default"]&&$return[$row["Charset"]]){array_unshift($return[$row["Charset"]],$row["Collation"]);}else{$return[$row["Charset"]][]=$row["Collation"];}}$result->free();return$return;}function
token(){return($GLOBALS["TOKENS"][]=rand(1,1e6));}function
token_delete(){if($_POST["token"]&&($pos=array_search($_POST["token"],(array)$GLOBALS["TOKENS"]))!==false){unset($GLOBALS["TOKENS"][$pos]);return
true;}return
false;}function
redirect($location,$message=null){if(isset($message)){$_SESSION["messages"][]=$message;}token_delete();if(strlen(SID)){$location.=(strpos($location,"?")===false?"?":"&").SID;}header("Location: ".(strlen($location)?$location:"."));exit;}function
remove_from_uri($param=""){$param="($param|".session_name().")";return
preg_replace("~\\?$param=[^&]*&~",'?',preg_replace("~\\?$param=[^&]*\$|&$param=[^&]*~",'',$_SERVER["REQUEST_URI"]));}function
get_file($key){if(isset($_POST["files"][$key])){$length=strlen($_POST["files"][$key]);return($length&&$length<4?intval($_POST["files"][$key]):base64_decode($_POST["files"][$key]));}return(!$_FILES[$key]||$_FILES[$key]["error"]?$_FILES[$key]["error"]:file_get_contents($_FILES[$key]["tmp_name"]));}function
select($result){global$SELF;if(!$result->num_rows){echo"<p class='message'>".lang('No rows.')."</p>\n";}else{echo"<table border='1' cellspacing='0' cellpadding='2'>\n";for($i=0;$row=$result->fetch_row();$i++){if(!$i){echo"<thead><tr>";$links=array();$indexes=array();$columns=array();$blobs=array();$types=array();for($j=0;$j<count($row);$j++){$field=$result->fetch_field();if(strlen($field->orgtable)){if(!isset($indexes[$field->orgtable])){$indexes[$field->orgtable]=array();foreach(indexes($field->orgtable)as$index){if($index["type"]=="PRIMARY"){$indexes[$field->orgtable]=array_flip($index["columns"]);break;}}$columns[$field->orgtable]=$indexes[$field->orgtable];}if(isset($columns[$field->orgtable][$field->orgname])){unset($columns[$field->orgtable][$field->orgname]);$indexes[$field->orgtable][$field->orgname]=$j;$links[$j]=$field->orgtable;}}if($field->charsetnr==63){$blobs[$j]=true;}$types[$j]=$field->type;echo"<th>".htmlspecialchars($field->name)."</th>";}echo"</tr></thead>\n";}echo"<tr>";foreach($row
as$key=>$val){if(!isset($val)){$val="<i>NULL</i>";}else{if($blobs[$key]&&preg_match('~[\\x80-\\xFF]~',$val)){$val="<i>".lang('%d byte(s)',strlen($val))."</i>";}else{$val=(strlen(trim($val))?nl2br(htmlspecialchars($val)):"&nbsp;");if($types[$key]==254){$val="<code>$val</code>";}}if(isset($links[$key])&&!$columns[$links[$key]]){$link="edit=".urlencode($links[$key]);foreach($indexes[$links[$key]]as$col=>$j){$link.="&amp;where".urlencode("[".bracket_escape($col)."]")."=".urlencode($row[$j]);}$val='<a href="'.htmlspecialchars($SELF).$link.'">'.$val.'</a>';}}echo"<td>$val</td>";}echo"</tr>\n";}echo"</table>\n";}$result->free();}function
shorten_utf8($string,$length){for($i=0;$i<strlen($string);$i++){if(ord($string[$i])>=192){while(ord($string[$i+1])>=128&&ord($string[$i+1])<192){$i++;}}$length--;if($length==0){return
nl2br(htmlspecialchars(substr($string,0,$i+1)))."<em>...</em>";}}return
nl2br(htmlspecialchars($string));}if(get_magic_quotes_gpc()){$process=array(&$_GET,&$_POST);while(list($key,$val)=each($process)){foreach($val
as$k=>$v){unset($process[$key][$k]);if(is_array($v)){$process[$key][stripslashes($k)]=$v;$process[]=&$process[$key][stripslashes($k)];}else{$process[$key][stripslashes($k)]=stripslashes($v);}}}unset($process);}static$langs=array('en'=>'English','cs'=>'Čeština','sk'=>'Slovenčina','nl'=>'Nederlands','es'=>'Español','de'=>'Deutsch',);function
lang($idf,$number=null){global$LANG,$translations;$translation=$translations[$idf];if(is_array($translation)&&$translation){switch($LANG){case'cs':$pos=($number==1?0:(!$number||$number>=5?2:1));break;case'sk':$pos=($number==1?0:(!$number||$number>=5?2:1));break;default:$pos=($number==1?0:1);}$translation=$translation[$pos];}$args=func_get_args();array_shift($args);return
vsprintf(($translation?$translation:$idf),$args);}function
switch_lang(){global$langs;echo"<p id='lang'>".lang('Language').":";$base=remove_from_uri("lang");foreach($langs
as$lang=>$val){echo' <a href="'.htmlspecialchars($base.(strpos($base,"?")!==false?"&":"?"))."lang=$lang\" title='$val'>$lang</a>";}echo"</p>\n";}if(isset($_GET["lang"])){$_COOKIE["lang"]=$_GET["lang"];$_SESSION["lang"]=$_GET["lang"];}if(isset($langs[$_COOKIE["lang"]])){setcookie("lang",$_GET["lang"],strtotime("+1 month"),preg_replace('~\\?.*~','',$_SERVER["REQUEST_URI"]));$LANG=$_COOKIE["lang"];}elseif(isset($langs[$_SESSION["lang"]])){$LANG=$_SESSION["lang"];}else{$accept_language=array();preg_match_all('~([-a-z_]+)(;q=([0-9.]+))?~',strtolower($_SERVER["HTTP_ACCEPT_LANGUAGE"]),$matches,PREG_SET_ORDER);foreach($matches
as$match){$accept_language[str_replace("_","-",$match[1])]=(isset($match[3])?$match[3]:1);}arsort($accept_language);$LANG="en";foreach($accept_language
as$lang=>$q){if(isset($langs[$lang])){$LANG=$lang;break;}$lang=preg_replace('~-.*~','',$lang);if(!isset($accept_language[$lang])&&isset($langs[$lang])){$LANG=$lang;break;}}}switch($LANG){case'cs':$translations=array('Login'=>'Přihlásit se','phpMinAdmin'=>'phpMinAdmin','Logout successful.'=>'Odhlášení proběhlo v pořádku.','Invalid credentials.'=>'Neplatné přihlašovací údaje.','Server'=>'Server','Username'=>'Uživatel','Password'=>'Heslo','Select database'=>'Vybrat databázi','Invalid database.'=>'Nesprávná databáze.','Create new database'=>'Vytvořit novou databázi','Table has been dropped.'=>'Tabulka byla odstraněna.','Table has been altered.'=>'Tabulka byla změněna.','Table has been created.'=>'Tabulka byla vytvořena.','Alter table'=>'Pozměnit tabulku','Create table'=>'Vytvořit tabulku','Table name'=>'Název tabulky','engine'=>'úložiště','collation'=>'porovnávání','Column name'=>'Název sloupce','Type'=>'Typ','Length'=>'Délka','NULL'=>'NULL','Auto Increment'=>'Auto Increment','Options'=>'Volby','Save'=>'Uložit','Drop'=>'Odstranit','Database has been dropped.'=>'Databáze byla odstraněna.','Database has been created.'=>'Databáze byla vytvořena.','Database has been renamed.'=>'Databáze byla přejmenována.','Database has been altered.'=>'Databáze byla změněna.','Alter database'=>'Pozměnit databázi','Create database'=>'Vytvořit databázi','SQL command'=>'SQL příkaz','Dump'=>'Export','Logout'=>'Odhlásit','database'=>'databáze','Use'=>'Vybrat','No tables.'=>'Žádné tabulky.','select'=>'vypsat','Create new table'=>'Vytvořit novou tabulku','Item has been deleted.'=>'Položka byla smazána.','Item has been updated.'=>'Položka byla aktualizována.','Item has been inserted.'=>'Položka byla vložena.','Edit'=>'Upravit','Insert'=>'Vložit','Save and insert next'=>'Uložit a vložit další','Delete'=>'Smazat','Database'=>'Databáze','Routines'=>'Procedury','Indexes has been altered.'=>'Indexy byly změněny.','Indexes'=>'Indexy','Alter indexes'=>'Pozměnit indexy','Add next'=>'Přidat další','Language'=>'Jazyk','Select'=>'Vypsat','New item'=>'Nová položka','Search'=>'Vyhledat','Sort'=>'Setřídit','DESC'=>'sestupně','Limit'=>'Limit','No rows.'=>'Žádné řádky.','Action'=>'Akce','edit'=>'upravit','Page'=>'Stránka','Query executed OK, %d row(s) affected.'=>array('Příkaz proběhl v pořádku, byl změněn %d záznam.','Příkaz proběhl v pořádku, byly změněny %d záznamy.','Příkaz proběhl v pořádku, bylo změněno %d záznamů.'),'Error in query'=>'Chyba v dotazu','Execute'=>'Provést','Table'=>'Tabulka','Foreign keys'=>'Cizí klíče','Triggers'=>'Triggery','View'=>'Pohled','Unable to select the table'=>'Nepodařilo se vypsat tabulku','Invalid CSRF token. Send the form again.'=>'Neplatný token CSRF. Odešlete formulář znovu.','Comment'=>'Komentář','Default values has been set.'=>'Výchozí hodnoty byly nastaveny.','Default values'=>'Výchozí hodnoty','BOOL'=>'BOOL','Show column comments'=>'Zobrazit komentáře sloupců','%d byte(s)'=>array('%d bajt','%d bajty','%d bajtů'),'No commands to execute.'=>'Žádné příkazy k vykonání.','Unable to upload a file.'=>'Nepodařilo se nahrát soubor.','File upload'=>'Nahrání souboru','File uploads are disabled.'=>'Nahrávání souborů není povoleno.','Routine has been called, %d row(s) affected.'=>array('Procedura byla zavolána, byl změněn %d záznam.','Procedura byla zavolána, byly změněny %d záznamy.','Procedura byla zavolána, bylo změněno %d záznamů.'),'Call'=>'Zavolat','No MySQL extension'=>'Žádná MySQL extenze','None of supported PHP extensions (%s) are available.'=>'Není dostupná žádná z podporovaných PHP extenzí (%s).','Sessions must be enabled.'=>'Session proměnné musí být povolené.','Session expired, please login again.'=>'Session vypršela, přihlašte se prosím znovu.','Text length'=>'Délka textů','Syntax highlighting'=>'Zvýrazňování syntaxe','Foreign key has been dropped.'=>'Cizí klíč byl odstraněn.','Foreign key has been altered.'=>'Cizí klíč byl změněn.','Foreign key has been created.'=>'Cizí klíč byl vytvořen.','Foreign key'=>'Cizí klíč','Target table'=>'Cílová tabulka','Change'=>'Změnit','Source'=>'Zdroj','Target'=>'Cíl','Add column'=>'Přidat sloupec','Alter'=>'Změnit','Add foreign key'=>'Přidat cizí klíč','ON DELETE'=>'Při smazání','ON UPDATE'=>'Při změně','Index Type'=>'Typ indexu','Column (length)'=>'Sloupec (délka)','View has been dropped.'=>'Pohled byl odstraněn.','View has been altered.'=>'Pohled byl změněn.','View has been created.'=>'Pohled byl vytvořen.','Alter view'=>'Pozměnit pohled','Create view'=>'Vytvořit pohled','Name'=>'Název','Process list'=>'Seznam procesů','%d process(es) has been killed.'=>array('Byl ukončen %d proces.','Byly ukončeny %d procesy.','Bylo ukončeno %d procesů.'),'Kill'=>'Ukončit','IN-OUT'=>'IN-OUT','Parameter name'=>'Název parametru','Database schema'=>'Schéma databáze','Create procedure'=>'Vytvořit proceduru','Create function'=>'Vytvořit funkci','Routine has been dropped.'=>'Procedura byla odstraněna.','Routine has been altered.'=>'Procedura byla změněna.','Routine has been created.'=>'Procedura byla vytvořena.','Alter function'=>'Změnit funkci','Alter procedure'=>'Změnit proceduru','Return type'=>'Návratový typ','Add trigger'=>'Přidat trigger','Trigger has been dropped.'=>'Trigger byl odstraněn.','Trigger has been altered.'=>'Trigger byl změněn.','Trigger has been created.'=>'Trigger byl vytvořen.','Alter trigger'=>'Změnit trigger','Create trigger'=>'Vytvořit trigger','Time'=>'Čas','Event'=>'Událost','MySQL version: %s through PHP extension %s'=>'Verze MySQL: %s přes PHP extenzi %s','%d row(s)'=>array('%d řádek','%d řádky','%d řádků'),'around %d row(s)'=>array('přibližně %d řádek','přibližně %d řádky','přibližně %d řádků'),'ON UPDATE CURRENT_TIMESTAMP'=>'Při změně aktuální čas','Remove'=>'Odebrat','Are you sure?'=>'Opravdu?','Privileges'=>'Oprávnění','Create user'=>'Vytvořit uživatele','User has been dropped.'=>'Uživatel byl odstraněn.','User has been altered.'=>'Uživatel byl změněn.','User has been created.'=>'Uživatel byl vytvořen.','Hashed'=>'Zahašované','Column'=>'Sloupec','Routine'=>'Procedura','Grant'=>'Povolit','Revoke'=>'Zakázat','Error during deleting'=>'Chyba při mazání','%d item(s) have been deleted.'=>array('Byl smazán %d záznam.','Byly smazány %d záznamy.','Bylo smazáno %d záznamů.'),'all'=>'vše','Delete selected'=>'Smazat označené','Truncate table'=>'Promazat tabulku','Too big POST data. Reduce the data or increase the "post_max_size" configuration directive.'=>'Příliš velká POST data. Zmenšete data nebo zvyšte hodnotu konfigurační direktivy "post_max_size".','Logged as: %s'=>'Přihlášen jako: %s','Move up'=>'Přesunout nahoru','Move down'=>'Přesunout dolů',);break;case'de':$translations=array('Login'=>'Login','phpMinAdmin'=>'phpMinAdmin','Logout successful.'=>'Abmeldung erfolgreich.','Invalid credentials.'=>'Ungültige Anmelde-Informationen.','Server'=>'Server','Username'=>'Benutzer','Password'=>'Passwort','Select database'=>'Datenbank auswählen','Invalid database.'=>'Datenbank ungültig.','Create new database'=>'Neue Datenbank','Table has been dropped.'=>'Tabelle gelöscht.','Table has been altered.'=>'Tabelle geändert.','Table has been created.'=>'Tabelle erstellt.','Alter table'=>'Tabelle ändern','Create table'=>'Neue Tabelle erstellen','Table name'=>'Name der Tabelle','engine'=>'Motor','collation'=>'Kollation','Column name'=>'Spaltenname','Type'=>'Typ','Length'=>'Länge','NULL'=>'NULL','Auto Increment'=>'Auto-Inkrement','Options'=>'Optionen','Save'=>'Speichern','Drop'=>'Löschen','Database has been dropped.'=>'Datenbank gelöscht.','Database has been created.'=>'Datenbank erstellt.','Database has been renamed.'=>'Datenbank umbenannt.','Database has been altered.'=>'Datenbank geändert.','Alter database'=>'Datenbank ändern','Create database'=>'Neue Datenbank','SQL command'=>'SQL-Query','Dump'=>'Export','Logout'=>'Abmelden','database'=>'Datenbank','Use'=>'Benutzung','No tables.'=>'Keine Tabellen.','select'=>'zeigen','Create new table'=>'Neue Tabelle','Item has been deleted.'=>'Datensatz gelöscht.','Item has been updated.'=>'Datensatz geändert.','Item has been inserted.'=>'Datensatz hinzugefügt.','Edit'=>'Ändern','Insert'=>'Hinzufügen','Save and insert next'=>'Speichern und nächsten hinzufügen','Delete'=>'Löschen','Database'=>'Datenbank','Routines'=>'Prozeduren','Indexes has been altered.'=>'Indizes geändert.','Indexes'=>'Indizes','Alter indexes'=>'Indizes ändern','Add next'=>'Hinzufügen','Language'=>'Sprache','Select'=>'Daten zeigen von','New item'=>'Neuer Datensatz','Search'=>'Suchen','Sort'=>'Ordnen','DESC'=>'absteigend','Limit'=>'Begrenzung','No rows.'=>'Keine Daten.','Action'=>'Aktion','edit'=>'ändern','Page'=>'Seite','Query executed OK, %d row(s) affected.'=>array('Abfrage ausgeführt, %d Datensatz betroffen.','Abfrage ausgeführt, %d Datensätze betroffen.'),'Error in query'=>'Fehler in der SQL-Abfrage','Execute'=>'Ausführen','Table'=>'Tabelle','Foreign keys'=>'Fremdschlüssel','Triggers'=>'Trigger','View'=>'View','Unable to select the table'=>'Tabelle kann nicht ausgewählt werden','Invalid CSRF token. Send the form again.'=>'CSRF Token ungültig. Bitte die Formulardaten erneut abschicken.','Comment'=>'Kommentar','Default values has been set.'=>'Standard Vorgabewerte sind erstellt worden.','Default values'=>'Vorgabewerte festlegen','BOOL'=>'BOOL','Show column comments'=>'Spaltenkomentare zeigen','%d byte(s)'=>array('%d Byte','%d Bytes'),'No commands to execute.'=>'Kein Kommando vorhanden.','Unable to upload a file.'=>'Unmöglich Dateien hochzuladen.','File upload'=>'Datei importieren','File uploads are disabled.'=>'Importieren von Dateien abgeschaltet.','Routine has been called, %d row(s) affected.'=>array('Kommando SQL ausgeführt, %d Datensatz betroffen.','Kommando SQL ausgeführt, %d Datensätze betroffen.'),'Call'=>'Aufrufen','No MySQL extension'=>'Keine MySQL-Erweiterungen installiert','None of supported PHP extensions (%s) are available.'=>'Keine der unterstützten PHP-Erweiterungen (%s) ist vorhanden.','Sessions must be enabled.'=>'Sitzungen müssen aktiviert sein.','Session expired, please login again.'=>'Sitzungsdauer abgelaufen, bitte erneut anmelden.','Text length'=>'Textlänge','Syntax highlighting'=>'Syntax highlighting','Foreign key has been dropped.'=>'Fremdschlüssel gelöscht.','Foreign key has been altered.'=>'Fremdschlüssel geändert.','Foreign key has been created.'=>'Fremdschlüssel erstellt.','Foreign key'=>'Fremdschlüssel','Target table'=>'Zieltabelle','Change'=>'Ändern','Source'=>'Ursprung','Target'=>'Ziel','Add column'=>'Spalte hinzufügen','Alter'=>'Ändern','Add foreign key'=>'Fremdschlüssel hinzufügen','ON DELETE'=>'ON DELETE','ON UPDATE'=>'ON UPDATE','Index Type'=>'Index-Typ','Column (length)'=>'Spalte (Länge)','View has been dropped.'=>'View gelöscht.','View has been altered.'=>'View geändert.','View has been created.'=>'View erstellt.','Alter view'=>'View ändern','Create view'=>'Neue View erstellen','Name'=>'Name','Process list'=>'Prozessliste','%d process(es) has been killed.'=>array('%d Prozess gestoppt.','%d Prozesse gestoppt.'),'Kill'=>'Anhalten','IN-OUT'=>'IN-OUT','Parameter name'=>'Name des Parameters','Database schema'=>'Datenbankschema','Create procedure'=>'Neue Prozedur','Create function'=>'Neue Funktion','Routine has been dropped.'=>'Prozedur gelöscht.','Routine has been altered.'=>'Prozedur geändert.','Routine has been created.'=>'Prozedur erstellt.','Alter function'=>'Funktion ändern','Alter procedure'=>'Prozedur ändern','Return type'=>'Typ des Rückgabewertes','Add trigger'=>'Trigger hinzufügen','Trigger has been dropped.'=>'Trigger gelöscht.','Trigger has been altered.'=>'Trigger geändert.','Trigger has been created.'=>'Trigger erstellt.','Alter trigger'=>'Trigger ändern','Create trigger'=>'Trigger hinzufügen','Time'=>'Zeitpunkt','Event'=>'Ereignis','%d row(s)'=>array('%d Datensatz','%d Datensätze'),'ON UPDATE CURRENT_TIMESTAMP'=>'ON UPDATE CURRENT_TIMESTAMP','Remove'=>'Entfernen','Are you sure?'=>'Sind Sie sicher ?','Privileges'=>'Rechte','Create user'=>'Neuer Benutzer','User has been dropped.'=>'Benutzer gelöscht.','User has been altered.'=>'Benutzer geändert.','User has been created.'=>'Benutzer erstellt.','Hashed'=>'Gehashed','Column'=>'Spalte','Routine'=>'Routine','Grant'=>'Erlauben','Revoke'=>'Verbieten','Error during deleting'=>'Fehler beim Löschvorgang','%d item(s) have been deleted.'=>array('%d Artikel gelöscht.','%d Artikel gelöscht.'),'all'=>'alle','Delete selected'=>'Markierte löschen','Truncate table'=>'Tabelleninhalt löschen (truncate)','MySQL version: %s through PHP extension %s'=>'Version MySQL: %s, Zugriff unter Benutzung der PHP-Erweiterung %s','around %d row(s)'=>array('ungefähren %d Datensatz','ungefähren %d Datensätze'),'Logged as: %s'=>'Angemeldet als: %s','Too big POST data. Reduce the data or increase the "post_max_size" configuration directive.'=>'POST data zu gross. Reduzieren Sie die Grösse oder vergrössern Sie den Wert "post_max_size" in der Konfiguration.','Move up'=>'Nach oben','Move down'=>'Nach unten',);break;case'en':$translations=array('Query executed OK, %d row(s) affected.'=>array('Query executed OK, %d row affected.','Query executed OK, %d rows affected.'),'%d byte(s)'=>array('%d byte','%d bytes'),'Routine has been called, %d row(s) affected.'=>array('Routine has been called, %d row affected.','Routine has been called, %d rows affected.'),'%d process(es) has been killed.'=>array('%d process has been killed.','%d processes have been killed.'),'%d row(s)'=>array('%d row','%d rows'),'around %d row(s)'=>array('around %d row','around %d rows'),'%d item(s) have been deleted.'=>array('%d item has been deleted.','%d items have been deleted.'),);break;case'es':$translations=array('Login'=>'Login','phpMinAdmin'=>'phpMinAdmin','Logout successful.'=>'Salida exitosa.','Invalid credentials.'=>'Autenticación fallada.','Server'=>'Servidor','Username'=>'Usuario','Password'=>'Contraseña','Select database'=>'Seleccionar Base de datos','Invalid database.'=>'Base de datos inválida.','Create new database'=>'Nueva Base de datos','Table has been dropped.'=>'Tabla eliminada.','Table has been altered.'=>'Tabla modificada.','Table has been created.'=>'Tabla creada.','Alter table'=>'Modificar tabla','Create table'=>'Crear tabla','Table name'=>'Nombre de tabla','engine'=>'motor','collation'=>'collation','Column name'=>'Nombre de columna','Type'=>'Tipo','Length'=>'Longitud','NULL'=>'NULL','Auto Increment'=>'Auto increment','Options'=>'Opciones','Save'=>'Guardar','Drop'=>'Eliminar','Database has been dropped.'=>'Base de datos eliminada.','Database has been created.'=>'Base de datos creada.','Database has been renamed.'=>'Base de datos renombrada.','Database has been altered.'=>'Base de datos modificada.','Alter database'=>'Modificar Base de datos','Create database'=>'Crear Base de datos','SQL command'=>'Comando SQL','Dump'=>'Exportar','Logout'=>'Logout','database'=>'base de datos','Use'=>'Uso','No tables.'=>'No existen tablas.','select'=>'ver','Create new table'=>'Nueva tabla','Item has been deleted.'=>'Registro eliminado.','Item has been updated.'=>'Registro modificado.','Item has been inserted.'=>'Registro insertado.','Edit'=>'Modificar','Insert'=>'Agregar','Save and insert next'=>'Guardar e insertar otro','Delete'=>'Eliminar','Database'=>'Base de datos','Routines'=>'Procedimientos','Indexes has been altered.'=>'Indices modificados.','Indexes'=>'Indices','Alter indexes'=>'Modificar indices','Add next'=>'Agregar','Language'=>'Idioma','Select'=>'Ver datos de','New item'=>'Nuevo registro','Search'=>'Buscar','Sort'=>'Ordenar','DESC'=>'descendiente','Limit'=>'Limit','No rows.'=>'No hay filas.','Action'=>'Acción','edit'=>'modificar','Page'=>'Página','Query executed OK, %d row(s) affected.'=>array('Consulta ejecutada, %d registro afectado.','Consulta ejecutada, %d registros afectados.'),'Error in query'=>'Error en consulta','Execute'=>'Ejecutar','Table'=>'Tabla','Foreign keys'=>'Claves foráneas','Triggers'=>'Triggers','View'=>'Vistas','Unable to select the table'=>'No posible seleccionar la tabla','Invalid CSRF token. Send the form again.'=>'Token CSRF inválido. Vuelva a enviar los datos del formulario.','Comment'=>'Comentario','Default values has been set.'=>'Valores por omisión establecidos.','Default values'=>'Establecer valores por omisión','BOOL'=>'BOOL','Show column comments'=>'Mostrar comentario de columnas','%d byte(s)'=>array('%d byte','%d bytes'),'No commands to execute.'=>'No hay comando a ejecutar.','Unable to upload a file.'=>'No posible subir archivos.','File upload'=>'Importar archivo','File uploads are disabled.'=>'Importación de archivos deshablilitado.','Routine has been called, %d row(s) affected.'=>array('Consulta ejecutada, %d registro afectado.','Consulta ejecutada, %d registros afectados.'),'Call'=>'Llamar','No MySQL extension'=>'No hay extension MySQL','None of supported PHP extensions (%s) are available.'=>'Ninguna de las extensiones PHP soportadas (%s) está disponible.','Sessions must be enabled.'=>'Deben estar habilitadas las sesiones.','Session expired, please login again.'=>'Sesion expirada, favor loguéese de nuevo.','Text length'=>'Longitud de texto','Syntax highlighting'=>'Coloreado de Sintaxis','Foreign key has been dropped.'=>'Clave foránea eliminada.','Foreign key has been altered.'=>'Clave foránea modificada.','Foreign key has been created.'=>'Clave foránea creada.','Foreign key'=>'Clave foránea','Target table'=>'Tabla destino','Change'=>'Modificar','Source'=>'Origen','Target'=>'Destino','Add column'=>'Agregar columna','Alter'=>'Modificar','Add foreign key'=>'Agregar clave foránea','ON DELETE'=>'ON DELETE','ON UPDATE'=>'ON UPDATE','Index Type'=>'Tipo de índice','Column (length)'=>'Columna (longitud)','View has been dropped.'=>'Vista eliminada.','View has been altered.'=>'Vista modificada.','View has been created.'=>'Vista creada.','Alter view'=>'Modificar vista','Create view'=>'Cear vista','Name'=>'Nombre','Process list'=>'Lista de procesos','%d process(es) has been killed.'=>array('%d proceso detenido.','%d procesos detenidos.'),'Kill'=>'Detener','IN-OUT'=>'IN-OUT','Parameter name'=>'Nombre de Parametro','Database schema'=>'Esquema de base de datos','Create procedure'=>'Crear procedimiento','Create function'=>'Crear función','Routine has been dropped.'=>'Procedimiento eliminado.','Routine has been altered.'=>'Procedimiento modificado.','Routine has been created.'=>'Procedimiento creado.','Alter function'=>'Modificar Función','Alter procedure'=>'Modificar procedimiento','Return type'=>'Tipo de valor retornado','Add trigger'=>'Agregar trigger','Trigger has been dropped.'=>'Trigger eliminado.','Trigger has been altered.'=>'Trigger modificado.','Trigger has been created.'=>'Trigger creado.','Alter trigger'=>'Modificar Trigger','Create trigger'=>'Agregar Trigger','Time'=>'Tiempo','Event'=>'Evento','%d row(s)'=>array('%d fila','%d filas'),'ON UPDATE CURRENT_TIMESTAMP'=>'ON UPDATE CURRENT_TIMESTAMP','Remove'=>'Eliminar','Are you sure?'=>'Está seguro?','Privileges'=>'Privilegios','Create user'=>'Crear Usuario','User has been dropped.'=>'Usuario eliminado.','User has been altered.'=>'Usuario modificado.','User has been created.'=>'Usuario creado.','Hashed'=>'Gehashed','Column'=>'Columna','Routine'=>'Rutina','Grant'=>'Conceder','Revoke'=>'Impedir','Error during deleting'=>'Error durante el borrado','%d item(s) have been deleted.'=>array('%d item eliminado.','%d itemes eliminados.'),'all'=>'todos','Delete selected'=>'Eliminar seleccionados','Truncate table'=>'Vaciar tabla (truncate)','MySQL version: %s through PHP extension %s'=>'Versión MySQL: %s a través de extensión PHP %s','around %d row(s)'=>array('acaso %d fila','acaso %d filas'),'Logged as: %s'=>'Logeado como: %s','Too big POST data. Reduce the data or increase the "post_max_size" configuration directive.'=>'POST data demasiado grande. Reduzca el tamaño o aumente la directiva de configuración "post_max_size".','Move up'=>'Mover arriba','Move down'=>'Mover abajo',);break;case'nl':$translations=array('Login'=>'Inloggen','phpMinAdmin'=>'phpMinAdmin','Logout successful.'=>'Uitloggen geslaagd.','Invalid credentials.'=>'Ongeldige logingegevens.','Server'=>'Server','Username'=>'Gebruikersnaam','Password'=>'Wachtwoord','Select database'=>'Database selecteren','Invalid database.'=>'Ongeldige database.','Create new database'=>'Nieuwe database','Table has been dropped.'=>'Tabel verwijderd.','Table has been altered.'=>'Tabel aangepast.','Table has been created.'=>'Tabel aangemaakt.','Alter table'=>'Tabel aanpassen','Create table'=>'Tabel aanmaken','Table name'=>'Tabelnaam','engine'=>'engine','collation'=>'collation','Column name'=>'Kolomnaam','Type'=>'Type','Length'=>'Lengte','NULL'=>'NULL','Auto Increment'=>'Auto nummering','Options'=>'Opties','Save'=>'Opslaan','Drop'=>'Verwijderen','Database has been dropped.'=>'Database verwijderd.','Database has been created.'=>'Database aangemaakt.','Database has been renamed.'=>'Database hernoemd.','Database has been altered.'=>'Database aangepast.','Alter database'=>'Database aanpassen','Create database'=>'Database aanmaken','SQL command'=>'SQL opdracht','Dump'=>'Exporteer','Logout'=>'Uitloggen','database'=>'database','Use'=>'Gebruik','No tables.'=>'Geen tabellen.','select'=>'kies','Create new table'=>'Nieuwe tabel','Item has been deleted.'=>'Item verwijderd.','Item has been updated.'=>'Item aangepast.','Item has been inserted.'=>'Item toegevoegd.','Edit'=>'Bewerk','Insert'=>'Toevoegen','Save and insert next'=>'Opslaan, daarna toevoegen','Delete'=>'Verwijderen','Database'=>'Database','Routines'=>'Procedures','Indexes has been altered.'=>'Index aangepast.','Indexes'=>'Indexen','Alter indexes'=>'Indexen aanpassen','Add next'=>'Volgende toevoegen','Language'=>'Taal','Select'=>'Kies','New item'=>'Nieuw item','Search'=>'Zoeken','Sort'=>'Sorteren','DESC'=>'Aflopend','Limit'=>'Beperk','No rows.'=>'Geen rijen.','Action'=>'Acties','edit'=>'bewerk','Page'=>'Pagina','Query executed OK, %d row(s) affected.'=>array('Query uitgevoerd, %d rij geraakt.','Query uitgevoerd, %d rijen geraakt.'),'Error in query'=>'Fout in query','Execute'=>'Uitvoeren','Table'=>'Tabel','Foreign keys'=>'Foreign keys','Triggers'=>'Triggers','View'=>'View','Unable to select the table'=>'Onmogelijk tabel te selecteren','Invalid CSRF token. Send the form again.'=>'Ongeldig CSRF token. Verstuur het formulier opnieuw.','Comment'=>'Commentaar','Default values has been set.'=>'Standaard waarde ingesteld.','Default values'=>'Standaard waarden','BOOL'=>'BOOL','Show column comments'=>'Kolomcommentaar weergeven','%d byte(s)'=>array('%d byte','%d bytes'),'No commands to execute.'=>'Geen opdrachten uit te voeren.','Unable to upload a file.'=>'Onmogelijk bestand te uploaden.','File upload'=>'Bestand uploaden','File uploads are disabled.'=>'Bestanden uploaden is uitgeschakeld.','Routine has been called, %d row(s) affected.'=>array('Procedure uitgevoerd, %d rij geraakt.','Procedure uitgevoerd, %d rijen geraakt.'),'Call'=>'Uitvoeren','No MySQL extension'=>'Geen MySQL extensie','None of supported PHP extensions (%s) are available.'=>'Geen geldige PHP extensies beschikbaar (%s).','Sessions must be enabled.'=>'Siessies moeten geactiveerd zijn.','Session expired, please login again.'=>'Uw sessie is verlopen. Gelieve opnieuw in te loggen.','Text length'=>'Tekst lengte','Syntax highlighting'=>'Syntax highlighting','Foreign key has been dropped.'=>'Foreign key verwijderd.','Foreign key has been altered.'=>'Foreign key aangepast.','Foreign key has been created.'=>'Foreign key aangemaakt.','Foreign key'=>'Foreign key','Target table'=>'Doeltabel','Change'=>'Veranderen','Source'=>'Bron','Target'=>'Doel','Add column'=>'Kolom toevoegen','Alter'=>'Aanpassen','Add foreign key'=>'Foreign key aanmaken','ON DELETE'=>'ON DELETE','ON UPDATE'=>'ON UPDATE','Index Type'=>'Index type','Column (length)'=>'Kolom (lengte)','View has been dropped.'=>'View verwijderd.','View has been altered.'=>'View aangepast.','View has been created.'=>'View aangemaakt.','Alter view'=>'View aanpassen','Create view'=>'View aanmaken','Name'=>'Naam','Process list'=>'Proceslijst','%d process(es) has been killed.'=>array('%d proces gestopt.','%d processen gestopt.'),'Kill'=>'Stoppen','IN-OUT'=>'IN-OUT','Parameter name'=>'Parameternaam','Database schema'=>'Database schema','Create procedure'=>'Procedure aanmaken','Create function'=>'Functie aanmaken','Routine has been dropped.'=>'Procedure verwijderd.','Routine has been altered.'=>'Procedure aangepast.','Routine has been created.'=>'Procedure aangemaakt.','Alter function'=>'Functie aanpassen','Alter procedure'=>'Procedure aanpassen','Return type'=>'Return type','Add trigger'=>'Trigger aanmaken','Trigger has been dropped.'=>'Trigger verwijderd.','Trigger has been altered.'=>'Trigger aangepast.','Trigger has been created.'=>'Trigger aangemaakt.','Alter trigger'=>'Trigger aanpassen','Create trigger'=>'Trigger aanmaken','Time'=>'Time','Event'=>'Event','MySQL version: %s through PHP extension %s'=>'MySQL versie: %s met PHP extensie %s','%d row(s)'=>array('%d rij','%d rijen'),'around %d row(s)'=>array('ongeveer %d rij','ongeveer %d rijen'),'ON UPDATE CURRENT_TIMESTAMP'=>'ON UPDATE CURRENT_TIMESTAMP','Remove'=>'Verwijderen','Are you sure?'=>'Weet u het zeker?','Privileges'=>'Rechten','Create user'=>'Gebruiker aanmaken','User has been dropped.'=>'Gebruiker verwijderd.','User has been altered.'=>'Gebruiker aangepast.','User has been created.'=>'Gebruiker aangemaakt.','Hashed'=>'Gehashed','Column'=>'Kolom','Routine'=>'Routine','Grant'=>'Toekennen','Revoke'=>'Intrekken','Error during deleting'=>'Fout tijdens verwijderen','%d item(s) have been deleted.'=>array('%d item werd verwijderd.','%d items warden verwijderd.'),'all'=>'alle','Delete selected'=>'Geselecteerde verwijderen','Truncate table'=>'Tabel leegmaken','Logged as: %s'=>'Aangemeld als: %s','Too big POST data. Reduce the data or increase the "post_max_size" configuration directive.'=>'POST-data is te groot. Verklein de hoeveelheid data of verhoog de "post_max_size" configuratie.','Move up'=>'Omhoog','Move down'=>'Omlaag',);break;case'sk':$translations=array('Login'=>'Prihlásiť sa','phpMinAdmin'=>'phpMinAdmin','Logout successful.'=>'Odhlásenie prebehlo v poriadku','Invalid credentials.'=>'Neplatné prihlasovacie údaje.','Server'=>'Server','Username'=>'Používateľ','Password'=>'Heslo','Select database'=>'Vybrať databázu','Invalid database.'=>'Nesprávna databáza.','Create new database'=>'Vytvoriť novú databázu','Table has been dropped.'=>'Tabuľka bola odstránená.','Table has been altered.'=>'Tabuľka bola zmenená.','Table has been created.'=>'Tabuľka bola vytvorená.','Alter table'=>'Zmeniť tabuľku','Create table'=>'Vytvoriť tabuľku','Table name'=>'Názov tabuľky','engine'=>'úložisko','collation'=>'porovnávanie','Column name'=>'Názov stĺpca','Type'=>'Typ','Length'=>'Dĺžka','NULL'=>'NULL','Auto Increment'=>'Auto Increment','Options'=>'Voľby','Save'=>'Uložiť','Drop'=>'Odstrániť','Database has been dropped.'=>'Databáza bola odstránená.','Database has been created.'=>'Databáza bola vytvorená.','Database has been renamed.'=>'Databáza bola premenovaná.','Database has been altered.'=>'Databáza bola zmenená.','Alter database'=>'Zmeniť databázu','Create database'=>'Vytvoriť databázu','SQL command'=>'SQL príkaz','Dump'=>'Export','Logout'=>'Odhlásiť','database'=>'databáza','Use'=>'Vybrať','No tables.'=>'Žiadne tabuľky.','select'=>'vypísať','Create new table'=>'Vytvoriť novú tabuľku','Item has been deleted.'=>'Položka bola vymazaná.','Item has been updated.'=>'Položka bola aktualizovaná.','Item has been inserted.'=>'Položka bola vložená.','Edit'=>'Upraviť','Insert'=>'Vložiť','Save and insert next'=>'Uložiť a vložiť ďalší','Delete'=>'Zmazať','Database'=>'Databáza','Routines'=>'Procedúry','Indexes has been altered.'=>'Indexy boli zmenené.','Indexes'=>'Indexy','Alter indexes'=>'Zmeniť indexy','Add next'=>'Pridať ďalší','Language'=>'Jazyk','Select'=>'Vypísať','New item'=>'Nová položka','Search'=>'Vyhľadať','Sort'=>'Zotriediť','DESC'=>'zostupne','Limit'=>'Limit','No rows.'=>'Žiadne riadky.','Action'=>'Akcia','edit'=>'upraviť','Page'=>'Stránka','Query executed OK, %d row(s) affected.'=>array('Príkaz prebehol v poriadku, bol zmenený %d záznam.','Príkaz prebehol v poriadku boli zmenené %d záznamy.','Príkaz prebehol v poriadku bolo zmenených %d záznamov.'),'Error in query'=>'Chyba v dotaze','Execute'=>'Vykonať','Table'=>'Tabuľka','Foreign keys'=>'Cudzie kľúče','Triggers'=>'Triggery','View'=>'Pohľad','Unable to select the table'=>'Tabuľku sa nepodarilo vypísať','Invalid CSRF token. Send the form again.'=>'Neplatný token CSRF. Odošlite formulár znova.','Comment'=>'Komentár','Default values has been set.'=>'Východzie hodnoty boli nastavené.','Default values'=>'Východzie hodnoty','BOOL'=>'BOOL','Show column comments'=>'Zobraziť komentáre stĺpcov','%d byte(s)'=>array('%d bajt','%d bajty','%d bajtov'),'No commands to execute.'=>'Žiadne príkazy na vykonanie.','Unable to upload a file.'=>'Súbor sa nepodarilo nahrať.','File upload'=>'Nahranie súboru','File uploads are disabled.'=>'Nahrávánie súborov nie je povolené.','Routine has been called, %d row(s) affected.'=>array('Procedúra bola zavolaná, bol zmenený %d záznam.','Procedúra bola zavolaná, boli zmenené %d záznamy.','Procedúra bola zavolaná, bolo zmenených %d záznamov.'),'Call'=>'Zavolať','No MySQL extension'=>'Žiadne MySQL rozšírenie','None of supported PHP extensions (%s) are available.'=>'Nie je dostupné žiadne z podporovaných rozšírení (%s).','Sessions must be enabled.'=>'Session premenné musia byť povolené.','Session expired, please login again.'=>'Session vypršala, prihláste sa prosím znova.','Text length'=>'Dĺžka textov','Syntax highlighting'=>'Zvýrazňovanie syntaxe','Foreign key has been dropped.'=>'Cudzí kľúč bol odstránený.','Foreign key has been altered.'=>'Cudzí kľúč bol zmenený.','Foreign key has been created.'=>'Cudzí kľúč bol vytvorený.','Foreign key'=>'Cudzí kľúč','Target table'=>'Cieľová tabuľka','Change'=>'Zmeniť','Source'=>'Zdroj','Target'=>'Cieľ','Add column'=>'Pridať stĺpec','Alter'=>'Zmeniť','Add foreign key'=>'Pridať cudzí kľúč','ON DELETE'=>'ON DELETE','ON UPDATE'=>'ON UPDATE','Index Type'=>'Typ indexu','Column (length)'=>'Stĺpec (dĺžka)','View has been dropped.'=>'Pohľad bol odstránený.','View has been altered.'=>'Pohľad bol zmenený.','View has been created.'=>'Pohľad bol vytvorený.','Alter view'=>'Zmeniť pohľad','Create view'=>'Vytvoriť pohľad','Name'=>'Názov','Process list'=>'Zoznam procesov','%d process(es) has been killed.'=>array('Bol ukončený %d proces.','Boli ukončené %d procesy.','Bolo ukončených %d procesov.'),'Kill'=>'Ukončiť','IN-OUT'=>'IN-OUT','Parameter name'=>'Názov parametra','Database schema'=>'Schéma databázy','Create procedure'=>'Vytvoriť procedúru','Create function'=>'Vytvoriť funkciu','Routine has been dropped.'=>'Procedúra bola odstránená.','Routine has been altered.'=>'Procedúra bola zmenená.','Routine has been created.'=>'Procedúra bola vytvorená.','Alter function'=>'Zmeniť funkciu','Alter procedure'=>'Zmeniť procedúru','Return type'=>'Návratový typ','Add trigger'=>'Pridať trigger','Trigger has been dropped.'=>'Trigger bol odstránený.','Trigger has been altered.'=>'Trigger bol zmenený.','Trigger has been created.'=>'Trigger bol vytvorený.','Alter trigger'=>'Zmeniť trigger','Create trigger'=>'Vytvoriť trigger','Time'=>'Čas','Event'=>'Udalosť','MySQL version: %s through PHP extension %s'=>'Verzia MySQL: %s cez PHP rozšírenie %s','%d row(s)'=>array('%d riadok','%d riadky','%d riadkov'),'around %d row(s)'=>array('približne %d riadok','približne %d riadky','približne %d riadkov'),'ON UPDATE CURRENT_TIMESTAMP'=>'Pri zmene aktuálny čas','Remove'=>'Odobrať','Are you sure?'=>'Naozaj?','Privileges'=>'Oprávnenia','Create user'=>'Vytvoriť používateľa','User has been dropped.'=>'Používateľ bol odstránený.','User has been altered.'=>'Používateľ bol zmenený.','User has been created.'=>'Používateľ bol vytvorený.','Hashed'=>'Zahašované','Column'=>'Stĺpec','Routine'=>'Procedúra','Grant'=>'Povoliť','Revoke'=>'Zakázať','Error during deleting'=>'Chyba pri mazaní','%d item(s) have been deleted.'=>array('%d záznam bol zmazaný.','%d záznamy boli zmazané.','%d záznamov bolo zmazaných.'),'all'=>'všetko','Delete selected'=>'Zmazať označené','Truncate table'=>'Vyprázdniť tabuľku','Too big POST data. Reduce the data or increase the "post_max_size" configuration directive.'=>'Príliš veľké POST dáta. Zmenšite dáta alebo zvýšte hodnotu konfiguračej direktívy "post_max_size".','Logged as: %s'=>'Prihlásený ako: %s','Move up'=>'Presunúť hore','Move down'=>'Presunúť dolu',);break;}function
page_header($title,$error="",$breadcrumb=array(),$title2=""){global$SELF,$LANG;header("Content-Type: text/html; charset=utf-8");?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="<?php echo$LANG;?>">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="Content-Script-Type" content="text/javascript" />
<meta name="robots" content="noindex" />
<title><?php echo$title.(strlen($title2)?": ".htmlspecialchars($title2):"")." - ".lang('phpMinAdmin')." 1.6.1";?></title>
<link rel="shortcut icon" type="image/x-icon" href="<?php echo
preg_replace("~\\?.*~","",$_SERVER["REQUEST_URI"])."?file=favicon.ico";?>" />
<link rel="stylesheet" type="text/css" href="<?php echo
preg_replace("~\\?.*~","",$_SERVER["REQUEST_URI"])."?file=default.css";?>" /><?php ?>
<?php if($_COOKIE["highlight"]=="jush"){?>
<style type="text/css">@import url(http://jush.sourceforge.net/jush.css);</style>
<script type="text/javascript" src="http://jush.sourceforge.net/jush.js" defer="defer"></script>
<script type="text/javascript">window.onload = function () { if (typeof jush != 'undefined') jush.highlight_tag('pre'); }</script>
<?php }?>
</head>

<body>

<div id="content">
<?php

if(isset($breadcrumb)){$link=substr(preg_replace('~db=[^&]*&~','',$SELF),0,-1);echo'<p id="breadcrumb"><a href="'.(strlen($link)?htmlspecialchars($link):".").'">'.(isset($_GET["server"])?htmlspecialchars($_GET["server"]):lang('Server')).'</a> &raquo; ';if(is_array($breadcrumb)){if(strlen($_GET["db"])){echo'<a href="'.substr($SELF,0,-1).'">'.htmlspecialchars($_GET["db"]).'</a> &raquo; ';}foreach($breadcrumb
as$key=>$val){if(strlen($val)){echo'<a href="'.htmlspecialchars($SELF)."$key=".($key!="privileges"?urlencode($val):"").'">'.htmlspecialchars($val).'</a> &raquo; ';}}}echo"$title</p>\n";}echo"<h2>$title".(strlen($title2)?": ".htmlspecialchars($title2):"")."</h2>\n";if($_SESSION["messages"]){echo"<p class='message'>".implode("<br />",$_SESSION["messages"])."</p>\n";$_SESSION["messages"]=array();}if(!$_SESSION["tokens"][$_GET["server"]]["?logout"]){$_SESSION["tokens"][$_GET["server"]]["?logout"]=rand(1,1e6);}if(isset($_SESSION["databases"][$_GET["server"]])&&!isset($_GET["sql"])){session_write_close();}if($error){echo"<p class='error'>".htmlspecialchars($error)."</p>\n";}}function
page_footer($missing=false){global$SELF,$mysql;?>
</div>

<?php switch_lang();?>
<div id="menu">
<h1><a href="http://phpminadmin.sourceforge.net"><?php echo
lang('phpMinAdmin');?></a></h1>
<?php if($missing!="auth"){?>
<form action="" method="post">
<p>
<a href="<?php echo
htmlspecialchars($SELF);?>sql="><?php echo
lang('SQL command');?></a>
<a href="<?php echo
htmlspecialchars($SELF);?>dump=<?php echo
urlencode($_GET["table"]);?>"><?php echo
lang('Dump');?></a>
<input type="hidden" name="token" value="<?php
echo$_SESSION["tokens"][$_GET["server"]]["?logout"];?>" />
<input type="submit" name="logout" value="<?php echo
lang('Logout');?>" />
</p>
</form>
<form action="">
<p><?php if(strlen($_GET["server"])){?><input type="hidden" name="server" value="<?php echo
htmlspecialchars($_GET["server"]);?>" /><?php }?>
<select name="db" onchange="this.form.submit();"><option value="">(<?php echo
lang('database');?>)</option>
<?php

if(!isset($_SESSION["databases"][$_GET["server"]])){flush();$_SESSION["databases"][$_GET["server"]]=get_vals("SHOW DATABASES");}echo
optionlist($_SESSION["databases"][$_GET["server"]],$_GET["db"]);?>
</select>
<?php if(isset($_GET["sql"])){?><input type="hidden" name="sql" value="" /><?php }?>
<?php if(isset($_GET["schema"])){?><input type="hidden" name="schema" value="" /><?php }?>
</p>
<noscript><p><input type="submit" value="<?php echo
lang('Use');?>" /></p></noscript>
</form>
<?php

if($missing!="db"&&strlen($_GET["db"])){$result=$mysql->query("SHOW TABLE STATUS");if(!$result->num_rows){echo"<p class='message'>".lang('No tables.')."</p>\n";}else{echo"<p>\n";while($row=$result->fetch_assoc()){echo'<a href="'.htmlspecialchars($SELF).'select='.urlencode($row["Name"]).'" title="'.($row["Engine"]=="MyISAM"?lang('%d row(s)',$row["Rows"]):lang('around %d row(s)',$row["Rows"])).'">'.lang('select').'</a> ';echo'<a href="'.htmlspecialchars($SELF).(isset($row["Engine"])?'table':'view').'='.urlencode($row["Name"]).'" title="'.(isset($row["Engine"])?htmlspecialchars($row["Engine"]):lang('View')).'">'.htmlspecialchars($row["Name"])."</a><br />\n";}echo"</p>\n";}echo'<p><a href="'.htmlspecialchars($SELF).'create=">'.lang('Create new table')."</a></p>\n";$result->free();}}?>
</div>

</body>
</html>
<?php
}if(extension_loaded("mysqli")){class
Min_MySQLi
extends
MySQLi{var$extension="MySQLi";function
Min_MySQLi(){$this->init();}function
connect($server,$username,$password){list($host,$port)=explode(":",$server,2);return@$this->real_connect((strlen($server)?$host:ini_get("mysqli.default_host")),(strlen("$server$username")?$username:ini_get("mysqli.default_user")),(strlen("$server$username$password")?$password:ini_get("mysqli.default_pw")),null,(strlen($port)?$port:ini_get("mysqli.default_port")));}function
result($result,$field=0){$row=$result->fetch_array();return$row[$field];}}$mysql=new
Min_MySQLi;}elseif(extension_loaded("mysql")){class
Min_MySQL{var$extension="MySQL",$_link,$_result,$server_info,$affected_rows,$error;function
connect($server,$username,$password){$this->_link=@mysql_connect((strlen($server)?$server:ini_get("mysql.default_host")),(strlen("$server$username")?$username:ini_get("mysql.default_user")),(strlen("$server$username$password")?$password:ini_get("mysql.default_password")),131072);if($this->_link){$this->server_info=mysql_get_server_info($this->_link);}return(bool)$this->_link;}function
select_db($database){return
mysql_select_db($database,$this->_link);}function
query($query){$result=mysql_query($query,$this->_link);if(!$result){$this->error=mysql_error($this->_link);return
false;}elseif($result===true){$this->affected_rows=mysql_affected_rows($this->_link);return
true;}return
new
Min_MySQLResult($result);}function
multi_query($query){return$this->_result=$this->query($query);}function
store_result(){return$this->_result;}function
next_result(){return
false;}function
result($result,$field=0){return
mysql_result($result->_result,0,$field);}function
escape_string($string){return
mysql_real_escape_string($string,$this->_link);}}class
Min_MySQLResult{var$_result,$_offset=0,$num_rows;function
Min_MySQLResult($result){$this->_result=$result;$this->num_rows=mysql_num_rows($result);}function
fetch_assoc(){return
mysql_fetch_assoc($this->_result);}function
fetch_row(){return
mysql_fetch_row($this->_result);}function
fetch_field(){$row=mysql_fetch_field($this->_result,$this->_offset++);$row->orgtable=$row->table;$row->orgname=$row->name;$row->charsetnr=($row->blob?63:0);return$row;}function
free(){return
mysql_free_result($this->_result);}}$mysql=new
Min_MySQL;}elseif(extension_loaded("pdo_mysql")){class
Min_PDO_MySQL
extends
PDO{var$extension="PDO_MySQL",$_result,$server_info,$affected_rows,$error;function
__construct(){}function
connect($server,$username,$password){set_exception_handler('auth_error');parent::__construct("mysql:host=".str_replace(":",";port=",$server),$username,$password);restore_exception_handler();$this->setAttribute(13,array('Min_PDOStatement'));$this->server_info=$this->result($this->query("SELECT VERSION()"));return
true;}function
select_db($database){return$this->query("USE ".idf_escape($database));}function
query($query){$result=parent::query($query);if(!$result){$errorInfo=$this->errorInfo();$this->error=$errorInfo[2];return
false;}$this->_result=$result;if(!$result->columnCount()){$this->affected_rows=$result->rowCount();return
true;}$result->num_rows=$result->rowCount();return$result;}function
multi_query($query){return$this->query($query);}function
store_result(){return($this->_result->columnCount()?$this->_result:true);}function
next_result(){return$this->_result->nextRowset();}function
result($result,$field=0){$row=$result->fetch();return$row[$field];}function
escape_string($string){return
substr($this->quote($string),1,-1);}}class
Min_PDOStatement
extends
PDOStatement{var$_offset=0,$num_rows;function
fetch_assoc(){return$this->fetch(2);}function
fetch_row(){return$this->fetch(3);}function
fetch_field(){$row=(object)$this->getColumnMeta($this->_offset++);$row->orgtable=$row->table;$row->orgname=$row->name;$row->charsetnr=(in_array("blob",$row->flags)?63:0);return$row;}function
free(){}}$mysql=new
Min_PDO_MySQL;}else{page_header(lang('No MySQL extension'),lang('None of supported PHP extensions (%s) are available.','MySQLi, MySQL, PDO'),null);page_footer("auth");exit;}$ignore=array("server","username","password");if(ini_get("session.use_trans_sid")&&isset($_POST[session_name()])){$ignore[]=session_name();}if(isset($_POST["server"])){if(isset($_REQUEST[session_name()])){session_regenerate_id();$_SESSION["usernames"][$_POST["server"]]=$_POST["username"];$_SESSION["passwords"][$_POST["server"]]=$_POST["password"];if(count($_POST)==count($ignore)){if((string)$_GET["server"]===$_POST["server"]){$location=remove_from_uri();}else{$location=preg_replace('~^[^?]*/([^?]*).*~','\\1',$_SERVER["REQUEST_URI"]).(strlen($_POST["server"])?'?server='.urlencode($_POST["server"]):'');}if(!isset($_COOKIE[session_name()])){$location.=(strpos($location,"?")===false?"?":"&").SID;}header("Location: ".(strlen($location)?$location:"."));exit;}}$_GET["server"]=$_POST["server"];}elseif(isset($_POST["logout"])){if($_POST["token"]!=$_SESSION["tokens"][$_GET["server"]]["?logout"]){page_header(lang('Logout'),lang('Invalid CSRF token. Send the form again.'));page_footer("db");exit;}else{unset($_SESSION["usernames"][$_GET["server"]]);unset($_SESSION["passwords"][$_GET["server"]]);unset($_SESSION["databases"][$_GET["server"]]);$_SESSION["tokens"][$_GET["server"]]=array();redirect(substr($SELF,0,-1),lang('Logout successful.'));}}function
auth_error(){global$ignore;$username=$_SESSION["usernames"][$_GET["server"]];if($_POST["token"]&&!isset($username)){$_POST["token"]=token();}unset($_SESSION["usernames"][$_GET["server"]]);page_header(lang('Login'),(isset($username)?lang('Invalid credentials.'):(isset($_POST["server"])?lang('Sessions must be enabled.'):($_POST?lang('Session expired, please login again.'):""))),null);?>
	<form action="" method="post">
	<table border="0" cellspacing="0" cellpadding="2">
	<tr><th><?php echo
lang('Server');?>:</th><td><input name="server" value="<?php echo
htmlspecialchars($_GET["server"]);?>" /></td></tr>
	<tr><th><?php echo
lang('Username');?>:</th><td><input name="username" value="<?php echo
htmlspecialchars($username);?>" /></td></tr>
	<tr><th><?php echo
lang('Password');?>:</th><td><input type="password" name="password" /></td></tr>
	</table>
	<p>
<?php
$process=$_POST;while(list($key,$val)=each($process)){if(is_array($val)){foreach($val
as$k=>$v){$process[$key."[$k]"]=$v;}}elseif(!in_array($key,$ignore)){echo'<input type="hidden" name="'.htmlspecialchars($key).'" value="'.htmlspecialchars($val).'" />';}}foreach($_FILES
as$key=>$val){echo'<input type="hidden" name="files['.htmlspecialchars($key).']" value="'.($val["error"]?$val["error"]:base64_encode(file_get_contents($val["tmp_name"]))).'" />';}?>
	<input type="submit" value="<?php echo
lang('Login');?>" />
	</p>
	</form>
<?php

page_footer("auth");}$username=$_SESSION["usernames"][$_GET["server"]];if(!isset($username)||!$mysql->connect($_GET["server"],$username,$_SESSION["passwords"][$_GET["server"]])){auth_error();exit;}$mysql->query("SET SQL_QUOTE_SHOW_CREATE=1");if(!(strlen($_GET["db"])?$mysql->select_db($_GET["db"]):isset($_GET["sql"])||isset($_GET["dump"])||isset($_GET["database"])||isset($_GET["processlist"])||isset($_GET["privileges"])||isset($_GET["user"]))){if(strlen($_GET["db"])){unset($_SESSION["databases"][$_GET["server"]]);}if(strlen($_GET["db"])){page_header(lang('Database').": ".htmlspecialchars($_GET["db"]),lang('Invalid database.'),false);}else{page_header(lang('Select database'),"",null);echo'<p><a href="'.htmlspecialchars($SELF).'database=">'.lang('Create new database')."</a></p>\n";echo'<p><a href="'.htmlspecialchars($SELF).'privileges=">'.lang('Privileges')."</a></p>\n";echo'<p><a href="'.htmlspecialchars($SELF).'processlist=">'.lang('Process list')."</a></p>\n";echo"<p>".lang('MySQL version: %s through PHP extension %s',"<b>$mysql->server_info</b>","<b>$mysql->extension</b>")."</p>\n";echo"<p>".lang('Logged as: %s',"<b>".htmlspecialchars($mysql->result($mysql->query("SELECT USER()")))."</b>")."</p>\n";}page_footer("db");exit;}$mysql->query("SET CHARACTER SET utf8");function
input($name,$field,$value){global$types;$name=htmlspecialchars(bracket_escape($name));$onchange=($field["null"]?' onchange="this.form[\'null['.addcslashes($name,"\r\n'\\").']\'].checked = false;"':'');if($field["type"]=="enum"){if(!isset($_GET["default"])){echo'<input type="radio" name="fields['.$name.']" value="0"'.($value===0?' checked="checked"':'').' />';}preg_match_all("~'((?:[^']+|'')*)'~",$field["length"],$matches);foreach($matches[1]as$i=>$val){$val=stripcslashes(str_replace("''","'",$val));$id="field-$name-".($i+1);$checked=(is_int($value)?$value==$i+1:$value===$val);echo' <label for="'.$id.'"><input type="radio" name="fields['.$name.']" id="'.$id.'" value="'.(isset($_GET["default"])?htmlspecialchars($val):$i+1).'"'.($checked?' checked="checked"':'').' />'.htmlspecialchars($val).'</label>';}if($field["null"]){$id="field-$name-";echo' <label for="'.$id.'"><input type="radio" name="fields['.$name.']" id="'.$id.'" value=""'.(strlen($value)?'':' checked="checked"').' />'.lang('NULL').'</label>';}}elseif($field["type"]=="set"){preg_match_all("~'((?:[^']+|'')*)'~",$field["length"],$matches);foreach($matches[1]as$i=>$val){$val=stripcslashes(str_replace("''","'",$val));$id="field-$name-".($i+1);$checked=(is_int($value)?($value>>$i)&1:in_array($val,explode(",",$value),true));echo' <input type="checkbox" name="fields['.$name.']['.$i.']" id="'.$id.'" value="'.(isset($_GET["default"])?htmlspecialchars($val):1<<$i).'"'.($checked?' checked="checked"':'').$onchange.' /><label for="'.$id.'">'.htmlspecialchars($val).'</label>';}}elseif(strpos($field["type"],"text")!==false){echo'<textarea name="fields['.$name.']" cols="50" rows="12"'.$onchange.'>'.htmlspecialchars($value).'</textarea>';}elseif(preg_match('~binary|blob~',$field["type"])){echo(ini_get("file_uploads")?'<input type="file" name="'.$name.'"'.$onchange.' />':lang('File uploads are disabled.').' ');}else{echo'<input name="fields['.$name.']" value="'.htmlspecialchars($value).'"'.(preg_match('~^([0-9]+)(,([0-9]+))?$~',$field["length"],$match)?" maxlength='".($match[1]+($match[3]?1:0)+($match[2]&&!$field["unsigned"]?1:0))."'":($types[$field["type"]]?" maxlength='".$types[$field["type"]]."'":'')).$onchange.' />';}if($field["null"]&&$field["type"]!="enum"){$id="null-$name";echo'<label for="'.$id.'"><input type="checkbox" name="null['.$name.']" value="1" id="'.$id.'"'.(isset($value)?'':' checked="checked"').' />'.lang('NULL').'</label>';}}function
process_input($name,$field){global$mysql;$name=bracket_escape($name);$value=$_POST["fields"][$name];if($field["type"]!="enum"&&!$field["auto_increment"]?$_POST["null"][$name]:!strlen($value)){return"NULL";}elseif($field["type"]=="enum"){return(isset($_GET["default"])?"'".$mysql->escape_string($value)."'":intval($value));}elseif($field["type"]=="set"){return(isset($_GET["default"])?"'".implode(",",array_map(array($mysql,'escape_string'),(array)$value))."'":array_sum((array)$value));}elseif(preg_match('~binary|blob~',$field["type"])){$file=get_file($name);if(!is_string($file)&&($file!=UPLOAD_ERR_NO_FILE||!$field["null"])){return
false;}return"_binary'".(is_string($file)?$mysql->escape_string($file):"")."'";}elseif($field["type"]=="timestamp"&&$value=="CURRENT_TIMESTAMP"){return$value;}else{return"'".$mysql->escape_string($value)."'";}}function
edit_type($key,$field,$collations){global$types,$unsigned,$inout;?>
<td><select name="<?php echo$key;?>[type]" onchange="type_change(this);"><?php echo
optionlist(array_keys($types),$field["type"]);?></select></td>
<td><input name="<?php echo$key;?>[length]" value="<?php echo
htmlspecialchars($field["length"]);?>" size="3" /></td>
<td><select name="<?php echo$key;?>[collation]"><option value="">(<?php echo
lang('collation');?>)</option><?php echo
optionlist($collations,$field["collation"]);?></select> <select name="<?php echo$key;?>[unsigned]"><?php echo
optionlist($unsigned,$field["unsigned"]);?></select></td>
<?php
}function
process_type($field,$collate="COLLATE"){global$mysql,$enum_length,$unsigned;return" $field[type]".($field["length"]&&!preg_match('~^date|time$~',$field["type"])?"(".process_length($field["length"]).")":"").(preg_match('~int|float|double|decimal~',$field["type"])&&in_array($field["unsigned"],$unsigned)?" $field[unsigned]":"").(preg_match('~char|text|enum|set~',$field["type"])&&$field["collation"]?" $collate '".$mysql->escape_string($field["collation"])."'":"");}function
edit_fields($fields,$collations,$type="TABLE"){global$inout;?>
<thead><tr>
<?php if($type=="PROCEDURE"){?><td><?php echo
lang('IN-OUT');?></td><?php }?>
<th><?php echo($type=="TABLE"?lang('Column name'):lang('Parameter name'));?></th>
<td><?php echo
lang('Type');?></td>
<td><?php echo
lang('Length');?></td>
<td><?php echo
lang('Options');?></td>
<?php if($type=="TABLE"){?>
<td><?php echo
lang('NULL');?></td>
<td><input type="radio" name="auto_increment_col" value="" /><?php echo
lang('Auto Increment');?></td>
<td><?php echo
lang('Comment');?></td>
<?php }?>
<td><input type="image" name="add[0]" src="<?php echo
preg_replace("~\\?.*~","",$_SERVER["REQUEST_URI"])."?file=plus.gif";?>" title="<?php echo
lang('Add next');?>" /></td>
</tr></thead>
<?php
$column_comments=false;foreach($fields
as$i=>$field){$i++;$display=($_POST["add"][$i-1]||(isset($field["field"])&&!$_POST["drop_col"][$i]));?>
<tr<?php echo($display?"":" style='display: none;'");?>>
<?php if($type=="PROCEDURE"){?><td><select name="fields[<?php echo$i;?>][inout]"><?php echo
optionlist($inout,$field["inout"]);?></select></td><?php }?>
<th><?php if($display){?><input name="fields[<?php echo$i;?>][field]" value="<?php echo
htmlspecialchars($field["field"]);?>" maxlength="64" /><?php }?><input type="hidden" name="fields[<?php echo$i;?>][orig]" value="<?php echo
htmlspecialchars($field[($_POST?"orig":"field")]);?>" /></th>
<?php edit_type("fields[$i]",$field,$collations);?>
<?php if($type=="TABLE"){?>
<td><input type="checkbox" name="fields[<?php echo$i;?>][null]" value="1"<?php if($field["null"]){?> checked="checked"<?php }?> /></td>
<td><input type="radio" name="auto_increment_col" value="<?php echo$i;?>"<?php if($field["auto_increment"]){?> checked="checked"<?php }?> /></td>
<td><input name="fields[<?php echo$i;?>][comment]" value="<?php echo
htmlspecialchars($field["comment"]);?>" maxlength="255" /></td>
<?php }?>
<td style="white-space: nowrap;">
<input type="image" name="add[<?php echo$i;?>]" src="<?php echo
preg_replace("~\\?.*~","",$_SERVER["REQUEST_URI"])."?file=plus.gif";?>" title="<?php echo
lang('Add next');?>" onclick="return !add_row(this);" />
<input type="image" name="drop_col[<?php echo$i;?>]" src="<?php echo
preg_replace("~\\?.*~","",$_SERVER["REQUEST_URI"])."?file=minus.gif";?>" title="<?php echo
lang('Remove');?>" onclick="return !remove_row(this);" />
<input type="image" name="up[<?php echo$i;?>]" src="<?php echo
preg_replace("~\\?.*~","",$_SERVER["REQUEST_URI"])."?file=up.gif";?>" title="<?php echo
lang('Move up');?>" />
<input type="image" name="down[<?php echo$i;?>]" src="<?php echo
preg_replace("~\\?.*~","",$_SERVER["REQUEST_URI"])."?file=down.gif";?>" title="<?php echo
lang('Move down');?>" />
</td>
</tr>
<?php

if(strlen($field["comment"])){$column_comments=true;}}return$column_comments;}function
process_fields(&$fields){ksort($fields);$offset=0;if($_POST["up"]){$last=0;foreach($fields
as$key=>$field){if(key($_POST["up"])==$key){unset($fields[$key]);array_splice($fields,$last,0,array($field));break;}if(isset($field["field"])){$last=$offset;}$offset++;}}if($_POST["down"]){$found=false;foreach($fields
as$key=>$field){if(isset($field["field"])&&$found){unset($fields[key($_POST["down"])]);array_splice($fields,$offset,0,array($found));break;}if(key($_POST["down"])==$key){$found=$field;}$offset++;}}$fields=array_values($fields);if($_POST["add"]){array_splice($fields,key($_POST["add"]),0,array(array()));}}function
type_change($count){?>
<script type="text/javascript">
var added = '.';
function add_row(button) {
	var match = /([0-9]+)(\.[0-9]+)?/.exec(button.name)
	var x = match[0] + (match[2] ? added.substr(match[2].length) : added) + '1';
	var row = button.parentNode.parentNode;
	var row2 = row.cloneNode(true);
	var tags = row.getElementsByTagName('select');
	var tags2 = row2.getElementsByTagName('select');
	for (var i=0; i < tags.length; i++) {
		tags[i].name = tags[i].name.replace(/([0-9.]+)/, x);
		tags2[i].selectedIndex = tags[i].selectedIndex;
	}
	tags = row.getElementsByTagName('input');
	for (var i=0; i < tags.length; i++) {
		if (tags[i].name == 'auto_increment_col') {
			tags[i].value = x;
			tags[i].checked = false;
		}
		tags[i].name = tags[i].name.replace(/([0-9.]+)/, x);
		if (/\[(orig|field|comment)/.test(tags[i].name)) {
			tags[i].value = '';
		}
	}
	row.parentNode.insertBefore(row2, row);
	tags[0].focus();
	added += '0';
	return true;
}
function remove_row(button) {
	var field = button.form[button.name.replace(/drop_col(.+)/, 'fields$1[field]')];
	field.parentNode.removeChild(field);
	button.parentNode.parentNode.style.display = 'none';
	return true;
}
function type_change(type) {
	var name = type.name.substr(0, type.name.length - 6);
	type.form[name + '[collation]'].style.display = (/char|text|enum|set/.test(type.options[type.selectedIndex].text) ? '' : 'none');
	type.form[name + '[unsigned]'].style.display = (/int|float|double|decimal/.test(type.options[type.selectedIndex].text) ? '' : 'none');
}
for (var i=1; <?php echo$count;?> >= i; i++) {
	document.getElementById('form')['fields[' + i + '][type]'].onchange();
}
</script>
<?php
}function
normalize_enum($match){return"'".str_replace("'","''",addcslashes(stripcslashes(str_replace($match[0]{0}.$match[0]{0},$match[0]{0},substr($match[0],1,-1))),'\\'))."'";}function
routine($name,$type){global$mysql,$enum_length,$inout;$aliases=array("bit"=>"tinyint","bool"=>"tinyint","boolean"=>"tinyint","integer"=>"int","double precision"=>"float","real"=>"float","dec"=>"decimal","numeric"=>"decimal","fixed"=>"decimal","national char"=>"char","national varchar"=>"varchar");$type_pattern="([a-z]+)(?:\\s*\\(((?:[^'\")]*|$enum_length)+)\\))?\\s*(zerofill\\s*)?(unsigned(?:\\s+zerofill)?)?(?:\\s*(?:CHARSET|CHARACTER\\s+SET)\\s*['\"]?([^'\"\\s]+)['\"]?)?";$pattern="\\s*(".($type=="FUNCTION"?"":implode("|",$inout)).")?\\s*(?:`((?:[^`]+|``)*)`\\s*|\\b(\\S+)\\s+)$type_pattern";$create=$mysql->result($mysql->query("SHOW CREATE $type ".idf_escape($name)),2);preg_match("~\\(((?:$pattern\\s*,?)*)\\)".($type=="FUNCTION"?"\\s*RETURNS\\s+$type_pattern":"")."\\s*(.*)~is",$create,$match);$fields=array();preg_match_all("~$pattern\\s*,?~is",$match[1],$matches,PREG_SET_ORDER);foreach($matches
as$i=>$param){$data_type=strtolower($param[4]);$fields[$i]=array("field"=>str_replace("``","`",$param[2]).$param[3],"type"=>(isset($aliases[$data_type])?$aliases[$data_type]:$data_type),"length"=>preg_replace_callback("~$enum_length~s",'normalize_enum',$param[5]),"unsigned"=>strtolower(preg_replace('~\\s+~',' ',trim("$param[7] $param[6]"))),"inout"=>strtoupper($param[1]),"collation"=>strtolower($param[8]),);}if($type!="FUNCTION"){return
array("fields"=>$fields,"definition"=>$match[10]);}$returns=array("type"=>$match[10],"length"=>$match[11],"unsigned"=>$match[13],"collation"=>$match[14]);return
array("fields"=>$fields,"returns"=>$returns,"definition"=>$match[15]);}if(isset($_GET["dump"])){header("Content-Type: text/plain; charset=utf-8");$filename=(strlen($_GET["db"])?preg_replace('~[^a-z0-9_]~i','-',(strlen($_GET["dump"])?$_GET["dump"]:$_GET["db"])):"dump");header("Content-Disposition: inline; filename=$filename.sql");function
dump_table($table,$data=true){global$mysql,$max_packet;$result=$mysql->query("SHOW CREATE TABLE ".idf_escape($table));if($result){echo$mysql->result($result,1).";\n\n";if($max_packet<1073741824){$row_size=21+strlen(idf_escape($table));foreach(fields($table)as$field){$type=$types[$field["type"]];$row_size+=5+($field["length"]?$field["length"]:$type)*(preg_match('~char|text|enum~',$field["type"])?3:1);}if($row_size>$max_packet){$max_packet=1024*ceil($row_size/1024);echo"SET max_allowed_packet = $max_packet, GLOBAL max_allowed_packet = $max_packet;\n";}}$result->free();if($data){$result=$mysql->query("SELECT * FROM ".idf_escape($table));if($result){if($result->num_rows){$insert="INSERT INTO ".idf_escape($table)." VALUES ";$length=0;while($row=$result->fetch_row()){foreach($row
as$key=>$val){$row[$key]=(isset($val)?"'".$mysql->escape_string($val)."'":"NULL");}$s="(".implode(", ",$row).")";if(!$length){echo$insert,$s;$length=strlen($insert)+strlen($s);}else{$length+=2+strlen($s);if($length<$max_packet){echo", ",$s;}else{echo";\n",$insert,$s;$length=strlen($insert)+strlen($s);}}}echo";\n";}$result->free();}echo"\n";}}if($mysql->server_info>=5){$result=$mysql->query("SHOW TRIGGERS LIKE '".$mysql->escape_string(addcslashes($table,"%_"))."'");if($result->num_rows){echo"DELIMITER ;;\n\n";while($row=$result->fetch_assoc()){echo"CREATE TRIGGER ".idf_escape($row["Trigger"])." $row[Timing] $row[Event] ON ".idf_escape($row["Table"])." FOR EACH ROW $row[Statement];;\n\n";}echo"DELIMITER ;\n\n";}$result->free();}}function
dump($db){global$mysql;static$routines;if(!isset($routines)){$routines=array();if($mysql->server_info>=5){foreach(array("FUNCTION","PROCEDURE")as$routine){$result=$mysql->query("SHOW $routine STATUS");while($row=$result->fetch_assoc()){if(!strlen($_GET["db"])||$row["Db"]===$_GET["db"]){$routines[$row["Db"]][]=$mysql->result($mysql->query("SHOW CREATE $routine ".idf_escape($row["Db"]).".".idf_escape($row["Name"])),2).";;\n\n";}}$result->free();}}}$views=array();$result=$mysql->query("SHOW TABLE STATUS");while($row=$result->fetch_assoc()){if(isset($row["Engine"])){dump_table($row["Name"]);}else{$views[]=$row["Name"];}}$result->free();foreach($views
as$view){dump_table($view,false);}if($routines[$db]){echo"DELIMITER ;;\n\n".implode("",$routines[$db])."DELIMITER ;\n\n";}echo"\n\n";}$max_packet=16777216;echo"SET NAMES utf8;\n";echo"SET foreign_key_checks = 0;\n";echo"SET time_zone = '".$mysql->escape_string($mysql->result($mysql->query("SELECT @@time_zone")))."';\n";echo"SET max_allowed_packet = $max_packet, GLOBAL max_allowed_packet = $max_packet;\n";echo"\n";if(!strlen($_GET["db"])){$result=$mysql->query("SHOW DATABASES");while($row=$result->fetch_assoc()){if($row["Database"]!="information_schema"||$mysql->server_info<5){if($mysql->select_db($row["Database"])){$result1=$mysql->query("SHOW CREATE DATABASE ".idf_escape($row["Database"]));if($result1){echo$mysql->result($result1,1).";\n";$result1->free();}echo"USE ".idf_escape($row["Database"]).";\n";dump($row["Database"]);}}}$result->free();}elseif(strlen($_GET["dump"])){dump_table($_GET["dump"]);}else{dump($_GET["db"]);}}elseif(isset($_GET["download"])){header("Content-Type: application/octet-stream");echo$mysql->result($mysql->query("SELECT ".idf_escape($_GET["field"])." FROM ".idf_escape($_GET["download"])." WHERE ".implode(" AND ",where())." LIMIT 1"));}else{$on_actions=array("RESTRICT","CASCADE","SET NULL","NO ACTION");$types=array("tinyint"=>3,"smallint"=>5,"mediumint"=>8,"int"=>10,"bigint"=>20,"float"=>12,"double"=>21,"decimal"=>66,"date"=>10,"datetime"=>19,"timestamp"=>19,"time"=>10,"year"=>4,"char"=>255,"varchar"=>65535,"binary"=>255,"varbinary"=>65535,"tinytext"=>255,"text"=>65535,"mediumtext"=>16777215,"longtext"=>4294967295,"tinyblob"=>255,"blob"=>65535,"mediumblob"=>16777215,"longblob"=>4294967295,"enum"=>65535,"set"=>64,);$unsigned=array("","unsigned","zerofill","unsigned zerofill");$enum_length='\'(?:\'\'|[^\'\\\\]+|\\\\.)*\'|"(?:""|[^"\\\\]+|\\\\.)*"';$inout=array("IN","OUT","INOUT");if(isset($_GET["table"])){$result=$mysql->query("SHOW COLUMNS FROM ".idf_escape($_GET["table"]));if(!$result){$error=$mysql->error;}page_header(lang('Table').": ".htmlspecialchars($_GET["table"]),$error);if($result){$table_status=table_status($_GET["table"]);$auto_increment_only=true;echo"<table border='1' cellspacing='0' cellpadding='2'>\n";while($row=$result->fetch_assoc()){if(!$row["auto_increment"]){$auto_increment_only=false;}echo"<tr><th>".htmlspecialchars($row["Field"])."</th><td>$row[Type]".($row["Null"]=="YES"?" <i>NULL</i>":"")."</td></tr>\n";}echo"</table>\n";$result->free();echo"<p>";echo'<a href="'.htmlspecialchars($SELF).'create='.urlencode($_GET["table"]).'">'.lang('Alter table').'</a>';echo($auto_increment_only?'':' <a href="'.htmlspecialchars($SELF).'default='.urlencode($_GET["table"]).'">'.lang('Default values').'</a>');echo"</p>\n";echo"<h3>".lang('Indexes')."</h3>\n";$indexes=indexes($_GET["table"]);if($indexes){echo"<table border='1' cellspacing='0' cellpadding='2'>\n";foreach($indexes
as$index){ksort($index["columns"]);$print=array();foreach($index["columns"]as$key=>$val){$print[]="<i>".htmlspecialchars($val)."</i>".($index["lengths"][$key]?"(".$index["lengths"][$key].")":"");}echo"<tr><td>$index[type]</td><td>".implode(", ",$print)."</td></tr>\n";}echo"</table>\n";}echo'<p><a href="'.htmlspecialchars($SELF).'indexes='.urlencode($_GET["table"]).'">'.lang('Alter indexes')."</a></p>\n";if($table_status["Engine"]=="InnoDB"){echo"<h3>".lang('Foreign keys')."</h3>\n";$foreign_keys=foreign_keys($_GET["table"]);if($foreign_keys){echo"<table border='1' cellspacing='0' cellpadding='2'>\n";foreach($foreign_keys
as$name=>$foreign_key){echo"<tr>";echo"<td><i>".implode("</i>, <i>",array_map('htmlspecialchars',$foreign_key["source"]))."</i></td>";$link=(strlen($foreign_key["db"])?"<strong>".htmlspecialchars($foreign_key["db"])."</strong>.":"").htmlspecialchars($foreign_key["table"]);echo'<td><a href="'.htmlspecialchars(strlen($foreign_key["db"])?preg_replace('~db=[^&]*~',"db=".urlencode($foreign_key["db"]),$SELF):$SELF)."table=".urlencode($foreign_key["table"])."\">$link</a>";echo"(<em>".implode("</em>, <em>",array_map('htmlspecialchars',$foreign_key["target"]))."</em>)</td>";echo'<td>'.(!strlen($foreign_key["db"])?'<a href="'.htmlspecialchars($SELF).'foreign='.urlencode($_GET["table"]).'&amp;name='.urlencode($name).'">'.lang('Alter').'</a>':'&nbsp;').'</td>';echo"</tr>\n";}echo"</table>\n";}echo'<p><a href="'.htmlspecialchars($SELF).'foreign='.urlencode($_GET["table"]).'">'.lang('Add foreign key')."</a></p>\n";}}if($mysql->server_info>=5){echo"<h3>".lang('Triggers')."</h3>\n";$result=$mysql->query("SHOW TRIGGERS LIKE '".$mysql->escape_string(addcslashes($_GET["table"],"%_"))."'");if($result->num_rows){echo"<table border='0' cellspacing='0' cellpadding='2'>\n";while($row=$result->fetch_assoc()){echo"<tr valign='top'><td>$row[Timing]</td><td>$row[Event]</td><th>".htmlspecialchars($row["Trigger"])."</th><td><a href=\"".htmlspecialchars($SELF).'trigger='.urlencode($_GET["table"]).'&amp;name='.urlencode($row["Trigger"]).'">'.lang('Alter')."</a></td></tr>\n";}echo"</table>\n";}$result->free();echo'<p><a href="'.htmlspecialchars($SELF).'trigger='.urlencode($_GET["table"]).'">'.lang('Add trigger')."</a></p>\n";}}elseif(isset($_GET["view"])){page_header(lang('View').": ".htmlspecialchars($_GET["view"]));$view=view($_GET["view"]);echo"<pre class='jush-sql'>".htmlspecialchars($view["select"])."</pre>\n";echo'<p><a href="'.htmlspecialchars($SELF).'createv='.urlencode($_GET["view"]).'">'.lang('Alter view')."</a></p>\n";}elseif(isset($_GET["schema"])){page_header(lang('Database schema'),"",array(),$_GET["db"]);$table_pos=array();$table_pos_js=array();preg_match_all('~([^:]+):([-0-9.]+)x([-0-9.]+)(_|$)~',$_COOKIE["schema"],$matches,PREG_SET_ORDER);foreach($matches
as$i=>$match){$table_pos[$match[1]]=array($match[2],$match[3]);$table_pos_js[]="\n\t'".addcslashes($match[1],"\r\n'\\")."': [ $match[2], $match[3] ]";}$top=0;$base_left=-1;$schema=array();$referenced=array();$lefts=array();$result=$mysql->query("SHOW TABLE STATUS");while($row=$result->fetch_assoc()){if(!isset($row["Engine"])){continue;}$pos=0;$schema[$row["Name"]]["fields"]=array();foreach(fields($row["Name"])as$name=>$field){$pos+=1.25;$field["pos"]=$pos;$schema[$row["Name"]]["fields"][$name]=$field;}$schema[$row["Name"]]["pos"]=($table_pos[$row["Name"]]?$table_pos[$row["Name"]]:array($top,0));if($row["Engine"]=="InnoDB"){foreach(foreign_keys($row["Name"])as$val){if(!$val["db"]){if($table_pos[$row["Name"]][1]||$table_pos[$row["Name"]][1]){$left=min($table_pos[$row["Name"]][1],$table_pos[$val["table"]][1])-1;}else{$left=$base_left;$base_left-=.1;}while($lefts[(string)$left]){$left-=.0001;}$schema[$row["Name"]]["references"][$val["table"]][(string)$left]=array_combine($val["source"],$val["target"]);$referenced[$val["table"]][$row["Name"]][(string)$left]=$val["target"];$lefts[(string)$left]=true;}}}$top=max($top,$schema[$row["Name"]]["pos"][0]+2.5+$pos);}$result->free();?>
<script type="text/javascript">
var that, x, y, em;
var table_pos = {<?php echo
implode(",",$table_pos_js)."\n";?>};

function mousedown(el, event) {
	that = el;
	em = document.getElementById('schema').offsetHeight / <?php echo$top;?>;
	x = event.clientX - el.offsetLeft;
	y = event.clientY - el.offsetTop;
}
document.onmousemove = function (ev) {
	if (that !== undefined) {
		ev = ev || event;
		var left = (ev.clientX - x) / em;
		var top = (ev.clientY - y) / em;
		var divs = that.getElementsByTagName('div');
		var line_set = { };
		for (var i=0; i < divs.length; i++) {
			if (divs[i].className == 'references') {
				var div2 = document.getElementById((divs[i].id.substr(0, 4) == 'refs' ? 'refd' : 'refs') + divs[i].id.substr(4));
				var ref = (table_pos[divs[i].title] ? table_pos[divs[i].title] : [ div2.parentNode.offsetTop / em, 0 ]);
				var left1 = -1;
				var is_top = true;
				var id = divs[i].id.replace(/^ref.(.+)-.+/, '$1');
				if (divs[i].parentNode != div2.parentNode) {
					left1 = Math.min(0, ref[1] - left) - 1;
					divs[i].style.left = left1 + 'em';
					divs[i].getElementsByTagName('div')[0].style.width = -left1 + 'em';
					var left2 = Math.min(0, left - ref[1]) - 1;
					div2.style.left = left2 + 'em';
					div2.getElementsByTagName('div')[0].style.width = -left2 + 'em';
					is_top = (divs[i].offsetTop + top * em < div2.offsetTop + ref[0] * em);
				}
				if (!line_set[id]) {
					var line = document.getElementById(divs[i].id.replace(/^....(.+)-[0-9]+$/, 'refl$1'));
					var shift = ev.clientY - y - that.offsetTop;
					line.style.left = (left + left1) + 'em';
					if (is_top) {
						line.style.top = (line.offsetTop + shift) / em + 'em';
					}
					if (divs[i].parentNode != div2.parentNode) {
						line = line.getElementsByTagName('div')[0];
						line.style.height = (line.offsetHeight + (is_top ? -1 : 1) * shift) / em + 'em';
					}
					line_set[id] = true;
				}
			}
		}
		that.style.left = left + 'em';
		that.style.top = top + 'em';
	}
}
document.onmouseup = function (ev) {
	if (that !== undefined) {
		ev = ev || event;
		table_pos[that.firstChild.firstChild.firstChild.data] = [ (ev.clientY - y) / em, (ev.clientX - x) / em ];
		that = undefined;
		var date = new Date();
		date.setMonth(date.getMonth() + 1);
		var s = '';
		for (var key in table_pos) {
			s += '_' + key + ':' + Math.round(table_pos[key][0] * 10000) / 10000 + 'x' + Math.round(table_pos[key][1] * 10000) / 10000;
		}
		document.cookie = 'schema=' + encodeURIComponent(s.substr(1)) + '; expires=' + date + '; path=' + location.pathname + location.search;
	}
}
</script>

<div id="schema" style="height: <?php echo$top;?>em;">
<?php
foreach($schema
as$name=>$table){echo"<div class='table' style='top: ".$table["pos"][0]."em; left: ".$table["pos"][1]."em;' onmousedown='mousedown(this, event);'>";echo'<a href="'.htmlspecialchars($SELF).'table='.urlencode($name).'"><strong>'.htmlspecialchars($name)."</strong></a><br />\n";foreach($table["fields"]as$field){$val=htmlspecialchars($field["field"]);if(preg_match('~char|text~',$field["type"])){$val="<span class='char'>$val</span>";}elseif(preg_match('~date|time|year~',$field["type"])){$val="<span class='date'>$val</span>";}elseif(preg_match('~binary|blob~',$field["type"])){$val="<span class='binary'>$val</span>";}elseif(preg_match('~enum|set~',$field["type"])){$val="<span class='enum'>$val</span>";}echo($field["primary"]?"<em>$val</em>":$val)."<br />\n";}foreach((array)$table["references"]as$target_name=>$refs){foreach($refs
as$left=>$columns){$left1=$left-$table_pos[$name][1];$i=0;foreach($columns
as$source=>$target){echo'<div class="references" title="'.htmlspecialchars($target_name)."\" id='refs$left-".($i++)."' style='left: $left1"."em; top: ".$table["fields"][$source]["pos"]."em; padding-top: .5em;'><div style='border-top: 1px solid Gray; width: ".(-$left1)."em;'></div></div>\n";}}}foreach((array)$referenced[$name]as$target_name=>$refs){foreach($refs
as$left=>$columns){$left1=$left-$table_pos[$name][1];$i=0;foreach($columns
as$target){echo'<div class="references" title="'.htmlspecialchars($target_name)."\" id='refd$left-".($i++)."' style='left: $left1"."em; top: ".$table["fields"][$target]["pos"]."em; height: 1.25em; background: url(".preg_replace("~\\?.*~","",$_SERVER["REQUEST_URI"])."?file=arrow.gif) no-repeat right center;'><div style='height: .5em; border-bottom: 1px solid Gray; width: ".(-$left1)."em;'></div></div>\n";}}}echo"</div>\n";}foreach($schema
as$name=>$table){foreach((array)$table["references"]as$target_name=>$refs){foreach($refs
as$left=>$ref){$min_pos=$top;$max_pos=-10;foreach($ref
as$source=>$target){$pos1=$table["pos"][0]+$table["fields"][$source]["pos"];$pos2=$schema[$target_name]["pos"][0]+$schema[$target_name]["fields"][$target]["pos"];$min_pos=min($min_pos,$pos1,$pos2);$max_pos=max($max_pos,$pos1,$pos2);}echo"<div class='references' id='refl$left' style='left: $left"."em; top: $min_pos"."em; padding: .5em 0;' /><div style='border-right: 1px solid Gray; height: ".($max_pos-$min_pos)."em;'></div></div>\n";}}}?>
</div>
<?php
}elseif(isset($_GET["privileges"])){page_header(lang('Privileges'));echo'<p><a href="'.htmlspecialchars($SELF).'user=">'.lang('Create user')."</a></p>";$result=$mysql->query("SELECT User, Host FROM mysql.user ORDER BY Host, User");if(!$result){echo"<form action=''><p>\n";if(strlen($_GET["server"])){echo'<input type="hidden" name="server" value="'.htmlspecialchars($_GET["server"]).'" />';}echo"</p></form>\n";?>
	<form action=""><p>
	<?php if(strlen($_GET["server"])){?><input type="hidden" name="server" value="<?php echo
htmlspecialchars($_GET["server"]);?>" /><?php }?>
	<?php echo
lang('Username');?>: <input name="user" />
	<?php echo
lang('Server');?>: <input name="host" value="localhost" />
	<input type="hidden" name="grant" value="" />
	<input type="submit" value="<?php echo
lang('Edit');?>" />
	</p></form>
<?php
$result=$mysql->query("SELECT SUBSTRING_INDEX(CURRENT_USER, '@', 1) AS User, SUBSTRING_INDEX(CURRENT_USER, '@', -1) AS Host");}echo"</p>\n";echo"<table border='1' cellspacing='0' cellpadding='2'>\n";echo"<thead><tr><th>&nbsp;</th><th>".lang('Username')."</th><th>".lang('Server')."</th></tr></thead>\n";while($row=$result->fetch_assoc()){echo'<tr><td><a href="'.htmlspecialchars($SELF).'user='.urlencode($row["User"]).'&amp;host='.urlencode($row["Host"]).'">'.lang('edit').'</a></td><td>'.htmlspecialchars($row["User"])."</td><td>".htmlspecialchars($row["Host"])."</td></tr>\n";}echo"</table>\n";$result->free();}else{$error="";if($_POST){if(!in_array($_POST["token"],(array)$TOKENS)){$error=lang('Invalid CSRF token. Send the form again.');}}elseif($_SERVER["REQUEST_METHOD"]=="POST"){$error=lang('Too big POST data. Reduce the data or increase the "post_max_size" configuration directive.');}$token=($_POST&&!$error?$_POST["token"]:token());if(isset($_GET["default"])){$_GET["edit"]=$_GET["default"];}if(isset($_GET["callf"])){$_GET["call"]=$_GET["callf"];}if(isset($_GET["function"])){$_GET["procedure"]=$_GET["function"];}if(isset($_GET["sql"])){if(isset($_POST["query"])){setcookie("highlight",$_POST["highlight"],strtotime("+1 month"),preg_replace('~\\?.*~','',$_SERVER["REQUEST_URI"]));$_COOKIE["highlight"]=$_POST["highlight"];}page_header(lang('SQL command'),$error);if(!$error&&$_POST&&is_string($query=(isset($_POST["query"])?$_POST["query"]:get_file("sql_file")))){$delimiter=";";$offset=0;$empty=true;while(rtrim($query)){if(!$offset&&preg_match('~^\\s*DELIMITER\\s+(.+)~i',$query,$match)){$delimiter=preg_quote($match[1],'~');$query=substr($query,strlen($match[0]));}elseif(preg_match("~$delimiter|['`\"]|/\\*|-- |#|\$~",$query,$match,PREG_OFFSET_CAPTURE,$offset)){if($match[0][0]&&$match[0][0]!=$delimiter){$pattern=($match[0][0]=="-- "||$match[0][0]=="#"?'~.*~':($match[0][0]=="/*"?'~.*\\*/~sU':'~\\G([^\\\\'.$match[0][0].']+|\\\\.)*('.$match[0][0].'|$)~s'));preg_match($pattern,$query,$match,PREG_OFFSET_CAPTURE,$match[0][1]+1);$offset=$match[0][1]+strlen($match[0][0]);}else{$empty=false;echo"<pre class='jush-sql'>".htmlspecialchars(substr($query,0,$match[0][1]))."</pre>\n";if(!$mysql->multi_query(substr($query,0,$match[0][1]))){echo"<p class='error'>".lang('Error in query').": ".htmlspecialchars($mysql->error)."</p>\n";}else{do{$result=$mysql->store_result();if(is_object($result)){select($result);}else{if(preg_match("~^\\s*(CREATE|DROP)(\\s+|/\\*.*\\*/|(#|-- )[^\n]*\n)+(DATABASE|SCHEMA)\\b~isU",$query)){unset($_SESSION["databases"][$_GET["server"]]);}echo"<p class='message'>".lang('Query executed OK, %d row(s) affected.',$mysql->affected_rows)."</p>\n";}}while($mysql->next_result());}$query=substr($query,$match[0][1]+strlen($match[0][0]));$offset=0;}}}if($empty){echo"<p class='message'>".lang('No commands to execute.')."</p>\n";}}elseif($_POST){echo"<p class='error'>".lang('Unable to upload a file.')."</p>\n";}?>

<form action="" method="post">
<p><textarea name="query" rows="20" cols="80" style="width: 98%;"><?php echo
htmlspecialchars($_POST["query"]);?></textarea></p>
<p>
<input type="hidden" name="token" value="<?php echo$token;?>" />
<input type="submit" value="<?php echo
lang('Execute');?>" />
<script type="text/javascript">
document.write('<label for="highlight"><input type="checkbox" name="highlight" id="highlight" value="jush"<?php echo($_COOKIE["highlight"]=="jush"?' checked="checked"':'');?> /><?php echo
addcslashes(lang('Syntax highlighting'),"\r\n'\\");?></label>');
</script>
</p>
</form>

<?php
if(!ini_get("file_uploads")){echo"<p>".lang('File uploads are disabled.')."</p>\n";}else{?>
<form action="" method="post" enctype="multipart/form-data">
<p>
<?php echo
lang('File upload');?>: <input type="file" name="sql_file" />
<input type="hidden" name="token" value="<?php echo$token;?>" />
<input type="submit" value="<?php echo
lang('Execute');?>" />
</p>
</form>
<?php }}elseif(isset($_GET["edit"])){$where=where();$fields=fields($_GET["edit"]);foreach($fields
as$name=>$field){if(isset($_GET["default"])?$field["auto_increment"]||preg_match('~text|blob~',$field["type"]):!isset($field["privileges"][$where?"update":"insert"])){unset($fields[$name]);}}if($_POST&&!$error){if(isset($_POST["delete"])){$set=true;$query="DELETE FROM ".idf_escape($_GET["edit"])." WHERE ".implode(" AND ",$where)." LIMIT 1";$message=lang('Item has been deleted.');}else{$set=array();foreach($fields
as$name=>$field){$val=process_input($name,$field);if($val!==false){if(!isset($_GET["default"])){$set[]=idf_escape($name)." = $val";}elseif($field["type"]=="timestamp"){$set[]=" MODIFY ".idf_escape($name)." timestamp".($field["null"]?" NULL":"")." DEFAULT $val".($_POST["on_update"][bracket_escape($name)]?" ON UPDATE CURRENT_TIMESTAMP":"");}else{$set[]=" ALTER ".idf_escape($name).($val==($field["null"]?"NULL":"''")?" DROP DEFAULT":" SET DEFAULT $val");}}}if(isset($_GET["default"])){$query="ALTER TABLE ".idf_escape($_GET["edit"]).implode(",",$set);$message=lang('Default values has been set.');}elseif($where){$query="UPDATE ".idf_escape($_GET["edit"])." SET ".implode(", ",$set)." WHERE ".implode(" AND ",$where)." LIMIT 1";$message=lang('Item has been updated.');}else{$query="INSERT INTO ".idf_escape($_GET["edit"])." SET ".implode(", ",$set);$message=lang('Item has been inserted.');}}if(!$set||$mysql->query($query)){redirect($SELF.(isset($_GET["default"])?"table=":($_POST["insert"]?"edit=":"select=")).urlencode($_GET["edit"]),($set?$message:null));}$error=$mysql->error;}page_header((isset($_GET["default"])?lang('Default values'):($_GET["where"]?lang('Edit'):lang('Insert'))),$error,array((isset($_GET["default"])?"table":"select")=>$_GET["edit"]),$_GET["edit"]);if($_POST){$row=(array)$_POST["fields"];foreach((array)$_POST["null"]as$key=>$val){$row[$key]=null;}}elseif($where){$select=array();foreach($fields
as$name=>$field){if(isset($field["privileges"]["select"])&&!preg_match('~binary|blob~',$field["type"])){$select[]=($field["type"]=="enum"||$field["type"]=="set"?"1*".idf_escape($name)." AS ":"").idf_escape($name);}}if($select){$result=$mysql->query("SELECT ".implode(", ",$select)." FROM ".idf_escape($_GET["edit"])." WHERE ".implode(" AND ",$where)." LIMIT 1");$row=$result->fetch_assoc();}else{$row=array();}}else{unset($row);}?>

<form action="" method="post" enctype="multipart/form-data">
<?php
if($fields){unset($create);echo"<table border='0' cellspacing='0' cellpadding='2'>\n";foreach($fields
as$name=>$field){echo"<tr><th>".htmlspecialchars($name)."</th><td>";if(!isset($row)){$value=$field["default"];}elseif(strlen($row[$name])&&($field["type"]=="enum"||$field["type"]=="set")){$value=intval($row[$name]);}else{$value=$row[$name];}input($name,$field,$value);if(isset($_GET["default"])&&$field["type"]=="timestamp"){$id=htmlspecialchars("on_update-$name");if(!isset($create)&&!$_POST){$create=$mysql->result($mysql->query("SHOW CREATE TABLE ".idf_escape($_GET["edit"])),1);}$checked=($_POST?$_POST["on_update"][bracket_escape($name)]:preg_match("~\n\\s*".preg_quote(idf_escape($name),'~')." timestamp.* on update CURRENT_TIMESTAMP~i",$create));echo'<label for="'.$id.'"><input type="checkbox" name="on_update['.htmlspecialchars(bracket_escape($name)).']" id="'.$id.'" value="1"'.($checked?' checked="checked"':'').' />'.lang('ON UPDATE CURRENT_TIMESTAMP').'</label>';}echo"</td></tr>\n";}echo"</table>\n";}?>
<p>
<input type="hidden" name="token" value="<?php echo$token;?>" />
<?php if($fields){?>
<input type="submit" value="<?php echo
lang('Save');?>" />
<?php if(!isset($_GET["default"])){?><input type="submit" name="insert" value="<?php echo
lang('Save and insert next');?>" /><?php }?>
<?php }?>
<?php if($where){?> <input type="submit" name="delete" value="<?php echo
lang('Delete');?>" onclick="return confirm('<?php echo
lang('Are you sure?');?>');" /><?php }?>
</p>
</form>
<?php
}elseif(isset($_GET["create"])){if($_POST&&!$error&&!$_POST["add"]&&!$_POST["drop_col"]&&!$_POST["up"]&&!$_POST["down"]){if($_POST["drop"]){if($mysql->query("DROP TABLE ".idf_escape($_GET["create"]))){redirect(substr($SELF,0,-1),lang('Table has been dropped.'));}}else{$auto_increment_index=" PRIMARY KEY";if(strlen($_GET["create"])&&strlen($_POST["fields"][$_POST["auto_increment_col"]]["orig"])){foreach(indexes($_GET["create"])as$index){foreach($index["columns"]as$column){if($column===$_POST["fields"][$_POST["auto_increment_col"]]["orig"]){$auto_increment_index="";break
2;}}if($index["type"]=="PRIMARY"){$auto_increment_index=" UNIQUE";}}}$fields=array();ksort($_POST["fields"]);$after="FIRST";foreach($_POST["fields"]as$key=>$field){if(strlen($field["field"])&&isset($types[$field["type"]])){$fields[]=(!strlen($_GET["create"])?"":(strlen($field["orig"])?"CHANGE ".idf_escape($field["orig"])." ":"ADD ")).idf_escape($field["field"]).process_type($field).($field["null"]?" NULL":" NOT NULL").($key==$_POST["auto_increment_col"]?" AUTO_INCREMENT$auto_increment_index":"")." COMMENT '".$mysql->escape_string($field["comment"])."'".(strlen($_GET["create"])?" $after":"");$after="AFTER ".idf_escape($field["field"]);}elseif(strlen($field["orig"])){$fields[]="DROP ".idf_escape($field["orig"]);}}$status=($_POST["Engine"]?" ENGINE='".$mysql->escape_string($_POST["Engine"])."'":"").($_POST["Collation"]?" COLLATE '".$mysql->escape_string($_POST["Collation"])."'":"").(strlen($_POST["Auto_increment"])?" AUTO_INCREMENT=".intval($_POST["Auto_increment"]):"")." COMMENT='".$mysql->escape_string($_POST["Comment"])."'";if(strlen($_GET["create"])){$query="ALTER TABLE ".idf_escape($_GET["create"])." ".implode(", ",$fields).", RENAME TO ".idf_escape($_POST["name"]).", $status";$message=lang('Table has been altered.');}else{$query="CREATE TABLE ".idf_escape($_POST["name"])." (".implode(", ",$fields).")$status";$message=lang('Table has been created.');}if($mysql->query($query)){redirect($SELF."table=".urlencode($_POST["name"]),$message);}}$error=$mysql->error;}page_header((strlen($_GET["create"])?lang('Alter table'):lang('Create table')),$error,array("table"=>$_GET["create"]),$_GET["create"]);$engines=array();$result=$mysql->query("SHOW ENGINES");while($row=$result->fetch_assoc()){if($row["Support"]=="YES"||$row["Support"]=="DEFAULT"){$engines[]=$row["Engine"];}}$result->free();if($_POST){$row=$_POST;process_fields($row["fields"]);if($row["auto_increment_col"]){$row["fields"][$row["auto_increment_col"]-1]["auto_increment"]=true;}}elseif(strlen($_GET["create"])){$row=table_status($_GET["create"]);if($row["Engine"]=="InnoDB"){$row["Comment"]=preg_replace('~(?:(.+); )?InnoDB free: .*~','\\1',$row["Comment"]);}$row["name"]=$_GET["create"];$row["fields"]=array_values(fields($_GET["create"]));}else{$row=array("fields"=>array(array("field"=>"")));}$collations=collations();?>

<form action="" method="post" id="form">
<p>
<?php echo
lang('Table name');?>: <input name="name" maxlength="64" value="<?php echo
htmlspecialchars($row["name"]);?>" />
<select name="Engine"><option value="">(<?php echo
lang('engine');?>)</option><?php echo
optionlist($engines,$row["Engine"]);?></select>
<select name="Collation"><option value="">(<?php echo
lang('collation');?>)</option><?php echo
optionlist($collations,$row["Collation"]);?></select>
<input type="submit" value="<?php echo
lang('Save');?>" />
</p>
<table border="0" cellspacing="0" cellpadding="2">
<?php $column_comments=edit_fields($row["fields"],$collations);?>
</table>
<?php echo
type_change(count($row["fields"]));?>
<p>
<?php echo
lang('Auto Increment');?>: <input name="Auto_increment" size="4" value="<?php echo
intval($row["Auto_increment"]);?>" />
<?php echo
lang('Comment');?>: <input name="Comment" value="<?php echo
htmlspecialchars($row["Comment"]);?>" maxlength="60" />
<script type="text/javascript">
document.write('<label for="column_comments"><input type="checkbox" id="column_comments"<?php if($column_comments){?> checked="checked"<?php }?> onclick="column_comments_click(this.checked);" /><?php echo
lang('Show column comments');?></label>');
function column_comments_click(checked) {
	var trs = document.getElementsByTagName('tr');
	for (var i=0; i < trs.length; i++) {
		trs[i].getElementsByTagName('td')[5].style.display = (checked ? '' : 'none');
	}
}
<?php if(!$column_comments){?>column_comments_click(false);<?php }?>

</script>
</p>
<p>
<input type="hidden" name="token" value="<?php echo$token;?>" />
<input type="submit" value="<?php echo
lang('Save');?>" />
<?php if(strlen($_GET["create"])){?><input type="submit" name="drop" value="<?php echo
lang('Drop');?>" onclick="return confirm('<?php echo
lang('Are you sure?');?>');" /><?php }?>
</p>
</form>
<?php
}elseif(isset($_GET["indexes"])){$index_types=array("PRIMARY","UNIQUE","INDEX","FULLTEXT");$indexes=indexes($_GET["indexes"]);if($_POST&&!$error&&!$_POST["add"]){$alter=array();foreach($_POST["indexes"]as$index){if(in_array($index["type"],$index_types)){$columns=array();$lengths=array();$set=array();ksort($index["columns"]);foreach($index["columns"]as$key=>$column){if(strlen($column)){$length=$index["lengths"][$key];$set[]=idf_escape($column).($length?"(".intval($length).")":"");$columns[count($columns)+1]=$column;$lengths[count($lengths)+1]=($length?$length:null);}}if($columns){foreach($indexes
as$name=>$existing){ksort($existing["columns"]);ksort($existing["lengths"]);if($index["type"]==$existing["type"]&&$existing["columns"]===$columns&&$existing["lengths"]===$lengths){unset($indexes[$name]);continue
2;}}$alter[]="ADD $index[type]".($index["type"]=="PRIMARY"?" KEY":"")." (".implode(", ",$set).")";}}}foreach($indexes
as$name=>$existing){$alter[]="DROP INDEX ".idf_escape($name);}if(!$alter||$mysql->query("ALTER TABLE ".idf_escape($_GET["indexes"])." ".implode(", ",$alter))){redirect($SELF."table=".urlencode($_GET["indexes"]),($alter?lang('Indexes has been altered.'):null));}$error=$mysql->error;}page_header(lang('Indexes'),$error,array("table"=>$_GET["indexes"]),$_GET["indexes"]);$fields=array_keys(fields($_GET["indexes"]));if($_POST){$row=$_POST;if($_POST["add"]){foreach($row["indexes"]as$key=>$index){if(strlen($index["columns"][count($index["columns"])])){$row["indexes"][$key]["columns"][]="";}}$index=end($row["indexes"]);if($index["type"]||array_filter($index["columns"],'strlen')||array_filter($index["lengths"],'strlen')){$row["indexes"][]=array("columns"=>array(1=>""));}}}else{$row=array("indexes"=>$indexes);foreach($row["indexes"]as$key=>$index){$row["indexes"][$key]["columns"][]="";}$row["indexes"][]=array("columns"=>array(1=>""));}?>

<script type="text/javascript">
function add_row(field) {
	var row = field.parentNode.parentNode.cloneNode(true);
	var spans = row.getElementsByTagName('span');
	row.getElementsByTagName('td')[1].innerHTML = '<span>' + spans[spans.length - 1].innerHTML + '</span>';
	var selects = row.getElementsByTagName('select');
	for (var i=0; i < selects.length; i++) {
		selects[i].name = selects[i].name.replace(/indexes\[[0-9]+/, '$&1');
		selects[i].selectedIndex = 0;
	}
	var input = row.getElementsByTagName('input')[0];
	input.name = input.name.replace(/indexes\[[0-9]+/, '$&1');
	input.value = '';
	field.parentNode.parentNode.parentNode.appendChild(row);
	field.onchange = function () { };
}

function add_column(field) {
	var column = field.parentNode.cloneNode(true);
	var select = column.getElementsByTagName('select')[0];
	select.name = select.name.replace(/\]\[[0-9]+/, '$&1');
	select.selectedIndex = 0;
	var input = column.getElementsByTagName('input')[0];
	input.name = input.name.replace(/\]\[[0-9]+/, '$&1');
	input.value = '';
	field.parentNode.parentNode.appendChild(column);
	field.onchange = function () { };
}
</script>

<form action="" method="post">
<table border="0" cellspacing="0" cellpadding="2">
<thead><tr><th><?php echo
lang('Index Type');?></th><td><?php echo
lang('Column (length)');?></td></tr></thead>
<?php
$j=0;foreach($row["indexes"]as$index){echo"<tr><td><select name='indexes[$j][type]'".($j==count($row["indexes"])-1?" onchange='add_row(this);'":"")."><option></option>".optionlist($index_types,$index["type"])."</select></td><td>\n";ksort($index["columns"]);foreach($index["columns"]as$i=>$column){echo"<span><select name='indexes[$j][columns][$i]'".($i==count($index["columns"])?" onchange='add_column(this);'":"")."><option></option>".optionlist($fields,$column)."</select>";echo"<input name='indexes[$j][lengths][$i]' size='2' value=\"".htmlspecialchars($index["lengths"][$i])."\" /></span>\n";}echo"</td></tr>\n";$j++;}?>
</table>
<p>
<input type="hidden" name="token" value="<?php echo$token;?>" />
<input type="submit" value="<?php echo
lang('Alter indexes');?>" />
</p>
<noscript><p><input type="submit" name="add" value="<?php echo
lang('Add next');?>" /></p></noscript>
</form>
<?php
}elseif(isset($_GET["database"])){if($_POST&&!$error){if($_POST["drop"]){if($mysql->query("DROP DATABASE ".idf_escape($_GET["db"]))){unset($_SESSION["databases"][$_GET["server"]]);redirect(substr(preg_replace('~db=[^&]*&~','',$SELF),0,-1),lang('Database has been dropped.'));}}elseif($_GET["db"]!==$_POST["name"]){if($mysql->query("CREATE DATABASE ".idf_escape($_POST["name"]).($_POST["collation"]?" COLLATE '".$mysql->escape_string($_POST["collation"])."'":""))){unset($_SESSION["databases"][$_GET["server"]]);if(!strlen($_GET["db"])){redirect($SELF."db=".urlencode($_POST["name"]),lang('Database has been created.'));}$result=$mysql->query("SHOW TABLES");while($row=$result->fetch_row()){if(!$mysql->query("RENAME TABLE ".idf_escape($row[0])." TO ".idf_escape($_POST["name"]).".".idf_escape($row[0]))){break;}}$result->free();if(!$row){$mysql->query("DROP DATABASE ".idf_escape($_GET["db"]));redirect(preg_replace('~db=[^&]*&~','',$SELF)."db=".urlencode($_POST["name"]),lang('Database has been renamed.'));}}}elseif(!$_POST["collation"]||$mysql->query("ALTER DATABASE ".idf_escape($_POST["name"])." COLLATE '".$mysql->escape_string($_POST["collation"])."'")){redirect(substr($SELF,0,-1),($_POST["collation"]?lang('Database has been altered.'):null));}$error=$mysql->error;}page_header(strlen($_GET["db"])?lang('Alter database'):lang('Create database'),$error,array(),$_GET["db"]);$collations=collations();if($_POST){$name=$_POST["name"];$collate=$_POST["collation"];}else{$name=$_GET["db"];$collate=array();if(!strlen($_GET["db"])){$result=$mysql->query("SHOW GRANTS");while($row=$result->fetch_row()){if(preg_match('~ ON (`(([^\\\\`]+|``|\\\\.)*)%`\\.\\*)?~',$row[0],$match)&&$match[1]){$name=stripcslashes(idf_unescape($match[2]));break;}}$result->free();}elseif(($result=$mysql->query("SHOW CREATE DATABASE ".idf_escape($_GET["db"])))){$create=$mysql->result($result,1);if(preg_match('~ COLLATE ([^ ]+)~',$create,$match)){$collate=$match[1];}elseif(preg_match('~ CHARACTER SET ([^ ]+)~',$create,$match)){$collate=$collations[$match[1]][0];}$result->free();}}?>

<form action="" method="post">
<p>
<input name="name" value="<?php echo
htmlspecialchars($name);?>" maxlength="64" />
<select name="collation"><option value="">(<?php echo
lang('collation');?>)</option><?php echo
optionlist($collations,$collate);?></select>
<input type="hidden" name="token" value="<?php echo$token;?>" />
<input type="submit" value="<?php echo
lang('Save');?>" />
<?php if(strlen($_GET["db"])){?><input type="submit" name="drop" value="<?php echo
lang('Drop');?>" onclick="return confirm('<?php echo
lang('Are you sure?');?>');" /><?php }?>
</p>
</form>
<?php
}elseif(isset($_GET["call"])){page_header(lang('Call').": ".htmlspecialchars($_GET["call"]),$error);$routine=routine($_GET["call"],(isset($_GET["callf"])?"FUNCTION":"PROCEDURE"));$in=array();$out=array();foreach($routine["fields"]as$i=>$field){if(substr($field["inout"],-3)=="OUT"){$out[$i]="@".idf_escape($field["field"])." AS ".idf_escape($field["field"]);}if(!$field["inout"]||substr($field["inout"],0,2)=="IN"){$in[]=$i;}}if(!$error&&$_POST){$call=array();foreach($routine["fields"]as$key=>$field){if(in_array($key,$in)){$val=process_input($key,$field);if($val===false){$val="''";}if(isset($out[$key])){$mysql->query("SET @".idf_escape($field["field"])." = ".$val);}}$call[]=(isset($out[$key])?"@".idf_escape($field["field"]):$val);}$result=$mysql->multi_query((isset($_GET["callf"])?"SELECT":"CALL")." ".idf_escape($_GET["call"])."(".implode(", ",$call).")");if(!$result){echo"<p class='error'>".htmlspecialchars($mysql->error)."</p>\n";}else{do{$result=$mysql->store_result();if(is_object($result)){select($result);}else{echo"<p class='message'>".lang('Routine has been called, %d row(s) affected.',$mysql->affected_rows)."</p>\n";}}while($mysql->next_result());if($out){select($mysql->query("SELECT ".implode(", ",$out)));}}}?>

<form action="" method="post">
<?php
if($in){echo"<table border='0' cellspacing='0' cellpadding='2'>\n";foreach($in
as$key){$field=$routine["fields"][$key];echo"<tr><th>".htmlspecialchars($field["field"])."</th><td>";$value=$_POST["fields"][$key];if(strlen($value)&&($field["type"]=="enum"||$field["type"]=="set")){$value=intval($value);}input($key,$field,$value);echo"</td></tr>\n";}echo"</table>\n";}?>
<p>
<input type="hidden" name="token" value="<?php echo$token;?>" />
<input type="submit" value="<?php echo
lang('Call');?>" />
</p>
</form>
<?php
}elseif(isset($_GET["foreign"])){if($_POST&&!$error&&!$_POST["add"]&&!$_POST["change"]&&!$_POST["change-js"]){if($_POST["drop"]){if($mysql->query("ALTER TABLE ".idf_escape($_GET["foreign"])." DROP FOREIGN KEY ".idf_escape($_GET["name"]))){redirect($SELF."table=".urlencode($_GET["foreign"]),lang('Foreign key has been dropped.'));}}else{$source=array_filter($_POST["source"],'strlen');ksort($source);$target=array();foreach($source
as$key=>$val){$target[$key]=$_POST["target"][$key];}if($mysql->query("
			ALTER TABLE ".idf_escape($_GET["foreign"]).(strlen($_GET["name"])?" DROP FOREIGN KEY ".idf_escape($_GET["name"]).",":"")."
			ADD FOREIGN KEY (".implode(", ",array_map('idf_escape',$source)).")
			REFERENCES ".idf_escape($_POST["table"])." (".implode(", ",array_map('idf_escape',$target)).")".(in_array($_POST["on_delete"],$on_actions)?" ON DELETE $_POST[on_delete]":"").(in_array($_POST["on_update"],$on_actions)?" ON UPDATE $_POST[on_update]":""))){redirect($SELF."table=".urlencode($_GET["foreign"]),(strlen($_GET["name"])?lang('Foreign key has been altered.'):lang('Foreign key has been created.')));}}$error=$mysql->error;}page_header(lang('Foreign key'),$error,array("table"=>$_GET["foreign"]),$_GET["foreign"]);$tables=array();$result=$mysql->query("SHOW TABLE STATUS");while($row=$result->fetch_assoc()){if($row["Engine"]=="InnoDB"){$tables[]=$row["Name"];}}$result->free();if($_POST){$row=$_POST;ksort($row["source"]);if($_POST["add"]){$row["source"][]="";}elseif($_POST["change"]||$_POST["change-js"]){$row["target"]=array();}}elseif(strlen($_GET["name"])){$foreign_keys=foreign_keys($_GET["foreign"]);$row=$foreign_keys[$_GET["name"]];$row["source"][]="";}else{$row=array("table"=>$_GET["foreign"],"source"=>array(""));}$source=get_vals("SHOW COLUMNS FROM ".idf_escape($_GET["foreign"]));$target=($_GET["foreign"]===$row["table"]?$source:get_vals("SHOW COLUMNS FROM ".idf_escape($row["table"])));?>

<script type="text/javascript">
function add_row(field) {
	var row = field.parentNode.parentNode.cloneNode(true);
	var selects = row.getElementsByTagName('select');
	for (var i=0; i < selects.length; i++) {
		selects[i].name = selects[i].name.replace(/\]/, '1$&');
		selects[i].selectedIndex = 0;
	}
	field.parentNode.parentNode.parentNode.appendChild(row);
	field.onchange = function () { };
}
</script>

<form action="" method="post">
<p>
<?php echo
lang('Target table');?>:
<select name="table" onchange="this.form['change-js'].value = '1'; this.form.submit();"><?php echo
optionlist($tables,$row["table"]);?></select>
<input type="hidden" name="change-js" value="" />
</p>
<noscript><p><input type="submit" name="change" value="<?php echo
lang('Change');?>" /></p></noscript>
<table border="0" cellspacing="0" cellpadding="2">
<thead><tr><th><?php echo
lang('Source');?></th><th><?php echo
lang('Target');?></th></tr></thead>
<?php
$j=0;foreach($row["source"]as$key=>$val){echo"<tr>";echo"<td><select name='source[".intval($key)."]'".($j==count($row["source"])-1?" onchange='add_row(this);'":"")."><option></option>".optionlist($source,$val)."</select></td>";echo"<td><select name='target[".intval($key)."]'>".optionlist($target,$row["target"][$key])."</select></td>";echo"</tr>\n";$j++;}?>
</table>
<p>
<?php echo
lang('ON DELETE');?>: <select name="on_delete"><option></option><?php echo
optionlist($on_actions,$row["on_delete"]);?></select>
<?php echo
lang('ON UPDATE');?>: <select name="on_update"><option></option><?php echo
optionlist($on_actions,$row["on_update"]);?></select>
</p>
<p>
<input type="hidden" name="token" value="<?php echo$token;?>" />
<input type="submit" value="<?php echo
lang('Save');?>" />
<?php if(strlen($_GET["name"])){?><input type="submit" name="drop" value="<?php echo
lang('Drop');?>" onclick="return confirm('<?php echo
lang('Are you sure?');?>');" /><?php }?>
</p>
<noscript><p><input type="submit" name="add" value="<?php echo
lang('Add column');?>" /></p></noscript>
</form>
<?php
}elseif(isset($_GET["createv"])){$dropped=false;if($_POST&&!$error){if(strlen($_GET["createv"])&&($_POST["dropped"]||$mysql->query("DROP VIEW ".idf_escape($_GET["createv"])))){if($_POST["drop"]){redirect(substr($SELF,0,-1),lang('View has been dropped.'));}$dropped=true;}if(!$_POST["drop"]&&$mysql->query("CREATE VIEW ".idf_escape($_POST["name"])." AS ".$_POST["select"])){redirect($SELF."view=".urlencode($_POST["name"]),(strlen($_GET["createv"])?lang('View has been altered.'):lang('View has been created.')));}$error=$mysql->error;}page_header((strlen($_GET["createv"])?lang('Alter view'):lang('Create view')),$error,array("view"=>$_GET["createv"]),$_GET["createv"]);if($_POST){$row=$_POST;}elseif(strlen($_GET["createv"])){$row=view($_GET["createv"]);$row["name"]=$_GET["createv"];}else{$row=array();}?>

<form action="" method="post">
<p><textarea name="select" rows="10" cols="80" style="width: 98%;"><?php echo
htmlspecialchars($row["select"]);?></textarea></p>
<p>
<input type="hidden" name="token" value="<?php echo$token;?>" />
<?php if($dropped){?><input type="hidden" name="dropped" value="1" /><?php }?>
<?php echo
lang('Name');?>: <input name="name" value="<?php echo
htmlspecialchars($row["name"]);?>" maxlength="64" />
<input type="submit" value="<?php echo
lang('Save');?>" />
<?php if(strlen($_GET["createv"])){?><input type="submit" name="drop" value="<?php echo
lang('Drop');?>" onclick="return confirm('<?php echo
lang('Are you sure?');?>');" /><?php }?>
</p>
</form>
<?php
}elseif(isset($_GET["procedure"])){$routine=(isset($_GET["function"])?"FUNCTION":"PROCEDURE");$dropped=false;if($_POST&&!$error&&!$_POST["add"]&&!$_POST["drop_col"]&&!$_POST["up"]&&!$_POST["down"]){if(strlen($_GET["procedure"])&&($_POST["dropped"]||$mysql->query("DROP $routine ".idf_escape($_GET["procedure"])))){if($_POST["drop"]){redirect(substr($SELF,0,-1),lang('Routine has been dropped.'));}$dropped=true;}if(!$_POST["drop"]){$set=array();$fields=array_filter((array)$_POST["fields"],'strlen');ksort($fields);foreach($fields
as$field){if(strlen($field["field"])){$set[]=(in_array($field["inout"],$inout)?"$field[inout] ":"").idf_escape($field["field"]).process_type($field,"CHARACTER SET");}}if($mysql->query("CREATE $routine ".idf_escape($_POST["name"])." (".implode(", ",$set).")".(isset($_GET["function"])?" RETURNS".process_type($_POST["returns"],"CHARACTER SET"):"")."
			$_POST[definition]")){redirect(substr($SELF,0,-1),(strlen($_GET["procedure"])?lang('Routine has been altered.'):lang('Routine has been created.')));}}$error=$mysql->error;}page_header((strlen($_GET["procedure"])?(isset($_GET["function"])?lang('Alter function'):lang('Alter procedure')).": ".htmlspecialchars($_GET["procedure"]):(isset($_GET["function"])?lang('Create function'):lang('Create procedure'))),$error);$collations=get_vals("SHOW CHARACTER SET");if($_POST){$row=$_POST;$row["fields"]=(array)$row["fields"];process_fields($row["fields"]);}elseif(strlen($_GET["procedure"])){$row=routine($_GET["procedure"],$routine);$row["name"]=$_GET["procedure"];}else{$row=array("fields"=>array());}?>

<form action="" method="post" id="form">
<table border="0" cellspacing="0" cellpadding="2">
<?php edit_fields($row["fields"],$collations,$routine);?>
<?php if(isset($_GET["function"])){?><tr><td><?php echo
lang('Return type');?></td><?php echo
edit_type("returns",$row["returns"],$collations);?></tr><?php }?>
</table>
<?php echo
type_change(count($row["fields"]));?>
<?php if(isset($_GET["function"])){?>
<script type="text/javascript">
document.getElementById('form')['returns[type]'].onchange();
</script>
<?php }?>
<p><textarea name="definition" rows="10" cols="80" style="width: 98%;"><?php echo
htmlspecialchars($row["definition"]);?></textarea></p>
<p>
<input type="hidden" name="token" value="<?php echo$token;?>" />
<?php if($dropped){?><input type="hidden" name="dropped" value="1" /><?php }?>
<?php echo
lang('Name');?>: <input name="name" value="<?php echo
htmlspecialchars($row["name"]);?>" maxlength="64" />
<input type="submit" value="<?php echo
lang('Save');?>" />
<?php if(strlen($_GET["procedure"])){?><input type="submit" name="drop" value="<?php echo
lang('Drop');?>" onclick="return confirm('<?php echo
lang('Are you sure?');?>');" /><?php }?>
</p>
</form>
<?php
}elseif(isset($_GET["trigger"])){$trigger_time=array("BEFORE","AFTER");$trigger_event=array("INSERT","UPDATE","DELETE");$dropped=false;if($_POST&&!$error){if(strlen($_GET["name"])&&($_POST["dropped"]||$mysql->query("DROP TRIGGER ".idf_escape($_GET["name"])))){if($_POST["drop"]){redirect($SELF."table=".urlencode($_GET["trigger"]),lang('Trigger has been dropped.'));}$dropped=true;}if(!$_POST["drop"]){if(in_array($_POST["Timing"],$trigger_time)&&in_array($_POST["Event"],$trigger_event)&&$mysql->query("CREATE TRIGGER ".idf_escape($_POST["Trigger"])." $_POST[Timing] $_POST[Event] ON ".idf_escape($_GET["trigger"])." FOR EACH ROW $_POST[Statement]")){redirect($SELF."table=".urlencode($_GET["trigger"]),(strlen($_GET["name"])?lang('Trigger has been altered.'):lang('Trigger has been created.')));}}$error=$mysql->error;}page_header((strlen($_GET["name"])?lang('Alter trigger').": ".htmlspecialchars($_GET["name"]):lang('Create trigger')),$error,array("table"=>$_GET["trigger"]));if($_POST){$row=$_POST;}elseif(strlen($_GET["name"])){$result=$mysql->query("SHOW TRIGGERS LIKE '".$mysql->escape_string(addcslashes($_GET["trigger"],"%_"))."'");while($row=$result->fetch_assoc()){if($row["Trigger"]===$_GET["name"]){break;}}$result->free();}else{$row=array();}?>

<form action="" method="post" id="form">
<table border="0" cellspacing="0" cellpadding="2">
<tr><th><?php echo
lang('Name');?></th><td><input name="Trigger" value="<?php echo
htmlspecialchars($row["Trigger"]);?>" maxlength="64" /></td></tr>
<tr><th><?php echo
lang('Time');?></th><td><select name="Timing"><?php echo
optionlist($trigger_time,$row["Timing"]);?></select></td></tr>
<tr><th><?php echo
lang('Event');?></th><td><select name="Event"><?php echo
optionlist($trigger_event,$row["Event"]);?></select></td></tr>
</table>
<p><textarea name="Statement" rows="10" cols="80" style="width: 98%;"><?php echo
htmlspecialchars($row["Statement"]);?></textarea></p>
<p>
<input type="hidden" name="token" value="<?php echo$token;?>" />
<?php if($dropped){?><input type="hidden" name="dropped" value="1" /><?php }?>
<input type="submit" value="<?php echo
lang('Save');?>" />
<?php if(strlen($_GET["name"])){?><input type="submit" name="drop" value="<?php echo
lang('Drop');?>" onclick="return confirm('<?php echo
lang('Are you sure?');?>');" /><?php }?>
</p>
</form>
<?php
}elseif(isset($_GET["user"])){$privileges=array();$result=$mysql->query("SHOW PRIVILEGES");while($row=$result->fetch_assoc()){foreach(explode(",",$row["Context"])as$context){$privileges[$context][$row["Privilege"]]=$row["Comment"];}}$result->free();$privileges["Server Admin"]+=$privileges["File access on server"];$privileges["Databases"]["Create routine"]=$privileges["Procedures"]["Create routine"];$privileges["Columns"]=array();foreach(array("Select","Insert","Update","References")as$val){$privileges["Columns"][$val]=$privileges["Tables"][$val];}unset($privileges["Server Admin"]["Usage"]);unset($privileges["Procedures"]["Create routine"]);foreach($privileges["Tables"]as$key=>$val){unset($privileges["Databases"][$key]);}function
all_privileges(&$grants,$privileges){foreach($privileges
as$privilege=>$val){if($privilege!="Grant option"){$grants[strtoupper($privilege)]=true;}}}if($_POST){$new_grants=array();foreach($_POST["objects"]as$key=>$val){$new_grants[$val]=((array)$new_grants[$val])+((array)$_POST["grants"][$key]);}}$grants=array();$old_pass="";if(isset($_GET["host"])&&($result=$mysql->query("SHOW GRANTS FOR '".$mysql->escape_string($_GET["user"])."'@'".$mysql->escape_string($_GET["host"])."'"))){while($row=$result->fetch_row()){if(preg_match('~GRANT (.*) ON (.*) TO ~',$row[0],$match)){if($match[1]=="ALL PRIVILEGES"){if($match[2]=="*.*"){all_privileges($grants[$match[2]],$privileges["Server Admin"]);}if(substr($match[2],-1)=="*"){all_privileges($grants[$match[2]],$privileges["Databases"]);all_privileges($grants[$match[2]],(array)$privileges["Procedures"]);}all_privileges($grants[$match[2]],$privileges["Tables"]);}elseif(preg_match_all('~ *([^(,]*[^ ,(])( *\\([^)]+\\))?~',$match[1],$matches,PREG_SET_ORDER)){foreach($matches
as$val){$grants["$match[2]$val[2]"][$val[1]]=true;}}}if(preg_match('~ WITH GRANT OPTION~',$row[0])){$grants[$match[2]]["GRANT OPTION"]=true;}if(preg_match("~ IDENTIFIED BY PASSWORD '([^']+)~",$row[0],$match)){$old_pass=$match[1];}}$result->free();}if($_POST&&!$error){$old_user=(isset($_GET["host"])?$mysql->escape_string($_GET["user"])."'@'".$mysql->escape_string($_GET["host"]):"");$new_user=$mysql->escape_string($_POST["user"])."'@'".$mysql->escape_string($_POST["host"]);$pass=$mysql->escape_string($_POST["pass"]);if($_POST["drop"]){if($mysql->query("DROP USER '$old_user'")){redirect($SELF."privileges=",lang('User has been dropped.'));}}elseif($old_user==$new_user||$mysql->query(($mysql->server_info<5?"GRANT USAGE ON *.* TO":"CREATE USER")." '$new_user' IDENTIFIED BY".($_POST["hashed"]?" PASSWORD":"")." '$pass'")){if($old_user==$new_user){$mysql->query("SET PASSWORD FOR '$new_user' = ".($_POST["hashed"]?"'$pass'":"PASSWORD('$pass')"));}$revoke=array();foreach($new_grants
as$object=>$grant){if(isset($_GET["grant"])){$grant=array_filter($grant);}$grant=array_keys($grant);if(isset($_GET["grant"])){$revoke=array_diff(array_keys(array_filter($new_grants[$object],'strlen')),$grant);}elseif($old_user==$new_user){$old_grant=array_keys((array)$grants[$object]);$revoke=array_diff($old_grant,$grant);$grant=array_diff($grant,$old_grant);unset($grants[$object]);}if(preg_match('~^(.+)(\\(.*\\))?$~U',$object,$match)&&(($grant&&!$mysql->query("GRANT ".implode("$match[2], ",$grant)."$match[2] ON $match[1] TO '$new_user'"))||($revoke&&!$mysql->query("REVOKE ".implode("$match[2], ",$revoke)."$match[2] ON $match[1] FROM '$new_user'")))){$error=$mysql->error;if($old_user!=$new_user){$mysql->query("DROP USER '$new_user'");}break;}}if(!$error){if(isset($_GET["host"])&&$old_user!=$new_user){$mysql->query("DROP USER '$old_user'");}elseif(!isset($_GET["grant"])){foreach($grants
as$object=>$revoke){if(preg_match('~^(.+)(\\(.*\\))?$~U',$object,$match)){$mysql->query("REVOKE ".implode("$match[2], ",array_keys($revoke))."$match[2] ON $match[1] FROM '$new_user'");}}}redirect($SELF."privileges=",(isset($_GET["host"])?lang('User has been altered.'):lang('User has been created.')));}}if(!$error){$error=$mysql->error;}}page_header((isset($_GET["host"])?lang('Username').": ".htmlspecialchars("$_GET[user]@$_GET[host]"):lang('Create user')),$error,array("privileges"=>lang('Privileges')));if($_POST){$row=$_POST;$grants=$new_grants;}else{$row=$_GET+array("host"=>"localhost");$row["pass"]=$old_pass;if(strlen($old_pass)){$row["hashed"]=true;}$grants[""]=true;}?>
<form action="" method="post">
<table border="0" cellspacing="0" cellpadding="2">
<tr><th><?php echo
lang('Username');?></th><td><input name="user" maxlength="16" value="<?php echo
htmlspecialchars($row["user"]);?>" /></td></tr>
<tr><th><?php echo
lang('Server');?></th><td><input name="host" maxlength="60" value="<?php echo
htmlspecialchars($row["host"]);?>" /></td></tr>
<tr><th><?php echo
lang('Password');?></th><td><input name="pass" value="<?php echo
htmlspecialchars($row["pass"]);?>" /> <label for="hashed"><input type="checkbox" name="hashed" id="hashed" value="1"<?php if($row["hashed"]){?> checked="checked"<?php }?> /><?php echo
lang('Hashed');?></label></td></tr>
</table>

<?php

echo"<table border='0' cellspacing='0' cellpadding='2'>\n";echo"<thead><tr><th colspan='2'>".lang('Privileges')."</th>";$i=0;foreach($grants
as$object=>$grant){echo'<th>'.($object!="*.*"?'<input name="objects['.$i.']" value="'.htmlspecialchars($object).'" size="10" />':'<input type="hidden" name="objects['.$i.']" value="*.*" size="10" />*.*').'</th>';$i++;}echo"</tr></thead>\n";foreach(array("Server Admin"=>lang('Server'),"Databases"=>lang('Database'),"Tables"=>lang('Table'),"Columns"=>lang('Column'),"Procedures"=>lang('Routine'),)as$context=>$desc){foreach((array)$privileges[$context]as$privilege=>$comment){echo'<tr><td>'.$desc.'</td><td title="'.htmlspecialchars($comment).'"><i>'.htmlspecialchars($privilege).'</i></td>';$i=0;foreach($grants
as$object=>$grant){$name='"grants['.$i.']['.htmlspecialchars(strtoupper($privilege)).']"';$value=$grant[strtoupper($privilege)];if($context=="Server Admin"&&$object!=(isset($grants["*.*"])?"*.*":"")){echo"<td>&nbsp;</td>";}elseif(isset($_GET["grant"])){echo"<td><select name=$name><option></option><option value='1'".($value?" selected='selected'":"").">".lang('Grant')."</option><option value='0'".($value=="0"?" selected='selected'":"").">".lang('Revoke')."</option></select></td>";}else{echo"<td align='center'><input type='checkbox' name=$name value='1'".($value?" checked='checked'":"")." /></td>";}$i++;}echo"</tr>\n";}}echo"</table>\n";?>
<p>
<input type="hidden" name="token" value="<?php echo$token;?>" />
<input type="submit" value="<?php echo
lang('Save');?>" />
<?php if(isset($_GET["host"])){?><input type="submit" name="drop" value="<?php echo
lang('Drop');?>" onclick="return confirm('<?php echo
lang('Are you sure?');?>');" /><?php }?>
</p>
</form>
<?php
}elseif(isset($_GET["processlist"])){if($_POST&&!$error){$killed=0;foreach((array)$_POST["kill"]as$val){if($mysql->query("KILL ".intval($val))){$killed++;}}if($killed||!$_POST["kill"]){redirect($SELF."processlist=",lang('%d process(es) has been killed.',$killed));}$error=$mysql->error;}page_header(lang('Process list'),$error);?>

<form action="" method="post">
<table border="1" cellspacing="0" cellpadding="2">
<?php
$result=$mysql->query("SHOW PROCESSLIST");for($i=0;$row=$result->fetch_assoc();$i++){if(!$i){echo"<thead><tr lang='en'><th>&nbsp;</th><th>".implode("</th><th>",array_keys($row))."</th></tr></thead>\n";}echo"<tr><td><input type='checkbox' name='kill[]' value='$row[Id]' /></td><td>".implode("</td><td>",$row)."</td></tr>\n";}$result->free();?>
</table>
<p>
<input type="hidden" name="token" value="<?php echo$token;?>" />
<input type="submit" value="<?php echo
lang('Kill');?>" />
</p>
</form>
<?php
}elseif(isset($_GET["select"])){$table_status=table_status($_GET["select"]);$indexes=indexes($_GET["select"]);$operators=array("=","<",">","<=",">=","!=","LIKE","REGEXP","IN","IS NULL");if($table_status["Engine"]=="MyISAM"){$operators[]="AGAINST";}$fields=fields($_GET["select"]);$rights=array();$columns=array();unset($text_length);foreach($fields
as$key=>$field){if(isset($field["privileges"]["select"])){$columns[]=$key;if(preg_match('~text|blob~',$field["type"])){$text_length=(isset($_GET["text_length"])?$_GET["text_length"]:"100");}}$rights+=$field["privileges"];}$where=array();foreach($indexes
as$i=>$index){if($index["type"]=="FULLTEXT"&&strlen($_GET["fulltext"][$i])){$where[]="MATCH (".implode(", ",array_map('idf_escape',$index["columns"])).") AGAINST ('".$mysql->escape_string($_GET["fulltext"][$i])."'".(isset($_GET["boolean"][$i])?" IN BOOLEAN MODE":"").")";}}foreach((array)$_GET["where"]as$val){if(strlen($val["col"])&&in_array($val["op"],$operators)){if($val["op"]=="IN"){$in=process_length($val["val"]);$where[]=(strlen($in)?idf_escape($val["col"])." IN ($in)":"0");}elseif($val["op"]=="AGAINST"){$where[]="MATCH (".idf_escape($val["col"]).") AGAINST ('".$mysql->escape_string($val["val"])."' IN BOOLEAN MODE)";}else{$where[]=idf_escape($val["col"])." $val[op]".($val["op"]=="IS NULL"?"":" '".$mysql->escape_string($val["val"])."'");}}}$order=array();foreach((array)$_GET["order"]as$key=>$val){if(in_array($val,$columns,true)){$order[]=idf_escape($val).(isset($_GET["desc"][$key])?" DESC":"");}}$limit=(isset($_GET["limit"])?$_GET["limit"]:"30");$from="FROM ".idf_escape($_GET["select"]).($where?" WHERE ".implode(" AND ",$where):"").($order?" ORDER BY ".implode(", ",$order):"").(strlen($limit)?" LIMIT ".intval($limit).(intval($_GET["page"])?" OFFSET ".($limit*$_GET["page"]):""):"");if($_POST&&!$error){$result=true;$deleted=0;if(isset($_POST["truncate"])){$result=$mysql->query("TRUNCATE ".idf_escape($_GET["select"]));$deleted=$mysql->affected_rows;}elseif(is_array($_POST["delete"])){foreach($_POST["delete"]as$val){parse_str($val,$delete);$result=$mysql->query("DELETE FROM ".idf_escape($_GET["select"])." WHERE ".implode(" AND ",where($delete))." LIMIT 1");if(!$result){break;}$deleted+=$mysql->affected_rows;}}elseif($_POST["delete_selected"]){if(!$_GET["page"]){$result=$mysql->query("DELETE $from");$deleted=$mysql->affected_rows;}else{$result1=$mysql->query("SELECT * $from");while($row1=$result1->fetch_assoc()){parse_str(implode("&",unique_idf($row1,$indexes)),$delete);$result=$mysql->query("DELETE FROM ".idf_escape($_GET["select"])." WHERE ".implode(" AND ",where($delete))." LIMIT 1");if(!$result){break;}$deleted+=$mysql->affected_rows;}$result1->free();}}if($result){redirect(remove_from_uri("page"),lang('%d item(s) have been deleted.',$deleted));}$error=$mysql->error;}page_header(lang('Select').": ".htmlspecialchars($_GET["select"]),($error?lang('Error during deleting').": $error":""));if(isset($rights["insert"])){echo'<p><a href="'.htmlspecialchars($SELF).'edit='.urlencode($_GET['select']).'">'.lang('New item')."</a></p>\n";}if(!$columns){echo"<p class='error'>".lang('Unable to select the table').($fields?"":": ".htmlspecialchars($mysql->error)).".</p>\n";}else{echo"<form action='' id='form'>\n<fieldset><legend>".lang('Search')."</legend>\n";if(strlen($_GET["server"])){echo'<input type="hidden" name="server" value="'.htmlspecialchars($_GET["server"]).'" />';}echo'<input type="hidden" name="db" value="'.htmlspecialchars($_GET["db"]).'" />';echo'<input type="hidden" name="select" value="'.htmlspecialchars($_GET["select"]).'" />';echo"\n";foreach($indexes
as$i=>$index){if($index["type"]=="FULLTEXT"){echo"(<i>".implode("</i>, <i>",array_map('htmlspecialchars',$index["columns"]))."</i>) AGAINST";echo' <input name="fulltext['.$i.']" value="'.htmlspecialchars($_GET["fulltext"][$i]).'" />';echo"<label for='boolean-$i'><input type='checkbox' name='boolean[$i]' value='1' id='boolean-$i'".(isset($_GET["boolean"][$i])?" checked='checked'":"")." />".lang('BOOL')."</label>";echo"<br />\n";}}$i=0;foreach((array)$_GET["where"]as$val){if(strlen($val["col"])&&in_array($val["op"],$operators)){echo"<div><select name='where[$i][col]'><option></option>".optionlist($columns,$val["col"])."</select>";echo"<select name='where[$i][op]' onchange=\"where_change(this);\">".optionlist($operators,$val["op"])."</select>";echo"<input name='where[$i][val]' value=\"".htmlspecialchars($val["val"])."\" /></div>\n";$i++;}}?>
<script type="text/javascript">
function where_change(op) {
	op.form[op.name.substr(0, op.name.length - 4) + '[val]'].style.display = (op.value == 'IS NULL' ? 'none' : '');
}
<?php if($i){?>
for (var i=0; <?php echo$i;?> > i; i++) {
	document.getElementById('form')['where[' + i + '][op]'].onchange();
}
<?php }?>

function add_row(field) {
	var row = field.parentNode.cloneNode(true);
	var selects = row.getElementsByTagName('select');
	for (var i=0; i < selects.length; i++) {
		selects[i].name = selects[i].name.replace(/[a-z]\[[0-9]+/, '$&1');
		selects[i].selectedIndex = 0;
	}
	var input = row.getElementsByTagName('input')[0];
	input.name = input.name.replace(/[a-z]\[[0-9]+/, '$&1');
	input.value = '';
	field.parentNode.parentNode.appendChild(row);
	field.onchange = function () { };
}
</script>
<?php

echo"<div><select name='where[$i][col]' onchange='add_row(this);'><option></option>".optionlist($columns,array())."</select>";echo"<select name='where[$i][op]' onchange=\"where_change(this);\">".optionlist($operators,array())."</select>";echo"<input name='where[$i][val]' /></div>\n";echo"</fieldset>\n";echo"<fieldset><legend>".lang('Sort')."</legend>\n";$i=0;foreach((array)$_GET["order"]as$key=>$val){if(in_array($val,$columns,true)){echo"<div><select name='order[$i]'><option></option>".optionlist($columns,$val)."</select>";echo"<label><input type='checkbox' name='desc[$i]' value='1'".(isset($_GET["desc"][$key])?" checked='checked'":"")." />".lang('DESC')."</label></div>\n";$i++;}}echo"<div><select name='order[$i]' onchange='add_row(this);'><option></option>".optionlist($columns,array())."</select>";echo"<label><input type='checkbox' name='desc[$i]' value='1' />".lang('DESC')."</label></div>\n";echo"</fieldset>\n";echo"<fieldset><legend>".lang('Limit')."</legend>\n";echo'<div><input name="limit" size="3" value="'.htmlspecialchars($limit).'" /></div>';echo"</fieldset>\n";if(isset($text_length)){echo"<fieldset><legend>".lang('Text length')."</legend>\n";echo'<div><input name="text_length" size="3" value="'.htmlspecialchars($text_length).'" /></div>';echo"</fieldset>\n";}echo"<fieldset><legend>".lang('Action')."</legend><div><input type='submit' value='".lang('Select')."' /></div></fieldset>\n";echo"</form>\n";echo"<div style='clear: left;'>&nbsp;</div>\n";$result=$mysql->query("SELECT SQL_CALC_FOUND_ROWS * $from");if(!$result){echo"<p class='error'>".htmlspecialchars($mysql->error)."</p>\n";}else{if(!$result->num_rows){echo"<p class='message'>".lang('No rows.')."</p>\n";}else{$found_rows=$mysql->result($mysql->query(" SELECT FOUND_ROWS()"));$foreign_keys=array();foreach(foreign_keys($_GET["select"])as$foreign_key){foreach($foreign_key["source"]as$val){$foreign_keys[$val][]=$foreign_key;}}echo"<form action='' method='post'>\n";echo"<table border='1' cellspacing='0' cellpadding='2'>\n";for($j=0;$row=$result->fetch_assoc();$j++){if(!$j){echo'<thead><tr><td><label><input type="checkbox" name="delete_selected" value="1" onclick="var elems = this.form.elements; for (var i=0; i < elems.length; i++) if (elems[i].name == \'delete[]\') elems[i].checked = this.checked;" />'.lang('all').'</label></td><th>'.implode("</th><th>",array_map('htmlspecialchars',array_keys($row)))."</th></tr></thead>\n";}$unique_idf=implode('&amp;',unique_idf($row,$indexes));echo'<tr><td><input type="checkbox" name="delete[]" value="'.$unique_idf.'" /> <a href="'.htmlspecialchars($SELF).'edit='.urlencode($_GET['select']).'&amp;'.$unique_idf.'">'.lang('edit')."</a></td>";foreach($row
as$key=>$val){if(!isset($val)){$val="<i>NULL</i>";}elseif(preg_match('~blob|binary~',$fields[$key]["type"])&&preg_match('~[\\x80-\\xFF]~',$val)){$val='<a href="'.htmlspecialchars($SELF).'download='.urlencode($_GET["select"]).'&amp;field='.urlencode($key).'&amp;'.$unique_idf.'">'.lang('%d byte(s)',strlen($val)).'</a>';}else{if(!strlen(trim($val))){$val="&nbsp;";}elseif(intval($text_length)>0&&preg_match('~blob|text~',$fields[$key]["type"])&&strlen($val)>intval($text_length)){$val=(preg_match('~blob~',$fields[$key]["type"])?nl2br(htmlspecialchars(substr($val,0,intval($text_length))))."<em>...</em>":shorten_utf8($val,intval($text_length)));}else{$val=nl2br(htmlspecialchars($val));if($fields[$key]["type"]=="char"){$val="<code>$val</code>";}}foreach((array)$foreign_keys[$key]as$foreign_key){if(count($foreign_keys[$key])==1||count($foreign_key["source"])==1){$val="\">$val</a>";foreach($foreign_key["source"]as$i=>$source){$val="&amp;where%5B$i%5D%5Bcol%5D=".urlencode($foreign_key["target"][$i])."&amp;where%5B$i%5D%5Bop%5D=%3D&amp;where%5B$i%5D%5Bval%5D=".urlencode($row[$source]).$val;}$val='<a href="'.htmlspecialchars(strlen($foreign_key["db"])?preg_replace('~([?&]db=)[^&]+~','\\1'.urlencode($foreign_key["db"]),$SELF):$SELF).'select='.htmlspecialchars($foreign_key["table"]).$val;break;}}}echo"<td>$val</td>";}echo"</tr>\n";}echo"</table>\n";echo"<p><input type='hidden' name='token' value='$token' /><input type='submit' value='".lang('Delete selected')."' /> <input type='submit' name='truncate' value='".lang('Truncate table')."' onclick=\"return confirm('".lang('Are you sure?')."');\" /></p>\n";echo"</form>\n";if(intval($limit)&&$found_rows>$limit){$max_page=floor(($found_rows-1)/$limit);function
print_page($page){echo" ".($page==$_GET["page"]?$page+1:'<a href="'.htmlspecialchars(remove_from_uri("page").($page?"&page=$page":"")).'">'.($page+1)."</a>");}echo"<p>".lang('Page').":";print_page(0);if($_GET["page"]>3){echo" ...";}for($i=max(1,$_GET["page"]-2);$i<min($max_page,$_GET["page"]+3);$i++){print_page($i);}if($_GET["page"]+3<$max_page){echo" ...";}print_page($max_page);echo"</p>\n";}}$result->free();}}}else{unset($_SESSION["tokens"][$_GET["server"]][$_SERVER["REQUEST_URI"]]);page_header(lang('Database').": ".htmlspecialchars($_GET["db"]),$error,false);echo'<p><a href="'.htmlspecialchars($SELF).'database=">'.lang('Alter database')."</a></p>\n";echo'<p><a href="'.htmlspecialchars($SELF).'schema=">'.lang('Database schema')."</a></p>\n";if($mysql->server_info>=5){echo'<p><a href="'.htmlspecialchars($SELF).'createv=">'.lang('Create view')."</a></p>\n";echo"<h3>".lang('Routines')."</h3>\n";$result=$mysql->query("SELECT * FROM information_schema.ROUTINES WHERE ROUTINE_SCHEMA = '".$mysql->escape_string($_GET["db"])."'");if($result->num_rows){echo"<table border='0' cellspacing='0' cellpadding='2'>\n";while($row=$result->fetch_assoc()){echo"<tr>";echo"<td>".htmlspecialchars($row["ROUTINE_TYPE"])."</td>";echo'<th><a href="'.htmlspecialchars($SELF).($row["ROUTINE_TYPE"]=="FUNCTION"?'callf':'call').'='.urlencode($row["ROUTINE_NAME"]).'">'.htmlspecialchars($row["ROUTINE_NAME"]).'</a></th>';echo'<td><a href="'.htmlspecialchars($SELF).($row["ROUTINE_TYPE"]=="FUNCTION"?'function':'procedure').'='.urlencode($row["ROUTINE_NAME"]).'">'.lang('Alter')."</a></td>\n";echo"</tr>\n";}echo"</table>\n";}$result->free();echo'<p><a href="'.htmlspecialchars($SELF).'procedure=">'.lang('Create procedure').'</a> <a href="'.htmlspecialchars($SELF).'function=">'.lang('Create function')."</a></p>\n";}}}page_footer();}