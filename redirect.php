<?php
require_once ('utility/config.php');

if(!$c_Connection->Connect())
{
	echo "Database connection failed";
	exit;
}

function csv_to_array($filename='', $delimiter=',')
{
	if(!file_exists($filename) || !is_readable($filename))
		return FALSE;
	
	$header = NULL;
	$data = array();
	if (($handle = fopen($filename, 'r')) !== FALSE)
	{
		while (($row = fgetcsv($handle, 1000, $delimiter)) !== FALSE)
		{
			if(!$header)
				$header = $row;
			else
				$data[] = array_combine($header, $row);
		}
		fclose($handle);
	}
	return $data;
}

$arr_redirects = csv_to_array('redirects.csv');



if( !empty($arr_redirects) )
{
	$redirect_data = array();

	foreach ($arr_redirects as $key => $arr_redirect)
	{
		$old_url                            = urldecode($arr_redirect['old_url']);
		$new_url                            = urldecode($arr_redirect['new_url']);
		
		if($old_url != '' && $new_url != '')
		{
			$redirect_data[$key]                = array();
		
			$redirect_data[$key]['old_url']     = $old_url;
			$redirect_data[$key]['new_url']     = $new_url;
			$redirect_data[$key]['status_code'] = 301;
			$redirect_data[$key]['status']      = 'A';

			insert_row($redirect_data[$key], 'redirect');
		}
		
	}


}

?>