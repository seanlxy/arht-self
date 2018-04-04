<?php 
	
	//  Initialize variables
	$form          = '';
	$form_is_valid = false;
	$output        = '';
	$error_message = '';

	//  Create post variables
	$first_name     = sanitize_input('first_name');
	$last_name      = sanitize_input('last_name');
	$email_address  = sanitize_input('email_address', FILTER_VALIDATE_EMAIL);
	$contact_number = sanitize_input('contact_number');
	$message        = sanitize_input('message');
	$captcha        = sanitize_input('captcha');


	// validate required fields
	if(isset($_POST['submit']))
	{
		//  Create error variables
		$first_name_error     = FALSE;
		$last_name_error      = FALSE;
		$email_address_error  = FALSE;
		$contact_number_error = FALSE;
		$captcha_error        = FALSE;

		
		// validate first name
		if(empty($first_name))
		{
			$first_name_error = TRUE;
			$error_message .= 'Please enter your First Name. <br>';
		}
		if(!empty($first_name) && !is_alpha($first_name))
		{
			$first_name_error = TRUE;			
			$error_message .= 'Please enter valid characters in your First Name. <br>';
		}
		
		// validate last name
		if (!empty($last_name) && !is_alpha($last_name)) {
			$last_name_error = TRUE;
			$error_message .= 'Please enter valid characters in your Last Name. <br>';
		}

		if (empty($last_name)) {
			$last_name_error = TRUE;
			$error_message .= 'Please enter your Last Name. <br>';
		}


		// validate email address
		if (empty($email_address) || !is_email($email_address)) {
			$email_address_error = TRUE;
			$error_message .= 'Please enter a valid email address. <br>';
		}


		// validate captcha
		if(empty($captcha))
		{
			$captcha_error = TRUE;
			$error_message .= 'Captcha is required.<br>';
		}elseif(hash('sha512', sha1(md5($captcha))) != $_SESSION['captcha'])
		{
			$captcha_error = TRUE;
			$error_message .= 'Invalid captcha provided.<br>';
		}
		

		if(!$first_name_error && !$last_name_error && !$email_address_error && !$captcha_error)
		{
			$form_is_valid = true;
		}
	}

?>