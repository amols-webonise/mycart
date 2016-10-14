<?php
include_once('./statuscode.php');
include_once('./http_response_message.php');
class category { 

	public $c = NULL;

	function __construct($obj = null){
		if($obj instanceof Category_Model){
			$this->c = $obj;
		}
	}

	function add(){
		$db = connection::connect();
		try{
			if((int)$this->c->id > 0){
				throw new Exception(http_response_message::$response_message[1004]);
			}
			if($this->c  instanceof Category_Model){
				$statement = $db->prepare("INSERT INTO category(name, description, tax) VALUES (:name, :description, :tax)");
				$statement->execute(array(
				    ":name" => $this->c->getName(),
				    ":description" => $this->c->getDescription(),
				    ":tax" => $this->c->getTax(),
				));
				http_response::generate('SUCCESS', 1000, 201, array('id'=>$db->lastInsertId()), NULL);
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
			
			if((int)$this->c->id < 1){
				throw new Exception(http_response_message::$response_message[1001]);
			}else{
				if(!category::validCategoryId($this->c->id)){
					throw new Exception(http_response_message::$response_message[1001]);
				}
			}
			
			if($this->c  instanceof Category_Model){
				
				$sql = "";
				$deli = '';
				$sql .= "UPDATE category SET ";
				if(strlen($this->c->name) > 0){
					$sql .= "name = :name ";
					$deli = ',';
				}
				if(strlen($this->c->description) > 0){
					$sql .= $deli."description = :description ";
					$deli = ',';
				}
				if(strlen($this->c->tax) > 0){
					$sql .= $deli."tax = :tax  ";
				}
				$sql .= "WHERE id = :id";
				$stmt = $db->prepare($sql);                                  
				
				if(strlen($this->c->name) > 0){
					$stmt->bindParam(':name', $this->c->name, PDO::PARAM_STR);       
				}
				if(strlen($this->c->description) > 0){
					$stmt->bindParam(':description', $this->c->description, PDO::PARAM_STR);    
				}
				
				if(strlen($this->c->tax) > 0){
					$stmt->bindParam(':tax', $this->c->tax, PDO::PARAM_STR);
				}
				
				if(strlen($this->c->id) > 0){
					$stmt->bindParam(':id', $this->c->id, PDO::PARAM_INT);   
				}
				$stmt->execute(); 
				http_response::generate('SUCCESS', 1000, 200, NULL, NULL);
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
			
			if((int)$this->c->id < 1){
				throw new Exception(http_response_message::$response_message[1001]);
			}else{
				if(!category::validCategoryId($this->c->id)){
					throw new Exception(http_response_message::$response_message[1001]);
				}
			}
			if($this->c  instanceof Category_Model){
				$sql = 'delete from category where id=:id';
				$stmt = $db->prepare($sql);
				$stmt->bindParam(':id', $this->c->id, PDO::PARAM_INT);
				$stmt->execute();
				http_response::generate('SUCCESS', 1002, 200, NULL, NULL);
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
			
			$sql = 'select * from category order by name asc';
			$stmt = $db->prepare($sql);
			$stmt->execute();
			$array = $stmt->fetchAll(PDO::FETCH_ASSOC);

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

	function getById(){
		$db = connection::connect();
		try{
			
			if(!category::validCategoryId($this->c->getId())){
				throw new Exception(http_response_message::$response_message[1001]);
			}

			$sql = 'select * from category where id=:id';
			$stmt = $db->prepare($sql);
			$stmt->bindValue(':id', $this->c->getId());
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

	function validCategoryId($categoryId){
		$db = connection::connect();
		$sql = 'select id from category where id=:id';
		$stmt = $db->prepare($sql);
		$stmt->bindValue(':id', $categoryId);
		$stmt->execute();
		$array = $stmt->fetch(PDO::FETCH_ASSOC);
		
		if((int)$array['id'] > 0){
			return true;
		} else {
			return false;
		}
	}
}