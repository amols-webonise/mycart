<?php
include_once('./category.php');
include_once('./product.php');
class cart {
	public $cartid = 0;
	//name, total, total_discount, total_with_discount, total_tax, total_with_tax, grand_total
	function __construct(){

	}

	function calculateProduct($product){
		global $db;
		$p['total'] = ($product['price'] * $product['qty']);
		$p['total_discount'] = (($p['total']/100) * $product['discount']);
		$p['total_with_discount'] = ($p['total'] - $p['total_discount']);
		$p['total_tax'] = (($p['total']/100) * $product['tax']);
		$p['total_with_tax'] = ($p['total_with_discount'] + $p['total_tax']);
		$p['grand_total'] = $p['total_with_tax'];
		return $p;
	}

	function createCartProduct($product){
		global $db;
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
			$response = array('status'=>'ERROR', 'message'=>$e->getMessage());
			echo json_encode($response);
			errorlog::save('Caught exception: '.  $e->getMessage());
		} catch (Exception $e) {
			$response = array('status'=>'ERROR', 'message'=>$e->getMessage());
			echo json_encode($response);
			errorlog::save('Caught exception: '.  $e->getMessage());
		}
	}

	function isProductInCart($cart_id, $product_id){
		global $db;
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
			$response = array('status'=>'ERROR', 'message'=>$e->getMessage());
			echo json_encode($response);
			errorlog::save('Caught exception: '.  $e->getMessage());
		} catch (Exception $e) {
			$response = array('status'=>'ERROR', 'message'=>$e->getMessage());
			echo json_encode($response);
			errorlog::save('Caught exception: '.  $e->getMessage());
		}
	}

	function updateCartProduct($param){
		global $db;
		try{
			if($this->isProductInCart($param['cartid'], $param['product_id'])){
				$product = product::getProductById($param['product_id']);
				$product['qty'] = $param['qty'];
				if((int)$product['qty'] > 0){
					$cart = $this->calculateProduct($product);
					$update_sql = 'update cart_details set qty=:qty, price=:price, total=:total, total_discount=:total_discount, total_with_discount=:total_with_discount, total_tax=:total_tax, total_with_tax=:total_with_tax where cart_id=:cart_id and product_id=:product_id';
					$stmt = $db->prepare($update_sql);
					$stmt->bindParam(':qty', $product['qty']);
					$stmt->bindParam(':price', $product['price']);
					$stmt->bindParam(':total', $cart['total']);
					$stmt->bindParam(':total_discount', $cart['total_discount']);
					$stmt->bindParam(':total_with_discount', $cart['total_with_discount']);
					$stmt->bindParam(':total_tax', $cart['total_tax']);
					$stmt->bindParam(':total_with_tax', $cart['total_with_tax']);
					$stmt->bindParam(':cart_id', $param['cartid']);
					$stmt->bindParam(':product_id', $param['product_id']);
					$stmt->execute(); 
				} else {
					$sql_delete_from_cart = 'delete from cart_details where cart_id=:cart_id and product_id=:product_id';
					$stmt = $db->prepare($sql_delete_from_cart);
					$stmt->bindParam(':cart_id', $param['cartid'], PDO::PARAM_INT);
					$stmt->bindParam(':product_id', $param['product_id'], PDO::PARAM_INT);
					$stmt->execute();
				}
			}else{
				$this->createCart($param);
			}
			$this->updateCart($param['cartid']);
		} catch (PDOException $e) {
			$response = array('status'=>'ERROR', 'message'=>$e->getMessage());
			echo json_encode($response);
			errorlog::save('Caught exception: '.  $e->getMessage());
		} catch (Exception $e) {
			$response = array('status'=>'ERROR', 'message'=>$e->getMessage());
			echo json_encode($response);
			errorlog::save('Caught exception: '.  $e->getMessage());
		}
	}

	function updateCart($cart_id){
		global $db;
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
			$update_sql = "UPDATE cart SET total=:total, total_discount=:total_discount, total_with_discount=:total_with_discount, total_tax=:total_tax, total_with_tax=:total_with_tax, grand_total=:grand_total where id=:id";
			$stmt = $db->prepare($update_sql);
			$stmt->bindParam(':total', $cart['total']);
			$stmt->bindParam(':total_discount', $cart['total_discount']);
			$stmt->bindParam(':total_with_discount', $cart['total_with_discount']);
			$stmt->bindParam(':total_tax', $cart['total_tax']);
			$stmt->bindParam(':total_with_tax', $cart['total_with_tax']);
			$stmt->bindParam(':grand_total', $cart['grand_total']);
			$stmt->bindParam(':id', $cart_id);
			$stmt->execute(); 
		} catch (PDOException $e) {
			$response = array('status'=>'ERROR', 'message'=>$e->getMessage());
			echo json_encode($response);
			errorlog::save('Caught exception: '.  $e->getMessage());
		} catch (Exception $e) {
			$response = array('status'=>'ERROR', 'message'=>$e->getMessage());
			echo json_encode($response);
			errorlog::save('Caught exception: '.  $e->getMessage());
		}
	}

	function createCart($param){
		global $db;
		try{
			if(is_array($param) && sizeof($param) > 0){
				if((int)$param['product_id'] > 0){
					
					$product = product::getProductById($param['product_id']);
					$product['qty'] = $param['qty'];
					$cart = $this->calculateProduct($product);
					if((int)$param['cartid'] < 1){
						$statement = $db->prepare("INSERT INTO cart(name, total, total_discount, total_with_discount, total_tax, total_with_tax, grand_total) VALUES (:name, :total, :total_discount, :total_with_discount, :total_tax, :total_with_tax, :grand_total)");
						$statement->execute(array(
						    ":name" => $param['name'],
						    ":total" => $cart['total'],
						    ":total_discount" => $cart['total_discount'],
						    ":total_with_discount" => $cart['total_with_discount'],
						    ":total_tax" => $cart['total_tax'],
						    ":total_with_tax" => $cart['total_with_tax'],
						    ":grand_total" => $cart['grand_total'],
						));
						$this->cartid = $db->lastInsertId();
					}else{
						$this->cartid = $param['cartid'];
					}
					if((int)$this->cartid > 0){
						$cart['cart_id'] = $this->cartid;
						$cart['product_id'] = $param['product_id'];
						$cart['qty'] = $param['qty'];
						$cart['price'] = $product['price'];
						$this->createCartProduct($cart);
						return $this->cartid;
					}
				}
			}
		} catch (PDOException $e) {
			$response = array('status'=>'ERROR', 'message'=>$e->getMessage());
			echo json_encode($response);
			errorlog::save('Caught exception: '.  $e->getMessage());
		} catch (Exception $e) {
			$response = array('status'=>'ERROR', 'message'=>$e->getMessage());
			echo json_encode($response);
			errorlog::save('Caught exception: '.  $e->getMessage());
		}
	}

	function isCart($cart_id){
		global $db;
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
			$response = array('status'=>'ERROR', 'message'=>$e->getMessage());
			echo json_encode($response);
			errorlog::save('Caught exception: '.  $e->getMessage());
		} catch (Exception $e) {
			$response = array('status'=>'ERROR', 'message'=>$e->getMessage());
			echo json_encode($response);
			errorlog::save('Caught exception: '.  $e->getMessage());
		}
	}

	function add($param = array(), $method){
		global $db;
		try{
			if($method != 'POST'){
				throw new Exception('Error: Invalid request type.');
			}
			if((int)$param['product_id'] < 1){
				throw new Exception('Error: Invalid product.');
			}
			
			if(is_array($param) && sizeof($param) > 0){
				if((int)$param['cartid'] < 1){
					$cart_id = $this->createCart($param);
					if($cart_id > 0){
						$response = array('status'=>'SUCCESS', 'message'=>'Your cart successfully created!', 'cartid'=>$cart_id);
						echo json_encode($response);
					}else{
						$response = array('status'=>'ERROR', 'message'=>'Error in your cart');
						echo json_encode($response);
					}
				} else{
					$this->cartid = $param['cartid'];
					if($this->isCart($param['cartid'])){
						$this->updateCartProduct($param);
						$response = array('status'=>'SUCCESS', 'message'=>'Your cart is updated successfully!');
						echo json_encode($response);
					} else {
						$response = array('status'=>'ERROR', 'message'=>'Invalid cart id');
						echo json_encode($response);
					}
					
				}
			}
		} catch (PDOException $e) {
			$response = array('status'=>'ERROR', 'message'=>$e->getMessage());
			echo json_encode($response);
			errorlog::save('Caught exception: '.  $e->getMessage());
		} catch (Exception $e) {
			$response = array('status'=>'ERROR', 'message'=>$e->getMessage());
			echo json_encode($response);
			errorlog::save('Caught exception: '.  $e->getMessage());
		}

	}

	function delete($param, $method){
		global $db;
		try{
			if($method != 'DELETE'){
				throw new Exception('Error: Invalid request type.');
			}
			if((int)$param['cartid'] < 1){
				throw new Exception('Error: Invalid cart id.');
			}
			
			$sql_delete_cart_details = 'delete from cart_details where cart_id=:cart_id';
			$stmt = $db->prepare($sql_delete_cart_details);
			$stmt->bindParam(':cart_id', $param['cartid'], PDO::PARAM_INT);
			$stmt->execute();


			$sql_delete_cart = 'delete from cart where id=:id';
			$stmt = $db->prepare($sql_delete_cart);
			$stmt->bindParam(':id', $param['cartid'], PDO::PARAM_INT);
			$stmt->execute();
			$response = array('status'=>'SUCCESS', 'message'=>'Successfully deleted!');
			echo json_encode($response);

		} catch (PDOException $e) {
			$response = array('status'=>'ERROR', 'message'=>$e->getMessage());
			echo json_encode($response);
			errorlog::save('Caught exception: '.  $e->getMessage());
		} catch (Exception $e) {
			$response = array('status'=>'ERROR', 'message'=>$e->getMessage());
			echo json_encode($response);
			errorlog::save('Caught exception: '.  $e->getMessage());
		}
	}

	function showcart($param, $method){
		global $db;
		try{
			if($method != 'GET'){
				throw new Exception('Error: Invalid request type.');
			}
			if((int)$param['cartid'] < 1){
				throw new Exception('Error: Invalid cart id.');
			}
			
			$sql = 'select * from cart where id=:id';
			$stmt = $db->prepare($sql);
			$stmt->bindParam(':id', $param['cartid'], PDO::PARAM_INT);
			$stmt->execute();
			$array = $stmt->fetchAll(PDO::FETCH_ASSOC);
			if(is_array($array) && sizeof($array) > 0){
				$data = $array;
				$response = array('status'=>'SUCCESS', 'message'=>'Your data list!', 'data'=>$data);
				echo json_encode($response);
			}else{
				$response = array('status'=>'ERROR', 'message'=>'List is empty');
				echo json_encode($response);
			}
		} catch (PDOException $e) {
			$response = array('status'=>'ERROR', 'message'=>$e->getMessage());
			echo json_encode($response);
			errorlog::save('Caught exception: '.  $e->getMessage());
		} catch (Exception $e) {
			$response = array('status'=>'ERROR', 'message'=>$e->getMessage());
			echo json_encode($response);
			errorlog::save('Caught exception: '.  $e->getMessage());
		}
	}


	function carttotal($param, $method){
		global $db;
		try{
			if($method != 'GET'){
				throw new Exception('Error: Invalid request type.');
			}
			if((int)$param['cartid'] < 1){
				throw new Exception('Error: Invalid cart id.');
			}
			
			$sql = 'select total from cart where id=:id';
			$stmt = $db->prepare($sql);
			$stmt->bindParam(':id', $param['cartid'], PDO::PARAM_INT);
			$stmt->execute();
			$array = $stmt->fetchAll(PDO::FETCH_ASSOC);
			if(is_array($array) && sizeof($array) > 0){
				$data = $array;
				$response = array('status'=>'SUCCESS', 'message'=>'Cart total is:', 'data'=>$data);
				echo json_encode($response);
			}else{
				$response = array('status'=>'ERROR', 'message'=>'No records found');
				echo json_encode($response);
			}
		} catch (PDOException $e) {
			$response = array('status'=>'ERROR', 'message'=>$e->getMessage());
			echo json_encode($response);
			errorlog::save('Caught exception: '.  $e->getMessage());
		} catch (Exception $e) {
			$response = array('status'=>'ERROR', 'message'=>$e->getMessage());
			echo json_encode($response);
			errorlog::save('Caught exception: '.  $e->getMessage());
		}
	}

	function carttotaldiscount($param, $method){
		global $db;
		try{
			if($method != 'GET'){
				throw new Exception('Error: Invalid request type.');
			}
			if((int)$param['cartid'] < 1){
				throw new Exception('Error: Invalid cart id.');
			}
			
			$sql = 'select total_discount from cart where id=:id';
			$stmt = $db->prepare($sql);
			$stmt->bindParam(':id', $param['cartid'], PDO::PARAM_INT);
			$stmt->execute();
			$array = $stmt->fetchAll(PDO::FETCH_ASSOC);
			if(is_array($array) && sizeof($array) > 0){
				$data = $array;
				$response = array('status'=>'SUCCESS', 'message'=>'Cart total discount is:', 'data'=>$data);
				echo json_encode($response);
			}else{
				$response = array('status'=>'ERROR', 'message'=>'No records found');
				echo json_encode($response);
			}
		} catch (PDOException $e) {
			$response = array('status'=>'ERROR', 'message'=>$e->getMessage());
			echo json_encode($response);
			errorlog::save('Caught exception: '.  $e->getMessage());
		} catch (Exception $e) {
			$response = array('status'=>'ERROR', 'message'=>$e->getMessage());
			echo json_encode($response);
			errorlog::save('Caught exception: '.  $e->getMessage());
		}
	}


	function carttotaltax($param, $method){
		global $db;
		try{
			if($method != 'GET'){
				throw new Exception('Error: Invalid request type.');
			}
			if((int)$param['cartid'] < 1){
				throw new Exception('Error: Invalid cart id.');
			}
			
			$sql = 'select total_tax from cart where id=:id';
			$stmt = $db->prepare($sql);
			$stmt->bindParam(':id', $param['cartid'], PDO::PARAM_INT);
			$stmt->execute();
			$array = $stmt->fetchAll(PDO::FETCH_ASSOC);
			if(is_array($array) && sizeof($array) > 0){
				$data = $array;
				$response = array('status'=>'SUCCESS', 'message'=>'Cart total tax is:', 'data'=>$data);
				echo json_encode($response);
			}else{
				$response = array('status'=>'ERROR', 'message'=>'No records found');
				echo json_encode($response);
			}
		} catch (PDOException $e) {
			$response = array('status'=>'ERROR', 'message'=>$e->getMessage());
			echo json_encode($response);
			errorlog::save('Caught exception: '.  $e->getMessage());
		} catch (Exception $e) {
			$response = array('status'=>'ERROR', 'message'=>$e->getMessage());
			echo json_encode($response);
			errorlog::save('Caught exception: '.  $e->getMessage());
		}
	}

	
}