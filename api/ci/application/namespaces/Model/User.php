<?php
namespace Model;
defined('BASEPATH') OR exit('No direct script access allowed');

use Saya\DB ;

Class User{
	static $logged_area = 'just-forbidden';
	static $data=null;
	
	function __construct()
	{
	
	}
	
	public static function is_logged_in( $read =false)
	{
		if(!empty(self::$data) && !$read ){
			return true;
		}
		$CI = &get_instance();
		if(!isset($CI->session))show_error('<strong>Session Not Found </strong>',503);
		$a = $CI->session->all_userdata();
		
		if(isset($a['id_user']) && isset($a['nama']) ){
			self::$data=array(
				'id_user'=>$a['id_user'] ,'nama'=>$a['nama'],'level'=>date('Y-m-d H:i:s'),'nama_d'=>'','blokir'=>'N','extra'=>array(),'modul'=>array() ,'md5'=>''
			);
			if($read){
				DB::koneksi();
				$user= DB::select("SELECT level,namausers,blokir,sandiusers as md5 FROM ". KODE ."users WHERE id_user=:id_user LIMIT 1" ,array('id_user'=> $a['id_user'] ) ) ;
				if(isset($user[0])){
					self::$data =array_merge( self::$data ,$user[0] );
					$b = explode(' ',self::$data['nama']);
					self::$data['nama_d'] = $b[0];
				}else{
					//$_SESSION=null;
					$CI->session->sess_destroy();
					$ajax = $CI->input->is_ajax_request();
					if(!$ajax){
						show_error(' <h1 style="font-weight:bold; color:red;" >[ DATA ANDA TIDAK DITEMUKAN SYSTEM ] <a href="'. base_url() .'"> HOME </a> </h1> ',404);
					}
					exit;
				}
				$user= DB::select("SELECT nama,isi FROM ". KODE ."users_ext WHERE id_user=:id_user LIMIT 17" ,array('id_user'=> $a['id_user'] ) ) ;
				self::$data['extra'] = ganti($user , 'nama' , 'isi');
				if(self::$data['level'] != 'admin'){
					$user= DB::table(KODE .'users_izin')->select('nama_app as nama','nama_app as isi')->where('level',self::$data['level'] )->limit(27)->get();
					self::$data['modul'] = ganti($user , 'nama' , 'isi');
				}
				
			}
			return true;
		}else{
			return false;
		}
	}
	
	public static function login( $data )
	{
		if( self::is_logged_in( TRUE ) ){
			return array('berhasil'=>'Logged In !!!' ,'location'=>self::$logged_area );
		}
		$a=array('error' => 'Username & Password Not Match' );
		if( !isset($data) ){
			return $a;
		}
		
		DB::koneksi();
		$user= DB::table(KODE .'users')->select('id_user','namausers','sandiusers','blokir')->where('email_users',$data['id_user'])->orWhere('username',$data['id_user'])->limit(1)->first() ;
		
		if(isset($user['id_user']) && is_array($user ) ){
			if( $user['blokir'] != 'N' ){
				return array('error'=>'Can\'t Log into System, You have been Blocked !!!' );
			}
			if (password_verify($data['password'], $user['sandiusers'] )) {
				//$_SESSION['id_user']= ; $_SESSION['nama']= $user['namausers'];
				
				if( !empty($data['remember']) ){
					$cookieName = get_instance()->config->item('sess_cookie_name'); // we get the cookie
					$cookie = get_instance()->input->cookie( $cookieName ); // we get the cookie
					
					if(!empty($cookie))
					{
						get_instance()->input->set_cookie( $cookieName , $cookie, '35580000');
					}
					
				}
				get_instance()->session->set_userdata([
					'id_user'=>$user['id_user'],'nama'=> $user['namausers']
				]);
				$a = array(
					'berhasil'=>'You have Logged in' ,'location'=>self::$logged_area
				);
				\Saya\Notif::set( 0 , '' , $user['id_user'] );
			}
			
		}
		return $a ;
	}
	
	
}

function ganti ($data,$key , $val =null ){
	$baru=array();
	foreach($data as $v ){
		$baru[ $v[ $key ] ] = ($val == null) ? $v : $v[ $val ];
	}
	return $baru;
}