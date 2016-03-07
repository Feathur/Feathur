<?php
namespace Origin\Autoload {

	use \Origin\Autoload\AutoloadException;

	class Autoload {
		/*
		* Static paths to include files (usually files which are not namespaced or have specific namespaces that would not normally load correctly).
		* Ideally eventually this will be moved to some sort of a config file.
		* If you have another autoloader file I'd personally suggest putting it on line 8 of incluides/loader.php
		*/
		public static $static_paths = array();

		public static $autoload_paths = array(
			'includes/Library/%s.php',
			'includes/Library/%s.class.php',
			'%s.php',
			'%s.class.php',
		);
		/*
		*
		*/
		public static function Load($class){
			if(self::StaticPath($class)){
				return true;
			}

			$path = explode(constant('NAMESPACE'), $class);
			return self::LoadClassOrDie(implode(DIRECTORY_SEPARATOR, $path));
		}

		/*
		* @params - $class
		* Attempts to look for a static path match. If found it will load that class.
		* @return boolean (Successfully loaded a class).
		*/
		private static function StaticPath($class){
			// Direct comparison.
			if(in_array($class, self::$static_paths)){
				require_once(self::$static_paths[$class]);
				return true;
			}

			// If we're this far in the direct comparison has failed. So we'll look for "off by one".
			foreach(self::$static_paths as $namespace => $path){
				if(stripos($namespace, $class) !== false){
					if(self::Difference((strlen($namespace) - strlen($class)), -1, 1)){
						require_once(self::$static_paths[$class]);
						return true;
					}
				}
			}

			return false;
		}

		/*
		* @params - $val, $min, $max
		* Determines if the value is greater than min and less than max.
		* @return boolean ($val is between $min and $max)
		*/
		private static function Difference($val, $min, $max) {
			return ($val >= $min && $val <= $max);
		}

		/*
		* @params - Path to the file without the file's extension.
		* Attempts to find a file matching the path passed to it based on possible autoload locations ($autoload_paths).
		* @return boolean (Successfully found and loaded file.)
		*
		* NOTE: Will throw AutoloadException if the file does not exist or the path is invalid.
		*/
		private static function LoadClassOrDie($path){
			foreach(self::$autoload_paths as $location){
				//die(print_r(sprintf($location, $path), true));
				if(file_exists(sprintf($location, $path))){
					require_once(sprintf($location, $path));
					return true;
				}
			}

			throw new AutoloadException("Invalid path specified unable to load {$path}");
		}
	}

	class AutoloadException extends \Exception {}
}