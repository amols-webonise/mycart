<?php
class database {

	function __construct($config = array()){
		try {
			if(is_array($config) && sizeof($config) > 0){
				$pdo = new PDO('mysql:host=localhost;dbname=mycart', 'root', 'root');
				return $pdo;
			}
		} catch (PDOException $e) {
		    print "Error!: " . $e->getMessage() . "<br/>";
		    die();
		}
	}
} 