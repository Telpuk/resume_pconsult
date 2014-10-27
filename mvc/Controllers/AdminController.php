<?php
class AdminController extends IController{
	private
		$_view,
		$_db_admin;

	public function  __construct(){

		parent::__construct();
		$this->_view = new View();
		$this->_db_admin = new Admin();
		$this->_db_admin->deleteNotStockedResume();
	}

	public function indexAction(){
		$this->sessionClear();
		return $this->_view->render(array(
			'view' => 'admin/authorization',
		));
	}

	public function outAction(){
		$this->sessionClear();
		$this->headerLocation('admin');
	}

	public function authorizationAction(){
		if (isset($_POST['submitAuthorization'])){
			if (!empty($_POST['login']) && !empty($_POST['password'])) {
				$users = $this->_db_admin->checkLoginAndPassword(array('login' => $_POST['login'], 'password' => $_POST['password']));
				if ($users === false) {
					return $this->_view->render(array(
						'view' => 'admin/authorization',
						'data' => array('helpers' => array('no-authorization' => 'admin/helpers/repeat'), 'login' => $_POST['login'])

					));
				} else if(isset($users['type_user']) && isset($users['id'])){
					$this->setSessionUsers(array('admin' => $users['type_user'], 'id_user_admin'=>$users['id']));
					$this->headerLocation('admincontrol');
				}

			} else {
				return $this->_view->render(array(
					'view' => 'admin/authorization',
					'data' => array('helpers' => array('no-authorization' => 'admin/helpers/empty_input'), 'login' => $_POST['login'])

				));
			}
		}else{
			return $this->_view->render(array(
				'view' => 'admin/authorization',
			));
		}


	}

}