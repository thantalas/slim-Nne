<?php
/**
 * Configure, this class is used to store configuration like cakeph framework
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
namespace Nne\Libs;
class Configure {
	
	private static $confList = array();
	private static $defineList;
	private static $singletoninstance;
	
	public static function write($config,$value=null){
		if (!is_object(self::$singletoninstance)) self::singleton();
		
		if (!is_array($config)) {
			$config = array($config => $value);
		}
	
		foreach ($config as $name => $value) {
			self::$confList = Hash::insert(self::$confList, $name, $value);
		}
	
		$_SESSION['cacheConfig'] = self::$confList;
	
	}
	/*
	 * alias di getConf
	 */
	public static function read($const,$default=false){

		if (!is_object(self::$singletoninstance)) self::singleton();
	
		if (!isset(self::$confList[$const]) && defined($const)){
			return constant($const);
		}
	
		$value= Hash::get(self::$confList,$const,$default);
		
		return $value;
	}
	
	public static function &singleton(){
		if (!is_object(self::$singletoninstance)){
			$classname=__CLASS__;
			self::$singletoninstance=true;
			self::$singletoninstance=new $classname(false);
			self::$singletoninstance->init();
		}else if (!is_a(self::$singletoninstance,__CLASS__)){
			if((APP_MODE=='dev')) die("Singleton error: tentativo di creare diverse istanze ereditarie di ". __CLASS__);
		}
	
		return self::$singletoninstance;
	}
	/**
	 * init configure and red a staic json file for custom app settings
	 */
	public static function init(){
		//unset($_SESSION['cacheConfig']);
// 		self::$confFile=self::$confPath.self::$confFileName;
	
// 		if(file_exists(self::$confFile)){
// 			$filetime=filemtime(self::$confFile);
// 			if(isset($_SESSION['cacheConfig']) && !empty($_SESSION['cacheConfig']) && $_SESSION['cacheConfig']['cachetime']==$filetime){
// 				self::$confList=$_SESSION['cacheConfig'];
// 			}else{
// 				if(!self::$confList=@parse_ini_file(self::$confFile,true)){
// 					traceLog(__CLASS__.'::'.__METHOD__.' File  non caricato:'.self::$confFile,2);
// 				}else{
// 					$_SESSION['cacheConfig']=self::$confList;
// 					$_SESSION['cacheConfig']['cachetime']=$filetime;
// 				}
// 			}
// 		}
	}
}

