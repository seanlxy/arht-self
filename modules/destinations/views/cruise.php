<?php

$cruise_details = fetch_row("SELECT c.`page_meta_data_id`, c.`id` AS main_cruise_id, c.`public_token` AS cruise_token, c.`no_of_days`, c.`no_of_nights`, 
	IF(c.`standard_price`, c.`standard_price`, 'POA') AS rate, c.`is_featured`, c.`on_special`, c.`inclusions`, c.`booking_notes`, c.`route_map_json`, s.`id` AS ship_id, 
	c.`route_map_photo`, s.`public_token` AS ship_token, s.`no_of_crew_members`, s.`officers`, s.`no_of_guests`, REPLACE(s.`weight_in_tons`, '.00', '') AS tonnage,
	REPLACE(s.`length_in_feet`, '.00', '') AS feet_length, REPLACE(s.`length_in_metre`, '.00', '') AS metre_length, REPLACE(s.`width_in_feet`, '.00', '') AS feet_width,
	REPLACE(s.`speed_in_knot`, '.00', '') AS knot_speed, s.`no_of_pax_deck`, s.`year_built`, s.`registry`, pmd.`heading`, pmd.`full_url`, pmd.`menu_label` AS cruise_name,
	pmd.`photo`, pmd.`title`, pmd.`meta_description`, pmd.`og_title`, pmd.`og_image`, pmd.`slideshow_id`, pmd.`gallery_id` AS cruise_gallery_id, cr.`code` AS currency_code, 
	cr.`public_token` AS cruise_currency_token, cr.`symbol` AS currency_symbol, dpmd.`menu_label` AS region_label, spmd.`menu_label` AS ship_label, spmd.`thumb_photo` AS ship_thumb_photo, 
	spmd.`description` AS ship_details, dp.`name` AS departure_port, dpc.`name` AS departure_port_country, ap.`name` AS arrival_port,  apc.`name` AS arrival_port_country, 
	(SELECT COUNT(`id`) FROM `photo` WHERE `photo_group_id` = pmd.`gallery_id` AND `thumb_path` != '' ) AS photos_count,
	(SELECT COUNT(`id`) FROM `ship_suite` WHERE `ship_id` = s.`id` AND `status` = 'A' ) AS suites_count,
	(SELECT COUNT(`id`) FROM `cruise_itinerary_day` WHERE `cruise_id` = c.`id` ) AS total_itinerary_days,
	(SELECT COUNT(`id`)
		FROM `cruise_pricing_period`
		WHERE `status` = 'A'
		AND `cruise_id` = c.`id`
		AND `end_date` >= CURRENT_DATE() ) AS total_periods,
	IF(dpc.`id` != apc.`id`, TRUE, FALSE ) AS show_country_label
    FROM `cruise` c
    LEFT JOIN `page_meta_data` pmd
    ON(pmd.`id` = c.`page_meta_data_id`)
    LEFT JOIN `port` dp
    ON(dp.`id` = c.`depart_port_id`)
    LEFT JOIN `country` dpc
    ON(dpc.`id` = dp.`country_id`)
    LEFT JOIN `port` ap
    ON(ap.`id` = c.`arrival_port_id`)
    LEFT JOIN `country` apc
    ON(apc.`id` = ap.`country_id`)
    LEFT JOIN `currency` cr
	ON(cr.`id` = c.`currency_id`)
	LEFT JOIN `destination` d
	ON(d.`id` = c.`destination_id`)
	LEFT JOIN `page_meta_data` dpmd
	ON(dpmd.`id` = d.`page_meta_data_id`)
    LEFT JOIN `ship` s
    ON(s.`id` = c.`ship_id`)
    LEFT JOIN `page_meta_data` spmd
	ON(spmd.`id` = s.`page_meta_data_id`)
    WHERE pmd.`status` = 'A'
    AND pmd.`url` = '{$segment2}'
    LIMIT 1");


if( $cruise_details )
{

	
	extract($cruise_details);

	$og_photo = ($og_image) ? $og_image : $photo;

	$tags_arr['title']    = $title;
	$tags_arr['og_title'] = ($og_title) ? $og_title : $title;
	$tags_arr['og_image'] = ($og_photo) ? "{$htmlroot}{$og_photo}" : '';
	$tags_arr['mdescr']   = $meta_description;
	$tags_arr['heading']  = $heading;
	$tags_arr['content']  = '';

	$jsVars['data']['cruiseKey']   = $cruise_token;
	$jsVars['data']['currencyKey'] = $cruise_currency_token;


	require_once 'cruise_content.php';

	$tags_arr['script-ext'] .= '<script async src="'.get_file_path('/assets/js/cruise.js').'"></script>';


}


?>