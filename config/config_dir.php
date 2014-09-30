<?php
	$project_folder = str_replace( [$_SERVER['DOCUMENT_ROOT'],'/',basename(dirname(__FILE__))],[""],__DIR__);
	return [
		'host' => $_SERVER["HTTP_HOST"],
		'project_folder'=>$project_folder,
		'dir_project' =>  $_SERVER['DOCUMENT_ROOT']."/".$project_folder
	];