<?php

require_once ('../../utility/config.php'); ## System config file

if(!$c_Connection->Connect()) {

    echo "Database connection failed";
    exit;
}

$Message = "";
$c_Message	= $c_Connection->GetMessage();

$action = mysql_real_escape_string($_POST['action']);

switch ($action)
{

	case 'fetch-list':
		get_all_cruise_periods( sanitize_input('token') );
	break;
	case 'fetch-departure-details':
		get_departure_details( sanitize_input('token') );
	break;
	case 'fetch-dep-opts':
		get_departure_room_grades( sanitize_input('token') );
	break;
	case 'add-period':
		add_new_period( sanitize_input('token') );
	break;
	case 'change-cal':
		get_period_calendar( sanitize_input('date'), sanitize_input('token'), true );
	break;
	case 'remove-period':
		remove_period( sanitize_input('token') );
	break;
	case 'save-details':
		save_details( sanitize_input('token') );
	break;
	case 'save-dep-details':
		save_departure_details();
	break;
	case 'remove-dep':
		remove_departure_date( sanitize_input('token') );
	break;

}

function get_period_months( $start_date, $end_date )
{
	$data = array();

	if( $start_date && $start_date )
	{
		$current_date = new DateTime();
		$current_date->setTime(0,0,0);


		$start_date_obj = new DateTime( $start_date );
		$end_date_obj   = new DateTime( $end_date );
		
		$cal_start_date = clone $start_date_obj;
		$cal_end_date   = clone $end_date_obj;
		
		$cal_start_date->setDate($cal_start_date->format('Y'), $cal_start_date->format('m'), 1);
		$cal_end_date->setDate($cal_end_date->format('Y'), $cal_end_date->format('m'), $cal_end_date->format('t'));

		
		$interval    = new DateInterval('P1M');
		$month_range = new DatePeriod($cal_start_date, $interval, $cal_end_date);

		$has_selection = false;

		foreach( $month_range as $month_obj )
		{
			
			$is_selected = ( $month_obj->format('Y-m') === $current_date->format('Y-m') && !$has_selection ) ? true : '';

			if( $is_selected && !$has_selection  )
			{
				$has_selection = true;
			}

			$data['months'][] = array(
				'val' => $month_obj->format('Y-m-01'),
				'label' => $month_obj->format('F Y'),
				'isSelected' => $is_selected
			);
		}

	}

	return $data;
}

function get_cruise_departure_dates( $period_token, $departure_token = '' )
{
	$data = array();


	$sql = "SELECT cid.`public_token` AS token, cid.`label` AS title, cid.`start_date` AS startDate,
		IF(cid.`price`, REPLACE(cid.`price`, '.00', ''), '') AS rate,
		IF(cid.`on_special` = 'Y', TRUE, FALSE) AS onSale
		FROM `cruise_itinerary_departure` cid
		LEFT JOIN `cruise_pricing_period` cpp
		ON(cpp.`id` = cid.`cruise_pricing_period_id`)
		WHERE cpp.`public_token` = '{$period_token}'";

	$sql .= ( $departure_token )  ? " AND cid.`public_token` = '{$departure_token}' " : '';

	$sql .= " ORDER BY cid.`start_date` ";


	$departure_dates = fetch_all($sql);


	if( !empty($departure_dates) )
	{
		foreach ($departure_dates as $departure_date)
		{
			$data[$departure_date['startDate']] = $departure_date;
		}
	}


	return $data;
}

function get_period_calendar( $start_date, $period_token, $return_json = false )
{
	
	$days            = array();
	$departure_dates = get_cruise_departure_dates( $period_token );

	$period_data = fetch_row("SELECT `start_date`, `end_date` FROM `cruise_pricing_period` WHERE `public_token` = '{$period_token}' LIMIT 1");


	$min_date    = $period_data['start_date'];
	$max_date    = $period_data['end_date'];

	$start_date_obj = new DateTime($start_date);

	$min_date_obj   = new DateTime($min_date);
	$min_date_obj->setTime(0,0,0);

	$max_date_obj   = new DateTime($max_date);
	$max_date_obj->setTime(0,0,0);

	if( $start_date_obj && $max_date_obj )
	{

		$end_date_obj   = clone $start_date_obj;

		$end_date_obj->modify("+ {$end_date_obj->format('t')} days");
		
		$interval       = new DateInterval('P1D');
		$date_range     = new DatePeriod($start_date_obj, $interval, $end_date_obj);

		for($j=0; $j < $start_date_obj->format('w'); $j++) $days[] = array( 'date'=> '', 'label'=> '', 'cls' => 'invb', 'isDisabled' => true );

		$current_date = new DateTime();
		$current_date->setTime(0,0,0);

		foreach ($date_range as $i => $date_obj)
		{
			$sql_date = $date_obj->format('Y-m-d');

			$is_disabled = ( ($date_obj < $current_date) || ($date_obj > $max_date_obj) || ($date_obj < $min_date_obj));

		

			$days[] = array(
				'date' => $sql_date,
				'label' => $date_obj->format('j'),
				'departureToken' => (( isset($departure_dates[$sql_date]) ) ? $departure_dates[$sql_date]['token'] : ''),
				'onSale' => (( isset($departure_dates[$sql_date]) ) ? $departure_dates[$sql_date]['onSale'] : ''),
				'isDisabled' => $is_disabled,
				'cls' => ''
			);

		}


	}

	if( $return_json )
	{
		die( json_encode($days) );
	}
	else
	{
		return $days;
	}
}


function get_departure_details( $token )
{

	$departure_details = fetch_row("SELECT `public_token` AS depToken, 
		IF(`price`, REPLACE(`price`, '.00', ''), '') AS rate,
		( IF(`on_special` = 'Y', TRUE, FALSE) ) AS onSpecial
		FROM `cruise_itinerary_departure`
		WHERE `public_token` = '{$token}'
		LIMIT 1");


	die( json_encode($departure_details) );

}

function get_departure_room_grades( $token )
{

	$data = array();

	$departure_details = fetch_row("SELECT cid.`id`, cid.`cruise_id`, cid.`ship_id`,
		(SELECT COUNT(`id`)
			FROM `cruise_departure_has_ship_suite` 
			WHERE `cruise_departure_id` = cid.`id`
			AND `cruise_id` = cid.`cruise_id` )  AS options_count
		FROM `cruise_itinerary_departure` cid
		WHERE cid.`public_token` = '{$token}'
		LIMIT 1");


	$ship_id       = sanitize_input('ship', FILTER_VALIDATE_INT);
	$departure_id  = $departure_details['id'];
	$options_count = $departure_details['options_count'];

	$room_grades = array();

	$room_grades = fetch_all("SELECT ss.`id` AS gradeInd, ss.`heading` AS label,
		IF(ss.`price_from`, REPLACE(ss.`price_from`, '.00', ''), '') AS mainRate,
		(SELECT IF(`price`, REPLACE(`price`, '.00', ''), '')
			FROM `cruise_departure_has_ship_suite`
			WHERE `ship_id` = '{$ship_id}'
			AND `cruise_departure_id` = '{$departure_id}'
			AND `ship_suite_id` = ss.`id`
		LIMIT 1) AS rate
		FROM `ship_suite` ss
		WHERE ss.`status` != 'D'
		AND ss.`ship_id` = '{$ship_id}'
		ORDER BY ss.`rank`");

	$data['roomGrades'] = $room_grades;

	die( json_encode($data) );

}


function get_all_cruise_periods( $cruise_token )
{

	$data = array();


	$periods = fetch_all("SELECT cpp.`public_token` AS token, cpp.`start_date`, cpp.`end_date`, 
		DATE_FORMAT(cpp.`start_date`, '%d/%m/%Y') AS startDate, DATE_FORMAT(cpp.`end_date`, '%d/%m/%Y') AS endDate,
		IF(cpp.`price_from`, REPLACE(cpp.`price_from`, '.00', ''), '') AS rate
		FROM `cruise_pricing_period` cpp
		LEFT JOIN `cruise` c
		ON(c.`id` = cpp.`cruise_id`)
		WHERE c.`public_token` = '{$cruise_token}'
		ORDER BY cpp.`start_date`, cpp.`end_date`");

	if( !empty( $periods ) )
	{
		foreach ($periods as $i => $period)
		{

			$months_data = get_period_months( $period['start_date'], $period['end_date'] );

			$months      = $months_data['months'];

			$period['months']  = $months;


			unset($period['start_date']);
			unset($period['end_date']);

			$data[$i] = $period;

		}

	}

	die(json_encode($data));

}


function add_new_period( $cruise_token )
{
	$data     = array();
	$is_valid = false;
	$state    = '';
	$message  = '';

	$cruise_data = fetch_row("SELECT `id`, `standard_price` FROM `cruise` WHERE `public_token` = '{$cruise_token}' LIMIT 1");


	if( $cruise_data )
	{

	
		$start_date = sanitize_input('start-date');
		$end_date   = sanitize_input('end-date');

		$start_date_obj = ( validate_date($start_date, 'd/m/Y') ) ? DateTime::createFromFormat('d/m/Y', $start_date) : '';
		$end_date_obj   = ( validate_date($end_date, 'd/m/Y') )   ? DateTime::createFromFormat('d/m/Y', $end_date) : '';

		if( $start_date_obj && $end_date_obj )
		{
			$cruise_id    = $cruise_data['id'];
			$cruise_price = $cruise_data['standard_price'];

			$sql_start_date = $start_date_obj->format('Y-m-d');
			$sql_end_date   = $end_date_obj->format('Y-m-d');

			$is_existing = fetch_value("SELECT COUNT(`id`)
				FROM `cruise_pricing_period`
				WHERE `cruise_id` = '{$cruise_id}'
				AND ( ('{$sql_start_date}' BETWEEN `start_date` AND `end_date`) OR ( '{$sql_end_date}' BETWEEN `start_date` AND `end_date` ) )");


			if( $is_existing == 0 )
			{
				$pricing_period_data = array();

				$pricing_period_data['public_token'] = md5( sha1( create_rand_chars() ) );
				$pricing_period_data['start_date']   = $sql_start_date;
				$pricing_period_data['end_date']     = $sql_end_date;
				$pricing_period_data['price_from']   = $cruise_price;
				$pricing_period_data['status']       = 'A';
				$pricing_period_data['cruise_id']    = $cruise_id;


				$pricing_period_id = insert_row($pricing_period_data, 'cruise_pricing_period');

				if( $pricing_period_id )
				{
					$is_valid = true;
					$state    = 'success';
					$message  = 'New pricing period has been added successfully.';
				}
			}
			else
			{
				$state = 'danger';
				$message = 'Start or End date conflicts with existing pricing period. Please provide different date range.';
			}

		}
		else
		{
			$state = 'danger';
			$message = 'Invalid Start or End date provided.';
		}


	}
	else
	{
		$state = 'danger';
		$message = 'Invalid request.';
	}


	$data['isValid'] = $is_valid;
	$data['state']   = $state;
	$data['msg']     = $message;

	die(json_encode($data));

}


function remove_period( $period_token )
{

	global $rootfull;

	$data     = array();
	$is_valid = false;
	$state    = '';
	$message  = '';


	$period_id = fetch_value("SELECT `id` FROM `cruise_pricing_period` WHERE `public_token` = '{$period_token}' LIMIT 1");


	if( $period_id )
	{
		
		run_query("DELETE FROM `cruise_itinerary_departure` WHERE `cruise_pricing_period_id` = '{$period_id}'");
		run_query("DELETE FROM `cruise_pricing_period` WHERE `id` = '{$period_id}'");

		$is_valid = true;
		$state    = 'success';
		$message  = 'Pricing period has been removed successfully.';
		
	}


	$data['isValid'] = $is_valid;
	$data['state']   = $state;
	$data['msg']     = $message;

	die( json_encode($data) );
}


function get_valid_time( $time )
{
	$time_format = 'g:i A';

	$is_valid_format = validate_date($time, $time_format);

	if( !$is_valid_format ) return false;

	$time_obj = DateTime::createFromFormat( $time_format, $time);

	return $time_obj->format('H:i:00');
}


function save_details( $period_token )
{
	global $rootfull, $rootadmin;

	$data     = array();
	$is_valid = false;
	$state    = '';
	$message  = '';

	$period_id = fetch_value("SELECT `id` FROM `cruise_pricing_period` WHERE `public_token` = '{$period_token}' LIMIT 1");

	if( $period_id )
	{
		$date_format = 'd/m/Y';

		$start_date = sanitize_input( 'start-date' );
		$end_date   = sanitize_input( 'end-date' );
		$price      = sanitize_input( 'period-price', FILTER_VALIDATE_FLOAT );


		$start_date_obj = ( validate_date( $start_date, $date_format ) ) ? DateTime::createFromFormat($date_format, $start_date) : '';
		$end_date_obj   = ( validate_date( $end_date, $date_format ) ) ? DateTime::createFromFormat($date_format, $end_date) : '';

		if( $start_date_obj &&  $end_date_obj && $price )
		{
			$period_data = array();
			$period_data['start_date'] = $start_date_obj->format('Y-m-d');
			$period_data['end_date']   = $end_date_obj->format('Y-m-d');
			$period_data['price_from'] = $price;

			update_row($period_data, 'cruise_pricing_period', "WHERE `id` = '{$period_id}' LIMIT 1");

			$state   = 'success';
			$message = 'Changes has been saved successfully';

		}
		else
		{
			$state   = 'danger';
			$message = 'Start date, End date and Price is required';
		}

	}


	$data['isValid'] = $is_valid;
	$data['state']   = $state;
	$data['msg']     = $message;

	die(json_encode($data));
}

function save_departure_details( )
{
	global $rootfull, $rootadmin;

	$data     = array();
	$is_valid = false;
	$state    = '';
	$message  = '';

	$departure_price    = sanitize_input('dep-price', FILTER_VALIDATE_FLOAT);
	$on_special         = sanitize_input('on-special', FILTER_VALIDATE_INT);
	$ship_id            = sanitize_input('dep-ship', FILTER_VALIDATE_INT);
	$period_token       = sanitize_input('period-token');
	$departure_token    = sanitize_input('dep-token');
	$departure_date     = sanitize_input('dep-date');
	$departure_date_obj = new DateTime($departure_date);

	if( $departure_date_obj && $departure_price )
	{

		$period_data = fetch_row("SELECT `id`, `price_from`, `cruise_id` 
			FROM `cruise_pricing_period`
			WHERE `public_token` = '{$period_token}'
			LIMIT 1");

		if( $period_data )
		{

			$departure_date_data = array();

			$departure_date_data['label']                    = $departure_date_obj->format('j M');
			$departure_date_data['start_date']               = $departure_date_obj->format('Y-m-d');
			$departure_date_data['price']                    =  ($departure_price) ? $departure_price : $period_data['price_from'];
			$departure_date_data['on_special']               =  ($on_special == 1) ? 'Y' : 'N';
			$departure_date_data['ship_id']                  =  $ship_id;
			$departure_date_data['cruise_id']                =  $period_data['cruise_id'];
			$departure_date_data['cruise_pricing_period_id'] =  $period_data['id'];


			$departure_date_id = fetch_value("SELECT `id` FROM `cruise_itinerary_departure` WHERE `public_token` = '{$departure_token}' LIMIT 1");


			if( !$departure_date_id )
			{
				$departure_token = substr(sha1( md5( create_rand_chars() ) ), 0, 15);

				$departure_date_data['public_token'] = $departure_token;

				$departure_date_id = insert_row( $departure_date_data, 'cruise_itinerary_departure' );
			}
			else
			{
				update_row( $departure_date_data, 'cruise_itinerary_departure', "WHERE `id` = '{$departure_date_id}' LIMIT 1" );
			}

			run_query("DELETE FROM `cruise_departure_has_ship_suite` WHERE `cruise_departure_id` = '{$departure_date_id}'");

			if( $departure_date_id && $ship_id )
			{

				$room_grades_id          = $_POST['dep-room-grades-ind'];
				$room_grades_price       = $_POST['dep-room-grades-price'];
				


				for ($i=0; $i < count($room_grades_id); $i++)
				{ 
			
					$room_grade_price       = $room_grades_price[$i];
					$room_grade_id          = $room_grades_id[$i];


					$date_opts_data = array();

					$date_opts_data['price']               = $room_grade_price;
					$date_opts_data['ship_id']             = $ship_id;
					$date_opts_data['cruise_id']           = $period_data['cruise_id'];
					$date_opts_data['ship_suite_id']       = $room_grade_id;
					$date_opts_data['cruise_departure_id'] = $departure_date_id;


					insert_row($date_opts_data, 'cruise_departure_has_ship_suite');



				}


			}


			$is_valid = true;
			$state    = 'success';
			$message  = 'Changes has been saved successfully';

		}

	}

	
	$data['isValid'] = $is_valid;
	$data['state']   = $state;
	$data['msg']     = $message;
	$data['token']   = $departure_token;

	die(json_encode($data));
}

function remove_departure_date( $token )
{
	global $rootfull, $rootadmin;

	$data     = array();
	$is_valid = false;
	$state    = '';
	$message  = '';


	if( $token )
	{
		run_query("DELETE FROM `cruise_itinerary_departure` WHERE `public_token` = '{$token}' LIMIT 1");

		$is_valid = true;
		$state    = 'success';
		$message  = 'Departure date has been removed successfully';
	}

	
	$data['isValid'] = $is_valid;
	$data['state']   = $state;
	$data['msg']     = $message;
	$data['token']   = $departure_token;

	die(json_encode($data));
}


?>