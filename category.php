<?php
class category {

	public $c = NULL;

	function __construct(){

	}

	function add($param = array(), $method){
		global $db;
		try{
			if($method != 'POST'){
				throw new Exception('Error: Invalid request type.');
			}
			if(is_array($param) && sizeof($param) > 0){
				$statement = $db->prepare("INSERT INTO category(name, description, tax) VALUES (:name, :description, :tax)");
				$statement->execute(array(
				    ":name" => $this->c->getName(),
				    ":description" => $this->c->getDescription(),
				    ":tax" => $this->c->getTax(),
				));
				$response = array('status'=>'SUCCESS', 'message'=>'Successfully saved!', 'id'=>$db->lastInsertId());
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
			if((int)$this->c->id < 1){
				throw new Exception('Error: Invalid category id.');
			}
			
			if(is_array($param) && sizeof($param) > 0){
				
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
			if((int)$this->c->id < 1){
				throw new Exception('Error: Invalid category id.');
			}
			if(is_array($param) && sizeof($param) > 0){
				$sql = 'delete from category where id=:id';
				$stmt = $db->prepare($sql);
				$stmt->bindParam(':id', $this->c->id, PDO::PARAM_INT);
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
			$sql = 'select * from category order by name asc';
			$stmt = $db->prepare($sql);
			$stmt->execute();
			$array = $stmt->fetchAll(PDO::FETCH_ASSOC);

			/*
			foreach($array as $k => $v){
				$cat = new Category_Model();
				$cat->setId($v['id']);
				$cat->setName($v['name']);
				$cat->setDescription($v['description']);
				$cat->setTax($v['tax']);
				$data[$k] = $cat;
				echo '<pre>'; print_r($cat);
			}
			*/
			
			if(is_array($array) && sizeof($array) > 0){
				$data = $array;
				$response = array('status'=>'SUCCESS', 'message'=>'Your data list!', 'data'=>$data);
				echo json_encode($response);
			}
			
			$response = array('status'=>'SUCCESS', 'message'=>'Your data list!', 'data'=>$data);
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
}