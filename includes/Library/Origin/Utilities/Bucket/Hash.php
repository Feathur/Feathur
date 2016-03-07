<?php
namespace Origin\Utilities\Bucket;

trait Hash {
	public function Hash($value = null){
		if(($value !== null) && (!is_array($value))){
			throw new Exception(sprint_f('Invalid value specified for type %s.', __FUNCTION__));
		}
		return $this->Bucket(null, $value);
	}
}
