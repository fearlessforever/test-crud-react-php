<?php

namespace Saya;
/*
|------------------------------------------------------------------------------
|	Simple Class untuk Upload Gambar
| 
| 
| 
| 
|------------------------------------------------------------------------------
*/
Class Upload{
	static $_max_width = 850 ; static $_max_height = 650;
	static $_folder = '';
	static $_asset = 'assets/';
	static $_mode = 'resize';
	static $_quality = 65;
	
	public static function generate_image_thumbnail($source_image_path, $nama_file)
	{
		$source_gd_image=false;
		list($source_image_width, $source_image_height, $source_image_type) = @getimagesize($source_image_path);
		switch ($source_image_type) {
			case IMAGETYPE_GIF:
				$source_gd_image = imagecreatefromgif($source_image_path);
				break;
			case IMAGETYPE_JPEG:
				$source_gd_image = imagecreatefromjpeg($source_image_path);
				break;
			case IMAGETYPE_PNG:
				$source_gd_image = imagecreatefrompng($source_image_path);
				break;
			default: 
				$source_gd_image=false;
				break;
		}
		if ($source_gd_image === false) {
			return false;
		}

		/* Buat Folder */
		if(!file_exists( self::$_asset . self::$_folder ) ){
			mkdir( self::$_asset . self::$_folder , 0755, true);
			
		}
		$nama_file = self::$_asset . self::$_folder . $nama_file;
		/* End Of Buat Folder */		
		
		$source_aspect_ratio = $source_image_width / $source_image_height;
		$thumbnail_aspect_ratio = self::$_max_width / self::$_max_height;
		
			$thumbnail_image_width = self::$_max_width;
			$thumbnail_image_height = self::$_max_height;
		if(self::$_mode == 'resize'){
			if ($source_image_width <= self::$_max_width && $source_image_height <= self::$_max_height) {
				$thumbnail_image_width = $source_image_width;
				$thumbnail_image_height = $source_image_height;
			} elseif ($thumbnail_aspect_ratio > $source_aspect_ratio) {
				$thumbnail_image_width = (int) (self::$_max_height * $source_aspect_ratio);
				$thumbnail_image_height = self::$_max_height;
			} else {
				$thumbnail_image_width = self::$_max_width;
				$thumbnail_image_height = (int) (self::$_max_width / $source_aspect_ratio);
			}
		}
		

		$thumbnail_gd_image = imagecreatetruecolor($thumbnail_image_width, $thumbnail_image_height);
		imagecopyresampled($thumbnail_gd_image, $source_gd_image, 0, 0, 0, 0, $thumbnail_image_width, $thumbnail_image_height, $source_image_width, $source_image_height);

		//$img_disp = imagecreatetruecolor(self::$_max_width,self::$_max_width);
		$img_disp = imagecreatetruecolor($thumbnail_image_width,$thumbnail_image_height);
		$backcolor = imagecolorallocate($img_disp,0,0,0);
		imagefill($img_disp,0,0,$backcolor);

			imagecopy($img_disp, $thumbnail_gd_image, (imagesx($img_disp)/2)-(imagesx($thumbnail_gd_image)/2), (imagesy($img_disp)/2)-(imagesy($thumbnail_gd_image)/2), 0, 0, imagesx($thumbnail_gd_image), imagesy($thumbnail_gd_image));

		imagejpeg($img_disp, $nama_file, self::$_quality ); // 90 = kualitas
		imagedestroy($source_gd_image);
		imagedestroy($thumbnail_gd_image);
		imagedestroy($img_disp);
		return true;
	}
	public static function imageSet( $a =array() )
	{
		if(is_array($a) ){
			foreach($a as $k => $val){
				switch($k){
					case 'max_width': self::$_max_width = ($val > 0) ? $val : 1 ; break;
					case 'max_height': self::$_max_height = ($val > 0) ? $val : 1 ; break;
					case 'quality': self::$_quality = ($val > 0 && $val <= 100) ? $val : 65 ; break;
					case 'folder': self::$_folder = is_string($val) ? $val : '' ; break;
					case 'asset': self::$_asset = is_string($val) ? $val : '' ; break;
					case 'mode': self::$_mode = is_string($val) ? $val : 'resize' ; break;
					default: break;
				}
			}
		}
	}
}