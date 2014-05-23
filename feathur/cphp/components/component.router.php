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

cphp_dependency_provides('cphp_router', '1.1');

class RouterException extends Exception {}

class CPHPRouter extends CPHPBaseClass
{
	public $routes = array();
	public $parameters = array();
	public $uVariables = array();
	public $custom_query = '';
	public $allow_slash = false;
	public $ignore_query = false;
	
	public function RouteRequest()
	{
		eval(extract_globals()); // hack hackity hack hack
		
		if(!empty($this->custom_query))
		{
			$requestpath = $this->custom_query;
		}
		else
		{
			if(!empty($_SERVER['REQUEST_URI']))
			{
				$requestpath = trim($_SERVER['REQUEST_URI']);
			}
			else
			{
				$requestpath = "/";
			}
		}
		
		// Save request path in Router object to make it accessible to other scripts.
		$this->uRequestPath = $requestpath;
		
		if($this->ignore_query === true)
		{
			if(strpos($requestpath, "?") !== false)
			{
				list($requestpath, $bogus) = explode("?", $requestpath, 2);
			}
		}
		
		$found = false;  // Workaround because a break after an include apparently doesn't work in PHP.
		
		foreach($this->routes as $priority)
		{
			foreach($priority as $route_regex => $route_destination)
			{
				if($found === false)
				{
					if($this->allow_slash === true)
					{
						if(strpos($route_regex, "$") !== false)
						{
							$route_regex = preg_replace("$", "/?$", $route_regex);
						}
						else
						{
							$route_regex = "{$route_regex}/?";
						}
					}
					
					$regex = preg_replace("/", "\/", $route_regex);
					if(preg_match("/{$regex}/i", $requestpath, $matches))
					{
						$this->uParameters = $matches;
						$this->uMethod = strtolower($_SERVER['REQUEST_METHOD']);
						
						if(is_array($route_destination))
						{
							// Options were provided.
							if(!isset($route_destination['target']))
							{
								throw new InvalidArgumentException("Target is missing from CPHPRoute options element.");
							}
							
							if(!empty($route_destination['methods']))
							{
								$sMethods = (!is_array($route_destination['methods'])) ? array($route_destination['methods']) : $route_destination['methods'];
								
								if(!in_array($this->uMethod, $sMethods))
								{
									continue;
								}
							}
							
							$authenticated = false;
							
							if(!isset($route_destination['authenticator']))
							{
								$authenticated = true;
							}
							else
							{
								if(!isset($route_destination['auth_error']))
								{
									throw new InvalidArgumentException("When specifying an authenticator, you must also specify a default error destination.");
								}
								
								$sRouterAuthenticated = false;
								$sRouterErrorDestination = $route_destination['auth_error'];
								
								require($route_destination['authenticator']);
								
								if($sRouterAuthenticated === true)
								{
									$authenticated = true;
								}
							}
							
							foreach($route_destination as $key => $value)
							{
								if(strlen($key) > 1 && substr($key, 0, 1) == "_")
								{
									$key = substr($key, 1);
									$this->uVariables[$key] = $value;
								}
							}
							
							if($authenticated === true)
							{
								$destination = $route_destination['target'];
								$this->sAuthenticated = true;
							}
							else
							{
								$destination = $sRouterErrorDestination;
								$this->sAuthenticated = false;
							}
						}
						else
						{
							$destination = $route_destination;
						}
						
						include($destination);
						$found = true;
					}
				}
			}
		}
		
		if($found === false)
		{
			throw new RouterException("No suitable route found");
		}
	}
}
