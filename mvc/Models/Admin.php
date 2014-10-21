<?php
class Admin{
	private
		$_dbc,
		$_user_object;

	function __construct(){
		$this->_dbc = Model::getInstance()->getDbh();
		$this->_user_object = new User();
	}

	public  function checkLoginAndPassword($data){
		try {
			$stmt = $this->_dbc->prepare ("
												SELECT
													login
												FROM
													users_access
												WHERE
													login = :login
												AND
													password = :password"
			);
			$stmt->execute(array(
				':login'=>$data['login'],
				':password'=>$data['password']
			));
			$user_access_data = $stmt->fetch(PDO::FETCH_ASSOC);
		}catch (PDOException $e){
			exit(print_r($e->errorInfo).$e->getFile());
		}

		if($user_access_data['login']){
			return $user_access_data['login'];
		}

		return false;
	}

	public function search($search){
		try {
			$stmt = $this->_dbc->prepare (
				"SELECT DISTINCT
		  prof.id,
		  prof.photo,
		  CONCAT_WS(
		    ' ',
		    prof.surname,
		    prof.first_name,
		    prof.patronymic
		  ) AS 'name',
		  exper.getting_starteds AS 'experience_getting_starteds',
		  exper.closing_works AS 'experience_closing_works',
		  exper.at_the_moments AS 'experience_at_the_moments',
		  prof.desired_position,
		  prof.salary
		FROM
		  profile AS prof,
		  experience AS exper,
		  education AS educ
		WHERE
		prof.registered_user = 'yes' AND
		prof.id = exper.id_user AND
		prof.id = educ.id_user
		  AND (
		    prof.surname  LIKE :search ||
		    prof.first_name LIKE :search ||
		    prof.patronymic  LIKE :search ||
		    prof.birth LIKE :search ||
		    prof.sex LIKE :search ||
		    prof.city LIKE :search ||
			prof.move LIKE :search ||
			prof.trip LIKE :search ||
		    prof.nationality LIKE :search ||
		    prof.work_permit LIKE :search ||
		    prof.travel_time_work LIKE :search ||
		    prof.preferred_communication LIKE :search ||
		    prof.mobile_phone LIKE :search ||
		    prof.home_phone LIKE :search ||
		    prof.work_phone LIKE :search ||
		    prof.email LIKE :search ||
		    prof.comment_mobile_phone LIKE :search ||
		    prof.comment_home_phone LIKE :search ||
		    prof.comment_work_phone LIKE :search ||
		    prof.skype LIKE :search ||
		    prof.facebook LIKE :search ||
		    prof.desired_position LIKE :search ||
		    prof.professional_area LIKE :search ||
		    prof.salary LIKE :search ||
		    prof.currency LIKE :search ||
		    prof.employment LIKE :search ||
		    prof.schedule LIKE :search ||

		    exper.organizations LIKE :search ||
		    exper.regions LIKE :search ||
		    exper.positions LIKE :search ||
			exper.sites LIKE :search ||
		    exper.field_activities LIKE :search ||
		    exper.getting_starteds LIKE :search ||
		    exper.closing_works LIKE :search ||
		    exper.functions LIKE :search ||
		    exper.key_skills LIKE :search ||
		    exper.about_self LIKE :search ||
		    exper.recommend_names LIKE :search ||
		    exper.recommend_position LIKE :search ||
		    exper.recommend_organization LIKE :search ||
		    exper.recommend_phone LIKE :search ||

		    educ.level LIKE :search ||
		    educ.names_institutions LIKE :search ||
		    educ.faculties LIKE :search ||
		    educ.specialties_specialties LIKE :search ||
		    educ.years_graduations LIKE :search ||
		    educ.courses_names LIKE :search ||
		    educ.follow_organizations LIKE :search ||
		    educ.courses_specialties LIKE :search ||
		    educ.course_years_graduations LIKE :search ||
		    educ.tests_exams_names LIKE :search ||
		    educ.tests_exams_follow_organizations LIKE :search ||
		    educ.tests_exams_specialty LIKE :search ||
		    educ.tests_exams_years_graduations LIKE :search ||
		    educ.electronic_certificates_names LIKE :search ||
		    educ.electronic_certificates_years_graduations LIKE :search ||
		    educ.electronic_certificates_links LIKE :search ||
		    educ.native_language LIKE :search ||
		    educ.language_english LIKE :search ||
		    educ.language_germany LIKE :search ||
		    educ.language_french LIKE :search ||
		    educ.language_further LIKE :search ||
		    educ.language_further_level LIKE :search
		  ) ORDER BY prof.date DESC");
			$stmt->execute(array(':search'=>"%".$search."%"));
			$search_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
		}catch (PDOException $e){
			exit(print_r($e->errorInfo).$e->getFile());
		}

		foreach($search_data as $key =>$data){
			$experience_count[$key] = $this->_user_object->getExperienceCount(
				array(
					'experience_getting_starteds'=>explode('[@!-#-!@]',$data['experience_getting_starteds']),
					'experience_closing_works'=>explode('[@!-#-!@]',$data['experience_closing_works']),
					'experience_at_the_moments'=>explode('[@!-#-!@]',$data['experience_at_the_moments'])
				)
			);
			$search_data[$key]['sum_experience'] = $experience_count[$key]['sum'];
		}



		 return $search_data;
	}
}