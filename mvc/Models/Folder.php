<?php
class Folder{
	private $_dbc;

	function __construct(){
		$this->_dbc = Model::getInstance()->getDbh();
	}

	public function insertFolder($folder){
		try {
			$stmt = $this->_dbc->prepare ("INSERT INTO folders(name) VALUES(:name)");
			$stmt->execute(array(
				':name'=>$folder
			));
			$id = $this->_dbc->lastInsertId();
		}catch (PDOException $e){
			exit(print_r($e->errorInfo).$e->getFile());
		}
		return $id;
	}

	public function updateFolders($folders,$id_user){
		try {
			$stmt = $this->_dbc->prepare ("UPDATE
													profile
												SET
													folders = :folders
												WHERE
													id = :id_user");
			$stmt->execute(array(':folders'=>$folders,':id_user'=>$id_user));
		}catch (PDOException $e){
			exit(print_r($e->errorInfo).$e->getFile());
		}
	}
	public function selectFolders(){
		try {
			$stmt = $this->_dbc->query("SELECT
												id,
												name
											FROM
												folders
											ORDER BY name "
			);
			$folders = $stmt->fetchAll(PDO::FETCH_ASSOC);
		}catch (PDOException $e){
			exit(print_r($e->errorInfo).$e->getFile());
		}

		return $folders;
	}
}