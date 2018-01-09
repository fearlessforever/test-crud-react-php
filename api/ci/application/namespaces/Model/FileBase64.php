<?php 
namespace Model;

Class FileBase64 extends \Saya\Upload {
	static $tmpLocation = APPPATH .'tmp';
	static $limitSize = 8300000;
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
			return false; self::$errorMsg ='Data is Not Base64';
		}
		
		$str = str_replace(' ','+', $file[1]);
		
		file_put_contents( self::$tmpLocation .'/'.$name , base64_decode($str) );
		if( filesize(self::$tmpLocation .'/'.$name) > self::$limitSize ){
			return false; self::$errorMsg ='Data is Not Base64 !!!';
		}		
		self::$_folder = 'upload/' .date('Y') .'/' .date('m') .'/'.date('d').'/';
		
		if(!file_exists(self::$_asset . self::$_folder)){
			mkdir( self::$_asset .self::$_folder , 0755 , true);
		}
		//$check = move_uploaded_file( $name , self::$_asset . self::$_folder );
		$check = rename( self::$tmpLocation .'/'.$name , self::$_asset . self::$_folder.$name );
		//die(var_dump($check));
		@unlink( self::$tmpLocation .'/'.$name );
		return $check;
	}
}