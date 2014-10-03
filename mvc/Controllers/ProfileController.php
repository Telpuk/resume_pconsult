<?php
	class ProfileController extends IController
	{
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



			return $this->_view->render(array(
				'view' => 'profile/photo',
				'data'=> $this->_uploadsAction()
			));
		}

		public function personalAction(){
			return $this->_view->render(array(
				'view' => 'profile/personal',
				'js'=>$this->_jsPersonal(),
			));
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
						return array('src'=> $this->_movePhotoDir($_FILES['photo']));
					}
				}else{
					return array('helpers'=>array('empty_photo'=>'profile/helpers/empty_photo'));
				}
			}
		}

		private function _movePhotoDir($photo){
			if(isset($photo['name'])){
				$file_name = $this->getSessionUserID('user').'.'.substr(strrchr($photo['name'], '.'), 1);
				if(move_uploaded_file ($photo['tmp_name'], $this->_getDownloadDirPhoto() . "/{$file_name}")){
					$this->_dbuser->updatePhotoId($file_name, $this->getSessionUserID('user'));
					return BASE_URL."/files/photo/{$file_name}";
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


