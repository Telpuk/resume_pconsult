<?php
	set_include_path( implode(PATH_SEPARATOR, array(
		'mvc/Controllers/frontController',
		'mvc/Controllers/errorController',
		'mvc/Controllers/sessionController',
		'mvc/Controllers',
		'mvc/Models',
		'mvc/Views',
		'config/'
	)));

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

	$app = FrontController::getInstance();

	$app->run();
	echo $app->getBody();
