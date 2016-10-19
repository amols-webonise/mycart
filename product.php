<?php
include_once('category.php');
include_once('http_response_message.php');
include_once('product_validation.php');
class product {
	
	public $p = null;
	private $v;

	function __construct($obj = null){
		if($obj instanceof Product_Model){
			$this->p = $obj;
			$this->v = new product_validation();
		}
	}

	function validateProduct(){
		if($this->p->getAction() != 'add'){
			if(!product::validProductId($this->p->getId())){
				throw new Exception(http_response_message::$response_message[1005]);
			}
		}
		
		if($this->p->getAction() == 'add'){
			if(!$this->v->isValidName($this->p->getName())){
				throw new Exception(http_response_message::$response_message[1201]);
			}
			if(!$this->v->isValidDescription($this->p->getDescription())){
				throw new Exception(http_response_message::$response_message[1202]);
			}
			if(!$this->v->isValidPrice($this->p->getPrice())){
				throw new Exception(http_response_message::$response_message[1203]);
			}
			if(!$this->v->isValidDiscount($this->p->getDiscount())){
				throw new Exception(http_response_message::$response_message[1204]);
			}
			if(!category::validCategoryId($this->p->getCategoryId())){
				throw new Exception(http_response_message::$response_message[1001]);
			}
		} else {

			if($this->p->getAction() == 'update'){

				if($this->p->getName() || $this->p->getDescription() || $this->p->getPrice() || $this->p->getDiscount()) {
				
					if($this->p->getName() != ''){
						if(!$this->v->isValidName($this->p->getName())){
							throw new Exception(http_response_message::$response_message[1201]);
						}
					}

					if($this->p->getDescription() != ''){				
						if(!$this->v->isValidDescription($this->p->getDescription())){
							throw new Exception(http_response_message::$response_message[1202]);
						}
					}

					if($this->p->getPrice() != '' || $this->p->getPrice() > 0 || $this->p->getPrice() < 0){
						if(!$this->v->isValidPrice($this->p->getPrice())){
							throw new Exception(http_response_message::$response_message[1203]);
						}
					}

					if($this->p->getDiscount() != '' || $this->p->getDiscount() > 0 || $this->p->getDiscount() < 0){
						if(!$this->v->isValidDiscount($this->p->getDiscount())){
							throw new Exception(http_response_message::$response_message[1204]);
						}
					}

					if($this->p->getCategoryId() > 0) {
						if(!category::validCategoryId($this->p->getCategoryId())){
							throw new Exception(http_response_message::$response_message[1001]);
						}
					}
				} else {
					throw new Exception(http_response_message::$response_message[1206]);
				}
			}
		}
	}

	function add(){
		$db = connection::connect();

		try{
			
			$this->validateProduct();

			if($this->p instanceof Product_Model){
				$statement = $db->prepare("INSERT INTO product(name, description, price, discount, category_id) VALUES (:name, :description, :price, :discount, :category_id)");
				$statement->execute(array(
				    ":name" => $this->p->getName(),
				    ":description" => $this->p->getDescription(),
				    ":price" => $this->p->getPrice(),
				    ":discount" => $this->p->getDiscount(),
				    ":category_id" => $this->p->getCategoryId(),
				));
				$productId = $db->lastInsertId();
				http_response::generate('SUCCESS', 1006, 201, array('id'=>$db->lastInsertId()), NULL);
			}
		} catch (PDOException $e) {
			http_response::generate('ERROR', NULL, 400, NULL, $e->getMessage());
		} catch (Exception $e) {
			http_response::generate('ERROR', NULL, 400, NULL, $e->getMessage());
		}

	}

	function update(){
		$db = connection::connect();
		try{
			$this->validateProduct();

			if($this->p instanceof Product_Model){
				
				$sql = "";
				$deli = '';
				$sql .= "UPDATE product SET ";
				if(strlen($this->p->getName()) > 0){
					$sql .= "name = :name ";
					$deli = ',';
				}
				if(strlen($this->p->getDescription()) > 0){
					$sql .= $deli."description = :description ";
					$deli = ',';
				}
				if($this->p->getPrice() > 0){
					$sql .= $deli."price = :price  ";
					$deli = ',';
				}

				if($this->p->getDiscount() > 0){
					$sql .= $deli."discount = :discount  ";
				}
				if($this->p->getCategoryId() > 0){
					$sql .= $deli."category_id = :category_id  ";
				}
				$sql .= "WHERE id = :id";
				$stmt = $db->prepare($sql);                                  
				
				if(strlen($this->p->getName()) > 0){
					$stmt->bindParam(':name', $this->p->getName(), PDO::PARAM_STR);       
				}
				if(strlen($this->p->getDescription()) > 0){
					$stmt->bindParam(':description', $this->p->getDescription(), PDO::PARAM_STR);    
				}
				
				if((int)$this->p->getPrice() > 0){
					$stmt->bindParam(':price', $this->p->getPrice(), PDO::PARAM_STR);
				}
				
				if((int)$this->p->getDiscount() > 0){
					$stmt->bindParam(':discount', $this->p->getDiscount(), PDO::PARAM_STR);   
				}
				if((int)$this->p->getCategoryId() > 0){
					$stmt->bindParam(':category_id', $this->p->getCategoryId(), PDO::PARAM_INT);   
				}
				if((int)$this->p->getId() > 0){
					$stmt->bindParam(':id', $this->p->getId(), PDO::PARAM_INT);   
				}
				$stmt->execute(); 
				http_response::generate('SUCCESS', 1006, 200, NULL, NULL);

			}
		} catch (PDOException $e) {
			http_response::generate('ERROR', NULL, 400, NULL, $e->getMessage());
		} catch (Exception $e) {
			http_response::generate('ERROR', NULL, 400, NULL, $e->getMessage());
		}

	}

	function delete(){
		$db = connection::connect();
		try{
			$this->validateProduct();
			if($this->p instanceof Product_Model){
				$sql = 'delete from product where id=:id';
				$stmt = $db->prepare($sql);
				$stmt->bindParam(':id', $this->p->getId(), PDO::PARAM_INT);
				$stmt->execute();
				http_response::generate('SUCCESS', 1007, 200, NULL, NULL);
			}
		} catch (PDOException $e) {
			http_response::generate('ERROR', NULL, 400, NULL, $e->getMessage());
		} catch (Exception $e) {
			http_response::generate('ERROR', NULL, 400, NULL, $e->getMessage());
		}
	}

	function getAll(){
		$db = connection::connect();
		try{
			
			$sql = 'select * from product order by name asc';
			$stmt = $db->prepare($sql);
			$stmt->execute();
			$array = $stmt->fetchAll(PDO::FETCH_ASSOC);
			if(is_array($array) && sizeof($array) > 0){
				$data = $array;
				http_response::generate('SUCCESS', 1008, 200, array('list'=>$data), NULL);
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
			$this->validateProduct();
			$sql = 'select * from product where id=:id';
			$stmt = $db->prepare($sql);
			$stmt->bindValue(':id', $this->p->getId());
			$stmt->execute();
			$array = $stmt->fetch(PDO::FETCH_ASSOC);

			if(is_array($array) && sizeof($array) > 0){
				$data = $array;
				http_response::generate('SUCCESS', 1008, 200, array('list'=>$data), NULL);
			}
			http_response::generate('SUCCESS', 1008, 200, array('list'=>$data), NULL);
		} catch (PDOException $e) {
			http_response::generate('ERROR', NULL, 400, NULL, $e->getMessage());
		} catch (Exception $e) {
			http_response::generate('ERROR', NULL, 400, NULL, $e->getMessage());
		}
	}

	function getProductById($product_id){
		
		$db = connection::connect();
		$sql = 'select p.id, p.name, p.price, p.discount, c.id as category_id, c.tax from product as p left join category as c on (p.category_id=c.id) where p.id=:id';
		$stmt = $db->prepare($sql);
		$stmt->bindValue(':id', $product_id);
		$stmt->execute();
		$array = $stmt->fetch(PDO::FETCH_ASSOC);
		return $array;
	}

	function validProductId($product_id){
		$db = connection::connect();
		$sql = 'select p.id, p.name, p.price, p.discount, c.id as category_id, c.tax from product as p left join category as c on (p.category_id=c.id) where p.id=:id';
		$stmt = $db->prepare($sql);
		$stmt->bindValue(':id', $product_id);
		$stmt->execute();
		$arr = $stmt->fetch(PDO::FETCH_ASSOC);
		if((int)$arr['id'] > 0){
			return true;
		} else {
			return false;
		}
	}
}