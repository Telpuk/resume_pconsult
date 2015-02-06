<?php

class Model
{
	static private $_instance = null;
	static private $_dbh = null;

	private function __construct()
	{
		try {
			self::$_dbh = new PDO( TYPE_BD . ":host=" . HOST_DB . ";dbname=" . DB_NAME, USER, PASSWORD,
				array( PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8",
					PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION ) );
		} catch ( PDOException $e ) {
			print "Error!: " . $e->getMessage() . "<br/>";
			die();
		}
	}

	private function __clone() { }

	public function getDbh()
	{
		return self::$_dbh;
	}


	static public function getInstance()
	{
		if ( self::$_instance instanceof self ) {
			return self::$_instance;
		}
		self::$_instance = new Model();
		return self::$_instance;
	}
}