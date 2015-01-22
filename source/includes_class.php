<?php set_include_path( implode(PATH_SEPARATOR, array(
	__DIR__ . '/Engine/FrontController/',
	__DIR__ . '/Engine/SessionController/',
	__DIR__ . '/Engine/Model/',
	__DIR__ . '/Engine/View/',
	'mvc/Controllers/',
	'mvc/Models/',
	'mvc/Views/',
	'config/'
)));

require_once realpath( __DIR__ . '/vendor/Word/PhpWord/Autoloader.php' );
\PhpOffice\PhpWord\Autoloader::register();


spl_autoload_register(function ($class) {
	$file = $class . '.php';
	try {
		if ( !@include_once "$file" )
			throw new Exception();
	} catch ( Exception $e ) {
		header( "Location: " . BASE_URL . "/error" );
		exit;
	}
});