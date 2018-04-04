<?php


if( $_GET['reload'] == '1' )
{
	header("Expires: Tue, 01 Jan 2000 00:00:00 GMT");
	header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
	header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
	header("Cache-Control: post-check=0, pre-check=0", false);
	header("Pragma: no-cache");
	header("Location: {$htmlroot}{$_SERVER['REDIRECT_URL']}");
	exit();
}

$compare_list_view = '';

if( $page_id == $page_compare_list->id )
{

	if( !empty( $shortlist ) )
	{
		require_once 'inc/views/list.php';
	}
	else
	{
		$tags_arr['heading'] = 'There are no cruises in your compare list';
	}

	$link_url = '';
	$link_cls = ' active';
}
else
{
	$link_url = ' href="'.$page_compare_list->full_url.'"';
	$link_cls = '';
}


$tags_arr['compare_pg_link_view'] = '<a'.$link_url.' class="btn compare-link'.$link_cls.( ($shortlist_count == 0) ? ' invisible' : '' ).'" data-label="'.$shortlist_count.'">Compare</a>';


$tags_arr['mod_view'] = $compare_list_view;

?>