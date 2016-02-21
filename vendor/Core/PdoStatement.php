<?php

/**
 * @file : vendor/Core/Pdo.php
 * @author : Fabien Beaujean
 * @description : PDO overriding to allow the system to log every query
 */

namespace Core;

/**
 * Class PdoStatement
 * @package Core
 */

class PdoStatement extends \PDOStatement{

	/**
	 * list of vars for each query
	 * @var array
	*/

	private $_debugBindValues = [];

	/**
	 * constructor
	 * @access private
	 * @package Core
	*/

	private function __construct(){
	}

	/**
	 * override binvalue to keep in memory the vars
	 * @access public
	 * @param string $parameter
	 * @param string $value 
	 * @param int$dataType
	 * @return void
	 * @package Core
	 */

	public function bindValue($parameter, $value, $dataType = \PDO::PARAM_STR){
		$this->_debugBindValues[$parameter] = $value;
		parent::bindValue($parameter, $value, $dataType);
	}

	/**
	 * return query with vars
	 * @access public
	 * @param bool $replaced
	 * @return string
	 * @package Core
	*/

	public function debugQuery($replaced = true){
		$query = $this->queryString;

		if (!$replaced) {
			return $query;
		}
		else{
			if(count($this->_debugBindValues) > 0){
				return preg_replace_callback('/:([0-9a-z_]+)/i', [$this, 'debugReplaceBindValue'], $query);
			}
			else{
				return $query;
			}
		}
	}

	/**
	 * replace vars in the query
	 * @access protected
	 * @param $m array
	 * @return string
	 * @package Core
	*/

	private function debugReplaceBindValue($m){
		$v = $this->_debugBindValues[':'.$m[1]];

		switch(gettype($v)){
			case 'bool' :
				return $v;
			break;

			case 'integer' :
				return $v;
			break;

			case 'double' :
				return $v;
			break;

			case 'string' :
				return "'".addslashes($v)."'";
			break;

			case 'NULL' :
				return 'NULL';
			break;

			default :
				return 'NULL';
			break;
		}
	}
}