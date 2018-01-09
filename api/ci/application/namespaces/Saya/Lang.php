<?php
namespace Saya;
defined('BASEPATH') OR exit('No direct script access allowed');

Class Lang {
	static $tersedia =array(
		'en'=>'english','id'=>'indonesia'
	);
	static $lang =null;
	
	public static function setLang($certain=null)
	{
		if( isset(self::$lang) ){
			return ;
		}
		if(!isset($certain)){
			switch(true){
				case !empty($_SERVER['HTTP_ACCEPT_LANGUAGE']) : 
					$lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
					break;
				default: 
					$lang ='en';
					break;
			}
		}else{
			$lang = is_string( $certain ) ? $certain : '';
		}
		
		self::$lang = isset(self::$tersedia[ $lang ] ) ? self::$tersedia[ $lang ] : self::$tersedia['en'] ;
	}
	
	public static function load($file='')
	{
		if( !isset(self::$lang) ){
			self::setLang();
		}
		
		$CI = &get_instance();
		$CI->lang->load( $file , self::$lang );
	}
	
	public static function get($pilih='')
	{
		$CI = &get_instance();
		return $CI->lang->line( $pilih , $log = FALSE );
	}
}
