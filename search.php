<?php
header('Content-type: application/json; charset=UTF-8');
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Methods: POST, GET, UPDATE, DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

if(!isset($_POST['lat']) || !isset($_POST['lon']) )
	exit;

$lat = floatval($_POST['lat']);
$lon = floatval($_POST['lon']);


$url  = 'https://catalog.api.2gis.com/get_dist_matrix?key=<key>&mode=driving';
$data = array(
	"points" => array(
		array("lat" => 51.135942, "lon" => 71.422636), 
		array("lat" => $lat, "lon" => $lon)
	), 
	"sources" => array(0),
	"targets" => array(1),
	"type" => "shortest"
);  


$data_string = json_encode($data); 

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");                                                                     
curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);                                                                  
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);                                                                      
curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
    'Content-Type: application/json',                                                                                
    'Content-Length: ' . strlen($data_string))                                                                       
); 

$resp = curl_exec($ch);
curl_close($ch);
$json = json_decode($resp, 1);

//var_dump($json);
$distance = $json["routes"][0]["distance"];
$km = $distance/1000;
$km = round($km, 1);

if($km <= 1) {
	$cost = 500;
} else if($km <= 7) {
	$cost = 700;
} else if($km <= 8) {
	$cost = 800;
} else if($km <= 9) {
	$cost = 950;
} else if($km <= 10) {
	$cost = 1000;
} else if($km <= 14) {
	$cost = 1400;	
} else {
	$cost = 0;
}


echo json_encode(
	array(
		"delivery" => ($cost > 0) ? true : false,
		"a" => $distance, 
		"distance" => $km, 
		"cost" => $cost, 
		"lat"  => $lat, 
		"lon"  => $lon
	)
);
?>
