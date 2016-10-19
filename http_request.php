<?php
include_once('http_response_message.php');
class http_request
{
	private $param = array();
	private $method = null;
	private $get = array();
	private $post = array();
	private $server = array();

    function __construct($method = NULL, $get = array(), $post = array(), $server = array()){
    	if($method != NULL){
    		$this->method = $method;
    	}
    	if(is_array($get) && sizeof($get) > 0){
    		$this->get = $get;
    	}
    	if(is_array($post) && sizeof($post) > 0){
	    	$this->post = $post;
	    }
	    if(is_array($server) && sizeof($server) > 0){
	    	$this->server = $server;
	    }
    }

    function process(){
		$this->getallheaders();
		$this->getallRequestParams();
		
		try{
			if(!$this->isValidApiKey()){
				throw new Exception(http_response_message::$response_message[2000]);
			}
			
			switch($this->param['module']){
				case 'category':
					$this->callCategory();
				break;
				case 'product':
					$this->callProduct();
				break;
				case 'cart':
					$this->callCart();
				break;
			}
		} catch (Exception $e) {
			http_response::generate('ERROR', NULL, 401, NULL, $e->getMessage());
		}
    }

    function getallheaders() 
    { 
		foreach ($this->server as $name => $value) // this is for nginx
		//foreach(getallheaders() as $name => $value) // this is for apache
		{ 
			if (substr($name, 0, 5) == 'HTTP_') 
			{ 
				$key = str_replace(' ', '-', strtolower(str_replace('_', ' ', substr($name, 5))));
				$this->param[$key] = $value; 
			} else {
				$this->param[$name] = $value; 
			}
		} 
    }

    function getallRequestParams() 
    {
		foreach ($this->get as $name => $value) 
		{ 
			$this->param[$name] = $value;
		} 
		foreach ($this->post as $name => $value) 
		{ 
			$this->param[$name] = $value;
		} 
    }

    function getallPatchParams(){
    	$data = file_get_contents('php://input');
    	if(strlen(trim($data)) > 0){
	    	parse_str($data, $output);
	    	foreach($output as $name => $value){
	    		if(strlen($name) > 0 && strlen($value) > 0){
	    			$this->param[$name] = $value;
	    		}
	    	}
	    }
    }

    function getallDeleteParams(){
    	$this->getallPatchParams();
    }

    function isValidApiKey(){
		if($this->param['apikey'] == config::$apikey){
			return true;
		} else {
			return false;
		}
	}

	function callCategory(){
		include_once('category.php');
		include_once('Category_Model.php');
		try{
			switch($this->method){
				case 'POST':
					if($this->param['action'] != 'add'){
						throw new Exception(http_response_message::$response_message[2001]);
					}
				break;
				case 'PATCH':
					$this->getallPatchParams();
					$this->param['action'] = 'update';
					if($this->param['action'] != 'update'){
						throw new Exception(http_response_message::$response_message[2001]);
					}

					if((int)$this->param['id'] < 1){
						throw new Exception(http_response_message::$response_message[1001]);
					}
				break;
				case 'GET':
					if($this->param['action'] == 'update' || $this->param['action'] == 'add' || $this->param['action'] == 'delete'){
						throw new Exception(http_response_message::$response_message[2001]);
					}
					if($this->param['action'] == ''){
						$this->param['action'] = 'getAll';
					}
					if((int)$this->param['id'] > 0){
						$this->param['action'] = 'getById';
					}
				break;
				case 'DELETE':
					$this->param['action'] = 'delete';
					$this->getallDeleteParams();
					if($this->param['action'] != 'delete'){
						throw new Exception(http_response_message::$response_message[2001]);
					}
					if((int)$this->param['id'] < 1){
						throw new Exception(http_response_message::$response_message[1001]);
					}
				break;
			}
			$c = new Category_Model();
			$c->setId($this->param['id']);
			$c->setName($this->param['name']);
			$c->setDescription($this->param['description']);
			$c->setTax($this->param['tax']);
			$c->setAction($this->param['action']);
			$category = new category($c);

			$category->{$this->param['action']}();
		} catch (Exception $e) {
			http_response::generate('ERROR', NULL, 400, NULL, $e->getMessage());
		}
	}

	function callProduct(){
		include_once('product.php');
		include_once('Product_Model.php');
		try{
			switch($this->method){
				case 'POST':
					if($this->param['action'] != 'add'){
						throw new Exception(http_response_message::$response_message[2001]);
					}
				break;
				case 'PATCH':
					$this->getallPatchParams();
					$this->param['action'] = 'update';
					if($this->param['action'] != 'update'){
						throw new Exception(http_response_message::$response_message[2001]);
					}

					if((int)$this->param['id'] < 1){
						throw new Exception(http_response_message::$response_message[1005]);
					}
				break;
				case 'GET':
					if($this->param['action'] == 'update' || $this->param['action'] == 'add' || $this->param['action'] == 'delete'){
						throw new Exception(http_response_message::$response_message[2001]);
					}
					if($this->param['action'] == ''){
						$this->param['action'] = 'getAll';
					}
					if((int)$this->param['id'] > 0){
						$this->param['action'] = 'getById';
					}
				break;
				case 'DELETE':
					$this->param['action'] = 'delete';
					$this->getallDeleteParams();
					if($this->param['action'] != 'delete'){
						throw new Exception(http_response_message::$response_message[2001]);
					}
					if((int)$this->param['id'] < 1){
						throw new Exception(http_response_message::$response_message[1005]);
					}
				break;
			}
			$p = new Product_Model();
			$p->setId($this->param['id']);
			$p->setName($this->param['name']);
			$p->setDescription($this->param['description']);
			$p->setPrice($this->param['price']);
			$p->setDiscount($this->param['discount']);
			$p->setCategoryId($this->param['category_id']);
			$p->setAction($this->param['action']);
			$product = new product($p);
			$product->{$this->param['action']}();
		} catch (Exception $e) {
			http_response::generate('ERROR', NULL, 400, NULL, $e->getMessage());
		}
	}

	function callCart(){
		include_once('cart.php');
		include_once('Cart_Model.php');

		try{
			switch($this->method){
				case 'POST':
					if($this->param['action'] != 'add'){
						throw new Exception(http_response_message::$response_message[2001]);
					}
				break;
				case 'PATCH':
					$this->getallPatchParams();
					$this->param['action'] = 'update';
					if($this->param['action'] != 'update'){
						throw new Exception(http_response_message::$response_message[2001]);
					}

					if((int)$this->param['cartid'] < 1){
						throw new Exception(http_response_message::$response_message[1010]);
					}
				break;
				case 'GET':
					if($this->param['action'] == 'update' || $this->param['action'] == 'add' || $this->param['action'] == 'delete'){
						throw new Exception(http_response_message::$response_message[2001]);
					}
					if($this->param['action'] == ''){
						$this->param['action'] = 'getAll';
					}
					if((int)$this->param['id'] > 0){
						$this->param['action'] = 'getById';
					}
				break;
				case 'DELETE':
					$this->param['action'] = 'delete';
					$this->getallDeleteParams();
					if($this->param['action'] != 'delete'){
						throw new Exception(http_response_message::$response_message[2001]);
					}
				break;
			}
			
			$c = new Cart_Model();
			$c->setCartId((int)$this->param['cartid']);
			$c->setName((string)$this->param['name']);
			$c->setProductId((int)$this->param['product_id']);
			$c->setQty((float)$this->param['qty']);
			$c->setAction($this->param['action']);
			$cart = new cart($c);
			$cart->{$this->param['action']}();
		} catch (Exception $e) {
			http_response::generate('ERROR', NULL, 400, NULL, $e->getMessage());
		}
	}

	function getParamList(){
		return $this->param;
	}

}
