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

			if($this->getParams('comments') !== 'false'){
				$comments = $this->_db_excel->selectCommetsExport($this->_id_user);
			}

			$this->_word->setDefaultFontName('Times New Roman');
			$this->_word->setDefaultFontSize(12);


			$sectionStyle = array('marginLeft' => $this->_m2t(10), //Левое поле равно 15 мм
				'marginRight' => $this->_m2t(10), 'marginTop' => $this->_m2t(10));

			$section = $this->_word->createSection($sectionStyle);

			$header = $section->addHeader(\PhpOffice\PhpWord\Element\Header::FIRST);
			$table = $header->addTable();
			$table->addRow(array('height'=>2));
			$cell = $table->addCell(4500);
			$textrun = $cell->addTextRun();
			$textrun->addText("Консалтинговый центр «Pro-consult»",array('align'=>'center','size'=>8));
			$textrun->addTextBreak();
			$textrun->addText("www.proconsult.by",array('align'=>'center','size'=>8));
			$textrun->addTextBreak();
			$textrun->addText("+375 44 779 03 94",array('align'=>'center', 'size'=>8));
			$table->addCell(8000)->addImage(
				BASE_URL."/public/img/logo.jpg",
				array('width' => 40, 'height' => 40, 'align' => 'right')
			);
			$header->addTextBreak();
			$styleTable = array('borderSize' => 6, 'borderColor' => '999999');

//			$this->_word->addTableStyle('Colspan Rowspan1', $styleTable);
//			$this->_word->addTableStyle('Colspan Rowspan2', $styleTable);
//			$this->_word->addTableStyle('Colspan Rowspan3', $styleTable);
//			$this->_word->addTableStyle('Colspan Rowspan4', $styleTable);
//			$this->_word->addTableStyle('Colspan Rowspan5', $styleTable);
//			$this->_word->addTableStyle('Colspan Rowspan6', $styleTable);

			$table = $section->addTable('Colspan Rowspan1');

			$row = $table->addRow();

			if ($personal_data['photo'] !== 'no-photo.png') {
				$table->addCell(3000)->addImage(BASE_URL . "/files/photo/" . $personal_data['photo'], array('width' => 150, 'height' => 150, 'align' => 'center'));
			}

			$cell = $row->addCell(10000);
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

			$row = $table->addRow();
			$cell = $row->addCell(4000, array('gridSpan' => 2, 'vMerge' => 'restart', 'valign' => 'center'));
			$textrun = $cell->addTextRun(array('align' => 'left'));
			$textrun->addText('Желаемая должность и зарплата', array('size' => 14));

			$row = $table->addRow();
			$cell = $row->addCell(20000);
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
				$row = $table->addRow();
				$cell = $row->addCell(4000, array('gridSpan' => 2, 'vMerge' => 'restart', 'valign' => 'center'));
				$textrun = $cell->addTextRun(array('align' => 'left'));
				$textrun->addText('Опыт работы - ' . $personal_data['sum_experience'], array('size' => 14));

				foreach($personal_data['experience_organizations'] as $key=>$value){
					$row = $table->addRow();

					$cell = $row->addCell(4000);
					$textrun = $cell->addTextRun(array('align' => 'left'));
					$textrun->addText($value['experience_getting_starteds'],array('size'=>9));
					$textrun->addTextBreak();
					$textrun->addText($value['experience_count'],array('size'=>9));

					$cell = $row->addCell(9000);
					$textrun = $cell->addTextRun(array('align' => 'left'));
					$textrun->addText($value['experience_organizations'],array('size'=>14,'bold'=>true));
					$textrun->addTextBreak();
					$value['experience_field_activities']?$textrun->addText($value['experience_field_activities'].' '):null;
					$textrun->addTextBreak();
					$textrun->addText($value['experience_regions'].' ');
					$value['experience_sites']?$textrun->addText($value['experience_sites'].' '):null;
					$textrun->addTextBreak(2);
					$textrun->addText($value['experience_positions'], array('size'=>14,'bold'=>true));
					$textrun->addTextBreak();
					$textrun->addText($value['experience_functions']);
				}
			}

			$section->addTextBreak();

			/*Образование */
			$table = $section->addTable('Colspan Rowspan4');
			$row = $table->addRow();
			$cell = $row->addCell(4000, array('gridSpan' => 2, 'vMerge' => 'restart', 'valign' => 'center'));
			$textrun = $cell->addTextRun(array('align' => 'left'));
			$textrun->addText('Образование', array('size' => 14));

			foreach($personal_data['institutions'] as $key=>$value){
				$row = $table->addRow();
				$cell = $row->addCell(4000);
				$textrun = $cell->addTextRun(array('align' => 'left'));
				$textrun->addText($value['years_graduations']);
				$cell = $row->addCell(9000);
				$textrun = $cell->addTextRun(array('align' => 'left'));
				$textrun->addText($value['name_institution'], array('bold'=>true));
				$textrun->addTextBreak();
				$textrun->addText($value['faculties'].', '.$value['specialties_specialties']);
			}
			if($personal_data['courses_names'][0]) {
				foreach ($personal_data['courses_names'] as $key => $value) {
					$row = $table->addRow();
					$cell = $row->addCell(4000);
					$textrun = $cell->addTextRun(array('align' => 'left'));
					$textrun->addText($value['course_years_graduations']);
					$cell = $row->addCell(9000);
					$textrun = $cell->addTextRun(array('align' => 'left'));
					$textrun->addText($value['courses_names'], array('bold' => true));
					$textrun->addTextBreak();
					$textrun->addText($value['courses_specialties']);
				}
			}
			if($personal_data['tests_exams_names'][0]) {
				foreach ($personal_data['tests_exams_names'] as $key => $value) {
					$row = $table->addRow();
					$cell = $row->addCell(4000);
					$textrun = $cell->addTextRun(array('align' => 'left'));
					$textrun->addText($value['tests_exams_years_graduations']);
					$cell = $row->addCell(9000);
					$textrun = $cell->addTextRun(array('align' => 'left'));
					$textrun->addText($value['tests_exams_names'], array('bold' => true));
					$textrun->addTextBreak();
					$textrun->addText($value['tests_exams_follow_organizations'].', '.$value['tests_exams_specialty']);
				}
			}

			if($personal_data['electronic_certificates_names'][0]) {
				foreach ($personal_data['electronic_certificates_names'] as $key => $value) {
					$row = $table->addRow();
					$cell = $row->addCell(4000);
					$textrun = $cell->addTextRun(array('align' => 'left'));
					$textrun->addText($value['electronic_certificates_years_graduations']);
					$cell = $row->addCell(9000);
					$textrun = $cell->addTextRun(array('align' => 'left'));
					$textrun->addText($value['electronic_certificates_names'], array('bold' => true));
					$textrun->addTextBreak();
					$textrun->addLink($value['electronic_certificates_links']);
				}
			}
			$section->addTextBreak();
			/*Ключевые навыки*/
			$table = $section->addTable('Colspan Rowspan5');
			$row = $table->addRow();
			$cell = $row->addCell(4000, array('gridSpan' => 2, 'vMerge' => 'restart', 'valign' => 'center'));
			$textrun = $cell->addTextRun(array('align' => 'left'));
			$textrun->addText('Ключевые навыки', array('size' => 14));


			$row = $table->addRow();
			$cell = $row->addCell(4000);
			$textrun = $cell->addTextRun(array('align' => 'left'));
			$textrun->addText('Знание языков');
			$cell = $row->addCell(9000);
			$textrun = $cell->addTextRun(array('align' => 'left'));
			foreach($personal_data['languages'] as $key=>$value){
				$textrun->addText($value);
				$textrun->addTextBreak();
			}

			$row = $table->addRow();
			$cell = $row->addCell(4000);
			$textrun = $cell->addTextRun(array('align' => 'left'));
			$textrun->addText('Навыки');
			$cell = $row->addCell(9000);
			$textrun = $cell->addTextRun(array('align' => 'left'));
			$textrun->addText($personal_data['experience_key_skills']);

			$section->addTextBreak();

			/*Дополнительная информация*/
			if($personal_data['experience_recommend'] || $personal_data['experience_about_self']) {

				$table = $section->addTable('Colspan Rowspan6');
				$row = $table->addRow();
				$cell = $row->addCell(4000, array('gridSpan' => 2, 'vMerge' => 'restart', 'valign' => 'center'));
				$textrun = $cell->addTextRun(array('align' => 'left'));
				$textrun->addText('Дополнительная информация', array('size' => 14));


				if ($personal_data['experience_recommend']) {
					$row = $table->addRow();
					$cell = $row->addCell(4000);
					$textrun = $cell->addTextRun(array('align' => 'left'));

					$textrun->addText('Рекомендации');
					$cell = $row->addCell(9000);
					$textrun = $cell->addTextRun(array('align' => 'left'));

					foreach ($personal_data['experience_recommend']['experience_recommend_names'] as $key => $value) {
						$textrun->addText($personal_data['experience_recommend']['experience_recommend_organization'][$key], array('bold' => true, 'size' => 16));
						$textrun->addText("$value({$personal_data['experience_recommend']['experience_recommend_position'][$key]}) {$personal_data['experience_recommend']['experience_recommend_phone'][$key]})");
						$textrun->addTextBreak();
					}
				}

				if ($personal_data['experience_about_self']) {
					$row = $table->addRow();
					$cell = $row->addCell(4000);
					$textrun = $cell->addTextRun(array('align' => 'left'));
					$textrun->addText('Обо мне');
					$cell = $row->addCell(9000);
					$textrun = $cell->addTextRun(array('align' => 'left'));
					$textrun->addText($personal_data['experience_about_self']);
				}
			}

			$section->addTextBreak();

			// ===================================Заключение============== //
			if($personal_data['conclusion'] && $this->getParams('conclusion') !== 'false'){
				$section->addText('Заключение:', array('size'=>16,'bold'=>true), array('align'=>'left'));
				$section->addText($personal_data['conclusion']);
			}
			// ===================================end Заключение======================= //

			// ===================================Комментарии============== //
			if(count($comments) && $this->getParams('comments') !== 'false'){
				$section->addText('Комментарии:', array('size'=>16,'bold'=>true), array('align'=>'left'));
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
