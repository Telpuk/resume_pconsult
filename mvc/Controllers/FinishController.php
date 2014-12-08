<?php

class FinishController extends IController
{
	private
		$_view,
		$_db_user;

	public function __construct(){
		parent::__construct();
		$this->_view = new View();
		$this->_db_user = new User();
	}

	public function indexAction()
	{
		$id_uniqid =  $this->getSessionParamsId('time_input_user');
		$id = $this->getSessionUserID('user');

		if(!empty($id) && ($id_uniqid === $this->getParams('uniqid'))) {
			$this->sessionClear();
			$this->_db_user->finishResume($id);
			return $this->_view->render(array('view' => 'finish/index'));
		}else{
			$this->headerLocation('index');
		}

	}
}