<?php
class Folder{
	private
		$_db_admin;

	function __construct(){
		$this->_db_admin =  new Admin();
	}

	public function insertFolder($folder){
		return $this->_db_admin->insertFolder($folder);
	}

	public function updateFoldersUsers($folders,$id_user){
		$this->_db_admin->updateFoldersUsers($folders,$id_user);
	}
	public function selectFolders(){
		return $this->_db_admin->selectFolders();
	}
	public function selectFoldersUser($id_user){
		return $this->_db_admin->selectFoldersUser($id_user);
	}
}