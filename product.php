<?php
class product {
	//name, description, price, discount
	function __construct(){

	}

	function validateCategoryId($categoryId){
		global $db;
		$sql = 'select id from category where id=:id';
		$stmt = $db->prepare($sql);
		$stmt->bindValue(':id', $categoryId);
		$stmt->execute();
		$array = $stmt->fetch(PDO::FETCH_ASSOC);
		if(is_array($array) && sizeof($array) > 0){
			return true;
		} else {
			return false;
		}
	}

	function add($param = array(), $method){
		global $db;
		try{
			if($method != 'POST'){
				throw new Exception('Error: Invalid request type.');
			}
			if((int)$param['category_id'] > 0){
				if(!$this->validateCategoryId($param['category_id'])){
					throw new Exception('Error: Invalid category id.');
				}
			}
			if(is_array($param) && sizeof($param) > 0){
				$statement = $db->prepare("INSERT INTO product(name, description, price, discount, category_id) VALUES (:name, :description, :price, :discount, :category_id)");
				$statement->execute(array(
				    ":name" => $param['name'],
				    ":description" => $param['description'],
				    ":price" => $param['price'],
				    ":discount" => $param['discount'],
				    ":category_id" => $param['category_id'],
				));
				$productId = $db->lastInsertId();
				$response = array('status'=>'SUCCESS', 'message'=>'Successfully saved!', 'id'=>$productId);
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

	function update($param = array(), $method){
		global $db;
		try{
			if($method != 'POST'){
				throw new Exception('Error: Invalid request type.');
			}
			if((int)$param['id'] < 1){
				throw new Exception('Error: Invalid id.');
			}
			if(is_array($param) && sizeof($param) > 0){
				
				$sql = "";
				$deli = '';
				$sql .= "UPDATE product SET ";
				if(strlen($param['name']) > 0){
					$sql .= "name = :name ";
					$deli = ',';
				}
				if(strlen($param['description']) > 0){
					$sql .= $deli."description = :description ";
					$deli = ',';
				}
				if(strlen($param['price']) > 0){
					$sql .= $deli."price = :price  ";
					$deli = ',';
				}
				if(strlen($param['discount']) > 0){
					$sql .= $deli."discount = :discount  ";
				}
				$sql .= "WHERE id = :id";
				$stmt = $db->prepare($sql);                                  
				
				if(strlen($param['name']) > 0){
					$stmt->bindParam(':name', $param['name'], PDO::PARAM_STR);       
				}
				if(strlen($param['description']) > 0){
					$stmt->bindParam(':description', $param['description'], PDO::PARAM_STR);    
				}
				
				if(strlen($param['price']) > 0){
					$stmt->bindParam(':price', $param['price'], PDO::PARAM_STR);
				}
				
				if(strlen($param['discount']) > 0){
					$stmt->bindParam(':discount', $param['discount'], PDO::PARAM_INT);   
				}
				if(strlen($param['id']) > 0){
					$stmt->bindParam(':id', $param['id'], PDO::PARAM_INT);   
				}
				$stmt->execute(); 
				$response = array('status'=>'SUCCESS', 'message'=>'Successfully saved!');
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

	function delete($param = array(), $method){
		global $db;
		try{
			if($method != 'DELETE'){
				throw new Exception('Error: Invalid request type.');
			}
			if(is_array($param) && sizeof($param) > 0){
				$sql = 'delete from product where id=:id';
				$stmt = $db->prepare($sql);
				$stmt->bindParam(':id', $param['id'], PDO::PARAM_INT);
				$stmt->execute();
				$response = array('status'=>'SUCCESS', 'message'=>'Successfully deleted!');
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

	function getAll($param = array(), $method){
		global $db;
		try{
			if($method != 'GET'){
				throw new Exception('Error: Invalid request type.');
			}
			$sql = 'select * from product order by name asc';
			$stmt = $db->prepare($sql);
			$stmt->execute();
			$array = $stmt->fetchAll(PDO::FETCH_ASSOC);
			if(is_array($array) && sizeof($array) > 0){
				$data = $array;
				$response = array('status'=>'SUCCESS', 'message'=>'Your data list!', 'data'=>$data);
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

	function getProductById($product_id){
		global $db;
		$sql = 'select p.id, p.name, p.price, p.discount, c.id as category_id, c.tax from product as p join category as c on (p.category_id=c.id) where p.id=:id';
		$stmt = $db->prepare($sql);
		$stmt->bindValue(':id', $product_id);
		$stmt->execute();
		$array = $stmt->fetch(PDO::FETCH_ASSOC);
		return $array;
	}
}