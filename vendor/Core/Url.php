<?php

/**
 * @file : vendor/Core/Url.php
 * @author : Fabien Beaujean
 * @description : Url generator
 */

namespace Core;

/**
 * Class Url
 * @package Core
 */

class Url{

	/**
	 * @param string $name
	 * @param array $vars
	 * @return string
	 */

	public static function get($name, $vars = []){
		$config = Config::instance()->get()['routes'];

		if(isset($config[$name])){
			$route = $config[$name];

			$url = preg_replace('#\((.*)\)#isU', '<($1)>',  $route['url']);
			$urls = explode('<', $url);
			$result = '';
			$i=0;

			foreach($urls as $url){
				if(preg_match('#\)>#', $url)){
					if(count($vars) > 0){
						if(isset($var[$i])){
							$result.= preg_replace('#\((.*)\)>#U', $var[$i], $url);
						}
						else{
							$result.= preg_replace('#\((.*)\)>#U', '', $url);
						}

						$i++;
					}
				}
				else{
					$result.=$url;
				}
			}

			$result = preg_replace('#\\\.#U', '.', $result);

			if(FOLDER != '')
				return '/'.substr(FOLDER, 0, strlen(FOLDER)-1).$result;
			else
				return $result;
		}

		return '';
	}
}