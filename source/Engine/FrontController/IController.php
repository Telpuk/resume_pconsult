<?php

abstract class IController extends SessionController
{
	protected
		$_paramsGET = array(),
		$_body,
		$_fc,
		$_currentUrl;


	public function __construct()
	{
		$this->_fc = FrontController::getInstance();
		$this->setParams( $this->_fc->getParams() );
	}

	public function setParams( $params )
	{
		$this->_paramsGET = $params;
	}

	public function headerLocation( $url = null )
	{
		header( 'Location: ' . BASE_URL . '/' . $url );
		exit;
	}

	public function writeCurrentUrlCookies( $url = null )
	{
		if ( $url ) {
			$_SESSION['currentUrl'] = $url;
		}

	}

	public function readCurrentUrlCookies()
	{
		return isset( $_SESSION['currentUrl'] ) ? $_SESSION['currentUrl'] : null;
	}

	public function writeSearchCookies( $data = null )
	{
		if ( $data && !is_null( $data ) ) {
			$_SESSION['search_admin'] = $data;
		} else {
			unset( $_SESSION['search_admin'] );
		}

	}

	public function readSearchCookies()
	{
		return isset( $_SESSION['search_admin'] ) ? $_SESSION['search_admin'] : null;
	}

	public function getCurrentUrl()
	{
		return $this->_fc->getCurrentUrl();
	}

	public function getParams( $param )
	{
		return isset( $this->_paramsGET[$param] ) ? $this->_paramsGET[$param] : null;
	}

}