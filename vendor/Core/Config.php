<?php

/**
 * @file : vendor/Core/Config.php
 * @author : Fabien Beaujean
 * @description : Config manager
 * At the beginning of the execution, this class include the file config.php and store the array returned in an attribute
 * Then, when the code need to access to the configuration from config.php, he only need to call this singleton class
 */

namespace Core;


/**
 * Class Config
 * @package Core
 */

class Config{

	/**
	 * Config array
	 * @var array
	 */

	private $config = [];

	/**
	 * singleton instance
	 * @var \Core\Config
	 */

	private static $instance = null;

	/**
	 * constructor
	 * @access private
	 */

	private function __construct(){
		$this->config = require_once('config.php');
	}

	/**
	 * singleton static method
	 * @access public
	 * @return \Core\Config
	 */

	public static function instance(){
		if (is_null(self::$instance))
			self::$instance = new Config();

		return self::$instance;
	}

	/**
	 * get config array
	 * @access public
	 * @return array
	 */

	public function get(){
		return $this->config;
	}
}