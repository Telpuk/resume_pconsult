<?php
class AdminControlController extends IController{
	private $_view,
		$_db_admin,
		$_count_view = 1,
		$_page = null;

	public function  __construct(){
		parent::__construct();
		$this->_view = new View();
		$this->_db_admin = new Admin();
	}

	public function unreviewedAction(){

		if ($this->getParams('view')) {
			$page = $this->getParams('page');
			$this->_page = empty($page)?null:$page;

			$users = $this->_db_admin->selectResumeNoView($this->_count_view, $this->_page);

			$users['count'] = $this->getSessionParamsId('count_view_admin_resume');

			return $this->_view->render(array(
				'view' => 'admin_control/index',
				'data' => array(
					'helpers' => array('widget_admin' => 'admin_control/helpers/widget'),
					'active_all_no_view'=>true,
					'users' => $users['users'] ? $users['users']  : '',
					'users_count'=>$this->getSessionParamsId('count_users'),
					'count_view_admin_resume'=>$this->getSessionParamsId('count_view_admin_resume'),
					'pagination' => $this->_db_admin->printPagination(
						ceil($users['count'] / $this->_count_view), $this->_page, array(
							'url'=>"unreviewed/view/".$this->getParams('view')))
				)
			));
		}
		$this->headerLocation('admincontrol');
	}


	public function indexAction(){
		$search = isset($_GET['search']) ? explode('/', $_GET['search']) : array();

		if (isset($search[0]) && !empty($search[0])) {
			$page = $this->getParams('page');
			$this->_page = empty($page)?null:$page;

			$users = $this->_db_admin->search($search[0], $this->_count_view, $this->_page);

		}elseif(!isset($search[0]) || (isset($search[0]) && empty($search[0]))) {
			$page = $this->getParams('page');
			$this->_page = empty($page)?null:$page;

			$users = $this->_db_admin->selectAllResume($this->_count_view, $this->_page);
		}

		$users['count'] = $this->getSessionParamsId('count_users');

		return $this->_view->render(array(
			'view' => 'admin_control/index',
			'data' => array(
				'helpers' => array('widget_admin' => 'admin_control/helpers/widget'),
				'active_all_resume'=>true,
				'users' => $users['users'] ? $users['users']  : '',
				'users_count' =>$users['count'],
				'count_view_admin_resume'=>$this->getSessionParamsId('count_view_admin_resume'),
				'search' => $search[0],
				'pagination' => $this->_db_admin->printPagination(
					ceil($users['count'] / $this->_count_view), $this->_page, array(
						'url'=>"index/search/?search=".$search[0]))
			)
		));

	}
}