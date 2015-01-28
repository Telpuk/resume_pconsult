<?php
class SqlQuestionSearch{
	/**
	 * basic question
	 */
	private $queryBasis = <<<HEAD
"prof.id,
    prof.photo,
    CONCAT_WS(
      ' ',
      prof.surname,
      prof.first_name,
      prof.patronymic
    ) AS 'name',
    prof.desired_position,
    prof.salary,
    prof.currency,
    prof.birth,
    prof.conclusion,
    DATE_FORMAT(prof.date,'%Y-%m-%d %H:%i') AS 'date_registration',
    exper.getting_starteds AS 'experience_getting_starteds',
    exper.closing_works AS 'experience_closing_works',
    exper.at_the_moments AS 'experience_at_the_moments',
    exper.positions AS 'experience_positions',
    exper.organizations AS 'experience_organizations',
    (SELECT COUNT(com.id) FROM  `comments` AS com WHERE com.id_user = prof.id  ) AS 'comments_count'
  FROM
    `profile` AS prof,
    `experience` AS exper,
    `education` AS educ
  WHERE prof.registered_user = 'yes'
    AND prof.id = exper.id_user
    AND prof.id = educ.id_user"
    AND ?
HEAD;
	/**
	 * @var null
	 */
	private $_query = null;

	/**
	 * post Question
	 */
	private $_postSearch = null;

	private $_searchColumTable = array(

		'profile'=>array(
			'prof.surname',
			"CONCAT_WS(' ',prof.surname, prof.first_name, prof.patronymic)",
			'prof.first_name',
			'prof.patronymic',
			'prof.city',
			'prof.move',
			'prof.trip',
			'prof.nationality',
			'prof.work_permit',
			'prof.travel_time_work',
			'prof.preferred_communication',
			'prof.mobile_phone',
			'prof.home_phone',
			'prof.work_phone',
			'prof.trip',
			'prof.email',
			'prof.comment_mobile_phone',
			'prof.comment_home_phone',
			'prof.comment_work_phone',
			'prof.skype',
			'prof.facebook',
			'prof.desired_position',
			'prof.professional_area',
			'prof.salary',
			'prof.currency',
			'prof.employment',
			'prof.schedule',
			'prof.conclusion'
		),
		'experience'=>array('exper.organizations',
			'exper.regions',
			'exper.positions',
			'exper.sites',
			'exper.field_activities',
			'exper.functions',
			'exper.key_skills',
			'exper.about_self',
			'exper.recommend_names',
			'exper.recommend_position',
			'exper.recommend_organization',
			'exper.recommend_phone'
		),
		'education'=>array('educ.level',
			'educ.names_institutions',
			'educ.faculties',
			'educ.specialties_specialties',
			'educ.years_graduations',
			'educ.courses_names',
			'educ.follow_organizations',
			'educ.courses_specialties',
			'educ.course_years_graduations',
			'educ.tests_exams_names',
			'educ.tests_exams_follow_organizations',
			'educ.tests_exams_specialty',
			'educ.tests_exams_years_graduations',
			'educ.electronic_certificates_names',
			'educ.electronic_certificates_years_graduations',
			'educ.electronic_certificates_links',
			'educ.native_language',
			'educ.language_english',
			'educ.language_germany',
			'educ.language_french',
			'educ.language_further',
			'educ.language_further_level'
		)
	);

	public  function __construct($postSearch = null){
		$this->_postSearch = $postSearch;
	}

	public  function analysis(){
		if(isset($this->_postSearch['wordKey'])){
			$wordKey = $this->_postSearch['wordKey'];
			$professional_area = array_values(array_diff($this->_postSearch['professional_area'],array('')));
			$city = array_values(array_diff($this->_postSearch['city'],array('')));
			$salary = array_diff($this->_postSearch['salary'],array(''));


			$likeWordKeyQuery = $this->_placeSearch($wordKey['placeSearch'],$this->_parseWordKey($wordKey['parse'], trim($wordKey['input'])));
			$likeProfessionalAreaQuery = $this->_parseProfessionalArea(count($professional_area)?$professional_area:null);
			$likeCityQuery = $this->_parseCity(count($city)?$city:null);
			$likeSalary = $this->_parseSalary(count($salary)?$salary:null);
			$likeExperience = $this->_parseExperience(isset($this->_postSearch['experience'])?$this->_postSearch['experience']:null);
			$likeEducation= $this->_parseEducation(isset($this->_postSearch['education'])?$this->_postSearch['education']:null);
			$likeNationality= $this->_parseNationality(isset($this->_postSearch['nationality'])?$this->_postSearch['nationality']:null);
			$likeWorkPermit= $this->_parseWorkPermit(isset($this->_postSearch['work_permit'])?$this->_postSearch['work_permit']:null);
			$likeAge= $this->_parseAge(isset($this->_postSearch['age'])?$this->_postSearch['age']:null);
			$sex = $this->_parseSex(isset($this->_postSearch['sex'])?$this->_postSearch['sex']:null);
			$employment = $this->_parseEmployment(isset($this->_postSearch['employment'])?$this->_postSearch['employment']:null);
			$schedule = $this->_parseSchedule(isset($this->_postSearch['schedule'])?$this->_postSearch['schedule']:null);
			$languages = $this->_parseLanguages(isset($this->_postSearch['languages']) ?$this->_postSearch['languages']:null);

			$this->setQuery(
				$likeWordKeyQuery,
				$likeProfessionalAreaQuery,
				$likeCityQuery,
				$likeSalary,
				$likeExperience,
				$likeEducation,
				$likeNationality,
				$likeWorkPermit,
				$likeAge,
				$sex,
				$employment,
				$schedule,
				$languages);

		}
	}

	private  function _parseLanguages($languages = null){
		$queryLike = null;
		if(!is_null($languages)){

			foreach($languages['language_further'] as $key=>$value){
				if($languages['language_further_level'][$key] ==='родной язык'){
					$queryLike .="(educ.native_language = '{$value}')AND";
					continue;
				}
				if($value === 'английский'){
					if($languages['language_further_level'][$key] === 'не имеет значения'){
						$queryLike .="(educ.language_english <> '')AND";
					}else{
						$queryLike .="(educ.language_english = '{$languages['language_further_level'][$key]}')AND";
					}
					continue;
				}
				if($value === 'немецкий'){
					if($languages['language_further_level'][$key] === 'не имеет значения'){
						$queryLike .="(educ.language_germany <> '')AND";
					}else{
						$queryLike .="(educ.language_germany = '{$languages['language_further_level'][$key]}')AND";
					}
					continue;
				}
				if($value === 'французский'){
					if($languages['language_further_level'][$key] === 'не имеет значения'){
						$queryLike .="(educ.language_french <> '')AND";
					}else{
						$queryLike .="(educ.language_french = '{$languages['language_further_level'][$key]}')AND";
					}
					continue;
				}
				$queryLike .="(educ.language_further LIKE '%{$value}%' AND educ.language_further_level LIKE '%{{$languages['language_further_level'][$key]}}%' )AND";

			}

			echo $queryLike;
			return trim($queryLike,'AND');
		}
		return $queryLike;
	}
	private  function _parseEmployment($employment = null){
		$queryLike = null;
		if(!is_null($employment)){
			foreach($employment as $value){
				$queryLike .="(prof.employment LIKE '%{$value}%')||";
			}
			return trim($queryLike,'||');
		}
		return $queryLike;
	}
	private  function _parseSchedule($schedule = null){
		$queryLike = null;
		if(!is_null($schedule)){
			foreach($schedule as $value){
				$queryLike .="(prof.schedule LIKE '%{$value}%')||";
			}
			return trim($queryLike,'||');
		}
		return $queryLike;
	}

	private function _parseSex($sex=null){
		$queryLike = null;
		if(!is_null($sex)){

			switch($sex){
				case 'Мужской':{
					$queryLike .=" (prof.sex = 'Мужской') ";
					break;
				}
				case 'Женский':{
					$queryLike .=" (prof.sex = 'Женский') ";
					break;
				}
				default:{
				return null;
				}
			}

			return trim($queryLike, ' ');
		}
		return $queryLike;
	}

	private function _parseAge($age=null){
		$queryLike = null;
		if(!is_null($age)){
			if(isset($age['from']) && $age['from']){
				$queryLike .=" (prof.age >= {$age['from']}) AND";
			}

			if(isset($age['before']) && $age['before']){
				$queryLike .=" (prof.age <= {$age['before']}) AND";
			}
			if(isset($age['show_empty'])){
				$queryLike .=" (prof.age <> 0) AND";
			}
			if(isset($age['show_photo'])){
				$queryLike .=" (prof.photo <> 'no-photo.png') AND";
			}

			return trim($queryLike, 'AND');
		}
		return $queryLike;
	}

	private function _parseWorkPermit($work_permit=null){
		$queryLike = null;
		if(!is_null($work_permit) && isset($work_permit['name'])&& $work_permit['name']){
			$queryLike .=" (prof.work_permit = '{$work_permit['name']}') ";
			return trim($queryLike);
		}
		return $queryLike;
	}

	private function _parseNationality($nationality=null){
		$queryLike = null;
		if(!is_null($nationality) && isset($nationality['name']) && $nationality['name']){
			$queryLike .=" (prof.nationality = '{$nationality['name']}') ";
			return trim($queryLike);
		}
		return $queryLike;
	}

	private function _parseEducation($education=null){
		$queryLike = null;
		if(!is_null($education)){
			if(isset($education['degree']) && $education['degree']){
				$queryLike .=" (educ.level = '{$education['degree']}') ";
			}
			if(isset($education['name']) && $education['name']){
				$queryLike .="AND (educ.names_institutions = '{$education['name']}')";
			}
			return trim($queryLike,'AND');
		}
		return $queryLike;
	}

	private  function _parseExperience($experience = null){
		$queryLike = null;
		if(!is_null($experience)){

			if(isset($experience['no'])){
				$queryLike .=' (exper.experience_sum = 0) ||';
			}
			if(isset($experience['one-three'])){
				$queryLike .=' (exper.experience_sum >= 1 AND exper.experience_sum <= 3) ||';
			}
			if(isset($experience['three-six'])){
				$queryLike .=" (exper.experience_sum >= 3 AND exper.experience_sum <= 6) ||";
			}
			if(isset($experience['six-more'])){
				$queryLike .=" (exper.experience_sum >= 6 )  ||";
			}
			return trim($queryLike,'||');
		}
		return $queryLike;
	}
	private  function _parseProfessionalArea($professionalArea = null){
		$queryLike = null;
		if(!is_null($professionalArea)){
			foreach($professionalArea as $key=>$value){
				$queryLike .=" prof.professional_area LIKE '%$value%' OR";
			}
			return trim($queryLike,'OR');
		}
		return $queryLike;
	}

	private  function _parseCity($city = null){
		$queryLike = null;
		if(!is_null($city)){
			foreach($city as $key=>$value){
				$queryLike .=" prof.city LIKE '%$value%' OR";
			}
			return trim($queryLike,'OR');
		}
		return $queryLike;
	}
	private  function _parseSalary($salary = null){
		$queryLike = null;
		if(!is_null($salary)){
			if(isset($salary['from'])){
				$queryLike .=' (prof.salary >= '.(int)$salary['from'].') AND';
			}
			if(isset($salary['before'])){
				$queryLike .=' (prof.salary <= '.(int)$salary['before'].') AND';
			}
			if(isset($salary['show_empty'])){
				$queryLike .=" (prof.salary != '') AND";
			}
			if(isset($salary['currency']) && (isset($salary['from']) || isset($salary['before'])) ){
				$queryLike .=" (prof.currency = '{$salary['currency']}') AND";
			}
			return trim($queryLike,'AND');
		}
		return $queryLike;
	}

	private function _placeSearch($placeSearch ,$likeString = null){
		$whereСolumn = '';
		foreach($placeSearch as $key => $value){
			switch($key){
				case 'all':{
					foreach($this->_searchColumTable['profile'] as $value){
						$whereСolumn .= str_replace( '&colum$',$value, $likeString).'||';
					}
					foreach($this->_searchColumTable['experience'] as $value){
						$whereСolumn .= str_replace( '&colum$',$value,$likeString).'||';
					}
					foreach($this->_searchColumTable['education'] as $value){
						$whereСolumn .= str_replace( '&colum$',$value,$likeString).'||';
					}
					return trim($whereСolumn,'||');
					break;
				}
				case 'education':{
					foreach($this->_searchColumTable['education'] as $value){
						$whereСolumn .= str_replace( '&colum$',$value,$likeString).'||';
					}
					return trim($whereСolumn,'||');
					break;
				}
				case 'skills':{
					$whereСolumn = str_replace( '&colum$','exper.key_skills',$likeString);
					return $whereСolumn;
					break;
				}
				case 'experience':{
					foreach($value as $k=>$v){

						if($k === 'field_activities'){
							$whereСolumn .= str_replace( '&colum$','exper.field_activities',$likeString).'||';
						}
						if($k === 'positions'){
							$whereСolumn .= str_replace( '&colum$','exper.positions',$likeString).'||';
						}
						if($k === 'functions'){
							$whereСolumn .= str_replace( '&colum$','exper.functions',$likeString).'||';
						}
					}
					return trim($whereСolumn,'||');
					break;
				}
			}
		}

		return $whereСolumn;

	}

	private function _parseWordKey($parse = null, $input= array()){
		$likeString = '';
		switch($parse){
			case 'allWorlds':{
				$input = explode(' ',$input);
				if(!empty($input[0])){
					foreach($input as $index=>$value){
						if($index === 0)
							$likeString .= "&colum$ LIKE '%{$value}%'";
						else
							$likeString .= " AND &colum$ LIKE '%{$value}%'";
					}
					return '('.$likeString.')';
				}
				break;
			}
			case 'someWorlds':{
				$input = explode(' ',$input);
				if(!empty($input[0])){
					foreach($input as $index=>$value){
						if($index === 0)
							$likeString .= "&colum$ LIKE '%{$value}%'";
						else
							$likeString .= " || &colum$ LIKE '%{$value}%'";
					}
					return '('.$likeString.')';
				}
				break;
			}
			case 'exactWorlds':{
				if($input){
					$likeString .= "&colum$ LIKE '%{$input}%'";
				}
				return '('.$likeString.')';
				break;
			}
		}
	}

	public function setQuery($likeWordKeyQuery = null,
							 $likeProfessionalAreaQuery = null,
							 $likeCityQuery=null,
							 $likeSalary=null,
							 $likeExperience=null,
							 $likeEducation=null,
							 $likeNationality=null,
							 $likeWorkPermit=null,
							 $likeAge=null,
							 $sex=null,
							 $employment=null,
							 $schedule=null,
							 $languages=null){
		$this->_query .= $likeWordKeyQuery?' AND ( '.$likeWordKeyQuery.' )':null;
		$this->_query .= $likeProfessionalAreaQuery?' AND ( '.$likeProfessionalAreaQuery.' )':null;
		$this->_query .= $likeCityQuery?' AND ( '.$likeCityQuery.' )':null;
		$this->_query .= $likeSalary?' AND ( '.$likeSalary.' )':null;
		$this->_query .= $likeExperience?' AND ( '.$likeExperience.' )':null;
		$this->_query .= $likeEducation?' AND ( '.$likeEducation.' )':null;
		$this->_query .= $likeNationality?' AND ( '.$likeNationality.' )':null;
		$this->_query .= $likeWorkPermit?' AND ( '.$likeWorkPermit.' )':null;
		$this->_query .= $likeAge?' AND ( '.$likeAge.' )':null;
		$this->_query .= $sex?' AND ( '.$sex.' )':null;
		$this->_query .= $employment?' AND ( '.$employment.' )':null;
		$this->_query .= $schedule?' AND ( '.$schedule.' )':null;
		$this->_query .= $languages?' AND ( '.$languages.' )':null;
//		echo $likeProfessionalAreaQuery,'<br>';
	}

	public function getQuery(){
		return $this->_query;
	}




}