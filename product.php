<?php
include_once('./category.php');
include_once('./http_response_message.php');
class product {
	
	public $p = null;

	function __construct($obj = null){
		if($obj instanceof Product_Model){
			$this->p = $obj;
		}
	}


	function add(){
		$db = connection::connect();

		try{
			
			if((int)$this->p->getCategoryId() > 0){
				if(!category::validCategoryId($this->p->getCategoryId())){
					throw new Exception(http_response_message::$response_message[1001]);
				}
			}
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
			
			if((int)$this->p->getId() < 1){
				throw new Exception(http_response_message::$response_message[1005]);
			} else {
				if(!$this->validProductId($this->p->getId())){
					throw new Exception(http_response_message::$response_message[1005]);
				}
			}

			if((int)$this->p->getCategoryId() > 0){
				if(!category::validCategoryId($this->p->getCategoryId())){
					throw new Exception(http_response_message::$response_message[1001]);
				}
			}
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
				if(strlen($this->p->getPrice()) > 0){
					$sql .= $deli."price = :price  ";
					$deli = ',';
				}
				if(strlen($this->p->getDiscount()) > 0){
					$sql .= $deli."discount = :discount  ";
				}
				if(strlen($this->p->getCategoryId()) > 0){
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
				
				if(strlen($this->p->getPrice()) > 0){
					$stmt->bindParam(':price', $this->p->getPrice(), PDO::PARAM_STR);
				}
				
				if(strlen($this->p->getDiscount()) > 0){
					$stmt->bindParam(':discount', $this->p->getDiscount(), PDO::PARAM_STR);   
				}
				if(strlen($this->p->getCategoryId()) > 0){
					$stmt->bindParam(':category_id', $this->p->getCategoryId(), PDO::PARAM_INT);   
				}
				if(strlen($this->p->getId()) > 0){
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
			
			if(!$this->validProductId($this->p->getId())){
				throw new Exception(http_response_message::$response_message[1005]);
			}
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
			if(!$this->validProductId($this->p->getId())){
				throw new Exception(http_response_message::$response_message[1005]);
			}
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