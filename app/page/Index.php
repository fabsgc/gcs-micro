<?php

use \Core\Page;
use \Core\View;

class Index extends Page{
	public function home(){
		return (new View('index/home.php'))->render();
	}
}