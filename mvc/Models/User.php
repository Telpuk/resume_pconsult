<?php
class User{
	private $_dbc;
	private $_id;
	private $_month = array(1=>"январь",
		2=>"февраль",
		3=>"март",
		4=>"апрель",
		5=>"май",
		6=>"июнь",
		7=>"июль",
		8=>"август",
		9=>"сентябрь",
		10=>"октябрь",
		11=>"ноябрь",
		12=>"декабрь");

	function __construct(){
		$this->_dbc = Model::getInstance()->getDbh();
	}

	public function finishResume($id_user){
		try {
			$stmt = $this->_dbc->prepare ("UPDATE
													profile
												SET
													registered_user = :registered_user
												WHERE
													id = :id_user");
			$stmt->execute(array('registered_user'=>'yes',':id_user'=>$id_user)
			);
		}catch (PDOException $e){
			exit(print_r($e->errorInfo).$e->getFile());
		}
	}

	public function json_encode_cyr($str){
		$arr_replace_utf = array('\u0410', '\u0430', '\u0411', '\u0431', '\u0412', '\u0432', '\u0413', '\u0433', '\u0414', '\u0434', '\u0415', '\u0435', '\u0401', '\u0451', '\u0416', '\u0436', '\u0417', '\u0437', '\u0418', '\u0438', '\u0419', '\u0439', '\u041a', '\u043a', '\u041b', '\u043b', '\u041c', '\u043c', '\u041d', '\u043d', '\u041e', '\u043e', '\u041f', '\u043f', '\u0420', '\u0440', '\u0421', '\u0441', '\u0422', '\u0442', '\u0423', '\u0443', '\u0424', '\u0444', '\u0425', '\u0445', '\u0426', '\u0446', '\u0427', '\u0447', '\u0428', '\u0448', '\u0429', '\u0449', '\u042a', '\u044a', '\u042b', '\u044b', '\u042c', '\u044c', '\u042d', '\u044d', '\u042e', '\u044e', '\u042f', '\u044f');
		$arr_replace_cyr = array('А', 'а', 'Б', 'б', 'В', 'в', 'Г', 'г', 'Д', 'д', 'Е', 'е', 'Ё', 'ё', 'Ж', 'ж', 'З', 'з', 'И', 'и', 'Й', 'й', 'К', 'к', 'Л', 'л', 'М', 'м', 'Н', 'н', 'О', 'о', 'П', 'п', 'Р', 'р', 'С', 'с', 'Т', 'т', 'У', 'у', 'Ф', 'ф', 'Х', 'х', 'Ц', 'ц', 'Ч', 'ч', 'Ш', 'ш', 'Щ', 'щ', 'Ъ', 'ъ', 'Ы', 'ы', 'Ь', 'ь', 'Э', 'э', 'Ю', 'ю', 'Я', 'я');
		$str1 = json_encode($str);
		$str2 = str_replace($arr_replace_utf, $arr_replace_cyr, $str1);
		return $str2;
	}

	public function selectAutocomplete(){
		$stmt = $this->_dbc->query("SELECT DISTINCT key_skills, regions FROM experience");
		$autocomplete = $stmt->fetchAll(PDO::FETCH_ASSOC);

		foreach ($autocomplete as $key => $value) {
			foreach (explode('[@!-#-!@]', $value['key_skills']) as $key => $val) {
				$autocomplete_arr['key_skills'][] = $val;
			}
			foreach (explode('[@!-#-!@]', $value['regions']) as $key => $val) {
				$autocomplete_arr['regions'][] = $val;
			}
		}

		$autocomplete_arr['key_skills'] = array_filter($autocomplete_arr['key_skills'], function($el){
			return !empty($el);}
		);
		$autocomplete_arr['regions'] = array_filter($autocomplete_arr['regions'], function($el){
			return !empty($el);}
		);

		$autocomplete_arr['key_skills'] = array_slice(array_unique($autocomplete_arr['key_skills']), 0);
		$autocomplete_arr['regions'] = array_slice(array_unique($autocomplete_arr['regions']), 0);

		$json['key_skills'] = $this->json_encode_cyr($autocomplete_arr['key_skills']);
		$json['regions'] = $this->json_encode_cyr($autocomplete_arr['regions']);

		return $this->json_encode_cyr($json);
	}

	public function deleteResume($id_user){
		try {
			$this->_dbc->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$this->_dbc->beginTransaction();

			$stmt = $this->_dbc->prepare ("SELECT
													photo
												FROM
													profile
												WHERE
													id = :id_user");
			$stmt->execute(array(':id_user'=>$id_user));
			$photo = $stmt->fetch(PDO::FETCH_ASSOC);

			if($photo['photo'] !=='no-photo.png'){
				@unlink(DIR_PROJECT."/files/photo/".$photo['photo']);
			}

			$stmt = $this->_dbc->prepare ("DELETE FROM profile WHERE id = :id_user");
			$stmt->execute(array(':id_user'=>$id_user));
			$this->_dbc->commit();
		}catch (PDOException $e){
			$this->_dbc->rollBack();
			exit(print_r($e->errorInfo).$e->getFile().$e->getCode().$e->getLine());
		}
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

	public function selectPersonalData($id_user){
		try {
			$stmt = $this->_dbc->prepare (
				"SELECT
					CONCAT_WS(' ',prof.surname, prof.first_name, prof.patronymic)AS'name',
			  prof.photo,

			  prof.birth,
			  prof.sex,
			  prof.city,
			  prof.move,
			  prof.trip,
			  prof.nationality,
			  prof.work_permit,
			  prof.travel_time_work,

			  prof.mobile_phone,
			  prof.home_phone,
			  prof.work_phone,
			  prof.email,
			  prof.preferred_communication,

			  prof.icq,
			  prof.skype,
			  prof.free_lance,
			  prof.my_circle,
			  prof.linkedln,
			  prof.facebook,
			  prof.live_journal,
			  prof.other_site,

			  prof.desired_position,
			  prof.professional_area,

			  prof.salary,
			  prof.currency,

			  REPLACE(prof.schedule, '[@!-#-!@]', ', ') AS 'schedule',
			  REPLACE(prof.employment, '[@!-#-!@]', ', ') AS 'employment',

			  educ.names_institutions AS 'names_institutions',
			  educ.faculties AS 'faculties',
			  educ.specialties_specialties AS 'specialties_specialties',
			  educ.years_graduations AS 'years_graduations',

			  educ.native_language AS 'native_language',
			  educ.language_english AS 'language_english',
			  educ.language_germany AS 'language_germany',
			  educ.language_french AS 'language_french',
		      educ.language_further AS 'language_further',
		      educ.language_further_level AS 'language_further_level',

              educ.courses_names AS 'courses_names',
              educ.follow_organizations AS 'follow_organizations',
              educ.courses_specialties AS 'courses_specialties',
              educ.course_years_graduations AS 'course_years_graduations',

			  educ.tests_exams_names AS 'tests_exams_names',
              educ.tests_exams_follow_organizations AS 'tests_exams_follow_organizations',
              educ.tests_exams_specialty AS 'tests_exams_specialty',
              educ.tests_exams_years_graduations AS'tests_exams_years_graduations',

              educ.electronic_certificates_names AS 'electronic_certificates_names',
              educ.electronic_certificates_years_graduations AS 'electronic_certificates_years_graduations',
              educ.electronic_certificates_links AS'electronic_certificates_links',

			exper.organizations AS 'experience_organizations',
			exper.regions AS 'experience_regions',
			exper.positions AS 'experience_positions',
			exper.sites AS 'experience_sites',
			exper.field_activities AS 'experience_field_activities',
			exper.getting_starteds AS 'experience_getting_starteds',
			exper.closing_works AS 'experience_closing_works',
			exper.at_the_moments AS 'experience_at_the_moments',
			exper.functions AS 'experience_functions',
			REPLACE(exper.key_skills,'[@!-#-!@]', '&nbsp;&nbsp;&nbsp;') AS 'experience_key_skills',
			exper.about_self AS 'experience_about_self',
			exper.recommend_names AS 'experience_recommend_names',
			exper.recommend_position AS 'experience_recommend_position',
			exper.recommend_organization AS 'experience_recommend_organization',
			exper.recommend_phone AS 'experience_recommend_phone'
				FROM
					profile AS prof,
					education AS educ,
					experience AS exper
				WHERE
					educ.id_user = prof.id AND
					exper.id_user = prof.id AND
					prof.id = :id_user"
			);
			$stmt->execute(array(':id_user'=>$id_user));
			$personal_data = $stmt->fetch(PDO::FETCH_ASSOC);
		}catch (PDOException $e){
			exit(print_r($e->errorInfo).$e->getFile());
		}



		$personal['name'] = trim($personal_data['name']);

		$personal['photo'] = trim($personal_data['photo']);

		$personal['birth_sex_city_move_trip'] = $this->_getBerBirthSexCityMoveTrip(
			array(
				'birth'=>$personal_data['birth'],
				'sex'=>$personal_data['sex'],
				'move'=>$personal_data['move'],
				'city'=>$personal_data['city'],
				'trip'=>$personal_data['trip']
			)
		);

		$personal['experience_organizations'] = $this->_getExperienceOrganizations(
			array(
				'experience_organizations'=>explode('[@!-#-!@]',$personal_data['experience_organizations']),
				'experience_getting_starteds'=>explode('[@!-#-!@]',$personal_data['experience_getting_starteds']),
				'experience_closing_works'=>explode('[@!-#-!@]',$personal_data['experience_closing_works']),
				'experience_at_the_moments'=>explode('[@!-#-!@]',$personal_data['experience_at_the_moments']),
				'experience_regions'=>explode('[@!-#-!@]',$personal_data['experience_regions']),
				'experience_sites'=>explode('[@!-#-!@]',$personal_data['experience_sites']),
				'experience_field_activities'=>explode('[@!-#-!@]',$personal_data['experience_field_activities']),
				'experience_positions'=>explode('[@!-#-!@]',$personal_data['experience_positions']),
				'experience_functions'=>explode('[@!-#-!@]',$personal_data['experience_functions'])
			)
		);

		$personal['experience_key_skills'] = $personal_data['experience_key_skills'];

		$personal['experience_about_self'] = $personal_data['experience_about_self'];
		$personal['experience_recommend'] = $this->_getExperienceRecommend(
			array(
				'experience_recommend_names'=>explode('[@!-#-!@]',$personal_data['experience_recommend_names']),
				'experience_recommend_position'=>explode('[@!-#-!@]',$personal_data['experience_recommend_position']),
				'experience_recommend_organization'=>explode('[@!-#-!@]',$personal_data['experience_recommend_organization']),
				'experience_recommend_phone'=>explode('[@!-#-!@]',$personal_data['experience_recommend_phone']),
			)
		);

		$personal['desired_position'] = $personal_data['desired_position'];

		$personal['salary'] = $personal_data['salary']?$personal_data['salary']." ".$personal_data['currency']:'';
		$personal['professional_area'] = $personal_data['professional_area'];

		$personal['employment'] = sprintf('Занятость: %s', $personal_data['employment']);
		$personal['schedule'] = sprintf('График работы: %s', $personal_data['schedule']);

		$personal['nationality_work_permit'] = $this->_getNationalityWorkPermit(
			array(
				'nationality'=> $personal_data['nationality'],
				'work_permit'=> $personal_data['work_permit'],
				'travel_time_work'=> $personal_data['travel_time_work'],
			)
		);

		$personal['institutions'] = $this->_getNamesInstitutions(
			array(
				'names_institutions'=>explode('[@!-#-!@]',$personal_data['names_institutions']),
				'faculties'=>explode('[@!-#-!@]',$personal_data['faculties']),
				'specialties_specialties'=>explode('[@!-#-!@]',$personal_data['specialties_specialties']),
				'years_graduations'=>explode('[@!-#-!@]',$personal_data['years_graduations']),
			));

		$personal['courses_names'] = $this->_getNamesCourses(
			array(
				'courses_names'=>explode('[@!-#-!@]',$personal_data['courses_names']),
				'follow_organizations'=>explode('[@!-#-!@]',$personal_data['follow_organizations']),
				'courses_specialties'=>explode('[@!-#-!@]',$personal_data['courses_specialties']),
				'course_years_graduations'=>explode('[@!-#-!@]',$personal_data['course_years_graduations']),
			));

		$personal['tests_exams_names'] = $this->_getTestsExamsNames(
			array(
				'tests_exams_names'=>explode('[@!-#-!@]',$personal_data['tests_exams_names']),
				'tests_exams_follow_organizations'=>explode('[@!-#-!@]',$personal_data['tests_exams_follow_organizations']),
				'tests_exams_specialty'=>explode('[@!-#-!@]',$personal_data['tests_exams_specialty']),
				'tests_exams_years_graduations'=>explode('[@!-#-!@]',$personal_data['tests_exams_years_graduations']),
			));


		$personal['electronic_certificates_names'] = $this->_getElectronicSertificates(
			array(
				'electronic_certificates_names'=>explode('[@!-#-!@]',$personal_data['electronic_certificates_names']),
				'electronic_certificates_years_graduations'=>explode('[@!-#-!@]',$personal_data['electronic_certificates_years_graduations']),
				'electronic_certificates_links'=>explode('[@!-#-!@]',$personal_data['electronic_certificates_links'])
			));

		$personal['languages'] = $this->_getLanguage(
			array(
				'native_language'=>$personal_data['native_language'],
				'language_english'=>$personal_data['language_english'],
				'language_germany'=>$personal_data['language_germany'],
				'language_french'=>$personal_data['language_french'],
				'language_further'=>explode('[@!-#-!@]',$personal_data['language_further']),
				'language_further_level'=>explode('[@!-#-!@]',$personal_data['language_further_level']),
			));

		$personal['call_me'] = $this->_getCallMe(
			array(
				'mobile_phone'=>$personal_data['mobile_phone'],
				'home_phone'=>$personal_data['home_phone'],
				'work_phone'=>$personal_data['work_phone'],
				'email'=>$personal_data['email'],
				'preferred_communication'=>$personal_data['preferred_communication'],
				'icq'=>$personal_data['icq'],
				'skype'=>$personal_data['skype'],
				'free_lance'=>$personal_data['free_lance'],
				'my_circle'=>$personal_data['my_circle'],
				'linkedln'=>$personal_data['linkedln'],
				'facebook'=>$personal_data['facebook'],
				'live_journal'=>$personal_data['live_journal'],
				'other_site'=>$personal_data['other_site'],
			));

		return $personal;
	}

	private function _getNationalityWorkPermit($personal_data){
		$data='';
		if($personal_data['nationality']){
			$data.="<p>Гражданство: {$personal_data['nationality']}</p>";
		}
		if($personal_data['work_permit']){
			$data.="<p>Разрешение на работу: {$personal_data['work_permit']}</p>";
		}
		if($personal_data['travel_time_work']){
			$data.="<p>Желательное время в пути до работы: {$personal_data['travel_time_work']}</p>";
		}
		return $data;
	}

	private function _getExperienceRecommend($personal_data){
		$data='';
		if($personal_data['experience_recommend_names'][0]) {
			foreach ($personal_data['experience_recommend_names'] as $key => $experience_recommend_names) {
				$data .= "<p><b>{$personal_data['experience_recommend_organization'][$key]}</b><br>" .
					"{$experience_recommend_names} ({$personal_data['experience_recommend_position'][$key]})<br>" .
					"{$personal_data['experience_recommend_phone'][$key]}</p>";

			}
		}
		return $data;
	}

	private function _getExperienceOrganizations($personal_data){
		$data='';
		if($personal_data['experience_organizations'][0]){
			$data='<table>';
			foreach($personal_data['experience_organizations'] as $key=>$organizations){

				$starteds = explode('-',$personal_data['experience_getting_starteds'][$key]);
				$starteds[0] = $this->_month[$starteds[0]];
				$starteds = implode($starteds,' ');

				$data.="<tr>"
					."<td>{$starteds}";
				if($personal_data['experience_at_the_moments'][$key]=='true'){
					$data.="&mdash; по ностоящее время</td>";
				}else{
					$closing = explode('-',$personal_data['experience_closing_works'][$key]);
					$closing[0] = $this->_month[$closing[0]];
					$closing = implode($closing,' ');
					$data.="&mdash; {$closing}</td>";
				}
				$data .="<td>"
					."<b>$organizations</b><br>"
					."{$personal_data['experience_regions'][$key]}";
				if($personal_data['experience_sites'][$key])
					$data .=",{$personal_data['experience_sites'][$key]}";

				if($personal_data['experience_field_activities'][$key])
					$data .="<br>{$personal_data['experience_field_activities'][$key]}";

				$data .="<br><br><b>{$personal_data['experience_positions'][$key]}</b><br>"
					."{$personal_data['experience_functions'][$key]}</td>"
					."</tr>";
			}

			$data .="<table>";

		}

		return $data;
	}

	private function _getBerBirthSexCityMoveTrip($personal_data){
		$trip = array('never'=>'никогда','ready'=>'готов','sometimes'=>'иногда');
		$move = array('no'=>'невозможен','yes'=>'возможен','desirable'=>'желателен');


		$birth_array = explode('-',$personal_data['birth']);
		$birth_array[1] = $this->_month[$birth_array[1]];
		$birth = implode($birth_array,' ');

		return sprintf(
			'<b>%s</b> &#183; <b>%s</b> пол &#183; <b>%s</b> &#183;  Переезд: <b>%s</b> &#183; Готовность командировкам: <b>%s</b>',
			($birth !== '  ')?$birth:'не указано',
			$personal_data['sex'],
			$personal_data['city'],
			$move[$personal_data['move']],
			$trip[$personal_data['trip']]);
	}

	private function _getElectronicSertificates($personal_data){
		$data ='';
		if($personal_data['electronic_certificates_names'][0]){
			$data = '<table>';
			foreach ($personal_data['electronic_certificates_names'] as $key => $name) {
				$data .= "<tr>"
					."<td>{$personal_data['electronic_certificates_years_graduations'][$key]}<td>"
					."<td>{$name}
<span><a href='{$personal_data['electronic_certificates_links'][$key]}'
target='_blank'>{$personal_data['electronic_certificates_links'][$key]}</a></span></td>"
					."</tr>";
			}
			$data .= '<table>';
		}
		return $data;
	}

	private function _getTestsExamsNames($personal_data){
		$data ='';
		if($personal_data['tests_exams_names'][0]){
			$data = '<table>';
			foreach ($personal_data['tests_exams_names'] as $key => $name) {
				$data .= "<tr>"
					."<td>{$personal_data['tests_exams_years_graduations'][$key]}<td>"
					."<td>{$personal_data['tests_exams_follow_organizations'][$key]}<span>{$name},
					{$personal_data['tests_exams_specialty'][$key]}</span></td>"
					."</tr>";
			}
			$data .= '<table>';
		}
		return $data;
	}

	private function _getNamesCourses($personal_data){
		$data ='';
		if($personal_data['courses_names'][0]){
			$data = '<table>';
			foreach ($personal_data['courses_names'] as $key => $name) {
				$data .= "<tr>"
					."<td>{$personal_data['course_years_graduations'][$key]}<td>"
					."<td>{$personal_data['follow_organizations'][$key]}<span>{$name},
					{$personal_data['courses_specialties'][$key]}</span></td>"
					."</tr>";
			}
			$data .= '<table>';
		}
		return $data;
	}

	private function _getLanguage($personal_data){
		$data ='';
		$language = array(
			'native_language'=>'родной',
			'language_english'=>'английский',
			'language_germany'=>'немецкий',
			'language_french'=>'французский'
		);
		foreach ($personal_data as $key => $value) {
			if($value !== 'Не владею' && !is_array($value)){
				$data .= "<p>".$language[$key]."&mdash;".$value."</p>";
			}else if(is_array($value) && $key === 'language_further'){
				foreach($value as $key=>$f_lang){
					if(!empty($f_lang)){
						$data .= "<p>".$f_lang."&mdash;".$personal_data['language_further_level'][$key]."</p>";
					}

				}

			}

		}

		return $data;
	}

	private function _getNamesInstitutions($personal_data){
		$data ='';
		if($personal_data['names_institutions'][0]){
			$data = '<table>';
			foreach ($personal_data['names_institutions'] as $key => $name_institution) {
				$data .= "<tr>"
					."<td>{$personal_data['years_graduations'][$key]}<td>"
					."<td>{$name_institution}<span>{$personal_data['faculties'][$key]},
					{$personal_data['specialties_specialties'][$key]}</span></td>"
					."</tr>";
			}
			$data .= '<table>';
		}
		return $data;
	}


	private function _getCallMe($personal_data){
		$call_me = '';
		if($personal_data['mobile_phone']){
			if($personal_data['preferred_communication']==1){
				$call_me .= "<p><img src='".BASE_URL."/public/img/phone.png'>{$personal_data['mobile_phone']}<span>желаемый способ связи</span></p>";
			}else{
				$call_me .= "<p><img src='".BASE_URL."/public/img/phone.png'>{$personal_data['mobile_phone']}</p>";
			}
		}
		if($personal_data['home_phone']){
			if($personal_data['preferred_communication']==2){
				$call_me .= "<p><img src='".BASE_URL."/public/img/phone.png'>{$personal_data['home_phone']}<span>желаемый способ связи</span></p>";
			}else{
				$call_me .= "<p><img src='".BASE_URL."/public/img/phone.png'>{$personal_data['home_phone']}</p>";
			}

		}
		if($personal_data['work_phone']){
			if($personal_data['preferred_communication']==3) {
				$call_me .= "<p><img src='".BASE_URL."/public/img/phone.png'>{$personal_data['work_phone']}<span>желаемый способ связи</span></p>";
			}else{
				$call_me .= "<p><img src='".BASE_URL."/public/img/phone.png'>{$personal_data['work_phone']}</p>";
			}
		}
		if($personal_data['email']){
			if($personal_data['preferred_communication']==4) {
				$call_me .= "<p><img src='".BASE_URL."/public/img/mail.png'>{$personal_data['email']}<span>желаемый способ связи</span></p>";
			}else{
				$call_me .= "<p><img src='".BASE_URL."/public/img/mail.png'>{$personal_data['email']}</p>";
			}
		}
		if($personal_data['icq']){
			$call_me .= "<p><img src='".BASE_URL."/public/img/icq.png'>{$personal_data['icq']}</p>";
		}
		if($personal_data['skype']){
			$call_me .= "<p><img src='".BASE_URL."/public/img/skype.png'>{$personal_data['skype']}</p>";
		}
		if($personal_data['free_lance']){
			$call_me .= "<p><img src='".BASE_URL."/public/img/freelance.png'>{$personal_data['free_lance']}</p>";
		}
		if($personal_data['my_circle']){
			$call_me .= "<p><img src='".BASE_URL."/public/img/moykrug.png'>{$personal_data['my_circle']}</p>";
		}
		if($personal_data['linkedln']){
			$call_me .= "<p><img src='".BASE_URL."/public/img/linkedin.png'>{$personal_data['linkedln']}</p>";
		}
		if($personal_data['facebook']){
			$call_me .= "<p><img src='".BASE_URL."/public/img/facebook.png'>{$personal_data['facebook']}</p>";
		}
		if($personal_data['live_journal']){
			$call_me .= "<p><img src='".BASE_URL."/public/img/livejournal.png'>{$personal_data['live_journal']}</p>";
		}
		if($personal_data['other_site']){
			$call_me .= "<p>{$personal_data['other_site']}</p>";
		}


		return $call_me;
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
													electronic_certificates_links =:electronic_certificates_links,

													native_language = :native_language,
													language_english = :language_english,
													language_germany = :language_germany,
													language_french = :language_french,

													language_further = :language_further,
													language_further_level = :language_further_level

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

				':language_further'=>implode('[@!-#-!@]',$inputs['language_further']['value']),
				':language_further_level'=>implode('[@!-#-!@]',$inputs['language_further_level']['value']),

				'native_language'=>$inputs['native_language']['value'],
				'language_english'=>$inputs['language_english']['value'],
				'language_germany'=>$inputs['language_germany']['value'],
				'language_french'=>$inputs['language_french']['value'],

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
													electronic_certificates_links,
													native_language,
													language_english,
													language_germany,
													language_french,
													language_further,
													language_further_level
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
			'native_language'=>array(
				'val'=>true,
				'value'=>$education_data['native_language'],
			),
			'language_english'=>array(
				'val'=>true,
				'value'=>$education_data['language_english'],
			),
			'language_germany'=>array(
				'val'=>true,
				'value'=>$education_data['language_germany'],
			),
			'language_french'=>array(
				'val'=>true,
				'value'=>$education_data['language_french'],
			),
			'language_further'=>array(
				'val'=>true,
				'value'=>explode('[@!-#-!@]',$education_data['language_further'])
			),
			'language_further_level'=>array(
				'val'=>true,
				'value'=>explode('[@!-#-!@]',$education_data['language_further_level'])
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
				':key_skills'=>implode('[@!-#-!@]',$inputs['key_skills']['value']),
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
				'value'=>explode('[@!-#-!@]',$experience_data['key_skills'])
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