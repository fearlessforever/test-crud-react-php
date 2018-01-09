<?php

namespace App\Api;
/*
|------------------------------------------------------------------------------
|	Session dari Api system
|
|------------------------------------------------------------------------------
*/
use \Exception ;
Class Nexts {
	
	public static function set($string ,$key=false)
	{
		$_key =  empty($key) ? get_instance()->config->item('encrypt_key_next') : $key ;
		return base64_encode($string .'|||'. md5($string.$key) );
	}
	public static function get(&$string ,$key=false)
	{
		$_key =  empty($key) ? get_instance()->config->item('encrypt_key_next') : $key ;
		$_string = base64_decode($string ); 
		$_string = explode('|||',$_string);
		if(!isset($_string[1]))return false;
		if( $_string[1] == md5($_string[0].$key) ){
			$string = $_string[0];
			return  true;
		}
		return false;
		
	}
}