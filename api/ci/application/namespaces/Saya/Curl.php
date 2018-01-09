<?php

namespace Saya;

Class Curl {
	static $ch = false;
	static $body = false;
	private static $setopt = array();
	public static function get( $data=array() )
	{
		//$ch = curl_init();
		if(!self::$ch)self::set($data);		
		try{
			self::$body = curl_exec(self::$ch) ; 
		}catch(\Exception $e){
			throw new \Exception('[SYS] '.$e->getMessage());
		}
		//die(var_dump($page,$header));
	}
	
	/*
	curl_setopt($ch,CURLOPT_POSTFIELDS, );
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER , false );
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_USERAGENT, true);
	curl_setopt($ch, CURLOPT_REFERER, true);
	curl_setopt($ch, CURLINFO_HEADER_OUT, true);
	*/
	public static function set( $data = array() , $noreset = false)
	{
		if(self::$ch)curl_close(self::$ch);
		self::$ch = curl_init();
		if(!$noreset){
			self::$setopt = array_merge(array(
				'CURLOPT_URL' => ''
				,'CURLINFO_HEADER_OUT'=>true
				,'CURLOPT_RETURNTRANSFER'=>1
				,'CURLOPT_FOLLOWLOCATION' => true
			),$data);
		}else{
			self::$setopt = array_merge(self::$setopt,$data) ;
		}
		
		foreach(self::$setopt as $k => $v ){
			try{
				curl_setopt(self::$ch , constant($k) , $v );
			}catch(\Exception $e){
				throw new \Exception('[SYS] '.$e->getMessage());
			} 
		}
	}
	public static function getError()
	{
		return (!self::$ch) ? false : curl_error(self::$ch);
	}
	public static function getHeader()
	{
		if(isset(self::$setopt['CURLOPT_HEADER']) and !empty(self::$body) ){
			$curl_info = curl_getinfo(self::$ch);;
			if(is_string(self::$body)){
				$header = substr(self::$body, 0, $curl_info['header_size']);
			}else{
				//$curl_info = curl_getinfo(self::$ch , CURLINFO_COOKIELIST); 
				die(var_dump($curl_info ));
			}
			//$header_size = curl_getinfo(self::$ch, CURLINFO_HEADER_SIZE);
			
			return ($header == '1') ? self::$body : $header ;
		} else return false;
	}
	public static function getBody()
	{
		if(isset(self::$setopt['CURLOPT_HEADER']) and !empty(self::$body) ){
			$header_size = curl_getinfo(self::$ch, CURLINFO_HEADER_SIZE);
			$check = @gzinflate(substr(substr(self::$body, $header_size), 10));
			return empty($check) ? substr(self::$body, $header_size) : $check ;
		}elseif( !isset(self::$setopt['CURLOPT_HEADER']) and !empty(self::$body)){
			$check = @gzinflate(substr(self::$body, 10));
			return empty($check) ? self::$body : $check;
		} else return false;
	}
}