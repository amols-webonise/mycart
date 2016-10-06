<?php
include_once('./db/config.php');
include_once('./db/connection.php');
include_once('./errorlog.php');

$db = connection::connect($config);

if (!function_exists('getallheaders')) 
{ 
    function getallheaders() 
    { 
		$headers = ''; 
		foreach ($_SERVER as $name => $value) 
		{ 
			if (substr($name, 0, 5) == 'HTTP_') 
			{ 
				$headers[str_replace(' ', '-', strtolower(str_replace('_', ' ', substr($name, 5))))] = $value; 
			} 
		} 
		return $headers; 
    } 
} 

if (!function_exists('getallRequestParams')) 
{ 
    function getallRequestParams() 
    { 
    	global $headers;
		$headers = ''; 
		foreach ($_REQUEST as $name => $value) 
		{ 
			$headers[$name] = $value;
		} 
		return $headers; 
    } 
} 

$method = $_SERVER['REQUEST_METHOD'];
$param1 = getallheaders();
$param2 = getallRequestParams();

if(is_array($param1) && sizeof($param1) > 0 && is_array($param2) && sizeof($param2) > 0){
	$headers = array_merge($param1, $param2);
} elseif(is_array($param1) && sizeof($param1) > 0) {
	$headers = $param1;
}else{
	$headers = $param2;
}


switch($headers['module']){
	case 'category':
		callCategory($headers, $method);
	break;
	case 'product':
		callProduct($headers, $method);
	break;
	case 'cart':
		callCart($headers, $method);
	break;
}

function callCategory($headers, $method){
	$action = $headers['action'];
	include_once('./category.php');
	include_once('./Category_Model.php');
	$c = new Category_Model();
	$c->setId($headers['id']);
	$c->setName($headers['name']);
	$c->setDescription($headers['description']);
	$c->setTax($headers['tax']);
	$category = new category();
	$category->c = $c;
	$category->$action($headers, $method);
}

function callProduct($headers, $method){
	$action = $headers['action'];
	include_once('./product.php');
	$product = new product();
	$product->$action($headers, $method);
}

function callCart($headers, $method){
	$action = $headers['action'];
	include_once('./cart.php');
	$product = new cart();
	$product->$action($headers, $method);
}