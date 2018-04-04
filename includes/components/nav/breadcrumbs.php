<?php

$tags_arr['breadcrumbs'] = '';
$breadcumbs = '';

if($page == 'home')
{
	$tags_arr['bdc_active_home'] = ' class="active"';
	$tags_arr['bdc_href_home'] = '';
}
else
{
	$tags_arr['bdc_active_home'] = '';
	$tags_arr['bdc_href_home'] = ' href="/"';
}


function generate_breadcrumbs($id, $current_page)
{
	global $tags_arr, $option1, $option2, $option3, $option4;

	$output = '';
	$icon = '<i class="fa  fa-angle-right"></i>';

	if($id != 1)
	{
		$sql = "SELECT page_id, page_menu, page_title, page_parentid, page_url, page_breadcrumb_menu
		FROM general_pages
		WHERE page_id ='$id'
		LIMIT 1";

		$result = fetch_row($sql);
		if(is_array($result)) extract($result);
		$page_menu = ($page_breadcrumb_menu) ? $page_breadcrumb_menu : $page_menu;

		if($current_page == $page_url)
		{
			$bdc_active = ' class="active"';
			$bdc_href = '';
		}
		else
		{
			
			$bdc_active = '';
			$bdc_href = ' href="/'.$page_url.'"';
		}

	    if($result['page_parentid'] > 0 && !$has_suburbs && !$has_prop_cats)
	    {
	    	$output .= generate_breadcrumbs($result['page_parentid'], $current_page, $has_suburbs);
			$output .= '<li'.$bdc_active.'>'.$icon.'<a'.$bdc_href.' title="'.$page_title.'">'.$page_menu.'</a></li>';
	    }
	    else
	    {
			$output .= '<li'.$bdc_active.'>'.$icon.'<a'.$bdc_href.' title="'.$page_title.'">'.$page_menu.'</a></li>';
	    }
    }
 

    return $output;
}

$tags_arr['breadcrumbs'] = '';

if($page_id != '1')
{
$tags_arr['breadcrumbs'] .= <<<  H

<div class="breadcrumbs hidden-xs hidden-sm">
    <ul>
        <li{$tags_arr['bdc_active_home']}><a{$tags_arr['bdc_href_home']}>Home</a></li>

H;

$page_modules = fetch_value("SELECT GROUP_CONCAT(DISTINCT `mod_id`) FROM `module_pages` WHERE `page_id` = '$page_id'");

$tags_arr['breadcrumbs'] .= generate_breadcrumbs($page_id,$page);


$tags_arr['breadcrumbs'] .= <<<  H

 	</ul>
	<div class="clearfix"></div>
</div><!--end of breadcrumbs-->

H;
}
?>