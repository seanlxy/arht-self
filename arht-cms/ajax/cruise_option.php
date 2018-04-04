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
		get_all( sanitize_input('token') );
	break;
	case 'add-option':
		add_new( sanitize_input('token') );
	break;
	case 'remove-option':
		remove_option( sanitize_input('token') );
	break;
	case 'save-details':
		save_details( sanitize_input('token') );
	break;

}


function get_all( $cruise_token )
{

	$data = fetch_all("SELECT co.`public_token` AS token, co.`heading` AS title, co.`photo_path` AS photoPath,
		co.`thumb_photo_path` AS photoThumbPath, co.`description` AS details, co.`pricing_notes` AS rateNotes,
		IF(co.`price_from`, REPLACE(co.`price_from`, '.00', ''), '') AS rate, co.`rank`
		FROM `cruise_option` co
		LEFT JOIN `cruise` c
		ON(c.`id` = co.`cruise_id`)
		WHERE c.`public_token` = '{$cruise_token}'
		ORDER BY co.`rank`");


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

			$option_data = array();

			$option_data['public_token'] = md5( sha1( create_rand_chars() ) );
			$option_data['cruise_id']    = $cruise_id;

			$option_id = insert_row($option_data, 'cruise_option');

			if( $option_id )
			{
				$is_valid = true;
				$message  = 'New option has been added successfully.';
				$state    = 'success';
			}

		}

	}


	$data['isValid'] = $is_valid;
	$data['state']   = $state;
	$data['msg']     = $message;

	die(json_encode($data));

}


function remove_option( $token )
{

	global $rootfull;

	$data     = array();
	$is_valid = false;
	$state    = '';
	$message  = '';

	if( $token )
	{
		

		$thumb_photo = fetch_value("SELECT `thumb_photo_path` FROM `cruise_option` WHERE `public_token` = '{$token}' LIMIT 1");

		$thumb_path = "{$rootfull}{$thumb_photo}";

		if( $thumb_photo && is_file($thumb_path) )
		{
			unlink($thumb_path);
		}




		run_query("DELETE FROM `cruise_option` WHERE `public_token` = '{$token}' LIMIT 1");

		$is_valid = true;
		$state    = 'success';
		$message  = 'Option has been removed successfully.';
		
	}


	$data['isValid'] = $is_valid;
	$data['state']   = $state;
	$data['msg']     = $message;

	die( json_encode($data) );
}

function save_details( $token )
{
	global $rootfull, $rootadmin;

	$data     = array();
	$is_valid = false;
	$state    = '';
	$message  = '';



	$option_data = fetch_row("SELECT `id`
		FROM `cruise_option`
		WHERE `public_token` = '{$token}'
		LIMIT 1");


	if( $option_data )
	{

		$option_id = $option_data['id'];

		$heading       = sanitize_input('opt-heading');
		$rate          = sanitize_input('opt-rate', FILTER_VALIDATE_FLOAT);
		$pricing_notes = sanitize_input('opt-pricing-notes');
		$rank          = sanitize_input('opt-rank', FILTER_VALIDATE_INT);
		$photo         = sanitize_input('opt-photo');
		$thumb_photo   = sanitize_input('opt-thumb-photo');
		$details       = sanitize_input('item-details');
		

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

		$option_data = array();



		$option_data['heading']          = $heading;
		$option_data['price_from']       = $rate;
		$option_data['pricing_notes']    = $pricing_notes;
		$option_data['rank']             = $rank;
		$option_data['photo_path']       = $photo;
		$option_data['thumb_photo_path'] = $thumb_photo;
		$option_data['description']      = $details;


		update_row($option_data, 'cruise_option', "WHERE `id` = '{$option_id}' LIMIT 1");

		$is_valid = true;
		$state    = 'success';
		$message  = 'Changes has been saved successfully.';

		$data['thumb'] = $thumb_photo;
	}


	$data['isValid'] = $is_valid;
	$data['state']   = $state;
	$data['msg']     = $message;

	die(json_encode($data));
}

?>