<?php
	class User{
		private $_dbc;
		private $_id;

		function __construct(){
			$this->_dbc = Model::getInstance()->getDbh();
		}

		public function setIdUser(){
			try {
				$stmt = $this->_dbc->prepare ("INSERT INTO profile(registered_user) VALUES(:registered_user)");
				$stmt->execute (array(':registered_user'=>'no'));
				$id = $this->_dbc->lastInsertId ();
			}catch (PDOException $e){
				exit(print_r($e->errorInfo).$e->getFile());
			}
			$this->_id = $id;
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