<?php
	abstract class Config{
		protected  abstract  function getConfig();

		protected  function _setConfigBD($config_bd = array()){
			define('TYPE_BD', $config_bd['type_bd']);
			define('HOST_DB', $config_bd['host']);
			define('DB_NAME', $config_bd['db_name']);
			define('USER', $config_bd['user']);
			define('PASSWORD', $config_bd['password']);
		}

		protected function _setConfigJS($config_js=array()){
			define('JS', $config_js['javascript']);
		}

		protected function _setConfigCompressor( $config_compressor = array() )
		{
			define( 'COMPRESS_HTML', $config_compressor['html'] );
		}

		protected  function _setConfigDIR($config_dir){
			$project_folder = empty($config_dir['project_folder']) ? "":"/".$config_dir['project_folder'];
			define('HOST', $config_dir['host']);
			define('PROJECT_FOLDER', $project_folder);
			define('DIR_PROJECT', $config_dir['dir_project']);
			define('BASE_URL', "http://".$config_dir['host'].$project_folder);
		}
	}