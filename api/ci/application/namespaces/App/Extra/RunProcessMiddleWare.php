<?php
namespace App\Extra;

Class RunProcessMiddleWare implements MiddlewareInterface{
	
	public function execute()
	{		
		// Run Model 
		get_instance()->proses->run();
	}
}