<?php
namespace App\Data;

use Saya\DB;

Class Datatable{
	static $listnya = false ;
	private $dataPost=false;
	private $listColumns=[];
	
	function __construct( Array $listColumns ){
		$this->listColumns = array_merge($this->listColumns, $listColumns);
	}
	
	public function get($table , $func = null ,$onlyone=false)
	{
		if(static::$listnya){
			return static::$listnya;
		}
		$response=array();
		$this->___getPost();
		
		DB::koneksi();
		$query2 = false;
		$query = DB::table( $table );
		if(is_callable($func)){
			$func($query);
		}
		
		if($onlyone){
			return $query->get();
		}		
		
		$response['recordsTotal'] = 0;
		if(empty($this->dataPost['totalrow'])){
			$query2 = DB::table( $table );
		}else{
			$response['recordsTotal'] = $this->dataPost['totalrow'] ;
		}
		if(!isset($this->dataPost['khusus'])){
			$query->limit($this->dataPost['length'] )->offset(($this->dataPost['start']));
		}
		//ORDER
		if(!empty($this->dataPost['order']) && is_array($this->dataPost['order'])){
			foreach($this->dataPost['order'] as $v){
				if(isset($v['column']) && isset($v['dir']) )
				{
					$v['dir'] = ($v['dir'] == 'asc') ? 'ASC' : 'DESC';
					$v['column'] = $this->___getColumn($v['column']);
					if($v['column']['orderable'] && !empty($v['column']['name'])){
						if(in_array($v['column']['name'],$this->listColumns))
						$query->orderBy($v['column']['name'], $v['dir'] );
					}
				}
			}
		}
		
		$query = $query->get();
		$response['data'] = empty($query) ? array() : $query ;
		
		$response['recordsTotal'] = $query2 ? $query2->count() : $response['recordsTotal'] ;
		$response['recordsFiltered'] = $response['recordsTotal'] ; //$this->dataPost['start'] + count($response['data'] ) ;
		return static::$listnya = $response;
	}
	private function ___getPost()
	{
		$this->dataPost = array(
			'start'=>(int) get_instance()->input->post('start'),
			'length'=> (int) get_instance()->input->post('length'),
			'search'=>get_instance()->input->post('search'), // array
			'columns'=>get_instance()->input->post('columns'),
			'order'=>get_instance()->input->post('order'),
			'cari'=>get_instance()->input->post('cari'),
			'totalrow'=>(int)get_instance()->input->post('totalrow')
		);
		if($this->dataPost['length'] > 100){
			$this->dataPost['length'] = 100;
		}
		$this->dataPost['length'] = empty($this->dataPost['length']) ? 10 : $this->dataPost['length'] ;
		
	}
	private function ___getColumn($nomor){
		$data=[
			'data'=>$nomor,
			'name'=>'',
			'searchable'=>false,
			'orderable'=>false,
			'search'=>['value'=>'','regex'=>false]
		];
		if(!empty($this->dataPost['columns'][$nomor])){
			$data = array_merge($data , $this->dataPost['columns'][$nomor]) ;
		}
		return $data;		
	}
	
}