<?php

$destinations_view = '';
$cruise_view       = '';

if( $segment1 && $segment2 )
{
	require_once 'views/cruise.php';
}
elseif( $segment1 && !$segment2 )
{
	require_once 'views/destination.php';
}
else
{
	require_once 'views/list.php';
}

?>