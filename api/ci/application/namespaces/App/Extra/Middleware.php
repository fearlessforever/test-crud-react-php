<?php
namespace App\Extra;

Class Middleware{
	private $listMiddleware = [];
	public function set( $set  )
	{
		$set = (array) $set ;
		if(is_array($set)){
			foreach($set as $v){
				$v = new $v;
				if($v instanceof MiddlewareInterface){
					$this->listMiddleware[]= $v;
				}				
			}
		}
		return $this;
	}
	public function run()
	{
		foreach($this->listMiddleware as $v){
			$v->execute();
		}
	}
}