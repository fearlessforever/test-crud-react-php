<?php defined('BASEPATH') OR exit('No direct script access allowed');
/*
	api_access_list ini adalah list array dari url beserta method nya yang ada.
	Misal : example.com/api/{key}
			di Controller akan cross check jika value {key} ada dalam array api_access_list
	api_access_list (
		{key}=>array( 'accesstoken'=>{set TRUE jika butuh access token}, 'model'=>{nama_file_modal} )
	)
*/
$config['api_access_list'] = array(
	'dashboard'=>array( 'accesstoken'=>false, 'model'=>'Dashboard/Dashboard' ) 
	,'master-resep-makanan'=>array( 'accesstoken'=>false, 'model'=>'Master/ResepMakanan' ) 
	,'master-resep-makanan2'=>array( 'accesstoken'=>false, 'model'=>'Master/ResepMakanan2' ) 
);

$config['api_time'] = 3600; // 1 jam = 3600
$config['api_version'] = [
	'v1'
];
$config['api_version_default'] ='v1' ;
// Set 0 Untuk Unlimited
$config['api_response_limit'] = 600000; 
$config['api_start_from'] = 1; 