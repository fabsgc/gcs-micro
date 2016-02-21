<?php

/**
 * @file : vendor/Core/Engine.php
 * @author : Fabien Beaujean
 * @description : Manager of the application
 * The goal of the engine is to determinate thanks to the url and the
 * config file to which page the user want to access and if he is allowed to do this.
 */

namespace Core;

/**
 * Class Engine
 * @package Core
 */

class Engine{

	/**
	 * @var \Core\Page
	 */

	private $page;

	/**
	 * @var \Core\Router
	 */

	private $route = false;

	/**
	 * init the application
	 * @access public
	 * @return \Core\Engine
	 */

	public function init(){
		$this->route();
		return $this;
	}

	/**
	 * run the application. If routing had found a route which match with user request before,
	 * instantiate the correct class page. Otherwise return a 404 page error
	 * @access public
	 * @throws \Exception
	 * @return void
	 */

	public function run(){
		if($this->route == true){
			$this->page();
			$request = Request::instance();

			$className = '\\'.ucfirst($request->page);

			/** @var \Core\Page $class */
			$class = new $className();

			if(($request->logged == '*' && $request->access == '*') || $class->setFirewall() == true){
				ob_start();
					if(method_exists($class,$request->action)){
						$action = $request->action;
						$output = $class->$action();
					}
					else{
						throw new \Exception('The requested '.ucfirst($request->page).'/'.$request->action.'"  doesn\'t exist');
					}

					$output = ob_get_contents().$output;
				ob_get_clean();

				echo $output;
			}
		}
		else{
			header('HTTP/1.0 404 Not Found');

			echo (new View('core/error.php'))
				->assign('error', '404 Not Found')
				->assign('title', '404 Not Found')
				->render();
		}
	}

	/**
	 * routing. This method read all the routing rules from de config file and determine which one match with the user request
	 * (check url and access privilege)
	 * @access private
	 * @return void
	 */

	private function route(){
		$config = Config::instance()->get();
		$request = Request::instance();
		$router = new Router($this);

		foreach ($config['routes'] as $key => $data) {
			$page = explode('.', $data['action'])[0];
			$action = explode('.', $data['action'])[1];
			$router->add(new Route($key, $data['url'], $page, $action, $data['vars'], $data['logged'], $data['access'], $data['method']));
		}

		if($matched = $router->get(preg_replace('`\?'.preg_quote($_SERVER['QUERY_STRING']).'`isU', '', $_SERVER['REQUEST_URI']))){
			$_GET = array_merge($_GET, $matched->vars);

			$request->name       =       $matched->name;
			$request->page = $matched->page;
			$request->action     =     $matched->action;
			$request->logged     =     $matched->logged;
			$request->access     =     $matched->access;
			$request->method     =     $matched->method;

			$this->route = true;
		}
	}

	/**
	 * include the php page file needed
	 * @access public
	 * @return void
	 * @throws \Exception
	 */

	protected function page(){
		$pagePath = 'app/page/'.ucfirst(Request::instance()->page).'.php';

		if(file_exists($pagePath)){
			require_once($pagePath);
		}
	}
}