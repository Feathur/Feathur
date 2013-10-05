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

if(!empty($cphp_config->locale->default_locale))
{
	$locale = new Localizer();
	$locale->Load($cphp_config->locale->default_locale);

	setlocale(LC_ALL, $locale->locale);
}
else
{
	die("No default locale was specified. Refer to the CPHP manual for instructions.");
}
