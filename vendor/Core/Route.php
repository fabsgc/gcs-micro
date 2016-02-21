<?php

/**
 * @file : vendor/Core/Route.php
 * @author : Fabien Beaujean
 * @description : Route manager
 */

namespace Core;

/**
 * Class Route
 * @property string $name
 * @property string $url
 * @property string $page
 * @property string $action
 * @property string $vars[]
 * @property string $varsName[]
 * @property string $logged
 * @property string $access
 * @property string $method
 * @method vars(array $vars)
 * @package Core
 */

class Route{

	/**
	 * @var string[] $data
	 */

	private $data = [
		'name'     => '',
		'url'      => '',
		'page'     => '',
		'action'   => '',
		'vars'     => [],
		'varsName' => [],
		'logged'   => '',
		'access'   => '',
		'method'   => ''
	];

	/**
	 * each route from route.xml become an instance of this class
	 * @access public
	 * @param string $name
	 * @param string $url
	 * @param string $page
	 * @param string $action
	 * @param array $varsName
	 * @param string $logged string
	 * @param string $access string
	 * @param string $method string
	 */

	public function __construct($name, $url, $page, $action, $varsName = [], $logged, $access, $method){
		$this->data['name'] = $name;
		$this->data['url'] = $url;
		$this->data['page'] = $page;
		$this->data['action'] = $action;
		$this->data['varsName'] = $varsName;
		$this->data['logged'] = $logged;
		$this->data['access'] = $access;
		$this->data['method'] = $method;
	}

	/**
	 * We want to know if the route has vars
	 * @return bool
	 */

	public function hasVars(){
		if(count($this->data['varsName']) > 0)
			return true;

		return false;
	}

	/**
	 * Get route properties
	 * @param string $key
	 * @return bool
	 */

	public function __get($key){
		if(isset($this->data[$key]))
			return $this->data[$key];

		return '';
	}

	/**
	 * Set route properties
	 * @param string $key
	 * @param string $value
	 * @return void
	 */

	public function __set($key, $value){
		if(isset($this->data[$key]))
			$this->data[$key] = $value;
	}

	/**
	 * @param $url
	 * @return array
	 * @since 3.0
	 */

	public function match($url){
		if (preg_match('`^'.$this->data['url'].'$`', $url, $matches)){
			return $matches;
		}
		else{
			return false;
		}
	}
}