<?php
class IndexController extends IController{
	private $_view,
		$_db_user,
		$_id_user,
		$_admin,
		$_type_admin = array('main'=>'админ','manager'=>"менеджер");

	public function __construct(){
		parent::__construct();
		$this->_view = new View();
		$this->_db_user = new User();
		$this->_db_admin = new Admin();

		$this->_admin = $this->getSessionUserID('admin');
		$this->_id_user = $this->getSessionUserID('user');

		if(($this->_admin === 'main' || $this->_admin === 'manager') && !$this->_id_user){
			if(is_numeric($this->getParams('id')) &&  $this->_db_user->checkId($this->getParams('id'))) {
				$this->_id_user = $this->getParams('id');
				$this->setSessionUsers(array('user' => $this->_id_user));
			}else{
				$this->headerLocation('admincontrol');
			}
		}


		if(!is_numeric($this->_id_user) && !$this->_admin){
			$this->_db_user->setIdUser();
			$this->_id_user = $this->_db_user->getIdUser();
			$this->setSessionUsers(array('user' => $this->_id_user));
		}
	}


	public function ajaxfoldersusersAction(){
		if(isset($_POST['ajax'])) {
			$obj_folders = new Folders($this->getSessionUserID('user'));
			if(!isset($_POST['all_checkbox'])){
				$folders = isset($_POST['folders'])?$_POST['folders']:array();
				$obj_folders->insertFolders($folders);
			}
			$ajax =  $obj_folders->getAjaxPost();
			echo($ajax);
		}
		exit;
	}

	public function ajaxfoldersAction(){
		if(isset($_POST['ajax']) && !empty($_POST['folder_name'])) {
			$obj_folders = new Folders();
			if($obj_folders->insertFolder($_POST['folder_name'])){
				echo($obj_folders->getFolders());
			}
		}
		exit;
	}

	public function dcommentAction(){
		$id = $this->getParams('comment_id');
		if($id){
			$this->_db_user->deleteComment($id);
		}
		$this->headerLocation('index');
	}

	public function conclusionAction(){
		if(isset($_POST['updateConclusion'])){
			$this->_db_user->updateConclusion(strip_tags($_POST['conclusion']),$this->_id_user);
		}
		if($this->getParams('delete')=='true'){
			$this->_db_user->updateConclusion('',$this->_id_user);
		}

		$this->headerLocation('index/#concl');
	}

	public  function indexAction(){
		$widget = array('widget' => 'index/helpers/widget_personal');

		if($this->_admin){
			$this->_db_user->viewAdmin($this->_id_user);
			$widget = array('widget'=>'index/helpers/widget_administrator');
		}

		$select_personal_data = $this->_db_user->selectPersonalData($this->_id_user);

		if($this->_admin==='main' || $this->_admin==='manager'){
			$select_personal_data = @array_merge((array)$select_personal_data,(array)$this->_db_user->selectCommits($this->_id_user));
		}

		if($select_personal_data !== false) {
			return $this->_view->render(array(
				'view' => 'index/index',
				'data' =>@array_merge(array(
						'helpers'=> $widget,
						'id_user'=>$this->getSessionUserID('user'),
						'id_admin'=>$this->getSessionUserID('id_user_admin'),
						'admin'=>$this->_admin,
						'type_admin_rus'=>$this->_type_admin),
					((array)$select_personal_data)
				),
				'js'=>$this->_jsIndex()
			));
		}
		$this->headerLocation('error');
	}

	public function commentAction(){
		if(isset($_POST['addComment']) && !empty($_POST['comment'])){
			$this->_db_user->addComment($_POST['comment'],$this->_id_user, $this->getSessionUserID('id_user_admin'));
		}
		$this->headerLocation('index');
	}

	public function deleteAction(){
		$this->_db_user->deleteResume($this->_id_user);
		if($this->_admin){
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

	private function _jsIndex(){
		return array(
			'src'=>array(
				BASE_URL."/public/js/jquery-2.1.1.min.js",
				BASE_URL."/public/js/jquery.validate.min.js",
				BASE_URL."/public/js/resume.js"
			),
		);
	}

}