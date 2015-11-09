<?php
namespace Test\Controllers;
use Admin\AdminController ;
class TestController extends AppController{
	public $name = 'test';
	public function __construct(){
		parent::__construct();
	}
	public function index(){
		$this->addBox("Test::index");
		$this->addCss("Test::test.less");
		$this->addCss("Test::/maremma/pippo.less");
		$this->render("Test::testtemplate");
	}
	public function admin_index(){
		$this->addBox("Test::admin/index");
		$this->addCss("Test::admin/test.less");
		// you can use module admin template
		$this->render("Test::admin/admin");
		// or use core admin template find a Nne/Views/templates/admin
		// you can use another core template  in this manner $this->render("anothertemplate");
		//$this->render(AdminController::getTemplate());
	}
}