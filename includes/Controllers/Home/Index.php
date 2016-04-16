<?php
namespace Controllers\Home;

use \Origin\Utilities\Layout;

class Index {
	public function Main(){
		Layout::Get()->Display('login.tpl');
	}
}