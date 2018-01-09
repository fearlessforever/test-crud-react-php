<?php

namespace Model;

Class Input{
	static $_data =null ;

	static function postNya( $key='' , $read = false )
	{
		if($read == true OR self::$_data == NuLL ){
			//self::$_data = urldecode(file_get_contents("php://input"));
			parse_str(file_get_contents('php://input'), self::$_data );
		}
		return ($key == '') ? self::$_data : ( isset(self::$_data[$key]) ? self::$_data[$key] : '' ) ;

	}
}