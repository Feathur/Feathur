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

if($_CPHP !== true) { die(); }

class BaseException extends Exception
{
	public function __construct($message, $code = 0, $previous = null, $data = array())
	{
		$this->data = $data;
		
		parent::__construct($message, $code, $previous);
	}
}

class OwnershipException extends BaseException {}
class UserAccessException extends BaseException {}
class PrototypeException extends BaseException {}
class ConstructorException extends BaseException {}
class MissingDataException extends BaseException {}
class DatabaseException extends BaseException {}
class TypeException extends BaseException {}
class DeprecatedException extends BaseException {}

class TemplateException extends Exception
{
	public $message = "";
	public $file = "";
	public $startpos = 0;
	public $endpos = 0;
	public $code = 0;
	
	public function __construct($message, $file, $startpos, $endpos = 0, $code = "")
	{
		$this->message = $message;
		$this->file = $file;
		$this->startpos = $startpos;
		$this->endpos = $endpos;
	}
}

class NotFoundException extends BaseException 
{
	public function __construct($message, $code = 0, $previous = null, $field =  "", $data = array())
	{
		$this->field = $field;
		
		parent::__construct($message, $code, $previous, $data);
	}
}

class TemplateSyntaxException extends TemplateException {}
class TemplateParsingException extends TemplateException {}
class TemplateEvaluationException extends BaseException {}
