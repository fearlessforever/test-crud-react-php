<?php
namespace App\Api;

Class Respond {
	private static $resp=[];
	public static function set( array $data)
	{
		self::$resp = array_merge(self::$resp , $data );
	}
	public static function get()
	{
		return self::$resp ;
	}
	public static function error($data)
	{
		$errorCode = get_instance()->input->get('errorcode');
		$errorCode = empty($errorCode) ? get_instance()->input->post('errorcode') : $errorCode;
		($errorCode == 'false') ? false : get_instance()->output->set_status_header(503) ;
		self::set(['error'=> $data ] ); 
	}
	public static function json( Config $cfg = null , $certain=null)
	{
		$cfg = empty($cfg) ? Config::init() : $cfg ;
		$hasil = empty($certain) ? self::$resp : $certain ;
		$hasil = is_string($hasil) ? $hasil : json_encode($hasil);
		
		if($cfg->limit() > 0){
			if(isset($hasil [ $cfg->limit() ])){
				$errorCode = get_instance()->input->get('errorcode');
				($errorCode == 'false') ? false : get_instance()->output->set_status_header(503) ;
				$hasil = '{"error":"Please decrease data limit"}';
			}
		}
		if( $cfg->noHtml() ){
			$hasil = str_replace(array('<','>',"'"),array('&lt;','&gt;','&#039;') , $hasil );
		}
		
		get_instance()->output->set_content_type('application/json');
		get_instance()->load->view('json' , array('hasil' => $hasil) );
	}
}