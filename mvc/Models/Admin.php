<?php
class Admin{
	private
		$_dbc;

	function __construct(){
		$this->_dbc = Model::getInstance()->getDbh();
	}

	public  function checkLoginAndPassword($data){
		try {
			$stmt = $this->_dbc->prepare ("
												SELECT
													login
												FROM
													users_access
												WHERE
													login = :login
												AND
													password = :password"
			);
			$stmt->execute(array(
				':login'=>$data['login'],
				':password'=>$data['password']
			));
			$user_access_data = $stmt->fetch(PDO::FETCH_ASSOC);
		}catch (PDOException $e){
			exit(print_r($e->errorInfo).$e->getFile());
		}

		if($user_access_data['login']){
			return $user_access_data['login'];
		}

		return false;
	}
}