<?php
namespace Origin\Router;

use \Origin\Utilities\Settings;
use \Origin\Utilities\Types\Exception;

/*
* Attempts to find a route from a route listed in routes.json.
* If one isn't found it will instead attempt to automatically detect the correct route.
* Should neither case happen this will direct the user to the 404 route listed in routes.json
* If no 404 route exists in routes.json this will throw an error. You have been warned.
*
* A default route must be specified in routes.json (designated by a *).
*/
class Router extends \Origin\Utilities\Types\Singleton {
  const CONTROLLER_BASE_PATH = 'includes/Controllers/%s.php';
  const CONTROLLER_BASE_NAMESPACE = '\\Controllers';
  const DIR = '/';
	const NS = '\\';
  
  /*
  * Called from any file you wish to do routing from. Usually only index.php in the public directory.
  * It is possible to specify a specific route via string as well (EG: For redirects in old code paths and the like).
  */
  public function Route($route = null){
    $route = ($route === null) ? trim(strtok($_SERVER["REQUEST_URI"],'?'), '/') : $route;
		if(empty($route)){
			return $this->Route('*');
		}
    
    // Exact match.
    if($this->routes->offsetExists($route)){
      return $this->PathFinder($this->routes->offsetGet($route));
    }
    
    // Regex match. I'm sure someone who's brighter than I am has a better solution for this in 30 lines or less.
		foreach($this->routes as $pattern => $path){
			if(@preg_match($this->RegexifyPattern($pattern), null) !== false){
				if(preg_match($this->RegexifyPattern($pattern), $route, $variables) > 0) {
					array_shift($variables);
					return $this->PathFinder($path, $variables);
				}
			}
		}
		
		return $this->Route('404');
  }
  
  /*
  * Configuration and private functions.
  */
  private $routes;
  private $attempts;
  private $max_route_attempts;
  public function __construct(){
    $this->routes = Settings::Get('routes')->Values(['routes']);
    $this->max_route_attempts = Settings::Get()->Value(['origin', 'max_route_attempts']);
		
		if(!$this->routes->offsetExists('*')){
      throw new Exception('You must specify a default route in your routes file.');
    }
    
    if(!$this->routes->offsetExists('404')){
      throw new Exception('You must specify a 404 route in your routes file.');
    }
  }
  
  /*
  * At this point we know the route we're going to use. Now to break it apart and call it.
  */
  private function PathFinder($path, array $variables = array()){
    if($this->AllowAttempt()){
      if($this->GetFile($path)){
        $class = $this->GetClass($path);
				if(call_user_func_array(array((new $class), $this->GetMethod($path)), $variables) === false){
          throw new Exception('File exists, but class is undefined for route: '.$this->GetClass($path).'->'.$this->GetMethod($path)); 
        }
        
        return true;
      }
      
      return $this->Route('404');
    }
  }
  
  /*
  * Finds the file for our route based on $path. If the file exists we'll require it.
  */
  private function GetFile($path){
    $path = $this->ClassFileCommon($path);
    if(file_exists(sprintf(self::CONTROLLER_BASE_PATH, implode(self::DIR, $path)))){
      require_once(sprintf(self::CONTROLLER_BASE_PATH, implode(self::DIR, $path)));
      return true;
    }
    
    return false;
  }
  
  /*
  * Returns the full class (including namespace) based on $path.
  */
  private function GetClass($path){
    $path = $this->ClassFileCommon($path);
    return self::CONTROLLER_BASE_NAMESPACE.self::NS.implode(self::NS, $path);
  }
  
  /*
  * Shared code between GetFile() and GetClass(). Returns the namespaces and class as an array while stripping off the method.
  */
  private function ClassFileCommon($path){
    $path = explode(self::DIR, $path);
    return array_slice($path, 0, (count($path) - 1));
  }
  
  /*
  * Retrieves the method name from $path.
  */
  private function GetMethod($path){
    $path = explode(self::DIR, $path);
    return $path[(count($path) - 1)];
  }
  
  /*
  * We'd only like to attempt to route a maximum number of times (otherwise we're likely in an infinite loop). 
  * This increments the counter and stops the madness in event of "failure".
  * This shouldn't be hit under normal circumstances unless another file makes a call to route itself or something rediculous.
  */
  private function AllowAttempt(){
    $this->attempts++;
    if($this->attempts > $this->max_route_attempts){
      throw new Exception('Routing has tried multiple times to find the correct path, but has failed. This likely means your class does not exist or is not in the correct location/namespace.');
    }
    
    return true;
  }
	
	private static $find = array('/', '*');
	private static $replace = array('\/', '\*');
	private function RegexifyPattern($pattern){
		return "@^" . preg_replace('/\\\:[a-zA-Z0-9\_\-]+/', '([a-zA-Z0-9\-\_]+)', preg_quote($pattern)) . "$@D";
	}
}
