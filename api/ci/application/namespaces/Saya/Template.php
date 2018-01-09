<?php
namespace Saya;
defined('BASEPATH') OR exit('No direct script access allowed');

use Saya\DB ;

Class Template {
	
	public static $data		= [
		'__current'		=> '',
		'__now' 		=> '',
		'theme'			=> '',
		'home'			=> '',
		'keyhash'		=> 'Ngasalasfasfasfwhfhjfs',
		'sys_debug_db'	=> false,
		'sys_demo'		=> false,
		'sys_notif'		=> false,
		'sys_hapus'		=> false,
		'asset'			=> '',
		'__tema'		=> '',
		'web_name'		=> '',
		'title'=>''
	];
	private static $status 	= false;
	
	function __construct()
	{
	
	}
	
	/*
	|---------------------------------------------------------------------------------------------------
	|	Ini untuk output / tampilan ke browser client berupa json .
	|	@no_html = TRUE , output json merubah < , > , ' ke html entities
	|	@hasil = array() ,
	|	P.S : method ini memebutuhkan file json.php di folder view Code Igniter .
	|---------------------------------------------------------------------------------------------------
	*/
	public static function view_json($hasil , $no_html =false)
	{
		$hasil = json_encode($hasil);
		if($no_html){
			$hasil = str_replace(array('<','>',"'"),array('&lt;','&gt;','&#039;') , $hasil );
		}
		
		$CI = &get_instance();
		$CI->output->set_content_type('application/json');
		$CI->load->view('json' , array('hasil' => $hasil) );
	}
	
	/*
	|---------------------------------------------------------------------------------------------------
	|	Ini untuk output html .
	|	@content = string untuk ditampilkan ke browser client
	|	
	|	P.S : method ini membutuhkan FILE view/{$nama_template}/z_load_view.php  di FOLDER view  Code Igniter .
	|---------------------------------------------------------------------------------------------------
	*/
	public static function view($content= NuLL)
	{
		
		if(!self::$status)
			self::getData();
		if(empty(self::$data['theme']))
			show_error('<span style="font-weight:bold;color:red;">ERROR : NO THEME DATA</span> ',503);
		
		$CI = &get_instance();
		self::$data['konten']=$content;
		
		$CI->load->view('tema/'. self::$data['theme'] .'/z_load_view' , self::$data);
	}
	
	/*
	|------------------------------------------------------------------------------------
	|	Ini untuk mendapatkan variabel pengaturan umum  .
	|	@reload = TRUE ,maka data yg sudah ada akan di replace / di ulang load
	|	
	|	P.S : method ini membutuhkan pembacaan database TABLE {KODE}pengaturan 
	|------------------------------------------------------------------------------------
	*/
	public static function &getData($reload = false)
	{
		if( self::$status && !$reload ){
			return self::$data;
		}
		
		if(! DB::$koneksi_status ){
			DB::koneksi();
		}
		
		$data = DB::select("SELECT nama,isi1,isi2 FROM ". KODE ."pengaturan ");
		if(isset($data[0]) && is_array($data) ){
			foreach($data as $val)self::$data[ $val['nama'] ]=$val;
		}
		self::$data['theme'] = !empty(self::$data['theme']['isi1']) ? self::$data['theme']['isi1'] : 'default' ; 
		self::$data['home'] = base_url() ; 
		self::$data['key_hash']= ( isset(self::$data['key_hash']['isi1'] ) && is_string( self::$data['key_hash']['isi1']) )? self::$data['key_hash']['isi1'] : 'Ngasalasfasfasfwhfhjfs'; 
		self::$data['sys_debug_db']= empty(self::$data['sys_debug_db']['isi1']) ? FALSE : (bool) self::$data['sys_debug_db']['isi1'];
		self::$data['sys_demo']= empty(self::$data['sys_demo']['isi1']) ? FALSE : (bool) self::$data['sys_demo']['isi1'];
		self::$data['sys_notif']= empty(self::$data['sys_notif']['isi1']) ? FALSE : (bool) self::$data['sys_notif']['isi1'];
		self::$data['sys_hapus']= empty(self::$data['sys_hapus']['isi1']) ? FALSE : (bool) self::$data['sys_hapus']['isi1'];
		self::$data['asset']=( isset(self::$data['asset']['isi1'][3]) && is_string(self::$data['asset']['isi1']) && self::$data['asset']['isi2'] == 1 )? self::$data['asset']['isi1'] : self::$data['home'] .'assets/';
		self::$data['__tema'] = self::$data['asset'] .'tema/'. self::$data['theme'] .'/';
		self::$data['web_name'] = ( isset(self::$data['web_name']['isi1'] ) && is_string( self::$data['web_name']['isi1']) )? self::$data['web_name']['isi1'] : ''; 
		
		$CI = &get_instance();
		self::$data['__current']=self::$data['home'] . $CI->uri->uri_string() .'/';
		self::$data['__now']=self::$data['home'] . $CI->uri->slash_segment(1) ;
		
		self::$status = true;
		return self::$data;
	}
	
}