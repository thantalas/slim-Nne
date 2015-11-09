<?php
/**
 */
class AppController extends  \Nne\Controllers\BaseController{
	/**
	 * Helper used on views
	 * some elpers will be replaced if we are in admin mode with corrispective admin version
	 * see Nne/Controllers/BaseController on AdminBaseHelpers array
	 * @var array
	 */
	protected $Helpers = array(
			'HtmlHelper'=>'Html' 
	);
	public function __construct(){
		parent::__construct();
	}
}