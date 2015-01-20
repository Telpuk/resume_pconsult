<?php

	abstract class IController extends SessionController{
		protected
			$_paramsGET = array(),
			$_body,
			$_fc,
			$_currentUrl;


		public function __construct(){
			$this->_fc = FrontController::getInstance();
			$this->setParams($this->_fc->getParams());
		}

		public function setParams($params){
			$this->_paramsGET = $params;
		}

		public function headerLocation($url = null){
			header('Location: '.BASE_URL.'/'.$url);
			exit;
		}

		public function writeCurrentUrlCookies($url = null){
			$_SESSION['currentUrl'] =  $url;
		}
		public function readCurrentUrlCookies(){
			return $_SESSION['currentUrl'];
		}

		public function getCurrentUrl(){
			return $this->_fc->getCurrentUrl();
		}

		public function getParams($param){
			return isset($this->_paramsGET[$param])?$this->_paramsGET[$param]:null;
		}

	}