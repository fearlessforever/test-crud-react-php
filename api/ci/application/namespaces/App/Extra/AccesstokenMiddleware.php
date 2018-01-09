<?php
namespace App\Extra;

use App\Api\Accesstoken;
use App\Api\Respond;
use App\Api\Config;
use Exception;

Class AccesstokenMiddleware implements MiddlewareInterface{
	public function execute()
	{
		$api = Config::init();
		$url = get_instance()->uri->segment( $api->getSegment() );
		if( $api->validate('accesstoken',$url) ){
			try{
				Accesstoken::init()->read();
			}catch(Exception $e){
				Respond::set(['accesstoken'=>false]);
				throw new Exception( $e->getMessage() );
			}
		}
	}
}