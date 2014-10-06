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
			$_checkForm = array();
			if(isset($_POST['savePersonal'])){
				$_checkForm = $this->_checkFormPersonal($_POST);

				foreach($_checkForm as $input){
					if(!$input['val']){
						break;
					}
				}
			}

			return $this->_view->render(array(
				'view' => 'profile/personal',
				'data'=>array('inputs'=>$_checkForm),
				'js'=>$this->_jsPersonal(),
			));
		}

		protected function _checkFormPersonal($post){
			$surname = isset($post['surname'])?trim(strip_tags(preg_replace('%[^A-Za-zА-Яа-я]%','',$post['surname']))):null;
			$first_name = isset($post['first_name'])?trim(strip_tags(preg_replace('%[^A-Za-zА-Яа-я]%','', $post['first_name']))):null;
			$patronymic = isset($post['patronymic'])?trim(strip_tags(preg_replace('%[^A-Za-zА-Яа-я]%','', $post['patronymic']))):null;

			$day_birth = isset($post['day_birth'])?trim(strip_tags($post['day_birth'])):null;
			$month_birth = isset($post['month_birth'])?trim(strip_tags($post['month_birth'])):null;
			$year_birth = isset($post['year_birth'])?trim(strip_tags($post['year_birth'])):null;

			$sex =  isset($post['sex'])?trim(strip_tags($post['sex'])):null;
			$city = isset($post['city'])?trim(strip_tags(preg_replace('%[^A-Za-zА-Яа-я]%','',$post['city']))):null;

			$move = isset($post['move'])?trim(strip_tags($post['move'])):null;
			$trip = isset($post['trip'])?trim(strip_tags($post['trip'])):null;

			$nationality = isset($post['nationality'])?trim(strip_tags($post['nationality'])): null;
			$nationality_other = isset($post['nationality_other'])?trim(strip_tags($post['nationality_other'])):null;

			$work_permit = isset($post['work_permit'])?trim(strip_tags($post['work_permit'])):null;
			$work_permit_other = isset($post['$work_permit_other'])?trim(strip_tags($post['work_permit_other'])):null;

			$travel_time_work = isset($post['travel_time_work'])?trim(strip_tags($post['travel_time_work'])):null;


			$surname_val = call_user_func(function($surname){
				return preg_match("/^[a-zA-Z ]*$/",$surname)&&!empty($surname) ? true: false;
			}, $surname);

			$first_name_val = call_user_func(function($first_name){
				return preg_match("/^[a-zA-Z ]*$/",$first_name)&&!empty($first_name) ? true: false;
			}, $first_name);

			$patronymic_val = call_user_func(function($patronymic){
				return preg_match("/^[a-zA-Z ]*$/",$patronymic)&&!empty($patronymic) ? true: false;
			}, $patronymic);


			return array(
				'name'=>array(
					'val'=>$surname_val,
					'value'=>$surname),
				'first_name'=>array(
					'val'=>$first_name_val,
					'value'=>$first_name
				),
				'patronymic'=>array(
					'val'=>$patronymic_val,
					'value'=>$patronymic)
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


