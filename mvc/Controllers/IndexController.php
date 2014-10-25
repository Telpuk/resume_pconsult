<?php
class IndexController extends IController{
	private $_view,
		$_db_user,
		$_id_user,
		$_id_admin,
		$type_admin = array('main'=>'админ','manager'=>"менеджер");

	public function __construct(){
		parent::__construct();
		$this->_view = new View();
		$this->_db_user = new User();

		$this->_id_admin = $this->getSessionUserID('admin');

		if($this->_id_admin === 'main' || $this->_id_admin === 'manager'){
			if($this->getParams('id')) {
				$this->_id_user = $this->getParams('id');
				$this->setSessionUsers(array('user' => $this->_id_user));
			}else{
				$this->_id_user = $this->getSessionUserID('user');
			}
		}else{
			$this->_id_user = $this->getSessionUserID('user');
		}

		if(!$this->_id_user && !$this->_id_admin){
			$this->_db_user->setIdUser();
			$this->_id_user = $this->_db_user->getIdUser();
			$this->setSessionUsers(array('user' => $this->_id_user));
		}
	}

	public function dcommentAction(){
		$id = $this->getParams('comment_id');
		if($id){
			$this->_db_user->deleteComment($id);
		}
		$this->headerLocation('index');
	}

	public  function indexAction(){
		$widget = !$this->_id_admin?array('widget' => 'index/helpers/widget_personal'):array('widget'=>'index/helpers/widget_administrator');

		$select_personal_data = $this->_db_user->selectPersonalData($this->_id_user);

		if($this->_id_admin==='main' || $this->_id_admin==='manager'){
			$select_personal_data = @array_merge((array)$select_personal_data,(array)$this->_db_user->selectCommits($this->_id_user));
		}

		if($select_personal_data !== false) {
			return $this->_view->render(array(
				'view' => 'index/index',
				'data' => @array_merge(array(
						'helpers'=> $widget
					),
					array(
						'id_admin'=>$this->_id_admin,
						'type_admin'=>$this->type_admin
					),
					(array)$select_personal_data)
			));
		}
		$this->headerLocation('error');
	}

	public function commentAction(){
		if(isset($_POST['addComment']) && !empty($_POST['comment'])){
			$this->_db_user->addComment($_POST['comment'],$this->_id_user);
		}
		$this->headerLocation('index');
	}

	public function deleteAction(){
		$this->_db_user->deleteResume($this->_id_user);
		if($this->_id_admin){
			$this->sessionDeleteIdUser('user');
			$this->headerLocation('admincontrol');
		}else{
			$this->sessionClear();
			$this->headerLocation('index');
		}
	}

	public function finishAction(){
		$this->_db_user->finishResume($this->_id_user);
		$this->sessionClear();
		$this->headerLocation('index');
	}

}