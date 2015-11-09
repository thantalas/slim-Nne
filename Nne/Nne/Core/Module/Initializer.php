<?php
/**
 * Module manager 
 * From Idea of SlimStarter, https://github.com/xsanisty/SlimStarter
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
 * @package		Nne\Helpers
 * @since		Nne (tm) v 1
 * @license		http://www.opensource.org/licenses/mit-license.php MIT License
 * @project		Ninety Nine Enemies Project
 * @encoding	utf-8
 * @author		Giorgio Tonelli <th.thantalas@gmail.com>, <http://thnet.komunikando.org>
 * @creation	08/nov/2015
 */
namespace Nne\Core\Module;

abstract class Initializer implements ModuleInterface{
    abstract function getModuleName();
    abstract function getModuleAccessor();
	/**
	 * add part at admin section menumanager
	 */
    public function registerMenuBuilder(){

    }
	/**
	 * add part at admin section widget manager
	 */
    public function registerWidgetBuilder(){

    }
	/**
	 * add part at admin section aiohp manager
	 */
    public function registerAiohptBuilder(){

    }
    /**
     * add part at admin section aiohp manager
     */
    public function registerSiteOptions(){
    
    }
    public function registerAdminRoute(){

    }

    public function registerAdminMenu(){

    }

    public function registerPublicRoute(){

    }

    public function getTemplatePath(){
        return array(
            $this->getModuleAccessor() => 'Views'
        );
    }
    public function getLessPath(){
        return array(
            $this->getModuleAccessor() => 'less'.DIRECTORY_SEPARATOR
        );
    }

    public function registerHook(){

    }

    public function boot(){
        $this->registerHook();
    }

    public function install(){

    }

    public function uninstall(){

    }

    public function activate(){

    }

    public function deactivate(){

    }

}