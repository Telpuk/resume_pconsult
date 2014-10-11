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

	public function experienceAction(){
		if(isset($_POST['saveExperience'])){
			$checkForm = $this->_checkFormExperience($_POST);
			foreach($checkForm as $inputs){
				$vals = (array)$inputs['val'];
				foreach($vals as $key=>$val) {
					if (array_key_exists ('message', (array)$val) || $key==='message') {
						return $this->_view->render (array (
							'view' => 'side/experience',
							'data' => array (
								'table_count_work' => count($checkForm['organizations']['value']),
								'table_count_recommendations' =>count($checkForm['recommend_name']['value']),
								'inputs'      => $checkForm),
							'js'   => $this->_jsExperience ()
						));
					}
				}
			}
			$this->_dbuser->updateExperience($checkForm, $this->getSessionUserID('user'));
			$this->headerLocation('index');
		}else{
			$checkForm = $this->_dbuser->selectExperience($this->getSessionUserID('user'));
			return $this->_view->render(array(
				'view' => 'side/experience',
				'data'=>array(
					'table_count_work'=>count($checkForm['organizations']['value']),
					'table_count_recommendations' =>count($checkForm['recommend_name']['value']),
					'inputs'=>$checkForm),
				'js'=>$this->_jsExperience()
			));
		}
	}

	private function _checkFormExperience($post){
		//			echo "<pre>";
		//			print_r($post);

		for($i=0, $len=count($post['organizations']); $i< $len; ++$i) {

			$organizations[$i] = trim(strip_tags($post['organizations'][$i]));

			$organizations_val[$i] = call_user_func(function($organization){
				return !empty($organization)?true: array('message'=>'Необходимо заполнить');
			}, $organizations[$i]);


			$regions[$i] = trim(strip_tags(mb_eregi_replace('[^A-Za-zА-Яа-яёЁ]','', $post['regions'][$i])));

			$regions_val[$i]=call_user_func(function($regions){
				return !empty($regions)?true: array('message'=>'Необходимо заполнить');
			}, $regions[$i]);

			$sites[$i] = trim(strip_tags($post['sites'][$i]));

			$field_activities[$i] = trim(strip_tags($post['field_activities'][$i]));

			$positions[$i] = trim(strip_tags(mb_eregi_replace('[^A-Za-zА-Яа-яёЁ-]','', $post['positions'][$i])));

			$functions[$i] = trim(strip_tags($post['functions'][$i]));

			$functions_val[$i]=call_user_func(function($position){
				return !empty($position)?true: array('message'=>'Необходимо заполнить');
			}, $functions[$i]);

			$positions_val[$i] = call_user_func(function($position){
				return !empty($position)?true: array('message'=>'Необходимо заполнить');
			}, $positions);


			$getting_starteds[$i]=$post['getting_starteds'][$i];
			$getting_starteds_val[$i] = call_user_func(function($getting_started){
				if($getting_started['year'] == 0){
					return array('message'=>'Необходимо заполнить');
				}elseif($getting_started['year'] > date('Y')){
					return array('message'=>'Слишком поздно');
				}

			}, $getting_starteds[$i]);

			$at_the_moments[$i] = isset($post['at_the_moments'][$i])?$post['at_the_moments'][$i]:'false';

			$closing_works[$i] = $at_the_moments[$i]==='false'?$post['closing_works'][$i]:array('month' => 1, 'year' => 0);


			$closing_works_val[$i] = call_user_func(function($closing_work, $getting_started, $at_the_moments){
				if($at_the_moments === 'false' && $closing_work['year']==0){
					return array('message'=>'Необходимо заполнить');
				}elseif(($closing_work['year'] < $getting_started['year'] && $at_the_moments === 'false')||
					($closing_work['year'] === $getting_started['year'] && $closing_work['month']<$getting_started['month'])){
					return array('message'=>'Дата окончания ранее даты начала');
				}

			}, $closing_works[$i], $getting_starteds[$i], $at_the_moments[$i]);


		}




		for($i=0, $len=count($post['recommend_name']); $i< $len; ++$i) {

			$recommend_name[$i] = trim(strip_tags($post['recommend_name'][$i]));
			$recommend_position[$i] = trim(strip_tags($post['recommend_position'][$i]));
			$recommend_organization[$i] = trim(strip_tags($post['recommend_organization'][$i]));
			$recommend_phone[$i] = trim(strip_tags($post['recommend_phone'][$i]));

			if(empty($recommend_name[$i])&&empty($recommend_position[$i])&&
				empty($recommend_organization[$i])&&empty($recommend_phone[$i])){
				$recommend_name_val[$i]=true;
				$recommend_position_val[$i]=true;
				$recommend_organization_val[$i]=true;
				$recommend_phone_val[$i]=true;
			}else{
				$recommend_name_val[$i] = call_user_func(function($organization){
					return !empty($organization)?true: array('message'=>'Необходимо заполнить');
				},$recommend_name[$i]);
				$recommend_position_val[$i] = call_user_func(function($organization){
					return !empty($organization)?true: array('message'=>'Необходимо заполнить');
				},$recommend_position[$i]);
				$recommend_organization_val[$i] = call_user_func(function($organization){
					return !empty($organization)?true: array('message'=>'Необходимо заполнить');
				},$recommend_organization[$i]);
				$recommend_phone_val[$i] = call_user_func(function($organization){
					return !empty($organization)?true: array('message'=>'Необходимо заполнить');
				},$recommend_phone[$i]);

			}
		}

		$key_skills = $post['key_skills'];

		$key_skills_val = call_user_func(function($key_skills){
			return !empty($key_skills)?true: array('message'=>'Необходимо заполнить');
		}, $key_skills);

		$about_self = $post['about_self'];

		return array(
			'organizations'=>array(
				'val'=>$organizations_val,
				'value'=>$organizations
			),
			'positions'=>array(
				'val'=>$positions_val,
				'value'=>$positions
			),
			'getting_starteds'=>array(
				'val'=>$getting_starteds_val,
				'value'=>$getting_starteds
			),

			'closing_works'=>array(
				'val'=>$closing_works_val,
				'value'=>$closing_works
			),
			'at_the_moments'=>array('value'=>$at_the_moments),
			'regions'=>array(
				'val'=>$regions_val,
				'value'=>$regions
			),
			'sites'=>array('value'=>$sites),
			'field_activities'=>array('value'=>$field_activities),
			'functions'=>array(
				'val'=>$functions_val,
				'value'=>$functions
			),
			'key_skills'=>array(
				'val'=>$key_skills_val,
				'value'=>$key_skills
			),
			'about_self'=>array('value'=>$about_self),

			'recommend_name'=>array(
				'val'=>$recommend_name_val,
				'value'=>$recommend_name,
			),
			'recommend_position'=>array(
				'val'=>$recommend_position_val,
				'value'=>$recommend_position,
			),
			'recommend_organization'=>array(
				'val'=>$recommend_organization_val,
				'value'=>$recommend_organization,
			),
			'recommend_phone'=>array(
				'val'=>$recommend_phone_val,
				'value'=>$recommend_phone,
			)

		);

	}

	private function _checkFormPosition($post){

		$desired_position = isset($post['desired_position'])?trim(strip_tags($post['desired_position'])):'';
		$professional_area = isset($post['professional_area'])?trim(strip_tags($post['professional_area'])):'';
		$salary = isset($post['salary'])?trim(strip_tags(mb_eregi_replace('[^0-9]','',$post['salary']))):'';
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

		$salary_val = call_user_func(function($salary){
			return (preg_match('/^[0-9]$/',$salary) || empty($salary))?true: array('message'=>'Некорректные
				данные');
		}, $salary);

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
			'salary'=>array(
				'val'=>$salary_val,
				'value'=>$salary
			),
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

	private function _jsExperience(){
		return array(
			'src'=>array(
				BASE_URL."/public/js/jquery-2.1.1.min.js",
				BASE_URL."/public/js/jquery.validate.min.js",
				BASE_URL."/public/js/handlebars-v2.0.0.js",
				BASE_URL."/public/js/experience.js"
			),
		);
	}
}