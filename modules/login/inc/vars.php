<?php

$is_logged_in = ( isset($_SESSION['login']) && $_SESSION['login']['is_logged'] === true );



$username = trim( sanitize_input( 'ag-username', FILTER_VALIDATE_EMAIL ) );
$password = trim( sanitize_input( 'ag-password' ) );

$form_is_valid = false;
$error_msg     = '';

if( $_POST['login'] == '1' )
{

	if( !empty($username) && !empty($password) )
	{
		$agent_details = fetch_row("SELECT `id`, `public_token`, `full_name`
			FROM `agent`
			WHERE `email_address` = '{$username}'
			AND `hashed_password` = SHA1('{$password}')
			AND `status` = 'A'
			LIMIT 1");

		if( !empty($agent_details) )
		{
			$_SESSION['login']['is_logged'] = true;
			$_SESSION['login']['token']     = $agent_details['public_token'];
			$_SESSION['login']['user']      = $agent_details['full_name'];


			run_query("UPDATE `agent` SET `last_login_date` = NOW() WHERE `id` = '{$agent_details['id']}' LIMIT 1");


			header("Location: {$htmlroot}{$page_agent_welcome->full_url}");
			exit();
		}
		else
		{
			$error_msg = 'Invalid Username and Password.';
		}

	}
	else
	{
		$error_msg = 'Username and Password is required.';

	}


	if( !$form_is_valid && $error_msg )
	{
		$error_msg = '<div class="alert alert-danger">'.$error_msg.'</div>';
	}


}

?>