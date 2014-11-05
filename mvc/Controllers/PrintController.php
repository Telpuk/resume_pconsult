<?php
class PrintController extends IController{
	private
		$_view,
		$_db_user;

	public function  __construct(){

		parent::__construct();
		$this->_view = new View();
		$this->_db_user = new User();
	}

	public function indexAction(){
		if($this->getParams('id') && is_numeric($this->getParams('id')) ){

			$select_personal_data = $this->_db_user->selectPersonalData($this->getParams('id'));

			$select_personal_data = @array_merge((array)$select_personal_data,(array)$this->_db_user->selectCommits($this->getParams('id')));

//			print_r($select_personal_data);
			return $this->_view->render(array(
				'view'=>'print/index',
				'data' => $select_personal_data,
				'js'=>$this->_jsPrint()
			));
		}else{
			$this->headerLocation('index');
		}


	}

	private function _jsPrint(){
		return array(
			'src'=>array(
				BASE_URL."/public/js/jquery-2.1.1.min.js",
				BASE_URL."/public/js/jquery.printPage.js",
				BASE_URL."/public/js/print.js"
			),
		);
	}
}