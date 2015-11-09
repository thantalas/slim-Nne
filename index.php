<?php
/**
 * first configuration  entrypoint  
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
if (!defined('PHP_VERSION_ID')) {
	$PHPversion = explode('.', PHP_VERSION);
	define('PHP_VERSION_ID', ($version[0] * 10000 + $version[1] * 100 + $version[2]));
}
if (PHP_VERSION_ID < 50207) {
	define('PHP_MAJOR_VERSION',   $PHPversion[0]);
	define('PHP_MINOR_VERSION',   $PHPversion[1]);
	define('PHP_RELEASE_VERSION', $PHPversion[2]);
}
define("__CONFIG_CHARSET","UTF-8");
 

define('ROOT_PATH'  , __DIR__.'/');
define('VENDOR_PATH', ROOT_PATH.'vendor'.DIRECTORY_SEPARATOR);
define('APP_PATH'   , ROOT_PATH.'app'.DIRECTORY_SEPARATOR);
define('APP_MODULE_PATH', APP_PATH.'Modules'.DIRECTORY_SEPARATOR);
define('MODULE_PATH', ROOT_PATH.'Nne'.DIRECTORY_SEPARATOR.'modules'.DIRECTORY_SEPARATOR);
define('PUBLIC_PATH', ROOT_PATH.'public'.DIRECTORY_SEPARATOR);
define('CORE_PATH', ROOT_PATH.'Nne/Nne'.DIRECTORY_SEPARATOR);
define('CORE_LIBS_PATH', CORE_PATH.'Libs'.DIRECTORY_SEPARATOR);
define("APP_CONF_PATH",APP_PATH."config".DIRECTORY_SEPARATOR);
define("ASSET_PATH",PUBLIC_PATH);
define("CSS_PATH",ASSET_PATH."css".DIRECTORY_SEPARATOR);
define("LESS_PATH",ASSET_PATH."less".DIRECTORY_SEPARATOR);
define("ADMIN_TEMPLATE_PATH",CORE_PATH.'Views'.DIRECTORY_SEPARATOR.'admin');

/**
 * define here your environmet switch
 */
if($_SERVER['SERVER_ADDR']=='127.0.0.1' || $_SERVER['SERVER_ADDR']=='192.168.111.111' || $_SERVER['SERVER_ADDR']=='192.168.1.2' ){
	define("APP_MODE",'dev');
} else {
	define("APP_MODE",'prod');
}

if($_SERVER['SERVER_ADDR']=='178.238.229.42' ) define('APP_LOCAL_SERVER'  ,false);
elseif(APP_MODE =='dev')  define('APP_LOCAL_SERVER',true);
else define('APP_LOCAL_SERVER',false);


require CORE_LIBS_PATH.'Hash.php';

require 'public/index.php';