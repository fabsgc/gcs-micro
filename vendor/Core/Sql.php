<?php

/**
 * @file : vendor/Core/Sql.php
 * @author : Fabien Beaujean
 * @description : Sql manager
 */

namespace Core;

/**
 * Class Sql
 * @package Core
 */

class Sql{

	/**
	 * @var array mixed
	 */

	protected $vars = [];

	/**
	 * @var string $query
	 */

	protected $query = '';

	/**
	 * results
	 * @var array
	 */

	protected $data = [];

	/**
	 * @var \PDO $db
	 */

	protected $db;

	const PARAM_INT                 = 1;
	const PARAM_BOOL                = 5;
	const PARAM_NULL                = 0;
	const PARAM_STR                 = 2;
	const PARAM_LOB                 = 3;
	const PARAM_FETCH               = 0;
	const PARAM_FETCHCOLUMN         = 1;
	const PARAM_FETCHINSERT         = 2;
	const PARAM_FETCHUPDATE         = 3;
	const PARAM_FETCHDELETE         = 4;

	/**
	 * constructor
	 * @access public
	 * @since 3.0
	 * @package System\Sql
	 */

	public function __construct (){
		$this->db = Database::instance()->get();
	}

	/**
	 * Add a new query to the instance
	 * @access public
	 * @param string $query
	 * @return void
	 */

	public function query($query){
		$this->query = $query;
	}

	/**
	 * add variables to the instance
	 * @access public
	 * @param $var  mixed : contain the list of the variable that will be used in the queries.
	 *  first syntax  : array('id' => array(31, Sql::PARAM_INT), 'pass' => array("fuck", sql::PARAM_STR))
	 *  second syntax : array('id' => 31, 'pass' => "fuck"). If you don't define the type of the variable, the class will assign itself the correct type
	 *  If you have only one variable to pass, you can use the 2/3 parameters form
	 *	first syntax  : ('id', 'value')
	 *  second syntax : ('id', 'value', Sql::PARAM_INT)
	 * @return void
	 */

	public function vars($var){
		if(is_array($var)){
			foreach($var as $key => $valeur){
				$this->vars[$key] = $valeur;
			}
		}
		else if(func_num_args() == 2){
			$args = func_get_args();
			$this->vars[$args[0]] = $args[1];
		}

		else if(func_num_args() == 3){
			$args = func_get_args();
			$this->vars[$args[0]] = [$args[1], $args[2]];
		}
	}

	/**
	 * Fetch a query. This method returns several values, depending on the fetching parameter
	 * @access public
	 * @param int $fetch : type of fetch. 5 values available
	 *  sql::PARAM_FETCH         : correspond to the fetch of PDO. it's usefull for SELECT queries
	 *  sql::PARAM_FETCHCOLUMN   : correspond to the fetchcolumn of PDO. it's usefull for SELECT COUNT queries
	 *  sql::PARAM_FETCHINSERT   : useful for INSERT queries
	 *  sql::PARAM_FETCHUPDATE   : useful for UPDATE queries
	 *  sql::PARAM_FETCHDELETE   : useful for DELETE queries
	 *  default value : sql::PARAM_FETCH
	 * @throws \Exception
	 * @return mixed
	 * @since 3.0
	 * @package System\Sql
	 */

	public function fetch($fetch = self::PARAM_FETCH){
		try {
			/** @var \Core\PdoStatement $query */
			$query = $this->db->prepare($this->query);

			foreach($this->vars as $key => $value){
				if(preg_match('`:'.$key.'[\s|,|\)|\(%]`', $this->query.' ')){
					if(is_array($value)){
						$query->bindValue(":$key", $value[0], $value[1]);
					}
					else{
						switch(gettype($value)){
							case 'boolean' :
								$query->bindValue(":$key", $value, self::PARAM_BOOL);
							break;

							case 'integer' :
								$query->bindValue(":$key", $value, self::PARAM_INT);
							break;

							case 'double' :
								$query->bindValue(":$key", $value, self::PARAM_STR);
							break;

							case 'string' :
								$query->bindValue(":$key", $value, self::PARAM_STR);
							break;

							case 'NULL' :
								$query->bindValue(":$key", $value, self::PARAM_NULL);
							break;
						}
					}
				}
			}

			$query->execute();

			switch($fetch){
				case self::PARAM_FETCH : $this->data = $query->fetchAll(); break;
				case self::PARAM_FETCHCOLUMN : $this->data = $query->fetchColumn(); break;
				default: $this->data = true; break;
			}

			file_put_contents('app/log/sql.txt', $query->debugQuery()."\n\n", FILE_APPEND);

			$query->closeCursor();

			return $this->data;
		}
		catch (\PDOException $e) {
			throw new \Exception($e->getMessage().' / '.$e->getCode());
		}
	}
}