<?php
	class ProfileController extends IController
	{
		private $_view;
		private $_format_photo = ['png'=>'png','jpg'=>'jpg','gif' =>'gif'];
		private $_size_photo = 6291456;

		public function __construct(){
			parent::__construct();
			$this->_view = new View();
		}

		private function _getDownloadDirPhoto(){
			return DIR_PROJECT.'/files/photo';
		}

		public function photoAction(){

			return $this->_view->render (array(
				'view' => 'profile/photo',
				'photo' => $this->_uploadsAction()
			));
		}

		public function personalAction(){
			return $this->_view->render (array(
				'view' => 'profile/personal',
				'js'=>$this->_jsPersonal(),
			));
		}

		protected function _uploadsAction(){
			if(isset($_POST['submit_download']) &&
				isset($_FILES['photo']) && !is_uploaded_file($_FILES['photo']['tmp_name']) && !$_FILES['photo']['error']){
				 return $this->_checkFilesFormatAndSize($_FILES['photo']);
			}else{
				return ['photo_empty'=>true];
			}
		}

		private function _checkFilesFormatAndSize($photo){
			if(!isset($this->_format_pict[mb_strtolower(substr(strrchr($photo['name'], '.'), 1), "utf-8")])){
				return ['photo_format'=> false];
			}
			if($photo['size'] <= 6291456){
				return ['photo_size'=> false];
			}
			return ['photo_format'=>true];
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


