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

function random_string($length)
{
	$pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

	$crypto_rand_secure = function ( $min, $max ) {
		$range = $max - $min;
		if ( $range < 0 ) return $min; // not so random...
		$log    = log( $range, 2 );
		$bytes  = (int) ( $log / 8 ) + 1; // length in bytes
		$bits   = (int) $log + 1; // length in bits
		$filter = (int) ( 1 << $bits ) - 1; // set all lower bits to 1
		do {
			$rnd = hexdec( bin2hex( openssl_random_pseudo_bytes( $bytes ) ) );
			$rnd = $rnd & $filter; // discard irrelevant bits
		} while ( $rnd >= $range );
		return $min + $rnd;
	};

	$token = "";
	$max   = strlen( $pool );
	for ( $i = 0; $i < $length; $i++ ) {
		$token .= $pool[$crypto_rand_secure( 0, $max )];
	}
	
	return $token;
}

function extract_globals()
{
    $vars = array();
    
    foreach($GLOBALS as $key => $value){
        $vars[] = "$".$key;
    }
    
    return "global " . join(",", $vars) . ";";
}

function utf8entities($utf8) 
{
	// Credits to silverbeat@gmx.at (http://www.php.net/manual/en/function.htmlentities.php#96648)
	$encodeTags = true;
	$result = '';
	for ($i = 0; $i < strlen($utf8); $i++) 
	{
		$char = $utf8[$i];
		$ascii = ord($char);
		if ($ascii < 128) 
		{
			$result .= ($encodeTags) ? htmlentities($char) : $char;
		} 
		else if ($ascii < 192) 
		{
			// Do nothing.
		} 
		else if ($ascii < 224) 
		{
			$result .= htmlentities(substr($utf8, $i, 2), ENT_QUOTES, 'UTF-8');
			$i++;
		} 
		else if ($ascii < 240) 
		{
			$ascii1 = ord($utf8[$i+1]);
			$ascii2 = ord($utf8[$i+2]);
			$unicode = (15 & $ascii) * 4096 +
			(63 & $ascii1) * 64 +
			(63 & $ascii2);
			$result .= "&#$unicode;";
			$i += 2;
		} 
		else if ($ascii < 248) 
		{
			$ascii1 = ord($utf8[$i+1]);
			$ascii2 = ord($utf8[$i+2]);
			$ascii3 = ord($utf8[$i+3]);
			$unicode = (15 & $ascii) * 262144 +
			(63 & $ascii1) * 4096 +
			(63 & $ascii2) * 64 +
			(63 & $ascii3);
			$result .= "&#$unicode;";
			$i += 3;
		}
	}
	return $result;
}

function clean_array($arr)
{
	$result = array();
	foreach($arr as $key => $value)
	{
		if(!empty($value))
		{
			$result[$key] = $value;
		}
	}
	return $result;
}

function pretty_dump($input)
{
	ob_start();
	
	var_dump($input);
	
	$output = ob_get_contents();
	ob_end_clean();
	
	while(preg_match("/^[ ]*[ ]/m", $output) == 1)
	{
		$output = preg_replace("/^([ ]*)[ ]/m", "$1&nbsp;&nbsp;&nbsp;", $output);
	}
	
	$output = nl2br($output);
	
	echo($output);
}

function rgb_from_hex($hex)
{
	if(strlen($hex) == 6)
	{
		$r = substr($hex, 0, 2);
		$g = substr($hex, 2, 2);
		$b = substr($hex, 4, 2);
		
		$rgb['r'] = base_convert($r, 16, 10);
		$rgb['g'] = base_convert($g, 16, 10);
		$rgb['b'] = base_convert($b, 16, 10);
		
		return $rgb;
	}
	else
	{
		return false;
	}
}

function hex_from_rgb($rgb)
{
	if(!empty($rgb['r']) && !empty($rgb['g']) && !empty($rgb['b']))
	{
		return base_convert($rgb['r'], 10, 16) . base_convert($rgb['g'], 10, 16) . base_convert($rgb['b'], 10, 16);
	}
	else
	{
		return false;
	}
}

function strip_tags_attr($string, $allowtags = NULL, $allowattributes = NULL)
{ 
	/* Thanks to nauthiz693@gmail.com (http://www.php.net/manual/en/function.strip-tags.php#91498) */
	$string = strip_tags($string,$allowtags);
	
	if (!is_null($allowattributes)) 
	{ 
		if(!is_array($allowattributes)) 
		{
			$allowattributes = explode(",",$allowattributes); 
		}

		if(is_array($allowattributes)) 
		{
			$allowattributes = implode(")(?<!",$allowattributes); 
		}

		if (strlen($allowattributes) > 0) 
		{
			$allowattributes = "(?<!".$allowattributes.")"; 
		}

		$string = preg_replace_callback("/<[^>]*>/i",create_function('$matches', 'return preg_replace("/ [^ =]*'.$allowattributes.'=(\"[^\"]*\"|\'[^\']*\')/i", "", $matches[0]);'),$string); 
	} 
	
	return $string; 
}

function cut_text($input, $length)
{
	if(strlen($input) > $length)
	{
		if(preg_match("/^(.{0,{$length}})\W/s", $input, $matches))
		{
			return $matches[1] . "...";
		}
		else
		{
			return "";
		}
	}
	else
	{
		return $input;
	}
}

function filter_html($input)
{
	return strip_tags_attr($input, "<a><b><i><u><span><div><p><img><br><hr><font><ul><li><ol><dt><dd><h1><h2><h3><h4><h5><h6><h7><del><map><area><strong><em><big><small><sub><sup><ins><pre><blockquote><cite><q><center><marquee><table><tr><td><th>", "href,src,alt,class,style,align,valign,color,face,size,width,height,shape,coords,target,border,cellpadding,cellspacing,colspan,rowspan");
}

function filter_html_strict($input)
{
	return strip_tags_attr($input, "<strong><em><br><hr><img><a><span><p><div>", "src,href,style");
}

function parse_rss($url)
{
	$rss = new DOMDocument();
	$rss->load($url);
	
	$items = array();
	
	foreach($rss->getElementsByTagName('item') as $item)
	{
		$items[] = array(
			'title'		=> $item->getElementsByTagName('title')->item(0)->nodeValue,
			'description'	=> $item->getElementsByTagName('description')->item(0)->nodeValue,
			'url'		=> $item->getElementsByTagName('link')->item(0)->nodeValue,
			'date'		=> strtotime($item->getElementsByTagName('pubDate')->item(0)->nodeValue)
		);
	}
	
	return $items;
}

function fix_utf8($input)
{
	return utf8_encode(utf8_decode($input));
}

function generate_pagination($min, $max, $current, $around, $start, $end)
{
	/* Generates an array with pages that should be shown in a pagination bar.
	 * $min		The first page number (this will usually be 1).
	 * $max		The last page number (this is usually the total amount of pages).
	 * $current	The page the user is currently on.
	 * $around	The amount of pages that should at least be shown around the current page.
	 * $start	The amount of pages that should at least be shown at the start.
	 * $end		The amount of pages that should at least be shown at the end.
	 * 
	 * Returns:
	 * Array, containing integers (for the pages) and null objects (for the boundaries). */
	
	if($max < ($start + $end + ($around * 2) + 1))
	{
		/* There are less pages than there would be elements. */
		$return_array = array();
		
		for($i = 1; $i <= $max; $i++)
		{
			$return_array[] = $i;
		}
		
		return $return_array;
	}
	else
	{
		/* Calculation time... */
		$return_array = array();
		
		/* Start with the left segment. */
		for($i = 1; $i <= $start; $i++)
		{
			$return_array[] = $i;
		}
		
		/* Now the middle segment. */
		if($start + $around >= $current - 1)
		{
			$left_start = $i;
		}
		else
		{
			$return_array[] = null;
			$left_start = $current - $around;
		}
		
		for($i = $left_start; $i <= $current + $around; $i++)
		{
			if($i >= $max)
			{
				break;
			}
			
			$return_array[] = $i;
		}
		
		/* And finally the right segment. */
		if($max - ($end + $around) <= $current + 1)
		{
			$right_start = $i;
		}
		else
		{
			$return_array[] = null;
			$right_start = $max - $end;
		}
		
		for($i = $right_start; $i <= $max; $i++)
		{
			$return_array[] = $i;
		}
		
		/* All done! */
		return $return_array;
	}
}

function ends_with($haystack, $needle)
{
	return (substr($haystack, -strlen($needle)) == $needle);
}

function redirect($target)
{
	header("Location: {$target}");
	die();
}

function ceil_precision($value, $precision = 0)
{
	$multiplier = pow(10, $precision);
	
	return ceil($value * $multiplier) / $multiplier;
}

function str_lreplace($search, $replace, $subject)
{
	$pos = strrpos($subject, $search);

	if($pos !== false)
	{
		$subject = substr_replace($subject, $replace, $pos, strlen($search));
	}

	return $subject;
}

function http_status_code($code)
{
	$codes = array(
		100 => "Continue",
		101 => "Switching Protocols",
		200 => "OK",
		201 => "Created",
		202 => "Accepted",
		203 => "Non-Authoritative Information",
		204 => "No Content",
		205 => "Reset Content",
		206 => "Partial Content",
		300 => "Multiple Choices",
		301 => "Moved Permanently",
		302 => "Moved Temporarily",
		303 => "See Other",
		304 => "Not Modified",
		305 => "Use Proxy",
		400 => "Bad Request",
		401 => "Unauthorized",
		402 => "Payment Required",
		403 => "Forbidden",
		404 => "Not Found",
		405 => "Method Not Allowed",
		406 => "Not Acceptable",
		407 => "Proxy Authentication Required",
		408 => "Request Time-out",
		409 => "Conflict",
		410 => "Gone",
		411 => "Length Required",
		412 => "Precondition Failed",
		413 => "Request Entity Too Large",
		414 => "Request-URI Too Large",
		415 => "Unsupported Media Type",
		418 => "I'm a teapot",
		422 => "Unprocessable Entity",
		500 => "Internal Server Error",
		501 => "Not Implemented",
		502 => "Bad Gateway",
		503 => "Service Unavailable",
		504 => "Gateway Time-out",
		505 => "HTTP Version not supported",
	);
	
	if(array_key_exists($code, $codes))
	{
		$text = $codes[$code];
	}
	else
	{
		throw new Exception("The specified HTTP status code does not exist.");
	}
	
	if(strpos(php_sapi_name(), "cgi") !== false)
	{
		header("Status: {$code} {$text}");
	}
	else
	{
		$protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');
		header("{$protocol} {$code} {$text}");
	}
}
