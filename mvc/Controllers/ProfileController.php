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

		public function contactsAction(){
			if(isset($_POST['saveContacts'])){
				$checkForm = $this->_checkFormContacts($_POST);
				foreach($checkForm as $input){
					if(isset($input['val']['message']) || $input['val']===false){
						return $this->_view->render(array(
							'view' => 'profile/contacts',
							'data'=>array('inputs'=>$checkForm),
							'js'=>$this->_jsContacts(),
						));
					}
				}
				$this->_dbuser->updateContacts($checkForm, $this->getSessionUserID('user'));
				$this->headerLocation('index');
			}else{
				$checkForm = $this->_dbuser->selectContacts($this->getSessionUserID('user'));
				return $this->_view->render(array(
					'view' => 'profile/contacts',
					'data'=>array('inputs'=>$checkForm),
					'js'=>$this->_jsContacts(),
				));
			}
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
				$checkForm = $this->_checkFormPersonal($_POST);
				foreach($checkForm as $input){
					if(isset($input['val']['message']) || $input['val']===false){
						return $this->_view->render(array(
							'view' => 'profile/personal',
							'data'=>array('inputs'=>$checkForm),
							'js'=>$this->_jsPersonal(),
						));
					}
				}
				$this->_dbuser->updatePersonal($checkForm, $this->getSessionUserID('user'));
				$this->headerLocation('index');
			}else{
				$checkForm = $this->_dbuser->selectPersonal($this->getSessionUserID('user'));
				return $this->_view->render(array(
					'view' => 'profile/personal',
					'data'=>array('inputs'=>$checkForm),
					'js'=>$this->_jsPersonal(),
				));
			}


		}

		private function _checkFormPersonal($post){
			$surname = isset($post['surname'])?trim(strip_tags(mb_eregi_replace('[^A-Za-zА-Яа-яёЁ]','', $post['surname']))):'';
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

			$work_permit = isset($post['work_permit'])?trim(strip_tags($post['work_permit'])):'';

			$travel_time_work = isset($post['travel_time_work'])?trim(strip_tags($post['travel_time_work'])):'';

			$surname_val = call_user_func(function($surname){
				if(empty($surname)){
					return array('message'=>'Необходимо заполнить');
				}elseif(!preg_match('/^[a-zA-Zа-яА-ЯёЁ]+$/ui',$surname)){
					return  array('message'=>'Указано некорректно');
				}else{
					return true;
				}
			}, $surname);

			$first_name_val = call_user_func(function($first_name){
				if(empty($first_name)){
					return array('message'=>'Необходимо заполнить');
				}elseif(!preg_match('/^[a-zA-Zа-яА-ЯёЁ]+$/ui',$first_name)){
					return  array('message'=>'Указано некорректно');
				}else{
					return true;
				}
			}, $first_name);

			$patronymic_val = call_user_func(function($patronymic){
				if(empty($patronymic)){
					return true;
				}elseif(!preg_match('/^[a-zA-Zа-яА-ЯёЁ]+$/ui',$patronymic)){
					return  array('message'=>'Указано некорректно');
				}
			}, $patronymic);

			$birth_val = call_user_func(function($date){
				return (is_numeric($date['day_birth'])&&
					is_numeric($date['month_birth'])&&
					is_numeric($date['year_birth'])) ||
				(empty($date['day_birth'])&& empty($date['month_birth'])&& empty($date['year_birth']))? true: array('message'=>'Некорректная дата');
			}, array('day_birth'=>$day_birth, 'month_birth'=>$month_birth, 'year_birth'=>$year_birth));

			$city_val = call_user_func(function($city){
				return !empty($city) ? true : array('message'=>'Необходимо заполнить');
			},$city);



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
				),
				'city'=>array(
					'val'=>$city_val,
					'value'=>$city
				),
				'sex'=>array('value'=>$sex),
				'move'=>array('value'=>$move),
				'trip'=>array('value'=>$trip),
				'work_permit'=>array('value'=>$work_permit),
				'nationality'=>array('value'=>$nationality),
				'travel_time_work'=>array('value'=>$travel_time_work)
			);

		}

		private function _checkFormContacts($post){
			$mobile_phone = isset($post['mobile_phone'])?trim(strip_tags($post['mobile_phone'])):'';
			$home_phone = isset($post['home_phone'])?trim(strip_tags($post['home_phone'])):'';
			$work_phone = isset($post['work_phone'])?trim(strip_tags($post['work_phone'])):'';

			$comment_mobile_phone = isset($post['comment_mobile_phone'])?trim(strip_tags($post['comment_mobile_phone'])):'';
			$comment_home_phone = isset($post['comment_home_phone'])?trim(strip_tags($post['comment_home_phone'])):'';
			$comment_work_phone = isset($post['comment_work_phone'])?trim(strip_tags($post['comment_work_phone'])):'';

			$preferred_communication = isset($post['preferred_communication'])?trim(strip_tags ($post['preferred_communication'])):1;

			$email =  isset($post['email'])?trim(strip_tags($post['email'])):'';

			$icq =  isset($post['icq'])?trim(strip_tags($post['icq'])):'';
			$skype =  isset($post['skype'])?trim(strip_tags($post['skype'])):'';
			$free_lance =  isset($post['free_lance'])?trim(strip_tags($post['free_lance'])):'';
			$my_circle =  isset($post['my_circle'])?trim(strip_tags($post['my_circle'])):'';
			$linkedln =  isset($post['linkedln'])?trim(strip_tags($post['linkedln'])):'';
			$facebook =  isset($post['facebook'])?trim(strip_tags($post['facebook'])):'';
			$live_journal =  isset($post['live_journal'])?trim(strip_tags($post['live_journal'])):'';
			$other_site =  isset($post['other_site'])?trim(strip_tags($post['other_site'])):'';



			$mobile_phone_val = call_user_func(function($phone){
				if(empty($phone['phone']) && $phone['preferred_communication'] == 1){
					return  array('message'=>'Необходимо заполнить');
				}elseif(!empty($phone['phone']) &&
					!preg_match('/^((8|\+7)[\- ]?)?(\(?\d{3}\)?[\- ]?)?[\d\- ]{7,10}$/i',
						$phone['phone'])){
					return  array('message'=>'Номер указан некорректно');
				}else{
					return true;
				}
			}, array('phone'=>$mobile_phone, 'preferred_communication'=>$preferred_communication));

			$home_phone_val = call_user_func(function($phone){
				if(empty($phone['phone']) && $phone['preferred_communication'] == 2){
					return  array('message'=>'Необходимо заполнить');
				}elseif(!empty($phone['phone']) &&
					!preg_match('/^((8|\+7)[\- ]?)?(\(?\d{3}\)?[\- ]?)?[\d\- ]{7,10}$/i',
						$phone['phone'])){
					return  array('message'=>'Номер указан некорректно');
				}else{
					return true;
				}

			}, array('phone'=>$home_phone, 'preferred_communication'=>$preferred_communication));

			$work_phone_val = call_user_func(function($phone){
				if(empty($phone['phone']) && $phone['preferred_communication'] == 3){
					return  array('message'=>'Необходимо заполнить');
				}elseif(!empty($phone['phone']) &&
					!preg_match('/^((8|\+7)[\- ]?)?(\(?\d{3}\)?[\- ]?)?[\d\- ]{7,10}$/i',
						$phone['phone'])){
					return  array('message'=>'Номер указан некорректно');
				}else{
					return true;
				}
			}, array('phone'=>$work_phone, 'preferred_communication'=>$preferred_communication));


			$email_val = call_user_func(function($email){
				if(empty($email)){
					return  array('message'=>'Необходимо заполнить');
				}elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)){
					return  array('message'=>'Не корректно указан email');
				}else{
					return true;
				}
			}, $email);



			return array(
				'mobile_phone'=>array(
					'val'=>$mobile_phone_val,
					'value'=>$mobile_phone ),
				'home_phone'=>array(
					'val'=>$home_phone_val,
					'value'=>$home_phone
				),
				'work_phone'=>array(
					'val'=>$work_phone_val,
					'value'=>$work_phone),
				'email'=>array(
					'val'=>$email_val,
					'value'=>$email
				),
				'preferred_communication'=>array('value'=>$preferred_communication),
				'comment_mobile_phone'=>array('value'=>$comment_mobile_phone),
				'comment_home_phone'=>array('value'=>$comment_home_phone),
				'comment_work_phone'=>array('value'=>$comment_work_phone),
				'icq'=>array('value'=>$icq),
				'skype'=>array('value'=>$skype),
				'free_lance'=>array('value'=>$free_lance),
				'my_circle'=>array('value'=>$my_circle),
				'linkedln'=>array('value'=>$linkedln),
				'facebook'=>array('value'=>$facebook),
				'live_journal'=>array('value'=>$live_journal),
				'other_site'=>array('value'=>$other_site),
			);

		}


		private function _uploadsAction(){
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

		private function _jsContacts(){
			return array(
				'src'=>array(
					BASE_URL."/public/js/jquery-2.1.1.min.js",
					BASE_URL."/public/js/jquery.validate.min.js",
					BASE_URL."/public/js/contacts.js"
				),
			);
		}

	}


