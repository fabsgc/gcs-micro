<?php

/**
 * @file : vendor/Core/Router.php
 * @author : Fabien Beaujean
 * @description : Router manager
 */

namespace Core;

/**
 * Class Router
 * @package Core
 */

class Router{

	/**
	 * contain all the routes
	 * @var \Core\Route[]
	 */

	private $routes = [];

	/**
	 * add route to the instance
	 * @access public
	 * @param Route $route
	 * @return void
	 */

	public function add(Route $route){
		if (!in_array($route, $this->routes)){
			$this->routes[] = $route;
		}
	}

	/**
	 * Return the right route which match
	 * @access public
	 * @param string $url
	 * @return \Core\Route
	 */

	public function get($url){
		$url = substr($url, strlen(FOLDER), strlen($url));
		$config = Config::instance()->get();
		$routeRight = null;

		foreach ($this->routes as $route){
			$varsValues = $route->match($url);

			if ($varsValues != false && ($route->method == '*' || in_array(strtolower($_SERVER['REQUEST_METHOD']), explode(',', $route->method)) || $route->method == strtolower($_SERVER['REQUEST_METHOD']))){
				$routeRight = $route;
				if ($route->hasVars()){
					$varsNames = $route->varsName;
					$listVars = [];

					foreach ($varsValues as $key => $match){
						if ($key > 0){
							if(array_key_exists($key - 1, $varsNames)){
								$listVars[$varsNames[$key - 1]] = $match;
							}
						}
					}

					$route->vars = $listVars;
				}

				/**
				 * sometimes, it's possible to have several times the same URL, for example, one when we are logged and one when we are not logged.
				 * each url has a different ID, so, when we have an url which her "logged" attribute is not correct,
				 * we have to check if there is an other url whith the right "logged" attribute
				 */
				$logged = $config['firewall']['logged'];
				$role = $config['firewall']['role'];

				switch($route->logged){
					case 'true' :
						if($logged == true && (in_array($role, array_map('trim', explode(',', $route->access))) || $route->access == '*')){
							return $route;
						}
					break;

					case 'false' :
						if($logged == false){
							return $route;
						}
					break;

					case '*' :
						return $route;
					break;
				}
			}
		}

		if($routeRight != null && $routeRight->match($url) != false)
			return $routeRight;

		return null;
	}
}