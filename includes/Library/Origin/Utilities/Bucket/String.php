<?php
namespace Origin\Utilities\Bucket;

trait String {
	public function String($value = null){
		if(($value !== null) && (!is_string($value))){
			throw new Exception(sprintf('Invalid value specified for type %s.', __FUNCTION__));
		}
		return $this->Bucket(null, $value);
	}
}
