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

class CPHPBaseClass
{
	public $render_template = "";
	
	public function RenderTimeAgo($template, $property)
	{
		/* DEPRECATED: Please do not use this function if you can avoid it. 
		 * The time_ago function can now be used to accomplish the same. */
		global $locale;
		
		$variable_name = "s{$property}";
		
		if(isset($this->$variable_name) && is_numeric($this->$variable_name))
		{
			$timestamp = $this->$variable_name;
			
			if($timestamp > time())
			{
				$sTimeAgo = $locale->strings['event-future'];
			}
			elseif($timestamp == time())
			{
				$sTimeAgo = $locale->strings['event-now'];
			}
			else
			{
				$date1 = new DateTime("@{$timestamp}", new DateTimeZone("GMT"));
				$date2 = new DateTime("now", new DateTimeZone("GMT"));
				
				$interval = $date1->diff($date2);
				$years = (int)$interval->format("%G"); 
				$months = (int)$interval->format("%m"); 
				$weeks = (int)$interval->format("%U"); 
				$days = (int)$interval->format("%d"); 
				$hours = (int)$interval->format("%H");
				$minutes = (int)$interval->format("%i");
				$seconds = (int)$interval->format("%S");
				
				if($years > 1)
				{
					$sTimeAgo = sprintf($locale->strings['event-years-ago'], $years);
				}
				elseif($years == 1)
				{
					$sTimeAgo = $locale->strings['event-1year-ago'];
				}
				elseif($months > 1)
				{
					$sTimeAgo = sprintf($locale->strings['event-months-ago'], $months);
				}
				elseif($months == 1)
				{
					$sTimeAgo = $locale->strings['event-1month-ago'];
				}
				elseif($weeks > 1)
				{
					$sTimeAgo = sprintf($locale->strings['event-weeks-ago'], $weeks);
				}
				elseif($weeks == 1)
				{
					$sTimeAgo = $locale->strings['event-1week-ago'];
				}
				elseif($days > 1)
				{
					$sTimeAgo = sprintf($locale->strings['event-days-ago'], $days);
				}
				elseif($days == 1)
				{
					$sTimeAgo = $locale->strings['event-1day-ago'];
				}
				elseif($hours > 1)
				{
					$sTimeAgo = sprintf($locale->strings['event-hours-ago'], $hours);
				}
				elseif($hours == 1)
				{
					$sTimeAgo = $locale->strings['event-1hour-ago'];
				}
				elseif($minutes > 1)
				{
					$sTimeAgo = sprintf($locale->strings['event-minutes-ago'], $minutes);
				}
				elseif($minutes == 1)
				{
					$sTimeAgo = $locale->strings['event-1minute-ago'];
				}
				elseif($seconds > 1)
				{
					$sTimeAgo = sprintf($locale->strings['event-seconds-ago'], $seconds);
				}
				elseif($seconds == 1)
				{
					$sTimeAgo = $locale->strings['event-1second-ago'];
				}
				else
				{
					// If you see this, there's probably something wrong.
					$sTimeAgo = $locale->strings['event-past'];
				}
				
			}
			
			$sDate = local_from_unix($timestamp, $locale->datetime_long);
			
			return $this->RenderTemplateExternal($template, array(
				'local-time'	=> $sDate,
				'time-ago'		=> $sTimeAgo,
				'timestamp'		=> $timestamp
			));
		}
		else
		{
			$classname = get_class($this);
			throw new Exception("Property {$classname}.{$property} does not exist or is not of a valid format.");
		}
	}
	
	public function RenderTemplateExternal($template, $strings)
	{
		/* DEPRECATED: Please do not use this function.
		 * Instead, you can use Templater::AdvancedParse for rendering arbitrary templates
		 * without instantiating a Templater yourself. */
		return $this->DoRenderTemplate($template, $strings);
	}
	
	public function DoRenderTemplate($template, $strings)
	{
		/* DEPRECATED: Please do not use this function.
		 * Class-specific templater functions have been discontinued. Instead, you can use
		 * Templater::AdvancedParse for rendering templates without instantiating a Templater
		 * yourself. */
		global $locale;
		
		try
		{
			$tpl = new Templater();
			$tpl->Load($template);
			$tpl->Localize($locale->strings);
			$tpl->Compile($strings);
			return $tpl->Render();
		}
		catch(Exception $e)
		{
			$classname = get_class($this);
			throw new Exception("Failed to render template {$classname}.{$template}.");
		}
	}
}
