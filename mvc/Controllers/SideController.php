<?php
	class SideController extends IController{
		private $_view;
		private $_dbuser;

		public function __construct ()
		{
			parent::__construct ();
			$this->_view   = new View();
			$this->_dbuser = new User();
		}

		public function positionAction(){
			print_r($_POST);
			return $this->_view->render(array(
				'view' => 'side/position',
				'data'=>array('inputs'=>''),
				'js'=>$this->_jsPosition(),
			));
		}

		private function _jsPosition(){
			return array(
				'src'=>array(
					BASE_URL."/public/js/jquery-2.1.1.min.js",
					BASE_URL."/public/js/jquery.validate.min.js",
					BASE_URL."/public/js/position.js"
				),
			);
		}
	}