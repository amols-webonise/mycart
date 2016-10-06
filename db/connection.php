<?php
class connection {

	private static $con = NULL;

	function __construct($config = array()){

	}

	function connect($config = array()){
		try {
			if(is_array($config) && sizeof($config) > 0){
				if (NULL == self::$con) {
					$pdo = new PDO('mysql:host=localhost;dbname=mycart', 'root', 'root');
					$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
					self::$con = $pdo;
				}
				return self::$con;
			}
		} catch (PDOException $e) {
		    print "Error!: " . $e->getMessage() . "<br/>";
		    die();
		}
	}
} 