<?php
namespace Origin\Utilities\Bucket;

trait Number {
	public function Number($value = null){
		if(($value !== null) && (!is_numeric($value))){
			throw new Exception(sprint_f('Invalid value specified for type %s.', __FUNCTION__));
		}
		return $this->Bucket(null, $value);
	}
}
