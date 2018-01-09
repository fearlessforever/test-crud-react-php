<?php 
namespace Model;

Class ImageBase64 extends \Saya\Upload {
	static $tmpLocation = APPPATH .'tmp';
	static $limitSize = 2100000;
	static $errorMsg = '';
	public static function get( $str = '' , $name = '')
	{
		if(empty($str)){
			return false; self::$errorMsg ='Data Not Found';
		}
		
		if(!file_exists(self::$tmpLocation)){
			mkdir( self::$tmpLocation , 0755 , true);
		}
		$file = explode('base64,',$str);
		if(!isset($file[1])){
			self::$errorMsg ='Data is Not Base64'; return false; 
		}
		
		$str = str_replace(' ','+', $file[1]);
		
		file_put_contents( self::$tmpLocation .'/'.$name , base64_decode($str) );
		if( filesize(self::$tmpLocation .'/'.$name) > self::$limitSize ){
			@unlink( self::$tmpLocation .'/'.$name );
			self::$errorMsg ='Data Size Reach Limit !!!'; return false; 
		}		
		self::$_folder = 'upload/' .date('Y') .'/' .date('m') .'/'.date('d').'/';
		$check = self::generate_image_thumbnail( self::$tmpLocation .'/'.$name , $name);
		if($check){
			self::$_max_width = 400; self::$_max_height = 400;
			self::generate_image_thumbnail( self::$tmpLocation .'/'.$name , 't_'.$name);
		}
		@unlink( self::$tmpLocation .'/'.$name );
		return $check;
	}
}