<?php

	abstract class IController extends SessionController{
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
			return $this->_paramsGET;
		}

	}