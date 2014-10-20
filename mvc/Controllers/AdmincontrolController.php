<?php
class AdminControlController extends IController{
	private $_view,
			$_db_admin;

	public function  __construct(){
		parent::__construct();
		$this->_view = new View();
		$this->_db_admin = new Admin();
	}

	public function indexAction(){
		if($_POST['searchResume']){
			 print_r($_POST);
		}

		return $this->_view->render(array(
			'view'=>'admin_control/index'
		));
	}


}