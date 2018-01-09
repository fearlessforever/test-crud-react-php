<?php
namespace Model;
defined('BASEPATH') OR exit('No direct script access allowed');
/*
|------------------------------------------------------------------------------
|	Class untuk Loged in area ,
|	Ini membutuhkan Table z_aplikasi
|------------------------------------------------------------------------------
*/

Class Logged_area {

	function __construct()
	{
	
	}
	
	public static function get( $page , $mode )
	{
		if(empty($page) || empty($mode) )return false;
		//die(var_dump(User::$data));
		$_data= \Saya\DB::table('z_aplikasi')->select('nama_app','mode','file_view','file_model','perawatan')->where('nama_app',$page )->limit(1)->first() ;
		if(isset($_data['nama_app'])){
			$file = $mode == 'view' ? APPPATH .'views/'.$_data['mode'].'/'.$_data['file_view'] . '.php'  : APPPATH .'models/'.$_data['mode'].'/'.$_data['file_model'] . '.php' ;
			
			if(!file_exists( $file )){
				if($mode == 'view')
					return array('page'=>'<h1 style="text-align:center;"> Application Not Install</h1>' );
				else 
					return array('error'=>'Application Not Install !!!' );
			}
			
			if( !empty($_data['perawatan']) ){
				if($mode == 'view') return array('page'=>'<h1 style="text-align:center;"> Application is On Maintenance State </h1>' );
				else return array('error'=>'Application is On Maintenance State !!!' );
			}
			
			if(User::$data['blokir'] != 'N'){
				get_instance()->session->sess_destroy();
				if($mode == 'view') return array('page'=>'<h1 style="text-align:center; font-weight:bold;color:red;">You Have Been Blocked !!!</h1>' );
				else return array('error'=>'You Have Been Blocked !!!' );
			}
			
			if(User::$data['level'] != 'admin'){
				if( !isset(User::$data['modul'][ $_data['nama_app'] ] ) ){
					if($mode == 'view') return array('page'=>'<h1 style="text-align:center; font-weight:bold;color:red;">You\'re Not Allowed To Acces This</h1>' );
					else return array('error'=>'You\'re Not Allowed To Acces This !!!' );
				}
			}
				
			$CI = &get_instance();
			if($mode == 'view'){
				\Saya\Template::$data['__controller'] = $_data['nama_app'] ;
				$a = $CI->load->view( $_data['mode'].'/'.$_data['file_view'] , \Saya\Template::getData() , TRUE );
				
				return array('page'=>$a );
			}else{
				$CI->load->model(  $_data['mode'].'/'.$_data['file_model'] ,'prosesnya');
				return $CI->prosesnya->run();
			}
			
		}
		
		return false;
	}
	
	
}