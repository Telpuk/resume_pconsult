<?php
class AdminControlController extends IController{
	private $_view,
		$_db_admin,
		$_count_view = 2,
		$_page = null;

	public function  __construct(){
		parent::__construct();
		$this->_view = new View();
		$this->_db_admin = new Admin();
	}


	public function indexAction(){
		$search = isset($_GET['search']) ? explode('/', $_GET['search']) : array();

		if (isset($search[0]) && !empty($search[0])) {
			$page = $this->getParams('page');
			$this->_page = empty($page)?null:$page;

			$users = $this->_db_admin->search($search[0], $this->_count_view, $this->_page);

			$users['count'] = $this->getSessionParamsId('count_users');

		}elseif(!isset($search[0]) || (isset($search[0]) && empty($search[0]))) {
			$page = $this->getParams('page');
			$this->_page = empty($page)?null:$page;

			$users = $this->_db_admin->selectAllResume($this->_count_view, $this->_page);

			$users['count'] = $this->getSessionParamsId('count_users');

		}

		return $this->_view->render(array(
			'view' => 'admin_control/index',
			'data' => array(
				'users' => $users['users'] ? $users['users']  : '',
				'users_count' => "Количество найденных анкет: " . $users['count'],
				'search' => $search[0],
				'pagination' => $this->_db_admin->printPagination(
					ceil($users['count'] / $this->_count_view), $this->_page, $search[0]))));

	}
}