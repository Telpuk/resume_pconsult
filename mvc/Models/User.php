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
					'value'=>explode('%',$position_data['employment'])
				),
				'schedule'=>array(
					'val'=>true,
					'value'=>explode('%',$position_data['schedule'])
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