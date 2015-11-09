<?php
/**
 * Common lib a collection of useful functions
 *
 * Nne  : Ninety Nine Enemies Project (http://thnet.komunikando.org)
 * 
 * Copyright (c) Ninety Nine Enemies Project, (http://thnet.komunikando.org)
 * Licensed under The MIT License
 * For license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 * 
 * Based on:
 * SlimStarter, https://github.com/xsanisty/SlimStarter
 * slim-facades, https://github.com/itsgoingd/slim-facades
 * 
 * @copyright	Copyright (c) Ninety Nine Enemies, (http://thnet.komunikando.org)
 * @link		http://thnet.komunikando.org Ninety Nine Enemies Project
 * @package		Nne\Controllers
 * @since		Nne (tm) v 1
 * @license		http://www.opensource.org/licenses/mit-license.php MIT License
 * @project		Ninety Nine Enemies Project 
 * @encoding	utf-8
 * @author		Giorgio Tonelli <th.thantalas@gmail.com>, <http://thnet.komunikando.org>
 * @creation	08/nov/2015
 */
function sendInfoForm($type,$maildata,$to){
	
	$mailMessage=new mailMessage($maildata,$type,explode(",",$to),$maildata['email'],Config::getConf('__CONFIG_SITE_NAME'));
	//$mailMessage->testMode=true;
	$validation['status']=$mailMessage->sendMEssage();
	if(!$validation['mailsended']){
		$validation['message']=Msg::getText('error mailgenericnotsended',$maildata['email']);
	}else{
		$validation['message']=Msg::getText('gnrl contacts sended message');
	}
	return $validation;
}
function twitterPost($twit,$url=''){
	// Convert URLs into hyperlinks
	$twit = preg_replace("/(https?:\/\/)(.*?)\/([\w\.\/&=?\-,:;#_~%+]*)/", "<a href=\"\$0\" target=\"_blank\">\$0</a>", $twit);  
	// Convert usernames (@) into links 
	$twit = preg_replace("(@([a-zA-Z0-9_]+))", "<a href=\"https://www.twitter.com/\$1\" target=\"_blank\">\$0</a>", $twit);
	// Convert hash tags (#) to links 
	$twit = preg_replace('/(^|\s)#(\w+)/', '\1<a href="https://www.twitter.com/search?q=\2" target="_blank">#\2</a>', $twit);  
	return $twit;
}
function getTwitterTimeline($count, $username) {
	if(isset($_SESSION['latest_tweet'])) return $_SESSION['latest_tweet'];
	require_once(__LIBS_INCLUDE_PATH.'external/cl_th_twitter.php');
	ThTwitter::singleton();
	return $_SESSION['latest_tweet']=ThTwitter::getStream($username,$count);
}
/**
 * prepara le dade dei post
 * @param String $string data proveniente da db
 * @return Array
 */
function commentSmartDate($date){
	$commDatea=getCommentDate($date);
	$commentData=$commDatea['daylabel'];
	//05 luglio 2013 ore 08:34
	//if(empty($commentData)) $commentData=' '.$commDatea['mday'].' '.$commDatea['month'].' '.$commDatea['year'].' '.Msg::getText('lbl hours ').' '.$commDatea['hour'].'';
	//05/07/2013 08:34
	if(empty($commentData)) $commentData=$commDatea['mday'].'/'.$commDatea['monthn'].'/'.$commDatea['year'].' '.$commDatea['hour'].'';
	else $commentData.=' '.$commDatea['hour'];
	return $commentData;
}
function getCommentDate($date){
	$vr=false;
	$vr['daylabel']='';
	$ts=strtotime($date);
	$timestamp_attuale = time();
	$giorni_passati = date("j", $timestamp_attuale) - date("j",$ts) ;
	$minuti_passati = intval(($timestamp_attuale - $ts) / 60);
	$ore_passate = intval($minuti_passati / 60);
	if ( $giorni_passati  <=1 ) {
		$minuti_passati = intval(($timestamp_attuale - $ts) / 60);
		if ( $minuti_passati < 59 ) {
			if ($minuti_passati > 1 ){
				$vr['daylabel']=Msg::getText('lbl minutes elapsed',$minuti_passati);
			}elseif($minuti_passati==1){
				$vr['daylabel']=Msg::getText('lbl minute elapsed',$minuti_passati) ;
			}else{
				$vr['daylabel']=Msg::getText('lbl minute less than one');
			}
		}else {
			$ore_passate = intval($minuti_passati / 60);
			if ( $ore_passate == 0 ) $ore_passate =1;
			if ( $ore_passate > 1 && $ore_passate<24){
				$vr['daylabel']=Msg::getText('lbl hours elapsed',$ore_passate);
			}elseif($ore_passate<24){
				$vr['daylabel']=Msg::getText('lbl hour elapsed',$ore_passate);
			}
			
		}
	}
	if($giorni_passati==1 && $ore_passate>24){
		$vr['daylabel']=Msg::getText('lbl yesterday');
	}
	$d=getdate($ts);
	$vr['year']=$d['year'];
 	$mese=monthName();
 	$vr['month']=mb_strtolower($mese[intval($d['mon'])]);
 	$vr['monthn']=$d['mon'];
 	if(intval($d['mon'])<10){
 		$vr['monthn']='0'.$d['mon'];
 	}
 	$weekd=weekDays();
 	$vr['wday']=$weekd[intval($d['wday'])];
 	$vr['mday']=$d['mday'];
 	if(intval($d['mday'])<10){
 		$vr['mday']='0'.$d['mday'];
 	}
 	
 	
 	$ora=$d['hours'];
 	if($d['hours']<10)
 		$ora='0'.$d['hours'];
 	$minuti=$d['minutes'];
 	if($d['minutes']<10)
 		$minuti='0'.$d['minutes'];
 	$vr['hour']=$ora.':'.$minuti;
 	return $vr;
}
/**
 * in base al numero dinamico delle colonne ritorna la classe 
 * span corretta di bootstrap in un  contesto di colonne dinamico a dimensione uguale
 * @param int $ncols
 */
function getBsSpanByCols($ncols){
	switch($ncols){
		case 0:
		case 1:
			return '12';
			break;
		case 2:
			return '6';
			break;
		case 3:
			return '4';
			break;
		case 4:
			return '3';
			break;
		case 5:
			return '2,4';
			break;
		case 6:
			return '2';
			break;
		case 7:
			return '1,5';
			break;
		default:
			return '1';
	}
	return '12';
}
/**
* funzione che crea i link e la legenda per la navigazione dei record
*
* utilizzata in tutti gli script dove è necessaria la paginazione di un array
* @param string $baseUrl url dello script
* @param integer $totalRecord numero di record totali
* @param integer $itemXPage numero di record per pagina
* @param string $getPageParam kiave in get paginazione
* @param string $cssClassPrev classe css link precedente
* @param string $cssClassNext classe css link successivo
* @param string $nextHtml htm per il link next
* @param string $prevHtml htm per il link prev
* @return array;
*/
function pageRecord($baseUrl,$totalRecord=0,$itemXPage=10,$cssClassPrev='arrowprev',$cssClassNext='arrownext'){
	$nextHtml="&gt;";
	$prevHtml="&lt;";
	$vr['nextLink']='<span class="'.$cssClassNext.'"> &gt; </span>';
	$vr['prevLink']='<span class="'.$cssClassPrev.'"> &lt;</span>';
	$vr['pageNumbers']='';
	$curPage=1;
	$paramSep=(!strpos($baseUrl,"?")===false) ? '&' : '?';
	$curPage=(isset($_GET['page']) && is_numeric($_GET['page'])) ? intval($_GET['page']): 1;
	if($curPage<=0) $curPage=1;
	if(intval($itemXPage) ==0){
		$itemXPage=10;
	}
	$firstPage= ($curPage - 1) * $itemXPage;
	$totPages = ceil($totalRecord/ $itemXPage);

	if($totalRecord>$itemXPage){
		if($curPage!=1){
			// prima pagina
			$vr['prevLink']= '<a href="'.$baseUrl.$paramSep.'page'.'='.($curPage - 1).'" id="prevPage" class="'.$cssClassPrev.'" rel="'.($curPage - 1).'" >'.$prevHtml.'</a>';
		}
		if($curPage != $totPages) {
			$next_page = ($curPage + 1);
			$vr['nextLink']= '<a href="'.$baseUrl.$paramSep.'page'.'='.($curPage + 1).'" id="nextPage" class="'.$cssClassNext.'" rel="'.($curPage + 1).'" >'.$nextHtml.'</a>';
		}
	}

	return $vr;
}
function pageRecordLazy($baseUrl,$totalRecord=0,$itemXPage=10,$anchor="",$dataUrl=""){
	$vr='';
	$prevChar='&laquo;';
	$nextChar='&raquo;';
	$totPages = ceil($totalRecord/ $itemXPage);
	$paramSep=(!mb_strpos($baseUrl,"?")===false) ? '&' : '?';
	if($totPages<=1) return $vr;
	$curPage=(isset($_GET['page']) && is_numeric($_GET['page'])) ? intval($_GET['page']): 1;
	if($dataUrl) $dataUrl=' data-url="'.$dataUrl.'"';
	for($i = 1; $i<= $totPages ; $i++){
		if($i>1){// prima pagina no link
			$url=addParamToUrl($baseUrl, array('page'=>$i.$anchor));
			if($dataUrl) $urlData=' data-url="'.addParamToUrl($dataUrl, array('page'=>$i.$anchor)).'" ';
			$vr.='<span class="pageRecord"><a href="'.$url.'"  '.$urlData.'  class="nav-link" rel="'.$i.'">'.$i.'</a></span>';
		} else $vr.='<span class="pageRecord selected">'.$i.'</span>';
	}
	return $vr;
}
function pageRecordNumber($baseUrl,$totalRecord=0,$itemXPage=10,$anchor="",$dataUrl=""){
	$vr='';
	$prevChar='&laquo;';
	$nextChar='&raquo;';
	$totPages = ceil($totalRecord/ $itemXPage);
	$paramSep=(!mb_strpos($baseUrl,"?")===false) ? '&' : '?';
	if($totPages<=1) return $vr;
	$limit=9;
	$curPage=(isset($_GET['page']) && is_numeric($_GET['page'])) ? intval($_GET['page']): 1;
	if ($curPage<1) $curPage=1;
	$start = $curPage - 5;
	if ($start<1) $start = 1;
	$end = $start + $limit;
	if ($end > $totPages) $end = $totPages;
	$start = $end - $limit;
	if ($start<1) $start = 1;
	$urlData='';

	$url=addParamToUrl($baseUrl, array('page'=>'1'.$anchor));
	if(($curPage-1)>1)$vr.='<span class="pageRecord"><a href="'.$url.'" '.$dataUrl.' class="nav-link laquoraquo" rel="1">'.$prevChar.$prevChar.'</a></span>';
	$url=addParamToUrl($baseUrl, array('page'=>($curPage-1).$anchor));
	if($dataUrl) $urlData=' data-url="'.addParamToUrl($baseUrl, array('page'=>($curPage-1).$anchor)).'"';
	if($curPage>1) $vr.='<span class="pageRecord" style="margin-right:4px;"><a href="'.$url.'"  '.$urlData.'  class="nav-link laquoraquo" rel="'.($curPage-1).'">'.$prevChar.'</a></span>';
	for ($i=$start; $i<=$end; $i++) {
		if($dataUrl) $urlData=' data-url="'.addParamToUrl($baseUrl, array('page'=>$i.$anchor)).'"';
		if ($i != $curPage){
			$url=addParamToUrl($baseUrl, array('page'=>$i.$anchor));
			$vr.='<span class="pageRecord"><a href="'.$url.'"  '.$urlData.'  class="nav-link" rel="'.$i.'">'.$i.'</a></span>';
		} else $vr.='<span class="pageRecord selected">'.$i.'</span>';
	}
	if($curPage<$totPages) {
		$url=addParamToUrl($baseUrl, array('page'=>($curPage+1).$anchor));
		if($dataUrl) $urlData=' data-url="'.addParamToUrl($baseUrl, array('page'=>($curPage+1).$anchor)).'"';
		$vr.='<span class="pageRecord" style="margin-left:4px;"><a href="'.$url.'"  '.$urlData.'  class="nav-link laquoraquo" rel="'.($curPage+1).'">'.$nextChar.'</a></span>';
	}
	$url=addParamToUrl($baseUrl, array('page'=>$totPages.$anchor));
	if($dataUrl) $urlData=' data-url="'.addParamToUrl($baseUrl, array('page'=>$totPages.$anchor)).'"';
	if(($curPage+1)<$totPages) $vr.='<span class="pageRecord"><a href="'.$url.'"  '.$urlData.'  class="nav-link laquoraquo" rel="'.$totPages.'" >'.$nextChar.$nextChar.'</a></span>';
	return $vr;
}
function getPageRecord($record,$perPage=10){
	$perPage=intval($perPage);
	if(isset($_GET['page']) && intval($_GET['page'])>1){
		return array_slice($record,($perPage*(intval($_GET['page'])-1)) , $perPage);
	}else{
		return array_slice($record, 0, $perPage);
	}
}
function th_strtolower($text){
	return (function_exists('mb_strtolower'))?  mb_strtolower($text,__CONFIG_CHARSET): strtolower($text);
}
function th_strtoupper($text){
	return (function_exists('mb_strtoupper'))?  mb_strtoupper($text,__CONFIG_CHARSET): strtoupper($text);
}
/**
* trasforma un url di youtube in codice iframe
*
* Enter description here ...
* @param string $video url pagina you tube
* @param string $w larghezza iframe
* @param string $h altezza iframe
* @param string $ap autoplay [1 o 0]
* @return string
*/
function youtubeUrlToIframe($video,$w='500',$h='315',$ap=1){
	//$video='http://www.youtube.com/watch?v=OFNfrkwcu48&feature=g-feat';
	//$video="http://youtu.be/OFNfrkwcu48";
	//$video="http://www.youtube.com/v/OFNfrkwcu48";
	$youtube_iframe='';
	if(preg_match('/(?:http:\/\/)?(?:www\.)?(?:youtube\.com|youtu\.be)\/(?:watch\?v=)?(.+)/i',$video,$matches)){
		$videoId=  preg_replace('/&.+/', '', $matches[1]);
		$iframe='<iframe width="'.$w.'" height="'.$h.'" src="http://www.youtube.com/embed/'.$videoId.'?autoplay='.$ap.'" frameborder="0" allowfullscreen></iframe>';
	}
	return $iframe;
}
function youtubeUrl2Embed($video,$ap=1){
	$videoId=youtubeVideoId($video);
	if($videoId)  return 'http://www.youtube.com/embed/'.$videoId.'?autoplay='.$ap;
	return $video;
}
function youtubeVideoId($video){
	if(preg_match('/(?:http:\/\/)?(?:www\.)?(?:youtube\.com|youtu\.be)\/(?:watch\?v=)?(.+)/i',$video,$matches)){
		$videoId=  preg_replace('/&.+/', '', $matches[1]);
		return $videoId;
	}
	return false;
}
function youtubeImagePlaceholder($video){
	$videoId=youtubeVideoId($video);
	//if($videoId)  return 'http://img.youtube.com/vi/'.$videoId.'/0.jpg';
	if($videoId)  return 'http://img.youtube.com/vi/'.$videoId.'/mqdefault.jpg';
	return NOIMAGE_URL;
	
}
 //<img class="ItemImage" src="http://img.youtube.com/vi/bQVoAWSP7k4/0.jpg" alt="video 8" />

/**
 * controlla l'esistemze di un record in una tabella di traduzione
 * 
 * @param mixed $id
 * @param integer $id_lingua
 * @param string $table
 * @param string $field
 */
function check_traslate_exists_indb($id,$id_lingua,$table,$field){
	$db=&Th::getDb();
	$varReturn=false;
	$query_rs_1 = "SELECT ".$field."   FROM ".$table." WHERE ".$field." = '".$id."' AND ID_LINGUA = ".$id_lingua." LIMIT 1";
	if(!$db->query($query_rs_1)){
		traceLog(__CLASS__.'::'.__METHOD__.' Errore query  SQLERROR:'.$db->errore,2);
		$varReturn =true;
	}else{
		if($db->numrows!=0) $varReturn=true;
	}
	return $varReturn;
 }
 
function check_data_exsist_in_db($id,$table,$field){ // nn rimuovere
	$db=&Th::getDb();
	$db->query("SELECT ".$field."   FROM ".$table." WHERE ".$field." = '$id' LIMIT 1");
	$res=$db->numrows;
	$db->freemem();
	return $res;
}
/**
 * prende dal db l�ordine dell'ultimo record inserito
 * 
 * @param $tabella
 */
function get_record_last_order($tabella){
	$db=&Th::getDb();
	$varReturn=1;
	$query_rs_1 = "SELECT ORDINE   
	FROM ".$tabella."
	ORDER BY ORDINE DESC LIMIT 1";
	$db->query($query_rs_1);
	if($db->numrows!=0){
		$rs=$db->setRecord();
		$varReturn=$rs[0]['ORDINE']+1;
	}
	return $varReturn;
 }
/***FINE FUNZIONI NUOVO DA SISTEMA CON CONTROLLER***/
	 
/**
 * simulazione kiamata asincrona
 * @param unknown_type $url
 * @param unknown_type $params
 */
function curl_post_async($url, $params=false){
	if(is_array($params)){
	    foreach ($params as $key => &$val) {
	      if (is_array($val)) $val = implode(',', $val);
	        $post_params[] = $key.'='.urlencode($val);
	    }
	     $post_string = implode('&', $post_params);
	}
    $parts=parse_url($url);
    $fp = fsockopen($parts['host'],isset($parts['port'])?$parts['port']:80,$errno, $errstr, 30);
    if(!$fp){
    	traceLog("Couldn't open a socket to ".$url." (".$errstr.")",2);
    	return false;
    }
    $out = "POST ".$parts['path']." HTTP/1.1\r\n";
    $out.= "Host: ".$parts['host']."\r\n";
    $out.= "Content-Type: application/x-www-form-urlencoded\r\n";
    $out.= "Content-Length: ".strlen($post_string)."\r\n";
    $out.= "Connection: Close\r\n\r\n";
    if (isset($post_string)) $out.= $post_string;
    fwrite($fp, $out);
    fclose($fp);
}
function urlSafe($url){
	return strip_tags(urldecode($url));
}
function setFormFieldStyle($validation,$key){
	if(isset($validation[$key]['status']) && !$validation[$key]['status']) return ' error';
	else return '';
}
    
function createBreadCrumbs($data,$fieldUrl='url',$fieldTitle='title'){
	$vr='';
	if(!is_array($data)) $data=array();
	$vr.='<div id="breadcrumbs" class="clearfix"><ul class="breadcrumb">';
	//$home=array('title'=>'Home','url'=>Th::getSiteUrl());
	$vr.='<li><a href="'.Th::getSiteUrl().'">Home</a>';
	//array_unshift($data,$home);
	$nv=count($data);
	//if($nv) $vr.='<span class="divider">'.htmlentities('/',ENT_COMPAT,__CONFIG_CHARSET).'</span>';
	foreach($data as $i=>$bc){
		if(!empty($bc[$fieldUrl]) || !empty($bc['url'])){
			$vr.='<li><a href="'.$bc[$fieldUrl].'">'.$bc[((isset($bc['title']))?'title':$fieldTitle)].'</a>';
		}else{
			$vr.='<li class="current">'.$bc[((isset($bc['title']))?'title':$fieldTitle)].'';
		}
// 		if($i<($nv-1))
// 			$vr.='<span class="divider">'.htmlentities('/',ENT_COMPAT,__CONFIG_CHARSET).'</span>';
		$vr.='</li>';
	}
	$vr.='</ul></div>';
	return $vr;
}
function addParamToUrl($url,$params){
	if(!$params) return $url;
	if(!is_array($params)) return $url;
	else{
		$isQuest=strpos($url,"?");
		if($isQuest ===false) $isQuest=0;
		if($isQuest){
			$url2=substr($url,0,$isQuest);
			$urlParams=substr($url,$isQuest+1);
			$url=$url2;
			$tmpParams=explode("&",$urlParams);
			foreach($tmpParams as $i=>$val){
				list($k,$v)=explode("=",$val);
				if(!isset($params[$k])) $params[$k]=$v;
			}
		}
		$i=0;
		foreach($params as $k=>$v){
			$sep=($i==0) ? '?' : '&';
			$url.=$sep.$k."=".$v;
			$i++;
		}
	}
	return $url;
}
function realCurrentDir(){
	return str_replace(basename($_SERVER['SCRIPT_FILENAME']),"",$_SERVER['SCRIPT_FILENAME']);
}

function clearforUrl($str){
	$str=str_replace(array('+',' ','.'),array('-'.'-','-'),$str);
	$str=preg_replace("/[^a-zA-Z0-9_\-]/i","",$str);
	return strtolower($str);
}
function strleft($s1, $s2) { return substr($s1, 0, strpos($s1, $s2)); }
function make_url_from($stripQueryString=false){
	$url=$_SERVER['REQUEST_URI'];
	if(!preg_match("/^http::\/\//",$url)){
		$url=preg_replace("/^\//","",$url);
		$url=Th::getSiteUrl()."/".$url;
	}
	if($stripQueryString){
		$res=preg_match("/(.*)[\?]/",$url,$b);
		if($res){
			if(isset($b[1]) && !empty($b[1])) return $b[1];
		}
		
	}
	return $url;
}
function make_ret_url(){
	$ur = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
	if (($_SERVER['REQUEST_METHOD'] == "GET") && ($_SERVER['QUERY_STRING'] != "")) {
		$ur.="?".$_SERVER['QUERY_STRING'];
	}
	return($ur);
}
function pr($var,$skipOutput=false){
	if($skipOutput)  return print_r($var,true);
	$st="<pre>\n";
	$st.=print_r($var,true);
	$st.="</pre>\n";
	echo $st;
}
function traceDebug($msg,$file=false){
	if(TRACE_DEBUG || $file){
		if(empty($msg)) return;
		$dbg = debug_backtrace();
		$dbg = $dbg[0];
		$f=explode("/",$dbg['file']);
		$n=count($f);
		$fi=(isset($f[($n-2)]))? $f[($n-2)]: '';
		$fi.=(isset($f[($n-1)]))? "/".$f[($n-1)]: '';
		$f=(!empty($file)) ? $file : "debug_%date%";
		if(is_array($msg)){
			traceLog(' ['.$fi.' '.$dbg['line'].']:'.print_r($msg,true),false,$f);
		}else{
			traceLog(' ['.$fi.' '.$dbg['line'].']:'.$msg,false,$f);
		}
	}
}
function makeSelectFromArray($data,$name,$selectValue=false,$id=''){
	if(!is_array($data) && !count($data)) return;
	$vr='<select name="'.$name.'" id="'.$id.'" class="form-control">';
	foreach($data as $i=>$val){
		$sel='';
		if($selectValue == $val['ID']) $sel=' selected ';
		$vr.='<option value="'.$val['ID'].'" '.$sel.'>'.$val['TITLE'].'</option>';
	}
	$vr.='</select>';	
	return $vr;
}
function makeCheckBoxesFromArray($data,$name,$selectValue=false){
	if(!is_array($data) && !count($data)) return;
	$vr='<ul id="catList">';
	foreach($data as $i=>$val){
		$sel='';
		if($selectValue){
			$pos = strpos($selectValue,$val['ID']);
			if($pos!==false) $sel=' checked ';
		}
		$vr.='<li><label for="'.$name.$val['ID'].'"><input value="'.$val['ID'].'" type="checkbox" name="'.$name.'[]" id="'.$name.$val['ID'].'" '.$sel.' /> '.$val['TITLE'].'</label></li>';
	}
	$vr.='</ul>';	
	return $vr;
}

function customColorsSelect($data,$selected=false,$onChange=false){
	if(!is_array($data) && !count($data)) return;
	$onch=($onChange) ? ' onchange="'.$onChange.'" ' : '';
	$vr='<select name="ecart-colors" id="ecart-colors" '.$onch.' class="selectpicker show-tick">';
	$vr.='<option value="0" data-available="0" data-incart="0">'.Msg::getText('ecomm lbl select color').'</option>';
	foreach($data as $i=>$val){
		$sel='';
		if($selected == $val['colorId']) $sel=' selected ';
		$vr.='<option value="'.$val['colorId'].'" '.$sel.'>'.$val['colorName'].'</option>';
	}
	$vr.='</select>';
	return $vr;
}
function customSizesSelect($data,$selected=false,$onChange=false){
	if(!is_array($data) && !count($data)) return;
	$onch=($onChange) ? ' onchange="'.$onChange.'" ' : '';
	$vr='<select name="ecart-sizes" id="ecart-sizes" '.$onch.' autocomplete="false" class="selectpicker show-tick">';
	$vr.='<option value="0" data-available="0" data-incart="0">'.Msg::getText('ecomm lbl select size').'</option>';
	foreach($data as $i=>$val){
		$sel='';
		if($selected == $val['sizeName']) $sel=' selected ';
		$vr.='<option value="'.$i.'" '.$sel.' data-available="'.$val['dispo'].'" data-incart="'.$val['qta'].'">'.$val['sizeName'].'</option>';
	}
	$vr.='</select>';
	return $vr;
}
function customVariantsSelect($data,$selected=false,$onChange=false){
	if(!is_array($data) && !count($data)) return;
	$onch=($onChange) ? ' onchange="'.$onChange.'" ' : '';
	$vr='<select name="ecart-variants" id="ecart-variants" '.$onch.' autocomplete="false" class="selectpicker show-tick">';
	//$vr.='<option value="0" data-available="0" data-incart="0">'.Msg::getText('ecomm lbl select size').'</option>';
	foreach($data as $i=>$val){
		$label='';
		if(!th_empty($val['MENULABEL'])) {
			$label=to_htm($val['MENULABEL'])." ";
		}
		$label.=(!th_empty($val['CAPABILITY'])) ? $val['CAPABILITY'] : to_htm($val['TITOLO']) ;
		$label.= '  '. to_e($val['PRICES']['prezzoValido']);
		$sel='';
		//if($selected == $val['ID_ARTICOLO']) $sel=' selected ';
		$vr.='<option value="'.$val['ID_ARTICOLO'].'" '.$sel.' data-price="'.to_e($val['PRICES']['prezzoValido']).'"  data-available="'.$val['DISPONIBILITA'].'" data-incart="'.$val['qta'].'">'.$label.'</option>';
	}
	$vr.='</select>';
	return $vr;
}

function customSelect($data,$name,$selected=false,$onChange='', $css=''){
	if(!is_array($data) && !count($data)) return;
	$onch=($onChange) ? ' onchange="'.$onChange.'" ' : '';
	$vr='<select name="'.$name.'" id="'.$name.'" '.$onch.' class="form-control '.$css.'">';
	foreach($data as $i=>$val){
		$sel='';
		if($selected == $i) $sel=' selected ';
		$vr.='<option value="'.$i.'" '.$sel.'>'.$val.'</option>';
	}
	$vr.='</select>';	
	return $vr;
}
function customTextfield($name,$selected=false,$css=''){
	$vr='<input type="text" name="'.$name.'" id="'.$name.'" class="form-control '.$css.'" value="'.to_field($selected).'" />';
	return $vr;
}
function selfURL() { 
	$s = empty($_SERVER["HTTPS"]) ? '' : ($_SERVER["HTTPS"] == "on") ? "s" : ""; 
	$protocol = strleft(strtolower($_SERVER["SERVER_PROTOCOL"]), "/").$s; 
	$port = ($_SERVER["SERVER_PORT"] == "80") ? "" : (":".$_SERVER["SERVER_PORT"]); return $protocol."://".$_SERVER['SERVER_NAME'].$port.$_SERVER['REQUEST_URI'];
} 
if(!function_exists('strleft')){
	function strleft($s1, $s2) { 
		return substr($s1, 0, strpos($s1, $s2)); 
	}
}
function to_js($t){
	return addslashes(stripslashes(str_replace(array("\t","\r","\n"),array("","",""),$t)));
}
function to_htm($t){
	if($t=='NULL') return '';
	return stripslashes($t);
}
function th_empty($t){
	if(!$t) return true;
	if(is_array($t)) return   empty($t);
	if(is_string($t))$t=trim(preg_replace("/^null$/i","",$t));
	return empty($t);
}
function clearToField($t,$textarea=false){
	return strip_tags(to_field($t,$textarea));
}
function clearFromField($t,$textarea=false){
	return strip_tags($t);
}
function to_field($t,$textarea=false){
	if($t=='NULL') return '';
	$t=stripslashes($t);
	$t=str_replace("\"","&quot;",$t);
	if($textarea){
		$t=str_replace("<br>","\n",$t);
		$t=str_replace("<br />","\n",$t);
	}
	return  strip_tags($t,'<myvideo>');
}
function stripJs($text){
	$search = array ("'<script[^>]*?>.*?</script>'si");
	$replace = array ("");
	$text = preg_replace($search, $replace, $text);
	return $text;
}

function jsonResponse($data){
	header('Cache-Control: no-cache, must-revalidate');
	header('Content-type: application/json');
	echo json_encode($data);
	exit;
}
function checkFileExixts($path){
	$vr=false;
	if(!is_dir($path) && file_exists($path)){
		$vr=true;
	}
	return $vr;
}


function calculateRemainingDays($date){
	$dbdate=strtotime($date);
	$now=time();
	return ceil((($dbdate-$now)/(60*60))/24);
}
/**
 * calcola la differenza in timestamp tra un valode dal db data-ora ed un intervallo di orre
 * @param unknown_type $dbDatetime
 * @param unknown_type $hoursIntervall
 */
function calculateTimestampDatatimeHoursDif($dbDatetime,$hoursIntervall){
	$dbdate=strtotime($dbDatetime);
	$elapse=strtotime('now -'.$hoursIntervall.' hours');
	return $dbdate-$elapse;
}
/**
 * calcola le ore da un intervallo calcolato di timestamp
 * @param $tmDiff
 */
function timestamDiffToHours($tmDiff){
	return number_format(($tmDiff/(60*60)), 2, ',', '');
}
function isdate($str){
#@TODO da finire 
	$r=explode('-',$str);
	if(count($r)==3){
		return true;
	}
	$r=explode('/',$str);
	if(count($r)==3){
		return true;
	}
	return false;
}
function dateToDb($date){
		$r=list($d,$m,$y)=explode('/',$date);
		return $y.'-'.$m.'-'.$d;
}
function timeselect($name,$selected=false,$id=false){
	$vr='<select name="'.$name.'"  id="'.$id.'" class="selectField">';
	for($i=0;$i<24;$i++){
		$j=($i<10) ? '0'.$i: $i;
		$value=$j.':00:00';
		$sel=($value==$selected) ? ' selected ': '';
		$vr.='<option value="'.$value.'" '.$sel.'>'.$value.'</oprion>';
	}
	$vr.='</select>';
	return $vr;
}


/**
 * sessi per ecomm
 * ritorna i sessi in lingua per ecomm
 * @return Array
 */
function sexNamesEcomm() {
	return explode("#",Msg::getText('ecomm lbl sexes'));
}

/**
 * sessi
 * ritorna i sessi in lingua
 * @return Array
 */
function sexNames() {
	return explode("#",Msg::getText('sexes'));
}
/**
 * sessi
 * ritorna i sessi in lingua
 * @return Array
 */
function getSex($index) {
	$list=sexNames();
	return (isset($list[$index])) ? $list[$index] : '&nbsp;' ;
}
/**
 * mesi dell'anno
 * ritorna i mesi dell'anno
 * @return Array
 */
function monthName() {
	return explode("#",Msg::getText('mesi'));
}
/**
 * mesi dell'anno short
 * ritorna i mesi dell'anno
 * @return Array
 */
function monthNameS() {
	return explode("#",Msg::getText('mesis'));
}
/**
 * giorgi della settimana
 * @return Array
 */
function weekDays() {
	return explode("#",Msg::getText('giorni'));
} 
function weekDaysS($langId=0) {
	return explode("#",Msg::getText('giornis'));
} 

function formatDatePart($a){
	$vr['date']=$a;
	if($a=='0000-00-00' || $a=='0000-00-00 00:00:00') return false;
	
	if(!is_validDbDate($a)){
		return false;
	}
	
	$e=explode(' ',$a);
	$vr['time']=(isset($e[1]))? $e[1]:'';
	$r=list($y,$m,$d)=explode('-',$a);
	$a=(mktime (0,0,0,$m ,$d,$y));
	$human_month=monthName();
	$human_month_s=monthNameS();
	
	$settimana=weekDays();
	$settimana_s=weekDaysS();
	$intday=date('w',$a);
	$day= date('d',$a);
	$intmonth=date('n',$a);

	$results=array(
		'day'=>$day,
		'weekDay'=>$settimana[$intday],
		'weekDays'=>$settimana_s[$intday],
		'month'=>$human_month[$intmonth],
		'months'=>$human_month_s[$intmonth],
		'intmonth'=> ($intmonth<10) ?  "0".$intmonth : $intmonth,
		'year'=>date('Y',$a),
		
	);
	return $results;
}

function formatDateComplete($a,$dayFormat=1,$monthFormat=1,$sep='/'){
	$vr['date']=$a;
	if($a=='0000-00-00' || $a=='0000-00-00 00:00:00') return $vr['date']='';
	if(!is_validDbDate($a)){return $vr;}
	$e=explode(' ',$a);
		$vr['time']=(isset($e[1]))? $e[1]:'';
		$r=list($y,$m,$d)=explode('-',$a);
		$a=(mktime (0,0,0,$m ,$d,$y));
		$human_month=monthName();
		$human_month_s=monthNameS();
		$settimana=weekDays();
		$settimana_s=weekDaysS();
		$intday=date('w',$a);
		if($dayFormat==1){
			$wday= $settimana_s[$intday];
		}else{
			$wday= $settimana[$intday];
		}
		$day= date('d',$a);
		$intmonth=date('n',$a);
		if($monthFormat==1){
			$month= $human_month[$intmonth];
		}else{
			$month= $human_month_s[$intmonth];
		}
		$year= date('Y',$a);
		//if($day<10) $day="0".$day;
		if($intmonth<10) $intmonth="0".$intmonth;
		$vr['date']=$day.$sep.$intmonth.$sep.$year;
		$vr['stringdate']=$wday." ".$day." ".$month." ".$year;

		return $vr;
}
function formatSimpleDate($d){
	$v=formatDateComplete($d);
	return $v['date'];
}
function formatSimpleDateTime($d){
	$v=formatDateComplete($d);
	return $v['date'].' '.$v['time'];
}
function getTimeFromdbdate($date){
	$t=explode(" ",$date);
	if(isset($t[1])) return trim($t[1]);
	return false;
}
function traceLog($msg='', $level=0, $file=FALSE, $dbg=FALSE) {
	if (!$msg) return;
	$logLEvel = array ('PHP Notice','PHP Warning','PHP Fatal error','PHP Critical');
	$logLEvelMail=2;
	if (!$dbg) {
		$dbg = debug_backtrace();
		$dbg = $dbg[0];
	}
	$message=(is_array($msg)) ? print_r($msg,true) : $msg;
	$xlevel=(isset($logLEvel[$level])) ? $logLEvel[$level] : '';
	$format = "[%date%] %xlevel%: %msg% in %file% on line %line% ";
	if($level!==false){
		$msg = str_replace(
			array('%date%','%xlevel%','%file%','%line%','%msg%'),
			array(date("d-M-Y H:i:s"),$xlevel,$dbg['file'],$dbg['line'],$message
		), $format);
	}else{
		$msg=$message;
	}

	$logFile=($file)? $file : LOGFILE_DEFAULT;
	$logFile = ($file)? str_replace("%date%",date('Ymd'),$logFile) :  str_replace("%date%",date(LOGFILE_DATEFORMAT),$logFile);
	$logFile = LOG_DIR .'/'. $logFile . '.log';

	$fp=@fopen($logFile,"a");
	
	if ($fp) {
		@fputs($fp, $msg."\n");
		fclose($fp);
	}
	if ($level>=$logLEvelMail && (APP_MODE=='prod' || !APP_LOCAL_SERVER)) {
		$mmsg = "Warning System: ".__CONFIG_SITE_NAME. ' - '.$xlevel." \n\n";
		$mmsg .= "Messaggio: ".$message."\n\n";
		$mmsg .= 'Hostname: '.$_SERVER['HTTP_HOST']."\n";
		$mmsg .= 'User agent: '.$_SERVER['HTTP_USER_AGENT']."\n";
		$mmsg .= 'Livello: '.$xlevel."\n";
		$mmsg .= 'Data: '.date('d/m/Y H:i:s')."\n";
		$q = getenv("QUERY_STRING");
		if ($q) $q = '?'.$q;
		$mmsg .= 'Url: http://'. $_SERVER['HTTP_HOST'].getenv("PATH_TRANSLATED").getenv("SCRIPT_NAME").$q."\n";
		$mmsg .= 'Method: '.getenv("REQUEST_METHOD")."\n";
		$mmsg .= 'File: '.$dbg['file']."\n";
		$mmsg .= 'Line: '.$dbg['line']."\n";
		$mmsg .= 'IP: '.getenv("REMOTE_ADDR")."\n\n";
		$mailer=new PHPMailer();
		$mailer->thUrl=Th::getSiteUrl();
		$mailer->thPath=__CONFIG_SITE_PATH;
		$mailer->From     = __CONFIG_SITE_TECHNICAL_CONTACT;
		$mailer->FromName = __CONFIG_SITE_NAME;
		$mailer->Subject =  "PHP ALERT: ".__CONFIG_SITE_NAME." - ".$xlevel;
		$mailer->MsgHTML(nl2br($mmsg));
		$mailer->AltBody=$mmsg;
		$mailer->AddAddress(__CONFIG_SITE_TECHNICAL_CONTACT);
		$res=$mailer->Send();		
	}
} 
function nospam_mail($mail,$oggetto="",$messaggio="",$immagine=""){
 // encode entire tag
  $url="mailto:".$mail;
  if($oggetto!=""){
  	$url.="&subject=".$oggetto."";
  }
   if($oggetto!=""){
  	$url.="&body=".$messaggio."";
  }
  $tag = "<a href=\"".$url."\">";
  $text=$mail;
 if($immagine!=""){
	$text=$immagine;
  }
  // convert string to array of ordered numbers between 1 and 2 billion
  $tag_a = array();
  $max_inc = 1000000000 / 256 / strlen($tag);
  $prefix = 1000000000;
  for ($i=0; $i<strlen($tag); ++$i) {
    $prefix += 256 * mt_rand(1,$max_inc);
    $tag_a[] = $prefix + ord($tag[$i]);
  }

  // permute the array
  for ($i=0; $icount($tag_a)-1; ++$i) {
    $j = mt_rand($i,count($tag_a)-1);
    $x = $tag_a[$i]; $tag_a[$i] = $tag_a[$j]; $tag_a[$j] = $x;
  }

  // javascript to create array, sort, and output tag
  $code = "a=Array(";
  $code .= $tag_a[0];
  for ($i=1; $icount($tag_a); ++$i)
    $code .= ",".$tag_a[$i];
  $code .= "); a.sort(); for (i=0; i<a.length; ++i)".
    " document.write(String.fromCharCode(a[i]%256));";

  // complete javascript program
  return 
    "<script type=\"text/javascript\">".
    $code.
    "</script>".
    $text.
    "<script type=\"text/javascript\">".
    "document.write(\"</a>\");".
    "</script>";
}
function clearTagForSeo($value){
	return htmlentities(strip_tags(html_entity_decode($value,ENT_COMPAT,'UTF-8')),ENT_COMPAT,__CONFIG_CHARSET,false);
}
function seo_generate_meta_description(&$arg,$max_length=SEO_META_DESCRIPTION_MAX_LENGTH,$html_preserve=false)
{
	$html_preserve = is_bool($html_preserve) ? $html_preserve : (bool) false;
	$content=strip_tags($arg);
	$words=preg_split("/[\s]+/", $content,-1,PREG_SPLIT_NO_EMPTY);
	$content='';
	while (count($words) && strlen($content)<$max_length)
	{
		$word=array_shift($words);
		$word=str_replace
		(
			array('  ', '"',  "\r\n", "\n", "\r")
			,array(' ', ' ',  ''    , ''  , '')
			,htmlentities(strip_tags(html_entity_decode($word,ENT_COMPAT,'UTF-8')),ENT_COMPAT,__CONFIG_CHARSET,false)
		);
		$content.= (strlen($word)>0) ? $word." " : "";
	}
	return (!$html_preserve) ? substr($content,0,-1) : str_replace('"',"'",substr(html_entity_decode($content,ENT_COMPAT,__CONFIG_CHARSET),0,-1));
}
function seo_generate_meta_keywords(&$keywords,$max_length=SEO_META_KEYWORDS_MAX_LENGTH,$max_word_length=SEO_META_KEYWORDS_MAX_WORD_LENGTH,$html_preserve=false)
{
	if (!is_array($keywords))
		return false;
	$result='';
	$inserted_keywords=array();
	reset($keywords);
	while (sizeof($keywords) && strlen($result)<$max_length)
	{
		$keyword=trim(array_pop($keywords));
		$keyword=str_replace(
			array('  ', '"',  "\r\n", "\n", "\r")
			,array(' ', ' ',  ''    , ''  , '')
			,(!$html_preserve) ? htmlentities(strip_tags(html_entity_decode($keyword,ENT_COMPAT,__CONFIG_CHARSET)),ENT_COMPAT,__CONFIG_CHARSET,false) : strip_tags(html_entity_decode($keyword,ENT_COMPAT,__CONFIG_CHARSET))
		);
		if (strlen($keyword)>0
			&& strlen($keyword)<=$max_word_length
			&& !isset($inserted_keywords[$keyword])){
			$result.="$keyword,";
			$inserted_keywords[$keyword]=1;
		}
	}
	return substr($result,0,-1);
}
	/**
	 * Genera un estratto da contenuto.
	 *
	 * Verr� generato un estratto con un numero totale di parole predefinito.
	 * Se il contenuto � maggiore di tale numero di parole, o del numero massimo
	 * di caratteri, sar� aggiunta l'entity (&hellip;) ovvero i tre puntini.
	 * Se invece il contenuto � inferiore ai limiti, sar� restituito com'�
	 *
	 * @param	string	$text			Testo da cui estrarre. (pu� essere html ed anche contenere shortcodes)
	 * @param	int		$maxwords		[opzionale]	Numero massimo di parole
	 * @param	int		$maxlength		[opzionale] Numero massimo di caratteri
	 * @param	boolean	$plainAscii		[opzionale] Se true, l'estratto viene convertito in plain ascii, altrimenti vengono mantenute le entities
	 * @return string 	L'estratto.
	 */
	function cutText($text,$maxwords=55,$maxlength=NULL,$plainAscii=false,$striptags=true,$permalink=false,$permalinkTarget=false,$data=array()) {
		if(th_empty($text)) return '';
		$helip=($plainAscii) ? ' [...]' : ' [&hellip;]';
		$target=($permalinkTarget) ? ' target="_blank"' : '';
	    if($permalink)  $helip = '<a href="'.$permalink.'" '. $target .'>'.$helip.'</a>';
		static $regexp,$regexp2,$linkexp;
		//$text=trim(str_replace("Foto di ","",$text));

		if(strpos($text," ")===false && strlen($text)>$maxlength){// non ci sono spazi
			return substr($text,0,$maxlength). $helip;
		}
		if (!is_string($regexp) || empty($regexp)){
			$regexp="/(\s|$|^|[_\,\.\+\?!\":;])/u";
			$regexp2='/[\w\d\J]/u';
			$linkexp="/([\w]+:\/\/)?([\d\w][-\d\w]{0,253}[\d\w]\.)+[\w]{2,4}(:[\d]+)?(\/([-+_~\.\d\w]|%[a-fA-f\d]{2,2})*)*(\?((&amp;|&)?([-+_~\.\d\w]|%[a-fA-f\d]{2,2})=?)*)?(#([-+_~\.\d\w]|%[a-fA-f\d]{2,2})*)?/u";
		}
		// Fix per uno strano fenomeno: preg_split non include \n o \r in \s
		$text=str_replace(array("\r\n","\n"),array(" "," "),$text);
		if($striptags) $text=strip_tags($text);
		$text=html_entity_decode($text,ENT_QUOTES,__CONFIG_CHARSET);
		$links=array();
		preg_match_all($linkexp,$text,$links);
		if (!empty($links)){
			$links=$links[0];
		}
		$text_segments=preg_split($linkexp,' '.$text);
		$words=array();
		foreach($text_segments as $segment) {
			$segment_words=preg_split($regexp,$segment, -1 , PREG_SPLIT_DELIM_CAPTURE + PREG_SPLIT_NO_EMPTY);
			while (sizeof($segment_words)){
				$words[]=array_shift($segment_words);
			}
			$words[]=array_shift($links);
		}

		$result='';
		$numwords=0;
		while (sizeof($words)>0
			&& ($maxlength==NULL || strlen($result.$words[0])<$maxlength)
			&& ($maxwords==NULL || $numwords<$maxwords)){
			$word=array_shift($words);
			if (''==trim($word,"\r\n\t ")){
				$result.=' ';
			}else{
				if (strlen($word)==1 && !preg_match($regexp2,$word)){
					if (substr($result,strlen($result)-1)==' '){
						$result=substr($result,0,strlen($result)-1);
					}
					if (in_array($word,array(',','.',':',';','?'),true)
						&& sizeof($words) && !in_array($words[0]{0},array(' ',',','.',':',';','?','/','\\','@','#','*','[',']','&'))) {
						$result.=$word.' ';
					}else{
						$result.=$word;
					}
				}else{
					$result.=$word;
					$numwords++;
				}
			}
		}
		if (!strlen($result) && $maxlength!==NULL && sizeof($words)) {
			$result=substr($words[0],0,$maxlength);
		}
		if (substr($result,0,1)==' '){
			$result=substr($result,1);
		}
		if (!$plainAscii) {
			$result=htmlentities($result,ENT_QUOTES,__CONFIG_CHARSET,false);
		}
		if(empty($result)){
			$strRet=$text;
			if(strlen($text)>$maxlength){// non ci sono spazi
				$strRet= substr($text,0,$maxlength).$helip;
			}
			return $strRet;
		}

		return !sizeof($words) ? $result : $result. $helip;
	}
function seoClearUrl($title){
	$t=$title;
	$t=trim($title);
	$t=preg_replace("/[^a-z0-9_\- ]/i","",$t);
	$t=trim($t);
	$t=preg_replace("/ /","-",$t);
	$i=0;
	while(strrpos($t,"-") == (strlen($t)-1)){
		$t=preg_replace("/-$/","",$t);
		$i++;
		if($i>100) return $t; // preveniamo i looping
	}
	return  strtolower($t);
}

#####################old

function crea_seme() {
    list($usec, $sec) = explode(' ', microtime());
    return (float) $sec + ((float) $usec * 100000);
}
function controlla_formato_e_mail($e_mail){
	return preg_match("/^([a-z0-9_\.-])+@(([a-z0-9_-])+\\.)+[a-z]{2,6}$/", trim($e_mail)); 
}
function controlla_formato_carattere($stringa){
	return preg_match("/^[a-z0-9_\.-]{0,}$/", $stringa); 
}

function replace_end($t){
	$t=utf8_encode(html_entity_decode($t));
	//$t=str_replace("&nbsp;"," ",$t);
	return $t;
}
function to_flash($t){
	$t=str_replace("<strong>","<b>",$t);
	$t=str_replace("</strong>","</b>",$t);
	
	$t=strip_tags($t,"b");
	//$t=replace_end($t);
	return urlencode(stripslashes($t));
}

function formatta_data_completa($a){
$human_month = array("error", "Gennaio", "Febbraio", "Marzo", "Aprile", "Maggio", "Giugno", "Luglio", "Agosto", "Settembre", "Ottobre", "Novembre", "Dicembre" ); 
$settimana   = array("Lun", "Mar", "Mer", "Gio", "Ven", "Sab", "Dom"); 
$arr_settimana   = array("Lunedi", "Martedi", "Mercoledi", "Giovedi", "Venerdi", "Sabato", "Domenica"); 
	$vr="";
	$intday=date('w',$a)-1;
	$wday= $arr_settimana[$intday];
	$day= date('d',$a);
	$month= $human_month[date('n',$a)];
	$year= date('Y',$a);
	$vr=$wday." ".$day." ".$month." ".$year;
	$vr=" ".$day." ".$month." ".$year;
	return $vr;
}
function to_e($e){
	if(is_numeric($e) && $e>0){
		return  "&euro; ".number_format($e, 2, ',', '.'); 
	}else{
		return "-";
	}
}
function numberToJs($n){
	return (!empty($n)) ? number_format($n, 2, '.', '') : 0;
}
function to_e_2($e){
	if(is_numeric($e) && $e>0){
		return  number_format($e, 2, ',', '.'); 
	}else{
		return "-";
	}
}
function arrotona_f($e){
	return  str_replace(",",".",number_format($e, 2, '.', '')); 
}

function controlla_formato_data($data)
{
	return preg_match("/[0-3]{1}[0-9]{1}[\/]{1}[0-1]{1}[0-9]{1}[\/][1-2]{1}[0-9]{3}/i", $data); 
}
function replace_x_db($s){
	$s = preg_replace("/'/","''",$s);
	return $s;
}
function controlla_scaduto($tdata2){
	list($anno, $mese, $giorno) = explode("-",$tdata2); 
	list($anno1, $mese1, $giorno1) = explode("-",prendi_data1()); 
	$giorni = ((mktime (0,0,0,$mese,$giorno,$anno) - mktime (0,0,0,$mese1,$giorno1,$anno1))/86400);
	//$pluraleosingolare = ((ceil(abs($giorni)>1)) or ceil($giorni)==0)?"giorni":"giorno";
	if($giorni>0){
		return false;
	}else{
		return true;
	}
}
function controllaStatoEnventoIncorso($form,$to){
	//stato 1 = in corso , 0 scaduto, 2 da venire
		$t=time();
		$tFrom=strtotime($form);
		$tTo=strtotime($to);
		$stato=1;
		if($tFrom>$t){
			$stato=2;
		}elseif(!empty($tFrom) && !empty($tTo)){
			if(($tFrom -$t)>=0 || ($tTo -$t)<=0) $stato=0;
		}
		return $stato;
}
function controlla_scadutodatetime($form,$to){
		$t=time();
		$tFrom=strtotime($form);
		$tTo=strtotime($to);
		$scaduto=false;
		if(!empty($tFrom) && !empty($tTo)){
			if(($tFrom -$t)>=0 || ($tTo -$t)<=0) $scaduto=true;
		}
		return $scaduto;
}
function prendi_data()
{
	$gg = date("d");
	$mm = date("m");
	$yy = date("Y");
	return  $yy . "/" . $mm . "/" .$gg ;
}
function prendi_data1()
{
	$gg = date("d");
	$mm = date("m");
	$yy = date("Y");
	return  $yy . "-" . $mm . "-" .$gg ;
}
//Funzione che, in base alla var data_sql, prende una data e la restituisce in formato gg/mm/aaaa
function formatodata($datain) {
 	$dataout =$datain;
	global $data_sql;
 	switch ($data_sql) {
     case 1:	//$data in formato gg/mm/aaaa
         $dataout = $datain;
         break;
     case 2:	//$data in formato aaaa/mm/gg
         $dataout = substr($datain, -2)."/".substr($datain, 5, 2)."/".substr($datain, 0, 4);
         break;
	case 3:	//$data in formato aaaa/mm/gg
         $dataout = substr($datain, -2)."/".substr($datain, 5, 2)."/".substr($datain, 0, 4);
         break;
 	}
	
 return ($dataout);
}
function aggiungi_data($numGiorni){
	return date('Y-m-d',strtotime(date('Y-m-d')." +$numGiorni days ")); 
}
function datediff($tdata1,$tdata2){
	list($giorno, $mese, $anno) = explode("/",$tdata1); 
	list($giorno1, $mese1, $anno1) = explode("/",$tdata2); 
	$giorni = ((mktime (0,0,0,$mese,$giorno,$anno) - mktime (0,0,0,$mese1,$giorno1,$anno1))/86400);
	$pluraleosingolare = ((ceil(abs($giorni)>1)) or ceil($giorni)==0)?"giorni":"giorno";
	return $giorni;
}

function formatodata_ora($datain,$timeTo=false) {
	$vr="";
	$vr.=substr($datain, 8, 2)."/";// giorno
	$vr.=substr($datain, 5, 2)."/";// mese
	$vr.=substr($datain, 0, 4)." ";//$anno
	$vr.=substr($datain, -8);// ora
 	return ($vr);
}

function GetSQLValueString2($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") 
{
  $theValue = (!get_magic_quotes_gpc()) ? addslashes($theValue) : $theValue;
  switch ($theType) {
    case "text":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
	  //$theValue = html_entity_decode($theValue);
	   //$theValue = traduci($theValue);

      break;    
    case "long":
    case "int":
      $theValue = ($theValue != "") ? intval($theValue) : "0";
      break;
    case "double":
      $theValue = ($theValue != "") ? "'" . doubleval($theValue) . "'" : "0";
      break;
    case "date":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;
    case "defined":
      $theValue = ($theValue != "") ? $theDefinedValue : $theNotDefinedValue;
      break;
  }
  return $theValue;
}
function traduci($text){
	$trans_tbl = get_html_translation_table(HTML_ENTITIES);
       foreach($trans_tbl as $k => $v)
       {
           $ttr[$v] = utf8_encode($k);
       }
   
       return  strtr($text, $ttr); 
	  //return htmlentities($text);
	  

}

function fra2parole($frase,$prima,$seconda,$index,$reverse=0){
	if (strlen($frase)&&strlen($prima)&&strlen($seconda)&&$index>=0){
	$inizio=strpos($frase,$prima,$index)+strlen($prima);
	if ($inizio!==false){
			   if ($reverse){
				$fine=strrpos($frase,$seconda);
			   }else{
				$fine=strpos($frase,$seconda,$inizio);
			   }                     
			   if ($fine!==false){
				   $lungh=$fine-$inizio;
				   return (substr($frase,$inizio,$lungh));
			   }else {
				return false; //$seconda non trovata
			   }
	}else {
	   return false; //$prima non trovata
	}
	}else{
		return false; //parametri insuff
	}
}
function get_editor_flash_param($str){
	$str=str_replace(">","",$str);
	$str=str_replace("</embed>","",$str);
	$pieces = explode(" ", $str);
	return $pieces;

}

function scrivi_flash_from_editor($tt){
		$str_movie="
		<div id=\"##ID##\"></div>
				<script type=\"text/javascript\">\r\n
				var so = new SWFObject('##MOVIE##', '##ID##', '##W##', '##H##', '8', '#FFFFFF');
				so.addParam(\"scale\", \"noscale\");
				so.addParam(\"wmode\", \"transparent\");
				so.write(\"##ID##\");
			</script>
		";
	$i=0;
	$stringa=fra2parole($tt,'<embed','</embed>',$index);
		while($stringa){
			$arr_param[$i]=get_editor_flash_param($stringa);
			for($x=0;$x<count($arr_param[$i]);$x++){			
				$pos=strripos($arr_param[$i][$x], 'width');
				if ($pos === false) {
				}else{
					$w=str_replace('width=','',$arr_param[$i][$x]);
					$w=str_replace('"','',$w);
				}
				$pos=strripos($arr_param[$i][$x], 'height');
				if ($pos === false) {
				}else{
					$h=str_replace('height=','',$arr_param[$i][$x]);
					$h=str_replace('"','',$h);
				}
				$pos=strripos($arr_param[$i][$x], 'src');
				if ($pos === false) {
				}else{
					$src=str_replace('src=','',$arr_param[$i][$x]);
					$src=str_replace('"','',$src);
				}
			}
			  $str_to_replace="<embed".$stringa."</embed>";
			  $replace_strimg=$str_movie;
			  $replace_strimg=str_replace("##ID##","m_".$i,$replace_strimg);
			  $replace_strimg=str_replace("##MOVIE##",$src,$replace_strimg);
			  $replace_strimg=str_replace("##W##",$w,$replace_strimg);
			  $replace_strimg=str_replace("##H##",$h,$replace_strimg);
			  $tt=str_replace($str_to_replace,$replace_strimg,$tt);
			  $stringa=fra2parole($tt,'<embed','</embed>',$index);
			  if($i==0){
			  //echo $tt;
			  }
			 $i++;
	}
		return  $tt;
		
}
function GetSQLValueString1($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "")  // per i campi che nn sno dell'editor
{

  $theValue = (!get_magic_quotes_gpc()) ? addslashes($theValue) : $theValue;
  switch ($theType) {
    case "text":
	
	$theValue =addslashes($theValue);
	//$theValue=str_replace("/","\/",$theValue);
	//echo  $theValue."<br>";
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
	 // $theValue =traduci($theValue );
	 
	  //$theValue=str_replace("\"","&quot;",$theValue);
      break;    
    case "long":
    case "int":
      $theValue = ($theValue != "") ? intval($theValue) : "0";
      break;
    case "double":
      $theValue = ($theValue != "") ? "'" . doubleval($theValue) . "'" : "0";
      break;
    case "date":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;
    case "defined":
      $theValue = ($theValue != "") ? $theDefinedValue : $theNotDefinedValue;
      break;
  }
  return $theValue;
}

function chech_user_exist($campo,$valore,$tabella){// controllo esistenza di indirizzo mail o username
	$sql =Th::getDb();
	
		$query = "SELECT ".$campo."   FROM ".$tabella."  WHERE ".$campo."  = '" . $valore . "' ";
		$sql->query($query);
		 if ($sql->numrows!=0) {
			$varReturn=true;
		}else{
			$varReturn=false;
		}
	return $varReturn;

 }
function is_validDbDate($date){
	return preg_match("/^[0-9]{4}\-[0-9]{1,2}\-[0-9]{1,2}.*?$/",$date);
}

function get_user_mail($id){
$sql =Th::getDb();

	$query = "SELECT E_MAIL  FROM utenti_catalogo  WHERE ID_UTENTE = '".$id."'";
	$sql->query($query);
	 if ($sql->numrows!=0) {
		$sql->inrecord(0);
		return  $sql->record["E_MAIL"];
	}else{
		return "";
	}
}




function get_nav_cat($cat_1,$cat_2,$cat_3){
	global $cat_1,$cat_2,$cat_3,$id_lang;
	return "";
}

 
function check_order_exist($id_o){// controllo esistenza di indirizzo mail o username
	$sql =Th::getDb();
	
	$vr=0;// ordine non trovato 
	$query = "SELECT ID_ORDINE,CONFERMATO,ANNULLATO  FROM ordini_id WHERE ID_ORDINE = '".$id_o."'";
	$sql->query($query);
		 if ($sql->numrows!=0) {
		 	$sql->inrecord(0);
			$vr=1;//ordine presente
			if($sql->record["CONFERMATO"]==1){
				$vr=2;/// ordine gi� confermato
			}
			if($sql->record["ANNULLATO"]==1){
				$vr=0;/// ordine gi� confermato
			}
		}
	return $vr;
 }
 


 
function get_last_id ($tabella,$campoID){
	$sql =Th::getDb();
	
	$varReturn=0;
	$query_rs_1 = "SELECT MAX(".$campoID.") As tlast   FROM " .$tabella ." ";
	$sql->query($query_rs_1);
	if($sql->numrows!=0){
		$sql->inrecord(0);
		$varReturn=$sql->record['tlast'];
	}
	return $varReturn;
 }
 
 function chech_exist ($tabella,$campoID,$id,$id_lingua){
	$sql =Th::getDb();
	
	$varReturn=false;
	$query_rs_1 = "SELECT ".$campoID."   FROM " .$tabella ." WHERE ".$campoID." = ".$id." AND ID_LINGUA = ".$id_lingua."";
	$sql->query($query_rs_1);
	if($sql->numrows!=0){
		$varReturn=true;
	}
	return $varReturn;
 }
 
function chech_exist_simple ($tabella,$campoID,$id){
	$sql =Th::getDb();
	
	$varReturn=false;
	$query_rs_1 = "SELECT ".$campoID."   FROM " .$tabella ." WHERE ".$campoID." = '".$id."'";
	$sql->query($query_rs_1);
	if($sql->numrows!=0){
		$varReturn=true;
	}
	return $varReturn;
 }
  function get_last_order ($tabella){
	$sql =Th::getDb();
	
	$varReturn=1;
	$query_rs_1 = "SELECT ORDINE   FROM " .$tabella ." ORDER BY ORDINE DESC LIMIT 1";
	$sql->query($query_rs_1);
	if($sql->numrows!=0){
		$sql->inrecord(0);
		
		$varReturn=$sql->record['ORDINE']+1;
	}
	return $varReturn;
 }
  function get_last_order_sezioni ($comportamento){
	$sql =Th::getDb();
	
	$varReturn=1;
	$query_rs_1 = "SELECT ORDINE   FROM sezioni WHERE BEHAVIOR = '".$comportamento."' ORDER BY ORDINE DESC LIMIT 1";
	$sql->query($query_rs_1);
	if($sql->numrows!=0){
		$sql->inrecord(0);
		
		$varReturn=$sql->record['ORDINE']+1;
	}
	return $varReturn;
 }
 
 function chech_exist_text_sezioni ($campo,$valore,$comportamento){
	$sql =Th::getDb();
	$varReturn=false;
	$query_rs_1 = "SELECT a.".$campo."   
	FROM sezioni_testo AS a, sezioni AS b
	 WHERE ".$campo." = '".$valore."' AND b.BEHAVIOR = '".$comportamento."' 
	 GROUP BY a.ID_SEZIONE 
	 LIMIT 1";
	$sql->query($query_rs_1);
	if($sql->numrows!=0){
		$varReturn=true;
	}
	return $varReturn;
 }
  function chech_exist_text ($tabella,$campo,$valore){
	$sql =Th::getDb();
	
	$varReturn=false;
	$query_rs_1 = "SELECT ".$campo."   FROM " .$tabella ." WHERE ".$campo." = '".$valore."' LIMIT 1";
	$sql->query($query_rs_1);
	if($sql->numrows!=0){
		$varReturn=true;
	}
	return $varReturn;
 }
function prendi_testo_lingua ($tabella,$campo_testo,$campoID,$id,$id_lingua){
	$sql =Th::getDb();
	$varReturn="";
	$query_rs_1 = "select ".$campoID.", ".$campo_testo."   FROM " .$tabella ." WHERE ".$campoID." = ".$id." AND ID_LINGUA = ".$id_lingua."";
	$sql->query($query_rs_1);
	if ($sql->numrows!=0){
		$sql->inrecord(0);
		$varReturn=trim(stripslashes($sql->record[$campo_testo]));
	}
	return (($varReturn)=='NULL') ? '': $varReturn;
 }
function set_last_access($id_utente){
	$sql =Th::getDb();
	
	$thtime=date("H:i:s");
	$tdatetime=prendi_data1()." " .$thtime ;
	$query_rs_1="UPDATE  utenti_catalogo  SET LAST_ACCESS  = NOW(), TOTAL_ACCESS = TOTAL_ACCESS+1 where ID_UTENTE='". $id_utente."'";
	$sql->query($query_rs_1);
	$query_rs_1="INSERT INTO utenti_catalogo_log SET ";
		$query_rs_1.="ID_UTENTE = ".$id_utente.",";
		$query_rs_1.="ID_ORDINE= '".$_SESSION['id_ordini']."',";
		$query_rs_1.="ID_SESSIONE= '".session_id()."',";
		$query_rs_1.="DATA= CURDATE(),";
		$query_rs_1.="ORA_IN= '".$thtime."',";
		$query_rs_1.="ORA_OUT= '00:00:00',";
		$query_rs_1.="IP  = '".$_SERVER['REMOTE_ADDR']."'";
	$sql->query($query_rs_1);

	$_SESSION['log_id']=$sql->lastoid;
	
 }
 
 function get_user_last_access($id){
$sql =Th::getDb();

	$query = "SELECT DATA, ORA_IN  FROM utenti_catalogo_log   
	WHERE ID_UTENTE = '".$id."' AND ID_SESSIONE <> '".session_id()."' 
	ORDER BY ID DESC LIMIT 1";
	$sql->query($query);
	 if ($sql->numrows!=0) {
		$sql->inrecord(0);
		return  $sql->record["DATA"]." ".$sql->record["ORA_IN"] ;
	}else{
		return "";
	}
}


 function set_log_out($id_log){
	if($id_log>0){
		$sql =Th::getDb();
		
		$thtime=date("H:i:s");
		$query_rs_1="UPDATE  utenti_catalogo_log  SET ORA_OUT  = '".$thtime."'where ID='". $id_log."'";
		$sql->query($query_rs_1);
	}
 }

 
 
 function get_file_type($file){
 $icons = array(
			
			// Microsoft Office
			'doc' => array('doc', 'Word Document'),
			'xls' => array('xls', 'Excel Spreadsheet'),
			'ppt' => array('ppt', 'PowerPoint Presentation'),
			'pps' => array('ppt', 'PowerPoint Presentation'),
			'pot' => array('ppt', 'PowerPoint Presentation'),
		
			'mdb' => array('access', 'Access Database'),
			'vsd' => array('visio', 'Visio Document'),
			'rtf' => array('rtf', 'RTF File'),
		
			// XML
			'htm' => array('htm', 'HTML Document'),
			'html' => array('htm', 'HTML Document'),
			'xml' => array('xml', 'XML Document'),
		
			 // Images
			'jpg' => array('image', 'JPEG Image'),
			'jpe' => array('image', 'JPEG Image'),
			'jpeg' => array('image', 'JPEG Image'),
			'gif' => array('image', 'GIF Image'),
			'bmp' => array('image', 'Windows Bitmap Image'),
			'png' => array('image', 'PNG Image'),
			'tif' => array('image', 'TIFF Image'),
			'tiff' => array('image', 'TIFF Image'),
			
			// Audio
			'mp3' => array('audio', 'MP3 Audio'),
			'wma' => array('audio', 'WMA Audio'),
			'mid' => array('audio', 'MIDI Sequence'),
			'midi' => array('audio', 'MIDI Sequence'),
			'rmi' => array('audio', 'MIDI Sequence'),
			'au' => array('audio', 'AU Sound'),
			'snd' => array('audio', 'AU Sound'),
		
			// Video
			'mpeg' => array('video', 'MPEG Video'),
			'mpg' => array('video', 'MPEG Video'),
			'mpe' => array('video', 'MPEG Video'),
			'wmv' => array('video', 'Windows Media File'),
			'avi' => array('video', 'AVI Video'),
			
			// Archives
			'zip' => array('zip', 'ZIP Archive'),
			'rar' => array('zip', 'RAR Archive'),
			'cab' => array('zip', 'CAB Archive'),
			'gz' => array('zip', 'GZIP Archive'),
			'tar' => array('zip', 'TAR Archive'),
			'zip' => array('zip', 'ZIP Archive'),
			
			// OpenOffice
			'sdw' => array('oo-write', 'OpenOffice Writer document'),
			'sda' => array('oo-draw', 'OpenOffice Draw document'),
			'sdc' => array('oo-calc', 'OpenOffice Calc spreadsheet'),
			'sdd' => array('oo-impress', 'OpenOffice Impress presentation'),
			'sdp' => array('oo-impress', 'OpenOffice Impress presentation'),
		
			// Others
			'txt' => array('txt', 'Text Document'),	
			'js' => array('js', 'Javascript Document'),
			'dll' => array('binary', 'Binary File'),
			'pdf' => array('pdf', 'Adobe Acrobat Document'),
			'php' => array('php', 'PHP Script'),
			'ps' => array('ps', 'Postscript File'),
			'dvi' => array('dvi', 'DVI File'),
			'swf' => array('swf', 'Flash'),
			'chm' => array('chm', 'Compiled HTML Help'),

			// Unkown
			'default' => array('txt', 'Unkown Document'),
			);
			$extens=str_replace(".","",get_file_extension($file));
			return  $icons[$extens][1];
 }
	function get_img_size($file) {
		return $img_size = getimagesize($file);
	}
function get_file_extension($from_file) {
	$ext = strtolower(strrchr($from_file,"."));
	return $ext;
}
function scrivi_icona($file){
	$extens=str_replace(".","",get_file_extension($file));
	
	if(strtoupper($extens)=="DOC"){
		echo "ico_doc.gif";
	}
	if(strtoupper($extens)=="RTF"){
		echo "ico_doc.gif";
	}
	if(strtoupper($extens)=="ZIP"){
		echo "ico_zip.gif";
	}
	if(strtoupper($extens)=="PDF"){
		echo "ico_pdf.gif";
	}
	if(strtoupper($extens)=="DWG"){
		echo "ico_dwg.gif";
	}
}
function get_file_size($path){
	$vr="";
	$lbl="";
	//if(file_exists($path)){
		$fsize=@filesize($path);
		if($fsize<1024){
			$lbl=" Byte";
			
		}elseif($fsize>1024 && $fsize <1048576){
			$lbl=" Kb";
			$fsize=number_format(($fsize/1024), 0, ',', '');
		}else{
			$lbl=" Mb";
			$fsize=number_format((($fsize/1024)/1024), 3, ',', '');
		}
		$vr=$fsize;
	//}
//number_format((filesize($path)/1024), 2, '.', '')

return $vr.$lbl;


}
 function scrivi_icona_onof($stato){
	$vr="unknow";
	if($stato==1){
		$vr="<img src=\"../imgs/icone/ico_visibile.png\" width=\"32\" height=\"32\" style=\"width:32;height:32\" alt=\"Modifica stato\" hspace=\"5\" border=\"0\" align=\"absmiddle\">";
	}elseif($stato==0){
		$vr="<img src=\"../imgs/icone/ico_nn_visibile.png\" width=\"32\" height=\"32\" style=\"width:32;height:32\" alt=\"Modifica stato\" hspace=\"5\" border=\"0\" align=\"absmiddle\">";
	}
	return $vr;
}


class easyExcell{
	function xlsBOF() {
	    echo pack("ssssss", 0x809, 0x8, 0x0, 0x10, 0x0, 0x0);  
	    return;
	}
	function xlsEOF() {
	    echo pack("ss", 0x0A, 0x00);
	    return;
	}
	function xlsWriteNumber($Row, $Col, $Value) {
	    echo pack("sssss", 0x203, 14, $Row, $Col, 0x0);
	    echo pack("d", $Value);
	    return;
	}
	
	function xlsWriteLabel($Row, $Col, $Value ) {
	    $L = strlen($Value);
	    echo pack("ssssss", 0x204, 8 + $L, $Row, $Col, 0x0, $L);
	    echo $Value;
		return;
	} 
	function writeHeader($filename){
		header("Pragma: public");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Content-Type: application/force-download");
		header("Content-Type: application/octet-stream");
		header("Content-Type: application/download");
		header("Content-Disposition: attachment;filename=".$filename); 
		header("Content-Transfer-Encoding: binary ");
	}
}

    function get_mimetype($value='') {

        $ct['htm'] = 'text/html';
        $ct['html'] = 'text/html';
        $ct['txt'] = 'text/plain';
        $ct['asc'] = 'text/plain';
        $ct['bmp'] = 'image/bmp';
        $ct['gif'] = 'image/gif';
        $ct['jpeg'] = 'image/jpeg';
        $ct['jpg'] = 'image/jpeg';
        $ct['jpe'] = 'image/jpeg';
        $ct['png'] = 'image/png';
        $ct['ico'] = 'image/vnd.microsoft.icon';
        $ct['mpeg'] = 'video/mpeg';
        $ct['mpg'] = 'video/mpeg';
        $ct['mpe'] = 'video/mpeg';
        $ct['qt'] = 'video/quicktime';
        $ct['mov'] = 'video/quicktime';
        $ct['avi']  = 'video/x-msvideo';
        $ct['wmv'] = 'video/x-ms-wmv';
        $ct['mp2'] = 'audio/mpeg';
        $ct['mp3'] = 'audio/mpeg';
        $ct['rm'] = 'audio/x-pn-realaudio';
        $ct['ram'] = 'audio/x-pn-realaudio';
        $ct['rpm'] = 'audio/x-pn-realaudio-plugin';
        $ct['ra'] = 'audio/x-realaudio';
        $ct['wav'] = 'audio/x-wav';
        $ct['css'] = 'text/css';
        $ct['zip'] = 'application/zip';
        $ct['pdf'] = 'application/pdf';
        $ct['doc'] = 'application/msword';
        $ct['bin'] = 'application/octet-stream';
        $ct['exe'] = 'application/octet-stream';
        $ct['class']= 'application/octet-stream';
        $ct['dll'] = 'application/octet-stream';
        $ct['xls'] = 'application/vnd.ms-excel';
        $ct['ppt'] = 'application/vnd.ms-powerpoint';
        $ct['wbxml']= 'application/vnd.wap.wbxml';
        $ct['wmlc'] = 'application/vnd.wap.wmlc';
        $ct['wmlsc']= 'application/vnd.wap.wmlscriptc';
        $ct['dvi'] = 'application/x-dvi';
        $ct['spl'] = 'application/x-futuresplash';
        $ct['gtar'] = 'application/x-gtar';
        $ct['gzip'] = 'application/x-gzip';
        $ct['js'] = 'application/x-javascript';
        $ct['swf'] = 'application/x-shockwave-flash';
        $ct['tar'] = 'application/x-tar';
        $ct['xhtml']= 'application/xhtml+xml';
        $ct['au'] = 'audio/basic';
        $ct['snd'] = 'audio/basic';
        $ct['midi'] = 'audio/midi';
        $ct['mid'] = 'audio/midi';
        $ct['m3u'] = 'audio/x-mpegurl';
        $ct['tiff'] = 'image/tiff';
        $ct['tif'] = 'image/tiff';
        $ct['rtf'] = 'text/rtf';
        $ct['wml'] = 'text/vnd.wap.wml';
        $ct['wmls'] = 'text/vnd.wap.wmlscript';
        $ct['xsl'] = 'text/xml';
        $ct['xml'] = 'text/xml';

        $extension = array_pop(explode('.',$value));
        if (!$type = $ct[strtolower($extension)]) {

            $type = 'text/html';
        }
        return $type;
    }
    function downloadFile($fileName,$path){
    	$file_path=@realpath($path).'/'.$fileName;
    	traceDebug($file_path);
		$file_mime = @get_mimetype($fileName);
		if(!$file_mime)$file_mime = "application/octet-stream";
		header("Content-Type: $file_mime");
		header("Content-Length: " . @filesize($file_path));
		$agent = $_SERVER["HTTP_USER_AGENT"];
		if( is_int(strpos($agent, "MSIE")) ){
		    $fn = preg_replace('/[:\\x5c\\/*?"<>|]/', '_', $fileName);
		    header("Content-Disposition: attachment; filename=". rawurlencode($fn));
		
		} else if( is_int(strpos($agent, "Gecko")) ){
		    header("Content-Disposition: attachment; filename*=UTF-8''" . rawurlencode($fileName));
		
		} else if( is_int(strpos($agent, "Opera")) ) {
		    $fn = preg_replace('/[:\\x5c\\/{?]/', '_', $fileName);
		    header("Content-Disposition: attachment; filename*=UTF-8''". rawurlencode($fn));
		} else {
		    $fn = mb_convert_encoding($fileName, "US-ASCII", "UTF-8");
		    $fn = (string) str_replace("\\", "\\\\", $fn);
		    $fn = (string) str_replace("\"", "\\\"", $fn);
		    header("Content-Disposition: attachment; filename=\"$fn\"");
		}
		@readfile($file_path);

    }
function createRandomPassword($length=8) {
    $chars = "abcdefghijkmnopqrstuvwxyz023456789";
    srand((double)microtime()*1000000);
    $i = 0;
    $pass = '' ;
    while ($i <= $length) {
        $num = rand() % 33;
        $tmp = substr($chars, $num, 1);
        $pass = $pass . $tmp;
        $i++;
    }
    return $pass;
}
?>