<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use Saya\DB;
use Saya\Validasi;
use App\Api\Respond as Resp;
use App\Api\Config;

class ResepMakanan extends CI_Model {
	private $table = 'master_resep';
	function __construct() {
		parent::__construct();  
	}
	
	public function run()
	{
		try{
			$method = $this->uri->segment( Config::init()->url() );
			switch( $method ){
				case 'permit' : $this->___isAllowed(); break;
				case 'insert_update_delete' : $this->___DB(); break;
				case '': $this->___getData(); break;
				default : 
					throw new Exception(' Method Not Found !!! '.$method );
					break;
			}
		}catch(PDOException $e){
			throw new Exception( $e->getMessage() );
		}
	}
	private function ___isAllowed($return = false ){
		$data =[
			'success'=>true,
			'permission'=>[
				'read'=>false,
				'create'=>true,
				'update'=>true,
				'delete'=>true
			]
		];
		
		if(!$return){
			Resp::set($data );
		}else{
			return $data;
		}
		
	}
	private function ___getData(){
		$dataTable = new App\Data\Datatable(['id_resep']);
		$idSystem =App\Api\Accesstoken::init()->userdata('id_system');
		$idResep = $this->input->post('id_resep');
		if(empty($idResep)){
			$data = $dataTable->get($this->table,function ($query) use ($idSystem){
				$query->select('id_resep','nama_resep');
			});
		}else{
			$data = $dataTable->get($this->table,function ($query) use ($idSystem,$idResep){
				$query->select('id_resep','nama_resep')
					   ->where('id_resep',$idResep);
			},true);
			$data=[ 'data'=>empty($data) ? [] : $data ];
		}
		$data['permission']=$this->___isAllowed(true)['permission'];
		Config::init()->noHtml(true);
		Resp::set( $data);
	}
	private function ___DB(){
		$dataPost=[
			'id_resep'=> $this->input->post('id_resep'), 
			'nama_resep'=> $this->input->post('nama_resep'),
			'mode'=> $this->input->post('mode')
		];
		
		$data=[];
		if(empty($dataPost['id_resep'])){
			if(empty($dataPost['nama_resep'])){
				throw new Exception('Nama Resep Tidak Boleh Kosong');
			}
			$this->___getPermission('create');
			$query = $this->___getQuery();
			
			unset($dataPost['mode'],$dataPost['id_resep']);
			$query->insert($dataPost);
			$data=[
				'success'=>true,'message'=>'Data Berhasil Ditambahkan !!!','total'=>1
			];
		}else{
			switch($dataPost['mode']){
				case 'update':
					if(empty($dataPost['nama_resep'])){
						throw new Exception('Nama Resep Tidak Boleh Kosong');
					}
					$this->___getPermission('update');
					unset($dataPost['mode']);
					$query = $this->___getQuery();
					$query = $query->where('id_resep',$dataPost['id_resep'])
								   ->update($dataPost);
					if($query){
						  $data=['success'=>true,'message'=>'Data Berhasil DiUpdate','total'=>0];
					}else throw new Exception('Update Data Gagal');
					
					break;
				case 'delete':
					$this->___getPermission('delete');
					$query = $this->___getQuery();
					$query = $query->where('id_resep',$dataPost['id_resep'])
						   ->delete();
					if($query){
						 $data=['success'=>true,'message'=>'Data Berhasil Dihapus','total'=>-1];
					}else throw new Exception('Hapus Data Gagal');
					break;
				default: throw new  Exception('Model Not Found'); break;
			}
		}
		Resp::set( $data);
	}
	private function ___getPermission($pilihan){
		if( empty($this->___isAllowed(true)['permission'][$pilihan]) ){
			throw new Exception('Permission not Granted !!!');
		}
	}
	private function ___getQuery()
	{
		DB::koneksi();
		return DB::table(  $this->table );
	}
}