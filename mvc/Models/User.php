<?php
class User{
	private $_dbc;
	private $_id;

	function __construct(){
		$this->_dbc = Model::getInstance()->getDbh();
	}

	public function setIdUser(){
		try {
			$this->_dbc->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$this->_dbc->beginTransaction();

			$stmt = $this->_dbc->prepare("INSERT INTO profile(registered_user) VALUES(:registered_user)");
			$stmt->execute(array(':registered_user'=>'no'));
			$id = $this->_dbc->lastInsertId('id');

			$stmt = $this->_dbc->prepare("INSERT INTO experience(id_user) VALUES(:id_user)");
			$stmt->execute(array(':id_user'=>$id));

			$stmt = $this->_dbc->prepare("INSERT INTO education(id_user) VALUES(:id_user)");
			$stmt->execute(array(':id_user'=>$id));


			$this->_dbc->commit();
		}catch (PDOException $e){
			$this->_dbc->rollBack();
			exit(print_r($e->errorInfo).$e->getFile().$e->getCode().$e->getLine());
		}
		$this->_id = $id;
	}

	public function selectPosition($id_user){
		try {
			$stmt = $this->_dbc->prepare ("SELECT
													desired_position,
													professional_area,
													employment,
													schedule,
													salary,
													currency
												FROM
													profile
												WHERE
													id = :id_user");
			$stmt->execute(array(':id_user'=>$id_user));
			$position_data = $stmt->fetch(PDO::FETCH_ASSOC);
		}catch (PDOException $e){
			exit(print_r($e->errorInfo).$e->getFile());
		}
		return array(
			'desired_position'=>array(
				'val'=>true,
				'value'=>$position_data['desired_position']),
			'professional_area'=>array(
				'val'=>true,
				'value'=>$position_data['professional_area']
			),
			'employment'=>array(
				'val'=>true,
				'value'=>explode('[@!-#-!@]',$position_data['employment'])
			),
			'schedule'=>array(
				'val'=>true,
				'value'=>explode('[@!-#-!@]',$position_data['schedule'])
			),
			'salary'=>array('value'=>$position_data['salary']),
			'currency'=>array('value'=>$position_data['currency'])
		);
	}

	public function updatePosition($inputs, $id_user){
		try {
			$stmt = $this->_dbc->prepare ("UPDATE
													profile
												SET
													desired_position = :desired_position,
													professional_area = :professional_area,
													employment = :employment,
													schedule = :schedule,
													salary = :salary,
													currency = :currency
												WHERE
													id = :id_user");
			$stmt->execute(array(
				':desired_position'=>$inputs['desired_position']['value'],
				':professional_area'=>$inputs['professional_area']['value'],
				':employment'=>implode('[@!-#-!@]',$inputs['employment']['value']),
				':schedule'=>implode('[@!-#-!@]',$inputs['schedule']['value']),
				':salary'=>$inputs['salary']['value'],
				':currency'=>$inputs['currency']['value'],
				':id_user'=>$id_user)
			);
		}catch (PDOException $e){
			exit(print_r($e->errorInfo).$e->getFile());
		}
	}

	public function updateEducation($inputs, $id_user){
		try {
			$stmt = $this->_dbc->prepare("UPDATE
													education
												SET
													level = :level,
													names_institutions = :names_institutions,
													faculties = :faculties,
													specialties_specialties = :specialties_specialties,
													years_graduations = :years_graduations,

													courses_names = :courses_names,
													follow_organizations = :follow_organizations,
													courses_specialties = :courses_specialties,
													course_years_graduations = :course_years_graduations,

													tests_exams_names =:tests_exams_names,
													tests_exams_follow_organizations =:tests_exams_follow_organizations,
													tests_exams_specialty = :tests_exams_specialty,
													tests_exams_years_graduations =:tests_exams_years_graduations,

													electronic_certificates_names =:electronic_certificates_names,
													electronic_certificates_years_graduations = :electronic_certificates_years_graduations,
													electronic_certificates_links =:electronic_certificates_links

												WHERE
													id_user = :id_user");
			$stmt->execute(array(
				':level'=>$inputs['level']['value'],
				':names_institutions'=>implode('[@!-#-!@]',$inputs['names_institutions']['value']),
				':faculties'=>implode('[@!-#-!@]',$inputs['faculties']['value']),
				':specialties_specialties'=>implode('[@!-#-!@]',$inputs['specialties_specialties']['value']),
				':years_graduations'=>implode('[@!-#-!@]',$inputs['years_graduations']['value']),

				':courses_names'=>implode('[@!-#-!@]',$inputs['courses_names']['value']),
				':follow_organizations'=>implode('[@!-#-!@]',$inputs['follow_organizations']['value']),
				':courses_specialties'=>implode('[@!-#-!@]',$inputs['courses_specialties']['value']),
				':course_years_graduations'=>implode('[@!-#-!@]',$inputs['course_years_graduations']['value']),

				':tests_exams_names'=>implode('[@!-#-!@]',$inputs['tests_exams_names']['value']),
				':tests_exams_follow_organizations'=>implode('[@!-#-!@]',$inputs['tests_exams_follow_organizations']['value']),
				':tests_exams_specialty'=>implode('[@!-#-!@]',$inputs['tests_exams_specialty']['value']),
				':tests_exams_years_graduations'=>implode('[@!-#-!@]',$inputs['tests_exams_years_graduations']['value']),

				':electronic_certificates_names'=>implode('[@!-#-!@]',$inputs['electronic_certificates_names']['value']),
				':electronic_certificates_years_graduations'=>implode('[@!-#-!@]',$inputs['electronic_certificates_years_graduations']['value']),
				':electronic_certificates_links'=>implode('[@!-#-!@]',$inputs['electronic_certificates_links']['value']),

				':id_user'=>$id_user)
			);
		}catch (PDOException $e){
			exit(print_r($e->errorInfo).$e->getFile());
		}
	}


	public function updatePhotoId($photo, $id_user){
		try {
			$stmt = $this->_dbc->prepare ("UPDATE profile SET photo = :photo WHERE id = :id_user");
			$stmt->execute(array(':photo'=>$photo, ':id_user'=>$id_user));
		}catch (PDOException $e){
			exit(print_r($e->errorInfo).$e->getFile());
		}
	}

	public function updatePersonal($inputs, $id_user){
		try {
			$stmt = $this->_dbc->prepare ("UPDATE
													profile
												SET
													surname = :surname,
													first_name = :first_name,
													patronymic = :patronymic,
													birth = :birth,
													sex = :sex,
													city = :city,
													move = :move,
													trip = :trip,
													nationality = :nationality,
													work_permit = :work_permit,
													travel_time_work = :travel_time_work
												WHERE
													id = :id_user");
			$stmt->execute(array(
				':surname'=>$inputs['surname']['value'],
				':first_name'=>$inputs['first_name']['value'],
				':patronymic'=>$inputs['patronymic']['value'],
				':birth'=>"{$inputs['birth']['day_birth']}-{$inputs['birth']['month_birth']}-{$inputs['birth']['year_birth']}",
				':sex'=>$inputs['sex']['value'],
				':city'=>$inputs['city']['value'],
				':move'=>$inputs['move']['value'],
				':trip'=>$inputs['trip']['value'],
				':nationality'=>$inputs['nationality']['value'],
				':work_permit'=>$inputs['work_permit']['value'],
				':travel_time_work'=>$inputs['travel_time_work']['value'],
				':id_user'=>$id_user)
			);
		}catch (PDOException $e){
			exit(print_r($e->errorInfo).$e->getFile());
		}
	}


	public function updateContacts($inputs, $id_user){
		try {
			$stmt = $this->_dbc->prepare ("UPDATE
													profile
												SET
													mobile_phone = :mobile_phone,
													home_phone = :home_phone,
													work_phone = :work_phone,
													comment_mobile_phone = :comment_mobile_phone,
													comment_home_phone = :comment_home_phone,
													comment_work_phone = :comment_work_phone,
													preferred_communication = :preferred_communication,
													email = :email,
													icq = :icq,
													skype = :skype,
													free_lance = :free_lance,
													my_circle = :my_circle,
													linkedln = :linkedln,
													facebook = :facebook,
													live_journal = :live_journal,
													other_site = :other_site
												WHERE
													id = :id_user");
			$stmt->execute(array(
				':mobile_phone'=>$inputs['mobile_phone']['value'],
				':home_phone'=>$inputs['home_phone']['value'],
				':work_phone'=>$inputs['work_phone']['value'],
				':comment_mobile_phone'=>$inputs['comment_mobile_phone']['value'],
				':comment_home_phone'=>$inputs['comment_home_phone']['value'],
				':comment_work_phone'=>$inputs['comment_work_phone']['value'],
				':preferred_communication'=>$inputs['preferred_communication']['value'],
				':email'=>$inputs['email']['value'],
				':icq'=>$inputs['icq']['value'],
				':skype'=>$inputs['skype']['value'],
				':free_lance'=>$inputs['free_lance']['value'],
				':my_circle'=>$inputs['my_circle']['value'],
				':linkedln'=>$inputs['linkedln']['value'],
				':facebook'=>$inputs['facebook']['value'],
				':live_journal'=>$inputs['live_journal']['value'],
				':other_site'=>$inputs['other_site']['value'],
				':id_user'=>$id_user)
			);
		}catch (PDOException $e){
			exit(print_r($e->errorInfo).$e->getFile().$e->getMessage());
		}
	}

	public function selectEducation($id_user){
		try {
			$stmt = $this->_dbc->prepare("SELECT
													level,
													names_institutions,
													faculties,
													specialties_specialties,
													years_graduations,
													courses_names,
													follow_organizations,
													courses_specialties,
													course_years_graduations,
													tests_exams_names,
													tests_exams_follow_organizations,
													tests_exams_specialty,
													tests_exams_years_graduations,
													electronic_certificates_names,
													electronic_certificates_years_graduations,
													electronic_certificates_links
												FROM
													education
												WHERE
													id_user = :id_user");
			$stmt->execute(array(':id_user'=>$id_user));
			$education_data = $stmt->fetch(PDO::FETCH_ASSOC);
		}catch (PDOException $e){
			exit(print_r($e->errorInfo).$e->getFile());
		}
		return array(
			'level'=>array(
				'val'=>true,
				'value'=>$education_data['level']
			),
			'names_institutions'=>array(
				'val'=>true,
				'value'=>explode('[@!-#-!@]',$education_data['names_institutions'])
			),
			'faculties'=>array(
				'val'=>true,
				'value'=>explode('[@!-#-!@]',$education_data['faculties'])
			),
			'specialties_specialties'=>array(
				'val'=>true,
				'value'=>explode('[@!-#-!@]',$education_data['specialties_specialties'])
			),
			'years_graduations'=>array(
				'val'=>true,
				'value'=>explode('[@!-#-!@]',$education_data['years_graduations'])
			),
			'courses_names'=>array(
				'val'=>true,
				'value'=>explode('[@!-#-!@]',$education_data['courses_names'])
			),
			'follow_organizations'=>array(
				'val'=>true,
				'value'=>explode('[@!-#-!@]',$education_data['follow_organizations'])
			),
			'courses_specialties'=>array(
				'val'=>true,
				'value'=>explode('[@!-#-!@]',$education_data['courses_specialties'])
			),
			'course_years_graduations'=>array(
				'val'=>true,
				'value'=>explode('[@!-#-!@]',$education_data['course_years_graduations'])
			),
			'tests_exams_names'=>array(
				'val'=>true,
				'value'=>explode('[@!-#-!@]',$education_data['tests_exams_names'])
			),
			'tests_exams_follow_organizations'=>array(
				'val'=>true,
				'value'=>explode('[@!-#-!@]',$education_data['tests_exams_follow_organizations'])
			),
			'tests_exams_specialty'=>array(
				'val'=>true,
				'value'=>explode('[@!-#-!@]',$education_data['tests_exams_specialty'])
			),
			'tests_exams_years_graduations'=>array(
				'val'=>true,
				'value'=>explode('[@!-#-!@]',$education_data['tests_exams_years_graduations'])
			),

			'electronic_certificates_names'=>array(
				'val'=>true,
				'value'=>explode('[@!-#-!@]',$education_data['electronic_certificates_names'])
			),
			'electronic_certificates_years_graduations'=>array(
				'val'=>true,
				'value'=>explode('[@!-#-!@]',$education_data['electronic_certificates_years_graduations'])
			),
			'electronic_certificates_links'=>array(
				'val'=>true,
				'value'=>explode('[@!-#-!@]',$education_data['electronic_certificates_links'])
			),


		);
	}

	public function selectContacts($id_user){
		try {
			$stmt = $this->_dbc->prepare ("SELECT
													mobile_phone,
													home_phone,
													work_phone,
													email,
													preferred_communication,
													comment_mobile_phone,
													comment_home_phone,
													comment_work_phone,
													icq,
													skype,
													free_lance,
													my_circle,
													linkedln,
													facebook,
													live_journal,
													other_site
												FROM
													profile
												WHERE
													id = :id_user");
			$stmt->execute(array(':id_user'=>$id_user));
			$contacts_data = $stmt->fetch(PDO::FETCH_ASSOC);
		}catch (PDOException $e){
			exit(print_r($e->errorInfo).$e->getFile());
		}
		return array(
			'mobile_phone'=>array(
				'val'=>true,
				'value'=>$contacts_data['mobile_phone'] ),
			'home_phone'=>array(
				'val'=>true,
				'value'=>$contacts_data['home_phone']
			),
			'work_phone'=>array(
				'val'=>true,
				'value'=>$contacts_data['work_phone']),
			'email'=>array(
				'val'=>true,
				'value'=>$contacts_data['email']
			),
			'preferred_communication'=>array('value'=>$contacts_data['preferred_communication']),
			'comment_mobile_phone'=>array('value'=>$contacts_data['comment_mobile_phone']),
			'comment_home_phone'=>array('value'=>$contacts_data['comment_home_phone']),
			'comment_work_phone'=>array('value'=>$contacts_data['comment_work_phone']),
			'icq'=>array('value'=>$contacts_data['icq']),
			'skype'=>array('value'=>$contacts_data['skype']),
			'free_lance'=>array('value'=>$contacts_data['free_lance']),
			'my_circle'=>array('value'=>$contacts_data['my_circle']),
			'linkedln'=>array('value'=>$contacts_data['linkedln']),
			'facebook'=>array('value'=>$contacts_data['facebook']),
			'live_journal'=>array('value'=>$contacts_data['live_journal']),
			'other_site'=>array('value'=>$contacts_data['other_site']),
		);
	}

	public function selectPersonal($id_user){
		try {
			$stmt = $this->_dbc->prepare ("SELECT
													surname,
													first_name,
													patronymic,
													birth,
													sex,
													city,
													move,
													trip,
													nationality,
													work_permit,
													travel_time_work
												FROM
													profile
												WHERE
													id = :id_user");
			$stmt->execute(array(':id_user'=>$id_user));
			$personal_data = $stmt->fetch(PDO::FETCH_ASSOC);
		}catch (PDOException $e){
			exit(print_r($e->errorInfo).$e->getFile());
		}
		$birth  = explode('-',$personal_data['birth']);
		return array(
			'surname'=>array(
				'val'=>true,
				'value'=>$personal_data['surname']),
			'first_name'=>array(
				'val'=>true,
				'value'=>$personal_data['first_name']
			),
			'patronymic'=>array(
				'val'=>true,
				'value'=>$personal_data['patronymic']),
			'birth'=>array(
				'val'=>true,
				'day_birth'=>$birth[0],
				'month_birth'=>$birth[1],
				'year_birth'=>$birth[2]
			),
			'city'=>array(
				'val'=>true,
				'value'=>$personal_data['city']
			),
			'sex'=>array('value'=>$personal_data['sex']),
			'move'=>array('value'=>$personal_data['move']),
			'trip'=>array('value'=>$personal_data['trip']),
			'work_permit'=>array('value'=>$personal_data['work_permit']),
			'nationality'=>array('value'=>$personal_data['nationality']),
			'nationality_other'=>array('value'=>$personal_data['nationality_other']),
			'work_permit_other'=>array('value'=>$personal_data['work_permit_other']),
			'travel_time_work'=>array('value'=>$personal_data['travel_time_work'])
		);
	}

	public function updateExperience($inputs, $id_user){

		foreach($inputs['getting_starteds']['value'] as $key=>$value){
			$getting_starteds[$key] = $value['month'].'-'.$value['year'];
		}
		foreach($inputs['closing_works']['value'] as $key=>$value){
			$closing_works[$key] = $value['month'].'-'.$value['year'];
		}

		try {
			$stmt = $this->_dbc->prepare ("UPDATE
													experience
												SET
													organizations = :organizations,
													positions = :positions,
													regions = :regions,
													sites = :sites,
													field_activities = :field_activities,
													getting_starteds = :getting_starteds,
													closing_works = :closing_works,
													at_the_moments = :at_the_moments,
													functions = :functions,
													key_skills = :key_skills,
													about_self = :about_self,
													recommend_names = :recommend_names,
													recommend_position = :recommend_position,
													recommend_organization = :recommend_organization,
													recommend_phone = :recommend_phone
												WHERE
													id_user = :id_user");
			$stmt->execute(array(
				':organizations'=>implode('[@!-#-!@]',$inputs['organizations']['value']),
				':positions'=>implode('[@!-#-!@]',$inputs['positions']['value']),
				':regions'=>implode('[@!-#-!@]',$inputs['regions']['value']),
				':sites'=>implode('[@!-#-!@]',$inputs['sites']['value']),
				':field_activities'=>implode('[@!-#-!@]',$inputs['field_activities']['value']),
				':getting_starteds'=>implode('[@!-#-!@]',$getting_starteds),
				':closing_works'=>implode('[@!-#-!@]',$closing_works),
				':at_the_moments'=>implode('[@!-#-!@]',$inputs['at_the_moments']['value']),
				':functions'=>implode('[@!-#-!@]',$inputs['functions']['value']),
				':key_skills'=>$inputs['key_skills']['value'],
				':about_self'=>$inputs['about_self']['value'],
				':recommend_names'=>implode('[@!-#-!@]',$inputs['recommend_names']['value']),
				':recommend_position'=>implode('[@!-#-!@]',$inputs['recommend_position']['value']),
				':recommend_organization'=>implode('[@!-#-!@]',$inputs['recommend_organization']['value']),
				':recommend_phone'=>implode('[@!-#-!@]',$inputs['recommend_phone']['value']),
				':id_user'=>$id_user)
			);
		}catch (PDOException $e){
			exit(print_r($e->errorInfo).$e->getFile());
		}
	}

	public function selectExperience($id_user){
		try {
			$stmt = $this->_dbc->prepare("SELECT
													organizations,
													regions,
													positions,
													sites,
													field_activities,
													getting_starteds,
													closing_works,
													at_the_moments,
													functions,
													key_skills,
													about_self,
													recommend_names,
													recommend_position,
													recommend_organization,
													recommend_phone
												FROM
													experience
												WHERE
													id_user = :id_user");
			$stmt->execute(array(':id_user'=>$id_user));
			$experience_data = $stmt->fetch(PDO::FETCH_ASSOC);
		}catch (PDOException $e){
			exit(print_r($e->errorInfo).$e->getFile());
		}


		$getting_start = explode('[@!-#-!@]',$experience_data['getting_starteds']);
		$closing_work = explode('[@!-#-!@]',$experience_data['closing_works']);

		foreach($getting_start as $key=>$value){
			$month_year= explode('-',$value);
			$getting_starteds[$key]['month'] = $month_year[0];
			$getting_starteds[$key]['year'] = $month_year[1];
		}

		foreach($closing_work as $key=>$value){
			$month_year= explode('-',$value);
			$closing_works[$key]['month'] = $month_year[0];
			$closing_works[$key]['year'] = $month_year[1];
		}


		return array(
			'organizations'=>array(
				'val'=>true,
				'value'=>explode('[@!-#-!@]',$experience_data['organizations'])
			),
			'positions'=>array(
				'val'=>true,
				'value'=>explode('[@!-#-!@]',$experience_data['positions'])
			),
			'getting_starteds'=>array(
				'val'=>true,
				'value'=>$getting_starteds
			),

			'closing_works'=>array(
				'val'=>true,
				'value'=>$closing_works
			),
			'at_the_moments'=>array(
				'value'=>explode('[@!-#-!@]',$experience_data['at_the_moments'])
			),
			'regions'=>array(
				'value'=>explode('[@!-#-!@]',$experience_data['regions'])
			),
			'sites'=>array(
				'value'=>explode('[@!-#-!@]',$experience_data['sites'])
			),
			'field_activities'=>array(
				'value'=>explode('[@!-#-!@]',$experience_data['field_activities'])
			),
			'functions'=>array(
				'val'=>true,
				'value'=>explode('[@!-#-!@]',$experience_data['functions'])
			),
			'key_skills'=>array(
				'val'=>true,
				'value'=>$experience_data['key_skills']
			),
			'about_self'=>array(
				'val'=>true,
				'value'=>$experience_data['about_self']
			),
			'recommend_names'=>array(
				'val'=>true,
				'value'=>explode('[@!-#-!@]',$experience_data['recommend_names'])
			),
			'recommend_position'=>array(
				'val'=>true,
				'value'=>explode('[@!-#-!@]',$experience_data['recommend_position'])
			),
			'recommend_organization'=>array(
				'val'=>true,
				'value'=>explode('[@!-#-!@]',$experience_data['recommend_organization'])
			),

			'recommend_phone'=>array(
				'val'=>true,
				'value'=>explode('[@!-#-!@]',$experience_data['recommend_phone'])
			),
		);
	}


	public function selectPhotoID($id_user){
		try {
			$stmt = $this->_dbc->prepare ("SELECT photo FROM profile WHERE id = :id_user");
			$stmt->execute(array(':id_user'=>$id_user));
			$photo_name = $stmt->fetch(PDO::FETCH_ASSOC);
		}catch (PDOException $e){
			exit(print_r($e->errorInfo).$e->getFile());
		}
		return $photo_name['photo'];
	}

	public function getIdUser(){
		return $this->_id;
	}
}