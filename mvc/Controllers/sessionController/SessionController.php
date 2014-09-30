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

		public function setSessionUser($session=array()){
			$_SESSION['session_user'] = $session;
		}

		public function getSessionUser(){
			return isset($_SESSION['session_user']['user']) ? $_SESSION['session_user']['user']: false;
		}


	}