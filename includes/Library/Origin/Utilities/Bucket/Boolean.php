<?php
namespace Origin\Utilities\Bucket;

trait Boolean {
	public function Boolean($value = null){
		if(($value !== null) && (!is_bool($value))){
			throw new Exception(sprint_f('Invalid value specified for type %s', __FUNCTION__));
		}
		
		return $this->Bucket(null, $value);
	}
}
