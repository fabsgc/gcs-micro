<?php

/**
 * @file : vendor/Core/Engine
 * @author : Fabien Beaujean
 * @description : View manager
 */

namespace Core;

/**
 * Class View
 * @package Core
 */

class View{

	/**
	 * @var string $file
	 */

	private $file;

	/**
	 * @var array $vars
	 */

	private $vars = [];

	/**
	 *constructor.
	 * @param string $file
	 * @return \Core\View
	 */

	public function __construct($file){
		$this->file = $file;
		return $this;
	}

	/**
	 * insert variable
	 * @param $name
	 * @param $vars
	 * @return \Core\View
	 */

	public function assign($name, $vars = ''){
		if(is_array($name))
			$this->vars = array_merge($this->vars, $name);
		else
			$this->vars[$name] = $vars;

		return $this;
	}

	/**
	 * Return the render
	 * @throws \Exception
	 * @return string
	 */

	public function render(){
		foreach ($this->vars as $key => $value){
			${$key} = $value;
		}

		ob_start();
			if(file_exists('app/view/'.$this->file))
				require_once('app/view/'.$this->file);
			else
				throw new \Exception('The view "app/view/'.$this->file.'" doesn\'t exist');

			$output = ob_get_contents();
		ob_get_clean();

		return $output;
	}
}