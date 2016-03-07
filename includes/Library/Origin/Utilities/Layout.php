<?php
namespace Origin\Utilities;

use \Smarty;

class Layout extends \Origin\Utilities\Types\Singleton {
	private $holder;
	
  /*
  * Assign a variable to the template engine.
  */
	public function Assign($name, $value){
		$this->Smarty()->Assign($name, $value);
	}
	
  /*
  * Display a template to a user and exit.
  */
	public function Display($template){
		exit($this->Smarty()->display($template));
	}
	
  /*
  * Get the HTML from a template. (Also resets the template and all assigned variables back to null).
  */
	public function ToHTML($template){
		$result = $this->Smarty()->fetch($template);
    $this->holder = null;
    return $result;
	}
	
  /*
  * Assign a list of javascript files to be added to the page's HTML.
  */
	public function Javascript(array $array){
		$this->Assign('javascript_files', $array);
	}
	
  /*
  * Assign a list of style sheet files to be added to the page's HTML.
  */
  public function Style(array $array){
    $this->Assign('style_files', $array);
  }
  
  /*
  * Creates the smarty object if it doesn't exist and 
  */
	private function Smarty(){
		if(!is_object($this->holder)){
			$this->holder = new Smarty();
      $this->holder->registerClass('Settings', '\\Origin\\Utilities\\Settings');
			//$this->holder->registerClass('AntiCSRF', '\\Origin\\Utilities\\AntiCSRF'); // TODO
		}
		
		return $this->holder;
	}
}