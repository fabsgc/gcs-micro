<?php

/**
 * @file : vendor/Autoload.php
 * @author : Fabien Beaujean
 * @description : Autoloader
 */

class Autoload{

	/**
	 * Autoloader for classes
	 * @param string $class : partial path to the class to include
	 * @return void
	 */

	public static function load($class){
		if(file_exists('vendor/'.$class.'.php')){
			require_once('vendor/'.$class.'.php');
		}
	}
}

spl_autoload_register(__NAMESPACE__ . "\\Autoload::load");