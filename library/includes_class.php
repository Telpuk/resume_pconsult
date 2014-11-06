<?php set_include_path( implode(PATH_SEPARATOR, array(
	'mvc/Controllers/frontController',
	'mvc/Controllers/errorController',
	'mvc/Controllers/sessionController',
	'mvc/Controllers',
	'mvc/Models',
	'mvc/Views',
	'config/',
	'library/'
)));

require_once  realpath(__DIR__.'/vendor/word/PhpWord/Autoloader.php');
\PhpOffice\PhpWord\Autoloader::register();


spl_autoload_register(function ($class) {
	$file = $class . '.php';
	//		try{
	//			if(!@
	include_once "$file";
	//			)
	//				throw new Exception();
	//		}catch (Exception $e){
	//			header("Location: ".BASE_URL."/error");
	//			exit;
	//		}
});