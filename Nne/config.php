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
 

define('ROOT_PATH'  , __DIR__.'/../');
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

if($_SERVER['SERVER_ADDR']=='127.0.0.1' || $_SERVER['SERVER_ADDR']=='192.168.111.111' || $_SERVER['SERVER_ADDR']=='192.168.1.2' ){
	define("APP_MODE",'dev');
} else {
	define("APP_MODE",'prod');
}

if($_SERVER['SERVER_ADDR']=='178.238.229.42' ) define('APP_LOCAL_SERVER'  ,false);
elseif(APP_MODE =='dev')  define('APP_LOCAL_SERVER',true);
else define('APP_LOCAL_SERVER',false);

require CORE_LIBS_PATH.'Hash.php';


// define("__CONFIG_SITE_PATH",ROOT_PATH);
// define("__CONFIG_SITE_URL",( $_SERVER['SERVER_PORT'] !='80') ? "http://".$_SERVER['SERVER_NAME'].':'.$_SERVER['SERVER_PORT']: "http://".$_SERVER['SERVER_NAME']);
// define("__ADMIN_PATH",__CONFIG_SITE_PATH."admin/");
// define("__ADMIN_URL",__CONFIG_SITE_URL."/admin/");
// define("__CONFIG_PHPMAILER_CLASSPATH",VENDOR_PATH."phpmailer/class.phpmailer.php");
// define("__CONFIG_VALIDATION_CLASSPATH",CORE_PATH."validation.php");
// define("__CONF_USERS_PATH",__CONFIG_SITE_PATH."config_user/");


// // Definizioni per ottimizzazione SEO
// define('SEO_META_DESCRIPTION_MAX_LENGTH',250); // Massima lunghezza del contenuto della meta tag description
// define('SEO_META_KEYWORDS_MAX_LENGTH',800); // Massima lunghezza del contenuto della meta tag keywords
// define('SEO_META_KEYWORDS_MAX_WORD_LENGTH',100); // Massima lunghezza di una singola keyword
// define('SEO_URLREW_ON',true);// se true le url vengono costruite

// define('PARAMLANGKEY','languagedata');


// define("ADMIN_USERLEVEL_NONE",0);
// define("ADMIN_USERLEVEL_READ",1);
// define("ADMIN_USERLEVEL_ALL",2);

// if(APP_MODE=='prod') define("__CONFIG_LOGIN_REFRESH_LOGINDATA_INTERVALL",300);//300=5 minuti
// else define("__CONFIG_LOGIN_REFRESH_LOGINDATA_INTERVALL",20);
// define("__CONFIG_COOKIE_DOMAIN",preg_replace("/www\./","",$_SERVER['SERVER_NAME']));