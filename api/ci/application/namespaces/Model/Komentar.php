<?php
namespace Model;
use \Saya\DB;
Class Komentar{
	private static $id=null;
	private static $idsc=array(); // ids Komen
	private static $__errorMsg ='';
	function __construct(){
		
	}
	public static function getError()
	{
		return self::$__errorMsg ;
	}
	public static function id( $string =false )
	{
		if(!isset($_SESSION))return false;
		if($string == false){
			return !empty(self::$id) ? self::$id : false;
		}
		$md5 = md5($string);
		$_SESSION['komentar_hash_id'][$md5]=$string;
		self::$id = $md5;
		return true;
	}
	public static function cek($md5='')
	{
		return isset($_SESSION['komentar_hash_id'][$md5]) ? true : false ;
	}
	public static function getString($md5='')
	{
		return isset($_SESSION['komentar_hash_id'][$md5]) ? $_SESSION['komentar_hash_id'][$md5] : '' ;
	}
	private static function __simpanUser($data){
		try {
			DB::insert("INSERT IGNORE INTO ".KODE."komentar_user(email,nama) VALUES(:email,:nama) " ,$data);
		} catch (\PDOException $e) {
			self::$__errorMsg = $e->getMessage();
			return false;
		}
		return true;
	}
	private static function __simpanTopik($data){
		try {
			DB::insert("INSERT IGNORE INTO ".KODE."komentar_topik VALUES(:kode,:topik) " ,$data);
		} catch (\PDOException $e) {
			self::$__errorMsg = $e->getMessage();
			return false;
		}
		return true;
	}
	private static function __simpanComment($data , $reply = false){
		$_table = ($reply == false) ? 'komentar_i' : 'komentar_ir';
		try {
			DB::insert("INSERT INTO ".KODE.$_table."(kode ,id_komentar,id_user,isi_komentar,publish) 
				VALUES(:kode ,:id_komentar
				,(SELECT IFNULL(id_user,0 ) FROM ".KODE."komentar_user WHERE email=:email LIMIT 1),:isi,1 ) " ,$data);
		} catch ( \PDOException $e) {
			self::$__errorMsg = $e->getMessage();
			return false;
		}
		return true;
	}
	private static function __simpanHash($data){
		try {
			DB::insert("INSERT IGNORE INTO ".KODE."komentar(id_source,url) VALUES(:hash,:url) " ,$data);
		} catch ( \PDOException $e) {
			self::$__errorMsg = $e->getMessage();
			return false;
		}
		return true;
	}
	private static function __checkCommentExists($idc=''){
		try {
			$a = DB::table(KODE .'komentar_i')->select('kode')->where('kode',$idc)->limit(1)->first();
		} catch ( \PDOException $e) {
			self::$__errorMsg = $e->getMessage();
			return false;
		}
		return isset($a['kode']) ? true : false ;
	}
	public static function simpanReply($a)
	{
		if(! DB::$koneksi_status ){
			DB::koneksi();
		}
		if(empty($a['idc']))return false;

		$__id = false;
		if(self::__checkCommentExists($a['idc']) ){
			$listSimpan = array(
				'__simpanUser'=>array('nama'=>$a['nama'],'email'=>$a['email'])
				,'__simpanComment'=>array('kode'=>'' ,'email'=>$a['email'],'id_komentar'=>$a['idc'],'isi'=>$a['isi'])
			);
			$__replace = array();
			foreach($listSimpan as $k => $v ){
				if(isset($__replace[$k])){
					$v = array_merge($v , $__replace[$k]);
				}
				$proses = self::$k( $v ,true );
				if(!$proses)return $proses;
				if($k == '__simpanUser'){
					$__replace['__simpanComment']=array( );
					$id = DB::select("SELECT CONCAT(kode_auto('R',true),DATE_FORMAT(CURRENT_DATE(),'%y%m%d') ) as kode");
					$__id = isset($id[0]['kode']) ?$id[0]['kode'] : 0;
					$__replace['__simpanComment']['kode'] = $__id ;
				}			
			}

			self::$idsc[]= $a['idc'] ;
		}
		return $__id;
	}
	public static function simpanComment($a)
	{
		if(! DB::$koneksi_status ){
			DB::koneksi();
		}
		$__id = false;

		$listSimpan = array(
			'__simpanHash'=>array('hash'=>$a['hash'],'url'=> $_SESSION['komentar_hash_id'][$a['hash']] )
			,'__simpanUser'=>array('nama'=>$a['nama'],'email'=>$a['email'])
			,'__simpanComment'=>array('kode'=>'' ,'email'=>$a['email'],'id_komentar'=>'','isi'=>$a['isi'])
		);
		if(!empty($a['topik'])){
			$listSimpan['__simpanTopik']=array('kode'=>'','topik'=>$a['topik']);
		}
		$__replace = array();
		foreach($listSimpan as $k => $v ){
			if(isset($__replace[$k])){
				$v = array_merge($v , $__replace[$k]);
			}
			$proses = self::$k( $v );
			if(!$proses)return $proses;
			if($k == '__simpanUser'){
				$__replace['__simpanComment']=array( );
				$id = DB::select( "SELECT IFNULL(id_komentar,0 ) as kode FROM ".KODE."komentar WHERE id_source=:hash LIMIT 1",array('hash'=>$a['hash']));
				$__replace['__simpanComment']['id_komentar'] = isset($id[0]['kode']) ?$id[0]['kode'] : 0;
				$id = DB::select("SELECT CONCAT(kode_auto('C',true),DATE_FORMAT(CURRENT_DATE(),'%y%m%d') ) as kode");
				$__id = isset($id[0]['kode']) ?$id[0]['kode'] : 0;
				$__replace['__simpanComment']['kode'] = $__id ;
				$__replace['__simpanTopik']['kode'] = $__id ;
			}			
		}	
		
		return $__id;

	}	
	public static function getComments( $kon=false , $d =false )
	{
		if(! DB::$koneksi_status ){
			DB::koneksi();
		}
		$_data =array(); $_kon ='';
		if($kon != false){
			$_kon = 'AND ' . $kon;
			$_data = $d;
		}
		try {
		$core = DB::select("
			SELECT a.kode,a.id_komentar,b.nama,b.filternya,a.isi_komentar,DATE_FORMAT(a.tanggal_ct,'%d %M %Y ~ %H:%i') as  tanggal,a.tanggal_ut as tgl,a.cnt_r,c.jumlah
			FROM ".KODE."komentar_i a
			LEFT JOIN ".KODE."komentar_user b ON a.id_user=b.id_user
			LEFT JOIN ".KODE."komentar c ON a.id_komentar = c.id_komentar
			WHERE publish=1 {$_kon}
			ORDER BY a.tanggal_ut DESC
			LIMIT 7
		",$_data);
		}catch (\PDOException $e) {
			self::$__errorMsg = $e->getMessage(); return array();
		}
		
		if(isset($core[0]) && is_array($core) ){
			foreach($core as &$v){
				self::$idsc[]=$v['kode'];
				// filter output pesan untuk hindari xss ;
				$v['isi_komentar'] = ($v['filternya'] != 0 ) ? str_replace(array('<','>'),array('&lt;','&gt;'), $v['isi_komentar'] ) : $v['isi_komentar'];
			}
		}
		return $core;
	}
	public static function getReplys($kon=false , $d =false)
	{
		if(empty(self::$idsc))return array();
		if(! DB::$koneksi_status ){
			DB::koneksi();
		}

		$__extra=''; $__IN = '?'. str_repeat(',?', (count(self::$idsc) -1 ) ) ;
		if($kon && is_string($kon) && is_array($d) ){
			$__extra = $kon ;
			foreach($d as $v)self::$idsc[]=$v ;
		}
		try {
		$core = DB::select("
			SELECT a.kode,a.id_komentar,b.nama,b.filternya,a.isi_komentar,DATE_FORMAT(a.tanggal_ct,'%d %M %Y ~ %H:%i') as  tanggal,a.tanggal_ut as tgl,a.cnt_r
			FROM ".KODE."komentar_ir a
			LEFT JOIN ".KODE."komentar_user b ON a.id_user=b.id_user
			WHERE publish=1 AND a.id_komentar IN (". $__IN .") ".$__extra."
			ORDER BY a.tanggal_ut DESC
			LIMIT 7
		", self::$idsc );	
		} catch (\PDOException $e) {
			self::$__errorMsg = $e->getMessage(); return array();
		}
		
		if(isset($core[0]) && is_array($core) ){
			foreach($core as &$v){
				$v['isi_komentar'] = ($v['filternya'] != 0 ) ? str_replace(array('<','>'),array('&lt;','&gt;'), $v['isi_komentar'] ) : $v['isi_komentar'];
			}
		}

		return $core;
	}
	public static function addIdsc($data=array())
	{
		if(empty($data) OR !is_array($data))return false;
		self::$idsc=array_merge(self::$idsc,$data);
	}
}