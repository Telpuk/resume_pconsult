<?php
	class User{
		private $_dbc;

		function __construct(){
			$this->_dbc = Model::getInstance()->getDbh();
		}

		public function getIdUser(){
			$no = 'no';
			try {
				$stmt = $this->_dbc->prepare ("INSERT INTO profile(registered_user) VALUES(:registered_user)");
				$stmt->bindParam (':registered_user', $no);
				$stmt->execute ();
				$id = $this->_dbc->lastInsertId ();
			}catch (PDOException $e){
				exit(print_r($e->errorInfo).$e->getFile());
			}
			return $id;
		}
	}