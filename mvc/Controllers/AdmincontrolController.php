<?php
class AdminControlController extends IController{
	private $_view,
		$_db_admin,
		$_db_user,
		$_count_view = 10,
		$_page = null;

	public function  __construct(){
		parent::__construct();
		$this->_view = new View();
		$this->_db_admin = new Admin();
		$this->_db_user = new User();

		if($this->getSessionUserID('user')){
			$this->sessionDeleteIdUser('user');
		}
	}

	public function delmanagerAction(){
		$id = $this->getParams('delete');
		if($id){
			if($this->_db_admin->deleteManager($id)){
				$this->headerLocation("admincontrol/managers");
			}
		}
	}

	public function dcommentAction(){
		if(isset($_POST['id'])){
			$this->_db_user->deleteComment((int)$_POST['id']);
		}
		exit;
	}

	public function addcommentAction(){
		if(isset($_POST['comment']) && isset($_POST['id_user'])){
			$this->_db_user->addComment($_POST['comment'], $_POST['id_user'],$this->getSessionUserID('id_user_admin'));
		}
		exit;
	}

	public function getcommentAction(){
		if(isset($_POST['id'])){
			echo($this->_db_admin->getCommentUser((int)$_POST['id'], $this->getSessionUserID('id_user_admin')));
		}
		exit;
	}

	public function conclusionAction(){

		if(isset($_POST['updateConclusion'])){
			$this->_db_user->updateConclusion(strip_tags($_POST['conclusion']), $_POST['id_user']);
		}
		echo "true";
		exit;
	}

	public function upadminAction(){
		if(isset($_POST['submitUpdateAdminInfo']) && !empty($_POST['login']) && !empty($_POST['name_first'])){
			$result = $this->_db_admin->upadmin($_POST, $this->getSessionUserID('id_user_admin'));

			if(is_array($result)){

				$this->setSessionParams(array(
					'name_first' => $result[':name_first'],
					'name_second'=>$result[':name_second'],
					'patronymic'=>$result[':patronymic'],
					'login'=>$result[':login'],
				));
			}
		}
		$this->headerLocation('admincontrol');
	}

	public function upadminajaxAction(){
		if(isset($_POST['ajax']) && !empty($_POST['admin_info']['login']) && !empty($_POST['admin_info']['name_first'])){
			$result = $this->_db_admin->upadmin($_POST['admin_info'], $this->getSessionUserID('id_user_admin'));

			if(is_array($result)){

				$this->setSessionParams(array(
					'name_first' => $result[':name_first'],
					'name_second'=>$result[':name_second'],
					'patronymic'=>$result[':patronymic'],
					'login'=>$result[':login'],
				));
			}
			echo true;
		}
		exit;
	}
	public function ajaxfoldersusersAction(){
		if(isset($_POST['ajax'])) {
			$obj_folders = new Folders($this->getSessionUserID('user'));
			if(!isset($_POST['all_checkbox'])){
				$folders = isset($_POST['folders'])?$_POST['folders']:array();
				$obj_folders->insertFolders($folders);
			}
			$ajax =  $obj_folders->getAjaxPost();
			print_r($ajax);
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


	public function foldersAction(){
		$data_search = null;
		$this->writeCurrentUrlCookies($this->getCurrentUrl());

		$search = isset($_GET['search']) ? explode('/', $_GET['search']) : array();

		if ( isset( $search[0] ) ) {
			$search[0] = trim( strip_tags( preg_replace( '/\s{2,}/', ' ', $search[0] ) ) );
			$data_search = $search[0];
			$this->writeSearchCookies( $data_search );
		} else {
			$this->writeSearchCookies( null );
		}


		if($this->getParams('delete')){
			$this->_db_admin->deleteFolder($this->getParams('delete'));
			$this->headerLocation('admincontrol/folders');
		}


		if (isset($search[0]) && !empty($search[0])) {
			$page = $this->getParams('page');
			$this->_page = empty($page)?null:$page;

			$folder_users = $this->_db_admin->searchFolderUsersProcedure($this->getParams('id'),$search[0], $this->_count_view, $this->_page);
			$count =$this->getSessionParamsId('count_users_folders_search');
			$message =  "Найденных анкет: ".$count;
		}elseif(!isset($search[0]) || (isset($search[0]) && empty($search[0]) && $this->getParams('id'))) {
			$page = $this->getParams('page');
			$this->_page = empty($page)?null:$page;
			$folder_users  = $this->_db_admin->selectFolderUsersProcedure($this->getParams('id'), $this->_count_view, $this->_page);
			$count = $this->getSessionParamsId('count_users_folders');
			$message =  "Количество анкет: ".$count;
		}

		$folders_list  = $this->_db_admin->selectFolders();


		return $this->_view->render(array(
			'view' => 'admin_control/folders',
			'data' => array(
				'id_folders_active'=>$this->getParams('id'),
				'admin'=>$this->getSessionUserID('admin'),
				'admin_info'=>array(
					'name_first'=>$this->getSessionParamsId('name_first'),
					'name_second'=>$this->getSessionParamsId('name_second'),
					'patronymic'=>$this->getSessionParamsId('patronymic'),
					'login'=>$this->getSessionParamsId('login'),
					'type_admin_widget'=>$this->getSessionParamsId('type_admin_widget')
				),
				'users'=> $folder_users['users'],
				'count_users'=>$count,
				'message'=>$message,
				'search_folders_user'=>isset($search[0])?$search[0]:null,
				'helpers' => array(
					'header'=> 'admin_control/helpers/header',
					'admin_info_widget'=> 'admin_control/helpers/admin_info_widget',
					'widget_admin' => 'admin_control/helpers/widget',
					'widget_folders'=>'admin_control/helpers/widget_folders'),
				'folder'=>true,
				'folders'=>$folders_list,
				'users_count'=>$this->getSessionParamsId('count_users'),
				'count_view_admin_resume'=>$this->getSessionParamsId('count_view_admin_resume'),
				'pagination' => $this->_db_admin->printPagination(
					ceil($count / $this->_count_view), $this->_page, array(
						'url'=>"folders/id/{$this->getParams('id')}/search/?search=".(isset($search[0])?$search[0]:null))),
			),
			'js' => $this->_jsFolders( $data_search )
		));
	}

	public function addmanagerAction(){

		$managers = $this->_db_admin->selectManagers();

		if($_POST['addManager']){
			$inputs = $this->_checkFormManager($_POST);
			foreach($inputs as $value) {
				if (isset($value['val']['message'])) {
					return $this->_view->render(array(
						'view'=>'admin_control/managers',
						'data' => array(
							'admin'=>$this->getSessionUserID('admin'),
							'admin_info'=>array(
								'name_first'=>$this->getSessionParamsId('name_first'),
								'name_second'=>$this->getSessionParamsId('name_second'),
								'patronymic'=>$this->getSessionParamsId('patronymic'),
								'login'=>$this->getSessionParamsId('login'),
								'type_admin_widget'=>$this->getSessionParamsId('type_admin_widget')
							),
							'active_manager'=>true,
							'managers'=>$managers,
							'inputs'=>$inputs,
							'helpers' => array(
								'widget_admin' => 'admin_control/helpers/widget',
								'admin_info_widget' => 'admin_control/helpers/admin_info_widget',
							),
							'users_count'=>$this->getSessionParamsId('count_users'),
							'count_view_admin_resume'=>$this->getSessionParamsId('count_view_admin_resume')
						),
						'js'=>$this->_jsManager()
					));
				}
			}
			if (!$this->_db_admin->insertManager($inputs)) {
				return $this->_view->render(array(
					'view'=>'admin_control/managers',
					'data' => array(
						'admin'=>$this->getSessionUserID('admin'),
						'admin_info'=>array(
							'name_first'=>$this->getSessionParamsId('name_first'),
							'name_second'=>$this->getSessionParamsId('name_second'),
							'patronymic'=>$this->getSessionParamsId('patronymic'),
							'login'=>$this->getSessionParamsId('login'),
							'type_admin_widget'=>$this->getSessionParamsId('type_admin_widget')
						),
						'active_manager'=>true,
						'managers'=>$managers,
						'inputs'=>$inputs,
						'helpers' => array(
							'widget_admin' => 'admin_control/helpers/widget',
							'admin_info_widget' => 'admin_control/helpers/admin_info_widget',
							'message'=>'admin_control/helpers/exists_login'
						),
						'users_count'=>$this->getSessionParamsId('count_users'),
						'count_view_admin_resume'=>$this->getSessionParamsId('count_view_admin_resume')
					),
					'js'=>$this->_jsManager()
				));
			}
		}
		$this->headerLocation('admincontrol/managers');

	}


	private function _checkFormManager($inputs){
		$name_first = strip_tags(trim($inputs['name_first']));
		$name_first_val = call_user_func(function($var){
			if(empty($var)){
				return  array('message'=>'Необходимо заполнить');
			}elseif(!preg_match('/^[a-zA-Zа-яА-ЯёЁ\s]{2,}+$/ui',$var)){
				return  array('message'=>'Указано некорректно');
			}
		}, $name_first);

		$login_manager = strip_tags(trim($inputs['login_manager']));
		$login_manager_val = call_user_func(function($var){
			if(empty($var)){
				return  array('message'=>'Необходимо заполнить');
			}elseif(!preg_match('/^[a-zA-Z][a-zA-Z0-9-_\.\@]{3,60}$/',$var)){
				return  array('message'=>'Указано некорректно(мининимум 4 символа, на латинице)');
			}
		}, $login_manager);

		$password_manager = strip_tags(trim($inputs['password_manager']));
		$password_manager_val = call_user_func(function($var){
			if(empty($var)){
				return  array('message'=>'Необходимо заполнить');
			}elseif(!preg_match('/^[_a-zA-Z0-9-_\.\$\!\@\#]{4,20}$/',$var)){
				return  array('message'=>'Указано некорректно (пример: parol123)');
			}
		}, $password_manager);

		return array(
			'name_first'=>array(
				'val'=>$name_first_val,
				'value'=>$name_first
			),
			'login_manager'=>array(
				'val'=>$login_manager_val,
				'value'=>$login_manager
			),
			'password_manager'=>array(
				'val'=>$password_manager_val,
				'value'=>$password_manager
			)
		);
	}

	public function managersAction(){
		$managers = $this->_db_admin->selectManagers();

		return $this->_view->render(array(
			'view'=>'admin_control/managers',
			'data' => array(
				'admin'=>$this->getSessionUserID('admin'),
				'admin_info'=>array(
					'name_first'=>$this->getSessionParamsId('name_first'),
					'name_second'=>$this->getSessionParamsId('name_second'),
					'patronymic'=>$this->getSessionParamsId('patronymic'),
					'login'=>$this->getSessionParamsId('login'),
					'type_admin_widget'=>$this->getSessionParamsId('type_admin_widget')
				),
				'active_manager'=>true,
				'managers'=>$managers,
				'helpers' =>array(
					'header'=> 'admin_control/helpers/header',
					'admin_info_widget'=> 'admin_control/helpers/admin_info_widget',
					'widget_admin' => 'admin_control/helpers/widget'
				),
				'users_count'=>$this->getSessionParamsId('count_users'),
				'count_view_admin_resume'=>$this->getSessionParamsId('count_view_admin_resume')
			),
			'js'=>$this->_jsManager()
		));
	}

	public function unreviewedAction(){
		$this->writeCurrentUrlCookies($this->getCurrentUrl());

		if ($this->getParams('view')) {
			$page = $this->getParams('page');
			$this->_page = empty($page)?null:$page;

			$users = $this->_db_admin->selectResumeNoView( $this->_count_view, $this->_page, (int)$this->getSessionUserID( 'id_user_admin' ) );

			$users['count'] = $this->getSessionParamsId('count_view_admin_resume');

			return $this->_view->render(array(
				'view' => 'admin_control/index',
				'js'=>$this->_jsAdminControl(),
				'data' => array(
					'admin'=>$this->getSessionUserID('admin'),
					'admin_info'=>array(
						'name_first'=>$this->getSessionParamsId('name_first'),
						'name_second'=>$this->getSessionParamsId('name_second'),
						'patronymic'=>$this->getSessionParamsId('patronymic'),
						'login'=>$this->getSessionParamsId('login'),
						'type_admin_widget'=>$this->getSessionParamsId('type_admin_widget')
					),
					'helpers' => array(
						'header'=> 'admin_control/helpers/header',
						'admin_info_widget'=> 'admin_control/helpers/admin_info_widget',
						'widget_admin' => 'admin_control/helpers/widget'
					),
					'active_all_no_view'=>true,
					'users' => $users['users'] ? $users['users']  : '',
					'users_count'=>$this->getSessionParamsId('count_users'),
					'count_view_admin_resume'=>$this->getSessionParamsId('count_view_admin_resume'),
					'pagination' => $this->_db_admin->printPagination(
						ceil($users['count'] / $this->_count_view), $this->_page, array(
							'url'=>"unreviewed/view/".$this->getParams('view')))
				)
			));
		}
		$this->headerLocation('admincontrol');
	}


	public function indexAction(){
		$data_search = null;
		$this->writeCurrentUrlCookies($this->getCurrentUrl());

		$search = isset($_GET['search']) ? explode('/', $_GET['search']) : array();
		if ( isset( $search[0] ) ) {
			$search[0] = trim( strip_tags( preg_replace( '/\s{2,}/', ' ', $search[0] ) ) );
			$data_search = $search[0];
			$this->writeSearchCookies( $data_search );
		} else {
			$this->writeSearchCookies( null );
		}

		if (isset($search[0]) && !empty($search[0])) {
			$page = $this->getParams('page');
			$this->_page = empty($page)?null:$page;

			$users = $this->_db_admin->search($search[0], $this->_count_view, $this->_page);
			$users['count'] = $this->getSessionParamsId('count_users_search');

		}elseif(!isset($search[0]) || (isset($search[0]) && empty($search[0]))) {
			$page = $this->getParams('page');
			$this->_page = empty($page)?null:$page;

			$users = $this->_db_admin->selectAllResume( $this->_count_view, $this->_page, (int)$this->getSessionUserID( 'id_user_admin' ) );
			$users['count'] = $this->getSessionParamsId('count_users');
			$this->deleteSessionParamsId('count_users_search');
		}

		return $this->_view->render(array(
			'view' => 'admin_control/index',
			'js' => $this->_jsAdminControl( $data_search ),
			'styles' => array(
				'styleCode' => 'sdfds',
			),
			'data' => array(
				'admin'=>$this->getSessionUserID('admin'),
				'admin_info'=>array(
					'name_first'=>$this->getSessionParamsId('name_first'),
					'name_second'=>$this->getSessionParamsId('name_second'),
					'patronymic'=>$this->getSessionParamsId('patronymic'),
					'login'=>$this->getSessionParamsId('login'),
					'type_admin_widget'=>$this->getSessionParamsId('type_admin_widget')
				),
				'helpers' => array(
					'header'=> 'admin_control/helpers/header',
					'admin_info_widget'=> 'admin_control/helpers/admin_info_widget',
					'widget_admin' => 'admin_control/helpers/widget'
				),
				'active_all_resume'=>true,
				'users' => $users['users'] ? $users['users']  : '',
				'users_count' =>$this->getSessionParamsId('count_users'),
				'users_count_search'=>$this->getSessionParamsId('count_users_search'),
				'count_view_admin_resume'=>$this->getSessionParamsId('count_view_admin_resume'),
				'search' => isset($search[0])?$search[0]:null,
				'pagination' => $this->_db_admin->printPagination(
					ceil($users['count'] / $this->_count_view), $this->_page, array(
						'url'=>"index/search/?search=".(isset($search[0])?$search[0]:null))),
			)
		));

	}


	private function _jsAdminControl( $data = null )
	{
		return array(
			'javascriptFooter' => array(
				'src' => array(
					BASE_URL . "/public/js/jquery-2.1.1.min.js",
					BASE_URL . "/public/js/handlebars-v2.0.0.js",
					BASE_URL . "/public/js/vendor/highlight.js",
					BASE_URL . "/public/js/vendor/jquery.easing.1.3.min.js",
					BASE_URL . "/public/js/admincontrol.js",
				),
				'js_c' => is_null( $data ) || !$data ? null : '(function($){$(".person_inform, .conclusion").highlight("' . $data . '");})($)'
			)
		);
	}

	private function _jsManager(){
		return array(
			'javascriptFooter' => array(
				'src' => array(
					BASE_URL . "/public/js/jquery-2.1.1.min.js",
					BASE_URL . "/public/js/jquery.validate.min.js",
					BASE_URL . "/public/js/vendor/jquery.easing.1.3.min.js",
					BASE_URL . "/public/js/manager.js"
				),
			)
		);
	}

	private function _jsFolders( $data = null )
	{
		return array(
			'javascriptFooter' => array(
				'src' => array(
					BASE_URL . "/public/js/jquery-2.1.1.min.js",
					BASE_URL . "/public/js/handlebars-v2.0.0.js",
					BASE_URL . "/public/js/vendor/jquery.easing.1.3.min.js",
					BASE_URL . "/public/js/vendor/highlight.js",
					BASE_URL . "/public/js/folders.js"
				),
				'js_c' => is_null( $data ) || !$data ? null : '(function($){$(".person_inform, .conclusion").highlight("' . $data . '");})($)'
			)
		);
	}

}