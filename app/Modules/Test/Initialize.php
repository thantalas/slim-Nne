<?php

namespace Test;

use \App;
use \Menu;
use \Route;

class Initialize extends \Nne\Core\Module\Initializer{
	protected $name = 'Test';
    public function getModuleName(){
        return $this->name;
    }
    public function registerAdminMenu(){

    	$adminMenu = Menu::get('admin_sidebar');

    
    	$testBase = $adminMenu->createItem('testgroup', array(
    			'label' => 'Test module',
    			'icon'  => 'group',
    			'url'   => '#'
    	));
    	$testBase->setAttribute('class', 'nav nav-second-level');
    
    	$test1Menu = $adminMenu->createItem('test1', array(
    			'label' => 'Test link 1',
    			'icon'  => 'user',
    			'url'   => 'admin/test'
    	));
    
    	$test2Menu = $adminMenu->createItem('test2', array(
    			'label' => 'Test link 2',
    			'icon'  => 'group',
    			'url'   => 'admin/test/soon'
    	));
    
    	$testBase->addChildren($test1Menu);
    	$testBase->addChildren($test2Menu);
    
    	$adminMenu->addItem('testbase', $testBase);
    }
    public function getModuleAccessor(){
        return $this->name;
    }
    public function registerAdminRoute(){
		//Route::resource('/test', $this->name.'\Controllers\TestController');// resource map rest action stored in routerFacade
		// OR
		Route::resource('/test', array('name'=>$this->name),$this->name.'\Controllers\TestController');// resource map rest action stored in routerFacade
		//Route::get('/test',  $this->name.'\Controllers\TestController:admin_index')->name('admin-test-index');
    }
    public function registerPublicRoute(){
    	Route::get('/test', $this->name.'\Controllers\TestController:index')->name('test-index');
        //Route::resource('/test', 'Test\Controllers\TestController');
    }
}