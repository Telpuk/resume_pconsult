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
			$_SESSION['session_users']['users'] = @array_merge((array)$_SESSION['session_users']['users'],$session);
		}
		public function setSessionParams($session=array()){
			$_SESSION['params'] = @array_merge((array)$_SESSION['params'],$session);
		}

		public function getSessionParamsId($id_params){
			return isset($_SESSION['params'][$id_params]) ? $_SESSION['params'][$id_params]: false;
		}
		public function deleteSessionParamsId($id_params){
			unset($_SESSION['params'][$id_params]);
		}

		public function getSessionUsers(){
			return isset($_SESSION['session_users']['users']) ? $_SESSION['session_users']['users']: false;
		}

		public function getSessionUserID($user){
			return isset($_SESSION['session_users']['users'][$user]) ? $_SESSION['session_users']['users'][$user]:false;
		}

		public function deleteSessionUsers($user){
			unset($_SESSION['session_users']['users'][$user]);
		}

		public function sessionClear(){
			session_destroy();
		}

		public function sessionDeleteIdUser($user){
			unset($_SESSION['session_users']['users'][$user]);
		}



	}