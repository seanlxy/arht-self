<?php

$ships_view = '';



if( $segment1 )
{
	require_once 'views/single.php';
}
else
{
	require_once 'views/list.php';
}

?>