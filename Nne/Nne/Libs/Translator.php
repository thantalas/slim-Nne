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
 * This lib by https://github.com/briannesbitt/Slim-Multilingual
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
interface iTranslate {
	public function translate($lang, $key, $replacements);
}
class Translator implements iTranslate {
	private $log;
	private $path;
	public function __construct($log, $path) {
		$this->log = $log;
		$this->path = $path;
		if (substr($this->path, -1) != '/') {
			$this->path .= '/';
		}
	}
	public function translate($lang, $key, $replacements) {
		global $locale;
		include_once $this->path.'lang.common.php';
		include_once $this->path.'lang.'.$lang.'.php';
		
		$text = '';
		if (!array_key_exists($key, $locale)) {
			//$this->log->error("Translation of $key was not found.");
			return '';
		} else {
			$text = $locale[$key];
		}
		if (is_array($replacements) && count($replacements) > 0) {
			foreach($replacements as $name => $value) {
				$text = str_replace('{{' . $name . '}}', $value, $text);
			}
		}
		return $text;
	}
}