<?php
class category_validation { 

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

	public function isValidTax($tax){
		if(is_float($tax) && (int)$tax != 0){
			if((int)$tax >= 100){
				return false;
			} else {
				if((int)$tax < 1){
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