<?php

namespace Model;
use \Saya\DB;

/*  File ini untuk Baca,Buat,Update,Delete database yang coloumn nya type BloB json Khusus MariaDB */

Class JsonDB  {
	use \NoDirect\TraitList\Json  {
		addColumn as public;
		decodeJson as public;
	}
	use \NoDirect\TraitList\JsonQueryBuilder{

	}
	private $__tableName = KODE .'json';
	private $__insertExtra = '';
	private static $__jsonColoumn = 'jsonnya';
	public static $errorMsg =false;
	public static $errorCode =false;
	
	
	function __construct()
	{
		
	}
	
	public static function getError()
	{
		return ['msg'=>self::$errorMsg , 'code'=>self::$errorCode ];
	}
	
	
	
	
	/*
	Contoh INSERT : INSERT INTO assets VALUES ('MariaDB T-shirt', COLUMN_CREATE('color', 'blue', 'size', 'XL'));
	Contoh SELECT : SELECT item_name, COLUMN_GET(dynamic_cols, 'color' as char) AS color FROM assets;
	Contoh Remove A Cloumn : 
		UPDATE assets SET dynamic_cols=COLUMN_DELETE(dynamic_cols, "price") 
			WHERE COLUMN_GET(dynamic_cols, 'color' as char)='black';
	Contoh Add a Coloumn :
		UPDATE assets SET dynamic_cols=COLUMN_ADD(dynamic_cols, 'warranty', '3 years')
			WHERE item_name='Thinkpad Laptop';
	Contoh Select Tampilkan List COLUMN dari json :
		SELECT item_name, column_list(dynamic_cols) FROM assets;
	Contoh Select Baca Isi json semua string ny format json :
		SELECT item_name, COLUMN_JSON(dynamic_cols) FROM assets;
	COLUMN DELETE
		COLUMN_DELETE(dyncol_blob, column_nr, column_nr...);
		COLUMN_DELETE(dyncol_blob, column_name, column_name...);
	COLUMN_EXISTS
		COLUMN_EXISTS(dyncol_blob, column_nr);
		COLUMN_EXISTS(dyncol_blob, column_name);
	*/
}