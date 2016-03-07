<?php
namespace Origin\Utilities\Bucket;

trait CheckPopulated {
	public function CheckPopulated(array $required_things = array()) {
		if(empty($required_things)){
			$required_things = self::$required_things;
		}
		
		if(empty($required_things)){
			throw new Exception('Required population array is empty. Can not check to see if bucket is full.');
		}
		
		foreach($required_things as $thing){
			if(!isset($this->things[$thing]) || ($this->things[$thing] === null)){
				return false;
			}
		}
		
		return true;
	}
}
