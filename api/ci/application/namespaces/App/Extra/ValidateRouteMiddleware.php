<?php
namespace App\Extra;

use App\Api\Config ;
use App\Api\Accesstoken ;
use Exception;

Class ValidateRouteMiddleware implements MiddlewareInterface{
	
	public function execute()
	{
		$api = Config::init();
		
		//validate url
		$url = get_instance()->uri->segment( $api->getSegment() );
		$api->validate('url',$url);
		
		// validate model exist or not
		$model = 'api/'.strtolower( $api->getSegment(true) ).'/'. $api->model($url) ;
		if(!file_exists(APPPATH ."models/{$model}.php"))
			throw new Exception('Model File Not Found' );
		
		// Load Model
		get_instance()->load->model( $model ,'proses',false);
	}
}