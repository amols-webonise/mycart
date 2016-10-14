<?php
class errorlog {

	function __construct(){
	
	}

	function save($error_message){
		$db = connection::connect();
		$param_list = 'NULL';
		try{
			$statement = $db->prepare("INSERT INTO errorlog(error_message, param_list, added_on_date) VALUES (:error_message, :param_list, :added_on_date)");
			$_p_list = http_request::getParamList();
			if(is_array($_p_list) && sizeof($_p_list) > 0){
				$param_list = json_encode($_p_list);	
			}
			$statement->execute(array(
			    "error_message" => $error_message,
			    "param_list" => $param_list,
			    "added_on_date" => date('Y-m-d h:i:s', time())
			));
		} catch (PDOException $e) {
			echo 'Caught exception: '.  $e->getMessage();
		}
	}
} 