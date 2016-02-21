<?php

/**
 * @file : vendor/Core/Page.php
 * @author : Fabien Beaujean
 * @description : Page manager
 */

namespace Core;

/**
 * Class Page
 * @package Core
 */

class Page{

	/**
	 * @var \Core\Request
	 */

	protected $request;

	/** @var  \PDO */

	protected $db;

	/**
	 * constructor
	 */

	final public function __construct(){
		$this->request = Request::instance();
		$this->db = Database::instance()->get();
	}

	/**
	 * check firewall
	 * @access public
	 * @return bool
	 */

	final public function setFirewall(){
		$firewall = new Firewall();

		if($firewall->check())
			return true;
		else
			return false;
	}
}