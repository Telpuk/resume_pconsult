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
		if(is_numeric($this->getParams('id'))) {

			$personal_data = $this->_db_excel->excelExport($this->_id_user);

			//			if($this->getParams('comments') !== 'false'){
			//				$comments = $this->_db_excel->selectCommetsExport($this->_id_user);
			//			}

			$this->_word->setDefaultFontName('Times New Roman');
			$this->_word->setDefaultFontSize(12);


			$sectionStyle = array('marginLeft' => $this->_m2t(15), //Левое поле равно 15 мм
				'marginRight' => $this->_m2t(15), 'marginTop' => $this->_m2t(15), 'borderTopColor' => 'C0C0C0');

			$section = $this->_word->createSection($sectionStyle);

			//			$header = $section->addHeader();
			//			$table = $header->addTable();
			//			$table->addRow();
			//			$cell = $table->addCell(4500);
			//			$textrun = $cell->addTextRun();
			//			$textrun->addText("Консалтинговый центр «Pro-consult»",array('align'=>'center','size'=>8));
			//			$textrun->addTextBreak();
			//			$textrun->addLink("www.proconsult.by");
			//			$textrun->addTextBreak();
			//			$textrun->addText("+375 44 779 03 94",array('align'=>'center', 'size'=>8));
			//			$table->addCell(4000)->addImage(
			//				BASE_URL."/public/img/logo.jpg",
			//				array('width' => 50, 'height' => 50, 'align' => 'right')
			//			);

			//			print_r($personal_data);
			$styleTable = array('borderSize' => 6, 'borderColor' => '999999');

			$this->_word->addTableStyle('Colspan Rowspan1', $styleTable);
			$this->_word->addTableStyle('Colspan Rowspan2', $styleTable);
			$this->_word->addTableStyle('Colspan Rowspan3', $styleTable);

			$table = $section->addTable('Colspan Rowspan1');

			$table->addRow();

			if ($personal_data['photo'] !== 'no-photo.png') {
				$table->addCell(3000)->addImage(BASE_URL . "/files/photo/" . $personal_data['photo'], array('width' => 150, 'height' => 150, 'align' => 'center'));
			}

			$cell = $table->addCell(10000);
			$textrun = $cell->addTextRun();
			$textrun->addText($personal_data['name'], array('bold' => true, 'size' => 18), array('align' => 'left'));
			$textrun->addTextBreak();
			$textrun->addText("Пол: {$personal_data['sex']}" . ($personal_data['old'] ? ', ' . $personal_data['old'] : ''));
			$textrun->addTextBreak();

			foreach ($personal_data['call_me'] as $key => $value) {
				$textrun->addTextBreak();
				$textrun->addText($value);

			}

			$textrun->addTextBreak();

			foreach ($personal_data['city_move_trip'] as $key => $value) {
				$textrun->addTextBreak();
				$textrun->addText($value);

			}
			$section->addTextBreak();

			/*Желаемая должность и зарплата*/
			$table = $section->addTable('Colspan Rowspan2');

			$table->addRow();
			$cell = $table->addCell(4000, array('gridSpan' => 2, 'vMerge' => 'restart', 'valign' => 'center'));
			$textrun = $cell->addTextRun(array('align' => 'left'));
			$textrun->addText('Желаемая должность и зарплата', array('size' => 14));

			$table->addRow();
			$cell = $table->addCell(20000);
			$textrun = $cell->addTextRun(array('align' => 'left'));
			$textrun->addText($personal_data['desired_position'], array('size' => 16, 'bold' => true));
			$textrun->addTextBreak();
			$textrun->addText($personal_data['professional_area']);
			$textrun->addTextBreak(2);
			$textrun->addText("Занятость: " . mb_strtolower($personal_data['employment'], 'UTF-8'));
			$textrun->addTextBreak();
			$textrun->addText("График работы: " . mb_strtolower($personal_data['schedule'], 'UTF-8'));
			$textrun->addTextBreak(2);
			$textrun->addText("Желательное время в пути до работы: " . mb_strtolower($personal_data['travel_time_work'], 'UTF-8'));
			$cell = $table->addCell(10000);
			$textrun = $cell->addTextRun(array('align' => 'right'));
			if ($personal_data['salary']) {
				$textrun->addText($personal_data['salary'], array('size' => 16, 'bold' => true));
				$textrun->addText(' ' . $personal_data['currency']);
			}

			$section->addTextBreak();

			/*Опыт работы*/
			if ( $personal_data['sum_experience'] !== 'без опыта работы') {

				$table = $section->addTable('Colspan Rowspan3');
				$table->addRow();
				$cell = $table->addCell(4000, array('gridSpan' => 2, 'vMerge' => 'restart', 'valign' => 'center'));
				$textrun = $cell->addTextRun(array('align' => 'left'));
				$textrun->addText('Опыт работы - ' . $personal_data['sum_experience'], array('size' => 14));

				foreach($personal_data['experience_organizations'] as $key=>$value){
					$table->addRow();

					$cell = $table->addCell(20000);
					$textrun = $cell->addTextRun(array('align' => 'left'));
					$textrun->addText($personal_data['experience_getting_starteds']);
					$textrun->addTextBreak();
					$textrun->addText($personal_data['experience_count']);

					$cell = $table->addCell(20000);
					$textrun = $cell->addTextRun(array('align' => 'left'));
					$textrun->addText($personal_data['experience_organizations']);
					$textrun->addTextBreak();
					$textrun->addText($personal_data['experience_count']);

//					$section->addText($value['experience_organizations'].
//						"({$value['experience_getting_starteds']}, опыт: {$value['experience_count']})", array('bold'=>true));
//					$section->addText("Регион: {$value['experience_regions']}", array('bold'=>true));
//					$section->addText("Сфера деятельносьти компании: {$value['experience_field_activities']}", array('bold'=>true));
//					$section->addText("Сайт: {$value['experience_sites']}", array('bold'=>true));
//					$section->addText("Должность: ".$value['experience_positions'], array('bold'=>true));
//					$section->addText("Обязанности:");
//					$section->addText($value['experience_functions']);
//					$section->addTextBreak();
				}
			}









			$section->addText('РЕЗЮМЕ', array('size'=>14),array('align'=>'center'));
			$section->addText($personal_data['name'].$personal_data['old'], array('bold'=>true,'size'=>14), array('align'=>'center'));






			// ===================================опыт======================= //
			$section->addText('Опыт:',
				array('size'=>18,'italic'=>true, 'color'=>'blue'),
				array('align'=>'center')
			);

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
			$section->addText('Образование:', array('size'=>16,'italic'=>true, 'color'=>'blue'), array('align'=>'center'));
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
			$section->addText('Навыки:', array('size'=>16,'italic'=>true, 'color'=>'blue'), array('align'=>'center'));
			$section->addText($personal_data['experience_key_skills']);
			$section->addTextBreak();

			// ===================================end навыки======================== //


			// ===================================Личностные качества============== //
			$section->addText('Личностные качества:', array('size'=>16,'italic'=>true, 'color'=>'blue'), array('align'=>'center'));
			$section->addText($personal_data['experience_about_self']);
			$section->addTextBreak();
			// ===================================end Личностные качества======================= //


			// ===================================Рекомендации============== //
			$section->addText('Рекомендации:', array('size'=>16,'italic'=>true, 'color'=>'blue'), array('align'=>'center'));
			if($personal_data['experience_recommend']){
				foreach($personal_data['experience_recommend']['experience_recommend_names'] as $key=>$value){
					$section->addText($personal_data['experience_recommend']['experience_recommend_organization'][$key],array('bold'=>true, 'size'=>16));
					$section->addText("{$personal_data['experience_recommend']['experience_recommend_position'][$key]} – {$value} ({$personal_data['experience_recommend']['experience_recommend_phone'][$key]})");
					$section->addTextBreak();
				}
			}
			// ===================================end Рекомендации======================= //


			// ===================================Заключение============== //
			if($personal_data['conclusion'] && $this->getParams('conclusion') !== 'false'){
				$section->addText('Заключение:', array('size'=>16,'italic'=>true, 'color'=>'blue'), array('align'=>'center'));
				$section->addText($personal_data['conclusion']);
			}
			// ===================================end Заключение======================= //

			// ===================================Комментарии============== //
			if(count($comments) && $this->getParams('comments') !== 'false'){
				$section->addText('Комментарии:', array('size'=>16,'italic'=>true, 'color'=>'blue'),
					array('align'=>'center'));
				foreach($comments as $value) {
					$section->addText($value['name'] . "({$value['date']})", array('bold' => true));
					$section->addText($value['comment'],array('tabs' => true));
				}
			}
			// ===================================end Комментарии======================= //


			header("Content-Description: File Transfer");
			header("Content-Disposition: attachment; filename={$personal_data['name']}.docx");
			header("Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document");
			header("Content-Transfer-Encoding: binary");
			header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			header("Expires: 0");
			$writer = \PhpOffice\PhpWord\IOFactory::createWriter($this->_word, 'Word2007');
			$writer->save('php://output');
			exit;
		}
		$this->headerLocation('admincontrol');
	}
}
