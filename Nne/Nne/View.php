<?php
/**
 * oh this is my template engine.
 * 
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
namespace Nne;
require VENDOR_PATH.'lessc.php';

class View extends \Slim\View{
	protected $_templatesPath=array();
	protected $_lessPath=array();
	protected $Boxes=array();
	/** queued css files */
	protected $Css=array();
	/** queued js files */
	protected $Js=array();
	private $app = null;
	private $_templateExtension = '.php';
	
	private $chars;
	private $translator;
	protected $Html = null;
	protected $Controller  = null;
	protected $_Less = null;
	protected $domReady = array();
	protected $_pathPrefix = ''; // prefix for example admin
	
	
	protected $_streamContent = array(
			'first'=>'',
			'beforeContent'=>'',
			'content'=>'',
			'afterContent'=>'',
			'last'=>'',
	);

	public function __construct(\Nne\NneSlim $app, \Nne\Libs\Translator $translator) {
		parent::__construct();
		$this->app = $app;
		$this->translator = $translator;
		$chars = get_html_translation_table(HTML_ENTITIES);
		$remove = get_html_translation_table(HTML_SPECIALCHARS);
		unset($remove['&']);
		$this->chars = array_diff($chars, $remove);
		if($this->app->isAdmin()){
			$this->_pathPrefix = 'admin';
			//$this->setTemplatesDirectory($this->app->config('templates.admin.path'));
		}
	}
	
	/*moltiluingual*/
	public function getLang() {
		return $this->getData('lang');
	}
	public function setLang($lang) {
		$this->setData('lang', $lang);
	}
	public function setAvailableLangs($availableLangs) {
		$this->setData('availableLangs', $availableLangs);
	}
	public function setPathInfo($pathInfo) {
		$this->setData('pathInfo', $pathInfo);
	}
	public function urlFor_($name, $params = array()) {
		return $this->app->urlFor($name, $params);
	}
	public function url($url, $lang = null) {
		return sprintf('/%s%s', ($lang != null) ? $lang : $this->getLang(), $url);
	}
	public function tr($key, $replacements = array()) {
		return $this->htmlEntitiesButTags($this->translator->translate($this->getLang(), $key, $replacements));
	}
	public function __($key, $replacements = array()) {
		return $this->tr($this->translator->translate($this->getLang(), $key, $replacements));
	}
	private function htmlEntitiesButTags($txt) {
		return strtr($txt, $this->chars);
	}
	/*moltiluingual*/
	
	
	/* head method*/
	/**
	 * 
	 * get current site area to set html and body calls 
	 *
	 * @return string
	 */
	public function getAreaId(){
		if(!is_null($this->Controller)  && empty($this->data['area'])){
			$this->data['area']  = $this->Controller->name;
		}
		if(!empty($this->data['subarea'])){
			if(!empty($this->data['subarea']) && is_numeric($this->data['subarea'])){
				return  $this->data['area'].' item'.$this->data['subarea'];
			}else{
				return  $this->data['area'].' '.$this->data['subarea'];
			}
		}
		return $this->data['area'];
	}
	protected function getModulePrefix($path){
		$part =  explode("::",$path);
		return (count($part)>1) ? $part : false; 
	}
	public function loadCss($files){
		if(empty($files)) return;
		if(!is_array($files)) $files = array($files);
		if(!$this->_Less) $this->_Less = new \lessc;
		$this->_Less->setImportDir(array($this->app->config('less.path')));
		/**
		 * if path of file contin something like test::file.less
		 * test:: will be replaced with module path
		 */
		foreach($files as $file ){
			$isLess =preg_match("/less$/",$file);
			if($isLess){
				$modulePath= '';
				$lessPath = $this->app->config('less.path');
				$destFile=preg_replace("/less/","css",$file);
				// check if is a file in a module
				if($part = $this->getModulePrefix($file)){
					$modulePath = $part[0];
					$file  = ltrim(end($part) ,"/");
					$opi = pathinfo($file);
					
					$destFile = strtolower($modulePath).'_'.$opi['filename'].'.css';
					if($opi['dirname'] && $opi['dirname']!='.'){
						$destFile = $opi['dirname'].'/'.$destFile;
					}
					$lessPath = $this->_lessPath[$modulePath];
				}
				$file=$lessPath.$file;
				
				try {
					
					if($this->app->isProd() && !$this->app->config('fe.asset.forceless')) {
						$this->_Less->checkedCompile($file, $this->app->config('css.path').$destFile);
					} else {
						$this->_Less->compileFile($file, $this->app->config('css.path').$destFile);
					}
					echo '<link rel="stylesheet" href="'.$this->get('assetUrl').'css/'.$destFile.'?v='.$this->app->config('css.version').'" type="text/css"/>';
					echo "\n";
				} catch (exception $e) {
					//die( "fatal error: " . $e->getMessage());
				}
			}else{
				if(!preg_match("/^\//",$file)){
					echo '<link rel="stylesheet" href="'.$this->get('assetUrl').'css/'.$file.'?v='.$this->app->config('css.version').'" type="text/css"/>';
					echo "\n";
				}else{
					echo '<link rel="stylesheet" href="'.$file.'?v='.$this->app->config('css.version').'" type="text/css"/>';
					echo "\n";
				}
			}
		}
	}
	public function loadJs($files){
		if(empty($files)) return;
		if(!is_array($files)) $files = array($files);
		foreach($files as $file ){
			if(!preg_match("/^\//",$file)){
				echo '<script src="'.$this->get('assetUrl').'js/'.$file.'?v='.$this->app->config('js.version').'" type="text/javascript"></script>';
				echo "\n";
			}else{
				echo '<script src="'.$file.'?v='.$this->app->config('js.version').'" type="text/javascript" ></script>';
				echo "\n";
			}
		}
	}
	/**
	 * 
	 * start block for domready 
	 * use:
	 * $this->startDomready(); ?>
	 * <script>
	 * dostaff on js
	 * 
	 * </script>
	 * 
	 * <?$this->endDomready(); ?>
	 */

	public function startDomready(){
		ob_start();
	}
	
	public function endDomready(){
		$string= ob_get_clean();
		$pattern = "/<script[^>]*?>([\s\S]*?)<\/script>/";
		preg_match($pattern, $string, $inside_script_array);
		if(isset($inside_script_array[1]) && !empty($inside_script_array[1])){
			$this->domReady[] = $inside_script_array[1];
		}
	}
	public function onDomready(){
		if(empty($this->domReady)) return;
		$vr = "<script type=\"text/javascript\" >\n";
		$vr .= "$(document).ready(function(){\n";
	
		$vr .=implode("\n\n",$this->domReady);
		
		$vr .= "\n})\n";
		$vr .= "</script>\n";
		return $vr;
	}
	/* head method*/
	
	/*
	 *
	 * add css files
	 */
	public function addCss($css){
		if(!is_array($css))  $css = array($css=>$css);
		foreach($css as $k => $file){
			$key = $k;
			if(is_numeric($k)){
				$key = $file;
			}
			if(!isset($this->Css[$key])) $this->Css[$key] = $file;
		}
	}
	/**
	 * add js files 
	 * @param unknown $js
	 */
	public function addJs($js){
		if(!is_array($js)) $js = array($js=>$js);
		foreach($js as $k => $file){
			$key = $k;
			if(is_numeric($k)){
				$key = $file;
			}
			if(!isset($this->Js[$key])) $this->Js[$key] = $file;
		}
	}
	/**
	 * clear enqueued css asset
	 */
	public function resetCss(){
		$this->Css = array();
	}
	
	/**
	 * clear enqueued js asset
	 */
	public function resetJs(){
		$this->$js = array();
	}
	/**
	 * remove individual css file from queue list
	 * @param  [string] $css [css file to be removed]
	 */
	public function removeCss($css){
		if(isset($this->Css[$css])) unset($this->Css[$css]);
	}
	
	/**
	 * remove individual js file from queue list
	 * @param  [string] $js [js file to be removed]
	 */
	public function removeJs($js){
		if(isset($this->Js[$js])) unset($this->Js[$js]);
	}
	

	
	
	
	public function urlFor($name, $params = array(), $appName = 'default',$lang = null){
		$lang = $lang ? $lang : $this->config('lang');
		$params['lang'] = $lang;
		return parent::urlFor($name, $params);
	}
	
	
	/**************************************** SETTER ********************/

	
	
	/**
	 * DEPRECATION WARNING! This method will be removed in the next major point release
	 *
	 * Use getInstance method instead
	 */
	public function getEnvironment()
	{
		return $this->getInstance();
	}
	public function getInstance(){
		return $this;
	}
	/**
	 * chiamata in Modules/manager
	 * @param unknown $loader
	 * @return \Nne\View
	 */
	public function setLoader($loader){
		return $this;
	}
	

	public function setController($controller){
		$this->Controller = $controller;
	}
	public function getController(){
		return $this->Controller;
	}
	public function baseUrl(){
		if(!is_null($this->Controller) ){
			return $this->get('baseUrl');
		}
		return $this->Controller->baseUrl();
	}
	
	public function setHelper($helper){
		foreach($helper as $H=>$class){
			$this->{$H} = 	$class;
		}
	}
	/**\.************************************** SETTER ********************/
	
	
	/**************************************** Render ********************/


	/**
	 * add a box for an area of template 
	 * 
	 * @param unknown $path
	 * @param unknown $options
	 * @param string $sequence
	 */
	public function addBox($path,$options=array(),$sequence='maincol'){
		$this->Boxes[$sequence][] =array(
				'box'=>$path,
				'boxId'=>isset($options['id']) ?  $options['id'] : '',
				'title'=>isset($options['title']) ?  $options['title'] : false,
				'class'=>isset($options['class']) ?  $options['class'] : false,
				'data'=>isset($options['data']) ?  $options['data'] : false,
				'usePath'=>isset($options['usePath']) ?  $options['usePath'] : 'default',
		);
	}
	
	/**
	 * return number of boxes for spefic area
	 * @param unknown $area
	 * @return number
	 */
	function countBox($area){
		return count($this->Boxes[$area]);
	}
	
	/**
	 * called from template part .. load a piece of content and pass to id some parameters
	 * @param unknown $box
	 * @param unknown $params
	 */
	public function getElement($box,$params=array()){
		extract($params,EXTR_OVERWRITE);
		$bowPath  = $this->getTemplatePath($box.$this->_templateExtension,'boxes');
		@include($bowPath);
	}
	/**
	 * loop thought page boxes and include it
	 * @param unknown $seq
	 * @param number $selfstart
	 * @param number $selfnd
	 */
	function sequence($seq, $selfstart=0, $selfnd=0) {
		if ($selfnd==0) $selfnd = $this->countBox($seq);
	
		for($indIncludeBox=$selfstart; $indIncludeBox<$selfnd; $indIncludeBox++) {
			$BOX_VAR = array();
			if (is_array($this->Boxes[$seq][$indIncludeBox])) $BOX_VAR = $this->Boxes[$seq][$indIncludeBox];
			else $BOX_VAR['box'] = $this->Boxes[$seq][$indIncludeBox];
				
			$autobox = false;
			if (isset($BOX_VAR['autobox'])) $autobox = $BOX_VAR['autobox'];
				
			ob_start();
				
			$bx = $BOX_VAR['box'];
	
			$this->boxBefore($this->Boxes[$seq][$indIncludeBox]);
			if(!empty($BOX_VAR['data'])){
				extract($BOX_VAR['data'],EXTR_OVERWRITE);
			}
			$bowPath  = $this->getTemplatePath($bx.$this->_templateExtension,'boxes',(!empty($BOX_VAR['usePath']) ?  $BOX_VAR['usePath'] : false));
			@include($bowPath);
			$this->boxAfter($this->Boxes[$seq][$indIncludeBox]);
			ob_end_flush();
		}
	
	}
	/**
	 * output a html before each content box
	 * @param unknown $params
	 */
	private function boxBefore($params){
		if(!empty($params['boxId']) || !empty($params['class'])){
			echo "\n<div";
			if(!empty($params['boxId'])) echo ' id="'.$params['boxId'].'"';
			if(!empty($params['class'])) echo ' class="'.$params['class'].'"';
			echo "> \n";
		}
	}
	/**
	 * output a html after each content box
	 * @param unknown $params
	 */
	private function boxAfter($params){
		if(!empty($params['boxId']) || !empty($params['class'])) echo "</div>";
	}
	
	/**
	 * render a page(non-PHPdoc)
	 * @see \Slim\View::render()
	 */
	public function render($template, $data = null){
		$data = array_merge($this->all(), (array) $data);
		$templatePathname = $this->getTemplatePathnameLayout($template.$this->_templateExtension);
		if (!is_file($templatePathname)) {
			throw new \RuntimeException("View cannot render `$template` because the template does not exist");
		}
		$data = array_merge($this->data->all(), (array) $data);
		extract($data);
		ob_start();
		require $templatePathname;
		
		$content = $this->_streamContent['beforeContent'];
		$content .= ob_get_clean();
		$content .= $this->_streamContent['afterContent'];
		
		if($this->_streamContent['first']){
			ob_start();
			require $this->_streamContent['first'];
			$last = ob_get_clean();
			$content = $last . $content;
		}
		if($this->_streamContent['last']){
			ob_start();
			require $this->_streamContent['last'];
			$content .= ob_get_clean();
		}
		return $content;
	}
	
	
	/**
	 * add a template path
	 * @param unknown $templatePath
	 * @param string $namespace
	 */
	public function addPath($templatePath, $namespace = 'default'){
		$this->_templatesPath[$namespace]=$templatePath;
	}
	/**
	 * add a less path 
	 * @param unknown $lessPath
	 * @param string $namespace
	 */
	public function addLessPath($lessPath, $namespace = 'default'){
		$this->_lessPath[$namespace]=$lessPath;
	}
	
	/**
	 * return head file path
	 * @param string $file
	 * @param array $options
	 */
	public function getHead($file='head',$options=array()){
		$this->_streamContent['first']= $this->getTemplatePathnameLayoutPart($file.$this->_templateExtension,$options);;
	}
	/**
	 * return header streem content
	 * @param string $file
	 * @param array $options
	 */
	public function getHeader($file='header',$options=array()){
		$templatePathname = $this->getTemplatePathnameLayoutPart($file.$this->_templateExtension,$options);
		ob_start();
		require $templatePathname;
		$this->_streamContent['beforeContent'].= ob_get_clean();
	}
	
	/**
	 * return foot file path
	 * @param string $file
	 * @param array $options
	 */
	public function getFoot($file='foot',$options=array()){
		$this->_streamContent['last']=$this->getTemplatePathnameLayoutPart($file.$this->_templateExtension,$options);
	}
	/**
	 * return footer streem content
	 * @param string $file
	 * @param array $options
	 */
	public function getFooter($file='footer',$options=array()){
		$templatePathname = $this->getTemplatePathnameLayoutPart($file.$this->_templateExtension,$options);
		ob_start();
		require $templatePathname;
		$this->_streamContent['afterContent'].= ob_get_clean();
	}
	/**
	 * get template file with modules fallback
	 * @see \Slim\View::getTemplatePathname()
	 */
	public function getTemplatePath($file,$baseFolder,$namespace= false) {
		if(!$namespace) $namespace = 'default';
		// check if is a file in a module
		if($part = $this->getModulePrefix($file)){
			$namespace = $part[0];
			$file = end($part);
		}
		if($baseFolder){
			$file= $baseFolder . DIRECTORY_SEPARATOR . ltrim($file, DIRECTORY_SEPARATOR);
		}
		$this->setTemplatesDirectory($this->_templatesPath[$namespace]);
		$patName = parent::getTemplatePathname($file);
		$this->setTemplatesDirectory($this->_templatesPath['default']);
		return $patName;
	}
	/**
	 * get template part  path (eg. header, footer)
	 * @param unknown $file
	 * @return string
	 */
	public function  getTemplatePathnameLayoutPart($file){
		return $this->templatesDirectory . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR  . 'layout' . DIRECTORY_SEPARATOR . ltrim($file, DIRECTORY_SEPARATOR);
	}
	/**
	 * get template file  path
	 * @param unknown $file
	 * @return string
	 */
	public function getTemplatePathnameLayout($file){
		return $this->getTemplatePath($file,'templates');
	}
}