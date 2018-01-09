<?php
namespace Saya;
defined('BASEPATH') OR exit('No direct script access allowed');

Class PubTemplate extends Template {
	protected static $pub = array(
		'navigasi'=>array(
			['desc'=>'Home','url'=> '/','active'=>false]
			,['desc'=>'Tentang Kami','url'=>'about-us','active'=>false]
			,['desc'=>'Extra','url'=>'#','active'=>false,'sub'=>[
				['desc'=>'Galery','url'=> 'galery','active'=>false]
				,['desc'=>'Agenda','url'=> 'agenda','active'=>false]
				,['desc'=>'Anggota','url'=> 'anggota','active'=>false]
				,['desc'=>'Laporan Khas','url'=> 'laporan-khas','active'=>false]
				,['desc'=>'Sistem Informasi','url'=> 'login','active'=>false]
			] ]
			,['desc'=>'Blog','url'=>'blog','active'=>false]
			,['desc'=>'Kontak','url'=>'contact-us','active'=>false]
		)
		,'contact'=>' '
	);
	function __construct()
	{
		
	}
	public static function configJson( $file ='takada' , $merge = false)
	{
		if( file_exists(APPPATH .'config/json/' . $file) ){
			$file = file_get_contents( APPPATH .'config/json/' . $file );
			if($merge){
				$result = json_decode($file,TRUE);
				self::$pub = array_merge(self::$pub , $result);
			}else{
				self::$pub = json_decode($file,TRUE);
			}
		}
	}
	public static function MakeNav( $custom=null )
	{
		$a = $custom == null ? self::$pub['navigasi']  : $custom ;
		$str = '' ; $home = base_url();
		if(is_array( $a )){
			foreach($a as $val){
				$str .='<li class="'.( !empty($val['active']) ? 'active' : '' ) .(isset($val['sub'])? ' dropdown' : '').'"><a href="'.$home.$val['url'].'" '.(isset($val['sub'])? 'class="dropdown-toggle" data-toggle="dropdown"' : '').'>'.$val['desc'].' '.(isset($val['sub'])? '<i class="icon-angle-down"></i>' : '').'</a> '.(isset($val['sub']) ? self::MakeNav( $val['sub'] ) : '' ).'</li>';
			}
			$str = $custom == null ? '<ul class="nav navbar-nav  navbar-right">'. $str.'</ul>' : '<ul class="dropdown-menu">'. $str.'</ul>';
		}
		return $str;
	}
	public static function setActive( $str ='')
	{
		if(is_array(self::$pub['navigasi']))foreach(self::$pub['navigasi'] as &$val)if($val['desc'] == $str){ $val['active']=true; return true;}
	}
	public static function getPub($var='')
	{
		return isset(self::$pub[$var]) ? self::$pub[$var] : '';
	}
	public static function setPub($var=null ,$data =null )
	{
		if(empty($var) || empty($data))return false;
		self::$pub[$var] =$data;
		return true;
	}
	public static function MakeBC($data = null)
	{
		$str =''; $no=0;
		$breadcrumb = isset($data)? $data: ( isset(self::$pub['breadcrumb']) ? self::$pub['breadcrumb'] : NuLL ) ;
		if(isset($breadcrumb) && is_array($breadcrumb) ){
			foreach($breadcrumb as $val){
				if(!isset($val['link']) || !isset($val['title']) )continue;
				$str .= '<li itemprop="itemListElement" itemscope
						  itemtype="http://schema.org/ListItem">
						<a itemscope itemtype="http://schema.org/Thing"
						   itemprop="item" href="'.$val['link'].'">
							<span itemprop="name">'. $val['title'] .'</span> </a>
						<meta itemprop="position" content="'. ++$no .'" />
					  </li>';
			}
			$str = '<ol itemscope itemtype="http://schema.org/BreadcrumbList" class="breadcrumb"> '.$str.' </ol>';
		}
		return $str;
	}
	public static function tema_view( $extra = '' )
	{
		return 'tema/'. self::$data['theme'] .'/'.$extra ;
	}
	public static function check_view($extra='')
	{
		return (file_exists( APPPATH .'views/tema/'. self::$data['theme'] .'/'.$extra ) ) ? true : false;
	}
	public static function b( &$data , $key )
	{
		$b = array();
		$a = explode('|',$key);
		if(!isset($a[1]) OR !is_array($data) )return false;
		foreach($data as $k => $v){
			if(isset($v[$a[0]]) && isset($v[$a[1]]) )$b[$v[$a[0]]] = $v[$a[1]] ;
		}
		if(!empty($b)){
			$data = $b;
			return true;
		}
		return false;
		
	}
}