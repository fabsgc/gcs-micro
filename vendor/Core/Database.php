<?php

/**
 * @file : vendor/Core/Database.php
 * @author : Fabien Beaujean
 * @description : Database manager
 * The goal of this class is to conserve the instance of PDO. Indeed, thanks to the design pattern Singleton, the PDO instance is
 * created once only.
 */

namespace Core;

/**
 * Class Database
 * @package Core
 */

class Database{

	/**
	 * @var \PDO
	 */

	private $db;

	/**
	 * constructor
	 * @access private
	 */

	/**
	 * singleton instance
	 * @var \Core\Database
	 */

	private static $instance = null;

	private function __construct (){
		$this->connect(Config::instance()->get()['database']);
	}

	/**
	 * singleton static method
	 * @access public
	 * @return \Core\Config
	 */

	public static function instance(){
		if (is_null(self::$instance))
			self::$instance = new Database();

		return self::$instance;
	}

	/**
	 * create database connection
	 * @param $db
	 * @access private
	 * @return void
	 */

	private function connect($db = []){
		if(Config::instance()->get()['database']['enabled']) {
			$options = [\PDO::ATTR_STATEMENT_CLASS => ['\Core\PdoStatement', []]];

			try {
				$this->db = new \PDO('mysql:host=' . $db['host'] . ';dbname=' . $db['database'], $db['username'], $db['password'], $options);
				$this->db->exec('SET NAMES UTF-8');
			} catch (\PDOException $e) {
				throw new \PDOException($e->getMessage() . ' / ' . $e->getCode());
			}
		}
	}

	/**
	 * get PDO instance
	 * @access public
	 * @return \PDO
	 */

	public function get(){
		return $this->db;
	}
}