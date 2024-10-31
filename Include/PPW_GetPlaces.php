<?php
	require_once('../../../../wp-load.php');
	
	ini_set('soap.wsdl_cache_enabled', 0);
	ini_set('soap.wsdl_cache_ttl', 900);
	ini_set('default_socket_timeout', 600);
	
	$load = array(
		'PP_Url'		=>	get_option('ppw_e_pp_url'),
		'PP_User'		=>	get_option('ppw_e_pp_username'),
		'PP_Password'	=>	get_option('ppw_e_pp_password')
	);
	
	require_once(PPW_Plugin_Dir . '/Include/classes/ParcelPerfect.php');
	
	$ParcelPerfect = new ParcelPerfect(json_decode(json_encode($load)));	
	$token = $ParcelPerfect->GenerateToken();
	
	$query = array(
				'name' => sanitize_text_field($_GET['q'])
			);

	$result = $ParcelPerfect->GetPlaceByName($query);
	
	$data = array();
	$json = array();
	
	foreach ($result->results as $row) {    
		$data[] = array("value"=>$row->place.'#-#'.$row->town.'#-#'.$row->pcode,"place"=>$row->place, "town"=>$row->town, "pcode"=>$row->pcode);
	}
	
	$json['count'] = count($data);
	$json['items'] = $data;
	
	echo json_encode($json);

?>