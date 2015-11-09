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


class Manager{
    private $modules;
    private $app;

    public function __construct(\Slim\Slim $app = null){
        $this->modules = array();
        $this->app = $app;
    }

    public function setApp(\Slim\Slim $app){
        $this->app = $app;
    }

    public function run($module){

    }

    public function trigger($event){

    }

    public function install($module){

    }

    public function uninstall($module){

    }

    public function register(ModuleInterface $module){
        $this->modules[$module->getModuleAccessor()] = $module;
    }

    public function boot(){
        $viewInstance   = $this->app->view->getEnvironment();

        foreach ($this->modules as $module) {
            $prefixDir = $module->getModuleName();
            $modulePath= $this->app->config('path.module').$prefixDir.'/';
			require($modulePath.'bootstrap.php');
            /** registering module view namespace */
            foreach ($module->getTemplatePath() as $namespace => $dir) {
                $moduleTemplatePath = $modulePath.$dir;
                $viewInstance->addPath($moduleTemplatePath, $namespace);
            }
            /** registering module view namespace */
            foreach ($module->getLessPath() as $namespace => $dir) {
                $moduleLessPath = $modulePath.$dir;
                $viewInstance->addLessPath($moduleLessPath, $namespace);
            }

            $module->boot();
        }

    }

    public function getModules(){
        return $this->modules;
    }
}