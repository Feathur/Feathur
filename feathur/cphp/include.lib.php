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

if(!isset($cphp_config->libraries))
{
	/* No library configuration has been specified. We don't need to
	 * execute the rest of this file. */
	return;
}

/* Some libraries want to have a cache directory. Instead of storing all
 * this stuff in the document root, we'll put it into /tmp (or any other
 * config-specified directory). We should probably make sure the needed
 * directories exist, though... */

$tmp_dir = (isset($cphp_config->tmp_dir)) ? $cphp_config->tmp_dir : "/tmp";

if(!file_exists("{$tmp_dir}/cphp"))
{
	mkdir("{$tmp_dir}/cphp", 0700);
}

if(!file_exists("{$tmp_dir}/cphp/cache"))
{
	mkdir("{$tmp_dir}/cphp/cache", 0700);
}

/* We'll set up HTMLPurifier here if so desired. */
if(isset($cphp_config->libraries->htmlpurifier))
{
	if(!is_dir("{$tmp_dir}/cphp/cache/htmlpurifier"))
	{
		mkdir("{$tmp_dir}/cphp/cache/htmlpurifier", 0700);
	}
	
	$library_config = $cphp_config->libraries->htmlpurifier;
	$library_path = (isset($library_config->path)) ? $library_config->path : "lib/htmlpurifier/HTMLPurifier.auto.php";
	
	require_once($library_path);
	
	$purifier_config = HTMLPurifier_Config::createDefault();
	
	if(isset($library_config->doctype))
	{
		$purifier_config->set("HTML.Doctype", $library_config->doctype);
	}

	if(isset($library_config->encoding))
	{
		$purifier_config->set("Core.Encoding", $library_config->encoding);
	}
	
	$purifier_config->set("Cache.SerializerPath", "{$tmp_dir}/cphp/cache/htmlpurifier");
	
	$cphp_purifier = new HTMLPurifier($purifier_config);
	$cphp_hash_purifier_config = md5(serialize($purifier_config));

	function purify_html($input, $cache_duration = 3600, $config = null)
	{
		if(isset($config))
		{
			$config->set("Cache.SerializerPath", "{$tmp_dir}/cphp/cache/htmlpurifier");
			$cphp_purifier = new HTMLPurifier($config);
			$hash_config = md5(serialize($config));
		}
		else
		{
			global $cphp_purifier;
			global $cphp_hash_purifier_config;
			$hash_config = $cphp_hash_purifier_config;
		}
		
		$hash_input = md5($input) . md5($input . "x");
		$memcache_key = "purify_{$hash_config}_{$hash_input}";
		
		if($result = mc_get($memcache_key))
		{
			return $result;
		}
		else
		{
			$result = $cphp_purifier->purify($input);
			mc_set($memcache_key, $result, $cache_duration);
			return $result;
		}
	}
}
