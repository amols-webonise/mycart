<?php
include_once('category.php');
include_once('product.php');
include_once('cart_validation.php');
include_once('http_response_message.php');

class cart {
	
	public $cartid = 0;
	public $c = null;
	private $v;

	function __construct($obj = null){
		if($obj instanceof Cart_Model){
			$this->c = $obj;
			$this->v = new cart_validation();
		}
	}

	function validateAdd(){
		if(($this->c->getAction() == 'add') || (($this->c->getAction() == 'update') && ($this->c->getName() != ''))){
			if(!$this->v->isValidName($this->c->getName())){
				throw new Exception(http_response_message::$response_message[1201]);
			}	
		}

		if($this->c->getQty() >= 0) {
			if(!$this->v->isValidQty($this->c->getQty())){
				throw new Exception(http_response_message::$response_message[1205]);
			}
		}

		if((int)$this->c->getProductId() >= 0 || (int)$this->c->getProductId() < 1) {
			if(!product::validProductId((int)$this->c->getProductId())){
				throw new Exception(http_response_message::$response_message[1005]);
			}
		}
	}

	function validate($case){
		switch($case){
			case 'category_id':
				if(true){
					throw new Exception(http_response_message::$response_message[1005]);
				}
			break;
			case 'product_id':
				if(!product::validProductId((int)$this->c->getProductId())){
					throw new Exception(http_response_message::$response_message[1005]);
				}
			break;
			case 'cart_id':
				if(!$this->isCart($this->c->getCartId())){
					throw new Exception(http_response_message::$response_message[1010]);
				}
			break;
		}
	}

	function calculateProduct($product){
		$db = connection::connect();
		$p['total'] = ($product['price'] * $product['qty']);
		$p['total_discount'] = (($p['total']/100) * $product['discount']);
		$p['total_with_discount'] = ($p['total'] - $p['total_discount']);
		$p['total_tax'] = (($p['total']/100) * $product['tax']);
		$p['total_with_tax'] = ($p['total_with_discount'] + $p['total_tax']);
		$p['grand_total'] = $p['total_with_tax'];
		return $p;
	}

	function createCartProduct($product){
		$db = connection::connect();
		try{
			$statement = $db->prepare("INSERT INTO cart_details(cart_id, product_id, qty, price, total, total_discount, total_with_discount, total_tax, total_with_tax) VALUES (:cart_id, :product_id, :qty, :price, :total, :total_discount, :total_with_discount, :total_tax, :total_with_tax)");
			$statement->execute(array(
			    ":cart_id" => $product['cart_id'],
			    ":product_id" => $product['product_id'],
			    ":qty" => $product['qty'],
			    ":price" => $product['price'],
			    ":total" => $product['total'],
			    ":total_discount" => $product['total_discount'],
			    ":total_with_discount" => $product['total_with_discount'],
			    ":total_tax" => $product['total_tax'],
			    ":total_with_tax" => $product['total_with_tax'],
			));
			$this->updateCart($product['cart_id']);
		} catch (PDOException $e) {
			http_response::generate('ERROR', NULL, 400, NULL, $e->getMessage());
		} catch (Exception $e) {
			http_response::generate('ERROR', NULL, 400, NULL, $e->getMessage());
		}
	}

	function isProductInCart($cart_id, $product_id){
		$db = connection::connect();
		try{
			$sql = 'select product_id from cart_details where cart_id=:cart_id and product_id=:id';
			$stmt = $db->prepare($sql);
			$stmt->bindValue(':cart_id', $cart_id);
			$stmt->bindValue(':id', $product_id);
			$stmt->execute();
			$array = $stmt->fetch(PDO::FETCH_ASSOC);
			if(is_array($array) && sizeof($array) > 0){
				return true;
			}else{
				return false;
			}

		} catch (PDOException $e) {
			http_response::generate('ERROR', NULL, 400, NULL, $e->getMessage());
		} catch (Exception $e) {
			http_response::generate('ERROR', NULL, 400, NULL, $e->getMessage());
		}
	}

	function updateCartProduct(){
		$db = connection::connect();
		try{
			if($this->isProductInCart($this->c->getCartId(), $this->c->getProductId())){
				$product = product::getProductById($this->c->getProductId());
				if((int)$product['id'] < 1){
					throw new Exception(http_response_message::$response_message[1005]);
				}
				$product['qty'] = $this->c->getQty();
				if((int)$product['qty'] > 0){
					$cart = $this->calculateProduct($product);
					$update_sql = 'update cart_details set qty=:qty, price=:price, total=:total, total_discount=:total_discount, total_with_discount=:total_with_discount, total_tax=:total_tax, total_with_tax=:total_with_tax where cart_id=:cart_id and product_id=:product_id';
					$stmt = $db->prepare($update_sql);
					//print_r($product);
					$stmt->bindParam(':qty', $product['qty']);
					$stmt->bindParam(':price', $product['price']);
					$stmt->bindParam(':total', $cart['total']);
					$stmt->bindParam(':total_discount', $cart['total_discount']);
					$stmt->bindParam(':total_with_discount', $cart['total_with_discount']);
					$stmt->bindParam(':total_tax', $cart['total_tax']);
					$stmt->bindParam(':total_with_tax', $cart['total_with_tax']);
					$stmt->bindParam(':cart_id', $this->c->getCartId());
					$stmt->bindParam(':product_id', $this->c->getProductId());
					$stmt->execute(); 
				} else {
					$sql_delete_from_cart = 'delete from cart_details where cart_id=:cart_id and product_id=:product_id';
					$stmt = $db->prepare($sql_delete_from_cart);
					$stmt->bindParam(':cart_id', $this->c->getCartId(), PDO::PARAM_INT);
					$stmt->bindParam(':product_id', $this->c->getProductId(), PDO::PARAM_INT);
					$stmt->execute();
				}
			}else{
				$this->createCart();
			}
			$this->updateCart($this->c->getCartId());
		} catch (PDOException $e) {
			http_response::generate('ERROR', NULL, 400, NULL, $e->getMessage());
		} catch (Exception $e) {
			http_response::generate('ERROR', NULL, 400, NULL, $e->getMessage());
		}
	}

	function updateCart($cart_id){
		$db = connection::connect();
		try{
			$sql = 'select * from cart_details where cart_id=:cart_id';
			$stmt = $db->prepare($sql);
			$stmt->bindValue(':cart_id', $cart_id);
			$stmt->execute();
			$data = $stmt->fetchAll(PDO::FETCH_ASSOC);
			foreach($data as $key => $val){
				$cart['total'] = ($cart['total'] + $val['total']);
				$cart['total_discount'] = ($cart['total_discount'] + $val['total_discount']);
				$cart['total_with_discount'] = ($cart['total_with_discount'] + $val['total_with_discount']);
				$cart['total_tax'] = ($cart['total_tax'] + $val['total_tax']);
				$cart['total_with_tax'] = ($cart['total_with_tax'] + $val['total_with_tax']);
				$cart['grand_total'] = ($cart['grand_total'] + $val['total_with_tax']);
			}
			if(strlen(trim($this->c->getName())) > 0){
				$update_param = "name=:name,";
			}
			
			$update_sql = "UPDATE cart SET ".$update_param." total=:total, total_discount=:total_discount, total_with_discount=:total_with_discount, total_tax=:total_tax, total_with_tax=:total_with_tax, grand_total=:grand_total where id=:id";
			$stmt = $db->prepare($update_sql);
			if(strlen(trim($this->c->getName())) > 0){
				$stmt->bindParam(':name', $this->c->getName());
			}
			$stmt->bindParam(':total', $cart['total']);
			$stmt->bindParam(':total_discount', $cart['total_discount']);
			$stmt->bindParam(':total_with_discount', $cart['total_with_discount']);
			$stmt->bindParam(':total_tax', $cart['total_tax']);
			$stmt->bindParam(':total_with_tax', $cart['total_with_tax']);
			$stmt->bindParam(':grand_total', $cart['grand_total']);
			$stmt->bindParam(':id', $cart_id);
			$stmt->execute(); 
		} catch (PDOException $e) {
			http_response::generate('ERROR', NULL, 400, NULL, $e->getMessage());
		} catch (Exception $e) {
			http_response::generate('ERROR', NULL, 400, NULL, $e->getMessage());
		}
	}

	function createCart(){
		$db = connection::connect();
		try{
			if($this->c instanceof Cart_Model){
				if((int)$this->c->getProductId() > 0){
					$product = product::getProductById($this->c->getProductId());
					$product['qty'] = $this->c->getQty();
					$cart = $this->calculateProduct($product);
					if((int)$this->c->getCartId() < 1){
						$statement = $db->prepare("INSERT INTO cart(name, total, total_discount, total_with_discount, total_tax, total_with_tax, grand_total) VALUES (:name, :total, :total_discount, :total_with_discount, :total_tax, :total_with_tax, :grand_total)");
						$statement->execute(array(
						    ":name" => $this->c->getName(),
						    ":total" => $cart['total'],
						    ":total_discount" => $cart['total_discount'],
						    ":total_with_discount" => $cart['total_with_discount'],
						    ":total_tax" => $cart['total_tax'],
						    ":total_with_tax" => $cart['total_with_tax'],
						    ":grand_total" => $cart['grand_total'],
						));
						$this->c->setCartId($db->lastInsertId());
					}
					if((int)$this->c->getCartId() > 0){
						$cart['cart_id'] = $this->c->getCartId();
						$cart['product_id'] = $this->c->getProductId();
						$cart['qty'] = $this->c->getQty();
						$cart['price'] = $product['price'];
						$this->createCartProduct($cart);
						return $this->c->getCartId();
					}
				}
			}
		} catch (PDOException $e) {
			http_response::generate('ERROR', NULL, 400, NULL, $e->getMessage());
		} catch (Exception $e) {
			http_response::generate('ERROR', NULL, 400, NULL, $e->getMessage());
		}
	}

	function isCart($cart_id){
		$db = connection::connect();
		try{
			$sql = 'select * from cart where id=:id';
			$stmt = $db->prepare($sql);
			$stmt->bindParam(':id', $cart_id);
			$stmt->execute();
			$array = $stmt->fetch(PDO::FETCH_ASSOC);
			if(is_array($array) && sizeof($array) > 0){
				return true;
			} else {
				return false;
			}
		} catch (PDOException $e) {
			http_response::generate('ERROR', NULL, 400, NULL, $e->getMessage());
		} catch (Exception $e) {
			http_response::generate('ERROR', NULL, 400, NULL, $e->getMessage());
		}
	}

	function add(){
		$db = connection::connect();
		try{

			$this->validateAdd();
			
			if($this->c instanceof Cart_Model){
				if((int)$this->c->getCartId() < 1){
					$cart_id = $this->createCart();
					if($cart_id > 0){
						http_response::generate('SUCCESS', 1009, 201, array('cartid'=>$cart_id), NULL);
					}else{
						http_response::generate('ERROR', 1010, 400, NULL, NULL);
					}
				} else{
					$this->cartid = $this->c->getCartId();
					if($this->isCart($this->c->getCartId())){
						$this->updateCartProduct();
						http_response::generate('SUCCESS', 1011, 200, NULL, NULL);
					} else {
						http_response::generate('ERROR', 1010, 400, NULL, NULL);
					}
					
				}
			}
		} catch (PDOException $e) {
			http_response::generate('ERROR', NULL, 400, NULL, $e->getMessage());
		} catch (Exception $e) {
			http_response::generate('ERROR', NULL, 400, NULL, $e->getMessage());
		}

	}

	function update(){
		$this->add();
	}

	function delete(){
		$db = connection::connect();
		try{
			
			$this->validate('cart_id');
			
			if((int)$this->c->getProductId() > 0){
				$sql_delete_cart_details = 'delete from cart_details where product_id=:pid';
				$stmt = $db->prepare($sql_delete_cart_details);
				$stmt->bindParam(':pid', $this->c->getProductId(), PDO::PARAM_INT);
				$stmt->execute();
				$this->updateCart($this->c->getCartId());
			} else {
				$sql_delete_cart_details = 'delete from cart_details where cart_id=:cart_id';
				$stmt = $db->prepare($sql_delete_cart_details);
				$stmt->bindParam(':cart_id', $this->c->getCartId(), PDO::PARAM_INT);
				$stmt->execute();


				$sql_delete_cart = 'delete from cart where id=:id';
				$stmt = $db->prepare($sql_delete_cart);
				$stmt->bindParam(':id', $this->c->getCartId(), PDO::PARAM_INT);
				$stmt->execute();
			}
			

			http_response::generate('SUCCESS', 1012, 200, NULL, NULL);
		} catch (PDOException $e) {
			http_response::generate('ERROR', NULL, 400, NULL, $e->getMessage());
		} catch (Exception $e) {
			http_response::generate('ERROR', NULL, 400, NULL, $e->getMessage());
		}
	}

	function getAll(){
		$db = connection::connect();
		try{
			$sql = 'select * from cart limit 0,100';
			$stmt = $db->prepare($sql);
			$stmt->bindParam(':id', $this->c->getCartId(), PDO::PARAM_INT);
			$stmt->execute();
			$array = $stmt->fetchAll(PDO::FETCH_ASSOC);
			if(is_array($array) && sizeof($array) > 0){
				foreach($array as $key => $value){
					$sql_items = 'select * from cart_details where cart_id=:id';
					$stmt_items = $db->prepare($sql_items);
					$stmt_items->bindParam(':id', $value['id'], PDO::PARAM_INT);
					$stmt_items->execute();
					$array_items = $stmt_items->fetchAll(PDO::FETCH_ASSOC);
					$array[$key]['cart_line_items'] = $array_items;
				}
				
				http_response::generate('SUCCESS', NULL, 200, $array, NULL);
			}else{
				http_response::generate('ERROR', NULL, 400, NULL, $e->getMessage());
			}
		} catch (PDOException $e) {
			http_response::generate('ERROR', NULL, 400, NULL, $e->getMessage());
		} catch (Exception $e) {
			http_response::generate('ERROR', NULL, 400, NULL, $e->getMessage());
		}
	}


	function getById(){
		$db = connection::connect();
		try{
			
			if((int)$this->c->getCartId() < 1){
				throw new Exception(http_response_message::$response_message[1010]);
			}
			
			$sql = 'select * from cart where id=:id';
			$stmt = $db->prepare($sql);
			$stmt->bindParam(':id', $this->c->getCartId(), PDO::PARAM_INT);
			$stmt->execute();
			$array = $stmt->fetchAll(PDO::FETCH_ASSOC);
			if(is_array($array) && sizeof($array) > 0){
				$data = $array;

				$sql_items = 'select * from cart_details where cart_id=:id';
				$stmt_items = $db->prepare($sql_items);
				$stmt_items->bindParam(':id', $this->c->getCartId(), PDO::PARAM_INT);
				$stmt_items->execute();
				$array_items = $stmt_items->fetchAll(PDO::FETCH_ASSOC);
				$data[0]['cart_line_items'] = $array_items;
				http_response::generate('SUCCESS', NULL, 200, $data, NULL);
			}else{
				http_response::generate('ERROR', NULL, 400, NULL, $e->getMessage());
			}
		} catch (PDOException $e) {
			http_response::generate('ERROR', NULL, 400, NULL, $e->getMessage());
		} catch (Exception $e) {
			http_response::generate('ERROR', NULL, 400, NULL, $e->getMessage());
		}
	}


	function carttotal(){
		$db = connection::connect();
		try{
			
			if((int)$this->c->getCartId() < 1){
				throw new Exception(http_response_message::$response_message[1010]);
			}
			
			$sql = 'select total from cart where id=:id';
			$stmt = $db->prepare($sql);
			$stmt->bindParam(':id', $this->c->getCartId(), PDO::PARAM_INT);
			$stmt->execute();
			$array = $stmt->fetchAll(PDO::FETCH_ASSOC);
			if(is_array($array) && sizeof($array) > 0){
				$data = $array;
				http_response::generate('SUCCESS', NULL, 200, array('cart_total'=>$data), NULL);
			}else{
				http_response::generate('ERROR', 1013, 200, NULL, NULL);
			}
		} catch (PDOException $e) {
			http_response::generate('ERROR', NULL, 400, NULL, $e->getMessage());
		} catch (Exception $e) {
			http_response::generate('ERROR', NULL, 400, NULL, $e->getMessage());
		}
	}

	function carttotaldiscount(){
		$db = connection::connect();
		try{
			
			if((int)$this->c->getCartId() < 1){
				throw new Exception(http_response_message::$response_message[1010]);
			}
			
			$sql = 'select total_discount from cart where id=:id';
			$stmt = $db->prepare($sql);
			$stmt->bindParam(':id', $this->c->getCartId(), PDO::PARAM_INT);
			$stmt->execute();
			$array = $stmt->fetchAll(PDO::FETCH_ASSOC);
			if(is_array($array) && sizeof($array) > 0){
				$data = $array;
				http_response::generate('SUCCESS', NULL, 200, array('total_discount'=>$data), NULL);
			}else{
				http_response::generate('ERROR', 1013, 200, NULL, NULL);
			}
		} catch (PDOException $e) {
			http_response::generate('ERROR', NULL, 400, NULL, $e->getMessage());
		} catch (Exception $e) {
			http_response::generate('ERROR', NULL, 400, NULL, $e->getMessage());
		}
	}


	function carttotaltax(){
		$db = connection::connect();
		try{
			
			if((int)$this->c->getCartId() < 1){
				throw new Exception(http_response_message::$response_message[1010]);
			}
			
			$sql = 'select total_tax from cart where id=:id';
			$stmt = $db->prepare($sql);
			$stmt->bindParam(':id', $this->c->getCartId(), PDO::PARAM_INT);
			$stmt->execute();
			$array = $stmt->fetchAll(PDO::FETCH_ASSOC);
			if(is_array($array) && sizeof($array) > 0){
				$data = $array;
				http_response::generate('SUCCESS', NULL, 200, array('total_tax'=>$data), NULL);
			}else{
				http_response::generate('ERROR', 1013, 200, NULL, NULL);
			}
		} catch (PDOException $e) {
			http_response::generate('ERROR', NULL, 400, NULL, $e->getMessage());
		} catch (Exception $e) {
			http_response::generate('ERROR', NULL, 400, NULL, $e->getMessage());
		}
	}

	
}