<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use Saya\DB;
use Saya\Validasi;
use App\Api\Respond as Resp;

class Dashboard extends CI_Model {
	var $cfg;
	function __construct() {
		parent::__construct();  
	}
	
	public function run()
	{
		try{
			$this->cfg = new App\Api\Config;
			$method = $this->uri->segment( $this->cfg->url() );
			switch(true){
				//case !empty($isAccessTokenExist): $this->___read(); break; 
				default :  $this->___index(); break;
			}
		}catch(PDOException $e){
			throw new Exception( $e->getMessage() );
		}
	}
	private function ___index(){
		//throw new Exception('ada aja ya');
		$data =[
			'success'=>true,
			'user'=>[
				'photo'=>'/external/img/samples/scarlet-159.png'
				,'name'=>'Scarlett Johansson'
			],
			'expiredIn'=> App\Api\Accesstoken::init()->userdata('at_generate') - time() + $this->cfg->getVar('accessTime') ,
			'notif'=>[
				'pesan'=>0,'notif'=>0
			]
		];

		Resp::set($data );
	}
}