<?php
class cart_validation { 

	public function isValidName($name){
		if(strlen(trim((string)$name)) > 0){
			return true;
		} else {
			return false;
		}
	}

	public function isValidQty($qty){
		if((int)$qty > 0){
			return true;
		} else {
			return false;
		}
	}

	public function isValidProductId($product_id){
		if((int)$product_id < 1){
			return false;
		} else {
			return true;
		}
	}

}