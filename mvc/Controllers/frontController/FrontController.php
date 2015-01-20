<?php
class FrontController extends Config{
	static private $_instance = null;
	private $_controller, $_action, $_content ,$_body, $_currentUrl, $_params = array();

	private function __construct(){

		$this->getConfig();
		$this->setCurrentUrl(urldecode($_SERVER["REQUEST_URI"]));

		$router  = explode("/", trim($this->getCurrentUrl(), '/'));

		if(($key = array_search(trim(PROJECT_FOLDER, "/"), $router)) !== false)
		{
			$cntr_ind = 1; $actn_ind = 2; $prms_ind = 3;
			unset($router[$key]);
		}else{
			$cntr_ind = 0; $actn_ind = 1; $prms_ind = 2;
		}

		$this->setController(!empty($router[$cntr_ind]) ? ucfirst(strtolower($router[$cntr_ind]))."Controller" :
			"IndexController");

		$this->setAction(!empty($router[$actn_ind]) ? strtolower($router[$actn_ind]).'Action' : "indexAction");

		$this->_checkSession($this->getController());

		if( !empty($router[$prms_ind]) ){
			$paramsSlice = array_slice($router, 2);
			$params = array();

			foreach($paramsSlice as $key => $value){
				if( $key % 2 === 0){
					if(empty($paramsSlice[$key+1])){
						$params[$value] = '';
						continue;
					}
					$params[$value] = $paramsSlice[$key+1];
				}
			}
			$this->setParams($params);
		}
	}

	private function __clone(){}

	protected function getConfig(){
		$this->_setConfigBD(require_once "config_bd.php");
		$this->_setConfigDIR(require_once "config_dir.php");
		$this->_setConfigJS(require_once "config_js.php");
	}

	private  function _checkSession($controller){
		$sessionControllers = new SessionController();
		$sess_user = $sessionControllers->getSessionUsers();
		$sessionContr = $sessionControllers->getControllers();
		foreach($sessionContr as $key => $cntrls){
			if(in_array($controller, $cntrls['controllers']) && !array_key_exists($key, (array)$sess_user)){
				header ("Location: " . BASE_URL);
				exit;
			}
		}
	}

	public function getParams(){
		return $this->_params;
	}
	public function getAction(){
		return $this->_action;
	}

	public function getCurrentUrl(){
		return $this->_currentUrl;
	}
	public function setCurrentUrl($url = null){
		$this->_currentUrl = $url;
	}
	public function getController(){
		return $this->_controller;
	}
	public function getContent(){
		return $this->_content;
	}
	public function setContent( $content){
		$this->_content = $content;
	}
	public function getBody(){
		return $this->_content;
	}
	public function setParams($params){
		$this->_params = $params;
	}
	public function setAction($action){
		$this->_action = $action;
	}
	public function setController($controller){
		$this->_controller = $controller;
	}
	public function setBody($body){
		$this->_body = $body;
	}

	public function run(){
		if(class_exists($this->getController())){
			$class = new ReflectionClass($this->getController());

			if( $class->hasMethod($this->getAction()) ){

				$controller = $class->newInstance();

				$method = $class->getMethod($this->getAction());

				$this->setContent($method->invoke($controller));

			}else{
				$error = new ErrorController();
				$this->setContent($error->indexAction());
			}
		}else{
			$error = new ErrorController();
			$this->setContent($error->indexAction());
		}


	}

	static public function getInstance(){
		if(self::$_instance instanceof self){
			return self::$_instance;
		}
		self::$_instance = new FrontController();
		return  self::$_instance;
	}



}