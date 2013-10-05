<?php
/*
 * CPHP is more free software. It is licensed under the WTFPL, which
 * allows you to do pretty much anything with it, without having to
 * ask permission. Commercial use is allowed, and no attribution is
 * required. We do politely request that you share your modifications
 * to benefit other developers, but you are under no enforced
 * obligation to do so :)
 * 
 * Please read the accompanying LICENSE document for the full WTFPL
 * licensing text.
 */

cphp_dependency_provides("cphp_errorhandler", "0.1");

define("CPHP_ERRORHANDLER_TYPE_ERROR",			90001	);
define("CPHP_ERRORHANDLER_TYPE_INFO",			90002	);
define("CPHP_ERRORHANDLER_TYPE_WARNING",		90003	);
define("CPHP_ERRORHANDLER_TYPE_SUCCESS",		90004	);

class CPHPErrorHandler
{
	public $sErrorType = CPHP_ERRORHANDLER_TYPE_ERROR;
	public $sLogError = true;
	public $sTitle = "";
	public $sMessage = "";
	
	public function __construct($type, $title, $message, $log = true)
	{
		$this->sErrorType = $type;
		$this->sLogError = $log;
		$this->sTitle = $title;
		$this->sMessage = $message;
	}
	
	public function LogError($context, $message)
	{
		// FIXME placeholder function, error logging has not been implemented yet
	}
	
	public function Render()
	{
		global $locale;
		
		switch($this->sErrorType)
		{
			case CPHP_ERRORHANDLER_TYPE_ERROR:
				$template = "errorhandler.error";
				break;
			case CPHP_ERRORHANDLER_TYPE_INFO:
				$template = "errorhandler.info";
				break;
			case CPHP_ERRORHANDLER_TYPE_WARNING:
				$template = "errorhandler.warning";
				break;
			case CPHP_ERRORHANDLER_TYPE_SUCCESS:
				$template = "errorhandler.success";
				break;
			default:
				return false;
		}
		
		return Templater::AdvancedParse($template, $locale->strings, array(
			'title'		=> $this->sTitle,
			'message'	=> $this->sMessage
		));
	}
}
