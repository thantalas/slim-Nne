<?php
class ErrorController extends AppController{
	public $name = 'error';
	public function notFound(){
		$this->set('title','Page not foud');
		$this->addBox("errors/404");
		$this->render('400');
	}
	public function error($errorCode = 500){
		$this->set('title','Server error');
		$this->set('errorCode',$errorCode);
		$this->addBox("errors/500");
		$this->render('error');
	}
}