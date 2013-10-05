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

$cphp_dependencies = array();
$cphp_last_dependency = "";

function cphp_dependency_provides($component, $version)
{
	global $cphp_dependencies, $cphp_last_dependency;
	$cphp_dependencies[$component] = $version;
	$cphp_last_dependency = $component;
}

function cphp_dependency_requires($component, $version)
{
	global $cphp_dependencies, $cphp_last_dependency;
	
	if(!isset($cphp_dependencies[$component]))
	{
		die("The {$cphp_last_dependency} component requires the {$component} component to be loaded, but this is not the case.");
	}
	
	$current_version = $cphp_dependencies[$component];
	
	if(!cphp_dependency_match($current_version, $version))
	{
		die("The {$cphp_last_dependency} component requires the {$component} component with version {$version} to be loaded, but an incompatible version ({$current_version}) was found.");
	}
}

function cphp_dependency_match($available, $required)
{
	if(strpos($required, ",") !== false)
	{
		$ranges = explode(",", $required);
	}
	else
	{
		$ranges[] = $version;
	}
	
	foreach($ranges as $range)
	{
		if(strpos($required, "|") !== false)
		{
			list($min, $max) = explode("|", $range);
			
			$f_min = (float) $min;
			$f_max = (float) $max;
			$f_cur = (float) $available;
			
			if(empty($min) && empty($max))
			{
				return true;
			}
			elseif(empty($min))
			{
				if($f_cur < $f_max)
				{
					return true;
				}
				else
				{
					return false;
				}
			}
			elseif(empty($max))
			{
				if($f_cur > $f_min)
				{
					return true;
				}
				else
				{
					return false;
				}
			}
			else
			{
				if($f_cur > $f_min && $f_cur < $f_max)
				{
					return true;
				}
				else
				{
					return false;
				}
			}
		}
	}
}
