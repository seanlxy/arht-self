<?php

require_once 'inc/vars.php';

require_once 'views/search_panel.php';

if( $page_id === $page_search->id )
{
	require_once 'inc/result.php';
}
elseif( isset($_SESSION['search_data']) )
{
	unset($_SESSION['search_data']);
}


?>