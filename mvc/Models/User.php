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