<?php
class connection {

	private static $con = NULL;

	function __construct($config = array()){

	}

	function connect($config = array()){
		try {
				if (NULL == self::$con) {
					$pdo = new PDO('mysql:host='.config::$db_host.';dbname='.config::$db_name.'', config::$db_user, config::$db_pass);
					$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
					self::$con = $pdo;
				}
				return self::$con;
		} catch (PDOException $e) {
		    print "Error!: " . $e->getMessage() . "<br/>";
		    die();
		}
	}
} 