<?php
namespace Origin\Log;

use \Origin\Utilities\Settings;
use \Origin\Utilities\Types\Exception;
/*
* Relies on settings to determine if the system should write to the log.
* If the log level defined in settings isn't high enough this will throw the message away.
*
* Use: Log::Get('my_log_file')->Log('example', 'fun text', Log::SEVERITY_ERROR);
*  or
* Log::Get('my_log_file')->Error('example', 'fun text');
*/
class Log extends \Origin\Utilities\Types\Singleton {
	/*
	* Possible log levels (constants)
	*/
	const SEVERITY_ERROR = 1; // Fatal will exit.
	const SEVERITY_WARNING = 2; // Won't exit, but will be placed in log.log as well as the log file you specify.
	const SEVERITY_INFO = 3; // Things you may want to know about.
	
	// End of normal log levels. These are for debugging purposeses (say if something is broken. You can then turn up the log level in settings.)
	const SEVERITY_TRACE = 4; // For going through a process line by line.
	const SEVERITY_KITCHEN_SINK = 5; // For logging things that aren't normally important to anyone.
	const SEVERITY_EXCESSIVE = 6; // For logging everything including what you had for breakfast this morning.
	const SEVERITY_SOMETHING_EXTRA = 7; // You shouldn't log to this level if you can help it.
	
	const LOG_BASE_PATH = 'hidden/logs/';
	
	private $generic_log_file;
	public function __construct($file = null){
		$this->generic_log_file = Settings::Get('settings')->Value(['origin', 'generic_log_file']);
		$this->minimum_log_level = (int) Settings::Get('settings')->Value(['origin', 'log_level']);
		$this->file = ($file === null) ? 'log.log' : $file.'.log';
	}
	
	public function Log($key = null, $value = null, $log_level = self::SEVERITY_TRACE){
		if($this->CanLog($log_level)){
			
			// Basic Logging
			if(file_put_contents(self::LOG_BASE_PATH.$this->file, $this->Format($key, $value, $log_level), FILE_APPEND | LOCK_EX) === false){
				throw new Exception('Can not write to log file. Please check the path and that the folder is writeable.');
			}
			
			// Log level 1 only.
			if($log_level <= self::SEVERITY_WARNING){
				if(file_put_contents(self::LOG_BASE_PATH.$this->generic_log_file, $this->Format($key, $value, $log_level), FILE_APPEND | LOCK_EX) === false){
					throw new Exception('Can not write to log file. Please check the path and that the folder is writeable.');
				}
				
				if($log_level === self::SEVERITY_ERROR){
					throw new Exception('An error has occured.');
				}
			}
			
		}
	}
	
	/*
	* This may eventually become more complex as I may add flags to logging (EG: only log these flags).
	*/
	private function CanLog($log_level){
		if($log_level <= $this->minimum_log_level){
			return true;
		}
		
		return false;
	}
	
	/*
	* Prepares a key and a value into a simgle string for writing to a file.
	*/
	private function Format($key, $value, $log_level){
		$key = (is_array($key) || is_object($key)) ? print_r($key, true) : $key;
		$value = (is_array($value) || is_object($value)) ? print_r($value, true) : $value;
		return sprintf("%s (%s): %s\n", $key, $log_level, $value);
	}
}