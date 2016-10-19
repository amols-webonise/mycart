<?php
class product_validation { 

	public function isValidName($name){
		if(strlen(trim((string)$name)) > 0){
			return true;
		} else {
			return false;
		}
	}

	public function isValidDescription($description){
		if(strlen(trim((string)$description)) > 0){
			return true;
		} else {
			return false;
		}
	}

	public function isValidPrice($price){
		if(is_float($price) && (int)$price != 0){
			if((int)$price < 1){
				return false;
			} else {
				return true;
			}
		} else {
			return false;
		}
	}

	public function isValidDiscount($discount){
		if(is_float($discount) && (int)$discount != 0){
			if((int)$discount >= 100){
				return false;
			} else {
				if((int)$discount < 1) {
					return false;
				} else {
					return true;
				}
			}
		} else {
			return false;
		}
	}

}