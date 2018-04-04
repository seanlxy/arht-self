<?php
session_start();   
require_once ('../utility/config.php');
// error_reporting(E_ALL);

if(!$c_Connection->Connect())
{
	echo "Database connection failed";
	exit;
}

if($debug)
{
	include_once $classdir.'/firephp/fb.php';
	FB::setEnabled($debug);
}

$request_type = ($_POST) ? $_POST : $_GET;

$action       = sanitize_var($request_type['action']);

switch($action)
{
	case 'sign-up':
		do_mailchimp_signup();
	break;
	case 'fetch-gallery':
		get_gallery_photos( sanitize_var($request_type['key']) );
	break;
	case 'fetch-compare-list':
		get_compare_tour_list();
	break;
	case 'fetch-map-styles':
		get_map_styles();
	break;
	case 'cruise-map':
		get_cruise_map( sanitize_var($request_type['key']) );
	break;
	case 'fetch-destination-map':
		get_destination_map( );
	break;
	case 'fetch-list':
		get_cruise_list();
	break;
	case 'get-ships':
		fetch_ship_list();
	break;
	case 'search-results':
		get_search_results();
	break;
	case 'fetch-travel-stlye-list':
		get_travel_stlye_cruise_list();
	break;
	case 'get-departure':
		fetch_cruise_departure_data( sanitize_var( $request_type['type'] ), sanitize_var( $request_type['cruiseKey'] ) );
	break;
	case 'get-itinerary-days':
		fetch_itinerary_days( sanitize_var( $request_type['date'] ), sanitize_var( $request_type['cruiseKey'] ) );
	break;
	case 'get-currency-rate':
		get_converted_currency_rate();
	break;
	case 'book-cruise':
		book_cruise();
	break;
	case 'toggle-booking-item':
		toggle_booking_item();
	break;
	case 'toggle-shortlist-item':
		toggle_shortlist_item( sanitize_var( $request_type['token'] ) );
	break;
	case 'fetch-room-grades':
		get_ship_suites( sanitize_var( $request_type['token'] ), sanitize_var( $request_type['dep-token'] ) );
	break;
	case 'destroy-comparison':
		destroy_shortlist( sanitize_var( $request_type['token'] ), sanitize_var( $request_type['dep-token'] ) );
	break;
	

}

function get_gallery_photos( $gallery_id )
{
	$data = array();
	$gallery_id = filter_var($gallery_id, FILTER_VALIDATE_INT);
	if( $gallery_id )
	{
		$data = fetch_all("SELECT `full_path` AS src, `thumb_path` AS msrc, `width` AS w, `height` AS h
			FROM `photo`
			WHERE `photo_group_id` = '{$gallery_id}'
			ORDER BY `rank`");
	}

	die( json_encode( $data ) );
} 

function do_mailchimp_signup()
{
	global $request_type, $classdir;

	$msg      = '';
	$msg_type = 'text-dange';
	$is_valid = false;

	$full_name     = '';
	$email_address = sanitize_one($request_type['email']);

	if(!empty($email_address))
	{
		if(filter_var($email_address, FILTER_VALIDATE_EMAIL))
		{
			include_once "{$classdir}/mail_chimp.php";

			$mailchimp_data = fetch_row("SELECT `mailchimp_api_key`, `mailchimp_list_id` FROM `general_settings` WHERE `id` = '1' LIMIT 1");

			if( $mailchimp_data )
			{

				$user_info = array('FNAME' => $full_name, 'LNAME' => '');

				$mc_api  = new MCAPI($mailchimp_data['mailchimp_api_key']);

				$list_id = $mailchimp_data['mailchimp_list_id'];

				if($mc_api->listSubscribe($list_id, $email_address, $user_info) === true)
				{
					$msg      = 'Success! Check your email to confirm sign up.';
					$msg_type = 'text-succes';
					$is_valid = true;
				}
				else
				{
					$msg = $mc_api->errorMessage;
				}
			}

		}
		else
		{
			$msg = 'Invalid email address provided.';
		}
	}
	else
	{
		$msg = 'Your name and email address is required.';
	}


	die( json_encode( array( 'msg' => $msg, 'type' => $msg_type, 'isValid' => $is_valid ) ) );
}



function get_compare_tour_list()
{
	
	global $shortlist_session_key;

	$data        = array();
	
	//  Requested data
	$shortlist       = $_SESSION[$shortlist_session_key];

	if( !empty($shortlist) )
	{

		$data = fetch_all("SELECT t.`id`, MD5(t.`id`) AS hIndex, t.`no_of_days` AS noOfDays, t.`no_of_nights` AS noOfNights,
			REPLACE(FORMAT(t.`distance`, 2), '.00', '') AS travelDistance, IF(`price_from`, `price_from`, 'POA') AS rateFrom,
			pmd.`menu_label` AS label, pmd.`thumb_photo` AS thumbPath, pmd.`title`, pmd.`full_url` AS uri,
			IF((al.`location_area_id` = dl.`location_area_id`), dla.`menu_label`, 'NZ North & South Island' ) AS areaCovered,
			pmd.`short_description` AS details
			FROM  `tour` t
			LEFT JOIN `page_meta_data` pmd
			ON(pmd.`id` = t.`page_meta_data_id`)
			LEFT JOIN `location` AS al
			ON(al.`id` = t.`arrival_location_id`)
			LEFT JOIN `location` AS dl
			ON(dl.`id` = t.`depart_location_id`)
			LEFT JOIN `location_area` AS dla
			ON(dla.`id` = dl.`location_area_id`)
			WHERE pmd.`status` = 'A'
			AND MD5(t.`id`) IN('".implode("','", $shortlist)."')
			ORDER BY pmd.`rank`");
		

	}

	die(json_encode($data));

}

function get_map_styles()
{
	global $incdir;

	$styles = file_get_contents("{$incdir}/map_styles.json");

	die($styles);
}

function get_cruise_map( $public_token )
{
	$data = fetch_row("SELECT `route_map_json` AS coords, `map_markers_json` AS markers
		FROM `cruise`
		WHERE `public_token` = '{$public_token}'
		LIMIT 1");

	die(json_encode($data));
}


function get_destination_map()
{
	$data = fetch_all("SELECT d.`latitude` AS lat, d.`longitude` AS lng,
		d.`formatted_address` AS infoBoxContent, pmd.`title`, pmd.`full_url` AS uri
		FROM `destination` d
		LEFT JOIN `page_meta_data` pmd
		ON(pmd.`id` = d.`page_meta_data_id`)
		WHERE pmd.`status` = 'A'
		AND d.`latitude` != ''
		AND d.`longitude` != ''");

	die(json_encode($data));
}


function get_search_results()
{

	global $request_type;


	$destination = sanitize_var( $request_type['destination'] );
	$departure   = sanitize_var( $request_type['departure'] );
	$offset_days = 30;
	$sort        = sanitize_var( $request_type['sort'] );

	$departure_date = ( validate_date($departure, 'd M Y') ) ? DateTime::createFromFormat('d M Y', $departure) : '';

	list($sort_by, $sort_type) = explode(':', $sort);

	$sort_type  = ( $sort_type === 'desc' ) ? 'DESC' : 'ASC';

	$sort_field = '';

	switch ($sort_by)
	{
		case 'featured':
			$sort_field = 'c.`is_featured`';
		break;
		case 'rate':
			$sort_field = 'c.`standard_price`';
		break;
		case 'duration':
			$sort_field = 'c.`no_of_days`';
		break;
		case 'date':
			$sort_field = 'pmd.`date_created`';
		break;
		case 'label':
			$sort_field = 'pmd.`menu_label`';
		break;
		default:
			$sort_field = 'pmd.`rank`';
		break;
	}


	$data = array();


	// Extra query conditions

	$ex_query = '';

	if( $destination )
	{
		$ex_query .= " AND d.`public_token` = '{$destination}' ";
	}


	if( $departure_date )
	{

		$current_date       = new DateTime('now');

		$sql_departure_date = ( $departure_date >= $current_date ) ? $departure_date->format('Y-m-d') : $current_date->format('Y-m-d');

		// $ex_query .= " AND (SELECT COUNT(`id`) FROM `cruise_itinerary_departure`
		// 	WHERE `cruise_id` = c.`id`
		// 	AND `start_date` >= '{$sql_departure_date}'
		// 	AND `start_date` >= CURRENT_DATE()) > 0 
		// AND (SELECT COUNT(`id`)
		// 		FROM `cruise_itinerary_day`
		// 		WHERE `cruise_id` = c.`id`) > 0 ";


		$ex_query .= " AND (SELECT COUNT(`id`) FROM `cruise_itinerary_departure`
			WHERE `cruise_id` = c.`id`
			AND `start_date` BETWEEN DATE_SUB( '{$sql_departure_date}' , INTERVAL {$offset_days} DAY) AND DATE_ADD( '{$sql_departure_date}' , INTERVAL {$offset_days} DAY)
			AND `start_date` >= CURRENT_DATE()
		) > 0 
		AND (SELECT COUNT(`id`)
				FROM `cruise_itinerary_day`
				WHERE `cruise_id` = c.`id`) > 0 ";
	}


	$cruises = fetch_all("SELECT c.`public_token` AS token, c.`no_of_days` AS noOfDays, c.`no_of_nights` AS noOfNights, 
		IF(c.`standard_price`, c.`standard_price`, 'POA') AS rate, c.`is_featured` AS featured, c.`on_special` AS onSpecial,
		pmd.`menu_label` AS label, pmd.`title`, pmd.`full_url` AS uri, pmd.`thumb_photo` AS thumbPhoto, pmd.`short_description` AS details,
		cr.`code` AS currencyCode, cr.`symbol` AS currencySymbol, dpmd.`menu_label` AS regionLabel, spmd.`menu_label` AS shipLabel
		FROM `cruise` c
		LEFT JOIN `page_meta_data` pmd
		ON(pmd.`id` = c.`page_meta_data_id`)
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
		{$ex_query}
		ORDER BY {$sort_field} {$sort_type}");

	$data['items'] = $cruises;
	$data['msg']   = (empty($cruises)) ? 'There are no expeditions that meet your criteria. You may be able to find your cruise through the Destination tabs.' : '';

	die( json_encode( $data ) );
}


function get_cruise_list()
{

	global $request_type;


	$max_items_to_load = MAX_LIST_ITEMS;
	// $max_items_to_load = 6;

	$destination_token = sanitize_var( $request_type['dst'] );
	$offset            = sanitize_var( $request_type['offset'] );
	$sort              = sanitize_var( $request_type['sort'] );

	list($sort_by, $sort_type) = explode(':', $sort);

	$sort_type  = ( $sort_type === 'desc' ) ? 'DESC' : 'ASC';

	$sort_field = '';

	switch ($sort_by)
	{
		case 'featured':
			$sort_field = 'c.`is_featured`';
		break;
		case 'rate':
			$sort_field = 'c.`standard_price`';
		break;
		case 'duration':
			$sort_field = 'c.`no_of_days`';
		break;
		case 'date':
			$sort_field = 'pmd.`date_created`';
		break;
		case 'label':
			$sort_field = 'pmd.`menu_label`';
		break;
		default:
			$sort_field = 'pmd.`rank`';
		break;
	}


	$data = array();


	if( $offset && $max_items_to_load && $destination_token )
	{
		$start = ($offset - 1) * $max_items_to_load;
		// $start = 0;

		$data = fetch_all("SELECT c.`public_token` AS token, c.`no_of_days` AS noOfDays, c.`no_of_nights` AS noOfNights, 
			IF(c.`standard_price`, c.`standard_price`, 'POA') AS rate, c.`is_featured` AS featured, c.`on_special` AS onSpecial,
			pmd.`menu_label` AS label, pmd.`title`, pmd.`full_url` AS uri, pmd.`thumb_photo` AS thumbPhoto, pmd.`short_description` AS details,
			cr.`code` AS currencyCode, cr.`symbol` AS currencySymbol, dpmd.`menu_label` AS regionLabel, spmd.`menu_label` AS shipLabel
			FROM `cruise` c
			LEFT JOIN `page_meta_data` pmd
			ON(pmd.`id` = c.`page_meta_data_id`)
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
			AND d.`public_token` = '{$destination_token}'
			ORDER BY {$sort_field} {$sort_type}
			LIMIT {$start}, {$max_items_to_load}");


	}

	die( json_encode( $data ) );
}



function fetch_ship_list()
{

	global $request_type;


	$max_items_to_load = SHIPS_PER_PAGE;

	$offset            = sanitize_var( $request_type['offset'] );

	$data = array();


	if( $offset && $max_items_to_load )
	{
		$start = ($offset - 1) * $max_items_to_load;

		$data = fetch_all("SELECT pmd.`menu_label` AS label, pmd.`title`, pmd.`full_url` AS uri,
			pmd.`thumb_photo` thumb, pmd.`short_description` AS details
		    FROM `ship` s
		    LEFT JOIN `page_meta_data` pmd
		    ON(pmd.`id` = s.`page_meta_data_id`)
		    WHERE pmd.`status` = 'A'
		    ORDER BY pmd.`menu_label`
			LIMIT {$start}, {$max_items_to_load}");


	}

	die( json_encode( $data ) );
}


function get_travel_stlye_cruise_list()
{

	global $request_type;


	$max_items_to_load = MAX_LIST_ITEMS;
	// $max_items_to_load = 6;

	$travel_style_token = sanitize_var( $request_type['ts'] );
	$offset            = sanitize_var( $request_type['offset'] );
	$sort              = sanitize_var( $request_type['sort'] );

	list($sort_by, $sort_type) = explode(':', $sort);

	$sort_type  = ( $sort_type === 'desc' ) ? 'DESC' : 'ASC';

	$sort_field = '';

	switch ($sort_by)
	{
		case 'featured':
			$sort_field = 'c.`is_featured`';
		break;
		case 'rate':
			$sort_field = 'c.`standard_price`';
		break;
		case 'duration':
			$sort_field = 'c.`no_of_days`';
		break;
		case 'date':
			$sort_field = 'pmd.`date_created`';
		break;
		case 'label':
			$sort_field = 'pmd.`menu_label`';
		break;
		default:
			$sort_field = 'pmd.`rank`';
		break;
	}


	$data = array();


	if( $offset && $max_items_to_load && $travel_style_token )
	{


		$travel_style_id = fetch_value("SELECT `id` FROM `travel_style` WHERE `public_token` = '{$travel_style_token}' LIMIT 1");

		$start = ($offset - 1) * $max_items_to_load;
		// $start = 0;

		$data = fetch_all("SELECT c.`public_token` AS token, c.`no_of_days` AS noOfDays, c.`no_of_nights` AS noOfNights, 
			IF(c.`standard_price`, c.`standard_price`, 'POA') AS rate, c.`is_featured` AS featured, c.`on_special` AS onSpecial,
			pmd.`menu_label` AS label, pmd.`title`, pmd.`full_url` AS uri, pmd.`thumb_photo` AS thumbPhoto, pmd.`short_description` AS details,
			cr.`code` AS currencyCode, cr.`symbol` AS currencySymbol, dpmd.`menu_label` AS regionLabel, spmd.`menu_label` AS shipLabel
			FROM `cruise` c
			LEFT JOIN `page_meta_data` pmd
			ON(pmd.`id` = c.`page_meta_data_id`)
			LEFT JOIN `cruise_has_travel_style` chts
			ON(chts.`cruise_id` = c.`id`)
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
			AND chts.`travel_style_id` = '{$travel_style_id}'
			ORDER BY {$sort_field} {$sort_type}
			LIMIT {$start}, {$max_items_to_load}");


	}

	die( json_encode( $data ) );
}




function fetch_cruise_departure_data( $type, $cruise_public_token )
{

	global $request_type;

	$data = array();
	
	if( $cruise_public_token )
	{
		$year = ( sanitize_var($request_type['year'], FILTER_VALIDATE_INT) );
		// $year = ( validate_date($year, 'Y') ) ? $year : '';

		$month = sanitize_var($request_type['month']);

		// $month = ( validate_date($month, 'Y-m') ) ? $month : '';

		if( $type === 'months' && $year )
		{
			$data = fetch_all("SELECT DISTINCT DATE_FORMAT(cid.`start_date`, '%Y-%m') AS value,
				DATE_FORMAT(cid.`start_date`, '%M') AS label
				FROM `cruise_itinerary_departure` cid
				LEFT JOIN `cruise` c
				ON(cid.`cruise_id` = c.`id`)
				WHERE c.`public_token` = '{$cruise_public_token}'
				AND YEAR(cid.`start_date`) = '{$year}'
				AND cid.`start_date` >= CURRENT_DATE()
				ORDER BY cid.`start_date`");

			
		}
		elseif( $type === 'dates' && $month )
		{

			$month_obj = DateTime::createFromFormat('Y-m', $month);

			$departure_dates = fetch_all("SELECT cid.`start_date` AS rdate, cid.`label`,
				IF(cid.`on_special` = 'Y', TRUE, FALSE) AS onSpecial,
				cid.`public_token` AS token
				FROM `cruise_itinerary_departure` cid
				LEFT JOIN `cruise` c
				ON(cid.`cruise_id` = c.`id`)
				WHERE c.`public_token` = '{$cruise_public_token}'
				AND DATE_FORMAT(cid.`start_date`, '%Y-%m') = '{$month}'
				AND cid.`start_date` >= CURRENT_DATE()
				ORDER BY cid.`start_date`");

			$data['departures'] = $departure_dates;
			$data['monthLabel'] = $month_obj->format('F');
			$data['yearLabel']  = $month_obj->format('Y');
		}
	}


	die( json_encode( $data ) );
}



function fetch_itinerary_days( $departure_date, $cruise_public_token )
{

	global $request_type;

	$data = array();
	
	if( $departure_date && $cruise_public_token )
	{

		$currency_token = sanitize_var( $request_type['currencyToken'] );

		$currency_data = fetch_row("SELECT c.`id`, c.`code`, c.`symbol`
			FROM `currency` c
			WHERE c.`public_token` = '{$currency_token}'
			AND c.`status` = 'A'
			LIMIT 1");


		$currency_symbol          = $currency_data['symbol'];
		$currency_code            = $currency_data['code'];
		$currency_id              = $currency_data['id'];


		$departure_date_data = fetch_row("SELECT cid.`public_token`, cid.`start_date`, cid.`cruise_id`,
			cid.`price`,  IF(cid.`on_special` = 'Y', TRUE, FALSE) AS onSpecial,
			IF( c.`currency_id` = '{$currency_id}', TRUE, FALSE ) AS isDefaultCurrency,
			(SELECT `rate`
				FROM `currency_conversion_rate`
				WHERE `currency_id` = c.`currency_id`
				AND `currency_id2` =  '{$currency_id}') AS conversion_rate
			FROM `cruise_itinerary_departure` cid
			LEFT JOIN `cruise` c
			ON(cid.`cruise_id` = c.`id`)
			WHERE c.`public_token` = '{$cruise_public_token}'
			AND cid.`start_date` = '{$departure_date}'
			LIMIT 1");



		if( !empty($departure_date_data) )
		{

			$days = fetch_all("SELECT cid.`number` AS num, IF(cid.`arrival_time`, TIME_FORMAT(cid.`arrival_time`, '%l:%i %p'), 'N/A') AS arrivalTime,
				IF(cid.`departure_time`, TIME_FORMAT(cid.`departure_time`, '%l:%i %p'), 'N/A') AS departureTime, cid.`heading` AS title,
				cid.`thumb_photo_path` AS photo, cid.`description` AS details, CONCAT( p.`name`, ', ', ct.`name` ) AS portName,
				DATE_FORMAT(DATE_ADD('{$departure_date_data['start_date']}', INTERVAL (cid.`number` - 1) DAY), '%W,%d %M %Y') AS rdate
				FROM `cruise_itinerary_day` cid
				LEFT JOIN `port` p
				ON(cid.`port_id` = p.`id`)
				LEFT JOIN `country` ct
				ON(ct.`id` = p.`country_id`)
				WHERE cid.`cruise_id` = '{$departure_date_data['cruise_id']}'
				ORDER BY cid.`number`");

			$data['token']          = $departure_date_data['public_token'];
			$data['rate']           = number_format(($departure_date_data['price'] * $departure_date_data['conversion_rate']), 2);
			$data['onSpecial']      = $departure_date_data['onSpecial'];
			$data['days']           = $days;
		}

	}


	die( json_encode( $data ) );
}


function get_converted_currency_rate()
{
	global $request_type;

	$currency_data = array();
	$state         = '';
	$notify_msg    = '';

	$currency_token = sanitize_var( $request_type['currency'] );

	// Cruise, Cruise Option, Ship Suite(Room Grades), Cruise Itinerary
	$type                   = sanitize_var( $request_type['type'] );
	
	$type_public_token      = sanitize_var( $request_type['typeToken'] );
	
	$base_rate              = sanitize_var( $request_type['bRate'] );
	
	$default_currency_token = sanitize_var( $request_type['currencyKey'] );
	$system_currency_id     = '';

	if( !$default_currency_token )
	{
		$system_currency_id = fetch_value("SELECT c.`id`
			FROM `general_settings` gs
			LEFT JOIN `currency` c
			ON(c.`id` = gs.`default_currency_id`)
			WHERE gs.`id` = '1'
			LIMIT 1");
	}



	if( $type && ( $type_public_token || $base_rate ) && $currency_token )
	{
		$currency_data = fetch_row("SELECT c.`id`, c.`code`, c.`symbol` AS default_currency_id,
			(SELECT `rate`
				FROM `currency_conversion_rate` 
				WHERE `currency_id` = IF(@val:=(SELECT `id` FROM `currency` WHERE `public_token` = '{$default_currency_token}' LIMIT 1 ), @val, '{$system_currency_id}' )
		AND `currency_id2` = c.`id` ) AS conversion_rate
		FROM `currency` c
		WHERE c.`public_token` = '{$currency_token}'
		AND c.`status` = 'A'
		LIMIT 1");

		if( !empty($currency_data) )
		{

			$currency_conversion_rate = $currency_data['conversion_rate'];

			if( $type_public_token && $type )
			{

				$field_name = 'price_from';
				$table_name = '';

				switch ( $type )
				{
					case 'cruise-option':
						$table_name = 'cruise_option';
					break;
					case 'room-grades':
						$table_name = 'ship_suite';
						
					break;
					case 'cruise-itin':
						$field_name = 'price';
						$table_name = 'cruise_itinerary_departure';
					break;
					
				}

				if( $table_name && $field_name )
				{

					$price = fetch_value("SELECT `{$field_name}`
						FROM `{$table_name}`
						WHERE `public_token` = '{$type_public_token}'
						LIMIT 1");


					$currency_data['rate'] = number_format(( $price * $currency_conversion_rate ), 2);
				}
			}
			elseif( $base_rate && $type )
			{
				$currency_data['rate'] = number_format(( $base_rate * $currency_conversion_rate ), 2);

			}


		}
	}


	die( json_encode($currency_data) );

}


function book_cruise()
{
	global $request_type, $htmlroot, $tmpldir, $moddir, $classdir, $htmladmin;

	$data = array();
	
	$state = '';
	$msg   = '';

	require_once "{$moddir}/bookings/main.php";


	$data['errors']  = $errors;
	$data['isValid'] = $form_is_valid;
	$data['state']   = $state;
	$data['msg']     = $msg;

	die( json_encode( $data ) );

}


function toggle_booking_item()
{
	global $request_type;

	$data = array();

	$type  = sanitize_var( $request_type['type'] );
	$token = sanitize_var( $request_type['token'] );

	$type  = ( $type == 'grade' ) ? 'grades' : (( $type === 'option' ) ? 'options' : '');

	if( $token && $type )
	{
		if( !isset($_SESSION[$type]) )
		{
			$_SESSION[$type] = array();
		}

		if( in_array($token, $_SESSION[$type]) )
		{
			$token_index = array_search($token, $_SESSION[$type]);

			unset($_SESSION[$type][$token_index]);

			$is_added = false;
			$title    = 'request booking';
		}
		elseif( !in_array($token, $_SESSION[$type]) )
		{
			$_SESSION[$type][] = $token;

			$is_added = true;
			$title    = 'remove';
		}

		
		$data['isAdded'] = $is_added;
		$data['title']   = $title;

	}

	die( json_encode($data) );
}


function destroy_shortlist()
{
	global $shortlist_session_key;


	$data     = array();
	$is_valid = true;

	unset( $_SESSION[$shortlist_session_key] );
	unset( $_COOKIE[$shortlist_session_key] );

	setcookie($shortlist_session_key, '', (time()-(60*60*24)), '/');
	
	$data['isValid'] = $is_valid;

	die( json_encode($data) );
	
}

function toggle_shortlist_item( $cruise_token )
{
	global $shortlist_session_key;

	$data = array();

	$is_vaid = false;

	if( $cruise_token && $shortlist_session_key )
	{

		if( !isset( $_SESSION[$shortlist_session_key] ) )
		{
			$_SESSION[$shortlist_session_key] = array();
		}

		$shortlist = $_SESSION[$shortlist_session_key];

		$shortlist_count = count($shortlist);

		if( !in_array($cruise_token, $shortlist) && $shortlist_count  < 2 )
		{
			$shortlist[] = $cruise_token;

			$state = 'added';
			$title = 'Remove from compare list.';
			$label = 'Remove';
			$is_vaid = true;

		}
		elseif( in_array($cruise_token, $shortlist) )
		{
			$index = array_search($cruise_token, $shortlist);

			unset($shortlist[$index]);

			$state = '';
			$title = 'Add to compare list.';
			$label = 'Compare';
			$is_vaid = true;

		}

		

		$data['state'] = $state;
		$data['title'] = $title;
		$data['label'] = $label;

		$_SESSION[$shortlist_session_key] = $shortlist;

		setcookie($shortlist_session_key, implode(',', $_SESSION[$shortlist_session_key]), (time()+(60*60*24*180)), '/');
	}

	$data['count']     = count($_SESSION[$shortlist_session_key]);
	$data['shortlist'] = $_SESSION[$shortlist_session_key];
	$data['isValid']   = $is_vaid;

	die(json_encode($data));
}


function get_ship_suites( $ship_token, $departure_date_token )
{
	global $shortlist_session_key;

	$data = array();

	$is_vaid = false;

	if( $ship_token && $departure_date_token )
	{

		$departure_date_id = fetch_value("SELECT `id` FROM `cruise_itinerary_departure` WHERE `public_token` = '{$departure_date_token}' LIMIT 1");

		$suites = fetch_all("SELECT ss.`public_token` AS token, ss.`no_of_sleeps` AS noOfSleeps, ss.`heading` AS label,
			ss.`pricing_notes` AS pricingNotes, ss.`description` AS details, ss.`gallery_id` AS galleryInd,
			REPLACE(ss.`size_in_sq_metre`, '.00', '') AS sizeSqMetre, REPLACE(ss.`size_in_sq_feet`, '.00', '') AS sizeSqFeet,
			IF(ss.`price_from`, FORMAT(REPLACE(ss.`price_from`, '.00', ''), 0), 'POA') AS mainRate,
			(SELECT `price`
				FROM `cruise_departure_has_ship_suite`
				WHERE `ship_id` = s.`id`
				AND `cruise_departure_id` = '{$departure_date_id}'
				AND `ship_suite_id` = ss.`id`
			LIMIT 1) AS rate,
			(SELECT `thumb_path`
				FROM `photo`
				WHERE `photo_group_id` = ss.`gallery_id`
				AND `thumb_path` != ''
				ORDER BY `rank`
				LIMIT 1
			) AS mainPhoto,
			(SELECT COUNT(`id`)
				FROM `photo`
				WHERE `photo_group_id` = ss.`gallery_id`
				AND `thumb_path` != ''
			) AS totalPhotos
			FROM `ship_suite` ss
			LEFT JOIN `ship` s
			ON(s.`id` = ss.`ship_id`)
			WHERE s.`public_token` = '{$ship_token}'
			AND ss.`status` = 'A'
			ORDER BY ss.`rank`");

	if( !empty( $suites ) )
	{
		$shortlisted_suites = ( isset($_SESSION['grades']) ) ? $_SESSION['grades'] : array();

		foreach ($suites as $i => $suite)
		{

			$suite_is_shortlisted = in_array($suite['token'], $shortlisted_suites);

			$suites[$i]['isShortlisted'] = $suite_is_shortlisted;

		}

		$is_vaid = true;


	}


	}

	$data['isValid']    = $is_vaid;
	$data['roomGrades'] = $suites;

	die(json_encode($data));
}


?>