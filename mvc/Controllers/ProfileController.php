<?php
	class ProfileController extends IController
	{
		private $_view;

		public function __construct ()
		{
			parent::__construct ();
			$this->_view = new View();
		}


		public function indexAction ()
		{
			return $this->_view->render ([
				'view' => 'profile/create_profile',
			]);
		}


	}


