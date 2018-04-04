<?php

$ship_details = fetch_row("SELECT s.`id` AS ship_id, s.`public_token` AS ship_token, s.`no_of_crew_members`, s.`officers`, s.`no_of_guests`, 
	REPLACE(s.`weight_in_tons`, '.00', '') AS tonnage, REPLACE(s.`length_in_feet`, '.00', '') AS feet_length,
	REPLACE(s.`length_in_metre`, '.00', '') AS metre_length, REPLACE(s.`width_in_feet`, '.00', '') AS feet_width, REPLACE(s.`speed_in_knot`, '.00', '') AS knot_speed,
	s.`no_of_pax_deck`, s.`year_built`, s.`registry`, s.`deck_plan_path`, s.`vedio_url`, s.`features`, s.`dining`, pmd.`heading`, pmd.`full_url`, pmd.`description`,
	pmd.`menu_label` AS ship_name, pmd.`photo`, pmd.`title`, pmd.`meta_description`, pmd.`og_title`, pmd.`og_image`, pmd.`slideshow_id`, pmd.`gallery_id`,
	(SELECT COUNT(`id`)
		FROM `photo`
		WHERE `photo_group_id` = pmd.`gallery_id`
		AND `thumb_path` != ''
	) AS photos_count,
	(SELECT COUNT(`id`)
		FROM `ship_suite`
		WHERE `ship_id` = s.`id`
		AND `status` = 'A'
	) AS suites_count
    FROM `ship` s
    LEFT JOIN `page_meta_data` pmd
    ON(pmd.`id` = s.`page_meta_data_id`)
    WHERE pmd.`status` = 'A'
    AND pmd.`url` = '{$segment1}'");



if( $ship_details )
{

	extract($ship_details);

	
	$og_photo = ($og_image) ? $og_image : $photo;


	$tags_arr['title']    = $title;
	$tags_arr['og_title'] = ($og_title) ? $og_title : $title;
	$tags_arr['og_image'] = ($og_photo) ? "{$htmlroot}{$og_photo}" : '';
	$tags_arr['mdescr']   = $meta_description;
	$tags_arr['heading']  = $heading;


	require_once 'single_content.php';

	require_once 'tab_generator.php';
	
	require_once 'cruise_list.php';



}


?>