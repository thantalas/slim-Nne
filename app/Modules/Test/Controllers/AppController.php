<?php
namespace Test\Controllers;

use \App;
use \View;
use \Menu;

class AppController extends \AppController{
	protected $moduleName= 'Test';
	
	public function __construct(){
		parent::__construct();
	}
}