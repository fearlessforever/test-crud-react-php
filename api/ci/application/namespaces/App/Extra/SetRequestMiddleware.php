<?php
namespace App\Extra;

Class SetRequestMiddleware implements MiddlewareInterface{
	
	public function execute()
	{
		$listnya = ['email','password','accesstoken','errorcode','system'];
		foreach($listnya as $v){
			if(empty($_POST[$v])){
				$_POST[$v] =  isset($_GET[$v]) ? $_GET[$v] : '';
			}
		}
		
	}
}