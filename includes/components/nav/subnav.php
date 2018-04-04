<?php

$nav_view = '';

if( $absparent_id != $page_home->id )
{

$child_pages = fetch_all("SELECT gp.`id` AS page_id, pmd.`menu_label`, pmd.`title`,
    pmd.`full_url`, pmd.`url`
    FROM `general_pages` gp
    LEFT JOIN `page_meta_data` pmd
    ON(pmd.`id` = gp.`page_meta_data_id`)
    WHERE pmd.`status` = 'A'
    AND pmd.`menu_label` != ''
    AND gp.`parent_id` = '{$absparent_id}'
    ORDER BY pmd.`rank`");


if( !empty($child_pages) )
{

	$nav_view = '<nav id="tabbed-nav">';

	foreach ($child_pages as $child_page)
	{	


		$is_active = ( $option1 == $child_page['url'] ) ? ' class="active"' : '';

		$nav_view .= '<a href="'.$child_page['full_url'].'" title="'.$child_page['title'].'"'.$is_active.'>'.$child_page['menu_label'].'</a>';
	}

	$nav_view .= '</nav>';

}

}


$tags_arr['tabbed_nav_view'] = $nav_view;


?>