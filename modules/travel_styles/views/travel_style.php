<?php

$travel_style_details = fetch_row("SELECT ts.`id`, ts.`public_token`, ts.`page_meta_data_id`, pmd.`heading`, pmd.`full_url`,
	pmd.`photo`, pmd.`title`, pmd.`meta_description`, pmd.`og_title`, pmd.`og_image`, pmd.`slideshow_id`,
	(SELECT COUNT(`cruise_id`)
		FROM `cruise` c
	 	LEFT JOIN `page_meta_data` pmd
    	ON(pmd.`id` = c.`page_meta_data_id`)
    	LEFT JOIN `cruise_has_travel_style` chts
    	ON(chts.`cruise_id` = c.`id`)
		WHERE chts.`travel_style_id` = ts.`id`
		AND pmd.`status` = 'A'
	) AS total_cruises
    FROM `travel_style` ts
    LEFT JOIN `page_meta_data` pmd
    ON(pmd.`id` = ts.`page_meta_data_id`)
    WHERE pmd.`status` = 'A'
    AND pmd.`url` = '{$segment1}'");


if( $travel_style_details )
{


	$travel_style_id           = $travel_style_details['id'];
	$travel_style_full_url     = $travel_style_details['full_url'];
	$travel_style_public_token = $travel_style_details['public_token'];
	$page_meta_data_id         = $travel_style_details['page_meta_data_id'];
	$slideshow_id              = $travel_style_details['slideshow_id'];
	$total_cruises             = $travel_style_details['total_cruises'];
	$og_photo                  = ($travel_style_details['og_image']) ? $travel_style_details['og_image'] : $travel_style_details['photo'];



	$tags_arr['title']    = $travel_style_details['title'];
	$tags_arr['og_title'] = ($travel_style_details['og_title']) ? $travel_style_details['og_title'] : $travel_style_details['title'];
	$tags_arr['og_image'] = ($og_photo) ? "{$htmlroot}{$og_photo}" : '';
	$tags_arr['mdescr']   = $travel_style_details['meta_description'];
	$tags_arr['heading']  = $travel_style_details['heading'];
	$tags_arr['content']  = get_content($page_meta_data_id);


	require_once 'cruise_list.php';

}


?>