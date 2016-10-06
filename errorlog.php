<?php
class errorlog {

	function __construct($config = array()){
	
	}

	function save($error_message){
		global $db;
		$statement = $db->prepare("INSERT INTO errorlog(error_message, added_on_date) VALUES (:error_message, :added_on_date)");
		$statement->execute(array(
		    "error_message" => $error_message,
		    "added_on_date" => date('Y-m-d h:i:s', time())
		));
	}
} 