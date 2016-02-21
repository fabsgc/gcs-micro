<?php

/**
 * @file : vendor/Core/Firewall
 * @author : Fabien Beaujean
 * @description : Firewall manager
 * For each url from the config file we can specify if the user must be logged in to access to page (logged parameter)
 * and which roles he must have
 */

namespace Core;

/**
 * Class Firewall
 * @package Core
 */

class Firewall{

	/**
	 * @var array
	 */

	private $config = [];

	/**
	 * @var \Core\Request
	 */

	private $request;

	/**
	 * @var array
	 */

	private $csrf = [];

	/**
	 * @var boolean
	 */

	private $logged;

	/**
	 * @var string
	 */

	private $role;

	/**
	 * constructor
	 */

	public function __construct(){
		$this->config = Config::instance()->get()['firewall'];
		$this->request = Request::instance();
		$this->setFirewall();
	}

	/**
	 * set firewall configuration
	 * @access public
	 * @return void
	 */

	private function setFirewall(){
		$csrf = $this->config['csrf']['name'];
		$logged = $this->config['logged'];
		$role = $this->config['role'];

		$this->csrf['POST'] = $this->firewallConfig($_POST, $csrf);
		$this->csrf['GET'] = $this->firewallConfig($_GET, $csrf);
		$this->csrf['SESSION'] = $this->firewallConfig($_SESSION, $csrf);
		$this->logged = $this->firewallConfig($_SESSION, $logged);
		$this->role = $this->firewallConfig($_SESSION, $role);
	}

	/**
	 * get token, logged and role value from environment
	 * @access public
	 * @param array $in : array which contain the value
	 * @param string $key
	 * @return mixed
	 * @since 3.0
	 * @package System\Security
	 */

	protected function firewallConfig($in, $key){
		if(isset($in[$key]))
			return $in[$key];

		return false;
	}

	/**
	 * check authorization to allow to a visitor to load a page
	 * @access public
	 * @return bool
	 */

	public function check(){
		if($this->_checkCsrf() == true) {
			switch ($this->request->logged) {
				case '*' :
					return true;
				break;

				case 'true' :
					if($this->_checkLogged()) {
						if ($this->_checkRole()) {
							return true;
						}
						else {
							echo (new View('core/error.php'))->assign('error', 'Access forbidden')->render();
							return false;
						}
					}
					else{
						$url = Url::get(Config::instance()->get()['firewall']['redirect']['login']);
						header('Location:'.$url);
						return false;
					}
				break;

				case 'false' :
					if($this->_checkLogged() == false){
						return true;
					}
					else{
						$url = Url::get(Config::instance()->get()['firewall']['redirect']['default']);
						header('Location:'.$url);
						return false;
					}
				break;
			}
		}
		else{
			echo (new View('core/error.php'))->assign('error', 'Access forbidden')->render();
			return false;
		}

		return true;
	}

	/**
	 * check csrf
	 * @access protected
	 * @return boolean
	 * @since 3.0
	 * @package System\Security
	 */

	protected function _checkCsrf(){
		if($this->config['csrf']['enabled'] == true && $this->request->logged == true){
			if($this->csrf['SESSION'] != false && ($this->csrf['GET'] != false || $this->csrf['POST'] != false)){
				if($this->csrf['POST'] == $this->csrf['SESSION'] || $this->csrf['GET'] == $this->csrf['SESSION'])
					return true;
				else
					return false;
			}
			else{
				return true;
			}
		}
		else{
			return true;
		}
	}

	/**
	 * check logged
	 * @access protected
	 * @return boolean
	 * @since 3.0
	 * @package System\Security
	 */

	protected function _checkLogged(){
		return $this->logged;
	}

	/**
	 * check role
	 * @access protected
	 * @return boolean
	 * @since 3.0
	 * @package System\Security
	 */

	protected function _checkRole(){
		if(in_array($this->role, array_map('trim', explode(',', $this->request->access))) || $this->request->access == '*')
			return true;
		else
			return false;
	}
}