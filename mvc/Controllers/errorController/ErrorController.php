<?php

class ErrorController extends IController{
	public function __construct(){}

	public function indexAction(){
		$view = new View();
		return $view->render(['view'=>'error/index']);
	}

}