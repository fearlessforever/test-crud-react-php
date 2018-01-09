<?php
namespace App\Api;

use \Exception;
Class Config{
	private static $accessList=array();
	private static $accessTime=0;
	private static $url=0;
	private static $limit=0;
	private static $no_html=false;
	private static $version;
	private static $version_default;
	private static $start_from = 1;
	private static $instance =false;
	private static $modelName =false;
	
	public static function init()
	{
		self::$instance = (!self::$instance) ? new Config : self::$instance ;
		return self::$instance ;
	}
	
	function __construct(){
		if(empty(self::$accessList)){
			$CI = &get_instance();
			$CI->load->config('my/config_api');
			self::$accessList = $CI->config->item('api_access_list');
			if(empty(self::$accessList))
				throw new Exception(' Access List Config Not Found !!! ');
			self::$accessTime = $CI->config->item('api_time');
			self::$limit = $CI->config->item('api_response_limit');
			self::$version = $CI->config->item('api_version');
			self::$version_default = $CI->config->item('api_version_default');
			self::$start_from = $CI->config->item('api_start_from');
		}		
	}
	
	public function getVar($variabel){
		switch($variabel){
			case 'accessTime': return self::$accessTime;
			default: return '';
		}
	}
	public function validate($tipe,&$str)
	{
		$str = strtolower($str); $str = preg_replace('/\.html/i','',$str);
		
		switch($tipe){
			case 'accesstoken':
				return isset(self::$accessList[$str]['accesstoken']) ? self::$accessList[$str]['accesstoken'] : false;
			case 'url':
				if(!isset(self::$accessList[$str]))
					throw new Exception('Access List Not Found !!! ');
				
				return  true;
			default: 
				throw new Exception('Cant Validate !!! ');
				break;
		}
	}
	public function model($str)
	{
		$str = strtolower($str);
		if(empty(self::$accessList[$str]['model']))
			throw new Exception('File Model\'s Name Not Found on Config !!! ');
		return self::$accessList[$str]['model'];
	}
	public function url($ke=NuLL)
	{
		if($ke > 0 && is_int($ke) ){
			self::$url=$ke;
		}
		return self::$url +1 ;
	}
	
	public function limit()
	{
		return self::$limit;
	}
	public function noHtml($set=Null)
	{
		if($set)self::$no_html= true;
		return self::$no_html;
	}
	public function getSegment($return_url=false)
	{
		if(self::$url > 0 && $return_url == false)return self::$url;
		self::$url = self::$start_from ;
		$check = get_instance()->uri->segment(self::$url);

		if(in_array($check , self::$version)){
			self::$url += 1; 
		}else{
			$check  = self::$version_default ;
		}
		return ( ($return_url) ? $check :  self::$url );
	}
	public function getModel()
	{
		if(static::$modelName){
			return static::$modelName;
		}
		$model = get_instance()->uri->segment( $this->getSegment() );
		$model = strtolower($model);
		static::$modelName = preg_replace('/\.html/i','',$model);
		return  static::$modelName ;
	}
}