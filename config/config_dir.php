<?php
	$project_folder = str_replace( array($_SERVER['DOCUMENT_ROOT'],'/',basename(dirname(__FILE__))),array(""),__DIR__);
	return array(
		'host' => $_SERVER["HTTP_HOST"],
		'project_folder'=>$project_folder,
		'dir_project' =>  $_SERVER['DOCUMENT_ROOT']."/".$project_folder
	);