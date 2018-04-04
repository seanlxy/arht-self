<?php

require_once ('../../utility/config.php');                              ## System config file

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
		get_all_days( sanitize_input('token') );
	break;
	case 'add-day':
		add_new( sanitize_input('token') );
	break;
	case 'remove-day':
		remove_day( sanitize_input('token') );
	break;
	case 'save-details':
		save_details( sanitize_input('token') );
	break;

}


function update_cruise_arrival_departure( $cruise_id )
{
	if( !$cruise_id ) return false;

	$itinerary_data = fetch_row("SELECT 
        ( SELECT `port_id` FROM `cruise_itinerary_day` WHERE `number` = MIN(cid.`number`) AND `cruise_id` = '{$cruise_id}' LIMIT 1 )  AS depart_port_id,
        ( SELECT `port_id` FROM `cruise_itinerary_day` WHERE `number` = MAX(cid.`number`) AND `cruise_id` = '{$cruise_id}' LIMIT 1 )  AS arrival_port_id
        FROM `cruise_itinerary_day` cid
        WHERE cid.`cruise_id` = '{$cruise_id}'");

	$cruise_data = array();

    $cruise_data['arrival_port_id']  = $itinerary_data['arrival_port_id'];
    $cruise_data['depart_port_id']   = $itinerary_data['depart_port_id'];


    return update_row($cruise_data, 'cruise', "WHERE `id` = '{$cruise_id}' LIMIT 1");
}


function get_all_days( $cruise_token )
{

	$data = fetch_all("SELECT cid.`id` AS token, cid.`number` AS dayNum, IF(cid.`arrival_time`, TIME_FORMAT(cid.`arrival_time`, '%l:%i %p'), '' ) AS arrivalTime,
		IF(cid.`departure_time`, TIME_FORMAT(cid.`departure_time`, '%l:%i %p'), '') AS departureTime, cid.`heading` AS title, cid.`photo_path` AS photoPath,
		cid.`thumb_photo_path` AS photoThumbPath, cid.`description` AS details, cid.`port_id` AS portToken
		FROM `cruise_itinerary_day` cid
		LEFT JOIN `cruise` c
		ON(c.`id` = cid.`cruise_id`)
		WHERE c.`public_token` = '{$cruise_token}'
		ORDER BY cid.`number`");

	die(json_encode($data));

}


function add_new( $cruise_token )
{
	$data     = array();
	$is_valid = false;
	$state    = '';
	$message  = '';

	if( $cruise_token )
	{

		$cruise_id = fetch_value("SELECT `id` FROM `cruise` WHERE `public_token` = '{$cruise_token}' LIMIT 1");

		if( $cruise_id )
		{

			$day_data = array();

			$day_data['number']    = fetch_value("SELECT (COUNT(`number`)+1) FROM `cruise_itinerary_day` WHERE `cruise_id` = '{$cruise_id}'");
			$day_data['cruise_id'] = $cruise_id;

			$day_id = insert_row($day_data, 'cruise_itinerary_day');

			if( $day_id )
			{
				$is_valid = true;
				$message  = 'New day has been added successfully.';
				$state    = 'success';
			}

		}

	}


	$data['isValid'] = $is_valid;
	$data['state']   = $state;
	$data['msg']     = $message;

	die(json_encode($data));

}


function remove_day( $day_id )
{

	global $rootfull;

	$data     = array();
	$is_valid = false;
	$state    = '';
	$message  = '';


	$day_data = fetch_row("SELECT `id`, `cruise_id`
		FROM `cruise_itinerary_day`
		WHERE `id` = '{$day_id}'
		LIMIT 1");

	if( $day_data )
	{
		$day_id    = $day_data['id'];
		$cruise_id = $day_data['cruise_id'];

		$thumb_photo = fetch_value("SELECT `thumb_photo_path` FROM `cruise_itinerary_day` WHERE `id` = '{$day_id}' LIMIT 1");
		
		$thumb_path  = '{$rootfull}{$thumb_photo}';

		if( is_file($thumb_path) && !is_dir($thumb_path) )
		{
			unlink($thumb_path);
		}
		
		run_query("DELETE FROM `cruise_itinerary_day` WHERE `id` = '{$day_id}' LIMIT 1");


		update_cruise_arrival_departure( $cruise_id );

		$is_valid = true;
		$state    = 'success';
		$message  = 'Day has been removed successfully.';
		
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


function save_details( $day_id )
{
	global $rootfull, $rootadmin;

	$data     = array();
	$is_valid = false;
	$state    = '';
	$message  = '';

	$day_data = fetch_row("SELECT `id`, `cruise_id`
		FROM `cruise_itinerary_day`
		WHERE `id` = '{$day_id}'
		LIMIT 1");

	if( $day_data )
	{


		$day_id    = $day_data['id'];
		$cruise_id = $day_data['cruise_id'];

		$number         = sanitize_input('it-day-num', FILTER_VALIDATE_INT);
		$heading        = sanitize_input('itd-heading');
		$arrival_time   = sanitize_input('itd-arrival-time');
		$departure_time = sanitize_input('itd-departure-time');
		$port           = sanitize_input('itd-port', FILTER_VALIDATE_INT);
		$details        = sanitize_input('item-details');
		$photo          = sanitize_input('itd-photo');
		$thumb_photo    = sanitize_input('itd-thumb-photo');


		$photo_full_path     = "{$rootfull}{$photo}";
		$old_thumb_full_path = "{$rootfull}{$thumb_photo}";


		if( is_file($old_thumb_full_path) )
		{
			unlink($old_thumb_full_path);
		}


		if( is_file($photo_full_path) )
		{
			$upload_dir    = '/uploads'.date('/Y/m');
			$upload_dir_full_path = "{$rootfull}{$upload_dir}";


			$thumb_name = uniqid('thumb-');

	        $thumb_photo = "{$upload_dir}/{$thumb_name}.jpg";

	        require_once("$rootadmin/classes/class_imageresizer.php");

			$resizer_class = new images();

	        $resizer_class->resizer($upload_dir_full_path, $photo_full_path, 310, 185, $thumb_name);
		}
		else
		{
			$photo       = '';
			$thumb_photo = '';
		}

		$day_data = array();


		$valid_arrival_time   = get_valid_time($arrival_time);
		$valid_departure_time = get_valid_time($departure_time);

		$time_error = false;

		if( $arrival_time && !$valid_arrival_time )
		{
			$time_error = true;
		}


		if( $departure_time && !$valid_departure_time )
		{
			$time_error = true;
		}
	


		if( !$time_error )
		{

			$day_data['number']           = $number;
			$day_data['arrival_time']     = $valid_arrival_time;
			$day_data['departure_time']   = $valid_departure_time;
			$day_data['heading']          = $heading;
			$day_data['photo_path']       = $photo;
			$day_data['thumb_photo_path'] = $thumb_photo;
			$day_data['description']      = $details;
			$day_data['port_id']          = $port;

			update_row($day_data, 'cruise_itinerary_day', "WHERE `id` = '{$day_id}' LIMIT 1");

			update_cruise_arrival_departure( $cruise_id );

			$is_valid = true;
			$state    = 'success';
			$message  = 'Changes has been saved successfully.';

			$data['thumb'] = $thumb_photo;

		}
		else
		{
			$state    = 'danger';
			$message  = 'Invalid time format. Please type time in (h:mm AM/PM) format.';
		}
	}


	$data['isValid'] = $is_valid;
	$data['state']   = $state;
	$data['msg']     = $message;

	die(json_encode($data));
}

?>