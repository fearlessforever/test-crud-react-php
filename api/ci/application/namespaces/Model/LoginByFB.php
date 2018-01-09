<?php
namespace Model;
defined('BASEPATH') OR exit('No direct script access allowed');

use Saya\DB;
Class LoginByFB{
	private static $fb_secreat = '3ff8555b885eba521b9d22bcbaa99362';
	private static $fb_app_id = '869459749739606';
	
	//private static $fb_secreat = '7be24f56cec27ba92e04b5acc0ab9814';
	//private static $fb_app_id = '797979153573447';
	private static $level_login ='anonym';
	
	public static $error = '';
	public static $debug = '';
	
	public static function run()
	{
		session_name ('jg_by_fb'); session_start();
		setcookie(session_name(),session_id(),time()+600 ,'login-by-facebook');
		
		$fb = new \Facebook\Facebook([
		  'app_id' => self::$fb_app_id  ,
		  'app_secret' => self::$fb_secreat ,
		  'default_graph_version' => 'v2.9',
		  //'default_access_token' => '{access-token}', // optional
		]);
		
		//

		// Use one of the helper classes to get a Facebook\Authentication\AccessToken entity.
		//   $helper = $fb->getRedirectLoginHelper();
		//   $helper = $fb->getJavaScriptHelper();
		//   $helper = $fb->getCanvasHelper();
		//   $helper = $fb->getPageTabHelper();
		
		$helper = $fb->getRedirectLoginHelper();
		$permissions = ['email', 'user_likes','user_friends']; // optional
		//$loginUrl = $helper->getLoginUrl( base_url('login-by-facebook') , $permissions);
		//$loginUrl = $helper->getReAuthenticationUrl( base_url('login-by-facebook') , $permissions);
		$loginUrl = $helper->getReRequestUrl( base_url('login-by-facebook') , $permissions);
		$apani = get_instance()->input->get('code');
		if(!$apani){
			// Tidak ada data login, alihkan ke halaman facebook
			redirect( $loginUrl ); exit;
		}
		try {
			$accessToken = $helper->getAccessToken();
			if(empty($accessToken)){
				self::$error = ' [GRAPH] Access Token Not Found !!!' ;
				return false;
			}
			$response = $fb->get('/me?fields=email,id,name,age_range,gender', $accessToken );
			$user_profile = $response->getDecodedBody() ;
			
			$data=array(
				'username'=>$user_profile['id']
				,'namausers'=>$user_profile['name']
				,'nama_lengkap_users'=>$user_profile['name']
				,'email_users'=> ( empty($user_profile['email']) ? '' : $user_profile['email'] )
				,'level'=>self::$level_login
			 );
			// var_dump($user_profile);
			//die($accessToken);
			 $user_profile['access_token_nya']= (string) $accessToken;
			 $data2 =array(
				array('id'=>'','nama'=>'fbid','isi'=>$user_profile['id'] )
				,array('id'=>'','nama'=>'folder','isi'=>'' )
				,array('id'=>'','nama'=>'last_seen_notif','isi'=>0)
				,array('id'=>'','nama'=>'profile_pic','isi'=>'no_image.jpg' )
				,array('id'=>'','nama'=>'facebook_detail','isi'=>json_encode($user_profile) )
			 );
			DB::koneksi();
			DB::insert("INSERT INTO ".KODE."users VALUES(
				NULL,:username,:namausers,'',:nama_lengkap_users,:level,NOW(),NOW(),:email_users,'N'
			 ) ON DUPLICATE KEY UPDATE login_terakhir=NOW(),nama_lengkap_users=VALUES(nama_lengkap_users) ", $data );
			 
			 $data = DB::table(KODE .'users')->select('id_user','namausers' )->where('username',$user_profile['id'] )->limit(1)->first();
			 if(empty($data)) {
				self::$error = ' [DB] Users Data Not Found !!!' ;
				return false;
			 }
			 $query = DB::getPdo()->prepare("INSERT INTO ".KODE."users_ext VALUES(:id,:nama,:isi) 
				ON DUPLICATE KEY UPDATE isi=IF( nama = 'facebook_detail', VALUES(isi) ,isi ) 
				");
			 foreach( $data2 as $val){
				 $val['id']=$data['id_user'];
				 $query->execute($val);
			 }
			 get_instance()->session->set_userdata(array(
				'id_user'=>$data['id_user']
				,'nama'=>$data['namausers']
			 ));
			 
			 \Saya\Notif::set( 0 , '' , $data['id_user'] );
			 
			 return true;
		} catch(\Facebook\Exceptions\FacebookResponseException $e) {
			 // When Graph returns an error
			self::$error = 'Graph returned an error: ' . $e->getMessage();
			return false;
		} catch(\Facebook\Exceptions\FacebookSDKException $e) {
			// When validation fails or other local issues
			self::$error = 'Facebook SDK returned an error: ' . $e->getMessage();
			return false; 
		}catch(\PDOException $e){
			self::$error = ' [DB] Error Saving Data To Database OR Reading Database !!! <br> Your FacebookID : '.$user_profile['id'] .' .<br> Please contact admin ' ;
			self::$debug = $e->getMessage() ;
			return false;
		 }
		
	}
}