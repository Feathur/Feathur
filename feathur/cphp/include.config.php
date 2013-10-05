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

if(empty($_CPHP_CONFIG))
{
	die("No valid CPHP configuration path was specified. Refer to the CPHP manual for instructions.");
}

$confdata = @file_get_contents($_CPHP_CONFIG);

if($confdata === false)
{
	die("The specified CPHP configuration path was not found. Refer to the CPHP manual for instructions.");
}

$cphp_config = @json_decode($confdata);

if(json_last_error() != JSON_ERROR_NONE)
{
	die("Failed to parse CPHP configuration. Refer to the CPHP manual for instructions.");
}
