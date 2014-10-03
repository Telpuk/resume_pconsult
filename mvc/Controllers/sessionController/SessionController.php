<?php
	class SessionController{
		private $_controllers;

		public function __construct(){
			$this->setControllers(require_once "config_session.php");
		}

		private function setControllers($controllers){
			$this->_controllers = $controllers;
		}

		public function getControllers(){
			return $this->_controllers;
		}

		public function setSessionUsers($session=array()){
			$_SESSION['session_users']['users'] = $session;
		}

		public function getSessionUsers(){
			return isset($_SESSION['session_users']['users']) ? $_SESSION['session_users']['users']: false;
		}

		public function getSessionUserID($user){
			return isset($_SESSION['session_users']['users'][$user]) ? $_SESSION['session_users']['users'][$user]: false;
		}

		public function sessionClear(){
			session_destroy();
			$this->headerLocation();
		}



	}