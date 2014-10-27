<?php
class ExcelController extends IController{
	private
		$_view,
		$_db_user,
		$_word,
		$_id_user;

	public function  __construct(){
		parent::__construct();
		$this->_view = new View();
		$this->_db_user = new User();
		$this->_word = new PHPWord();
	}

	private function _m2t($millimeters){
		return floor($millimeters*56.7); //1 твип равен 1/567 сантиметра
	}//m2t


	public function indexAction(){
		$this->_id_user = $this->getParams('id');
		if($this->getParams('id')){

			$personal_data = $this->_db_user->selectPersonalData($this->_id_user);

			$word = new PHPWord();
			$word->setDefaultFontName('Times New Roman');
			$word->setDefaultFontSize(14);

			$sectionStyle = array('orientation' => 'landscape',
				'marginLeft' => $this->_m2t(15), //Левое поле равно 15 мм
				'marginRight' => $this->_m2t(15),
				'marginTop' => $this->_m2t(15),
				'borderTopColor' => 'C0C0C0'
			);

			$section = $word->createSection($sectionStyle);

			$fontStyle = array('color'=>'black', 'size'=>18, 'bold'=>true);
			$section->addText($personal_data['name'], $fontStyle);
			$section->addTextBreak(1);
			$section->addText($personal_data['birth_sex_city_move_trip'], $fontStyle);


			header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
			header('Content-Disposition: attachment;filename="document.docx"');
			header('Cache-Control: max-age=0');
			$writer = PHPWord_IOFactory::createWriter($word, 'Word2007');
			$writer->save('php://output');



		}





	}
}
