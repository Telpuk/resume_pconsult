<?php
	class IndexController extends IController{
		private $_view;
		private $_dbuser;
		private $_iduser;

		public function __construct(){
			parent::__construct();
			$this->_view = new View();

			//$this->sessionClear();

			$this->_iduser = $this->getSessionUserID('user');

			if(!$this->_iduser){
				$this->_dbuser = new User();
				$this->_iduser = $this->_dbuser->getIdUser();
				$this->setSessionUsers(array('user' => $this->_iduser));
			}


		}

		public  function indexAction(){

			return $this->_view->render([
				'view'=>'index/index',
			]);
		}





	}