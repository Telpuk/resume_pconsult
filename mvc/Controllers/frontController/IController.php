<?php

	abstract class IController{
		protected $_paramsGET = [];
		protected $_body;

		public function __construct(){
			$fc = FrontController::getInstance();
			$this->setParams($fc->getParams());
		}

		public function setParams($params){
			$this->_paramsGET = $params;
		}

		public function headerLocation($url = ""){
			header("Location: ".BASE_URL."/{$url}");
			exit;
		}

		public function getParams(){
			return $this->_params;
		}

		public function getSessionElements($element){
			return $_SESSION[$element];
		}

		public function getSession(){
			return $_SESSION;
		}

		public function setSession($session_var = []){
			$_SESSION = $session_var;
		}

		public function sessionClear(){
			session_destroy();
			$this->headerLocation();
		}
	}