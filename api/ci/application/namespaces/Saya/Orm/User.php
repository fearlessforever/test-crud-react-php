<?php

namespace Saya\Orm;

use Saya\DB;
use Illuminate\Database\Eloquent\Model;

Class User extends Model {
	
	protected $table = KODE .'pengguna';
	protected $primaryKey = 'userid';
	protected $keyType = 'string';
	public $timestamps = false;
	
	function __construct(){
		DB::koneksi(TRUE);
	}
	 
}