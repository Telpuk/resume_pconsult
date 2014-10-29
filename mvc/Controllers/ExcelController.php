<?php
\PhpOffice\PhpWord\Autoloader::register();
class ExcelController extends IController{
	private
		$_view,
		$_db_excel,
		$_word,
		$_id_user;

	public function  __construct(){
		parent::__construct();
		$this->_view = new View();
		$this->_db_excel = new Excel();
		$this->_word = new \PhpOffice\PhpWord\PhpWord();
	}

	private function _m2t($millimeters){
		return floor($millimeters*56.7);
	}

	public function indexAction(){
		$this->_id_user = $this->getParams('id');
		if($this->getParams('id')){

			$personal_data = $this->_db_excel->excelExport($this->_id_user);

			$this->_word->setDefaultFontName('Times New Roman');
			$this->_word->setDefaultFontSize(14);

			$sectionStyle = array('orientation' => 'landscape',
				'marginLeft' => $this->_m2t(15), //Левое поле равно 15 мм
				'marginRight' => $this->_m2t(15),
				'marginTop' => $this->_m2t(15),
				'borderTopColor' => 'C0C0C0'
			);

			$section = $this->_word->createSection($sectionStyle);

			$header = $section->addHeader();
			$header->addImage(BASE_URL."/public/img/logo.jpg",
				array(
					'positioning' => \PhpOffice\PhpWord\Style\Image::POSITION_ABSOLUTE,
					'posHorizontal' => \PhpOffice\PhpWord\Style\Image::POSITION_HORIZONTAL_RIGHT,
					'posHorizontalRel' => \PhpOffice\PhpWord\Style\Image::POSITION_RELATIVE_TO_MARGIN,
					'marginRight' =>2,
					'marginTop' => 1,
					'width' => 70,
					'height' => 70)
			);


			$section->addImage(
				BASE_URL."/files/photo/".$personal_data['photo'],
				array('positioning' => 'relative', 'marginTop' => 50,'marginLeft' => -300, 'align' => 'right',
					'width' => 200, 'height' => 200, 'wrappingStyle' => 'square'

				));
			$section->addText($personal_data['name'], array('bold'=>true));

			$section->addText(str_replace('&#183;',' — ',strip_tags($personal_data['birth_sex_city_move_trip'])),
				array('width'=>200));

			$section->addText('Способ связи:',  array('bold'=>true));
			foreach($personal_data['call_me'] as $key=>$value){
				$section->addText($value);
			}
			$section->addText("Желаемая должность и зарплата", array(
				'bold'=>true, 'size'=>20,'underline'=>'solid', 'color'=> 'bababa' ));

			$section->addText($personal_data['desired_position']);
			$personal_data['salary'] = $personal_data['salary']?$personal_data['salary']." " .$personal_data['currency']:'';
			$section->addText($personal_data['salary']);
			$section->addText($personal_data['professional_area']);

			$section->addText("Занятость: ".$personal_data['schedule']);
			$section->addText("График работы: ".$personal_data['employment']);

			$section->addText("Образование", array('bold'=>true, 'size'=>20,'underline'=>'solid',
				'color'=> 'bababa' ));

			$table = $section->addTable();
			foreach($personal_data['institutions'] as $key=>$value){
				$table->addRow();
				$table->addCell(1750)->addText($value['years_graduations']);
				$table->addCell(12000)->addText($value['name_institution'], array('bold'=>true));
				$table->addRow();
				$table->addCell(1750)->addText('');
				$table->addCell(12000)->addText("{$value['faculties']}, {$value['specialties_specialties']}",
					array('color'=>'bababa'));
			}
			$section->addTextBreak(1);
			$section->addText("Зание языков",array('bold'=>true,'size'=>18));
			foreach($personal_data['languages'] as $key=>$value){
				$section->addText($value);
			}
			$section->addText("Повышение квалификации, курсы",array('bold'=>true,'size'=>18));
			$table = $section->addTable();
			foreach($personal_data['courses_names'] as $key=>$value){
				$table->addRow();
				$table->addCell(1750)->addText($value['course_years_graduations']);
				$table->addCell(12000)->addText($value['courses_names'], array('bold'=>true));
				$table->addRow();
				$table->addCell(1750)->addText('');
				$table->addCell(12000)->addText("{$value['follow_organizations']}, {$value['courses_specialties']}",
					array('color'=>'bababa'));
			}

			$section->addText("Тесты, экзамены ",array('bold'=>true,'size'=>18));
			$table = $section->addTable();
			foreach($personal_data['tests_exams_names'] as $key=>$value){
				$table->addRow();
				$table->addCell(1750)->addText($value['tests_exams_years_graduations']);
				$table->addCell(12000)->addText($value['tests_exams_names'], array('bold'=>true));
				$table->addRow();
				$table->addCell(1750)->addText('');
				$table->addCell(12000)->addText("{$value['tests_exams_follow_organizations']}, {$value['tests_exams_specialty']}",
					array('color'=>'bababa'));
			}

			$section->addText("Электронные сертификаты ",array('bold'=>true,'size'=>18));
			$table = $section->addTable();
			foreach($personal_data['electronic_certificates_names'] as $key=>$value){
				$table->addRow();
				$table->addCell(1750)->addText($value['electronic_certificates_years_graduations']);
				$table->addCell(12000)->addText($value['electronic_certificates_names'], array('bold'=>true));
				$table->addRow();
				$table->addCell(1750)->addText('');
				$table->addCell(12000)->addLink($value['electronic_certificates_links']);
			}

			$section->addText("Опыт работы {$personal_data['sum_experience']}", array('bold'=>true, 'size'=>20,
				'underline'=>'solid',
				'color'=> 'bababa' ));
			$table = $section->addTable();
			foreach($personal_data['experience_organizations'] as $key=>$value){
				$table->addRow();
				$table->addCell(5000)->addText($value['experience_getting_starteds']);
				$table->addCell(5000)->addText($value['experience_organizations'],array('bold'=>true));
				$table->addRow();
				$table->addCell(5000)->addText($value['experience_count'], array('color'=> 'bababa'));
				$table->addCell(5000)->addText($value['experience_regions'].", ".$value['experience_sites']);
				$table->addRow();
				$table->addCell(5000)->addText('');
				$table->addCell(5000)->addText($value['experience_field_activities']);
				$table->addRow();
				$table->addCell(5000)->addText('');
				$table->addCell(5000)->addText($value['experience_positions'], array('bold'=>true));
				$table->addRow();
				$table->addCell(5000)->addText('');
				$table->addCell(5000)->addText($value['experience_functions']);
			}

			$section->addText("Ключевые навыки ",array('bold'=>true,'size'=>18));
			$section->addText($personal_data['experience_key_skills'],array('color'=>'bababa'));
			$section->addText("Обо мне ",array('bold'=>true,'size'=>18));
			$section->addText($personal_data['experience_about_self'],array('color'=>'bababa'));

			$section->addText("Рекомендации ",array('bold'=>true,'size'=>18));
			if($personal_data['experience_recommend']){
				foreach($personal_data['experience_recommend']['experience_recommend_names'] as $key=>$value){
					$section->addText($personal_data['experience_recommend']['experience_recommend_organization'][$key],array('bold'=>true, 'size'=>16));
					$section->addText("{$value} ({$personal_data['experience_recommend']['experience_recommend_position'][$key]})");
					$section->addText($personal_data['experience_recommend']['experience_recommend_phone'][$key]);
					$section->addTextBreak();
				}
			}

			$section->addText("Гражданство, время в пути до работы", array(
				'bold'=>true, 'size'=>20,
				'underline'=>'solid',
				'color'=> 'bababa' ));
			$section->addText("Гражданство: {$personal_data['nationality']}");
			$section->addText("Разрешение на работу: {$personal_data['work_permit']}");
			$section->addText("Желательное время в пути до работы: {$personal_data['travel_time_work']}");


			header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
			header('Content-Disposition: attachment;filename="document.docx"');
			header('Cache-Control: max-age=0');
			$writer = \PhpOffice\PhpWord\IOFactory::createWriter($this->_word, 'Word2007');
			$writer->save('php://output');



		}





	}
}
