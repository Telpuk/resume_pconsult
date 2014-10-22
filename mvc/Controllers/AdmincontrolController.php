<?php
class AdminControlController extends IController{
	private $_view,
		$_db_admin,
		$_count_view =3;

	public function  __construct(){
		parent::__construct();
		$this->_view = new View();
		$this->_db_admin = new Admin();
	}


	public function indexAction(){
		if(isset($_GET['search'])){
			$search = explode('/',$_GET['search']);
			if($search[0]) {
				$page = $this->getParams('page');

				$users = $this->_db_admin->search($search[0], $this->_count_view, $page-1);


				if($page >= 0){
					$users['count'] = $this->getSessionParamsId('count_users');
				}

				return $this->_view->render(array(
					'view' => 'admin_control/index',
					'data' => array(
						'users' => $search[0] ? $users['users'] : '',
						'users_count'=>"Количество найденных анкет: ".$users['count'],
						'search' => $search[0],
						'pagination' => $this->_db_admin->printPagination(
							ceil($users['count'] / $this->_count_view), $page, $search[0])
					)));
			}
		}

		return $this->_view->render(array(
			'view'=>'admin_control/index'
		));
	}
}