<?php

/**
 * @file : vendor/Core/Request
 * @author : Fabien Beaujean
 * @description : Request container
 */

namespace Core;

/**
 * Class Request
 * @property string name
 * @property string page
 * @property string action
 * @property string logged
 * @property string access
 * @property string method
 * @package Core
 */

class Request{

	/**
	 * parameters of each action
	 * @var array
	 */

	private $param = [
		'name'   =>   '',
		'page'   =>   '',
		'action' =>   '',
		'logged' =>  '*',
		'access' =>  '*',
		'method' =>  '*',
	];

	/**
	 * singleton instance
	 * @var \Core\Request
	 */

	private static $instance = null;

	/**
	 * constructor
	 * @access private
	 */

	private function __construct (){
	}

	/**
	 * singleton static method
	 * @access public
	 */

	public static function instance(){
		if (is_null(self::$instance))
			self::$instance = new Request();

		return self::$instance;
	}

	/**
	 * Magic get method allows access to parsed routing parameters directly on the object.
	 * @access public
	 * @param string $name : name of the attribute
	 * @return mixed
	 * @throws \Exception
	 */

	public function __get($name){
		if (isset($this->param[$name])) {
			return $this->param[$name];
		}
		else{
			throw new \Exception('the attribute "'.$name.'" doesn\'t exist');
		}
	}

	/**
	 * Magic get method allows access to parsed routing parameters directly on the object to modify it
	 * @access public
	 * @param $name string : name of the attribute
	 * @param $value string : new value
	 * @return void
	 * @throws \Exception
	 */

	public function __set($name, $value){
		if (isset($this->param[$name])) {
			$this->param[$name] = $value;
		}
		else{
			throw new \Exception('the attribute "'.$name.'" doesn\'t exist');
		}
	}

	/**
	 * get server data
	 * @access public
	 * @param $env
	 * @return bool
	 */

	public function env($env){
		if(isset($_SERVER[$env])){
			return $_SERVER[$env];
		}
		else{
			return false;
		}
	}
}