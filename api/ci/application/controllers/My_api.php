<?php
use App\Api\Respond as Resp;

Class My_api extends CI_Controller{
	function __construct(){
		parent::__construct();
		$this->load->helper('load');
		//$this->load->helper('error_handler');
	}
	function route($id=null)
	{		
		$this->output->set_header('Access-Control-Allow-Origin : *');
		try{
			$middleWare = new App\Extra\Middleware;
			$middleWare->set([
				App\Extra\SetRequestMiddleware::class ,
				App\Extra\AccesstokenMiddleware::class ,
				App\Extra\ValidateRouteMiddleware::class,
				App\Extra\GetUserInformationMiddleware::class,
				App\Extra\RunProcessMiddleWare::class
			])->set(
				App\Extra\UpdateAccessTokenMiddleware::class
			)->run();
			
		}catch(Exception $e){
			Resp::error( $e->getMessage() );
		}
		Resp::json( );
	}
}