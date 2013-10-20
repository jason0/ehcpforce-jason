<?php
@ini_set("date.timezone","UTC");

function imgextension($f){
	return isextension($f,'img');
}

function isoextension($f){
	return isextension($f,'iso');
}

function isextension($f,$e){	
	if(!is_file($f)) return false;
	$f=basename($f);
	$ext = explode(".", strtolower($f));
	$ext=array_pop($ext);
	#echo "isleniyor: $f : ext:($ext) \n";
	return ($ext==$e);
}

function mymkdir($dirname){
	$dirname=trim($dirname);
	if($dirname<>'.' and $dirname<>'..') {
		if(!is_dir($dirname)) {
			if(mkdir($dirname,777,true)) echo "\ndirectory is made: ($dirname)\n";
			else "\nerror occured while making directory: ($dirname)\n";
		}
	}
}

if(!function_exists("print_r2")){
function print_r2($array)
{
	if (is_array($array)) return "<pre>Array:\n".str_replace(array("\n" , " "), array('<br>', '&nbsp;'), print_r($array, true)).'</pre>';
	elseif ($array===null) return "(null) ";
	elseif ($array==="") return "(bosluk= \"\")";
	elseif ($array===false) return "(bool-false)";
	elseif ($array===true) return "(bool-true)";
	else {
		return "Array degil:<br>(normal gosterim:$array)<br>print_r:(".print_r($array,true).") <br>var_dump:".var_dump($array);
	}
}
}

if(!function_exists('print_r3')){
function print_r3($ar,$header='') {
if(!$ar) return "(BOS-EMPTY)";
if(!is_array) return "Not Array:".$ar;

$sayi=count($ar);
$tr="<tr class='list'>";
$td="<td class='list'>";

$res.="<table border=1 class='list'> $header";

foreach($ar as $key=>$val) {
	$res.="$tr$td".$key."</td>$td".$val."</td></tr>";
}

$res.="</table>";
return $res;

/*
ic ice (recursive) yapmak icin, 
en basa, if(!is_array($ar)) return $ar;
$res.="<tr><td>".print_r3(key($ar))."</td><td>".print_r3($val)."</td></tr>";
*/
}
}

if(!function_exists("andle")){
function andle($s1,$s2) { //iki string'in andlenmi halini bulur. bir bosa "and" kullanlmaz. delphiden aldim..:)
  if($s1=='')$s1=$s2;
  elseif ($s2<>'')$s1=$s1.' and '.$s2;
  return $s1;
}
}

function to_array($ar){ # convert a variable to array if it is not already,
    if(is_array($ar)) return $ar;  # if array, dont do anything
    if(!$ar) return array(); # bos ise, bos array dondur.
    if(!is_array($ar)) return array($ar); # array olmayan bir degisken ise, arraya dondur ve return et.
    return "(arraya cevirme yapilamadi.)"; # hicbiri degilse hata var zaten.
}

function array_merge2($ar1,$ar2){
    return array_merge(to_array($ar1),to_array($ar2));
}


if(!function_exists("writeoutput")){
function writeoutput($file, $string, $mode="w",$log=true) {

	mymkdir(dirname($file)); # auto make the dir of filename

	if (!($fp = fopen($file, $mode))) {
			echo "hata: dosya acilamadi: $file (writeoutput) !";
			return false;
	}
	if (!fputs($fp, $string . "\n")) {
			fclose($fp);
			echo "hata: dosyaya yazilamadi: $file (writeoutput) !";
			return false;
	}



	fclose($fp);
	if($log) echo "\n".basename(__FILE__).": file written successfully: $file, mode:$mode \n";
	return true;
}
}

if(!function_exists("writeoutput2")){
function writeoutput2($file, $string, $mode="w",$debug=true) {
	$file=removeDoubleSlash($file);

	if ($debug){
		echo "\n".__FUNCTION__.":*** Writing to file ($file) the contents:\n\n$string\n\n";
	}

	mymkdir(dirname($file)); # auto make the dir of filename

	if (!($fp = fopen($file, $mode))) {
			echo "hata: dosya acilamadi: $file (writeoutput) !";
			return false;
	}
	if (!fputs($fp, $string . "\n")) {
			fclose($fp);
			echo "hata: dosyaya yazilamadi: $file (writeoutput) !";
			return false;
	}
	fclose($fp);
	return true;
}
}


if(!function_exists("alanlarial")){
function alanlarial($db2,$tablo) { // adodb de calsyor.
        foreach($db2->MetaColumnNames($tablo) as $alan) $alanlar[]=$alan;
    return $alanlar;
}
}

if(!function_exists("strop")){
function strop($str,$bas,$son) {
        return $bas.$str.$son;
}
}

if(!function_exists("arrayop")){
function arrayop($arr,$op) {
        foreach($arr as $ar) $ret[]=$op($ar,"{","}");
    return $ret;
}
}

if(!function_exists("executeprog2")){
function executeprog2($prog){ // echoes output.
        passthru($prog, $val);
        return ($val==0);
}
}

if(!function_exists('executeProg3')){
function executeProg3($prog,$echooutput=False){
	# executes program and return output
	if($echooutput) echo "\n".__FUNCTION__.": executing: ($prog)\n";
	exec($prog,$topcmd);
	if(!is_array($topcmd)) return "";
	foreach($topcmd as $t) $topoutput.=$t."\n";
	$out=trim($topoutput);
	if($echooutput and ($out<>'')) echo "\n$out\n";
	return $out;
}
}

if(!function_exists("executeprog")){
function executeprog($prog){ // does not echo output. only return it.
        $fp = popen("$prog", 'r');
        if(!$fp){
        	return "<br>Cannot Execute: $prog ".__FUNCTION__;
        }
        $read = fread($fp, 8192);
        pclose($fp);
        return $read;
}
}

if(!function_exists('degiskenal')){
function degiskenal($degiskenler) {
	$alansayisi=count($degiskenler);
	for ($i=0;$i<$alansayisi;$i++) {
		global ${$degiskenler[$i]};
		if($_POST[$degiskenler[$i]]<>"") ${$degiskenler[$i]}=$_POST[$degiskenler[$i]];
		else ${$degiskenler[$i]}=$_GET[$degiskenler[$i]];
		$degerler[]=${$degiskenler[$i]};
	};
	return $degerler;
}
}

if(!function_exists('replacelnieinfile')){
function replacelineinfile($find,$replace,$where,$addifnotexists=false) {
	// edit a line starting with $find, to edit especially conf files..

	debugecho("\nreplaceline: ($find -> $replace) in ($where) \n ");
	$bulundu=false;

	$filearr=@file($where);
	//if($find=='$dbrootpass=') print_r($filearr);

	if(!$filearr) {
		echo "cannot open file... returning...\n";
		return false;
	} //else print_r($file);

	$len=strlen($find);
	$newfile=array();

	foreach($filearr as $line){
		$line=trim($line)."\n";
		$sub=substr($line,0,$len);
		if($sub==$find) {
			$line=$replace."\n";
			$bulundu=true;
		}
		$newfile[]=$line;

	}

	if($addifnotexists and !$bulundu){
		echo "Line not found, adding at end: ($replace)\n";
		$newfile[]=$replace;
	}
	/*if($find=='$dbrootpass=') {
		echo "yeni dosya:\n";
		print_r($newfile);
	}*/

	return arraytofile($where,$newfile);
}

function replaceOrAddLineInFile($find,$replace,$where){
	return replacelineinfile($find,$replace,$where,true);
}

}


if(!function_exists("addifnotexists")){
function addifnotexists($what,$where) {
	debugecho("\naddifnotexists: ($what) -> ($where) \n ",4);
	#bekle(__FUNCTION__." basliyor..");
	$what.="\n";
	$filearr=@file($where);
	if(!$filearr) {
		echo "cannot open file, trying to setup: ($where)\n";
		$fp = fopen($where,'w');
		fclose($fp);
		$filearr=file($where);

	} //else print_r($file);

	if(array_search($what,$filearr)===false) {
		echo "dosyada bulamadı ekliyor: $where -> $what \n";
		$filearr[]=$what;
		arraytofile($where,$filearr);

	} else {
		//echo "buldu... sorun yok. \n";
		// already found, so, do not add
	}
	
	#bekle(__FUNCTION__." bitti...");

}
}


if(!function_exists('getlocalip')){
function getlocalip($interface='eth0') {
	global $localip;
	$ipline=exec("ifconfig $interface | grep \"inet addr\"");
 	# echo $ipline."\n\n";
	$ipline=strstr($ipline,"addr:");
 	# echo $ipline."\n\n";
	$pos=strpos($ipline," ");
	$ipline=trim(substr($ipline,5,$pos-5));
 	# echo "($ipline)\n\n";
	$localip=$ipline;
	# echo "(getlocalip) your ip is determined to be ($localip) using interface $interface \n";
	return $ipline;
}
}

if(!function_exists("debugecho")){
function debugecho($str,$level=0) {
	$currentlevel=4;
	if($level>=$currentlevel) echo $str;

}
}


if(!function_exists("arraytofile")){
function arraytofile($file,$lines) {
	$new_content = join('',$lines);
	$fp = fopen($file,'w');
	$write = fwrite($fp, $new_content);
	fclose($fp);
}
}

function inputform5ForTableConfig($tableConfig,$addArray){
	# written for compatibility with inputform5 general function.
	# convert a table config (like in start of classapp.php, 'subdomainstable'=>array....) to an array that is acceptable by function inputform5 and call inputform5
	$fields=$tableConfig['insertfields'];
	$fields2=array();
	$say=count($fields);

	for($i=0;$i<$say;$i++) {
		if(is_array($fields[$i])) $newitem=$fields[$i]; # accept fields both arrays and non-arrays
		else $newitem=array($fields[$i]);
		if($tableConfig['insertfieldlabels'][$i]<>'') $newitem['lefttext']=$tableConfig['insertfieldlabels'][$i];
		$fields2[]=$newitem;
	}

	#$out.="Say:$say, <br>insertFields".print_r2($fields).print_r2($fields2);
	$fields2=array_merge($fields2,$addArray);
	#$out.=print_r2($fields2);
	return $out.inputform5($fields2);

}

function inputform5($alanlar,$action='') {
	global $debuglevel,$output;
/*
 * general purpose input form generator. examples below.
 *
sadece echo yapmaz.
degistirildi. artik textarea gosterebiliyor.
$res.="alanlar:".print_r2($alan);
$res.="degerler:".print_r2($deger);
 */
	if(!is_array($alanlar)) $alanlar=array($alanlar);# convert to array if not , i.e, you dont need to use an array if you only has one input element,
	$alanlar[]=array('_insert','tip'=>'hidden','varsayilan'=>'1');
	$alansayisi=count($alanlar);

	$res.="
	<script> // script for pass generate
var keylist='abcdefghijklmnopqrstuvwxyz123456789'
var temp=''
function generatepass(){
	temp=''
	for (i=0;i<6;i++)
	temp+=keylist.charAt(Math.floor(Math.random()*keylist.length))
	return temp
}
</script>

	<form method=post enctype='multipart/form-data' ";
	
	if($action<>""){$res.=" action='$action'";};
	$res.="><table class='inputform'>";

	if($debuglevel>2) $output.=print_r2($alanlar);

	foreach($alanlar as $alan)
		$res.=inputelement2($alan);


	$res.="</table>";
	if(strstr($res,"input type='submit' ")===false) $res.="<input type=submit>";
	$res.="</form>";
	
	return $res;
        /* this function is very flexible, cok esnek yani... ingilizce yazdik diye yanlis anlasilmasin, anadoluda yazildi bu...;)
         * example usages:
         * echo inputform5('name')   # displays only an input form with field name
         * echo inputform5(array('name','surname'))  # input form with name, surname
         * echo inputform5(array(array('name','varsayilan'=>'defaultname'),'surname'))  # using default value
         * etc...
         */

}

function inputelement2($alan){

	if(!is_array($alan)) $alan=array($alan); # convert to array if not


	$solyazi=$alan['solyazi'].$alan['lefttext'];
	$alanadi=$alan['alanadi'].$alan['name'];
	$alantipi=$alan['tip'].$alan['type'];
	$sagyazi=$alan['sagyazi'].$alan['righttext'];
	$cols=$alan['cols'];
	$rows=$alan['rows'];
	$cols=($cols==""?40:$cols);
	$rows=($rows==""?10:$rows);

	if(!$alantipi or $alantipi=='') $alantipi=$alan[1]; # second array element is field type
	if(!$alantipi or $alantipi=='') $alantipi='text';


	if($alanadi=='') $alanadi=$alan[0]; # fieldname is the first element, if not defined as 'alanadi'=>'fieldname_example'
	if(!$solyazi and !in_array($alantipi,array('hidden','comment','submit'))) $solyazi=$alanadi;
	if($alantipi=='comment') $span=" colspan=3 "; # no 3 columns for comment type


	$varsayilan=$alan['varsayilan'];
	if(!$varsayilan) $varsayilan=$alan['default'];

	if(!$varsayilan and $alan['value']<>'') $varsayilan=$alan['value'];
	if(!$varsayilan and $alan['deger']<>'') $varsayilan=$alan['deger']; # ister varsayilan, ister value, ister deger de, gine de calisir..
	if($deger=='') $deger=$value=$varsayilan;
	
	if($alan['readonly']<>'') $readonly='readonly="yes"';


	$res.="<tr class='inputform'><td class='inputform' $span>";
	if($span=='') $res.=$solyazi."</td>\n<td class='inputform'>"; # no need to a new td if there is a col span
	
	switch($alantipi) {
		case 'password_with_generate':
			#$alantipi='password';
			#$alantipi='text';
    /* Password generator by cs4fun.lv */
$res.="<input id='$alanadi' type='text' name='$alanadi' value='$varsayilan'></td><td>
<input type=\"button\" value=\"Generate:\" onClick=\"$('#$alanadi').val(generatepass());\">
$sagyazi</td>\n";
            break;
    /* END Password generator by cs4fun.lv */
		case 'comment':
			$res.="$varsayilan</td>\n";
			break;
		case 'hidden&text':
			$res.="<input id='$alanadi' type='hidden' name='$alanadi' value='$varsayilan'>$varsayilan</td>\n";
			break;
		case 'password':
		case 'text':
		case 'hidden':
			$res.="<input id='$alanadi' type='$alantipi' name='$alanadi' value='$varsayilan'></td>\n";
			break;
		case 'textarea':
			$res.="<textarea id='$alanadi' cols=$cols name='$alanadi' rows=$rows $readonly>$varsayilan</textarea> <br></td>\n";
			break;
		case 'checkbox':
				if($alan['checked']) $checked="checked=".$alan['checked'];
				else $checked='';
				if($deger=='') $deger=$alanadi;
				$res.="<input type='checkbox' name='$alanadi'  value='$varsayilan'  $checked >".$alan['secenekyazisi']."</td>\n";
		break;

		case 'radio':
			foreach($alan['secenekler'] as $deger2=>$yazi2)
				$res.="<input type=radio name='$alanadi' value='$deger2' ".($varsayilan==$deger2?'checked':'').">$yazi2<br>";
			$res.="</td>";
/*
			echo print_r2($alan);
			echo "<br>(varsayilan:$varsayilan)<br>";
*/
		break;

		case 'select':			
            $res.="<select name='$alanadi'>\n\r";
            if(!is_array($alan['secenekler'])) $alan['secenekler']=$varsayilan;
            foreach($alan['secenekler'] as $deger2=>$yazi2) {
				if($varsayilan===$deger2) $sel=" selected='yes'";
				$res.="<option value='$deger2'$sel>$yazi2</option>\n\r";
			}
            #for ($j=0;$j<$sayi;$j++) $res.="<option value='".$varsayilan[$j]."'>".$varsayilan[$j]."</option>\n\r";
            $res.="</select></td>\n";
		break;

		case 'fileupload':
			$res.="\n<td><input type='file' id='$alanadi' name='$alanadi'></td>\n";
		break;
		
		case 'submit':
			$res.="\n<input type='submit' id='$alanadi' name='$alanadi' value='$deger'>\n";
		break;


		default:
			$res.="<input type='text' id='$alanadi' name='$alanadi' value='$deger'></td>\n";
	}

	if($span=='' and $alantipi<>'password_with_generate') $res.="<td>$sagyazi</td>";
	
	#$res.="<td>($alantipi)</td></tr>\n";
	$res.="</tr>\n";
	return $res;
}


function post($var){
	return mysql_real_escape_string($_POST[$var]);
}

function get($var){
	return mysql_real_escape_string($_GET[$var]);
}


if(!function_exists("tablobaslikyaz")){
function tablobaslikyaz($alan,$baslik,$extra) {// tablolistelede kullanilmak icin yazildi.
$tr="<tr class='list'>";
$td="<td class='list'>";
$th="<th class='list'>";

$alansayisi=count($alan);

		$result2=" \n $tr";
        if (count($baslik)>0)
        {
        for ($i=0;$i<$alansayisi;$i++)
                {
                if($baslik[$i]<>"") {$yaz=$baslik[$i];} else {$yaz=$alan[$i];}; $result2.="$th$yaz</th>";
                };
        }
        else
        {
        for ($i=0;$i<$alansayisi;$i++)
                {
                 $yaz=$alan[$i]; $result2.="$th$yaz</th>";
                };
        }
		for ($i=0;$i<count($extra);$i++)$result2.="$th&nbsp;</th>";
		
        $result2.="</tr>\n ";
        return $result2;
}
}
function timediffhrs($timein, $timeout){
	$timeinsec = strtotime ($timein);
	$timeoutsec = strtotime ($timeout);
	$timetot = $timeoutsec - $timeinsec;
	$timehrs  = intval($timetot/3600);
	$timehrsi =(($timetot/3600)-$timehrs)*60;
	$timemins = intval(($timetot/60) -$timehrs*60);
	return $timehrs;
}


function getFirstPart($str,$splitter){
	$position = strpos($str,$splitter);
	if($position===false) return $str;
	else return substr($str, 0,$position);
}


function getLastPart($str,$splitter){
	$position = strrpos($str,$splitter);
	return substr($str, $position + 1);
}

function get_filename_from_url($url){
  $lastslashposition = strrpos($url,"/");
  $filename=substr($url, $lastslashposition + 1);
  return $filename;
}

function removeDoubleSlash($str){
	# why this function?: some directory names contain trailing slash like /example/this/, and some portions of existing codes uses that. Until fixed, new codes are written using this, to let both style work..
	# this function may be removed after all trailing slashes removed..
	return str_replace("//","/",$str);
}

function get_filename_extension($filename) {

		$lastdotposition = strrpos($filename,".");

		if ($lastdotposition === 0)	  { $extension = substr($filename, 1); }
		elseif ($lastdotposition == "")  { $extension = $filename; }
		else							 { $extension = substr($filename, $lastdotposition + 1); }

		return strtolower($extension);

}

if(!function_exists('securefilename')){
function securefilename($fn){	
	$ret=str_replace(array("\\",'..','%','&'),array('','',''),$fn);
	#$ret=escapeshellarg($ret);
	return $ret;
}
}

function passthru2($cmd,$no_remove=false,$no_escape=false){
	$cmd1=$cmd;
	if(!$no_remove) $cmd=removeDoubleSlash($cmd);
	if(!$no_escape) $cmd=escapeshellcmd($cmd);
	echo "\nexecuting command: $cmd1 \n(escapedcmd: $cmd)\n";
	passthru($cmd);
	return true;
}

function passthru3($cmd,$source=''){
	$cmd=removeDoubleSlash($cmd);
	# Echoes command and execute, does not escapeshellcmd
	echo "\n$source:Executing command: ($cmd) \n";
	passthru($cmd);
}

function date_tarih(){
	return date('Y-m-d h:i:s');
}

function my_shell_exec($cmd,$source=''){
    echo "\n$source: ".date_tarih()." Executing command: ($cmd)";
    echo shell_exec($cmd);
}

function trimstrip($str){
	return trim(stripslashes($str));
}

function isNumericField($f){
	return (substr_count($f,'int')>0 or substr_count($f,'float')>0) ;
}

function stripslashes_deep($value)
{
	$value = is_array($value) ?
				array_map('stripslashes_deep', $value) :
				stripslashes($value);

	return $value;
}


//function to validate ip address format in php by Roshan Bhattarai(http://roshanbh.com.np)
function validateIpAddress($ip_addr)
{
  //first of all the format of the ip address is matched
  if(preg_match("/^(\d{1,3})\.(\d{1,3})\.(\d{1,3})\.(\d{1,3})$/",$ip_addr))
  {
	//now all the intger values are separated
	$parts=explode(".",$ip_addr);
	//now we need to check each part can range from 0-255
	foreach($parts as $ip_parts)
	{
	  if(intval($ip_parts)>255 || intval($ip_parts)<0)
	  return false; //if number is not within range of 0-255
	}
	return true;
  }
  else
	return false; //if format of ip address doesn't matches

}

if(!function_exists('buildoption2')) {
function buildoption2($adi,$arr,$selected) {
	$res="<select name='$adi'><option value=''>Select/Sec</option>";
    foreach($arr as $ar) $res.="<option value='$ar' ".(($ar==$selected)?"selected":"").">$ar</option>";
    $res.="</select>";
    return $res;
}
}


if(!function_exists("debug_print_backtrace2")){
function debug_print_backtrace2(){
	echo "<pre>";
	debug_print_backtrace();
	echo "</pre>";
}
}

if(!function_exists("debug_backtrace2")){
function debug_backtrace2(){
	$ar=debug_backtrace();
	$out="<br>";
	array_shift($ar); # enson cagrilan zaten bu. ona gerek yok. 
	$ar=array_reverse($ar);
	foreach($ar as $a) {
		$f=$a['file'];
		$f=explode("/",$f);
		$f=array_pop($f);
		#$nf=array();
		#$nf[]=array_pop($f);
		#$nf[]=array_pop($f);
		#$nf[]=array_pop($f); # son uc elemani al. cok uzun dosya adi/yolu olmasin diye
		#$nf=array_reverse($nf);
		#$f=implode("/",$nf);
		$out.="(".$f.':'.$a['line'].':'.$a['function'].")->";
		#$out.="(".$f.'->'.$a['function'].")->";
	
	}
	return $out."<br>";	
}
}

function textarea_to_array($area,$start=array(),$end=array()){
	$templ=array();
	$templates=explode("\n",$area);
	#echo print_r2($templates);
	$templates=array_merge($start,$templates,$end);	
	
	foreach($templates as $t) {
		$t=trim($t);
		$templ[$t]=$t;
		#echo "$t -> $t ekleniyor <br>";
	}
	#echo print_r2($templ);
	# bu çalışmadı, bug var veya anlamadım: $templ=array_merge($start,$templ,$end);	
	#array_push($templ,$end);  # bunlar da çalışmadı.
	#array_unshift($templ,$start);	
	#echo print_r2($templ);
	return $templ;
/*
çok ilginç, yukardaki array_merge fonksiyonları, array'ın indexlerini değiştiriyor:
çıktısı:
* Array gosteriliyor:
Array
(
    [4096] => 4096
    [2048] => 2048
    [256] => 256
    [512] => 512
    [1024] => 1024
    [1536] => 1536
)
Array gosteriliyor:
Array
(
    [0] => Array
        (
            [0] => seÃ§
        )

    [1] => 4096
    [2] => 2048
    [3] => 256
    [4] => 512
    [5] => 1024
    [6] => 1536
    [7] => Array
        (
        )

)
* 
 * 
 */	
	
}

?>
