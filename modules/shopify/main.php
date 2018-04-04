<?php
require( 'libraries/class_shopify.php');

$tags_arr['body_cls'] .= ' shop-page';

$shopify_query = fetch_row("SELECT `shopify_url`, `shopify_api_key`, `shopify_api_password`
    FROM `spty_settings`
    WHERE `id` = '1'
    LIMIT 1");

extract($shopify_query);

$params = (object) [
	'url'          => $shopify_url,
	'key'          => $shopify_api_key,
	'password'     => $shopify_api_password 
];

$shop = new Shopify($params);

$jsVars['shopify_url'] = $shopify_url; 

if(isset($_GET['a'])){
 
 $identifier = $_GET['a'];
 $collection = $shop->getCollectionId( $identifier );

 if(!empty($collection)) {

 	$collection_id    = $collection['selections']['collection_id'];
 	$collection_title = $collection['selections']['collection_title'];

 	require_once "inc/collection_catalog.php";
 }

} else{

 require_once "inc/collections.php";
 
}
?>