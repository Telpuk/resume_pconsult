<?php
class AdminController extends IController{
	private
		$_view,
		$_db_admin,
		$_id_admin;

	public function  __construct(){
		parent::__construct();
		$this->_view = new View();
		$this->_db_admin = new Admin();
	}

	public function indexAction(){
		$this->_id_admin = $this->getSessionUserID('admin');
		if($this->_id_admin === 'admin'){
			if($this->getSessionUserID('user')){
				$this->deleteSessionUsers('user');
			}
			$this->headerLocation('admincontrol');
		}else{
			$this->sessionClear();
			$this->headerLocation('admin/authorization');
		}
	}

	public function outAction(){
		$this->sessionClear();
		$this->headerLocation('admin');
	}

	public function authorizationAction(){
		if (isset($_POST['submitAuthorization'])){
			if (!empty($_POST['login']) && !empty($_POST['password'])) {
				$admin = $this->_db_admin->checkLoginAndPassword(array('login' => $_POST['login'], 'password' => $_POST['password']));
				if ($admin !== 'admin') {
					return $this->_view->render(array(
						'view' => 'admin/authorization',
						'data' => array('helpers' => array('no-authorization' => 'admin/helpers/repeat'), 'login' => $_POST['login'])

					));
				} else if($admin === 'admin'){
					$this->setSessionUsers(array('admin' => $admin));
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