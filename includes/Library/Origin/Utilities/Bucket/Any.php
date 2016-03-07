<?php
namespace Origin\Utilities\Bucket;

trait Any {
	public function Any($value = null){
		return $this->Bucket(null, $value);
	}
}
