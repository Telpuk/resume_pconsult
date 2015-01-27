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

		'profile'=>array('prof.surname',
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
			'exper.getting_starteds',
			'exper.closing_works',
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

			$likeString = $this->_parse($wordKey['parse'], $wordKey['input']);

			$this->setQuery($this->_placeSearch($wordKey['placeSearch'],$likeString));

		}
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

				}
				case 'skills':{

					break;
				}
				case 'experience':{

					break;
				}
			}
		}

		return $whereСolumn;

	}

	private function _parse($parse = null, $input= array()){
		$likeString = '';
		switch($parse){
			case 'allWorlds':{
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
			case 'someWorlds':{

				break;
			}
			case 'exactWorlds':{

				break;
			}
			case 'noWorld':{

				break;
			}
		}
	}

	public function setQuery($query = null){
		$this->_query = $query;
	}

	public function getQuery(){
		return $this->_query;
	}




}