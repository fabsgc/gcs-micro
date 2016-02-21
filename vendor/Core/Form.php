<?php

/**
 * @file : vendor/Core/Form.php
 * @author : Fabien Beaujean
 * @description : Form validator
 */

namespace Core;

/**
 * Class Form
 * @package Core
 */

class Form{

	/**
	 * @var string $name
	 */

	private $name = 'form';

	/**
	 * @var string $method
	 */

	private $method = 'post';

	/**
	 * all the elements to be validated
	 * @var \Core\Field[]
	 */

	private $fields = [];

	/**
	 * all errors after validation
	 * @var array
	 */

	private $errors = [];

	const METHOD_POST = 'post';
	const METHOD_GET  =  'get';

	/**
	 * Form constructor.
	 * @param string $name
	 * @param string $method
	 */

	public function __construct($name = 'form', $method = 'post'){
		$this->name = $name;
		$this->method = $method;
		return $this;
	}

	/**
	 * add new text field
	 * @param string $name
	 * @return FormText
	 */

	public function text($name){
		$text = new FormText($name, $this->method);
		array_push($this->fields, $text);
		return $text;
	}

	/**
	 * add new file field
	 * @param string $name
	 * @return FormText
	 */

	public function file($name){
		$file = new FormFile($name, $this->method);
		array_push($this->fields, $file);
		return $file;
	}

	public function sent(){
		switch($this->method){
			case Form::METHOD_GET:
				if(isset($_GET[$this->name]))
					return true;
			break;

			case Form::METHOD_POST:
				if(isset($_POST[$this->name]))
					return true;
			break;
		}

		return false;
	}

	/**
	 * validation of the form
	 * @return bool
	 */

	public function check(){
		foreach($this->fields as $field){
			if(!$field->check()){
				$this->errors[$field->getName()] = $field->errors();
			}
		}

		if(count($this->errors) > 0)
			return false;

		return true;
	}

	/**
	 * return all the errors after validation
	 * @return mixed
	 */

	public function errors(){
		return $this->errors;
	}
}

/**
 * Class FormField
 * @package Core
 */

class Field{
	/**
	 * @var string
	 */

	protected $name;

	/**
	 * @var string $method
	 */

	protected $method = Form::METHOD_POST;

	/**
	 * list of constraints
	 * @var array : list of all constraints
	 */

	protected $constraints = [];

	/**
	 * all errors from the field after validation
	 * @var array : list of errors after validation
	 */

	protected $errors = [];

	/**
	 * form data ($_GET, $_POST, $_FILES)
	 * @var array
	 */

	protected $data = [];

	const EQUAL         =  0;
	const DIFFERENT     =  1;

	const LENGTHMIN     =  2;
	const LENGTHMAX     =  3;

	const REGEX         =  4;
	const MAIL          =  5;
	const SQL           =  6;

	const ACCEPT        =  7;
	const EXTENSION     =  8;
	const SIZEMAX       =  9;

	const EXIST         = 10;

	const CLOSURE       = 11;

	const ALPHADASH     = 12;

	/**
	 * FormText constructor.
	 * @param $name
	 * @param $method
	 */

	public function __construct($name, $method){
		$this->name = $name;
		$this->method = $method;

		switch($method){
			case Form::METHOD_GET:
				$this->data = isset($_GET[$name]) ? $_GET[$name] : '';
			break;

			case Form::METHOD_POST:
				$this->data = isset($_POST[$name]) ? $_POST[$name] : '';
			break;
		}
	}

	/**
	 * add one constraint on the current field
	 * @param $type
	 * @param $constraint
	 * @param $error
	 * @return \Core\Field
	 */

	final public function add($type, $constraint, $error){
		array_push(
			$this->constraints, [
				'type' => $type,
				'constraint' => $constraint,
				'error' => $error
			]
		);

		return $this;
	}

	/**
	 * validate the field
	 * @return bool
	 */

	public function check(){
		return true;
	}

	/**
	 * return all the errors after validation
	 * @return mixed
	 */

	final public function errors(){
		return $this->errors;
	}

	/**
	 * get field name
	 * @return string
	 */

	final public function getName(){
		return $this->name;
	}
}

/**
 * Class FormText
 * @package Core
 */

class FormText extends Field{

	/**
	 * validate the field
	 * @return bool
	 */

	public function check(){
		foreach($this->constraints as $constraint){
			switch($constraint['type']){
				case Field::EQUAL:
					if($this->data != $constraint['constraint']){
						array_push($this->errors, $constraint['error']);
					}
				break;

				case Field::DIFFERENT:
					if($this->data == $constraint['constraint']){
						array_push($this->errors, $constraint['error']);
					}
				break;

				case Field::LENGTHMIN:
					if(strlen($this->data) < $constraint['constraint']){
						array_push($this->errors, $constraint['error']);
					}
				break;

				case Field::LENGTHMAX:
					if(strlen($this->data) > $constraint['constraint']){
						array_push($this->errors, $constraint['error']);
					}
				break;

				case Field::REGEX:
					if(!preg_match('#'.$constraint['constraint'].'#isU', $this->data)){
						array_push($this->errors, $constraint['error']);
					}
				break;

				case Field::MAIL:
					if(!filter_var($this->data, FILTER_VALIDATE_EMAIL)){
						array_push($this->errors, $constraint['error']);
					}
				break;

				case Field::SQL:
					$query = preg_replace('#\[(.*)\]\[(.*)\]\[(.*)\]\[(.*)\]\[(.*)\]#isU', '$1', $constraint['constraint']);
					$tokens = explode(',', preg_replace('#\[(.*)\]\[(.*)\]\[(.*)\]\[(.*)\]\[(.*)\]#isU', '$2', $constraint['constraint']));
					$vars = explode(',', preg_replace('#\[(.*)\]\[(.*)\]\[(.*)\]\[(.*)\]\[(.*)\]#isU', '$3', $constraint['constraint']));
					$condition = preg_replace('#\[(.*)\]\[(.*)\]\[(.*)\]\[(.*)\]\[(.*)\]#isU', '$4', $constraint['constraint']);
					$conditionValue = preg_replace('#\[(.*)\]\[(.*)\]\[(.*)\]\[(.*)\]\[(.*)\]#isU', '$5', $constraint['constraint']);

					$sql = new Sql();
					$sql->query($query);
					$sql->vars('value', $this->data);

					foreach($vars as $key => $var){
						$sql->vars($tokens[$key], $var);
					}

					$data = $sql->fetch(Sql::PARAM_FETCHCOLUMN);

					$querySuccess = true;

					switch($condition){
						case '==':
							if($data != $conditionValue)
								$querySuccess = false;
						break;

						case '!=':
							if($data == $conditionValue)
								$querySuccess = false;
						break;

						case '>':
							if($data <= $conditionValue)
								$querySuccess = false;
						break;

						case '<':
							if($data >= $conditionValue)
								$querySuccess = false;
						break;
					}

					if(!$querySuccess){
						array_push($this->errors, $constraint['error']);
					}
				break;

				case Field::EXIST:
					switch($this->method){
						case Form::METHOD_GET:
							if(empty($_GET[$this->name]))
								array_push($this->errors, $constraint['error']);
						break;

						case Form::METHOD_POST:
							if(empty($_POST[$this->name]))
								array_push($this->errors, $constraint['error']);
						break;
					}
				break;

				case Field::CLOSURE:
					if($constraint['constraint']() == false){
						array_push($this->errors, $constraint['error']);
					}
				break;

				case Field::ALPHADASH:
					if(!preg_match('#^([a-zA-Z_-]+)$#', $this->data)){
						array_push($this->errors, $constraint['error']);
					}
				break;
			}
		}

		if(count($this->errors) > 0)
			return false;

		return true;
	}
}

/**
 * Class FormFile
 * @package Core
 */

class FormFile extends Field{

	/**
	 * FormText constructor.
	 * @param $name
	 * @param $method
	 */

	public function __construct($name, $method){
		parent::__construct($name, $method);
		$this->data = $_FILES;
	}

	/**
	 * validate the field
	 * @return bool
	 */

	public function check(){
		foreach($this->constraints as $constraint){
			switch($constraint['type']){
				case Field::ACCEPT:

				break;

				case Field::EXTENSION:

				break;

				case Field::SIZEMAX:

				break;
			}
		}

		return true;
	}
}