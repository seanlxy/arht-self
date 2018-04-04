<?php

$destinations_details = fetch_row("SELECT d.`id`, d.`public_token`, d.`page_meta_data_id`, pmd.`heading`, pmd.`full_url`,
	pmd.`photo`, pmd.`title`, pmd.`meta_description`, pmd.`og_title`, pmd.`og_image`, pmd.`slideshow_id`,
	(SELECT COUNT(c.`id`)
		FROM `cruise` c
	 	LEFT JOIN `page_meta_data` pmd
    	ON(pmd.`id` = c.`page_meta_data_id`)
		WHERE c.`destination_id` = d.`id`
		AND pmd.`status` = 'A'
	) AS total_cruises
    FROM `destination` d
    LEFT JOIN `page_meta_data` pmd
    ON(pmd.`id` = d.`page_meta_data_id`)
    WHERE pmd.`status` = 'A'
    AND pmd.`url` = '{$segment1}'");


if( $destinations_details )
{


	$destination_id           = $destinations_details['id'];
	$destination_full_url     = $destinations_details['full_url'];
	$destination_public_token = $destinations_details['public_token'];
	$page_meta_data_id        = $destinations_details['page_meta_data_id'];
	$slideshow_id             = $destinations_details['slideshow_id'];
	$total_cruises            = $destinations_details['total_cruises'];
	$og_photo                 = ($destinations_details['og_image']) ? $destinations_details['og_image'] : $destinations_details['photo'];



	$tags_arr['title']    = $destinations_details['title'];
	$tags_arr['og_title'] = ($destinations_details['og_title']) ? $destinations_details['og_title'] : $destinations_details['title'];
	$tags_arr['og_image'] = ($og_photo) ? "{$htmlroot}{$og_photo}" : '';
	$tags_arr['mdescr']   = $destinations_details['meta_description'];
	$tags_arr['heading']  = $destinations_details['heading'];
	$tags_arr['content']  = get_content($page_meta_data_id);


	require_once 'cruise_list.php';

}


?>