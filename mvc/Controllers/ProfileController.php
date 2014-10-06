<?php
class ProfileController extends IController{
	private $_view;
	private $_dbuser;
	private $_format_photo = ['png'=>'png','jpg'=>'jpg','gif' =>'gif'];
	private $_size_photo = 6291456;

	public function __construct(){
		parent::__construct();
		$this->_view = new View();
		$this->_dbuser = new User();
	}

	private function _getDownloadDirPhoto(){
		return DIR_PROJECT.'/files/photo';
	}

	public function photoAction(){
		$photo_name = $this->_dbuser->selectPhotoID($this->getSessionUserID('user'));

		return $this->_view->render(array(
			'view' => 'profile/photo',
			'data'=> array_merge( array(
					'src'=>"/files/photo/{$photo_name}",
					'submit'=>($photo_name === 'no-photo.png') ? true: false),
				$this->_uploadsAction())
		));
	}

	public function deleteAction(){
		$this->_dbuser->updatePhotoId('no-photo.png', $this->getSessionUserID('user'));
		@unlink($this->_getDownloadDirPhoto()."/".$this->getParams()['photo']);
		$this->headerLocation('profile/photo');

	}

	public function personalAction(){

		if(isset($_POST['savePersonal'])){
			$_checkForm = $this->_checkFormPersonal($_POST);
			foreach($_checkForm as $input){
				if(!$input['val']){
					break;
				}
			}
		}else{
			$_checkForm = array();
		}

		return $this->_view->render(array(
			'view' => 'profile/personal',
			'data'=>array('inputs'=>$_checkForm),
			'js'=>$this->_jsPersonal(),
		));
	}

	protected function _checkFormPersonal($post){
		$surname = isset($post['surname'])?trim(strip_tags(mb_eregi_replace('[^A-Za-zА-Яа-яёЁ]','',
			$post['surname']))):'';
		$first_name = isset($post['first_name'])?trim(strip_tags(mb_eregi_replace('[^A-Za-zА-Яа-яёЁ]','', $post['first_name']))):'';
		$patronymic = isset($post['patronymic'])?trim(strip_tags(mb_eregi_replace('[^A-Za-zА-Яа-яёЁ]','', $post['patronymic']))):'';

		$day_birth = isset($post['day_birth'])?trim(strip_tags($post['day_birth'])):'';
		$month_birth = isset($post['month_birth'])?trim(strip_tags($post['month_birth'])):'';
		$year_birth = isset($post['year_birth'])?trim(strip_tags($post['year_birth'])):'';

		$sex =  isset($post['sex'])?trim(strip_tags($post['sex'])):'';
		$city = isset($post['city'])?trim(strip_tags(mb_eregi_replace('[^A-Za-zА-Яа-я]','',$post['city']))):'';

		$move = isset($post['move'])?trim(strip_tags($post['move'])):'';
		$trip = isset($post['trip'])?trim(strip_tags($post['trip'])):'';

		$nationality = isset($post['nationality'])?trim(strip_tags($post['nationality'])): '';
		$nationality_other = isset($post['nationality_other'])?trim(strip_tags($post['nationality_other'])):'';

		$work_permit = isset($post['work_permit'])?trim(strip_tags($post['work_permit'])):'';
		$work_permit_other = isset($post['$work_permit_other'])?trim(strip_tags($post['work_permit_other'])):'';

		$travel_time_work = isset($post['travel_time_work'])?trim(strip_tags($post['travel_time_work'])):'';


		$surname_val = call_user_func(function($surname){
			return preg_match('/^(?:[a-zA-Z0-9_()\s]+)|(?:[а-яА-ЯуёЁ0-9_()\s]+)$/',$surname)&&!empty($surname) ? true:
			false;
		}, $surname);

		$first_name_val = call_user_func(function($first_name){
			return preg_match('/^(?:[a-zA-Z0-9_()\s]+)|(?:[а-яА-ЯуёЁ0-9_()\s]+)$/',$first_name)&&!empty($first_name) ?
				true: false;
		}, $first_name);

		$patronymic_val = call_user_func(function($patronymic){
			return preg_match('/^(?:[a-zA-Z0-9_()\s]+)|(?:[а-яА-ЯуёЁ0-9_()\s]+)$/',$patronymic)&&!empty($patronymic)
				? true: false;
		}, $patronymic);

		$birth_val = call_user_func(function($date){
			return (is_numeric($date['day_birth'])&&
				is_numeric($date['month_birth'])&&
				is_numeric($date['year_birth'])) ||
			(empty($date['day_birth'])&& empty($date['month_birth'])&& empty($date['year_birth']))? true: false;
		}, array('day_birth'=>$day_birth, 'month_birth'=>$month_birth, 'year_birth'=>$year_birth));

		//echo $birth_val;
		return array(
			'surname'=>array(
				'val'=>$surname_val,
				'value'=>$surname),
			'first_name'=>array(
				'val'=>$first_name_val,
				'value'=>$first_name
			),
			'patronymic'=>array(
				'val'=>$patronymic_val,
				'value'=>$patronymic),
			'birth'=>array(
				'val'=>$birth_val,
				'day_birth'=>$day_birth,
				'month_birth'=>$month_birth,
				'year_birth'=>$year_birth
			)
		);

	}

	protected function _uploadsAction(){
		if(isset($_POST['submit_download'])){
			if(isset($_FILES['photo']) && is_uploaded_file($_FILES['photo']['tmp_name']) && !$_FILES['photo']['error']){
				$photo = $this->_checkFilesFormatAndSize($_FILES['photo']);
				if(isset($photo['photo_format']) || isset($photo['photo_size'])){
					switch($photo){
						case isset($photo['photo_format']):{
							return array('helpers'=>array('format_photo'=>'profile/helpers/format_photo'));
							break;
						}
						case isset($photo['photo_size']):{
							return array('helpers'=>array('photo_size'=>'profile/helpers/photo_size'));
							break;
						}
					}
				}else{
					$this->_movePhotoDir($_FILES['photo']);
					$this->headerLocation('profile/photo');
				}
			}else{
				return array('helpers'=>array('empty_photo'=>'profile/helpers/empty_photo'));
			}
		}else{
			return array();
		}
	}

	private function _movePhotoDir($photo){
		if(isset($photo['name'])){
			$file_name = $this->getSessionUserID('user').'.'.substr(strrchr($photo['name'], '.'), 1);
			if(move_uploaded_file ($photo['tmp_name'], $this->_getDownloadDirPhoto() . "/{$file_name}")){
				$this->_dbuser->updatePhotoId($file_name, $this->getSessionUserID('user'));
			}
		}
	}

	private function _checkFilesFormatAndSize($photo){
		if(!isset($this->_format_photo[mb_strtolower(substr(strrchr($photo['name'], '.'), 1), "utf-8")])){
			return array('photo_format'=> true);
		}
		if($photo['size'] > $this->_size_photo){
			return array('photo_size'=> true);
		}
		return true;
	}

	private function _jsPersonal(){
		return array(
			'src'=>array(
				BASE_URL."/public/js/jquery-2.1.1.min.js",
				BASE_URL."/public/js/jquery.validate.min.js",
				BASE_URL."/public/js/personal.js"
			),
		);

	}



}


