<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

spl_autoload_register(function ($class_name) {
	$file = APPPATH .'namespaces/' . str_replace('\\', '/', $class_name) . '.php';
	//file_put_contents(APPPATH .'cache/tes.txt' , $class_name . PHP_EOL , FILE_APPEND);
	if(file_exists( $file ) ){
		require_once( $file );
	}elseif(strpos($class_name , '\\') !== false ){
		show_error("Failed To Load Namespace :   <strong style='color:red;font-weight:bold;'> {$class_name} </strong>  [FILE NOT FOUND] ",503);
	} 
});