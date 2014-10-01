<?php
	class ProfileController extends IController
	{
		private $_view;

		public function __construct(){
			parent::__construct();
			$this->_view = new View();
		}


		public function indexAction(){
			return $this->_view->render (array(
				'view' => 'profile/index'
			));
		}

		public function photoAction(){
			return $this->_view->render (array(
				'view' => 'profile/photo',
			));
		}
		public function personalAction(){
			return $this->_view->render (array(
				'view' => 'profile/personal',
				'js'=>$this->_jsPersonal(),
			));
		}

		private function _jsPersonal(){
			return array(
				'src'=>array(
					BASE_URL."/public/js/jquery-2.1.1.min.js",
					BASE_URL."/public/js/jquery.validate.min.js",
					BASE_URL."/public/js/personal.js"
				),
			);

		}



	}


