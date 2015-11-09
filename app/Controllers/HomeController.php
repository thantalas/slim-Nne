<?php

/**
 * 
 * references 
 * to a view class view 
 * 		App::view()->setData("variabile","valore");
		View::setData("variabile","valore");
		$this->app->view->setData("variabile","valore");
		
 *  App::view()->setData("pippo","pluto");
 *  
 * @author thantalas
 *
 */
use \Nne\Components;
class HomeController extends AppController{
	public $name = 'home';
	protected $Components = array(
			'\Nne\Components\Contentz'=>array(
					'options'=>array(
							'pippo'=>'pluto',
							'cane'=>'gatto'
					)
			)
			
	);
	public function show(){
		$this->set('title','Welcome to Slim Starter Application');
		$this->addBox("home/home");
		$this->render();
	}

}