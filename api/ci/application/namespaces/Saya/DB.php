<?php

namespace Saya;
/*
|------------------------------------------------------------------------------
|	Class Ini Membutuhkan Beberapa File Core di Laravel Dan Carbon
|	Folder :
|		Laravel : Illuminate\Container , Illuminate\Events, Illuminate\Database, Illuminate\Support, Illuminate\Contracts
|		Carbon : Carbon
|	P.S : Carbon dibutuhkan Jika menggunakan Eloquent ORM database ( Untuk set timestamp , tapi jika timestamp di set false maka carbon tidak dibutuhkan)
|------------------------------------------------------------------------------
*/

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Events\Dispatcher;
use Illuminate\Container\Container;

//require ( '/../Illuminate/Support/helpers.php');
require_once ( APPPATH . 'namespaces/Illuminate/Support/helpers.php');

Class DB extends Capsule {
	
	public static $koneksi_status = FALSE;
	private static $status_orm = FALSE;
	private static $setting = array();
	
	public static function koneksi( $orm = FALSE )
	{
		if($orm == TRUE && !self::$status_orm ){
			self::$koneksi_status = FALSE;
			
		}
		if(self::$koneksi_status)return;
		
		$koneksi=null;
		if( file_exists( APPPATH .'config/database.php' ) && self::$setting == array() ){
			require (APPPATH .'config/database.php');
			$koneksi = isset($db['laravel_orm']) ? $db['laravel_orm'] : null;
		}else{
			$koneksi = self::$setting ;
		}
		if(empty($koneksi))show_error('
			<strong style="font-weight:bold;">Database Connection Not Found <span style="color:red;">[ application/config/database.php ]</span> !!!</strong>
			<p> Exp : $db["laravel_orm"]=array() ; </p>'
		);
		
		$capsule = new Capsule;
		$capsule->addConnection( $koneksi );
		/* $capsule->addConnection([
			'driver'    => 'mysql',
			'host'      => 'localhost',
			'database'  => 'infaq',
			'username'  => 'root',
			'password'  => '',
			'charset'   => 'utf8',
			'collation' => 'utf8_unicode_ci',
			'prefix'    => '',
		]); */

		

		// Make this Capsule instance available globally via static methods... (optional)
		$capsule->setAsGlobal();

		/*
		|-------------------------------------------------------------------------------
		|	Setup the Eloquent ORM... (optional; unless you've used setEventDispatcher())
		|	Uncomment This Line If you Want to use Laravel Eloquent ORM
		|	P.S : Activate ORM consume more server resource such a RAM
		|-------------------------------------------------------------------------------
		*/
		if($orm){
			// Set the event dispatcher used by Eloquent models... (optional)
			$capsule->setEventDispatcher(new Dispatcher(new Container));
			
			$capsule->bootEloquent();
			self::$status_orm = TRUE;
		}
		
		/*
		|-------------------------------------------------------------------------------
		|	Set Fetch Mode : Array atau Object , Default :Object
		|	Comment Line below If You want To Fetch from Mysql as an Object
		|-------------------------------------------------------------------------------
		*/
		$capsule->getConnection()->setFetchMode(\PDO::FETCH_ASSOC);
		//self::connection()->setFetchMode(PDO::FETCH_ASSOC);
		
		self::$koneksi_status= TRUE;
	}
	public static function  baru( $koneksi = null , $orm = FALSE )
	{
		if(!is_array( $koneksi)) return false;
		self::$koneksi_status= FALSE;
		self::$setting = $koneksi ;
		self::koneksi( $orm );
	}
}