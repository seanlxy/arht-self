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
	case 'get-coords':
		get_latlng_of_address(mysql_real_escape_string($_POST['address']));
	break;
	case 'check-url':
		validate_url($_POST['url'], $_POST['currUrl']);
	break;
	case 'add-suite':
		add_ship_suite( sanitize_input('token') );
	break;
	case 'toggle-visibility':
		toggle_ship_suite_status( sanitize_input('token') );
	break;
	case 'fetch-ship-suites':
		get_ship_suites( sanitize_input('token') );
	break;
	case 'remove-suite':
		remove_ship_suite( sanitize_input('token') );
	break;
	case 'save-suite-details':
		save_ship_suite_details( sanitize_input('token') );
	break;
	case 'add-gallery-photo':
		add_gallery_photo( sanitize_input( 'gToken', FILTER_VALIDATE_INT ) );
	break;
	case 'remove-gallery-photo':
		remove_gallery_photo( sanitize_input('pToken', FILTER_VALIDATE_INT) );
	break;
	case 'make-it-photo-primary':
		make_itinerary_day_photo_primary( sanitize_one($_POST['pindex'], 'sqlsafe') );
	break;
	case 'update-gallery-photo-rank':
		update_gallery_photo_rank();
	break;
}

function get_latlng_of_address($address, $return_json = TRUE)
{
	if($address)
	{
		$address = str_replace(' ','+',str_replace("\n",'',str_replace("\r",'',$address)));
	
		$request_url = "http://maps.googleapis.com/maps/api/geocode/json?address=$address&sensor=true";

		$c = curl_init();
		curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($c, CURLOPT_URL, $request_url);
		curl_setopt($c, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
		$json = curl_exec($c);
		$err  = curl_getinfo($c,CURLINFO_HTTP_CODE);
		curl_close($c);
		$details = json_decode($json);

		$result = $details->results[0]->geometry->location;
		
		$coords = array(
			'lat'              => $result->lat,
			'lng'              => $result->lng,
			'formattedAddress' => $details->results[0]->formatted_address
		);

		if($return_json) die(return_json($coords));
		else return $coords;
	}
}

function validate_url($url, $current_url)
{
 	global $root;

	$url         = prepare_item_url(trim(filter_var($url, FILTER_SANITIZE_MAGIC_QUOTES)));
	$current_url = trim(filter_var($current_url, FILTER_SANITIZE_MAGIC_QUOTES));
	$valid       = true;
	$message     = '';

 	if($url)
 	{
 			
		if($valid)
		{
			// Check if url exists
			$sql = "SELECT `url` FROM `page_meta_data`
			WHERE `url` = '$url'
			AND `url` != '$current_url'
			AND `status` != 'D'";

			$valid = (fetch_value($sql)) ? false : true;

			$message = (!$valid) ? 'This page url already exists. Please enter another.' : '';
		}

		if($valid)
		{
			// Check if folder exists with the same name as the url
			$valid = (is_dir("$root/$url")) ? false : true;
			$message = (!$valid) ? 'This URL conflicts with the system. Please enter another.' : '';
		}
 	}
 	else
 	{
		$valid   = false;
		$message = 'Please provide valid URL';
 	}

 	die( json_encode( array('valid' => $valid, 'message'   => $message) ) );
}




###################### Ship functions ######################
function get_ship_suites( $ship_token )
{
	$data     = array();
	

	if( $ship_token )
	{

		$galleries_photos = array();
		

		$gsphotos = fetch_all("SELECT p.`id` AS mindex, p.`full_path` AS path, p.`thumb_path` AS thumbPath,
			p.`rank`, p.`photo_group_id`
			FROM `photo` p
			LEFT JOIN `ship_suite` ss
			ON(ss.`gallery_id` = p.`photo_group_id`)
			LEFT JOIN `ship` s
			ON(s.`id` = ss.`ship_id`)
			WHERE s.`public_token` = '{$ship_token}'
			ORDER BY p.`photo_group_id`, p.`rank`");


		if( !empty($gsphotos) )
		{
			foreach ($gsphotos as $gsphoto)
			{
				$photo_group_id = $gsphoto['photo_group_id'];

				if( !isset($galleries_photos[$photo_group_id]) )
				{
					$c = 0;
					$galleries_photos[$photo_group_id] = array();
				}

				unset($gsphoto['photo_group_id']);

				$gsphoto['ind'] = $c;

				$galleries_photos[$photo_group_id][] = $gsphoto;

				$c++;
			}
		}


		$suites = fetch_all("SELECT ss.`public_token` AS token, REPLACE(ss.`size_in_sq_metre`, '.00', '') AS sizeSqMtr,
			REPLACE(ss.`size_in_sq_feet`, '.00', '') AS sizeSqFt, ss.`no_of_sleeps` AS sleeps, ss.`heading` AS title,
			ss.`pricing_notes` AS pricingNotes, ss.`description` AS details, IF(ss.`status` = 'A', TRUE, FALSE) AS isPublished, 
			IF(ss.`rank` != 0, ss.`rank`, '') AS position, ss.`gallery_id` AS galleryToken,
			IF(ss.`price_from`, REPLACE(ss.`price_from`, '.00', ''), '') AS priceFrom
			FROM `ship_suite` ss
			LEFT JOIN `ship` s
			ON(s.`id` = ss.`ship_id`)
			WHERE s.`public_token` = '{$ship_token}'
			AND ss.`status` != 'D'
			ORDER BY ss.`rank`");


		if( !empty($suites) )
		{
			foreach ($suites as $i => $suite)
			{
				$gallery_id = $suite['galleryToken'];


				$data[$i] = $suite;
				$data[$i]['photos'] = ( isset($galleries_photos[$gallery_id]) ) ? $galleries_photos[$gallery_id] : array();
			}
		}

	}


	die(json_encode($data));

}


function add_ship_suite( $ship_token )
{
	$data     = array();
	$is_valid = false;
	$state    = '';
	$message  = '';

	if( $ship_token )
	{

		$ship_id = fetch_value("SELECT `id` FROM `ship` WHERE `public_token` = '{$ship_token}' LIMIT 1");

		if( $ship_id )
		{

			$name = "Ship {$ship_id} suite";

			$gallery_data = array();

			$gallery_data['name']                 = $name;
			$gallery_data['menu_label']           = $name;
			$gallery_data['type']                 = 'G';
			$gallery_data['show_in_cms']          = 'N';
			$gallery_data['show_on_gallery_page'] = 'N';

			$gallery_id = insert_row($gallery_data, 'photo_group');
			


			$suite_data = array();

			$suite_data['public_token'] = sha1( md5( create_rand_chars() ) );
			$suite_data['ship_id']      = $ship_id;
			$suite_data['gallery_id']   = $gallery_id;

			$suite_id = insert_row($suite_data, 'ship_suite');

			if( $suite_id )
			{
				$is_valid = true;
				$message  = 'New suite has been added successfully.';
				$state    = 'success';
			}

		}

	}


	$data['isValid'] = $is_valid;
	$data['state']   = $state;
	$data['msg']     = $message;

	die(json_encode($data));

}

function toggle_ship_suite_status( $token )
{
	$data     = array();
	$is_published = '';

	if( $token )
	{
		run_query( "UPDATE `ship_suite` SET `status` = IF(`status` = 'H', 'A', 'H') WHERE `public_token` = '{$token}' LIMIT 1" );

		$is_published = fetch_value("SELECT IF(`status` = 'A', TRUE, FALSE) AS isPublished
			FROM `ship_suite`
			WHERE `public_token` = '{$token}'
			LIMIT 1");
	}


	$data['isPublished'] = $is_published;

	die(json_encode($data));
}


function remove_ship_suite( $token )
{

	global $rootfull;

	$data     = array();
	$is_valid = false;
	$state    = '';
	$message  = '';

	if( $token )
	{
		$suite_data = fetch_row("SELECT `id`, `heading`, `gallery_id`
			FROM `ship_suite`
			WHERE `public_token` = '{$token}'
			LIMIT 1");


		if( $suite_data )
		{

			$suite_id      = $suite_data['id'];
			$suite_heading = $suite_data['heading'];
			$gallery_id    = $suite_data['gallery_id'];

			$gallery_photos = fetch_all("SELECT `thumb_path`
				FROM `photo`
				WHERE `photo_group_id` = '{$gallery_id}'");


			if( !empty( $gallery_photos ) )
			{

				foreach ($gallery_photos as $gallery_photo)
				{
					$thumb_full_path = "{$rootfull}{$gallery_photo['thumb_path']}";

					if( is_file($thumb_full_path) )
					{
						unlink($thumb_full_path);
					}
				}
				
				
			}

			run_query("DELETE FROM `photo` WHERE `photo_group_id` = '{$gallery_id}'");
			run_query("DELETE FROM `photo_group` WHERE `id` = '{$gallery_id}' LIMIT 1");
		}

		run_query("DELETE FROM `ship_suite` WHERE `id` = '{$suite_id}' LIMIT 1");

		$is_valid = true;
		$state    = 'success';
		$message  = 'Suite '.$suite_heading.' has been removed successfully.';
		
	}


	$data['isValid'] = $is_valid;
	$data['state']   = $state;
	$data['msg']     = $message;

	die( json_encode($data) );
}


function save_ship_suite_details( $ship_suite_token )
{
	$data     = array();
	$is_valid = false;
	$state    = '';
	$message  = '';

	if( $ship_suite_token )
	{

		
		$heading       = sanitize_input('item-heading');
		$sleeps        = sanitize_input('item-sleeps', FILTER_VALIDATE_INT);
		$size_metres   = sanitize_input('item-size-metres', FILTER_VALIDATE_FLOAT);
		$size_feet     = sanitize_input('item-size-feet', FILTER_VALIDATE_FLOAT);
		$details       = sanitize_input('item-details');
		$price_from    = sanitize_input('item-price-from', FILTER_VALIDATE_FLOAT);
		$pricing_notes = sanitize_input('item-pricing-notes');
		$rank          = sanitize_input('item-rank', FILTER_VALIDATE_INT);

		$suite_data = array();

		$suite_data['size_in_sq_metre'] = $size_metres;
		$suite_data['size_in_sq_feet']  = $size_feet;
		$suite_data['no_of_sleeps']     = $sleeps;
		$suite_data['heading']          = $heading;
		$suite_data['price_from']       = $price_from;
		$suite_data['pricing_notes']    = $pricing_notes;
		$suite_data['description']      = $details;
		$suite_data['rank']             = $rank;


		update_row($suite_data, 'ship_suite', "WHERE `public_token` = '{$ship_suite_token}' LIMIT 1");

		$is_valid = true;
		$state    = 'success';
		$message  = 'Changes has been saved successfully.';
	}


	$data['isValid'] = $is_valid;
	$data['state']   = $state;
	$data['msg']     = $message;

	die(json_encode($data));
}


function add_gallery_photo( $gallery_id )
{

	global $rootfull, $rootadmin;

	$data     = array();
	$is_valid = false;
	$state    = '';
	$message  = '';

	if( $gallery_id )
	{

		$image_path      = sanitize_input('path');
		$image_rank      = sanitize_input('order', FILTER_VALIDATE_INT);
		$gallery_id      = fetch_value("SELECT `id` FROM `photo_group` WHERE `id` = '{$gallery_id}' LIMIT 1");
		$image_full_path = "{$rootfull}{$image_path}";

	
		if( $gallery_id )
		{

			$image_details = getimagesize( $image_full_path );

			if( $image_details )
			{

				require_once("$rootadmin/classes/class_imageresizer.php");

				$resizer_class = new images();

				$upload_dir    = '/uploads'.date('/Y/m');
    			$upload_dir_full_path = "{$rootfull}{$upload_dir}";


    			$thumb_name = uniqid('img-');

		        $new_thumb_path = "{$upload_dir}/{$thumb_name}.jpg";

		        $resizer_class->resizer($upload_dir_full_path, $image_full_path, 490, 305, $thumb_name);


				$image_data = array();

				$image_data['full_path']      = $image_path;
				$image_data['thumb_path']     = $new_thumb_path;
				$image_data['rank']           = $image_rank;
				$image_data['width']          = $image_details[0];
				$image_data['height']         = $image_details[1];
				$image_data['photo_group_id'] = $gallery_id;

				$photo_id = insert_row($image_data, 'photo');

				$data = fetch_row("SELECT `id` AS mindex, `full_path` AS path, `thumb_path` AS thumbPath,
					`rank`
					FROM `photo`
					WHERE `id` = '{$photo_id}'
					LIMIT 1");

				if( $data )
				{
					$is_valid = true;

					$state = 'success';
					$message = 'New photo has been added successfully.';
				}

			}
			else
			{
				$state   = 'danger';
				$message = 'Invalid file supplied.';
			}
		}
		else
		{
			$state   = 'danger';
			$message = 'Invalid request.';
		}

	}


	$data['isValid'] = $is_valid;
	$data['state']   = $state;
	$data['msg']     = $message;

	die(json_encode($data));
}


function remove_gallery_photo( $photo_id )
{

	global $rootfull;

	$data     = array();
	$is_valid = false;
	$state    = '';
	$message  = '';

	if( $photo_id )
	{
		$photo_data = fetch_row("SELECT `id`, `thumb_path`
			FROM `photo`
			WHERE `id` = '{$photo_id}'
			LIMIT 1");


		if( $photo_data )
		{

			$photo_id   = $photo_data['id'];
			$thumb_path = $photo_data['thumb_path'];

			if( $thumb_path )
			{
				$thumb_full_path = "{$rootfull}{$thumb_path}";

				if(is_file($thumb_full_path))
				{
					unlink($thumb_full_path);
				}
			}

			run_query("DELETE FROM `photo` WHERE `id` = '{$photo_id}' LIMIT 1");
			$is_valid = true;

			$state   = 'success';
			$message = 'Photo has been removed successfully.';
		}
		else
		{
			$state   = 'danger';
			$message = 'Invalid request';
		}
	}


	$data['isValid'] = $is_valid;
	$data['state']   = $state;
	$data['msg']     = $message;

	die(json_encode($data));
}


function update_gallery_photo_rank()
{


	$data     = array();
	$is_valid = false;
	$state    = '';
	$message  = '';
	
	if( $_POST )
	{
		$gallery_id = sanitize_input('gToken', FILTER_VALIDATE_INT);

		if( $gallery_id )
		{
			$photo_ranks = $_POST["gallery_photo_rank"];
			$photo_pids  = $_POST["gallery_photo_id"];

			if( !empty($photo_pids) )
			{
				for ($i=0; $i < count($photo_pids); $i++)
				{ 
					update_row(array('rank' => $photo_ranks[$i]), 'photo', "WHERE `id` = '{$photo_pids[$i]}' AND `photo_group_id` = '{$gallery_id}' LIMIT 1");
				}

				$is_valid = true;
				$state    = 'success';
				$message  = 'Photo ranking has been updated.';

			}
		}
	}


	$data['isValid'] = $is_valid;
	$data['state']   = $state;
	$data['msg']     = $message;

	die(json_encode($data));
}


?>