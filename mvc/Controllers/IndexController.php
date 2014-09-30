<?php
	class IndexController extends IController{
		private $_view;

		public function __construct(){
			parent::__construct();
			$this->_view = new View();
		}


		public  function indexAction(){
			return $this->_view->render([
				'view'=>'index/index',
			]);
		}

		public function sendingAction(){
			if(isset($_POST['sendingSubmit'])){
				return $this->_view->render([
					'view'=>'index/sending_message'
				]);
			}

		}



	}