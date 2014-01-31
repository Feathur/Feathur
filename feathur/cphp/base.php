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

require("include.constants.php");

require("include.config.php");
require("include.debug.php");

require("include.dependencies.php");
require("include.exceptions.php");
require("include.datetime.php");
require("include.misc.php");

require("include.memcache.php");
require("include.mysql.php");
require("include.session.php");
require("include.csrf.php");

require("include.lib.php");

require("class.templater.php");
require("class.localizer.php");

require("include.locale.php");

if(empty($not_html))
{
	header("Content-Type:text/html; charset=UTF-8");
}

require("class.base.php");
require("class.databaserecord.php");

foreach($cphp_config->components as $component)
{
	require("components/component.{$component}.php");
}

/* lighttpd (and perhaps some other HTTPds) won't pass on GET parameters
 * when using the server.error-handler-404 directive that is required to
 * use the CPHP router. This patch will try to detect such problems, and
 * manually extract the GET data from the request URI. I admit, it's a
 * bit of a hack, but there doesn't really seem to be a different way of
 * solving this issue. */

/* Detect whether the request URI and the $_GET array disagree on the
 * existence of GET parameters. */
if(strpos($_SERVER['REQUEST_URI'], "?") !== false && empty($_GET))
{
	/* Separate the protocol/host/path component from the query string. */
	list($uri, $query) = explode("?", $_SERVER['REQUEST_URI'], 2);
	
	/* Store the entire query string in the relevant $_SERVER variable -
	 * lighttpds strange behaviour breaks this variable as well. */
	$_SERVER['QUERY_STRING'] = $query;
	
	/* Finally, run the query string through PHPs own internal GET data
	 * parser, and have it store the result in the $_GET variable. This
	 * should yield an identical result to a well-functioning HTTPd. */
	parse_str($query, $_GET);
}

if(get_magic_quotes_gpc())
{
	/* By default, get rid of all quoted variables. Magic quotes are evil. */
	foreach($_POST as &$var)
	{
		$var = stripslashes($var);
	}
	
	foreach($_GET as &$var)
	{
		$var = stripslashes($var);
	}
}

if(!empty($cphp_config->autoloader))
{
	function cphp_autoload_class($class_name) 
	{
		global $_APP;
		
		$class_name = str_replace("\\", "/", strtolower($class_name));
		
		if(file_exists("classes/{$class_name}.php"))
		{
			require_once("classes/{$class_name}.php");
		}
	}

	spl_autoload_register('cphp_autoload_class');
}
