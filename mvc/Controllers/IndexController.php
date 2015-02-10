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

		if(($this->_admin === 'main' || $this->_admin === 'manager') && $this->getParams('id')){
			if($this->_db_user->checkId($this->getParams('id'))) {
				$this->_id_user = $this->getParams('id');
				$this->setSessionUsers(array('user' => $this->_id_user));
				return false;
			}else{
				$this->headerLocation('admincontrol');
			}
		}else if(!$this->_admin && !$this->_id_user){
			$this->_db_user->setIdUser();
			$this->_id_user = $this->_db_user->getIdUser();
			$this->setSessionUsers(array('user' => $this->_id_user));
			$this->setSessionParams(array('time_input_user'=>uniqid()));
			return false;
		}else if($this->_admin && !$this->_id_user){
			$this->headerLocation('admincontrol');
		}
		return false;
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

	public function controlyandexdiskAction($type_file = null){
		$diskClient = new YandexDisk('fd628ba2f3ea41f0aab456c8d5b64755');

		if(isset($_POST['uploadFileYandex']['submit']['movie'])){

			if($diskClient->parseArrayMovieFILES()) {
				$fileName = $diskClient->getFileNameUpload();
				if ( $fileName )
					$this->_db_admin->insertMovieNameFileYandex( $fileName, $this->_id_user );
			}else{
				$this->headerLocation('index/index/video/noFormat#noFormatVideo');
			}
		}

		if(isset($_POST['uploadFileYandex']['submit']['audio'])){
			if($diskClient->parseArrayAudioFILES()) {
				$fileName = $diskClient->getFileNameUpload();
				if ( $fileName )
					$this->_db_admin->insertAudioNameFileYandex( $fileName, $this->_id_user );
			}else{
				$this->headerLocation('index/index/audio/noFormat#noFormatAudio');
			}
		}
		$this->headerLocation('index');
	}

	public function uploadfileyandexAction(){
		$diskClient = new YandexDisk('fd628ba2f3ea41f0aab456c8d5b64755');
		if($this->getParams( 'dir' ) && $this->getParams( 'fileName' ) && $this->getParams( 'user_name' ) ) {
			$diskClient->uploadfileyandex( $this->getParams( 'dir' ), $this->getParams( 'fileName' ), $this->getParams( 'user_name' ) );
		}
		$this->headerLocation('index');

	}
	public function deletefileyandexAction(){
		$diskClient = new YandexDisk('fd628ba2f3ea41f0aab456c8d5b64755');
		if($this->getParams( 'dir' ) && $this->getParams( 'fileName' )) {
			if($diskClient->deletefileyandex( $this->getParams( 'dir' ), $this->getParams( 'fileName' ) )){

				if($this->getParams( 'dir' ) === 'movie'){
					$this->_db_admin->deleteMovieNameFileYandex($this->_id_user);
				}

				if($this->getParams( 'dir' ) === 'audio'){
					$this->_db_admin->deleteAudioNameFileYandex($this->_id_user);

				}
			}
		}
		$this->headerLocation('index');

	}

	public  function indexAction(){
		$noNoticeYandexDisk = array();

		$widget = array('widget' => 'index/helpers/widget_personal');

		if($this->_admin){
			$this->_db_user->viewAdmin( (int)$this->_id_user, (int)$this->getSessionUserID( 'id_user_admin' ) );
			$widget = array(
				'widget'=>'index/helpers/widget_administrator',
				'yandexUploadFile'=>'index/helpers/yandexUploadFile'
			);
			if($this->getParams('video') === 'noFormat'){
				$noNoticeYandexDisk['video'] = true;
			}
			if($this->getParams('audio') === 'noFormat'){
				$noNoticeYandexDisk['audio'] = true;
			}

			if($this->getParams('placeYandex') === 'noPlace'){
				$noNoticeYandexDisk['noPlace'] = true;
			}
		}

		$select_personal_data = $this->_db_user->selectPersonalData($this->_id_user);

		if($this->_admin==='main' || $this->_admin==='manager'){
			$select_personal_data = @array_merge((array)$select_personal_data,(array)$this->_db_user->selectCommits($this->_id_user));
		}

		if($select_personal_data !== false) {
			return $this->_view->render(array(
				'view' => 'index/index',
				'data' =>@array_merge(array(
						'helpers'=>$widget,
						'time_input_user'=>$this->getSessionParamsId('time_input_user'),
						'id_user'=>$this->getSessionUserID('user'),
						'currentUrlAdmin'=>$this->readCurrentUrlCookies(),
						'id_admin'=>$this->getSessionUserID('id_user_admin'),
						'noNoticeYandexDisk'=>$noNoticeYandexDisk,
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

	private function _jsIndex(){
		return array(
			'javascriptFooter' => array(
				'src' => array(
					BASE_URL . "/public/js/vendor/jquery-2.1.1.min.js",
					BASE_URL . "/public/js/vendor/jquery.validate.min.js",
					BASE_URL . "/public/js/vendor/highlight.min.js",
					BASE_URL . "/public/js/min/resume.min.js"
				),
				'js_c' => is_null( $this->readSearchCookies() ) ? null : '<script type="text/javascript">(function($){$(".resume_data_left,.conclusion").highlight("' . $this->readSearchCookies() . '");})(jQuery)</script>'
			)
		);
	}

}