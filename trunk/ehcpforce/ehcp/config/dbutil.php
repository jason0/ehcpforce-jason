<?php
// this file is deprecated. not used in ehcp mostly. left for compatibility reasons.

// Ver. 1.129.5. ehcp
// .3 ve .4 e gore birkac fonksiyon eklendi. buildoption2 gibi.
// baz duzeltmeler de yapld. 

//

$clientip = getenv ("REMOTE_ADDR");
if(substr($clientip,0,4)=="65.54") {die();};

// if($clientip=="65.54.188.109") exit; // saldiri oldu
// serverda mysql extension gidince aa�daki ie yaryor..

if(!function_exists("mysql_connect"))
	{
    echo "php mysql extension is not installed.... this is a serious problem. reinstall ehcp or php-mysql; if you just installed webserver/php, try to restart webserver, php, php-fpm";
    };

GLOBAL $confdir,$ortam;
$timebase=time() + 3600 * 8;

ini_set("display_errors","1");
error_reporting (E_ALL ^ E_NOTICE);

$headers="From: info@ehcp.net";
$adminemails=array("info@ehcp.net");

function strop($str,$bas,$son) {
	return $bas.$str.$son;
}

function arrayop($arr,$op) {
	foreach($arr as $ar) $ret[]=$op($ar,"{","}");
    return $ret;
}

function alanlarial($db2,$tablo) { // adodb de calsyor.
	foreach($db2->MetaColumnNames($tablo) as $alan) $alanlar[]=$alan;
    return $alanlar;
}

function andle($s1,$s2) { //iki string'in andlenmi halini bulur. bir bosa "and" kullanlmaz. delphiden aldim..:)
  if($s1=='')$s1=$s2;
  elseif ($s2<>'')$s1=$s1.' and '.$s2;
  return $s1;
}

function tabloayar($tablo) { // gine dinamik programlarda kullaniliyor... dbden kod almada...
	global $output, ${$tablo.'_alanlar'};
	if(!${$tablo.'_alanlar'}) return false;
    else return true;
}

function tabloolustur($baslik,$alan,$linkyazi,$linkdosya,$linkalan) {
     // henuz yazilmadi..
}

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

function basavirgul($eski,$yeni) { // alanlarn arasna virgul yerlestirmek icin,
	if($eski<>"") $virgul=",";
    else $virgul="";
    return $eski.$virgul.$yeni;
}

function satirdegistir($htmlkodu,$iceren,$yenisatir) { // bir kod satirini bulup degistirmek icin. evalde kullanilan kodlar.
 	global $output;
        $kodsatirlar=explode("\n",$htmlkodu);
        //$output.=print_r2($kodsatirlar);
        for($i=0;$i<count($kodsatirlar);$i++) {
            if(strstr($kodsatirlar[$i],$iceren)) $kodsatirlar[$i]=$yenisatir;
        }
        //$output.=print_r2($kodsatirlar);
        $htmlkodu=implode("\n",$kodsatirlar);
        return $htmlkodu;
}

function icerensatiribul($htmlkodu,$iceren) {
	$kodsatirlar=explode("\n",$htmlkodu);
	for($i=0;$i<count($kodsatirlar);$i++) {
	    if(strstr($kodsatirlar[$i],$iceren)) return $kodsatirlar[$i];
	}
}


function kasabakiyegoster($kullaniciadi,$aktifkasa) {
	$query="select sum(borc) as borctoplam,sum(alacak) as alacaktoplam, sum(borc)-sum(alacak) as bakiye from kasa where kasasahibi='$kullaniciadi' and kasakodu='$aktifkasa'";
	$res=dbresult($query,array("borctoplam","alacaktoplam","bakiye"));
	$out.="<br>Borctoplam: ".number_format($res[0])." <br>
	Alacaktoplam: ".number_format($res[1])." <br>
	Bakiye: ".number_format($res[2])." <br>";
    return $out;
}

function kasaozetgoster($kullaniciadi,$aktifkasa) {
	$query="select sum(borc) as borctoplam,sum(alacak) as alacaktoplam, sum(borc)-sum(alacak) as bakiye from kasa where kasasahibi='$kullaniciadi' and kasakodu='$aktifkasa'";
	$res=dbresult($query,array("borctoplam","alacaktoplam","bakiye"));
	$out.="<br>Aktif kullanc/bayi: $kullaniciadi
    <br>Aktif kasa: $aktifkasa
	<br>Borctoplam: ".number_format($res[0])." <br>
	Alacaktoplam: ".number_format($res[1])." <br>
	Bakiye: ".number_format($res[2])." <br>";
    return $out;
}

function kayitsayisi2($tablo,$filtre) {
    // kayitsayisi ndan farki, her db uzerinde calisir, db_query
	$query="select count(*) as count from $tablo";
	if($filtre!=""){$query.=" where $filtre";};
	//echo "<br>$query<br>";
	$result=db_query($conn,$query);
	if(!$result) {
    	return -1;
    } else {
	    $r=db_fetch_array($result);
	    $sayi=$r["count"];
	    return $sayi;
	};
};

function buildquery3($select,$filtre,$sirala='',$baslangic=0,$satirsayisi=0){ // v1.0
// buildquery2 den farki limit
    GLOBAL $dbtype;

    $res=$select;
	if($filtre<>"") {
	    $res.=" where $filtre";
	};

	if($sirala<>"") {
	    $res.=" order by $sirala";
	};
	
	if($satirsayisi>0)$res.=" limit $baslangic, $satirsayisi";
	
    return $res;
}


function buildquery2($select,$filtre,$orderby){ // v1.0
    GLOBAL $dbtype;

    $res=$select;
	if($filtre<>"") {
	    $res.=" where $filtre";
	};

	if($sirala<>"") {
	    $res.=" order by $sirala";
	};
    return $res;
}

if(!function_exists('buildoption2')) {
function buildoption2($adi,$arr,$selected) {
	$res="<select name='$adi'><option value=''>Select/Sec</option>";
    foreach($arr as $ar) $res.="<option value='$ar' ".(($ar==$selected)?"selected":"").">$ar</option>";
    $res.="</select>";
    return $res;
}
}

function tablolistele3_5_4($tablo,$baslik,$alan,$filtre,$sirala,$linkyazi,$linkdosya,$linkalan,$baslangic,$satirsayisi,$aramayap=true)
{
// bu suanda eksik calisiyor. dige yerde yapmistim. ordan alacam.
// tablolistele3_4 den fark bilgilerin belli gruplarda listelenmesi. ileri geri tular v.b.
// bir farki da echo yapmaz
// 3_5_2 den fark, mssqlden de okuyabilmesi olacak.. yeni yazyorum. adodb ye gectim.

GLOBAL $confdir, $dbtype,$output,$aranan,$arananalan,$app;
include($confdir."dbconf.php");

$color1="#FFE8B2";
$color2="#E2E2E2";

if(!isset($baslangic1)) $baslangic1=$baslangic;
if(!isset($satirsayisi1)) $satirsayisi1=$satirsayisi;
if(!isset($baslangic1)) $baslangic1=0;
if(!isset($satirsayisi1)) $satirsayisi1=10;
if($baslangic1=='') $baslangic1=0;
if($satirsayisi1=='') $satirsayisi1=10;


$result2="";
$alansayisi=count($alan);
$alansayisi2=count($linkyazi);
$satirno=0;


// listelemedeki arama fonksiyonlary:
degiskenal(array("arananalan","aranan","hepsi"));

if($hepsi<>"1") {
$sess_arananalan=$_SESSION['sess_arananalan'];
$sess_aranan=$_SESSION['sess_aranan'];
} else {
			$_SESSION['sess_arananalan']="";
			$_SESSION['sess_aranan']="";
}

if($arananalan<>"" or $sess_arananalan<>"") $output.="Arananalan:($arananalan), aranan:($aranan) , sess_arananalan:($sess_arananalan), sess_aranan($sess_aranan)..<br>";

        if($aranan<>""){
			$_SESSION['sess_arananalan']=$arananalan;
			$_SESSION['sess_aranan']=$aranan;
			$baslangic=0;
        	
            if($arananalan==""){
            	$output.="Aranacak Alany belirtmediniz. Bir alan seçiniz.";
            } else {
           	$filtre=andle($filtre,"$arananalan like '%$aranan%'");
                //$output.="Filtre: $filtre <br>";
            }
        } elseif($sess_arananalan<>"") { // bu session olayy, arama yapynca sayfalamanyn çaly?asy için
			$filtre=andle($filtre,"$sess_arananalan like '%$sess_aranan%'");
		} else {
			$_SESSION['sess_arananalan']="";
			$_SESSION['sess_aranan']="";
		}
//------------------ arama bitti -------------------

$kayitsayisi=kayitsayisi($tablo,$filtre);//$res[0];

if($kayitsayisi==0){
	$result2.=$app->sayinmylang("norecordfound");
	return $result2;
}

$selectalan=$alan;
if(!in_array($linkalan,$selectalan)) array_push($selectalan,$linkalan);

//$query=buildquery3("select ".selectstring($selectalan)." from $tablo",$filtre,$sirala,$baslangic,$satirsayisi);
$query=buildquery2("select ".selectstring($selectalan)." from $tablo",$filtre,$sirala);
//$result2.="<hr>query: ($query) <hr>filt: ($filtre)";

//$res = mysql_db_query($dbadi, $query);

include_once("adodb/adodb.inc.php");
//$db = NewADOConnection("mysql");
//$db->connect($dbhost,$dbkullaniciadi,$dbsifre,$dbadi);

$res = $app->conn->selectlimit($query,$satirsayisi,$baslangic);


// echo print_r2($res);
//$output.= "**query: $query <br>";

if ($res) {

        $result2.= "\n<table border=1>";
        if($kayitsayisi>0)$result2.=tablobaslikyaz($alan,$baslik,$linkyazi);
        while (! $res->EOF )
                {
		$r=$res->FetchRow();
		//$output.=print_r2($r);
		
                if(iseven($satirno)){$satirrengi=$color1;} else {$satirrengi=$color2;};$satirno++;
                $result2.="<tr bgcolor='$satirrengi'>";
                for ($i=0;$i<$alansayisi;$i++)
                        {
                        if(is_array($alan[$i])) $al=$alan[$i][0]; else $al=$alan[$i];
                        $yaz=$r[$al];
                        if($yaz==""){$result2.="<td>&nbsp</td>";}
                                        else { // buras 1.128.3 de degisti. eskiden sayy otomatik say gibi yazyordu. telefonlarda sknt oluyordu.
	                                        //if(is_numeric($yaz)){$yaz="<p align=right>".number_format($yaz,2)."</p>";};
	                                        if(is_array($alan[$i])) {
                                            	if($alan[$i][1]=="sayi") $yaz="<p align=right>".number_format($yaz,2)."</p>";
	                                        }
	                                        $result2.="<td>$yaz</td>";
                                        };
                        //if($al==$linkalan){$link=$r[$al];};
                        };
                        $link=$r[$linkalan];
                for ($i=0;$i<$alansayisi2;$i++)
                        {
                        $ly=$linkyazi[$i];
                        $ld=$linkdosya[$i];
                        $char="&";
                        if(strpos($ld,"?")===false)$char="?";
                        $result2.="<td><a href='$ld$char$linkalan=$link'><img src='$ly' border='0'></a></td>";
                        }

                $result2.= "</tr>\n";
                }

        $result2.= "</table>";
        $ilerimiktar=$baslangic+$satirsayisi;
        $result2.=ilerigeriekle($kayitsayisi,$baslangic,$satirsayisi,$querystring);

        $self=$_SERVER['PHP_SELF'];
	    $querystring=$_SERVER['QUERY_STRING'];
	    $self2=$self."?".$querystring;
        // aramalarn ayarlanmas.
        
        if($aramayap and $kayitsayisi>0){
            $arama="<form method=post>Arama yap:".buildoption2("arananalan",$alan,$arananalan)."<input type=text name=aranan value='$aranan'><input type=submit value=Ara></form>";
            $result2.=$arama;
        }
            if($aranan<>""){
            	$result2.="<a href=$self2>Hepsini Listele</a>";
            }

        //mysql_free_result($result);
        }
        else
        {
        $output.="Bir hata olustu:<br>sql:$query<br>".mysql_error(); //.$res->ErrorMsg();
        };
// $result2.="<br>tablo bitti.<br>";
//echo "query:$query";
$result2.=$app->conn->ErrorMsg();
return $result2;
};//fonksiyon

function ilerigeriekle($kayitsayisi,$baslangic,$satirsayisi,$querystring) {
	global $output,$app;
	if(!isset($baslangic))$baslangic=0;
	if(!isset($satirsayisi))$satirsayisi=10;

    $ilerimiktar=$baslangic+$satirsayisi;
    $self=$_SERVER['PHP_SELF'];
    $querystring=$_SERVER['QUERY_STRING'];
	$querystring=str_replace(array("&baslangic=$baslangic&satirsayisi=$satirsayisi","&&"),array("","&"),$querystring);
    $self2=$self."?".$querystring;

//    $output.="$baslangic,$satirsayisi <br>";
        if($satirsayisi>0) {
	        $sondanoncesi=$kayitsayisi-$satirsayisi;
	        $querystring=str_replace("baslangic=$baslangic&satirsayisi=$satirsayisi","",$querystring);
	                // asagidaki tabloya bu baslangic tekrar gitmesin diye. asagida zaten ekleniyor.
	        //if($querystring<>"")$querystring.="&"; // bialtsatrda ?den sonra yazmak i�n. ileri geride kullanlyor.

	        if($ilerimiktar<$kayitsayisi) {
	                $ileri="<a href=$self2&baslangic=$ilerimiktar&satirsayisi=$satirsayisi>&gt</a>";
	                $son="<a href=$self2&baslangic=$sondanoncesi&satirsayisi=$satirsayisi>&gt&gt</a>";
            } else {
	                $ileri="&gt";
	                $son="&gt&gt";
            };

	        if($baslangic>0) {
	                $geri="<a href=$self2&baslangic=".($baslangic-$satirsayisi)."&satirsayisi=$satirsayisi>&lt</a>";
	                $bas="<a href=$self2&baslangic=0&satirsayisi=$satirsayisi>&lt&lt</a>";
            } else {
	                $geri="&lt";
	                $bas="&lt&lt";
            };

	        if($kayitsayisi>$satirsayisi) {
            	$result2.= round(($baslangic/$satirsayisi)+1).".page:  (".($baslangic+1)."-".($baslangic+$satirsayisi).". records) (in each page $satirsayisi record)<br> $bas &nbsp  $geri $ileri &nbsp $son <br>";
                $sayfalar="Pages:";
                for($sayfa=0;$sayfa<($kayitsayisi/$satirsayisi);$sayfa++)
	        		$sayfalar.="<a href=$self2&baslangic=".($sayfa*$satirsayisi)."&satirsayisi=$satirsayisi>".($sayfa+1)." </a> &nbsp;";
            }

        };

        if($kayitsayisi>0) $reccount=$app->sayinmylang("recordcount").$kayitsayisi;
        $result2.=$sayfalar.$arama.$reccount."<br>";
        return $result2;

}


function kayitbilgi($tablo,$alanlar,$filtre) {
	$degerler=alanal2($tablo,$alanlar,$filtre);
    $res.="<table>";
    $sayi=count($alanlar);
    for($i=0;$i<$sayi;$i++){
    	$res.="<tr><td> $alanlar[$i] </td><td> $degerler[$i] </td></tr>";
    }
    $res.="</table>";
    return $res;
}

function checkpass4($mysqldbadi,$tablo,$kullanicialan,$sifrealan,$kullanici,$sifre) {
	GLOBAL $confdir;
	include($confdir."dbconf.php");

	$mysqldbhost=$dbhost;
	$conn=mysql_connect($mysqldbhost, $mysqlkullaniciadi, $mysqlsifre);
	if(!$conn){echo mysql_error();die ("<br><br><br>mysql'e Baglanilamadi");};


	$passtable=$tablo;
	$userfield=$kullanicialan;
	$passfield=$sifrealan;

	$kullanici=trim($kullanici);
	$sifre=trim($sifre);


	$query="select count(*) as count from $passtable where $userfield='$kullanici' and $passfield='$sifre'";
	$result=mysql_db_query($mysqldbadi,$query);

	if ($result)
	{
	while ($r = mysql_fetch_array($result))
	    {
	    $sayi=$r["count"];
	    if($sayi>0){$res=true;}else{$res=false;};
	    }
	}
	else
	{echo "Database'e baglanirken hata olustu. query:$query";exit;};


	return $res;
}


function trimarray($arr) {
    $sayi=count($arr);
	for($i=0;$i<$sayi;$i++) $arr[$i]=trim($arr[$i]);
    return $arr;
}


function selectstring($alanlar) {
	//if(count($alanlar)==0) return false;
    $res=$alanlar[0];
    $alansayisi=count($alanlar);

    for($i=1;$i<$alansayisi;$i++) {
		if(trim($alanlar[$i])<>"")$res.=",".$alanlar[$i];
	}
    return $res;
}

function tablolistele_magaza($tablo,$alanlar,$filtre) {
    return tablolistele_magaza2($tablo,$alanlar,$filtre,"magazatemplate","magazaurun");
}

function tablolistele_magaza2($tablo,$alanlar,$filtre,$magazatemplate,$uruntemplate) {
    GLOBAL $confdir;
    include($confdir."dbconf.php");

    $uruntemplate=htmlekle2($uruntemplate);
    $magazatemplate=htmlekle2($magazatemplate);

    $result2="";
    $alansayisi=count($alanlar);

    for($i=0;$i<$alansayisi;$i++) {
        $alanara[$i]="{".$alanlar[$i]."}";
    }
    // $result2.=print_r2($alanara);
    //$result2.="uruntemplate:".$uruntemplate."<br><br>";

    $query="select ".selectstring($alanlar)." from $tablo";
    if($filtre<>""){$query.=" where $filtre";};
    //$result2.="query: $query".print_r2($alan).print_r2($alanara);

    $result = mysql_db_query("$dbadi", $query);
        if ($result) {
            // template'i loop parcalarina ayir. ortadakini al.

            $parcalar=explode("{loop}",$magazatemplate);

            $result2.=$parcalar[0];
            while ($r = mysql_fetch_row($result)) {
                $res=$parcalar[1];
                //$r = mysql_fetch_array($result);
                $urun=str_replace($alanara,$r,$uruntemplate);
                //$result2.=print_r2($r);
                $res=preg_replace("/{urun}/",$urun,$res,1); // sadece bir kez replace yapmas i�n...

                // ayni loop icinde baska urunler varsa al..
                while(strstr($res,"{urun}")) {
                    $r = mysql_fetch_row($result);
                    if($r) { $urun=str_replace($alanara,$r,$uruntemplate); }
                    else $urun="";
                    $res=preg_replace("/{urun}/",$urun,$res,1);
                }
                $result2.=$res;

            }
            /* while ($r = mysql_fetch_array($result)) {
                $result2.=str_replace($alanara,$r,$uruntemplate);
                //$result2.= "<br>";
            }*/
            $result2.=$parcalar[2];
        } else {
            $result2.="bir hata olustu.".mysql_error();
        }

        $result2.="Burdaki �n Says:". kayitsayisi($tablo,$filtre)."<br>";
    return $result2;
}

function tablolistele_magaza_tekurun($tablo,$alanlar,$filtre,$uruntemplate) {
// yukardaki kodun biraz tekrar gibi oldu, burdaki kod yukarya adapte edilebilir, biraz daha toparlandktan sonra.
// ama imdilik yukardakine dokunmadm. yukarda hem urunu alyor, hem de loop iliyor, burda sadece rn alnyor.

    GLOBAL $confdir;
    include($confdir."dbconf.php");

    $uruntemplate=htmlekle2($uruntemplate);
    $result2="";
    $alansayisi=count($alanlar);
    for($i=0;$i<$alansayisi;$i++) {
        $alanara[$i]="{".$alanlar[$i]."}";
    }

    $query="select ".selectstring($alanlar)." from $tablo";
    if($filtre<>""){$query.=" where $filtre";};

    $result = mysql_db_query("$dbadi", $query);
        if ($result) {
            while ($r = mysql_fetch_row($result)) {
                $result2.=str_replace($alanara,$r,$uruntemplate);
            }
        } else {
            $result2.="bir hata olustu.".mysql_error();
        }
    return $result2;
}



function resimsil($resimdizin,$dosyaadi) {
	if($dosyaadi=="") {
    	return "Silinecek Resim yok.";
    }
    elseif(!unlink($resimdizin.$dosyaadi)){
    		$mes="Resim dosyas sistemden silinemedi:$resimdizin.$eskidosyaadi<br>";
            emailadminsvelog($mes,$mes);
            return $mes;        }
    else{
    	return("Resim dosyas silindi.<br>");
    }
}

function emailadminsvelog($konu,$mesaj,$baslikbilgisi="") {
	logyaz($konu.$mesaj);
    emailadmins($konu,$mesaj,$baslikbilgisi);
}

function emailadmins($konu,$mesaj,$baslikbilgisi="") {
	global $adminemails;
	foreach ($adminemails as $email) {
    	mail($email,$konu,$mesaj,$baslikbilgisi);
    }
}

function checklogin(){
    global $kullaniciadi,$aktifkasa;
    $kullaniciadi=$_SESSION['kullaniciadi'];
    $sifre=$_SESSION['sifre'];
    $isloggedin=$_SESSION['isloggedin'];
    $aktifkasa=$_SESSION['aktifkasa'];
    if(($isloggedin!=true)or($sifre=="")){header("Location: loginform.php");exit;};

}

// bur tr array yaps kullanmamn sebebi, ilerde daha modler bir ekleme mekanizmas yapmaya �lmak.
// bu sayede sadece array i�ri�ni de�tirerek, d�r kodlarla oynamadan yeni alanlar ekleyebilece�m.

function aramadizisi($alanlar,$degerler,$tablo) {
    $tabloozellik=tabloozellikleri($tablo);
    $hepsibos=true;
    for($i=0;$i<count($alanlar);$i++){   // arama dizsini olustur.
        if($degerler[$i]<>"") $hepsibos=false;
        $arama[]=array("alanadi"=>$alanlar[$i],"type"=>$tabloozellik["alanlarbyname"][$alanlar[$i]]["type"],"karsilastir"=>"=","deger"=>$degerler[$i]);
                         // string ise like kullan, date ise tam kullan, int-real ise tirnak koyma.
    }
    if($hepsibos) return false;
    else return $arama;
}


function aramafiltresi($arama,$filtre) { // diziden filtreyi olustur:
// $arama[]=array("alanadi"=>"dernekid","type"=>"string","karsilastir"=>"=","deger"=>$kullaniciadi);   // arama array'i, 0->alan, 1->tip, 2-> karsilastirma, 3->deger
                      // string ise like kullan, date ise tam kullan, int-real ise tirnak koyma.

    for($i=0;$i<count($arama);$i++){
        switch ($arama[$i]["type"]) {
            case "string": $filtre=filterstring($filtre,$arama[$i]["deger"],$arama[$i]["alanadi"]." like '%".$arama[$i]["deger"]."%'");break;
            case "date":
            case "datetime": $filtre=filterstring($filtre,$arama[$i]["deger"],$arama[$i]["alanadi"].$arama[$i]["karsilastir"]."'".$arama[$i]["deger"]."'") ;break;
            case "int":
            case "real": $filtre=filterstring($filtre,$arama[$i]["deger"],$arama[$i]["alanadi"].$arama[$i]["karsilastir"].$arama[$i]["deger"]) ;break;
            default:return "bilinmeyen_alan_turu_".$arama[$i]["type"]."_";
        }
    }
    return $filtre;
}



	function tabloozellikleri($tablo) {
        mysql_select_db("vidinli_my_db");
        $result = mysql_query("SELECT * FROM $tablo where id=0");
        $fields = mysql_num_fields($result);
            // bur tr array yaps kullanmamn sebebi, ilerde daha modler bir ekleme mekanizmas yapmaya �lmak.
            // bu sayede sadece array i�ri�ni de�tirerek, d�r kodlarla oynamadan yeni alanlar ekleyebilece�m.

        for ($i=0; $i < $fields; $i++) {
            $type  = mysql_field_type($result, $i);
            $name  = mysql_field_name($result, $i);
            $len   = mysql_field_len($result, $i);
            $flags = mysql_field_flags($result, $i);
            $tabloalanlar["alanlar"][]=array("type"=>$type,"name"=>$name,"len"=>$len);
            $tabloalanlar["alanlarbyname"][$name]=array("type"=>$type,"len"=>$len);
        }
        return $tabloalanlar;
    }


function mail2($kime,$subject,$mesaj,$headers){
	if($ortam=="gercek")mail($kime,$subject,$mesaj,$headers);
}

function list_array ($array) {
    while (list ($key, $value) = each ($array)) {
    $str .= "<b>$key:</b> $value<br>\n";
    }
    return $str;
}


function dovizcevir($girisbirim,$cikisbirim,$girismiktar){
    if($girisbirim=="")$girisbirim="YTL";
    $kur["USD"]=1.37;   // bunlar daha sonra dovizli sitelerden alinmasi lazim. ***
    $kur["EUR"]=1.75;
    $kur["YTL"]=1;

    $cikismiktar=$ytltutar=$girismiktar*$kur[$girisbirim];
    if($cikisbirim<>"YTL") $cikismiktar=$ytltutar/$kur[$cikisbirim];
    return $cikismiktar;
}

function alanal($tablo,$alan,$filtre){
	$query="select $alan from $tablo";
    if($filtre<>"")$query.=" where $filtre";
    $res=dbresult($query,array($alan));
    return $res[0];
}

function alanal2($tablo,$alanlar,$filtre){
	$query="select ".selectstring($alanlar)." from $tablo ";
    if($filtre<>"")$query.=" where $filtre";
    $res=dbresult($query,$alanlar);
    return $res;
}

function alanal3($tablo,$alanlar,$filtre){
	$query="select ".selectstring($alanlar)." from $tablo ";
    if($filtre<>"")$query.=" where $filtre";
    $res=dbresult2($query,$alanlar);
    return $res;
}


function tarih1(){
	GLOBAL $timebase;
	return date('Y-m-d H:i:s', $timebase);
}

function tarih2(){
	GLOBAL $timebase;
	return date('Y-m-d', $timebase);
}

function bilgigoster($tablo,$yazi,$alan,$filtre){
    $query="select * from $tablo";
    if($filtre<>"")$query.=" where $filtre";
	$bilgi=dbresult($query,$alan);
    $res="<br><table border=1>";$i=0;
    foreach($alan as $al){
        $al2=$yazi[$i]==""?$al:$yazi[$i];
    	$res.="<tr><td>$al2 :</td><td>".$bilgi[$i++]."&nbsp</td></tr>";
    }
    $res.="</table>";// <br>Filtre: $filtre <br>Kaytsays:".kayitsayisi($tablo,$filtre);
    return $res;
}

function db_query($conn,$query){
    GLOBAL $dbtype;// adodbye gecildi. burasi artik kullanilmiyor..
    switch($dbtype){
            case "mysql": return mysql_query($query,$conn);break;
            case "pgsql": if($conn) return pg_query($conn,$query) ;
                            else return pg_query($query); // eger conn belirtilmediyse..
                            break;
            default: echo "dbtype is not specified or not supported in dbconf.php config file: dbtyep is $dbtype !";return false;
            }
}

function db_dosomething(){
    // this is only a template, bvidinli.
    GLOBAL $dbtype;
    switch($dbtype)
            {
            case "mysql": ;break;
            case "pgsql": ;break;
            default: echo "dbtype is not specified or not supported in dbconf.php config file: dbtyep is $dbtype !";return false;
            }
}


function db_fetch_array($result){
    GLOBAL $dbtype;
    switch($dbtype)
            {
            case "mysql": return mysql_fetch_array($result);break;
            case "pgsql": return pg_fetch_array($result);break;
            default: echo "dbtype is not specified or not supported in dbconf.php config file: dbtyep is $dbtype !";return false;
            }
}


function buildquery($select,$baslangic,$satirsayisi){
    // bu mysql ile pgsql arasindaki limit cumlecigi farkindan dolayi yazildi. asagida fark belli.
    GLOBAL $dbtype;
    switch($dbtype)
            {
            case "mysql": return $select." limit $baslangic,$satirsayisi";break;
            case "pgsql": return $select." limit $satirsayisi offset $baslangic";break;
            default: echo "dbtype is not specified or not supported in dbconf.php config file: dbtyep is $dbtype !";return false;
            }
}


function tablolistele8($conn,$tablo,$baslik,$alan,$filtre,$sirala,$linkyazi,$linkdosya,$linkalan,$baslangic,$satirsayisi){
    // diger tablolistelelerden farki, connection bazli calismasi, yani parametre olarak conn alir, onu kullanir.

    //GLOBAL $confdir;
    //include($confdir."dbconf.php");

    $color1="#FFE8B2";
    $color2="#E2E2E2";

    $result2="";
    $alansayisi=count($alan);
    $alansayisi2=count($linkyazi);
    $satirno=0;

    $query="select * from $tablo";
    if($filtre<>""){$query.=" where $filtre";};
    if($sirala<>""){$query.=" order by $sirala";};
    if($satirsayisi>0) $query=buildquery($query,$baslangic,$satirsayisi);
    //echo "query: $query <br>";
    $result = db_query($conn,$query);

    if ($result)
            {
            $result2.= "\n<table border=0>\n<tr border=1>";
            // once basliklari yaz.
            if (count($baslik)>0)
            {
            for ($i=0;$i<$alansayisi;$i++)
                    {
                    if($baslik[$i]<>"") {$yaz=$baslik[$i];} else {$yaz=$alan[$i];}; $result2.="<td>$yaz</td>";
                    };
             }
             else
             {
            for ($i=0;$i<$alansayisi;$i++)
                    {
                     $yaz=$alan[$i]; $result2.="<td>$yaz</td>";
                    };
             }
    //        for ($i=0;$i<$alansayisi2;$i++)$result2.="<td>$linkyazi[$i]</td>";
            $result2.="</tr>\n ";
            while ($r = db_fetch_array($result))
                    {
                    $satirrengi=(iseven($satirno++))?$color1:$color2;

                    $result2.="<tr bgcolor='$satirrengi'>";
                    for ($i=0;$i<$alansayisi;$i++)
                            {
                             $al=$alan[$i];
                            $yaz=$r[$al];
                            if($yaz==""){$result2.="<td>&nbsp</td>";}
                                            else {
                                            if(is_numeric($yaz)){$yaz="<p align=right>".number_format($yaz,2)."</p>";};
                                            $result2.="<td>$yaz</td>";

                                            };

                            //if($al==$linkalan){$link=$r[$al];};
                            };
                            $link=$r[$linkalan];
                    for ($i=0;$i<$alansayisi2;$i++)
                            {
                            $ly=$linkyazi[$i];
                            $ld=$linkdosya[$i];
                            $char="?";
                            if(strpos($ld,"?"))$char="";
                            if($ly<>"")$result2.="<td><a href='$ld$char$linkalan=$link' target='_blank'><img src='$ly' border='0'></a></td>";
                            }

                    $result2.= "</tr>\n";
                    }

            //$query="select count(*) as count from $tablo";
            //if($filtre<>""){$query.=" where $filtre";};
            //$res=dbresult($query,array("count"));
            $kayitsayisi=kayitsayisi($tablo,$filtre);//$res[0];


            //$result2.= "<tr><td>Kayt Says: $res[0] </td></tr>";
            $result2.= "</table>";
            $ilerimiktar=$baslangic+$satirsayisi;

            if($satirsayisi>0)
            {
            $sondanoncesi=$kayitsayisi-$satirsayisi;
            $querystring=$_SERVER['QUERY_STRING'];
            $querystring=str_replace("baslangic=$baslangic&satirsayisi=$satirsayisi","",$querystring);
                    // asagidaki tabloya bu baslangic tekrar gitmesin diye. asagida zaten ekleniyor.
            if($querystring<>"")$querystring.="&"; // bialtsatrda ?den sonra yazmak in. ileri geride kullanlyor.
            if($ilerimiktar<$kayitsayisi)
                    {
                    $ileri="<a href=".$_SERVER['PHP_SELF']."?".$querystring."baslangic=$ilerimiktar&satirsayisi=$satirsayisi>&gt</a>";
                    $son="<a href=".$_SERVER['PHP_SELF']."?".$querystring."baslangic=$sondanoncesi&satirsayisi=$satirsayisi>&gt&gt</a>";
                    }
                    else
                    {
                    $ileri="&gt";
                    $son="&gt&gt";
                    };

            if($baslangic>0)
                    {
                    $geri="<a href=".$_SERVER['PHP_SELF']."?".$querystring."baslangic=".($baslangic-$satirsayisi)."&satirsayisi=$satirsayisi>&lt</a>";
                    $bas="<a href=".$_SERVER['PHP_SELF']."?".$querystring."baslangic=0&satirsayisi=$satirsayisi>&lt&lt</a>";
                    }
                    else
                    {
                    $geri="&lt";
                    $bas="&lt&lt";
                    };

            if($kayitsayisi>$satirsayisi)$result2.= "$baslangic .. ".($baslangic+$satirsayisi)."  $bas &nbsp  $geri $ileri &nbsp $son ";
            };
            $result2.="<br>Kayt Says: $res[0] <br>";
            //mysql_free_result($result);
            }
            else
            {
            echo "Bir hata olustu:<br>sql:$query<br>";echo mysql_error();
            };

    return $result2;
};//fonksiyon


function redirecttovidinlinet(){
return; // vidinli.com elden gidecek diye yazlmt.
        $url=$_SERVER["SERVER_NAME"].$_SERVER["PHP_SELF"];
        $url2=$_SERVER['QUERY_STRING'];
        if($url2<>"")$url.="?".$url2;
        if(strpos($url,"vidinli.com")){
                $url=str_replace("vidinli.com","vidinli.net",$url);
                header("Location: http://$url");
                exit;
        };
};

function htmlekle3($cerceve,$isaretler,$icerikler) {
	$output2=htmlekle2($cerceve);
    $isaretcount=count($isaretler);
         for($i=0;$i<$isaretcount;$i++){
         	$output2=str_replace($isaretler[$i],$icerikler[$i],$output2);
         }
    return $output2;
}

function cerceveletyaz3($cerceve,$isaretler,$icerikler){
		global $output;
         $isaret="{ickisim}";
         $cerceve=htmlekle2($cerceve);
         $output2=str_replace($isaret,$output,$cerceve); // cerceve icinde isareti ara, isaret yerine simdiye kadarki outputu koy.
         $isaretcount=count($isaretler);
         for($i=0;$i<$isaretcount;$i++){
         	$output2=str_replace($isaretler[$i],$icerikler[$i],$output2);
         }
         echo $output2;
}


function cerceveletyaz($output,$cerceve){
         $isaret="{ickisim}";
         $cerceve=htmlekle2($cerceve);
         $output2=str_replace($isaret,$output,$cerceve); // cerceve icinde isareti ara, isaret yerine simdiye kadarki outputu koy.
         echo $output2;
}

function cerceveletyaz2($cerceve = "standartcerceve"){
	global $output,$standartcerceve;

	if($cerceve=="standartcerceve")$cerceve=$standartcerceve; // bu config.php de tanimli
         $isaret="{ickisim}";
         $cerceve=htmlekle2($cerceve);
//	echo "test: cerceve: $cerceve";
         $output2=str_replace($isaret,$output,$cerceve); // cerceve icinde isareti ara, isaret yerine simdiye kadarki outputu koy.
         echo $output2;
}

function echo2($str){
        GLOBAL $output;
        $output.=$str;
}

function writeoutput($file, $string, $mode="a") {
                if (!($fp = fopen($file, $mode))) {
                        echo "hata**: dosya acilamadi: $file (writeoutput) !";
                        return false;
                }
                if (!fputs($fp, $string . "\n")) {
                        fclose($fp);
                        echo "hata**: dosyaya yazilamadi: $file (writeoutput) !";
                        return false;
                }
                fclose($fp);
                return true;
}



function httpkontrol($adres,$kontrolet)
{
$okunan="";
$fp=fopen($adres,"r");
  if($fp)
        {
        $okunan=fread($fp, 2096);
                fclose($fp);
        };
$oku=substr($okunan,0,30);
//echo "$oku <br>";

if(strstr($okunan,$kontrolet))
{
                return $okunan;
}
else
{
                return false;
};
};

function executeprog2($prog){ // echoes output.
	passthru($prog, $val);
	return ($val==0);
}

function executeprog($prog){ // does not echo output. only return it.
	$fp = popen("$prog", 'r');
	$read = fread($fp, 8192);
	pclose($fp);
	return $read;
}

function pingkontrol($ip)
{
                $fp = popen ("ping -c 2 $ip", "r");
                $okunan="";
                if($fp)
                {
                                        $okunan=fread($fp, 2096);
                }
                //echo "<br>Okunan: \n $okunan \n<br>";
                if(strstr($okunan,"100% packet loss"))
                                    {
                                    //echo "<br>$ip:calismiyor";
                                    return false;
                                        }
                                                else
                                    {
                                //echo "<br><br>$ip:calisiyor";
                                        return true;
                                    };
                                        flush();

}



function print_r2($array)
{
	if (is_array($array)) return "<pre>Array gosteriliyor:\n".str_replace(array("\n" , " "), array('<br>', '&nbsp;'), print_r($array, true)).'</pre>';
	elseif ($array===null) return "(null) ";
	elseif ($array=="") return "(bosluk)";
	else {
		return "Array degil:<br>".print_r($array,true);
	}
}

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


function toplambul($tablo,$alanlar,$filtre)
{
$alansayisi=count($alanlar);
$str="sum($alanlar[0]) as sum0";
$sums=array("sum0");

for($i=1;$i<$alansayisi;$i++) {$str.=",sum($alanlar[$i]) as sum$i";$sums[]="sum$i";};

$query="select $str from $tablo ";
if($filtre<>"")$query.=" where $filtre";
//echo "<br>query:$query<br>";

$res=dbresult($query,$sums);
//$res=dbquery($query);
return $res;
};

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


#function getVariable($variables,$dotrim=false) { # classappdaki aynı kod.
function degiskenal($variables,$dotrim=false) {
	# get variables by means of $_POST or $_GET globals.., makes them secure for sql injection
	if(!is_array($variables)) $variables=array($variables); # accept non-array, single parameter

	$varCount=count($variables);
	for ($i=0;$i<$varCount;$i++) {
		$varname=$variables[$i];
		if(is_array($varname)) $varname=$varname[0]; # accept array members.. use 1st element for varname.
		if($varname=='') continue;

		global ${$varname}; # make it global at same time.. may be disabled in future..

		# comformant with code at http://www.php.net/mysql_real_escape_string
		if($_POST[$varname]<>"") {
			if(get_magic_quotes_gpc()) ${$varname}=stripslashes($_POST[$varname]);
			else ${$varname}=$_POST[$varname];
		} else {
			if(get_magic_quotes_gpc()) ${$varname}=stripslashes($_GET[$varname]);
			else ${$varname}=$_GET[$varname];
		}
		$tmp=@mysql_real_escape_string(${$varname});
		if($tmp!==False) ${$varname}=$tmp; # otherwise, without a db connection, mysql_real_escape_string returns false. this will skip that; no need to mysql_real_escape_string when there is no db conn, I think. 
		
		if($dotrim) ${$varname}=trim(${$varname});
		$values[$varname]=${$varname};
	};
	#echo print_r2($variables).print_r2($values);
	return $values; # return values as an array too... may be used after disabling global variables...
}

function sessionadegiskenyaz($degiskenler)
{
    $alansayisi=count($degiskenler);
    for ($i=0;$i<$alansayisi;$i++)
                {
                global ${$degiskenler[$i]};
                $_SESSION[$degiskenler[$i]]=${$degiskenler[$i]};
                };
    //return $degerler;
}

function sessiondandegiskenal($degiskenler) {
    $alansayisi=count($degiskenler);
    for ($i=0;$i<$alansayisi;$i++)
                {
                global ${$degiskenler[$i]};
                ${$degiskenler[$i]}=$_SESSION[$degiskenler[$i]];
                $degerler[]=${$degiskenler[$i]};
                };
    return $degerler;
}

function buildstr($bas,$degiskenler,$son) {
// str= array("{title}","{baslik}") .... þeklinde string üretmek için...
    $alansayisi=count($degiskenler);
    for ($i=0;$i<$alansayisi;$i++)
                {
                //global ${$degiskenler[$i]};
                $degerler[]=$bas.$degiskenler[$i].$son;
                };
    return $degerler;
}




function trim2($str)
{
$str=str_replace(",","",$str);
$str=str_replace(".","",$str);
$str=str_replace(" ","",$str);
return $str;
}

function iseven($x){
        if($x & 1) return false;
        else return true;
}



function logtofile($log) {
        GLOBAL $confdir;
        if(!strstr($confdir,"vidinli")) return;
        $tarih=tarih1();
        $ip = getenv ("REMOTE_ADDR");
	$referrer = getenv("HTTP_REFERER");
    $url2=$_SERVER["SERVER_NAME"].$_SERVER["PHP_SELF"].(isset($_SERVER['QUERY_STRING'])?"?":"").$_SERVER['QUERY_STRING'].":".(isset($_SERVER["PHP_AUTH_USER"]) ? $_SERVER["PHP_AUTH_USER"] : "");
    writeoutput($confdir."logfile.txt","$tarih : $ip : $log : url:$url2 : ref: $referrer");
}

function logyaz($log){
        logtodb($log);
        logtofile($log);
}

function logtodb($log1)
{
$dbadi="vidinli_my_db";
$ip = getenv ("REMOTE_ADDR");
$tarih=tarih1();
$referrer = getenv("HTTP_REFERER");
$log1="+".$log1;


$url2=$_SERVER["SERVER_NAME"].$_SERVER["PHP_SELF"].(isset($_SERVER['QUERY_STRING'])?"?":"").$_SERVER['QUERY_STRING'].":".(isset($_SERVER["PHP_AUTH_USER"]) ? $_SERVER["PHP_AUTH_USER"] : "");


$query="insert into log values('','$tarih','$ip','$log1','$referrer','$url2')";
$result=mysql_db_query($dbadi,$query);
if(!$result){echo "log yazarken hata olustu.";echo mysql_error();};

$query="select count(*) from log2 where referrer='$referrer'";

$result = mysql_db_query("$dbadi", $query);

if ($result)
{

while ($r = mysql_fetch_array($result))
{
$sayi=$r[0];
}
} else { echo "Bir hata olustu:";echo mysql_error();}

if($sayi==0)
{
        $query="insert into log2 values('','$referrer',1,'')";
}
elseif($sayi>0)
        {
         $query="update log2 set count=count+1 where referrer='$referrer'";
        };

$result = mysql_db_query("$dbadi", $query);
if(!$result){ echo "Bir hata olustu:";echo mysql_error();}

return $result;
}

function getmax($tablo,$alan){
    GLOBAL $confdir;
    include($confdir."dbconf.php");

    $query="select max($alan) as max from $tablo";
    $result=dbquery($query);
    if ( !$result )  {echo "max alirken hata olustu. ";return false;};
    $max=dbresult($query,array("max"));
    return $max[0];
}


function getmax2($tablo,$alan,$filtre)
{
        GLOBAL $confdir;
include($confdir."dbconf.php");

$query="select max($alan) as max from $tablo where $filtre";
$result=dbquery($query);
if ( !$result )  {echo "max alirken hata olustu. ";return false;};
$max=dbresult($query,array("max"));
return $max[0];
}


function dbresult($query,$alanlar)
{
GLOBAL $confdir;

include($confdir."dbconf.php");

$result=dbquery($query);
$alansayisi=count($alanlar);
$res=array();

if($result)
        {
        $r = mysql_fetch_array($result);
        for ($i=0;$i<$alansayisi;$i++)
                {
                $al=$alanlar[$i];
                $res[]=$r[$al];
                };
        };
return $res;
}

function dbresult2($query,$alanlar)
{
GLOBAL $confdir;

include($confdir."dbconf.php");

$result=dbquery($query);
$alansayisi=count($alanlar);
$res=array();

if($result)
        {
        $r = mysql_fetch_assoc($result);
        };
return $r;
}


function topluemailgonder($tablo,$alan,$filtre,$subject,$kimden,$mesaj,$replacealanlar,$mesajsayisi=0)
{
echo "Toplu email gonderme basladi:";
GLOBAL $confdir,$output;
include($confdir."dbconf.php");
$result2="";
$alansayisi=count($alan);
$replacedalanlar=arrayop($replacealanlar,"strop"); // alanlar {adi} seklinde basina { ve sonuna } ekler.

$query="select * from $tablo";
if ($filtre!=""){$query.=" where $filtre";};
if($mesajsayisi>0) $query.=" limit $mesajsayisi ";

$result = dbquery($query);

//$output.="query: $query <br>";

if ($result)
{
$result2.="<table><tr><td><ol>";
while ($r = mysql_fetch_array($result))
        {
        $email=$r[$alan];
        $id=$r["id"];

        //$header="";
        //$result2=mail("$email","$subject","$mesaj","$headers");
        //if($result2){ $result2.="<br> mail gonderildi: $mail ";}else{ $result2.="gonderilemedi:$email ";};
        //   include("../mesaj.php");
        //   $mesaj.="\n Gnderilen email: $email";
        // htmlmailgonder("bvidinli@yahoo.com",$subject,$mesaj,$from);

        $degerler=alanal2($tablo,$replacealanlar,"id=$id");

        //$output.=print_r2($replacedalanlar).print_r2($degerler);

        $mesaj2=str_replace($replacedalanlar,$degerler,$mesaj); // i�nde {adi} {soyadi} seklinde gecen sablonlarn yerine kiilerin adn soyadn vb. yerletiri.
        //$output.="meaj:".$mesaj2."<br><br>";
        $res=htmlmailgonder($email,$subject,$mesaj2,$kimden);
        if($res){$result2.="<li>email gonderildi:$email.</li>";} else { $result2.="<li>gonderilemedi:$email </li>";};


        };
$result2.="</ol></td></td></table>";

mysql_free_result($result);
} else { $result2.="Bir hata olustu:". mysql_error();}
$result2.="<br>Mail gonderme bitti.<br>";
return $result2;
};//fonksiyon



function tablolistele($tablo,$alan)
{
GLOBAL $confdir;
include($confdir."dbconf.php");
$result2="";
$alansayisi=count($alan);

$query="select * from $tablo";
$result = mysql_db_query("$dbadi", $query);

if ($result)
{
$result2.= "<table class='vidinlistyle' border=1 bordercolor='6666CC'><tr>";
for ($i=0;$i<$alansayisi;$i++)$result2.="<td>$alan[$i]</td>";
$result2.="</tr>";

while ($r = mysql_fetch_array($result))
{
$result2.="<tr>";
for ($i=0;$i<$alansayisi;$i++)
        {
        $al=$alan[$i];
        $result2.="<td>$r[$al]</td>";
        };
$result2.= "</tr>";
}

$result2.= "</table>";
mysql_free_result($result);
} else { echo "Bir hata olustu:";echo mysql_error();}
echo $result2;
return $result2;
};//fonksiyon

function tbinputform($tablo,$alan,$filtre)
{
GLOBAL $confdir;
include($confdir."dbconf.php");

$result2="";
$alansayisi=count($alan);

$query="select * from $tablo where $filtre";
echo "query:$query<br>";
$result = mysql_db_query("$dbadi", $query);

if ($result)
{
$result2.= "<table class='vidinlistyle' border=1 bordercolor='6666CC'><tr>";
for ($i=0;$i<$alansayisi;$i++)$result2.="<td>$alan[$i]</td>";
$result2.="</tr>";

while ($r = mysql_fetch_array($result))
{
$result2.="<tr>";
for ($i=0;$i<$alansayisi;$i++)
        {
        $al=$alan[$i];
        $result2.="<td>$r[$al]</td>";
        };
$result2.= "</tr>";
}
$result2.= "</table>";
mysql_free_result($result);
} else { echo "Bir hata olustu:";echo mysql_error();}
echo $result2;
return $result2;
};//fonksiyon



function satirgetir($tablo,$aranacakalan,$aranacakdeger,$getirilecekalan)
{
GLOBAL $confdir;
include($confdir."dbconf.php");
$query="select $getirilecekalan from $tablo where $aranacakalan='$aranacakdeger'";
$result = mysql_db_query("$dbadi", $query);

if ($result)
        {
        while ($r = mysql_fetch_array($result))
                {
                        $res=$r["$getirilecekalan"];
}
}
return $res;
}

function tablolistele2($tablo,$alan,$filtre,$linkyazi,$linkdosya,$linkalan)
{
GLOBAL $confdir;
include($confdir."dbconf.php");
$result2="";
$alansayisi=count($alan);
$alansayisi2=count($linkyazi);

$query="select * from $tablo";
if($filtre<>""){$query.=" where $filtre";};
$result = mysql_db_query("$dbadi", $query);

if ($result)
        {
        $result2.= "<table class='vidinlistyle' border=1 bordercolor='6666CC'><tr>";
        for ($i=0;$i<$alansayisi;$i++)$result2.="<td>$alan[$i]</td>";
        $result2.="</tr>";

        while ($r = mysql_fetch_array($result))
                {
                $result2.="<tr>";
                for ($i=0;$i<$alansayisi;$i++)
                        {
                         $al=$alan[$i];
                        $result2.="<td>$r[$al]</td>";
                        if($al==$linkalan){$link=$r[$al];};
                        };
                for ($i=0;$i<$alansayisi2;$i++)
                        {
                        $ly=$linkyazi[$i];
                        $ld=$linkdosya[$i];
                        $result2.="<td><a href='$ld?$linkalan=$link'>$ly</a></td>";
                        }

                $result2.= "</tr>";
                }

        $result2.= "</table>";
        mysql_free_result($result);
        }
        else
        {
        echo "Bir hata olustu:";echo mysql_error();
        };
echo $result2;
return $result2;
};//fonksiyon



function tablolistele3($tablo,$alan,$filtre,$sirala,$linkyazi,$linkdosya,$linkalan)
{
echo tablolistele3_3($tablo,$alan,$filtre,$sirala,$linkyazi,$linkdosya,$linkalan);
}


function tablolistele3_3($tablo,$alan,$filtre,$sirala,$linkyazi,$linkdosya,$linkalan)
{ // echo yapmaz.
GLOBAL $confdir;
include($confdir."dbconf.php");
$result2="";
$alansayisi=count($alan);
$alansayisi2=count($linkyazi);

$query="select * from $tablo";
if($filtre<>""){$query.=" where $filtre";};
if($sirala<>""){$query.=" order by $sirala";};
$result = mysql_db_query("$dbadi", $query);

if ($result)
        {
        $result2.= "<table class='vidinlistyle' border=1 bordercolor='6666CC'><tr>";
        for ($i=0;$i<$alansayisi;$i++)$result2.="<td>$alan[$i]</td>";
        for ($i=0;$i<$alansayisi2;$i++)$result2.="<td>$linkyazi[$i]</td>";
        $result2.="</tr>";

        while ($r = mysql_fetch_array($result))
                {
                $result2.="<tr>";
                for ($i=0;$i<$alansayisi;$i++)
                        {
                         $al=$alan[$i];
                        $yaz=$r[$al];
                        if($yaz==""){$result2.="<td>&nbsp</td>";}
                                        else {
                                        if(is_numeric($yaz)){$yaz="<p align=right>".number_format($yaz,2)."</p>";};
                                        $result2.="<td>$yaz</td>";

                                        };

                        //if($al==$linkalan){$link=$r[$al];};
                        };
                        $link=$r[$linkalan];
                for ($i=0;$i<$alansayisi2;$i++)
                        {
                        $ly=$linkyazi[$i];
                        $ld=$linkdosya[$i];
                        $char="?";
                        if(strpos($ld,"?"))$char="";
                        $result2.="<td><a href='$ld$char$linkalan=$link' target='_blank'>$ly</a></td>";
                        }

                $result2.= "</tr>\n";
                }
        $query="select count(*) as count from $tablo";
        if($filtre<>""){$query.=" where $filtre";};
        $res=dbresult($query,array("count"));
        //$result2.= "<tr><td>Kayt Says: $res[0] </td></tr>";
        $result2.= "</table>";
        $result2.= "Kayt Says: $res[0] <br>";
        mysql_free_result($result);
        }
        else
        {
        echo "Bir hata olustu:<br>sql:$query<br>";echo mysql_error();
        };
// echo $result2;
return $result2;
};//fonksiyon

function tablolistele3_2($tablo,$baslik,$alan,$filtre,$sirala,$linkyazi,$linkdosya,$linkalan)
{
// tablolistele3 den farki, tablo basliklarinin olmasidir.
GLOBAL $confdir;
include($confdir."dbconf.php");

$color1="#FFE8B2";
$color2="#E2E2E2";

$result2="";
$alansayisi=count($alan);
$alansayisi2=count($linkyazi);
$satirno=0;

$query="select * from $tablo";
if($filtre<>""){$query.=" where $filtre";};
if($sirala<>""){$query.=" order by $sirala";};
$result = mysql_db_query("$dbadi", $query);

if ($result)
        {
        $result2.= "\n<table class='vidinlistyle' border=0> \n<tr border=1>";

        for ($i=0;$i<$alansayisi;$i++)
                {
                if($baslik[$i]<>"") {$yaz=$baslik[$i];} else {$yaz=$alan[$i];}; $result2.="<td>$yaz</td>";
                };

        for ($i=0;$i<$alansayisi2;$i++)$result2.="<td>$linkyazi[$i]</td>";
        $result2.="</tr>\n ";

        while ($r = mysql_fetch_array($result))
                {
                if(iseven($satirno)){$satirrengi=$color1;} else {$satirrengi=$color2;};$satirno++;
                $result2.="<tr bgcolor='$satirrengi'>";
                for ($i=0;$i<$alansayisi;$i++)
                        {
                         $al=$alan[$i];
                        $yaz=$r[$al];
                        if($yaz==""){$result2.="<td>&nbsp</td>";}
                                        else {
                                        if(is_numeric($yaz)){$yaz="<p align=right>".number_format($yaz,2)."</p>";};
                                        $result2.="<td>$yaz</td>";

                                        };

                        //if($al==$linkalan){$link=$r[$al];};
                        };
                        $link=$r[$linkalan];
                for ($i=0;$i<$alansayisi2;$i++)
                        {

                        $ly=$linkyazi[$i];
                        $ld=$linkdosya[$i];
                        $char="?";
                        if(strpos($ld,"?"))$char="";
                        $result2.="<td><a href='$ld$char$linkalan=$link' target='_blank'>$ly</a></td>";
                        }

                $result2.= "</tr>\n";
                }
   		$kayitsayisi=kayitsayisi($tablo,$filtre);
        //$query="select count(*) as count from $tablo";
        //if($filtre<>""){$query.=" where $filtre";};
        //$res=dbresult($query,array("count"));
        //$result2.= "<tr><td>Kayt Says: $res[0] </td></tr>";
        $result2.= "</table>";
        $result2.= "Kayt Says: $kayitsayisi <br>";
        mysql_free_result($result);
        }
        else
        {
        echo "Bir hata olustu:<br>sql:$query<br>"; echo mysql_error();
        };
echo $result2;

return $result2;
};//fonksiyon

function tablolistele3_2_2($tablo,$baslik,$alan,$filtre,$sirala,$linkyazi,$linkdosya,$linkalan)
{
// tablolistele3 den farki, tablo basliklarinin olmasidir.
// echo da yapmaz
GLOBAL $confdir;
include($confdir."dbconf.php");

$color1="#FFE8B2";
$color2="#E2E2E2";

$result2="";
$alansayisi=count($alan);
$alansayisi2=count($linkyazi);
$satirno=0;

$query="select * from $tablo";
if($filtre<>""){$query.=" where $filtre";};
if($sirala<>""){$query.=" order by $sirala";};
$result = mysql_db_query("$dbadi", $query);

if ($result)
        {
        $result2.= "\n<table border=0> \n<tr border=1>";

        for ($i=0;$i<$alansayisi;$i++)
                {
                if($baslik[$i]<>"") {$yaz=$baslik[$i];} else {$yaz=$alan[$i];}; $result2.="<td>$yaz</td>";
                };

        for ($i=0;$i<$alansayisi2;$i++)$result2.="<td>$linkyazi[$i]</td>";
        $result2.="</tr>\n ";

        while ($r = mysql_fetch_array($result))
                {
                if(iseven($satirno)){$satirrengi=$color1;} else {$satirrengi=$color2;};$satirno++;
                $result2.="<tr bgcolor='$satirrengi'>";
                for ($i=0;$i<$alansayisi;$i++)
                        {
                         $al=$alan[$i];
                        $yaz=$r[$al];
                        if($yaz==""){$result2.="<td>&nbsp</td>";}
                                        else {
                                        if(is_numeric($yaz)){$yaz="<p align=right>".number_format($yaz,2)."</p>";};
                                        $result2.="<td>$yaz</td>";

                                        };

                        //if($al==$linkalan){$link=$r[$al];};
                        };
                        $link=$r[$linkalan];
                for ($i=0;$i<$alansayisi2;$i++)
                        {

                        $ly=$linkyazi[$i];
                        $ld=$linkdosya[$i];
                        $char="?";
                        if(strpos($ld,"?"))$char="";
                        $result2.="<td><a href='$ld$char$linkalan=$link' target='_blank'>$ly</a></td>";
                        }

                $result2.= "</tr>\n";
                }
   		$kayitsayisi=kayitsayisi($tablo,$filtre);
        //$query="select count(*) as count from $tablo";
        //if($filtre<>""){$query.=" where $filtre";};
        //$res=dbresult($query,array("count"));
        //$result2.= "<tr><td>Kayt Says: $res[0] </td></tr>";
        $result2.= "</table>";
        $result2.= "Kayt Says: $kayitsayisi <br>";
        mysql_free_result($result);
        }
        else
        {
        echo "Bir hata olustu:<br>sql:$query<br>";echo mysql_error();
        };
return $result2;
};//fonksiyon

function tablolistele3_4($tablo,$baslik,$alan,$filtre,$sirala,$linkyazi,$linkdosya,$linkalan) {
	echo tablolistele3_4_2($tablo,$baslik,$alan,$filtre,$sirala,$linkyazi,$linkdosya,$linkalan);
}


function tablolistele3_4_2($tablo,$baslik,$alan,$filtre,$sirala,$linkyazi,$linkdosya,$linkalan)
{
// tablolistele3 den farki, tablo basliklarinin olmasidir.
// 3_2 den farki, islemler icin yazi degil resim olmasi.

GLOBAL $confdir;
include($confdir."dbconf.php");

$color1="#FFE8B2";
$color2="#E2E2E2";

$result2="";
$alansayisi=count($alan);
$alansayisi2=count($linkyazi);
$satirno=0;

$query="select * from $tablo";
if($filtre<>""){$query.=" where $filtre";};
if($sirala<>""){$query.=" order by $sirala";};
$result = mysql_db_query("$dbadi", $query);

if ($result)
        {
        $result2.= "\n<table class='vidinlistyle' border=0> \n<tr border=1>";
        // once basliklari yaz.
        if (count($baslik)>0)
        {
        for ($i=0;$i<$alansayisi;$i++)
                {
                if($baslik[$i]<>"") {$yaz=$baslik[$i];} else {$yaz=$alan[$i];}; $result2.="<td>$yaz</td>";
                };
         }
         else
         {
        for ($i=0;$i<$alansayisi;$i++)
                {
                 $yaz=$alan[$i]; $result2.="<td>$yaz</td>";
                };
         }
//        for ($i=0;$i<$alansayisi2;$i++)$result2.="<td>$linkyazi[$i]</td>";
        $result2.="</tr>\n ";

        while ($r = mysql_fetch_array($result))
                {
                if(iseven($satirno)){$satirrengi=$color1;} else {$satirrengi=$color2;};$satirno++;
                $result2.="<tr bgcolor='$satirrengi'>";
                for ($i=0;$i<$alansayisi;$i++)
                        {
                         $al=$alan[$i];
                        $yaz=$r[$al];
                        if($yaz==""){$result2.="<td>&nbsp</td>";}
                                        else {
                                        if(is_numeric($yaz)){$yaz="<p align=right>".number_format($yaz,2,",",".")."</p>";};
                                        $result2.="<td>$yaz</td>";

                                        };

                        //if($al==$linkalan){$link=$r[$al];};
                        };
                        $link=$r[$linkalan];
                for ($i=0;$i<$alansayisi2;$i++)
                        {
                        $ly=$linkyazi[$i];
                        $ld=$linkdosya[$i];
                        $char="?";
                        if(strpos($ld,"?"))$char="";
                        $result2.="<td><a href='$ld$char$linkalan=$link' target='_blank'><img src='$ly' border='0'></a></td>";
                        }

                $result2.= "</tr>\n";
                }
        $query="select count(*) as count from $tablo";
        if($filtre<>""){$query.=" where $filtre";};
        $res=dbresult($query,array("count"));
        //$result2.= "<tr><td>Kayt Says: $res[0] </td></tr>";
        $result2.= "</table>";
        $result2.= "Kayt Says: $res[0] <br>";
        mysql_free_result($result);
        }
        else
        {
        echo "Bir hata olustu:<br>sql:$query<br>";echo mysql_error();
        };
//echo $result2;
//echo "query:$query";
return $result2;
};//fonksiyon

function tablolistele3_5($tablo,$baslik,$alan,$filtre,$sirala,$linkyazi,$linkdosya,$linkalan,$baslangic,$satirsayisi)
{
// tablolistele3_4 den fark bilgilerin belli gruplarda listelenmesi. ileri geri tular v.b.

GLOBAL $confdir;
include($confdir."dbconf.php");

$color1="#FFE8B2";
$color2="#E2E2E2";

$result2="";
$alansayisi=count($alan);
$alansayisi2=count($linkyazi);
$satirno=0;

if(!isset($baslangic))$baslangic=0;
if(!isset($satirsayisi))$satirsayisi=10;

$query="select * from $tablo";
if($filtre<>""){$query.=" where $filtre";};
if($sirala<>""){$query.=" order by $sirala";};
if($satirsayisi>0)$query.=" limit $baslangic, $satirsayisi";
$result = mysql_db_query("$dbadi", $query);

if ($result)
        {
        $result2.= "\n<table class='vidinlistyle' border=0> \n<tr border=1>";
        // once basliklari yaz.
        if (count($baslik)>0)
        {
        for ($i=0;$i<$alansayisi;$i++)
                {
                    if(is_array($baslik[$i])or(is_array($alan[$i]))){
                        if($baslik[$i]<>"") {$yaz=$baslik[$i][0];} else {$yaz=$alan[$i][0];}; $result2.="<td>$yaz</td>";
                    }
                    else {
                        if($baslik[$i]<>"") {$yaz=$baslik[$i];} else {$yaz=$alan[$i];}; $result2.="<td>$yaz</td>";
                    }
                };
         }
         else
         {
        for ($i=0;$i<$alansayisi;$i++)
                {
                	if(is_array($alan[$i])) {
                    	$yaz=$alan[$i][0]; $result2.="<td>$yaz</td>";
                    }
                    else {
                    	$yaz=$alan[$i]; $result2.="<td>$yaz</td>";
                    }
                };
         }
//        for ($i=0;$i<$alansayisi2;$i++)$result2.="<td>$linkyazi[$i]</td>";
        $result2.="</tr>\n ";

        while ($r = mysql_fetch_array($result))
                {
                if(iseven($satirno)){$satirrengi=$color1;} else {$satirrengi=$color2;};$satirno++;
                $result2.="<tr bgcolor='$satirrengi'>";
                for ($i=0;$i<$alansayisi;$i++)
                        {
                        if (is_array($alan[$i])) {
                        	$al=$alan[$i][0];
                            $yaz=$r[$al];
                            if($alan[$i]["link"]=="1"){$yaz="<a target='".$alan[$i]["target"]."' href=$yaz>$yaz</a>";}; // eger link ise, yazilani hrefe koy.

                        }
                        else {
                        $al=$alan[$i];
                        $yaz=$r[$al];
                        }

                        if($yaz==""){$result2.="<td>&nbsp</td>";}
                                        else {
                                        if(is_numeric($yaz)){$yaz="<p align=right>".number_format($yaz,2)."</p>";};
                                        $result2.="<td>$yaz</td>";

                                        };

                        //if($al==$linkalan){$link=$r[$al];};
                        };
                        $link=$r[$linkalan];
                for ($i=0;$i<$alansayisi2;$i++)
                        {
                        $ly=$linkyazi[$i];
                        $ld=$linkdosya[$i];
                        $char="?";
                        if(strpos($ld,"?"))$char="";
                        $result2.="<td><a href='$ld$char$linkalan=$link' target='_blank'><img src='$ly' border='0'></a></td>";
                        }

                $result2.= "</tr>\n";
                }

        //$query="select count(*) as count from $tablo";
        //if($filtre<>""){$query.=" where $filtre";};
        //$res=dbresult($query,array("count"));
        //$result2.= "<tr><td>Kayt Says: $res[0] </td></tr>";
        $result2.= "</table>";

		$kayitsayisi=kayitsayisi($tablo,$filtre);//$res[0];


        $ilerimiktar=$baslangic+$satirsayisi;
        if($satirsayisi>0)
        {
                $sondanoncesi=$kayitsayisi-$satirsayisi;
        $querystring=$_SERVER['QUERY_STRING'];
        $querystring=str_replace("baslangic=$baslangic&satirsayisi=$satirsayisi","",$querystring);
                // asagidaki tabloya bu baslangic tekrar gitmesin diye. asagida zaten ekleniyor.
        if($querystring<>"")$querystring.="&"; // bialtsatrda ?den sonra yazmak i�n. ileri geride kullanlyor.
        if($ilerimiktar<$kayitsayisi)
                {
                $ileri="<a href=".$_SERVER['PHP_SELF']."?".$querystring."baslangic=$ilerimiktar&satirsayisi=$satirsayisi>&gt</a>";
                $son="<a href=".$_SERVER['PHP_SELF']."?".$querystring."baslangic=$sondanoncesi&satirsayisi=$satirsayisi>&gt&gt</a>";
                }
                else
                {
                $ileri="&gt";
                $son="&gt&gt";
                };

        if($baslangic>0)
                {
                $geri="<a href=".$_SERVER['PHP_SELF']."?".$querystring."baslangic=".($baslangic-$satirsayisi)."&satirsayisi=$satirsayisi>&lt</a>";
                $bas="<a href=".$_SERVER['PHP_SELF']."?".$querystring."baslangic=0&satirsayisi=$satirsayisi>&lt&lt</a>";
                }
                else
                {
                $geri="&lt";
                $bas="&lt&lt";
                };

        if($kayitsayisi>$satirsayisi)$result2.= "$baslangic .. ".($baslangic+$satirsayisi)."  $bas &nbsp  $geri $ileri &nbsp $son <br>";


        };
        $result2.="<br>Kayt Says: $res[0] <br>";
        mysql_free_result($result);
        }
        else
        {
        echo "Bir hata olustu:<br>sql:$query<br>";echo mysql_error();
        };
echo $result2;
//echo "query:$query";
return $result2;
};//fonksiyon

function tablolistele3_5_2($tablo,$baslik,$alan,$filtre,$sirala,$linkyazi,$linkdosya,$linkalan,$baslangic,$satirsayisi)
{
// tablolistele3_4 den fark bilgilerin belli gruplarda listelenmesi. ileri geri tular v.b.
// bir farki da echo yapmaz
GLOBAL $confdir;
include($confdir."dbconf.php");

$color1="#FFE8B2";
$color2="#E2E2E2";

if(!isset($baslangic))$baslangic=0;
if(!isset($satirsayisi))$satirsayisi=10;

$kayitsayisi=kayitsayisi($tablo,$filtre);//$res[0];
if($kayitsayisi==0){
	return "Kayt Bulunamad/Yok.";
}


$result2="";
$alansayisi=count($alan);
$alansayisi2=count($linkyazi);
$satirno=0;

$query="select * from $tablo";
if($filtre<>""){$query.=" where $filtre";};
if($sirala<>""){$query.=" order by $sirala";};
if($satirsayisi>0)$query.=" limit $baslangic, $satirsayisi";
$result = mysql_db_query("$dbadi", $query);

if ($result)
        {
        $result2.= "\n<table class='vidinlistyle' border=0> \n<tr border=1>";
        // once basliklari yaz.
        if (count($baslik)>0)
        {
        for ($i=0;$i<$alansayisi;$i++)
                {
                if($baslik[$i]<>"") {$yaz=$baslik[$i];} else {$yaz=$alan[$i];}; $result2.="<td>$yaz</td>";
                };
         }
         else
         {
        for ($i=0;$i<$alansayisi;$i++)
                {
                 $yaz=$alan[$i]; $result2.="<td>$yaz</td>";
                };
         }
//        for ($i=0;$i<$alansayisi2;$i++)$result2.="<td>$linkyazi[$i]</td>";
        $result2.="</tr>\n ";

        while ($r = mysql_fetch_array($result))
                {
                if(iseven($satirno)){$satirrengi=$color1;} else {$satirrengi=$color2;};$satirno++;
                $result2.="<tr bgcolor='$satirrengi'>";
                for ($i=0;$i<$alansayisi;$i++)
                        {
                         $al=$alan[$i];
                        $yaz=$r[$al];
                        if($yaz==""){$result2.="<td>&nbsp</td>";}
                                        else {
                                        if(is_numeric($yaz)){$yaz="<p align=right>".number_format($yaz,2)."</p>";};
                                        $result2.="<td>$yaz</td>";

                                        };

                        //if($al==$linkalan){$link=$r[$al];};
                        };
                        $link=$r[$linkalan];
                for ($i=0;$i<$alansayisi2;$i++)
                        {
                        $ly=$linkyazi[$i];
                        $ld=$linkdosya[$i];
                        $char="&";
                        if(strpos($ld,"?")===false)$char="?";
                        $result2.="<td><a href='$ld$char$linkalan=$link'><img src='$ly' border='0'></a></td>";
                        }

                $result2.= "</tr>\n";
                }
        //$query="select count(*) as count from $tablo";
        //if($filtre<>""){$query.=" where $filtre";};
        //$res=dbresult($query,array("count"));

        //$result2.= "<tr><td>Kayt Says: $res[0] </td></tr>";
        $result2.= "</table>";
        $ilerimiktar=$baslangic+$satirsayisi;
        if($satirsayisi>0)
        {
                $sondanoncesi=$kayitsayisi-$satirsayisi;
        $querystring=$_SERVER['QUERY_STRING'];
        $querystring=str_replace("baslangic=$baslangic&satirsayisi=$satirsayisi","",$querystring);
                // asagidaki tabloya bu baslangic tekrar gitmesin diye. asagida zaten ekleniyor.
        if($querystring<>"")$querystring.="&"; // bialtsatrda ?den sonra yazmak i�n. ileri geride kullanlyor.
        if($ilerimiktar<$kayitsayisi)
                {
                $ileri="<a href=".$_SERVER['PHP_SELF']."?".$querystring."baslangic=$ilerimiktar&satirsayisi=$satirsayisi>&gt</a>";
                $son="<a href=".$_SERVER['PHP_SELF']."?".$querystring."baslangic=$sondanoncesi&satirsayisi=$satirsayisi>&gt&gt</a>";
                }
                else
                {
                $ileri="&gt";
                $son="&gt&gt";
                };

        if($baslangic>0)
                {
                $geri="<a href=".$_SERVER['PHP_SELF']."?".$querystring."baslangic=".($baslangic-$satirsayisi)."&satirsayisi=$satirsayisi>&lt</a>";
                $bas="<a href=".$_SERVER['PHP_SELF']."?".$querystring."baslangic=0&satirsayisi=$satirsayisi>&lt&lt</a>";
                }
                else
                {
                $geri="&lt";
                $bas="&lt&lt";
                };

        if($kayitsayisi>$satirsayisi)$result2.= "$baslangic .. ".($baslangic+$satirsayisi)."  $bas &nbsp  $geri $ileri &nbsp $son <br>";
        };
        $result2.="Kayt Says: $kayitsayisi <br>";
        mysql_free_result($result);
        }
        else
        {
        echo "Bir hata olustu:<br>sql:$query<br>";echo mysql_error();
        };
//echo $result2;
//echo "query:$query";
return $result2;
};//fonksiyon



function tablolistele3_5_3($tablo,$query,$baslik,$alan,$filtre,$sirala,$linkyazi,$linkdosya,$linkalan,$baslangic,$satirsayisi)
{
// tablolistele3_4 den fark bilgilerin belli gruplarda listelenmesi. ileri geri tular v.b.
// 3_5_2 den fark, verilen query uzerinden calisir. kendisi query olusturmaz. serbest queryler ile tablo yazmak icin.
// bir farki da echo yapmaz
GLOBAL $confdir;
include($confdir."dbconf.php");

$color1="#FFE8B2";
$color2="#E2E2E2";

if(!isset($baslangic))$baslangic=0;
if(!isset($satirsayisi))$satirsayisi=10;

$kayitsayisi=kayitsayisi($tablo,$filtre);//$res[0];
if($kayitsayisi==0){
	return "Kayt Bulunamad/Yok.";
}


$result2="";
$alansayisi=count($alan);
$alansayisi2=count($linkyazi);
$satirno=0;

$result = mysql_db_query("$dbadi", $query);
$result2.="Query:".$query." <br>".mysql_error();

if ($result)
        {
        $result2.= "\n<table class='vidinlistyle' border=0> \n<tr border=1>";
        // once basliklari yaz.
        if (count($baslik)>0)
        {
        for ($i=0;$i<$alansayisi;$i++)
                {
                if($baslik[$i]<>"") {$yaz=$baslik[$i];} else {$yaz=$alan[$i];}; $result2.="<td>$yaz</td>";
                };
         }
         else
         {
        for ($i=0;$i<$alansayisi;$i++)
                {
                 $yaz=$alan[$i]; $result2.="<td>$yaz</td>";
                };
         }
//        for ($i=0;$i<$alansayisi2;$i++)$result2.="<td>$linkyazi[$i]</td>";
        $result2.="</tr>\n ";

        while ($r = mysql_fetch_array($result))
                {
                if(iseven($satirno)){$satirrengi=$color1;} else {$satirrengi=$color2;};$satirno++;
                $result2.="<tr bgcolor='$satirrengi'>";
                for ($i=0;$i<$alansayisi;$i++)
                        {
                         $al=$alan[$i];
                        $yaz=$r[$al];
                        if($yaz==""){$result2.="<td>&nbsp</td>";}
                                        else {
                                        if(is_numeric($yaz)){$yaz="<p align=right>".number_format($yaz,2)."</p>";};
                                        $result2.="<td>$yaz</td>";

                                        };

                        //if($al==$linkalan){$link=$r[$al];};
                        };
                        $link=$r[$linkalan];
                for ($i=0;$i<$alansayisi2;$i++)
                        {
                        $ly=$linkyazi[$i];
                        $ld=$linkdosya[$i];
                        $char="&";
                        if(strpos($ld,"?")===false)$char="?";
                        $result2.="<td><a href='$ld$char$linkalan=$link'><img src='$ly' border='0'></a></td>";
                        }

                $result2.= "</tr>\n";
                }
        //$query="select count(*) as count from $tablo";
        //if($filtre<>""){$query.=" where $filtre";};
        //$res=dbresult($query,array("count"));

        //$result2.= "<tr><td>Kayt Says: $res[0] </td></tr>";
        $result2.= "</table>";
        $ilerimiktar=$baslangic+$satirsayisi;
        if($satirsayisi>0)
        {
                $sondanoncesi=$kayitsayisi-$satirsayisi;
        $querystring=$_SERVER['QUERY_STRING'];
        $querystring=str_replace("baslangic=$baslangic&satirsayisi=$satirsayisi","",$querystring);
                // asagidaki tabloya bu baslangic tekrar gitmesin diye. asagida zaten ekleniyor.
        if($querystring<>"")$querystring.="&"; // bialtsatrda ?den sonra yazmak i�n. ileri geride kullanlyor.
        if($ilerimiktar<$kayitsayisi)
                {
                $ileri="<a href=".$_SERVER['PHP_SELF']."?".$querystring."baslangic=$ilerimiktar&satirsayisi=$satirsayisi>&gt</a>";
                $son="<a href=".$_SERVER['PHP_SELF']."?".$querystring."baslangic=$sondanoncesi&satirsayisi=$satirsayisi>&gt&gt</a>";
                }
                else
                {
                $ileri="&gt";
                $son="&gt&gt";
                };

        if($baslangic>0)
                {
                $geri="<a href=".$_SERVER['PHP_SELF']."?".$querystring."baslangic=".($baslangic-$satirsayisi)."&satirsayisi=$satirsayisi>&lt</a>";
                $bas="<a href=".$_SERVER['PHP_SELF']."?".$querystring."baslangic=0&satirsayisi=$satirsayisi>&lt&lt</a>";
                }
                else
                {
                $geri="&lt";
                $bas="&lt&lt";
                };

        if($kayitsayisi>$satirsayisi)$result2.= "$baslangic .. ".($baslangic+$satirsayisi)."  $bas &nbsp  $geri $ileri &nbsp $son <br>";
        };
        $result2.="Kayt Says: $kayitsayisi <br>";
        mysql_free_result($result);
        }
        else
        {
        echo "Bir hata olustu:<br>sql:$query<br>";echo mysql_error();
        };
//echo $result2;
//echo "query:$query";
return $result2;
};//fonksiyon



function tablolistele3_6($tablo,$baslik,$alan,$aciklamaalan,$resimalan,$filtre,$sirala,$linkyazi,$linkdosya,$linkalan,$baslangic,$satirsayisi)
{
// tablolistele3_4 den fark bilgilerin belli gruplarda listelenmesi. ileri geri tular v.b.
// 3_5 den farki, ayrica her satir icin resim gosterebilmesi, yani urun resmi v.b.
// henuz yapilmadi.

GLOBAL $confdir;
include($confdir."dbconf.php");

$color1="#FFE8B2";
$color2="#E2E2E2";

$result2="";
$alansayisi=count($alan);
$alansayisi2=count($linkyazi);
$satirno=0;

$query="select * from $tablo";
if($filtre<>""){$query.=" where $filtre";};
if($sirala<>""){$query.=" order by $sirala";};
if($satirsayisi>0)$query.=" limit $baslangic, $satirsayisi";
$result = mysql_db_query("$dbadi", $query);

if ($result)
        {
        $result2.= "\n<table class='vidinlistyle' border=0> \n<tr border=1>";
        // once basliklari yaz.
        if (count($baslik)>0)
        {
        for ($i=0;$i<$alansayisi;$i++)
                {
                if($baslik[$i]<>"") {$yaz=$baslik[$i];} else {$yaz=$alan[$i];}; $result2.="<td>$yaz</td>";
                };
         }
         else
         {
        for ($i=0;$i<$alansayisi;$i++)
                {
                 $yaz=$alan[$i]; $result2.="<td>$yaz</td>";
                };
         }
//        for ($i=0;$i<$alansayisi2;$i++)$result2.="<td>$linkyazi[$i]</td>";
        $result2.="</tr>\n ";

        while ($r = mysql_fetch_array($result))
                {
                if(iseven($satirno)){$satirrengi=$color1;} else {$satirrengi=$color2;};$satirno++;
                $result2.="<tr bgcolor='$satirrengi'>";
                for ($i=0;$i<$alansayisi;$i++)
                        {
                         $al=$alan[$i];
                        $yaz=$r[$al];
                        if($yaz==""){$result2.="<td>&nbsp</td>";}
                                        else {
                                        if(is_numeric($yaz)){$yaz="<p align=right>".number_format($yaz,2)."</p>";};
                                        $result2.="<td>$yaz</td>";

                                        };

                        //if($al==$linkalan){$link=$r[$al];};
                        };
                        $link=$r[$linkalan];
                for ($i=0;$i<$alansayisi2;$i++)
                        {
                        $ly=$linkyazi[$i];
                        $ld=$linkdosya[$i];
                        $char="?";
                        if(strpos($ld,"?"))$char="";
                        $result2.="<td><a href='$ld$char$linkalan=$link' target='_blank'><img src='$ly' border='0'></a></td>";
                        }
                $result2.="</tr>\n";
                $resim=$r[$resimalan];
                if($resim=="")$resim="Resmi Yok";
                else $resim="<img src=kasa/resimler/$resim>";
                $result2.="<tr><td>$r[$aciklamaalan]</td><td>$resim</td></tr>\n";
                }
        //$query="select count(*) as count from $tablo";
        //if($filtre<>""){$query.=" where $filtre";};
        //$res=dbresult($query,array("count"));
        $kayitsayisi=kayitsayisi($tablo,$filtre);//$res[0];

        //$result2.= "<tr><td>Kayt Says: $res[0] </td></tr>";
        $result2.= "</table>";
        $ilerimiktar=$baslangic+$satirsayisi;
        if($satirsayisi>0)
        {
        $querystring=$_SERVER['QUERY_STRING'];
        //        echo "<br>fonk:querystring2:$querystring2";
        $querystring=str_replace("baslangic=$baslangic&satirsayisi=$satirsayisi","",$querystring); // aadaki tabloya bu baslangic tekrar gitmesin diye. aada zaten ekleniyor.
        //echo "<br>fonk:querystring2:$querystring<br>";
        if($querystring<>"")$querystring.="&"; // bialtsatrda ?den sonra yazmak in. ileri geride kullanlyor.
        if($ilerimiktar<$kayitsayisi) $ileri="<a href=".$_SERVER['PHP_SELF']."?".$querystring."baslangic=$ilerimiktar&satirsayisi=$satirsayisi>&gt</a>";
        if($baslangic>0) $geri="<a href=".$_SERVER['PHP_SELF']."?".$querystring."baslangic=".($baslangic-$satirsayisi)."&satirsayisi=$satirsayisi>&lt</a>";
        if($kayitsayisi>$satirsayisi)$result2.= "$baslangic .. ".($baslangic+$satirsayisi)."  $geri &nbsp $ileri  ";
        };
        $result2.="<br>Kayt Says: $res[0] <br>";
        mysql_free_result($result);
        }
        else
        {
        echo "Bir hata olustu:<br>sql:$query<br>";echo mysql_error();
        };
echo $result2;
//echo "query:$query";
return $result2;
};//fonksiyon

function tablolistele3_7($tablo,$baslik,$alan,$aciklamaalan,$resimalan,$filtre,$sirala,$linkyazi,$linkdosya,$linkalan,$baslangic,$satirsayisi)
{
// tablolistele3_4 den fark bilgilerin belli gruplarda listelenmesi. ileri geri tular v.b.
// 3_5 den farki, ayrica her satir icin resim gosterebilmesi, yani urun resmi v.b.
// 3_6 dan farki, rakamlar icin kurus hanesinin yazilmasi

$result2=tablolistele3_7_2($tablo,$baslik,$alan,$aciklamaalan,$resimalan,"kasa/resimler/",$filtre,$sirala,$linkyazi,$linkdosya,$linkalan,$baslangic,$satirsayisi);
echo $result2;
return $result2;
};//fonksiyon


function tablolistele3_7_2($tablo,$baslik,$alan,$aciklamaalan,$resimalan,$resimpath,$filtre,$sirala,$linkyazi,$linkdosya,$linkalan,$baslangic,$satirsayisi)
{
// tablolistele3_4 den fark bilgilerin belli gruplarda listelenmesi. ileri geri tular v.b.
// 3_5 den farki, ayrica her satir icin resim gosterebilmesi, yani urun resmi v.b.
// 3_6 dan farki, rakamlar icin kurus hanesinin yazilmasi
// bir de echo yapmaz

GLOBAL $confdir;
include($confdir."dbconf.php");

$color1="#FFE8B2";
$color2="#E2E2E2";

if(!isset($baslangic))$baslangic=0;
if((!isset($satirsayisi))or($satirsayisi==0))$satirsayisi=10;

$result2="";
$alansayisi=count($alan);
$alansayisi2=count($linkyazi);
$satirno=0;

$kayitsayisi=kayitsayisi($tablo,$filtre);//$res[0];
if($kayitsayisi==0){
	return "Kayt Bulunamad/Yok.<filtre=$filtre>";
}
$query="select * from $tablo";
if($filtre<>""){$query.=" where $filtre";};
if($sirala<>""){$query.=" order by $sirala";};
if($satirsayisi>0)$query.=" limit $baslangic, $satirsayisi";
$result = mysql_db_query("$dbadi", $query);

$result2.="<query:$query>";

if ($result)

        {
        $result2.= "\n<table class='vidinlistyle' border=0> \n<tr border=1>";
        // once basliklari yaz.
        if (count($baslik)>0)
        {
        for ($i=0;$i<$alansayisi;$i++)
                {
                if($baslik[$i]<>"") {$yaz=$baslik[$i];} else {$yaz=$alan[$i];}; $result2.="<td>$yaz</td>";
                };
         }
         else
         {
        for ($i=0;$i<$alansayisi;$i++)
                {
                 $yaz=$alan[$i]; $result2.="<td>$yaz</td>";
                };
         }
//        for ($i=0;$i<$alansayisi2;$i++)$result2.="<td>$linkyazi[$i]</td>";
        $result2.="</tr>\n ";

        while ($r = mysql_fetch_array($result))
                {
                $satirrengi=(iseven($satirno++))?$color1:$color2;

                $result2.="<tr bgcolor='$satirrengi'>";
                for ($i=0;$i<$alansayisi;$i++)
                        {
                         $al=$alan[$i];
                        $yaz=$r[$al];
                        if($yaz==""){$result2.="<td>&nbsp</td>";}
                                        else {
                                        if(is_numeric($yaz)){$yaz="<p align=right>".number_format($yaz,2)."</p>";};
                                        $result2.="<td>$yaz</td>";
                                        };
                        //if($al==$linkalan){$link=$r[$al];};
                        };
                        $link=$r[$linkalan];
                $linkler=array();
                for ($i=0;$i<$alansayisi2;$i++)
                        {
                        $ly=$linkyazi[$i];
                        $ld=$linkdosya[$i];
                        $char="&";
                        if(strpos($ld,"?")===false)$char="?";
                        $result2.="<td><a href='$ld$char$linkalan=$link'><img src='$ly' border='0'></a></td>";
                        $linkler[]="$ld$char$linkalan=$link";
                        }
                $result2.="</tr>\n";
                $resim=$r[$resimalan];
                if($resim=="")$resim="Resmi Yok";
                else $resim="<a href='$linkler[0]'><img src='$resimpath$resim' border=0></a>";
                $result2.="<tr><td>$r[$aciklamaalan]</td><td>$resim</td></tr>\n";
                }
        //$query="select count(*) as count from $tablo";
        //if($filtre<>""){$query.=" where $filtre";};
        //$res=dbresult($query,array("count"));

        $result2.= "</table>";
        $ilerimiktar=$baslangic+$satirsayisi;

        if($satirsayisi>0)
        {
        $sondanoncesi=$kayitsayisi-$satirsayisi;
        $querystring=$_SERVER['QUERY_STRING'];
        $querystring=str_replace("&baslangic=$baslangic&satirsayisi=$satirsayisi","",$querystring); //eger basinda da & isareti varsa kaldir.
        $querystring=str_replace("baslangic=$baslangic&satirsayisi=$satirsayisi","",$querystring);
                // asagidaki tabloya bu baslangic tekrar gitmesin diye. asagida zaten ekleniyor...
        if($querystring<>"")$querystring.="&"; // bialtsatrda ?den sonra yazmak in. ileri geride kullanlyor.
        if($ilerimiktar<$kayitsayisi)
                {
                $ileri="<a href=".$_SERVER['PHP_SELF']."?".$querystring."baslangic=$ilerimiktar&satirsayisi=$satirsayisi>&gt
</a>";
                $son="<a href=".$_SERVER['PHP_SELF']."?".$querystring."baslangic=$sondanoncesi&satirsayisi=$satirsayisi>&gt&gt</a>";
                }
                else
                {
                $ileri="&gt";
                $son="&gt&gt";
                };

        if($baslangic>0)
                {
                $geri="<a href=".$_SERVER['PHP_SELF']."?".$querystring."baslangic=".($baslangic-$satirsayisi)."&satirsayisi=$satirsayisi>&lt</a>";
                $bas="<a href=".$_SERVER['PHP_SELF']."?".$querystring."baslangic=0&satirsayisi=$satirsayisi>&lt&lt</a>";
                }
                else
                {
                $geri="&lt";
                $bas="&lt&lt";
                };

        if($kayitsayisi>$satirsayisi)$result2.= "$baslangic .. ".($baslangic+$satirsayisi)."  $bas &nbsp  $geri $ileri &nbsp $son <br>";
        };


        $result2.="Kayt Says: $kayitsayisi <br>";
        mysql_free_result($result);
        }
        else
        {
        echo "Bir hata olustu:<br>sql:$query<br>";echo mysql_error();
        };
return $result2;
};


function tablolistele3_7_3($tablo,$baslik,$alan,$aciklamaalan,$resimalan,$resimpath,$filtre,$sirala,$linkyazi,$linkdosya,$linkalan,$baslangic,$satirsayisi,$tabloextra)
{
// tablolistele3_4 den fark bilgilerin belli gruplarda listelenmesi. ileri geri tular v.b.
// 3_5 den farki, ayrica her satir icin resim gosterebilmesi, yani urun resmi v.b.
// 3_6 dan farki, rakamlar icin kurus hanesinin yazilmasi
// bir de echo yapmaz
// 3_7_2 denfark da, sonda tabloextra var, tabloya background v.b. vermek i�n.

GLOBAL $confdir;
include($confdir."dbconf.php");

$color1="#FFE8B2";
$color2="#E2E2E2";

if(!isset($baslangic))$baslangic=0;
if((!isset($satirsayisi))or($satirsayisi==0))$satirsayisi=10;

$result2="";
$alansayisi=count($alan);
$alansayisi2=count($linkyazi);
$satirno=0;

$kayitsayisi=kayitsayisi($tablo,$filtre);//$res[0];
if($kayitsayisi==0){
	return "Kayt Bulunamad/Yok.<filtre=$filtre>";
}
$query="select * from $tablo";
if($filtre<>""){$query.=" where $filtre";};
if($sirala<>""){$query.=" order by $sirala";};
if($satirsayisi>0)$query.=" limit $baslangic, $satirsayisi";
$result = mysql_db_query("$dbadi", $query);

$result2.="<query:$query>";

if ($result)

        {
        $result2.= "\n<table class='vidinlistyle' border=0 $tabloextra> \n<tr border=1>";
        // once basliklari yaz.
        if (count($baslik)>0)
        {
        for ($i=0;$i<$alansayisi;$i++)
                {
                if($baslik[$i]<>"") {$yaz=$baslik[$i];} else {$yaz=$alan[$i];}; $result2.="<td>$yaz</td>";
                };
         }
         else
         {
        for ($i=0;$i<$alansayisi;$i++)
                {
                 $yaz=$alan[$i]; $result2.="<td>$yaz</td>";
                };
         }
//        for ($i=0;$i<$alansayisi2;$i++)$result2.="<td>$linkyazi[$i]</td>";
        $result2.="</tr>\n ";

        while ($r = mysql_fetch_array($result))
                {
                $satirrengi=(iseven($satirno++))?$color1:$color2;

                $result2.="<tr bgcolor='$satirrengi'>";
                for ($i=0;$i<$alansayisi;$i++)
                        {
                         $al=$alan[$i];
                        $yaz=$r[$al];
                        if($yaz==""){$result2.="<td>&nbsp</td>";}
                                        else {
                                        if(is_numeric($yaz)){$yaz="<p align=right>".number_format($yaz,2)."</p>";};
                                        $result2.="<td>$yaz</td>";
                                        };
                        //if($al==$linkalan){$link=$r[$al];};
                        };
                        $link=$r[$linkalan];
                $linkler=array();
                for ($i=0;$i<$alansayisi2;$i++)
                        {
                        $ly=$linkyazi[$i];
                        $ld=$linkdosya[$i];
                        $char="&";
                        if(strpos($ld,"?")===false)$char="?";
                        $result2.="<td><a href='$ld$char$linkalan=$link'><img src='$ly' border='0'></a></td>";
                        $linkler[]="$ld$char$linkalan=$link";
                        }
                $result2.="</tr>\n";
                $resim=$r[$resimalan];
                if($resim=="")$resim="Resmi Yok";
                else $resim="<a href='$linkler[0]'><img src='$resimpath$resim' border=0></a>";
                $result2.="<tr><td>$r[$aciklamaalan]</td><td>$resim</td></tr>\n";
                }
        //$query="select count(*) as count from $tablo";
        //if($filtre<>""){$query.=" where $filtre";};
        //$res=dbresult($query,array("count"));

        $result2.= "</table>";
        $ilerimiktar=$baslangic+$satirsayisi;

        if($satirsayisi>0)
        {
        $sondanoncesi=$kayitsayisi-$satirsayisi;
        $querystring=$_SERVER['QUERY_STRING'];
        $querystring=str_replace("&baslangic=$baslangic&satirsayisi=$satirsayisi","",$querystring); //eger basinda da & isareti varsa kaldir.
        $querystring=str_replace("baslangic=$baslangic&satirsayisi=$satirsayisi","",$querystring);
                // asagidaki tabloya bu baslangic tekrar gitmesin diye. asagida zaten ekleniyor...
        if($querystring<>"")$querystring.="&"; // bialtsatrda ?den sonra yazmak in. ileri geride kullanlyor.
        if($ilerimiktar<$kayitsayisi)
                {
                $ileri="<a href=".$_SERVER['PHP_SELF']."?".$querystring."baslangic=$ilerimiktar&satirsayisi=$satirsayisi>&gt
</a>";
                $son="<a href=".$_SERVER['PHP_SELF']."?".$querystring."baslangic=$sondanoncesi&satirsayisi=$satirsayisi>&gt&gt</a>";
                }
                else
                {
                $ileri="&gt";
                $son="&gt&gt";
                };

        if($baslangic>0)
                {
                $geri="<a href=".$_SERVER['PHP_SELF']."?".$querystring."baslangic=".($baslangic-$satirsayisi)."&satirsayisi=$satirsayisi>&lt</a>";
                $bas="<a href=".$_SERVER['PHP_SELF']."?".$querystring."baslangic=0&satirsayisi=$satirsayisi>&lt&lt</a>";
                }
                else
                {
                $geri="&lt";
                $bas="&lt&lt";
                };

        if($kayitsayisi>$satirsayisi)$result2.= "$baslangic .. ".($baslangic+$satirsayisi)."  $bas &nbsp  $geri $ileri &nbsp $son <br>";
        };


        $result2.="Kayt Says: $kayitsayisi <br>";
        mysql_free_result($result);
        }
        else
        {
        echo "Bir hata olustu:<br>sql:$query<br>";echo mysql_error();
        };
return $result2;
};



function tablolistele4($tablo,$alan,$filtre,$sirala,$linkyazi,$linkdosya,$linkalan)
{
// silmeli tablo
GLOBAL $confdir;
include($confdir."dbconf.php");
$result2="";
$alansayisi=count($alan);
$alansayisi2=count($linkyazi);

$query="select * from $tablo";
if($filtre<>""){$query.=" where $filtre";};
if($sirala<>""){$query.=" order by $sirala";};
$result = mysql_db_query("$dbadi", $query);

if ($result)
        {
        $result2.="<form method=post action=sil.php>";
        $result2.="<table class='vidinlistyle' border=1 bordercolor='6666CC'><tr>";
        for ($i=0;$i<$alansayisi;$i++)$result2.="<td>$alan[$i]</td>";
        for ($i=0;$i<$alansayisi2;$i++)$result2.="<td>$linkyazi[$i]</td>";
        $result2.="<td>Sec</td></tr>";
        $satirno=1;
        while ($r = mysql_fetch_array($result))
                {
                $result2.="<tr>";
                for ($i=0;$i<$alansayisi;$i++)
                        {
                        $al=$alan[$i];
                        $yaz=$r[$al];
                        $silvalue=$r[$linkalan];
                        if($yaz==""){$result2.="<td>&nbsp</td>";}
                        else {
                                if(is_numeric($yaz)){$yaz="<p align=right>".number_format($yaz,2)."</p>";};
                                $result2.="<td>$yaz</td>";
                                };
                        if($al==$linkalan){$link=$r[$al];};
                        };
                for ($i=0;$i<$alansayisi2;$i++)
                        {
                        $ly=$linkyazi[$i];
                        $ld=$linkdosya[$i];
                        $result2.="<td><a href='$ld?$linkalan=$link' target='_blank'>$ly</a></td>";
                        }

                $result2.= "<td><input type=checkbox name='sil$satirno' value='$silvalue'></td></tr>\n";
                $satirno++;
                }

        $result2.="</table>";
        $result2.="<input type=submit value=sil></form>";

        mysql_free_result($result);
        }
        else
        {
        echo "Bir hata olustu:<br>sql:$query<br>";echo mysql_error();
        };
echo $result2;
return $result2;
};//fonksiyon

function tablolistele5($tablo,$alan,$filtre,$sirala,$linkyazi,$linkdosya,$linkalan,$actiondosya)
{
// herhangi bir action yapabilen tablo.
GLOBAL $confdir;
include($confdir."dbconf.php");
$result2="";
$alansayisi=count($alan);
$alansayisi2=count($linkyazi);

$query="select * from $tablo";
if($filtre<>""){$query.=" where $filtre";};
if($sirala<>""){$query.=" order by $sirala";};
$result = mysql_db_query("$dbadi", $query);

if ($result)
        {
        $result2.="<form method=post action=$actiondosya>";
        $result2.="<table class='vidinlistyle' border=1 bordercolor='6666CC'><tr>";
        for ($i=0;$i<$alansayisi;$i++)$result2.="<td>$alan[$i]</td>";
        for ($i=0;$i<$alansayisi2;$i++)$result2.="<td>$linkyazi[$i]</td>";
        $result2.="<td>Sec</td></tr>";
        $satirno=1;
        while ($r = mysql_fetch_array($result))
                {
                $result2.="<tr>";
                for ($i=0;$i<$alansayisi;$i++)
                        {
                        $al=$alan[$i];
                        $yaz=$r[$al];
                        $silvalue=$r[$linkalan];
                        if($yaz==""){$result2.="<td>&nbsp</td>";}
                        else {
                                if(is_numeric($yaz)){$yaz="<p align=right>".number_format($yaz,2)."</p>";};
                                $result2.="<td>$yaz</td>";
                                };
                        if($al==$linkalan){$link=$r[$al];};
                        };
                for ($i=0;$i<$alansayisi2;$i++)
                        {
                        $ly=$linkyazi[$i];
                        $ld=$linkdosya[$i];
                        $result2.="<td><a href='$ld?$linkalan=$link' target='_blank'>$ly</a></td>";
                        }

                $result2.= "<td><input type=checkbox name='sil$satirno' value='$silvalue'></td></tr>\n";
                $satirno++;
                }

        $result2.="</table>";
        $result2.="<input type=submit value=islemyap></form>";

        mysql_free_result($result);
        }
        else
        {
        echo "Bir hata olustu:<br>sql:$query<br>";echo mysql_error();
        };
echo $result2;
return $result2;
};//fonksiyon




function tablolistele6($tablo,$alan,$filtre,$sirala,$linkyazi,$linkdosya,$linkalan)
{
// tablolistele3 den tek farki, sayilarda formatlama yapmaz. telekom i�n.
GLOBAL $confdir;
include($confdir."dbconf.php");
$result2="";
$alansayisi=count($alan);
$alansayisi2=count($linkyazi);

$query="select * from $tablo";
if($filtre<>""){$query.=" where $filtre";};
if($sirala<>""){$query.=" order by $sirala";};
$result = mysql_db_query("$dbadi", $query);

if ($result)
        {
        $result2.= "<table class='vidinlistyle' border=1 bordercolor='6666CC'><tr>";
        for ($i=0;$i<$alansayisi;$i++)$result2.="<td>$alan[$i]</td>";
        for ($i=0;$i<$alansayisi2;$i++)$result2.="<td>$linkyazi[$i]</td>";
        $result2.="</tr>";

        while ($r = mysql_fetch_array($result))
                {
                $result2.="<tr>";
                for ($i=0;$i<$alansayisi;$i++)
                        {
                         $al=$alan[$i];
                        $yaz=$r[$al];
                        if($yaz==""){$result2.="<td>&nbsp</td>";}
                                        else {
                                                //if(is_numeric($yaz)){$yaz="<p align=right>".number_format($yaz)."</p>";};
                                                $result2.="<td>$yaz</td>";
                                                };

                        };

                $link=$r[$linkalan];

                for ($i=0;$i<$alansayisi2;$i++)
                        {
                        $ly=$linkyazi[$i];
                        $ld=$linkdosya[$i];
                        $result2.="<td><a href='$ld?$linkalan=$link' target='_blank'>$ly</a></td>";
                        }

                $result2.= "</tr>\n";
                }

        $result2.= "</table>";
        mysql_free_result($result);
        }
        else
        {
        echo "Bir hata olustu:<br>sql:$query<br>";echo mysql_error();
        };
echo $result2;
return $result2;
};//fonksiyon




function tablolistele7($tablo,$alan,$filtre,$sirala,$linkyazi,$linkdosya,$linkalan)
{
// tablolistele3 den tek farki, sayilarda formatlama yapmaz. telekom i�n.
GLOBAL $confdir;
include($confdir."dbconf.php");
$result2="";
$alansayisi=count($alan);
$alansayisi2=count($linkyazi);

$query="select * from $tablo";
if($filtre<>""){$query.=" where $filtre";};
if($sirala<>""){$query.=" order by $sirala";};
$result = mysql_db_query("$dbadi", $query);

if ($result)
        {
        $result2.= "<table class='vidinlistyle' border=1 bordercolor='6666CC'><tr>";
        for ($i=0;$i<$alansayisi;$i++)$result2.="<td>$alan[$i]</td>";
        for ($i=0;$i<$alansayisi2;$i++)$result2.="<td>$linkyazi[$i]</td>";
        $result2.="</tr>";

        while ($r = mysql_fetch_array($result))
                {
                $result2.="<tr>";
                for ($i=0;$i<$alansayisi;$i++)
                        {
                         $al=$alan[$i];
                        $yaz=$r[$al];
                        if($yaz==""){$result2.="<td>&nbsp</td>";}
                                        else {
                                                //if(is_numeric($yaz)){$yaz="<p align=right>".number_format($yaz)."</p>";};
                                                $result2.="<td>$yaz</td>";
                                                };

                        };

                $link=$r[$linkalan];

                for ($i=0;$i<$alansayisi2;$i++)
                        {
                        $ly=$linkyazi[$i];
                        $ld=$linkdosya[$i];
                        $result2.="<td><a href='$ld$linkalan=$link' target='_blank'>$ly</a></td>";
                        }

                $result2.= "</tr>\n";
                }

        $result2.= "</table>";
        mysql_free_result($result);
        }
        else
        {
        echo "Bir hata olustu:<br>sql:$query<br>";echo mysql_error();
        };
echo $result2;
return $result2;
};//fonksiyon

function updatestring2($alanlar) // alanlar numerik olamaz.
{
global $$alanlar[0];
$alansayisi=count($alanlar);
// echo "count: $alansayisi <br>";
$set="$alanlar[0]='".$$alanlar[0]."'";

for($i=1;$i<$alansayisi;$i++) {
		global $$alanlar[$i];
        $set.=",$alanlar[$i]='".$$alanlar[$i]."'";
}
return $set ;
}


function tablolistelefiltre($tablo,$alan,$filtre)
// listelerken filtre kullanir.
{
GLOBAL $confdir;
include($confdir."dbconf.php");
$result2="";
$alansayisi=count($alan);

$query="select * from $tablo";
if($filtre!=""){$query.=" where $filtre";};
$result = mysql_db_query("$dbadi", $query);

if ($result)
{
$result2.= "<table class='vidinlistyle' border=1 bordercolor='6666CC'><tr>";
for ($i=0;$i<$alansayisi;$i++)$result2.="<td>$alan[$i]</td>";
$result2.="</tr>";

while ($r = mysql_fetch_array($result))
{
$result2.="<tr>";
for ($i=0;$i<$alansayisi;$i++)
        {
                $al=$alan[$i];
                $result2.="<td>$r[$al]</td>";
        };
        $result2.= "</tr>";
}
$result2.= "</table>";
mysql_free_result($result);
} else { echo "Bir hata olustu:";echo mysql_error();}

return $result2;
};//fonksiyon



function dbquery($query)
{
GLOBAL $confdir;
include($confdir."dbconf.php");
//echo "Uygulanan query: $query <br>";
$result=mysql_db_query($dbadi,$query);
if (!$result){echo "<br>(query1**) Hata olutu: conf:$confdir, db: $dbadi, kull: $mysqlkullaniciadi ";echo mysql_error();echo "<br>query: <xmp>$query</xmp>";};
return $result;

}


function tabloyabosekle($tablo)
{
GLOBAL $confdir;
include($confdir."dbconf.php");

$query="insert into $tablo values ()";
return dbquery($query);
}//fonksiyon tabloyaekle



function tabloyaekle2($tablo,$degerler)
{
GLOBAL $confdir;
include($confdir."dbconf.php");

$query="insert into $tablo values ($degerler)";
return dbquery($query);
}//fonksiyon tabloyaekle

function where($alanlar,$degerler)
{
// buraya query'ler icin where cumlesi olusturan komutlar yazilacak.
};

function tablodayoksaekle($alanlar,$degerler)
{
// buraya tabloda bir veri yoksa ekleyen komutlar.

};

function tabloyaekle($tablo,$alanlar,$degerler)
{
GLOBAL $confdir;
include($confdir."dbconf.php");
$alansayisi=count($alanlar);
$query="insert into $tablo ($alanlar[0]";

for ($i=1;$i<$alansayisi;$i++)
        {
        $query.=",$alanlar[$i]";
        };

$query.=") values (";

$query.="'$degerler[0]'";

for ($i=1;$i<$alansayisi;$i++)
        {
        $query.=",'$degerler[$i]'";
        };

$query.=")";

//echo "<br>Query: $query <br>";
$result=dbquery($query);
return $result;
}


function tablogiris($alanlar,$gizlialanlar,$gizlialandegerler)
{
GLOBAL $confdir;
$alansayisi=count($alanlar);

$result="<form name='formgonder' method='POST'><table>";
for ($i=0;$i<$alansayisi;$i++)
        {
        $result.="<tr><td>$alanlar[$i]:    </td><td><input type='text' name='$alanlar[$i]'></td></tr>";
        };

$result.="</table>
<input type='submit' value='Kaydet'>
<input type='reset' value='Temizle'>";
$alansayisi=count($gizlialanlar);
for ($i=0;$i<$alansayisi;$i++)
        {
        $result.="<input type='hidden' name='$gizlialanlar[$i]' value='$gizlialandegerler[$i]'>";
        }
$result.="</form>";
echo $result;
return $result;
}


function tablogiris3($alanlar,$degerler,$gizlialanlar,$gizlialandegerler)
{//textbox icine deger yazabilir.
GLOBAL $confdir;
$alansayisi=count($alanlar);

$result="<form name='formgonder' method='POST'><table>";
for ($i=0;$i<$alansayisi;$i++)
                {
                $result.="<tr><td>$alanlar[$i]:    </td><td><input type='text' name='$alanlar[$i]' value='$degerler[$i]'></td></tr>";
                };

$result.="</table>
<input type='submit' value='Kaydet'>
<input type='reset' value='Temizle'>";
$alansayisi=count($gizlialanlar);
for ($i=0;$i<$alansayisi;$i++)
                {
                    $result.="<input type='hidden' name='$gizlialanlar[$i]' value='$gizlialandegerler[$i]'>";
                    }
                        $result.="</form>";
                        echo $result;
return $result;
}

function tablogiris2($tablo,$kontrol,$alanlar,$degerler,$gizlialanlar,$gizlialandegerler)
{
// kontrol ederek $kontrol alanlari bos degilse ekleme yapar.
GLOBAL $confdir;
$dogru=1;
$alansayisi=count($kontrol);
for ($i=0;$i<$alansayisi;$i++){if ($kontrol[$i]==""){$dogru=0;};};
if($dogru==1)
        {

        $alanlar1=array_merge($alanlar,$gizlialanlar);
        $degerler1=array_merge($degerler,$gizlialandegerler);
        $result=tabloyaekle($tablo,$alanlar1,$degerler1);
        if (!$result){echo "<br>Eklerken hata olutu.";};
        }
        else {tablogiris3($alanlar,$degerler,$gizlialanlar,$gizlialandegerler);};

return $result;
}


function tabloguncelle($tablo,$index,$indexdeger,$alanlar,$degerler)
{
GLOBAL $confdir;
include($confdir."dbconf.php");
$alansayisi=count($alanlar);
$qu=" $alanlar[0]='$degerler[0]'";

for ($i=1;$i<$alansayisi;$i++)
        {
        $qu.=", $alanlar[$i]='$degerler[$i]'";
        };


$query="update $tablo set $qu where $index=$indexdeger";
echo "$query <br>";
return dbquery($query);
}


function kayitsayisi($tablo,$filtre)
{
GLOBAL $confdir;
$query="select count(*) as count from $tablo";
if($filtre!=""){$query.=" where $filtre";};
//echo "<br>$query<br>";
$result=dbquery($query);
if(!$result){return -1;}
else
{
$r=mysql_fetch_array($result);
$sayi=$r["count"];
return $sayi;
};

};

function kayitsayisibul($tablo,$filtre)
{
GLOBAL $confdir;
$query="select count(*) as count from $tablo";
if($filtre!=""){$query.=" where $filtre";};
//echo "<br>$query<br>";
$result=dbquery($query);
if(!$result){return -1;}
    else{
        $r=mysql_fetch_array($result);
        $sayi=$r["count"];
        return $sayi;
    };

};



function turkcegun($gun)
{
if($gun=="Sat"){$res="Cumartesi";}
elseif($gun=="Sun"){$res="Pazar";}
elseif($gun=="Mon"){$res="Pazartesi";}
elseif($gun=="Tue"){$res="Sali";}
elseif($gun=="Wed"){$res="Carsamba";}
elseif($gun=="Thu"){$res="Persembe";}
elseif($gun=="Fri"){$res="Cuma";};
return $res;
}



function htmlekle($id)
{        //echo "test";
echo htmlekle2($id);
}

function htmlekle2($id) {        // bunun tek farki echo yapmaz. return eder.
	$id=trim($id);
    if($id=="") {
    	return "id bo verilmi. (htmlekle2)";
    }
    if(kayitsayisi("html","id='$id'")==0){
    	return "($id) id'li kod bulunamadi. eklemek icin <a href=/kafe/admin/htmlkodekle.php?id=$id>buraya tiklayiniz</a> ";
    }

    GLOBAL $nestcount;
    $nestcount++;
    if($nestcount>100){echo "<hr>Cok fazla icice dongu(nest) var.(100 adet)";exit;};
    $query="select * from html where id='$id'";
    $kod=dbresult($query,array("htmlkodu"));
    //$kod="<kodadi=$id>".$kod."</kodadi=$id>";
    $parcalar=explode("{kod}",$kod[0]);
    $sayi=count($parcalar);

    $out="";
    $out.= "\n<kodadi=$id>";
    for($i=0;$i<$sayi;$i++)         {
                    if(iseven($i))
                    {
                    $out.= $parcalar[$i];
                    }
                    else
                    {
                           $out.= htmlekle2($parcalar[$i]);
                    }
            };
            $nestcount--;
    $out.="</kodadi=$id>";
    return $out;
}


function checkpass3($tablo,$kullanicialan,$sifrealan,$kullanici,$sifre)
{
GLOBAL $confdir;
include($confdir."dbconf.php");

$mysqldbhost=$dbhost;
// $mysqlkullaniciadi=   ;
//$mysqlsifre="prxj";
$mysqldbadi=$dbadi;


$conn=mysql_connect($mysqldbhost, $mysqlkullaniciadi, $mysqlsifre);
if(!$conn){echo mysql_error();die ("<br><br><br>mysql'e Baglanilamadi");};


$passtable=$tablo;
$userfield=$kullanicialan;
$passfield=$sifrealan;

$kullanici=trim($kullanici);
$sifre=trim($sifre);


$query="select count(*) as count from $passtable where $userfield='$kullanici' and $passfield='$sifre'";
$result=mysql_db_query($mysqldbadi,$query);

if ($result)
{
while ($r = mysql_fetch_array($result))
    {
    $sayi=$r["count"];
    if($sayi>0){$res=true;}else{$res=false;};
    }
}
else
{echo "Database'e baglanirken hata olustu. query:$query";exit;};


return $res;
}


function sifrehatirlat($tablo,$email,$emailalan,$kullanicialan,$sifrealan)
{
GLOBAL $confdir;
include($confdir."dbconf.php");

$mysqldbhost=$dbhost;
// $mysqlkullaniciadi=   ;
//$mysqlsifre="prxj";
$mysqldbadi=$dbadi;

$headers="From: info@vidinli.com";

$conn=mysql_connect($mysqldbhost, $mysqlkullaniciadi, $mysqlsifre);
if(!$conn){echo mysql_error();die ("<br><br><br>mysql'e Baglanilamadi");};


$passtable=$tablo;
$userfield=$kullanicialan;
$passfield=$sifrealan;

$email=trim($email);


$query="select $kullanicialan,$sifrealan from $passtable where $emailalan='$email'";
$result=mysql_db_query($mysqldbadi,$query);

if ($result)
{
while ($r = mysql_fetch_array($result))
    {
    $kullanici=$r["$kullanicialan"];
    $sifre=$r["$sifrealan"];
    }
}
else
{echo "Database'e baglanirken hata olustu. query:$query";exit;};

mysql_close($conn);
$mesaj="Sitemizi kullandiginiz icin tesekkur ederiz.Bilgileriniz asagidadir: \n Kullanici adiniz: $kullanici, Sifreniz: $sifre ";
mail($email,"www.vidinli.com/kasa sifre",$mesaj,$headers);
return $result;
}


function inputform_eskisi($action,$alan,$deger)
{
$alansayisi=count($alan);
$res="<form";
if($action<>""){$res.=" action='$action'";};
$res.="><table>";

for($i=0;$i<$alansayisi;$i++) $res.="<tr><td>$alan[$i] </td><td> <input type=text name=$alan[$i] value='$deger[$i]'></td></tr>";
$res.="</table><input type=submit value='Kaydet/Gnder'></form>";
echo $res;

}


function inputform($action,$alan,$deger)
{
$alansayisi=count($alan);
$res="<form";
if($action<>""){$res.=" action='$action'";};
$res.="><table>";


for($i=0;$i<$alansayisi;$i++)
        {
        $res.="<tr><td>$alan[$i] </td><td> ";
        $sayi=count($deger[$i]); // array m bu ? diye bulur.
        if(($sayi)>1)
                {

                $res.="\n\n<select name='$alan[$i]'>\n";
                for ($j=0;$j<$sayi;$j++) $res.="<option value='".$deger[$i][$j]."'>".$deger[$i][$j]."</option>\n";
                $res.="</select>\n";
                }
                else
                {
                $res.="<input type=text name=$alan[$i] value='$deger[$i]'>";
                };
        $res.="</td></tr>";

        };
$res.="</table><input type=submit value='Kaydet/Gnder'></form>";
echo $res;

}

function inputform4($action,$yazilacak,$alan,$deger,$hiddenalanlar,$hiddendegerler) {
/*
sadece echo yapmaz.
degistirildi. artik textarea gosterebiliyor.
$res.="alanlar:".print_r2($alan);
$res.="degerler:".print_r2($deger);
 */
        $alansayisi=count($alan);

        $res.="<form method=post ";
        if($action<>""){$res.=" action='$action'";};
        $res.="><table>";


        for($i=0;$i<$alansayisi;$i++) {
                        if($yazilacak[$i]==""){
                                if(is_array($alan[$i]))$yaz=$alan[$i][0]; else $yaz=$alan[$i];
                        }else{
                                $yaz=$yazilacak[$i];
                        };
                        $res.="<tr><td>$yaz </td><td> ";
                        $res.=inputelement($alan[$i],$deger[$i]);


                $res.="</td></tr>\n";
        };

        $alansayisi=count($hiddenalanlar);
        for($i=0;$i<$alansayisi;$i++) {
            $res.="<tr><td><input type=hidden name=$hiddenalanlar[$i] value='$hiddendegerler[$i]'>";
            $res.="</td></tr>\n";
        };

        $res.="</table><input type=submit value='Submit / Gonder'></form>";
        return $res;
}



function inputelement($alan,$deg){
        if(is_array($deg)) {
                $sayi=count($deg);
                $res.="\n\n<select name='$alan'>\n\r";
                for ($j=0;$j<$sayi;$j++) $res.="<option value='".$deg[$j]."'>".$deg[$j]."</option>\n\r";
                $res.="</select>\n";
        } else {

                if(is_array($alan)){  // input type text ten fakl\xfd ise.
                        $name=$alan[0];
                        $type=$alan[1];
                        switch($type) {
                                case 'password':
                                        $res.="<input type='password' name=$name value='$deg'>";
                                        break;
                                case 'text':
                                        $res.="<input type=$type name=$name value='$deg'>";
                                        break;
                                case 'textarea':
                                        $res.="<textarea cols=40 name='$name' rows=10>".trim($deg)."</textarea> <br>";
                                        break;
                                case 'checkbox':
                                        $res.="<input type=checkbox name='$name' ".($deg==''?'':'checked=yes')."><br>";
                                        break;
                        }
                } else {
                        $res.="<input type=text name=$alan value='$deg'>";
                }
        }
        return $res;
}



function inputform3($action,$yazilacak,$alan,$deger,$hiddenalanlar,$hiddendegerler)
{
$alansayisi=count($alan);
$res="<form";
if($action<>""){$res.=" action='$action'";};
$res.="><table>";


for($i=0;$i<$alansayisi;$i++)
        {
        if($yazilacak[$i]==""){$yaz=$alan[$i];}else{$yaz=$yazilacak[$i];};
        $res.="<tr><td>$yaz </td><td> ";
        $sayi=count($deger[$i]); // array m bu ? diye bulur.
        if(($sayi)>1)
                {
                $res.="\n\n<select name='$alan[$i]'>\n\r";
                for ($j=0;$j<$sayi;$j++) $res.="<option value='".$deger[$i][$j]."'>".$deger[$i][$j]."</option>\n\r";
                $res.="</select>\n";
                     }
                     else
                        {
                        $res.="<input type=text name=$alan[$i] value='$deger[$i]'>";
                        };
                $res.="</td></tr>\n";
                };

$alansayisi=count($hiddenalanlar);
for($i=0;$i<$alansayisi;$i++)
        {
        $res.="<tr><td><input type=hidden name=$hiddenalanlar[$i] value='$hiddendegerler[$i]'>";
        $res.="</td></tr>\n";
        };



$res.="</table><input type=submit value='Kaydet/Gonder'></form>";
echo $res;

}

function inputform2($action,$alan,$deger,$hiddenalanlar,$hiddendegerler)
{
$alansayisi=count($alan);
$res="<form";
if($action<>""){$res.=" action='$action'";};
$res.="><table>";


for($i=0;$i<$alansayisi;$i++)
        {
        $res.="<tr><td>$alan[$i] </td><td> ";
        $sayi=count($deger[$i]); // array m bu ? diye bulur.
        if(($sayi)>1)
                {
                $res.="\n\n<select name='$alan[$i]'>\n\r";
                for ($j=0;$j<$sayi;$j++) $res.="<option value='".$deger[$i][$j]."'>".$deger[$i][$j]."</option>\n\r";
                $res.="</select>\n";
                     }
                     else
                        {
                        $res.="<input type=text name=$alan[$i] value='$deger[$i]'>";
                        };
                $res.="</td></tr>\n";
                };

$alansayisi=count($hiddenalanlar);
for($i=0;$i<$alansayisi;$i++)
        {
        $res.="<tr><td><input type=hidden name=$hiddenalanlar[$i] value='$hiddendegerler[$i]'>";
        $res.="</td></tr>\n";
        };



$res.="</table><input type=submit></form>";
echo $res;

}



function buildoption($optionname,$tablo,$optionvaluealan,$optionalan,$filtre)
{
GLOBAL $confdir;
include($confdir."dbconf.php");


$query="select $optionvaluealan,$optionalan from $tablo";
if($filtre<>"")$query.=" where $filtre ";
// echo "query: $query <br>";

$res="\n<select name='$optionname'>\n";

$result = mysql_db_query($dbadi, $query);

if($result)
        {
        while ($r = mysql_fetch_array($result))
                {
                $res.="<option value=\"".$r[0]."\">".$r[1]."</option>\n";
                }
        };
$res.="</select>\n";

echo $res;
return $res;
}

function wapecho($icerik)
{
$icerik1="<?xml version=\"1.0\"?>
<!DOCTYPE wml PUBLIC \"-//WAPFORUM//DTD WML 1.1//EN\" \"http://www.wapforum.org/DTD/wml_1.1.xml\">

<wml>
<card>
<p>".
$icerik.
"</p></card>
</wml>";
echo $icerik1;
};

function updatestring($updatestr,$kontrol,$yenistr)
{
if($kontrol<>"")
        {
        if ($updatestr<>"")$updatestr.=",";
        $updatestr.=$yenistr;
        };

return $updatestr;
}

function filterstring($filterstr,$kontrol,$yenistr)
{
// ornek: $filtre=filterstring($filtre,$hizmetturu,"hizmetturu='$hizmetturu'");

if($kontrol<>"")
        {
        if ($filterstr<>"")$filterstr.=" and ";
        $filterstr.=$yenistr;
        };
return $filterstr;
}

function filterstring2($alanlar)
{
// bioncekinden farkly olarak array halinde alanlari kabul eder ve filter string olusturur.
// ornek: $filtre=filterstring(array("isim","soyisim"))  v.b.  sadece string icin yani ' kullanilabilecek.
// ornek: $filtre=filterstring($filtre,$hizmetturu,"hizmetturu='$hizmetturu'");
$alansayisi=count($alanlar);
echo "alansayisi: $alansayisi <br>";
$res="";

for($i=0;$i<$alansayisi;$i++){
        $alan=$alanlar[$i];
        GLOBAL $$alan;
        $res=filterstring($res,$$alan," $alan like '%".$$alan."%'");
}

return $res;
}


function filterstring3($filterstr,$kontrol,$yenistr,$logical)
{
// digerinden farkl olarak and/or serbest verilebilir
// ornek: $filtre=filterstring($filtre,$hizmetturu,"hizmetturu='$hizmetturu'","or");
if($kontrol<>"")
        {
        if ($filterstr<>"")$filterstr.=" $logical ";
        $filterstr.=$yenistr;
        };
return $filterstr;
}

function filterstring4($alanlar)
{ // filterstring2 den farkl olarak sadece and/or serbest...
// bioncekinden farkly olarak array halinde alanlari kabul eder ve filter string olusturur.
// ornek: $filtre=filterstring(array("isim","soyisim"))  v.b.  sadece string icin yani ' kullanilabilecek.
// ornek: $filtre=filterstring($filtre,$hizmetturu,"hizmetturu='$hizmetturu'");
$alansayisi=count($alanlar);
echo "alansayisi: $alansayisi <br>";
$res="";
for($i=0;$i<$alansayisi;$i++){
        $alan=$alanlar[$i];
        GLOBAL $$alan;
        $res=filterstring3($res,$$alan," $alan like '%".$$alan."%'","or");
}
return $res;
}


function acilariza($mesaj)
{
echo "<font size=+2>� anda buras ge�ci bir arza nedeniyle �lmyor. Ltfen ksa sre bekleyiniz, $mesaj</font><br>";
}

function countergoster($counterid,$inc,$url)
{
        GLOBAL $confdir;
include($confdir."dbconf.php");

 $alanlar='counterid,count';
 $sayi='';
 $tablo="counter";

// $mysql=mysql_connect("localhost", "$mysqlkullaniciadi", "$mysqlsifre");
// if(!$mysql){ echo "(countergoster) database'e baglanilamadi.";echo mysql_error();exit;};

$query = "SELECT $alanlar FROM counter $tablo where counteradi='$counterid'";
 //echo "<br> '$query'<br>";
$result11=mysql_db_query($dbadi,$query);
if(!$result11){echo "(countergoster)database hatasi olustu.";echo mysql_error();exit;};
    if ($result11)
    {
            //$count=0;
        while ($r = mysql_fetch_array($result11))
        {
            $count=$r["count"];
                                 };
        if ($inc<>'')
                {
                //echo ".artirildi.";
                $query = "update $tablo set count=count+1 where counteradi='$counterid'";
                $result112 = mysql_db_query("$dbadi", $query);
                if (!($result112)){echo "artirirken hata.";echo mysql_error();};
                echo $count;
                }

        if ($url<>'') {header("Location:$url");};

        if(kayitsayisi("counter","counteradi='$counterid'")==0)
                {
                        dbquery("insert into counter (counteradi,count)values('$counterid',0)");
                        echo "<br> $counterid counter listesine eklendi.(countergoster)";
                        };

            }//if result11
        else
      {
        echo "Bir hata olutu veya bo."; echo "<br> Hata kodu: "; echo mysql_errno();echo "<br> Hata: "; echo mysql_error();
        }


}
?>