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
		if($_POST['searchResume'] && $_POST['search']){

			return $this->_view->render(array(
				'view'=>'admin_control/index',
				'data'=>array(
					'user'=>$this->_db_admin->search($_POST['search']),
					'search'=>$_POST['search'])
			));

		}

		return $this->_view->render(array(
			'view'=>'admin_control/index'
		));
	}
}