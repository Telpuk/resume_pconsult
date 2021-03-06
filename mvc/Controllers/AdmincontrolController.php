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

	public function updatecommentAction(){
		if(isset($_POST['content'])&&isset($_POST['id_com'])&&!empty($_POST['id_com'])&&!empty($_POST['content'])) {
			if($this->_db_user->updateComment($_POST['id_com'], $_POST['content']))
				echo'true';
			else
				echo'false';
		}
		exit;
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
		}else{
			$this->headerLocation('error');
		}
		exit;
	}
	public function ajaxfoldersAction(){
		if(isset($_POST['ajax']) && !empty($_POST['folder_name'])) {
			$obj_folders = new Folders();
			if($obj_folders->insertFolder($_POST['folder_name'])){
				echo($obj_folders->getFolders());
			}
		}else{
			$this->headerLocation('error');
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
					'widget_admin' => 'admin_control/helpers/widget',
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

	public function advancedresultAction(){
		if(isset($_POST['advancedForm']) && isset($_POST['advancedForm']['advancedFormSubmit']) || $this->getSessionParamsId('advancedForm')) {

			$this->writeCurrentUrlCookies($this->getCurrentUrl());

			$page = $this->getParams('page');
			$this->_page = empty($page)?null:$page;

			if(isset($_POST['advancedForm']) && isset($_POST['advancedForm']['advancedFormSubmit'])){
				$this->setSessionParams(array('advancedForm'=>$_POST['advancedForm']));
			}

			$users = $this->_db_admin->advancedQuery($this->getSessionParamsId('advancedForm'), $this->_count_view, $this->_page);
			$users['count'] = $this->getSessionParamsId('count_advanced_result');

			$form = $this->getSessionParamsId('advancedForm');

			$input =  empty($form['wordKey']['input'])?null:$form['wordKey']['input'];
			$this->writeSearchCookies($input);

			return $this->_view->render(array(
				'view' => 'admin_control/index',
				'js' => $this->_jsAdminControl($input),
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
						'widget_admin' => 'admin_control/helpers/widget',
						'linkChangeAdvanced'=>'admin_control/helpers/linkChangeAdvanced'
					),
					'active_all_resume'=>true,
					'users' => $users['users'] ? $users['users']  : '',
					'users_count' =>$this->getSessionParamsId('count_users'),
					'users_count_search'=>$this->getSessionParamsId('count_advanced_result'),
					'count_view_admin_resume'=>$this->getSessionParamsId('count_view_admin_resume'),
					'search' => isset($search[0])?$search[0]:null,
					'pagination' => $this->_db_admin->printPagination(
						ceil($users['count'] / $users['count_view']), $this->_page, array(
							'url'=>"advancedresult")),
				)
			));
		}
	}

	public function autocompleteAction(){
		if($_POST['autocomplete']==='autocomplete'){
			echo($this->_db_user->selectAutocompletePersonal());
		}else {
			$this->headerLocation('index');
		}
	}

	public function treatmentProfessionalArea($data=null){
		$str = null;
		if(!is_null($data)){
			foreach($data as $val){
				if($val) {
					$str .= <<<HED
<span><span class='closeBlock'<input type="hidden" name="advancedForm[professional_area][]" value="{$val}"></span>{$val}</span>
HED;
				}
			}
		}
		return $str;
	}

	public function treatmentCity($data=null){
		$str = null;
		if(!is_null($data)){
			foreach($data as $val){
				if($val) {
					$str .= <<<HED
	<span>
		<span class="closeBlock" title="удалить">
		<input type="hidden" name="advancedForm[city][]" value="{$val}">
		</span>
		{$val}
	</span>
HED;
				}
			}
		}
		return $str;
	}

	public function advancedAction(){
		$form = array();

		if(!is_null($this->getParams('change'))){
			$form = $this->getSessionParamsId('advancedForm')?$this->getSessionParamsId('advancedForm'):array();
		}
		$form['professional_area'] = $this->treatmentProfessionalArea(isset($form['professional_area'])?$form['professional_area']:null);
		$form['city'] = $this->treatmentCity(isset($form['city']) ?$form['city']:null);

//		print_r($form);


		return $this->_view->render(array(
				'view'=>'admin_control/advanced',
				'js' => $this->_jsAdvanced(),
				'styles'=>$this->_sryleAdvanced(),
				'data' => array(
					'languages'=>$this->_db_user->_getLanguages(),
					'admin'=>$this->getSessionUserID('admin'),
					'admin_info'=>array(
						'name_first'=>$this->getSessionParamsId('name_first'),
						'name_second'=>$this->getSessionParamsId('name_second'),
						'patronymic'=>$this->getSessionParamsId('patronymic'),
						'login'=>$this->getSessionParamsId('login'),
						'type_admin_widget'=>$this->getSessionParamsId('type_admin_widget')
					),
					'form'=>$form,
					'helpers' => array(
						'header'=> 'admin_control/helpers/header',
						'admin_info_widget'=> 'admin_control/helpers/admin_info_widget',
						'widget_admin' => 'admin_control/helpers/widget'
					),
					'users_count' =>$this->getSessionParamsId('count_users'),
					'users_count_search'=>$this->getSessionParamsId('count_users_search'),
					'count_view_admin_resume'=>$this->getSessionParamsId('count_view_admin_resume')
				)
			)
		);
	}

	private function _sryleAdvanced(){
		return array(
			'styleLinks'=>array(
				BASE_URL.'/public/css/advanced.min.css',
				BASE_URL.'/public/css/datetimepicker.min.css'
			)
		);
	}

	public function _jsAdvanced( $data = null ){
		return array(
			'javascriptFooter' => array(
				'src' => array(
					BASE_URL . "/public/js/vendor/jquery-2.1.1.min.js",
					BASE_URL . "/public/js/vendor/handlebars-v2.min.js",
					BASE_URL . "/public/js/vendor/highlight.min.js",
					BASE_URL . "/public/js/vendor/jquery.easing.1.3.min.js",
					BASE_URL . "/public/js/vendor/jquery-ui.min.js",
					BASE_URL . "/public/js/vendor/datetimepicker.min.js",
					BASE_URL . "/public/js/min/admincontrol.min.js",
					BASE_URL . "/public/js/min/advanced.min.js",
				)
			),
			'javascriptHeader' => array(
				'js_c'=><<<HEAD
<script id="template_advanced_language" type="text/x-handlebars-template">
	<li>
		<select name="advancedForm[languages][language_further][]">
		{{#each languages}}
			<option value="{{this}}" {{#ifCond this  'английский' }}selected{{/ifCond}} >{{this}}</option>
		{{/each}}
		</select>
		<select name="advancedForm[languages][language_further_level][]">
			<option value="не имеет значения">Не имеет значения</option>
			<option value="базовые знания">Базовые знания</option>
			<option value="читаю профессиональную литературу">Чтение проф. литературы</option>
			<option value="могу проходить интервью">Может проходить интервью</option>
			<option value="свободно владею">Свободное владение</option>
			<option value="родной язык">Родной язык</option>
		</select><span class="closeBlock">
	</li>
	</script>
HEAD
			)
		);
	}

	private function _jsAdminControl( $data = null )
	{
		return array(
			'javascriptFooter' => array(
				'src' => array(
					BASE_URL . "/public/js/vendor/jquery-2.1.1.min.js",
					BASE_URL . "/public/js/vendor/handlebars-v2.min.js",
					BASE_URL . "/public/js/vendor/highlight.min.js",
					BASE_URL . "/public/js/vendor/jquery.easing.1.3.min.js",
					BASE_URL . "/public/js/min/admincontrol.min.js"
				),
				'js_c' => is_null( $data ) || !$data ? null : '<script type="text/javascript">(function($){$(".person_inform,.conclusion").highlight("' . $data . '");})(jQuery)</script>'
			)
		);
	}

	private function _jsManager(){
		return array(
			'javascriptFooter' => array(
				'src' => array(
					BASE_URL . "/public/js/vendor/jquery-2.1.1.min.js",
					BASE_URL . "/public/js/vendor/jquery.validate.min.js",
					BASE_URL . "/public/js/vendor/jquery.easing.1.3.min.js",
					BASE_URL . "/public/js/min/manager.min.js"
				),
			)
		);
	}

	private function _jsFolders( $data = null )
	{
		return array(
			'javascriptFooter' => array(
				'src' => array(
					BASE_URL . "/public/js/vendor/jquery-2.1.1.min.js",
					BASE_URL . "/public/js/vendor/handlebars-v2.min.js",
					BASE_URL . "/public/js/vendor/jquery.easing.1.3.min.js",
					BASE_URL . "/public/js/vendor/highlight.min.js",
					BASE_URL . "/public/js/min/folders.min.js"
				),
				'js_c' => is_null( $data ) || !$data ? null : '<script type="text/javascript">(function($){$(".person_inform,.conclusion").highlight("' . $data . '");})(jQuery)</script>'
			)
		);
	}

}