<!DOCTYPE html>
<html lang="" class="html-<?php echo $this->getAreaId()?>">
<head>
<meta charset="<?=__CONFIG_CHARSET?>" />
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo __CONFIG_CHARSET?>" />
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<!-- <link href="http://fonts.googleapis.com/css?family=Gafata" rel="stylesheet" type="text/css" /> -->
<?

$this->loadCss(Config::read('fe.asset.css'));
$this->loadCss($this->Css);

// $assetCss=Config::read('fe.asset.css');
// foreach($assetCss as $i =>$file){
// 	$this->cssLoad($file);
// }

// // css condizionale per ie
// if(isset($this->cssIeFix) && count($this->cssIeFix) && is_array($this->cssIeFix) && !$this->ajax){
// 	foreach($this->cssIeFix as $csfK=>$csfFile){
// 		echo "<!--[$csfK]>\n";
// 			if(is_array($csfFile)){
// 				for($cssi=0;$cssi<count($csfFile);$cssi++){
// 					echo "<link rel=\"stylesheet\" href=\"".Th::getSiteUrl().'/templates/css/cssmin.php?f='.($csfFile[$cssi])."\" type=\"text/css\" />\n";
// 				}
// 			}else{
// 				echo "<link rel=\"stylesheet\" href=\"".Th::getSiteUrl().'/templates/css/cssmin.php?f='.($csfFile)."\" type=\"text/css\" />\n";
// 			}
// 		echo "<![endif]-->\n";
// 	}
// }

#########SEO
// 	$default_keys=Config::getConf('SEO_DEFAULT_META_KEY');
// 	$default_desc =Config::getConf('SEO_DEFAULT_META_DESCRIPTION');
// 	if((isset($this->Parameters['extra']['seo_keywords']) && is_string($this->Parameters['extra']['seo_keywords'])) && !empty($this->Parameters['extra']['seo_keywords'])){
// 		$default_keys=str_replace(
// 			array('  ', '"',  "\r\n", "\n", "\r")
// 			,array(' ', ' ',  ''    , ''  , '')
// 			,(strip_tags(html_entity_decode($this->Parameters['extra']['seo_keywords'],ENT_COMPAT,__CONFIG_CHARSET)))
// 		);
// 	}
// $meta_desc = (!empty($this->Parameters['extra']['seo_description']))
// 					? seo_generate_meta_description($this->Parameters['extra']['seo_description'],SEO_META_DESCRIPTION_MAX_LENGTH,true)
// 					: $default_desc;
// $chechkey=true;
// if(is_array($this->Parameters['extra']['seo_keywords'])){// fix di un baco nelle pagine che mettono l'array [0] a vuoto se il meta_key nn ci sono
// 	$clone=$this->Parameters['extra']['seo_keywords'];
// 	$chechkey=array_shift($clone);
// }
// $meta_keys = (isset($this->Parameters['extra']['seo_keywords']) && is_array($this->Parameters['extra']['seo_keywords']) && count($this->Parameters['extra']['seo_keywords']) && $chechkey)
// 					? seo_generate_meta_keywords(array_reverse($this->Parameters['extra']['seo_keywords']),SEO_META_KEYWORDS_MAX_LENGTH,SEO_META_KEYWORDS_MAX_WORD_LENGTH,true)
// 					: $default_keys;
// $super_title= ($this->pageTitle) ? html_entity_decode($this->pageTitle,ENT_COMPAT,__CONFIG_CHARSET) : Config::getConf('SEO_DEFAULT_TITLE');

$super_title = $this->get('title');

?>
<script type="text/javascript" src="/public/js/jquery.js"></script>
<title><? echo stripslashes($super_title)?></title>




<meta name="google-site-verification" content="" />
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->
</head>
<body  id="page-top" class=" <?php echo $this->getAreaId()?> <?php echo $this->getLang()?>">
