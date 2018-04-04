<?php

if( $_POST && $page_id === $page_search->id )
{
	$_SESSION['search_data'] = $_POST;

	header("Location: {$htmlroot}{$page_search->full_url}");
	exit();
}


$searched_destination_id = '';
$searched_date           = '';
$searched_no_of_nights   = '';


if( isset($_SESSION['search_data']) )
{

	$searched_destination_id = $_SESSION['search_data']['destination'];
	$searched_date           = $_SESSION['search_data']['departure'];
}


?>