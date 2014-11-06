<?php
class Excel{
	private $_dbc;
	private $_month = array(1 => "январь", 2 => "февраль", 3 => "март", 4 => "апрель", 5 => "май", 6 => "июнь", 7 => "июль", 8 => "август", 9 => "сентябрь", 10 => "октябрь", 11 => "ноябрь", 12 => "декабрь");

	function __construct(){
		$this->_dbc = Model::getInstance()->getDbh();
	}


	public function  selectCommetsExport($id_user){
		try {
			$stmt = $this->_dbc->prepare ("SELECT
												comments.id,
												comments.comment,
												comments.id_admin,
												DATE_FORMAT(comments.date,'%Y-%m-%d %H:%i')as 'date',
												users.type_user,
												CONCAT_WS(' ', name_second, name_first, patronymic) as 'name'
						                	FROM
						                    	comments,
						                    	users
                                        	WHERE
                                        		comments.id_admin = users.id
                                        	AND
                                        		comments.id_user = :id_user ORDER BY date DESC");
			$stmt->execute(array(':id_user'=>$id_user));
			$data = $stmt->fetchAll(PDO::FETCH_ASSOC);
		}catch (PDOException $e){
			exit(print_r($e->errorInfo).$e->getFile());
		}
		return $data;
	}


	public function excelExport($id_user){
		try {
			$stmt = $this->_dbc->prepare("CALL selectPersonalData(:id_user)");
			$stmt->execute(array(':id_user'=>$id_user));
			$personal_data = $stmt->fetch(PDO::FETCH_ASSOC);
			if(!$personal_data){
				return false;
			}
		}catch (PDOException $e){
			exit(print_r($e->errorInfo).$e->getFile());
		}

		$personal_data['call_me'] = $this->_getCallMeNoHTML(array(
			'comment_mobile_phone'=>$personal_data['comment_mobile_phone']?"("
				.$personal_data['comment_mobile_phone'].")":'',
			'comment_home_phone'=>$personal_data['comment_home_phone']?"(".$personal_data['comment_home_phone']
				.")":'',
			'comment_work_phone'=>$personal_data['comment_work_phone']?"(".$personal_data['comment_work_phone']
				.")":'',
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

		$personal_data['birth_sex_city_move_trip'] = $this->_getBerBirthSexCityMoveTrip(
			array(
				'sex'=>$personal_data['sex'],
				'move'=>$personal_data['move'],
				'city'=>$personal_data['city'],
				'trip'=>$personal_data['trip']
			)
		);
		$personal_data['experience_key_skills'] = str_replace(' ',', ',str_replace('&nbsp;','',
			$personal_data['experience_key_skills']));

		$personal_data['old'] = $this->_getOld(array('birth'=>$personal_data['birth']));

		$personal_data['institutions']=$this->_getNamesInstitutionsNoHTML(
			array(
				'names_institutions'=>explode('[@!-#-!@]',$personal_data['names_institutions']),
				'faculties'=>explode('[@!-#-!@]',$personal_data['faculties']),
				'specialties_specialties'=>explode('[@!-#-!@]',$personal_data['specialties_specialties']),
				'years_graduations'=>explode('[@!-#-!@]',$personal_data['years_graduations']),
			));

		$personal_data['institutions']=$this->_getNamesInstitutionsNoHTML(
			array(
				'names_institutions'=>explode('[@!-#-!@]',$personal_data['names_institutions']),
				'faculties'=>explode('[@!-#-!@]',$personal_data['faculties']),
				'specialties_specialties'=>explode('[@!-#-!@]',$personal_data['specialties_specialties']),
				'years_graduations'=>explode('[@!-#-!@]',$personal_data['years_graduations']),
			));


		$personal_data['courses_names'] = $this->_getNamesCoursesNoHTML(
			array(
				'courses_names'=>explode('[@!-#-!@]',$personal_data['courses_names']),
				'follow_organizations'=>explode('[@!-#-!@]',$personal_data['follow_organizations']),
				'courses_specialties'=>explode('[@!-#-!@]',$personal_data['courses_specialties']),
				'course_years_graduations'=>explode('[@!-#-!@]',$personal_data['course_years_graduations']),
			));

		$personal_data['tests_exams_names'] = $this->_getTestsExamsNamesNoHTML(
			array(
				'tests_exams_names'=>explode('[@!-#-!@]',$personal_data['tests_exams_names']),
				'tests_exams_follow_organizations'=>explode('[@!-#-!@]',$personal_data['tests_exams_follow_organizations']),
				'tests_exams_specialty'=>explode('[@!-#-!@]',$personal_data['tests_exams_specialty']),
				'tests_exams_years_graduations'=>explode('[@!-#-!@]',$personal_data['tests_exams_years_graduations']),
			));


		$personal_data['electronic_certificates_names'] = $this->_getElectronicSertificatesNoHTML(
			array(
				'electronic_certificates_names'=>explode('[@!-#-!@]',$personal_data['electronic_certificates_names']),
				'electronic_certificates_years_graduations'=>explode('[@!-#-!@]',$personal_data['electronic_certificates_years_graduations']),
				'electronic_certificates_links'=>explode('[@!-#-!@]',$personal_data['electronic_certificates_links'])
			));

		$personal_data['languages'] = $this->_getLanguageNoHTML(
			array(
				'native_language'=>$personal_data['native_language'],
				'language_english'=>$personal_data['language_english'],
				'language_germany'=>$personal_data['language_germany'],
				'language_french'=>$personal_data['language_french'],
				'language_further'=>explode('[@!-#-!@]',$personal_data['language_further']),
				'language_further_level'=>explode('[@!-#-!@]',$personal_data['language_further_level']),
			));
		$experience_count = $this->getExperienceCount(
			array(
				'experience_getting_starteds'=>explode('[@!-#-!@]',$personal_data['experience_getting_starteds']),
				'experience_closing_works'=>explode('[@!-#-!@]',$personal_data['experience_closing_works']),
				'experience_at_the_moments'=>explode('[@!-#-!@]',$personal_data['experience_at_the_moments'])
			)
		);

		$personal_data['experience_recommend'] = $this->_getExperienceRecommendNoHTML(
			array(
				'experience_recommend_names'=>explode('[@!-#-!@]',$personal_data['experience_recommend_names']),
				'experience_recommend_position'=>explode('[@!-#-!@]',$personal_data['experience_recommend_position']),
				'experience_recommend_organization'=>explode('[@!-#-!@]',$personal_data['experience_recommend_organization']),
				'experience_recommend_phone'=>explode('[@!-#-!@]',$personal_data['experience_recommend_phone']),
			)
		);

		$personal_data['sum_experience'] = $experience_count['sum'];

		$personal_data['experience_organizations'] = $this->_getExperienceOrganizationsNoHTML(
			array(
				'experience_count' =>$experience_count,
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

		return $personal_data;
	}

	private function _getExperienceRecommendNoHTML($personal_data){
		$data=array();
		if($personal_data['experience_recommend_names'][0]) {
			foreach ($personal_data['experience_recommend_names'] as $key => $value) {
				$data['experience_recommend_organization'][$key] = $personal_data['experience_recommend_organization'][$key];
				$data['experience_recommend_names'][$key] = $personal_data['experience_recommend_names'][$key];
				$data['experience_recommend_position'][$key] = $personal_data['experience_recommend_position'][$key];
				$data['experience_recommend_phone'][$key] = $personal_data['experience_recommend_phone'][$key];
			}
		}
		return $data;
	}

	private function _getExperienceOrganizationsNoHTML($personal_data){
		$data=array();
		if($personal_data['experience_organizations'][0]){
			foreach($personal_data['experience_organizations'] as $key=>$organizations){

				$starteds = explode('-',$personal_data['experience_getting_starteds'][$key]);
				$starteds[1] = $this->_month[$starteds[1]];
				$starteds = implode(array_reverse($starteds),' ');

				$data[$key]['experience_getting_starteds'] = $starteds;

				if($personal_data['experience_at_the_moments'][$key]=='true'){
					$data[$key]['experience_getting_starteds'] .= " - по настоящее время";
					$data[$key]['experience_count'] =  $personal_data['experience_count'][$key];
				}else{
					$closing = explode('-',$personal_data['experience_closing_works'][$key]);
					$closing[1] = $this->_month[$closing[1]];
					$closing = implode(array_reverse($closing),' ');
					$data[$key]['experience_getting_starteds'] .=" - $closing";
					$data[$key]['experience_count'] = $personal_data['experience_count'][$key];
				}
				$data[$key]['experience_organizations'] = $organizations;
				$data[$key]['experience_regions']=$personal_data['experience_regions'][$key];
				if($personal_data['experience_sites'][$key])
					$data[$key]['experience_sites'] = $personal_data['experience_sites'][$key];

				if($personal_data['experience_field_activities'][$key])
					$data[$key]['experience_field_activities'] = $personal_data['experience_field_activities'][$key];

				$data[$key]['experience_positions'] = $personal_data['experience_positions'][$key];
				$data[$key]['experience_functions'] = $personal_data['experience_functions'][$key];
			}

		}

		return $data;
	}

	private function _getElectronicSertificatesNoHTML($personal_data){
		$data =array();
		if($personal_data['electronic_certificates_names'][0]){
			foreach ($personal_data['electronic_certificates_names'] as $key => $name) {
				$data[$key]['electronic_certificates_names'] = $name;
				$data[$key]['electronic_certificates_years_graduations'] = $personal_data['electronic_certificates_years_graduations'][$key];
				$data[$key]['electronic_certificates_links'] = $personal_data['electronic_certificates_links'][$key];
			}
		}
		return $data;
	}

	private function _getNamesCoursesNoHTML($personal_data){
		$data =array();
		if($personal_data['courses_names'][0]){
			foreach ($personal_data['courses_names'] as $key => $name) {
				$data[$key]['courses_names'] = $name;
				$data[$key]['course_years_graduations'] = $personal_data['course_years_graduations'][$key];
				$data[$key]['follow_organizations'] = $personal_data['follow_organizations'][$key];
				$data[$key]['courses_specialties'] = $personal_data['courses_specialties'][$key];
			}
		}
		return $data;
	}

	private function _getTestsExamsNamesNoHTML($personal_data){
		$data =array();
		if($personal_data['tests_exams_names'][0]){
			if($personal_data['tests_exams_names'][0]){
				foreach ($personal_data['tests_exams_names'] as $key => $name) {
					$data[$key]['tests_exams_names'] = $name;
					$data[$key]['tests_exams_years_graduations'] = $personal_data['tests_exams_years_graduations'][$key];
					$data[$key]['tests_exams_follow_organizations'] = $personal_data['tests_exams_follow_organizations'][$key];
					$data[$key]['tests_exams_specialty'] = $personal_data['tests_exams_specialty'][$key];
				}
			}
		}
		return $data;
	}

	private function _getLanguageNoHTML($personal_data){
		$data =array();
		$language = array(
			'native_language'=>'родной',
			'language_english'=>'английский',
			'language_germany'=>'немецкий',
			'language_french'=>'французский'
		);
		foreach ($personal_data as $key => $value) {
			if($value !== 'Не владею' && !is_array($value)){
				$data[] = $language[$key]."—".$value;
			}else if(is_array($value) && $key === 'language_further'){
				foreach($value as $key=>$f_lang){
					if(!empty($f_lang)){
						$data[] = $f_lang."—".$personal_data['language_further_level'][$key];
					}

				}

			}

		}

		return $data;
	}

	private function _getNamesInstitutionsNoHTML($personal_data){
		$data =array();
		if($personal_data['names_institutions'][0]){

			foreach ($personal_data['names_institutions'] as $key => $name_institution) {
				$data[$key]['name_institution'] = $name_institution;
				$data[$key]['years_graduations'] = $personal_data['years_graduations'][$key];
				$data[$key]['faculties'] = $name_institution.$personal_data['faculties'][$key];
				$data[$key]['specialties_specialties']=$personal_data['specialties_specialties'][$key];
			}

		}
		return $data;
	}

	private function _getCallMeNoHTML($personal_data){
		$call_me = array();
		if($personal_data['mobile_phone']){
			if($personal_data['preferred_communication']==1){
				$call_me[] = "мобильный — {$personal_data['mobile_phone']} {$personal_data['comment_mobile_phone']}(желаемый способ связи)";
			}else{
				$call_me[] = "мобильный — {$personal_data['mobile_phone']} {$personal_data['comment_mobile_phone']}";
			}
		}
		if($personal_data['home_phone']){
			if($personal_data['preferred_communication']==2){
				$call_me[] = "домашний — {$personal_data['home_phone']}{$personal_data['comment_home_phone']}(желаемый способ связи)";
			}else{
				$call_me[] = "домашний — {$personal_data['home_phone']}{$personal_data['comment_home_phone']}";
			}

		}
		if($personal_data['work_phone']){
			if($personal_data['preferred_communication']==3) {
				$call_me[] = "рабочий — {$personal_data['work_phone']}{$personal_data['comment_work_phone']}(желаемый способ связи)";
			}else{
				$call_me[] = "рабочий — {$personal_data['work_phone']}{$personal_data['comment_work_phone']}";
			}
		}
		if($personal_data['email']){
			if($personal_data['preferred_communication']==4) {
				$call_me[] = "email — {$personal_data['email']} (желаемый способ связи)";
			}else{
				$call_me[] = "email — {$personal_data['email']}";
			}
		}
		if($personal_data['icq']){
			$call_me[] = "icq — {$personal_data['icq']}";
		}
		if($personal_data['skype']){
			$call_me[] = "Skype — {$personal_data['skype']}";
		}
		if($personal_data['free_lance']){
			$call_me[] = "Free-lance — {$personal_data['free_lance']}";
		}
		if($personal_data['my_circle']){
			$call_me[] = "Мой круг — {$personal_data['my_circle']}";
		}
		if($personal_data['linkedln']){
			$call_me[] = "Linkedln — {$personal_data['linkedln']}";
		}
		if($personal_data['facebook']){
			$call_me[] = "Facebook — {$personal_data['facebook']}";
		}
		if($personal_data['live_journal']){
			$call_me[] = "LiveJournal — {$personal_data['live_journal']}";
		}
		if($personal_data['other_site']){
			$call_me[] = "Другой сайт — {$personal_data['other_site']}";
		}

		return $call_me;
	}

	public function getExperienceCount($personal_data){
		$date = array();
		$year_sum = '';
		$month_sum = '';

		if(isset($personal_data['experience_getting_starteds'][0]) && $personal_data['experience_getting_starteds'][0]) {
			foreach ($personal_data['experience_getting_starteds'] as $key => $data) {
				$str_date = '';
				$d1 = new DateTime($data . "-1");
				if ($personal_data['experience_at_the_moments'][$key] === 'false') {
					$d2 = new DateTime($personal_data['experience_closing_works'][$key] . '-1');
				} elseif ($personal_data['experience_at_the_moments'][$key] === 'true') {
					$d2 = new DateTime('NOW');
				}

				$year = (int)$d2->diff($d1)->format('%Y');

				if ($year) {
					$str_date .= $year . ' год(лет) ';
				}

				$month = (int)$d2->diff($d1)->format('%m');

				if ($month) {
					$str_date .= $month . ' месяц(ев)';
				} elseif (!$year && !$month) {
					$str_date .= 'около месяца';
				}

				$date[$key] = $str_date;
				$year_sum += $year;
				$month_sum += $month;
			}

			$year_sum += floor($month_sum / 12);
			$month_sum = $month_sum % 12;

			$str_year = (int)$year_sum ? $year_sum . " год(лет) " : '';
			$str_moth = (int)$month_sum ? $month_sum . " месяц(ев)" : '';

			$date['sum'] = $str_year . $str_moth;
		}
		return $date;
	}

	private function _getOld($personal_data){
		$birth='';
		if(trim($personal_data['birth'])!=='--'){
			$now = new DateTime(date("Y-m-d"));
			$births = new DateTime($personal_data['birth']);

			$interval = $now->diff($births);
			$birth = $interval->format('%y года(лет)');
		}

		return " ({$birth})";
	}

	private function _getBerBirthSexCityMoveTrip($personal_data){
		return sprintf(
			'<b>%s</b> пол &#183; <b>%s</b> &#183;  Переезд: <b>%s</b> &#183; Готовность командировкам: <b>%s</b>',
			$personal_data['sex'],
			$personal_data['city'],
			$personal_data['move'],
			$personal_data['trip']);
	}


}