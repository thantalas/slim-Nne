<?php
/**
 * Base Controller
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
namespace Nne\Controllers;
class BaseController
{

    protected $app;
    protected $data = array() ;
    protected $LoadedHelpers = array();
    protected $Helpers = array();
    protected $AdminBaseHelpers = array(
    		'AdminHtmlHelper'=>array(
    				'alias'=>'Html',
    				'namespace'=>'\\Nne\\Helpers\\'
    		)
    		
    );
    protected $Components = array();
    protected $ComponentsInstances = array();

    public function __construct()
    {
        $this->app = \Nne\NneSlim::getInstance();
        $this->data = array();
        $this->addComponents($this->Components);
        
        /**
         * replace base helpers with admin versions
         */
        if($this->app->isAdmin()){
        	foreach($this->AdminBaseHelpers as $helperName => $helper){
        		$name = $helperName;
        		if(is_array($helper) && isset($helper['alias'])){
        			$name = $helper['alias'];
        		}
        		if(isset($this->Helpers[$name])) {
        			unset($this->Helpers[$name]);
        			$this->Helpers[$helperName] = $helper;
        		}
        		
        	}
        }
        $this->setHelper($this->Helpers);

        /** default title */
        $this->data['title'] = '';

        /** meta tag and information */
        $this->data['meta'] = array();


        /** prepared message info */
        $this->data['message'] = array(
            'error'    => array(),
            'info'    => array(),
            'debug'    => array(),
        );

        /** global javascript var */
        $this->data['global'] = array();

        /** base dir for asset file */
        $this->data['baseUrl']  = $this->baseUrl();
        $this->data['assetUrl'] = $this->data['baseUrl'].'public/';
        /**
         * set on view object current controller insyance
         */
        $this->app->view()->setController($this);
    }
    
    public function setHelper($helpers = array()){
    	
    	if($helpers){
    		foreach($helpers as $helper => $h){
    			$name = $helper;
    			$namespace = '';
    			if(is_array($h)){
    				if(isset($h['alias'])) $name = $h['alias'];
    				if(isset($h['namespace'])) $namespace = $h['namespace'];
    			}
    			if(!isset($this->LoadedHelpers[$name])){
    				$className = $namespace.$helper;
    				$this->LoadedHelpers[$name] = new $className();
    				$this->app->view()->setHelper(array(
    						$name => $this->LoadedHelpers[$name]
    				));
    			}

    		}
    	}
    }
    /**
     * add components classes to controller
     * each component will be accessible by $this->componentname
     * @param unknown $components
     */
    public function addComponents($components = array()){
    	if(!is_array($components))  return;
            foreach($components as $class => $component){

            	$classArray= explode("\\",$class);
            	$className= end($classArray);
            	$options = array();
            	if(is_array($component)){
            		if(isset($component['className'])){
            			$className= $component['className'];
            		}
            		if(isset($component['options']) and is_array($component['options'])){
            			$options= $component['options'];
            		}
            	}
            	$classKey = $class . '.' .$className;
            	
        		if(!isset($this->ComponentsInstances[$classKey])){
        			$this->ComponentsInstances[$classKey]= $classKey;
        			$class.='Component';
        			$this->{$className} = new $class($this);
        			foreach($options as $opt => $optValue){
        				$this->{$className}->$opt = $optValue;
        			}
        		}
        	}
    }
    /**
     * get current language
     */
    public function getLang(){
    	return $this->app->view()->getLang();
    }
    /**
     * get translation string
     * @param unknown $key
     * @param unknown $replacements
     */
    public function __($key, $replacements = array()) {
    	return $this->app->view()->__($key,$replacements);
    }

    /**
     * addMessage to be viewd in the view file
     */
    protected function message($message, $type='info'){
        $this->data['message'][$type] = $message;
    }

    /**
     * add custom meta tags to the page
     */
    protected function meta($name, $content){
        $this->data['meta'][$name] = $content;
    }
    /**
     * add data to internal data array
     * @param unknown $key
     * @return Ambigous <boolean, multitype:>
     */
    protected function set($key,$value=false){
    	if(!is_array($key)){
    		$key = array($key => $value);
    	}
    	foreach($key as $k => $val){
    		$this->data[$k] = $val;
    	}
    	
    }
    /**
     * get data from  data array
     * @param unknown $key
     * @return Ambigous <boolean, multitype:>
     */
    protected function get($key= false){
    	if(!$key) return $this->data;
    	 return isset($this->data[$key]) ?  $this->data[$key] : false;
    }
    protected function render($template ='page',$data = array()){
    	$this->app->render($template, array_merge($this->get(),$data));
    }
    /**
     * add box to view
     * @param unknown $path
     * @param unknown $options
     * @param string $sequence
     */
    protected function addBox($path,$options=array(),$sequence='maincol'){
    	return $this->app->view->addBox($path,$options,$sequence);
    }
    /*
     * 
     * add css files to view
     */
    protected function addCss($css){
    	$this->app->view()->addCss($css);
    	
    }
    /**
     * add js files to view
     * @param unknown $js
     */
    protected function addJs($js){
    	$this->app->view()->addJs($js);
    }

    /**
     * remove individual css file from queue list
     * @param  [string] $css [css file to be removed]
     */
    protected function removeCss($css){
    	$this->app->view()->removeCss($css);
    }
    
    /**
     * remove individual js file from queue list
     * @param  [string] $js [js file to be removed]
     */
    protected function removeJs($js){
    	$this->app->view()->removeJs($js);
    }
    /**
     * clear enqueued css asset
     */
    protected function resetCss(){
    	$this->app->view()->resetCss();
    }
    
    /**
     * clear enqueued js asset
     */
    protected function resetJs(){
    	$this->app->view()->resetJs();
    }
    /**
     * generate base URL
     */
    protected function baseUrl()
    {
        $path       = dirname($_SERVER['SCRIPT_NAME']);
        $path       = trim($path, '/');
        $baseUrl    = \Request::getUrl();
        $baseUrl    = trim($baseUrl, '/');
        return $baseUrl.'/'.$path.( $path ? '/' : '' );
    }

    /**
     * generate siteUrl
     */
    protected function siteUrl($path, $includeIndex = false)
    {
        $path = trim($path, '/');
        return $this->data['baseUrl'].$path;
    }
}
