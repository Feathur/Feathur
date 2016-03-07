<?php
namespace Origin\Utilities\Bucket;

trait Date {
	public function Date(\DateTime $value = null){
		return $this->Bucket(null, $value);
	}
}