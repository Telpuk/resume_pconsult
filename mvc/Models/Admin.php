<?php
class Admin{
	private
		$_dbc,
		$_user_object,
		$_month = array(1=>"январь",
		2=>"февраль",
		3=>"март",
		4=>"апрель",
		5=>"май",
		6=>"июнь",
		7=>"июль",
		8=>"август",
		9=>"сентябрь",
		10=>"октябрь",
		11=>"ноябрь",
		12=>"декабрь");

	function __construct(){
		$this->_dbc = Model::getInstance()->getDbh();
		$this->_user_object = new User();
	}

	public function deleteNotStockedResume(){
		try {
			$sql = "DELETE
						profile
					FROM
						profile
					WHERE registered_user='no' AND  DATEDIFF(CURRENT_TIMESTAMP(),date) >=1";
			$this->_dbc->query($sql);
		}catch (PDOException $e){
			exit(print_r($e->errorInfo).$e->getFile());
		}
	}

	public  function checkLoginAndPassword($data){
		try {
			$stmt = $this->_dbc->prepare ("
												SELECT
													login
												FROM
													users_access
												WHERE
													login = :login
												AND
													password = :password"
			);
			$stmt->execute(array(
				':login'=>$data['login'],
				':password'=>$data['password']
			));
			$user_access_data = $stmt->fetch(PDO::FETCH_ASSOC);
		}catch (PDOException $e){
			exit(print_r($e->errorInfo).$e->getFile());
		}

		if($user_access_data['login']){
			return $user_access_data['login'];
		}

		return false;
	}

	public function selectAllResume($count_view, $page)
	{
		try {
			$stmt = $this->_dbc->prepare("CALL allResume(:start,:count_view)");
			$stmt->execute(array(':start' => $page, ':count_view' => $count_view));
			$search_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
		} catch (PDOException $e) {
			exit(print_r($e->errorInfo) . $e->getFile());
		}

		if (is_null($page)) {

			$count_user = count($search_data);
			$_SESSION['params']['count_users'] = $count_user;

			if ($count_user > $count_view) {
				for ($i = $count_view; $i < $count_user; ++$i) {
					unset($search_data[$i]);
				}

			}
		}


		return $this->getResumeFormat($search_data, $count_user);
	}

	public function search($search, $count_view, $page){

		try {
			$stmt = $this->_dbc->prepare ("CALL searchResume(:search, :start, :count_view)");
			$stmt->execute(array(
				':search'=>"%".$search."%",
				':start' => $page,
				':count_view'=>$count_view
			));
			$search_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
		}catch (PDOException $e){
			exit(print_r($e->errorInfo).$e->getFile());
		}

		if (is_null($page)) {

			$count_user = count($search_data);
			$_SESSION['params']['count_users'] = $count_user;

			if ($count_user > $count_view) {
				for ($i = $count_view; $i < $count_user; ++$i) {
					unset($search_data[$i]);
				}

			}
		}

		return $this->getResumeFormat($search_data, $count_user);

	}

	private function getResumeFormat($search_data, $count_user){
		foreach($search_data as $key =>$data){
			$experience_count[$key] = $this->_user_object->getExperienceCount(
				array(
					'experience_getting_starteds'=>explode('[@!-#-!@]',$data['experience_getting_starteds']),
					'experience_closing_works'=>explode('[@!-#-!@]',$data['experience_closing_works']),
					'experience_at_the_moments'=>explode('[@!-#-!@]',$data['experience_at_the_moments'])
				)
			);
			$search_data[$key]['sum_experience'] = $experience_count[$key]['sum'];
			$search_data[$key]['salary'] = $data['salary'] ? $data['salary']." ".$data['currency'] : '';

			$search_data[$key]['last_place_work'] = $this->lastPlaceWork(array(
				'experience_positions'=>explode('[@!-#-!@]',$data['experience_positions']),
				'experience_organizations'=>explode('[@!-#-!@]',$data['experience_organizations']),

				'experience_getting_starteds'=>explode('[@!-#-!@]',$data['experience_getting_starteds']),
				'experience_closing_works'=>explode('[@!-#-!@]',$data['experience_closing_works']),
				'experience_at_the_moments'=>explode('[@!-#-!@]',$data['experience_at_the_moments'])
			));
		}

		return array('users'=>$search_data, 'count'=>$count_user);
	}

	private function lastPlaceWork($data_user){
		$data = '';
		$date = array();

		if($data_user['experience_getting_starteds'][0] && $data_user['experience_organizations'][0]){
			foreach($data_user['experience_getting_starteds'] as $key=>$start_data){
				if($data_user['experience_at_the_moments'][$key] == 'true'){
					$date[$key] = new DateTime(date("Y-m"));
				}else{
					$date[$key] = new DateTime($data_user['experience_closing_works'][$key]."-1");
				}
			}
			$temp = $date[0];
			for($i = 1; $i < count($date); $i++){
				if($temp < $date[$i]){
					$temp =  $date[$i];
					$key = $i;
				}
			}
			$data = explode('-',$temp->format("Y-m"));
			if($data_user['experience_at_the_moments'][$key] == 'true'){
				$data_f = $this->_month[(int)$data[1]]." ".$data[0]."&mdash;по ностоящее время";
			}else{
				$data_f= $this->_month[(int)$data[1]]." ".$data[0];
			}
			$data['date'] = $data_f;
			$data['last_works'] = $data_user['experience_organizations'][$key];
			$data['last_position'] = $data_user['experience_positions'][$key];

		}

		return $data;
	}

	public  function printPagination($countPage, $actPage, $search){
		$actPage = $actPage ? $actPage:1;

		if ($countPage == 0 || $countPage == 1 || $countPage < $actPage) return '';

		if ($countPage > 10){
			if($actPage <= 4 || $actPage + 3 >= $countPage){
				for($i = 0; $i <= 4; $i++) {
					$class = (($i + 1) == $actPage)? 'active' : 'no-active';
					$pageArray[$i] = "<li><a class='$class' href='".BASE_URL."/admincontrol/index/search/?search=".$search."/page/".($i + 1)."'>".($i + 1)."</a></li>";
				}
				$pageArray[5] = "<li>...</li>";
				for($j = 6, $k = 4; $j <= 10; $j++, $k--){
					$class = (($countPage - $k) == $actPage)? 'active' : 'no-active';
					$pageArray[$j] = "<li><a class='$class' href='".BASE_URL."/admincontrol/index/search/?search=".$search."/page/".($countPage - $k)."'>".($countPage - $k)."</a></li>";
				}
			}else{
				$pageArray[0] = "<li><a class='no-active' href='".BASE_URL."/admincontrol/index/search/?search=".$search."/page/1'>1</a></li>";
				$pageArray[1] = "<li><a class='no-active' href='".BASE_URL."/admincontrol/index/search/?search=".$search."/page/2'>2</a></li>";
				$pageArray[2] = "<li>...</li>";
				$pageArray[3] = "<li><a class='no-active' href='".BASE_URL."/admincontrol/index/search/?search=".$search."/page/".($actPage - 2)."'>".($actPage - 2)."</a></li>";
				$pageArray[4] = "<li><a class='no-active' href='".BASE_URL."/admincontrol/index/search/?search=".$search."/page/".($actPage - 1)."'>".($actPage - 1)."</a></li>";
				$pageArray[5] = "<li><a class='active' href='".BASE_URL."/admincontrol/index/search/?search=".$search."/page/".$actPage."'>".$actPage."</a></li>";
				$pageArray[6] = "<li><a class='no-active' href='".BASE_URL."/admincontrol/index/search/?search=".$search."/page/".($actPage + 1)."'>".($actPage + 1)."</a></li>";
				$pageArray[7] = "<li><a class='no-active' href='".BASE_URL."/admincontrol/index/search/?search=".$search."/page/".($actPage + 2)."'>".($actPage + 2)."</a></li>";
				$pageArray[8] = "<li>...</li>";
				$pageArray[9] = "<li><a class='no-active' href='".BASE_URL."/admincontrol/index/search/?search=".$search."/page/".($countPage - 1)."'>".($countPage - 1)."</a></li>";
				$pageArray[10] = "<li><a class='no-active' href='".BASE_URL."/admincontrol/index/search/?search=".$search."/page/".$countPage."'>".$countPage."</a></li>";
			}
		}else{
			for($n = 0; $n < $countPage; $n++) {
				$class = ($n == ($actPage-1))? 'active' : 'no-active';
				$pageArray[$n] = "<li><a class='$class' href='".BASE_URL."/admincontrol/index/search/?search=".$search."/page/".($n + 1)."'>".($n + 1)."</a></li>";
			}
		}

		return "<ul>".implode('',$pageArray)."</ul>";
	}

}