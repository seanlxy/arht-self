<?php

$map_view       = '';
$route_map_data = json_decode($route_map_json, true);

if( !empty($route_map_data) )
{

	$tags_arr['scripts-load-top'] .= '<script src="https://maps.googleapis.com/maps/api/js"></script>';
	
	$jsVars['data']['cruiseKey'] = $cruise_token;

	$map_view = '<div id="map-canvas"></div>';
}
elseif($route_map_photo)
{
	$map_view = '<img src="'.$route_map_photo.'" alt="'.$heading.' route map" style="max-width:100%;">';
}

?>