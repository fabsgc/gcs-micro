<?php

session_start();

/**
 * @file : index.php
 * @author : Fabien Beaujean
 * @description : entry point of the application. Each page request must go trough this file.
 * This file create a new instance of the engine.
 */

require_once('vendor/Autoload.php');

$engine = new \Core\Engine();
$engine->init()->run();