<?php
error_reporting(E_ERROR | E_WARNING | E_PARSE);


defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/'));

set_include_path(implode(PATH_SEPARATOR, array(
	realpath(APPLICATION_PATH . '/'),
	get_include_path(),
)));

include_once('db/config.php');
include_once('db/connection.php');
include_once('bootstrap.php');
include_once('errorlog.php');
include_once('statuscode.php');
include_once('http_request.php');
include_once('http_response.php');