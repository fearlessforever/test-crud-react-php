<?php
namespace App\Extra;

use App\Api\Accesstoken;
use App\Api\AccesstokenCreate;
use App\Api\Respond;
use App\Api\Config;
use Exception;

Class UpdateAccessTokenMiddleware implements MiddlewareInterface{
	
	public function execute()
	{
		$api = Config::init();
		$url = get_instance()->uri->segment( $api->getSegment() );
		
		if( $api->validate('accesstoken',$url) ){
			try{
				$at_generate = Accesstoken::init()->userdata('at_generate');
				if( ($at_generate - time() + $api->getVar('accessTime') ) < 300)
				Respond::set([
					'accesstoken'=> AccesstokenCreate::init()->create(
						Accesstoken::init()->userdata()
					)
				]);
			}catch(Exception $e){
				Respond::set(['accesstoken'=>false]);
				throw new Exception( $e->getMessage() );
			}
		}
	}
}