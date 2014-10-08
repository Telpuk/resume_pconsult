<?php
	class SideController extends IController{
		private $_view;
		private $_dbuser;

		public function __construct ()
		{
			parent::__construct ();
			$this->_view   = new View();
			$this->_dbuser = new User();
		}

		public function positionAction(){
			if(isset($_POST['savePosition'])){
				$checkForm = $this->_checkFormPosition($_POST);
				foreach($checkForm as $input){
					if(isset($input['val']['message']) || $input['val']===false){
						return $this->_view->render(array(
							'view' => 'side/position',
							'data'=>array('inputs'=>$checkForm),
							'js'=>$this->_jsPosition(),
						));
					}
				}
				$this->_dbuser->updatePosition($checkForm, $this->getSessionUserID('user'));
				$this->headerLocation('index');
			}else{
				$checkForm = $this->_dbuser->selectPosition($this->getSessionUserID('user'));
				return $this->_view->render(array(
					'view' => 'side/position',
					'data'=>array('inputs'=>$checkForm),
					'js'=>$this->_jsPosition(),
				));
			}
		}

		private function _checkFormPosition($post){
			$desired_position = isset($post['desired_position'])?trim(strip_tags($post['desired_position'])):'';
			$professional_area = isset($post['professional_area'])?trim(strip_tags($post['professional_area'])):'';
			$salary = isset($post['salary'])?trim(strip_tags($post['salary'])):'';
			$currency = isset($post['currency'])?trim(strip_tags($post['currency'])):'';


			$employment = isset($post['employment'])?$post['employment']:array();
			$schedule = isset($post['schedule'])?$post['schedule']:array();


			$desired_position_val = call_user_func(function($desired_position){
				return !empty($desired_position)?true: array('message'=>'Необходимо заполнить');
			}, $desired_position);

			$professional_area_val = call_user_func(function($professional_area){
				return !empty($professional_area)?true: array('message'=>'Необходимо заполнить');
			}, $professional_area);

			$employment_val = call_user_func(function($employment){
				return count($employment)!==0?true: array('message'=>'Необходимо заполнить');
			}, $employment);

			$schedule_val = call_user_func(function($schedule){
				return count($schedule)!==0?true: array('message'=>'Необходимо заполнить');
			}, $schedule);

			return array(
				'desired_position'=>array(
					'val'=>$desired_position_val,
					'value'=>$desired_position ),
				'professional_area'=>array(
					'val'=>$professional_area_val,
					'value'=>$professional_area
				),
				'employment'=>array(
					'val'=>$employment_val,
					'value'=>$employment),
				'schedule'=>array(
					'val'=>$schedule_val,
					'value'=>$schedule
				),
				'salary'=>array('value'=>$salary),
				'currency'=>array('value'=>$currency),
			);
		}

		private function _jsPosition(){
			return array(
				'src'=>array(
					BASE_URL."/public/js/jquery-2.1.1.min.js",
					BASE_URL."/public/js/jquery.validate.min.js",
					BASE_URL."/public/js/position.js"
				),
			);
		}
	}