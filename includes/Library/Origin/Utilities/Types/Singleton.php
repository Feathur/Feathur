<?php
namespace Origin\Utilities\Types;

abstract class Singleton {
	protected function __construct() {}
	
	final public static function Get($version = null) {
		static $instances = array();
		$calledClass = get_called_class();
		if($version !== null){
			if(!isset($instances[$calledClass.$version])){
				$instances[$calledClass.$version] = new $calledClass($version);
			}
			
			return $instances[$calledClass.$version];
		} 
		
		if(!isset($instances[$calledClass])) {
			$instances[$calledClass] = new $calledClass();
		}
		return $instances[$calledClass];
	}
    final private function __clone() {}
}