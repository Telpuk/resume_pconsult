<?php
class IndexController extends IController{
	private $_view;
	private $_db_user;
	private $_id_user;

	public function __construct(){
		parent::__construct();
		$this->_view = new View();
		$this->_db_user = new User();

		//			$this->sessionClear();

		$this->_id_user = $this->getSessionUserID('user');

		if(!$this->_id_user){
			$this->_db_user->setIdUser();
			$this->_id_user = $this->_db_user->getIdUser();
			$this->setSessionUsers(array('user' => $this->_id_user));
		}
	}

	public  function indexAction(){

		return $this->_view->render(array(
			'view'=>'index/index',
			'data'=>$this->_db_user->selectPersonalData($this->_id_user)
		));
	}





}