<?php
namespace App\Data;

use Saya\DB;

Class User{
	
	public static function getPermission(array $userId )
	{
		if(empty($userId['id_user']) || empty($userId['model']) ){
			return false;
		}
		$query = self::___getPermissionQuery();
		$query->select('a.is_read as read','a.is_create as create','a.is_update as update','a.is_delete as delete')
			   ->where('b.id_user',$userId['id_user'] )
			   ->where('c.nama_model',$userId['model'] );
		$query = $query->first();
		return $query;
	}
	private static function ___getPermissionQuery(){
		DB::koneksi();
		$query = DB::table(KODE . 'users_level_permission as a')
				->leftJoin(KODE .'users as b' ,'a.id_level','=','b.id_level')
				->leftJoin(KODE .'models as c' ,'a.id_model','=','c.id_model');
		return $query;
	}
}