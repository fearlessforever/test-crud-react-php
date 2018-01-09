<?php

namespace Saya;
/*
|------------------------------------------------------------------------------
|	Class Ini Memudahkan Masalah session default di php 
|
|------------------------------------------------------------------------------
*/
use Saya\Extra\CI2_Session;

Class Session{
	private static $ci2=false;
	
	public static function close($id=null)
	{
		if(!isset($_SESSION))return false;
		return session_write_close($id);

	}
	public static function ci2($param=array())
	{
		if(!self::$ci2){
			self::$ci2 = new CI2_Session($param) ;
			get_instance()->session = self::$ci2;
		}
		return self::$ci2 ;
	}
}