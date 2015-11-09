<?php
/**
 * CUSTOM SLIM INSTANCE 
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
use Nne\Libs\Configure as Configure;

class NneSlim extends \Slim\Slim {
    public function urlFor( $name, $params = array() ) {
        return sprintf('/%s%s', $this->view()->getLang(), parent::urlFor($name, $params));
    }
    
    public function __construct(array $userSettings = array()){
    	parent::__construct($userSettings);
    	Configure::write($this->container['settings']);
    }
    
    
    public function view($viewClass = null){
    	if (!is_null($viewClass)) {
    		$existingData = is_null($this->view) ? array() : $this->view->getData();
    		if ($viewClass instanceOf \Slim\View) {
    			$this->view = $viewClass;
    		} else {
    			$this->view = new $viewClass($this);
    		}
    		$this->view->appendData($existingData);
    		$templateDirectory = ($this->isAdmin()) ? $this->config('templates.admin.path') : $this->config('templates.path');
    		$this->view->addPath($templateDirectory);
    		$this->view->addLessPath($this->config('less.path'));
    		$this->view->setTemplatesDirectory($templateDirectory);
    	}
    
    	return $this->view;
    }
    /**
     * overwrite original method with custom configure class
     ** @see \Slim\Slim::config()
     */
    public function config($name, $value = null){
    	$c = $this->container;
    	if (is_array($name)) {
    		if (true === $value) {
    			$c['settings'] = array_merge_recursive($c['settings'], $name);
    		} else {
    			$c['settings'] = array_merge($c['settings'], $name);
    		}
    		Configure::write($name);
    	} elseif (func_num_args() === 1) {
    		return Configure::read($name,null);
    		//return isset($c['settings'][$name]) ? $c['settings'][$name] : null;
    	} else {
    		$settings = $c['settings'];
    		$settings[$name] = $value;
    		$c['settings'] = $settings;
    		Configure::write($name,$value);
    	}
    }
    /**
     * return true if app run on prod see APP_MODE constants on /index.php
     * @return boolean
     */
    public function isProd(){
    	return ($this->config('mode') == 'production');
    }
    /**
     * return true if app run on dev see APP_MODE constants on /index.php
     * @return boolean
     */
    public function isDev(){
    	return ($this->config('mode') == 'development');
    }
    /**
     * return true if we are on admin area
     * 
     * @return boolean
     */
    public function isAdmin(){
    	return $this->config('isAdmin');
    }
}
