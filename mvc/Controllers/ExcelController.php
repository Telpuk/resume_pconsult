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

			//			$header = $section->addHeader();
			//			$header->addImage(BASE_URL."/public/img/logo.jpg",
			//				array(
			//					'positioning' => \PhpOffice\PhpWord\Style\Image::POSITION_ABSOLUTE,
			//					'posHorizontal' => \PhpOffice\PhpWord\Style\Image::POSITION_HORIZONTAL_RIGHT,
			//					'posHorizontalRel' => \PhpOffice\PhpWord\Style\Image::POSITION_RELATIVE_TO_MARGIN,
			//					'marginRight' =>2,
			//					'marginTop' => 1,
			//					'width' => 70,
			//					'height' => 70)
			//			);

			$section->addText('РЕЗЮМЕ', array('size'=>14),array('align'=>'center'));
			$section->addText($personal_data['name'].$personal_data['old'], array('bold'=>true,'size'=>14), array('align'=>'center'));

			foreach($personal_data['call_me'] as $key=>$value){
				$section->addText($value, array('align'=>'left'));
			}
			// ===================================опыт======================= //
			$section->addLine(array(
				'width' => \PhpOffice\PhpWord\Shared\Drawing::centimetersToPixels(10),
				'height' => \PhpOffice\PhpWord\Shared\Drawing::centimetersToPixels(0),
				'marginLeft' => \PhpOffice\PhpWord\Shared\Drawing::centimetersToPixels(10),
				'positioning' => 'absolute'
			));
			$section->addText('Опыт:', array('size'=>16,'italic'=>true, 'color'=>'blue'),array('align'=>'center'));
			$section->addLine(array(
				'width' => \PhpOffice\PhpWord\Shared\Drawing::centimetersToPixels(10),
				'height' => \PhpOffice\PhpWord\Shared\Drawing::centimetersToPixels(0),
				'marginLeft' => \PhpOffice\PhpWord\Shared\Drawing::centimetersToPixels(10),
				'positioning' => 'absolute'
			));

			foreach($personal_data['experience_organizations'] as $key=>$value){
				$section->addText($value['experience_organizations'].
					"({$value['experience_getting_starteds']}, опыт: {$value['experience_count']})", array('bold'=>true));
				$section->addText("Регион: {$value['experience_regions']}", array('bold'=>true));
				$section->addText("Сфера деятельносьти компании: {$value['experience_field_activities']}", array('bold'=>true));
				$section->addText("Сайт: {$value['experience_sites']}", array('bold'=>true));
				$section->addText("Должность: ".$value['experience_positions'], array('bold'=>true));
				$section->addText("Обязанности:");
				$section->addText($value['experience_functions']);
				$section->addTextBreak();
			}
			// ===================================end опыт======================= //


			// ===================================образование======================= //
			$section = $this->_word->addSection($sectionStyle);
			$section->addLine(array(
				'width' => \PhpOffice\PhpWord\Shared\Drawing::centimetersToPixels(10),
				'height' => \PhpOffice\PhpWord\Shared\Drawing::centimetersToPixels(0),
				'marginLeft' => \PhpOffice\PhpWord\Shared\Drawing::centimetersToPixels(10),
				'positioning' => 'absolute'
			));
			$section->addText('Образование:', array('size'=>16,'italic'=>true, 'color'=>'blue'), array('align'=>'center'));
			$section->addLine(array(
				'width' => \PhpOffice\PhpWord\Shared\Drawing::centimetersToPixels(10),
				'height' => \PhpOffice\PhpWord\Shared\Drawing::centimetersToPixels(0),
				'marginLeft' => \PhpOffice\PhpWord\Shared\Drawing::centimetersToPixels(10),
				'positioning' => 'absolute'
			));
			foreach($personal_data['institutions'] as $key=>$value){
				$section->addText($value['name_institution'], array('bold'=>true));
				$section->addText("Факультет: {$value['faculties']}");
				$section->addText("Специальность: {$value['specialties_specialties']}");
				$section->addText("Год окончания: ".$value['years_graduations']);
			}
			if($personal_data['courses_names'][0]) {
				$section->addText("Повышение квалификации, курсы: ", array('bold' => true));
				foreach ($personal_data['courses_names'] as $key => $value) {
					$section->addText("{$value['course_years_graduations']}-{$value['courses_names']}");
					$section->addText("{$value['follow_organizations']}, {$value['courses_specialties']}");
				}
			}
			if($personal_data['tests_exams_names'][0]) {
				$section->addText("Тесты, экзамены: ", array('bold' => true));
				foreach ($personal_data['tests_exams_names'] as $key => $value) {
					$section->addText($value['tests_exams_years_graduations']);
					$section->addText("{$value['tests_exams_follow_organizations']}, {$value['tests_exams_specialty']}");
				}
			}
			if($personal_data['electronic_certificates_names'][0]) {
				$section->addText("Электронные сертификаты: ", array('bold' => true));
				foreach ($personal_data['electronic_certificates_names'] as $key => $value) {
					$section->addText("{$value['electronic_certificates_years_graduations']}-{$value['electronic_certificates_names']}");
					$section->addLink("Ссылка: " . $value['electronic_certificates_links']);
				}
			}
			// ===================================end образование======================= //


			// ===================================навыки=============================== //
			$section = $this->_word->addSection($sectionStyle);
			$section->addLine(array(
				'width' => \PhpOffice\PhpWord\Shared\Drawing::centimetersToPixels(10),
				'height' => \PhpOffice\PhpWord\Shared\Drawing::centimetersToPixels(0),
				'marginLeft' => \PhpOffice\PhpWord\Shared\Drawing::centimetersToPixels(10),
				'positioning' => 'absolute'
			));
			$section->addText('Навыки:', array('size'=>16,'italic'=>true, 'color'=>'blue'), array('align'=>'center'));
			$section->addLine(array(
				'width' => \PhpOffice\PhpWord\Shared\Drawing::centimetersToPixels(10),
				'height' => \PhpOffice\PhpWord\Shared\Drawing::centimetersToPixels(0),
				'marginLeft' => \PhpOffice\PhpWord\Shared\Drawing::centimetersToPixels(10),
				'positioning' => 'absolute'
			));
			$section->addText($personal_data['experience_key_skills']);
			$section->addTextBreak();

			// ===================================end навыки======================== //


			// ===================================Личностные качества============== //
			$section->addLine(array(
				'width' => \PhpOffice\PhpWord\Shared\Drawing::centimetersToPixels(10),
				'height' => \PhpOffice\PhpWord\Shared\Drawing::centimetersToPixels(0),
				'marginLeft' => \PhpOffice\PhpWord\Shared\Drawing::centimetersToPixels(10),
				'positioning' => 'absolute'
			));
			$section->addText('Личностные качества:', array('size'=>16,'italic'=>true, 'color'=>'blue'), array('align'=>'center'));
			$section->addLine(array(
				'width' => \PhpOffice\PhpWord\Shared\Drawing::centimetersToPixels(10),
				'height' => \PhpOffice\PhpWord\Shared\Drawing::centimetersToPixels(0),
				'marginLeft' => \PhpOffice\PhpWord\Shared\Drawing::centimetersToPixels(10),
				'positioning' => 'absolute'
			));
			$section->addText($personal_data['experience_about_self']);
			$section->addTextBreak();
			// ===================================end Личностные качества======================= //


			// ===================================Рекомендации============== //
			$section->addLine(array(
				'width' => \PhpOffice\PhpWord\Shared\Drawing::centimetersToPixels(10),
				'height' => \PhpOffice\PhpWord\Shared\Drawing::centimetersToPixels(0),
				'marginLeft' => \PhpOffice\PhpWord\Shared\Drawing::centimetersToPixels(10),
				'positioning' => 'absolute'
			));
			$section->addText('Рекомендации:', array('size'=>16,'italic'=>true, 'color'=>'blue'), array('align'=>'center'));
			$section->addLine(array(
				'width' => \PhpOffice\PhpWord\Shared\Drawing::centimetersToPixels(10),
				'height' => \PhpOffice\PhpWord\Shared\Drawing::centimetersToPixels(0),
				'marginLeft' => \PhpOffice\PhpWord\Shared\Drawing::centimetersToPixels(10),
				'positioning' => 'absolute'
			));
			if($personal_data['experience_recommend']){
				foreach($personal_data['experience_recommend']['experience_recommend_names'] as $key=>$value){
					$section->addText($personal_data['experience_recommend']['experience_recommend_organization'][$key],array('bold'=>true, 'size'=>16));
					$section->addText("{$personal_data['experience_recommend']['experience_recommend_position'][$key]} – {$value} ({$personal_data['experience_recommend']['experience_recommend_phone'][$key]})");
					$section->addTextBreak();
				}
			}
			// ===================================end Рекомендации======================= //


			// ===================================Заключение============== //
			if($personal_data['conclusion']){
				$section->addLine(array(
					'width' => \PhpOffice\PhpWord\Shared\Drawing::centimetersToPixels(10),
					'height' => \PhpOffice\PhpWord\Shared\Drawing::centimetersToPixels(0),
					'marginLeft' => \PhpOffice\PhpWord\Shared\Drawing::centimetersToPixels(10),
					'positioning' => 'absolute'
				));
				$section->addText('Заключение:', array('size'=>16,'italic'=>true, 'color'=>'blue'), array('align'=>'center'));
				$section->addLine(array(
					'width' => \PhpOffice\PhpWord\Shared\Drawing::centimetersToPixels(10),
					'height' => \PhpOffice\PhpWord\Shared\Drawing::centimetersToPixels(0),
					'marginLeft' => \PhpOffice\PhpWord\Shared\Drawing::centimetersToPixels(10),
					'positioning' => 'absolute'
				));
				$section->addText($personal_data['conclusion']);
			}
			// ===================================end Заключение======================= //


			header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
			header('Content-Disposition: attachment;filename="document.docx"');
			header('Cache-Control: max-age=0');
			$writer = \PhpOffice\PhpWord\IOFactory::createWriter($this->_word, 'Word2007');
			$writer->save('php://output');



		}





	}
}
