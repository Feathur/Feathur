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

class CSRF
{
	public static function GenerateToken()
	{
		$key = random_string(12);
		$token = random_string(25);
		
		if(!isset($_SESSION['_CPHP_CSRF_KEYS']))
		{
			$_SESSION['_CPHP_CSRF_KEYS'] = array();
		}
		
		$_SESSION['_CPHP_CSRF_KEYS'][$key] = $token;
		
		return array(
			'key'	=> $key,
			'token'	=> $token
		);
	}
	
	public static function GenerateReplacement($matches)
	{
		$pair = CSRF::GenerateToken();
		
		return $matches[0] . "
		<input name=\"_CPHP_CSRF_KEY\" type=\"hidden\" value=\"{$pair['key']}\">
		<input name=\"_CPHP_CSRF_TOKEN\" type=\"hidden\" value=\"{$pair['token']}\">";
	}
	
	public static function InsertTokens($input)
	{
		return preg_replace_callback("/<form[^>]*>(?!\s*<input name=\"_CPHP_CSRF)/i", "CSRF::GenerateReplacement", $input);
	}
	
	public static function VerifyToken()
	{
		if(!empty($_POST['_CPHP_CSRF_KEY']) && !empty($_POST['_CPHP_CSRF_TOKEN']))
		{
			$key = $_POST['_CPHP_CSRF_KEY'];
			$token = $_POST['_CPHP_CSRF_TOKEN'];
			
			if(empty($_SESSION['_CPHP_CSRF_KEYS'][$key]) || $_SESSION['_CPHP_CSRF_KEYS'][$key] != $token)
			{
				throw new CsrfException("The given CSRF token does not match the given CSRF key.");
			}
		}
		else
		{
			throw new CsrfException("No CSRF token present in submitted data.");
		}
	}
}

class CsrfException extends Exception {}
