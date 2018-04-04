<?php

require_once ('../../utility/config.php');                              ## System config file
if(!$c_Connection->Connect()) {

    echo "Database connection failed";
    exit;
}
$Message = "";
$c_Message	= $c_Connection->GetMessage();

$action = mysql_real_escape_string($_REQUEST['action']);

switch ($action)
{
	case 'get-coords':
		get_latlng_of_address(mysql_real_escape_string($_POST['address']));
	break;
	case 'page-source':
		get_page_source($_GET['url']);
	break;
	case 'remove-photo':
		remove_photo(mysql_real_escape_string($_POST['index']));
	break;
}
function remove_photo($id)
{
	global $root, $rootfull;

	$data = array('status' => FALSE);
	if($id)
	{
		$photo_details = fetch_row("SELECT `path`, `media_id` FROM `media_photo` WHERE SHA1(`id`) = '$id' LIMIT 1");
		$photo_path  = $photo_details['path'];
		$media_id = $photo_details['media_id'];

	
		if($photo_path)
		{
			$photo_dirs = array('large', 'medium','small','thumb');
			for($i=0;$i<count($photo_dirs);$i++)
			{
				$dir = "$rootfull/media_photos/p{$media_id}/{$photo_dirs[$i]}";
				if(is_dir($dir))
				{
					if(file_exists("$dir/$photo_path"))
					{
						if(unlink("$dir/$photo_path"))
						{
							$data['status'] = TRUE;
							run_query("DELETE FROM `media_photo` WHERE SHA1(`id`) = '$id'");
						}
						else
						{
							$data['status'] = FALSE;
						}
					}
					else
					{
						$data['status'] = TRUE;
						run_query("DELETE FROM `media_photo` WHERE SHA1(`id`) = '$id'");
					}
				}
			}

			

		}
	}

	die(return_json($data));
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


function get_page_source($url)
{
	
	$url = urldecode($_GET['rurl']);
	$arr = array();
	$status = 'FAIL';
	if($url)
	{
		
		$source = file_get_contents($url);
		if($source)
		{
			$status = 'OK';
		}
	}
	else
	{
		$source = '';
	}

	$arr['status'] = $status;
	$arr['source'] = $source;
	die(json_encode($arr));
}

?>