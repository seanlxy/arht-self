<?php

$travel_styles_view = '';



if( $segment1 && !$segment2 )
{
	require_once 'views/travel_style.php';
}
else
{
	require_once 'views/list.php';
	
}

?>