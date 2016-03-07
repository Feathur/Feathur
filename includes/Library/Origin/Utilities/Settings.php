<?php
namespace Origin\Utilities;

use \Origin\Utilities\Types\Exception;
use \Origin\Utilities\Types\Hash;

/*
* Settings are awesome, storing them and retrieving them aren't usually so awesome.
* Settings can be retrieved from json files via a simple call now: Settings::Get('settings')->Value(['site', 'title']);
* For more information see the documentation on Ori which I will likely have written before anyone reads this comment.
*/
class Settings extends \Origin\Utilities\Types\Singleton {
	// Even Settings need settings :)
	const CONFIG_BASE_PATH = 'hidden/config/';
	const CONFIG_FILE_EXTENSION = '.json';
	
	/*
	* Retrieves a single string, integer or float from the requested settings file.
	* Should the setting requested be an array (Hash) this will throw an error.
	*/
	public function Value(array $child = null){
		$values = $this->FindValues($child);
		if($values instanceof Hash){
			throw new Exception('Expected setting to be a string or number, setting is an array.');
		}
		
		return $values;
	}
	
	/*
	* Retrieves an array (Hash) from the requested settings file.
	* Should the setting requested not be an array (Hash) this will throw an error.
	*/
	public function Values(array $child = null){
		$values = $this->FindValues($child);
		if(!($values instanceof Hash)){
			throw new Exception('Expected settings to be an array, got: '.gettype($values));
		}
		
		return $values;
	}
	
	/*
	* Configuration and private functions.
	*/
	private $version;
	private $values;
	
	/*
	* Retrieves and converts a json array of settings into a Hash object.
	* Said object is stored for the duration of the execution for any subsequent calls to this class for efficiency reasons.
	*/
	public function __construct($version = null){
		$this->version = ($version !== null) ? $version : 'settings';
		
		if(!file_exists(self::CONFIG_BASE_PATH.$this->version.self::CONFIG_FILE_EXTENSION)){
			throw new Exception('Unable to locate settings file with the name: '.$this->version.self::CONFIG_FILE_EXTENSION);
		}
		
		$this->values = new Hash();
		$this->values->Load(json_decode(file_get_contents(self::CONFIG_BASE_PATH.$this->version.self::CONFIG_FILE_EXTENSION), true));
	}
	
	/*
	* Repeated code between Value() and Values();
	* Gets the desired child/children from the cached json Hash and returns it.
	*/
	private function FindValues(array $child = null){
		if($child === null){
			throw new Exception('No setting value was passed, please pass a valid setting name.');
		}

		$values = $this->values;
		foreach($child as $key => $name){
			if(!$values->offsetExists($name)){
				throw new Exception('Invalid setting name passed. Please check the call and try again.');
			}
				
			$values = $values->offsetGet($name);
		}
		
		return $values;
	}
}