<?php 

$form = '';
$amount = '';
$subscribe = '';
$terms = '';
$form_is_valid = FALSE;
$error_message = '';
$nzd25 = '';
$nzd50 = '';
$nzd100 = '';
$nzd200 = '';
// $nzd1000 = '';
$NZDother = '';
$visibility = 'invisible';


// $first_name 			= mysql_real_escape_string(strip_tags(trim(filter_input(INPUT_POST, 'first_name'))));
// $last_name 				= mysql_real_escape_string(strip_tags(trim(filter_input(INPUT_POST, 'last_name'))));
// $phone_number 			= mysql_real_escape_string(strip_tags(trim(filter_input(INPUT_POST, 'phone_number'))));
// $email	 				= mysql_real_escape_string(strip_tags(trim(filter_input(INPUT_POST, 'email'))));
// $address 				= mysql_real_escape_string(strip_tags(trim(filter_input(INPUT_POST, 'address'))));
// $suburb 				= mysql_real_escape_string(strip_tags(trim(filter_input(INPUT_POST, 'suburb'))));
// $city	 				= mysql_real_escape_string(strip_tags(trim(filter_input(INPUT_POST, 'city'))));
// $post_code 				= mysql_real_escape_string(strip_tags(trim(filter_input(INPUT_POST, 'post_code'))));
// $ref_number				= mysql_real_escape_string(strip_tags(trim(filter_input(INPUT_POST, 'ref_number'))));
// $other_amount			= mysql_real_escape_string(strip_tags(trim(filter_input(INPUT_POST, 'other_amount'))));

$first_name 			=  $_POST['first_name'];
$last_name 				=  $_POST['last_name'];
$phone_number 			=  $_POST['phone_number'];
$email	 				=  $_POST['email'];
$address 				=  $_POST['address'];
$suburb 				=  $_POST['suburb'];
$city	 				=  $_POST['city'];
$post_code 				=  $_POST['post_code'];
$ref_number				=  $_POST['ref_number'];
$other_amount			=  $_POST['other_amount'];
$postedCaptcha          = sanitize_input('captcha');
$captchaResponseToken   = filter_input(INPUT_POST, 'g-recaptcha-response');

$amount_error     = FALSE;
$first_name_error = FALSE;
$last_name_error  = FALSE;
$phone_error      = FALSE;
$email_error      = FALSE;
$address_error    = FALSE;
$suburb_error     = FALSE;
$city_error       = FALSE;
$postal_error     = FALSE;
$terms_error      = FALSE;
$captchaError     = true;

if (isset($_POST['submit'])) {
	
	$donation_type_id = ($_POST['donation_type_id']);
	$donation_type_name = ($_POST['meta_page_name']);

	if (isset($_POST['donation_radio']) && empty($other_amount)) {

		$amount = $_POST['donation_radio'];


	}elseif (isset($_POST['donation_radio']) && !empty($other_amount)) {

		$amount = $_POST['donation_radio'];


	}elseif (!empty($other_amount) && is_numeric($other_amount)) {
		$amount = $other_amount;

	}else{
		$amount_error = TRUE;
		$error_message .= 'Please Select or enter the amount to donate. <br>';

	}

	if (empty($first_name)) {
		$first_name_error = TRUE;
		$error_message .= 'Please enter your First Name. <br>';
	}

	if (!empty($first_name) && !is_alpha($first_name)) {
		$first_name_error = TRUE;
		$error_message .= 'Please enter valid characters in your First Name. <br>';
	}

	if (!empty($last_name) && !is_alpha($last_name)) {
		$last_name_error = TRUE;
		$error_message .= 'Please enter valid characters in your Last Name. <br>';
	}

	if (empty($last_name)) {
		$last_name_error = TRUE;
		$error_message .= 'Please enter your Last Name. <br>';
	}

	if (empty($phone_number) || !is_numeric($phone_number)) {
		$phone_error = TRUE;
		$error_message .= 'Please enter a valid Phone Number. <br>';
	}

	if (empty($email) || !is_email($email)) {
		$email_error = TRUE;
		$error_message .= 'Please enter a valid email address. <br>';
	}

	if (!empty($address) && !preg_match("/[a-zA-Z0-9]/", $address)) {
		$address_error = TRUE;
		$error_message .= 'Please enter valid characters in Address. <br>';
	}

	if (!preg_match("/[a-zA-Z0-9]/", $suburb)) {
		$suburb_error = TRUE;
		$error_message .= 'Please enter valid characters in Suburbs. <br>';
	}

	if (!empty($city) && !preg_match("/[a-zA-Z0-9]/", $city)) {
		$city_error = TRUE;
		$error_message .= 'Please enter valid characters in City. <br>';
	}

	if (!empty($post_code) && !is_numeric($post_code)) {
		$postal_error = TRUE;
		$error_message .= 'Please enter valid characters in Postcode. <br>';
	}

	if (isset($_POST['subscribe']) && $_POST['subscribe'] === 'Y') {
		$subscribe = 'Y';
	} else{
		$subscribe = 'N';

	}

	if (empty($ref_number)) {
		$ref_number = "";
	}

	if (isset($_POST['terms']) && $_POST['terms'] === 'Y') {
		$terms = 'Y';
	}else{
		$terms_error = TRUE;
		$error_message .= 'Please read and accept our Terms &amp; Conditions <br>';
	}


	if (!empty($amount)) {
		switch ($amount) {
			case '25':
				$nzd30 = 'checked';
				break;

			case '50':
				$nzd60 = 'checked';
				break;

			case '100':
				$nzd100 = 'checked';
				break;

			case '200':
				$nzd200 = 'checked';
				break;

			default:
				$NZDother = 'checked';
				$visibility = 'visible';
				break;
		}
	}

	if( !empty($captchaResponseToken) )
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,"https://www.google.com/recaptcha/api/siteverify");
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, "secret=6Lc2Tk8UAAAAANGKTDByOawBNZrdhGGb8guSajvp&response={$captchaResponseToken}&remoteip=".getenv('REMOTE_ADDR'));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$gRecaptchaResponseJson = curl_exec ($ch);
		curl_close ($ch);

		$gRecaptchaResponse = json_decode($gRecaptchaResponseJson, true);
		
		$captchaError  = (bool) !$gRecaptchaResponse['success'];
	}
	else
	{
		$captchaError  = TRUE;
		$error_message .= 'Please accept the provided captcha.<br>';
	}
	

	if (!$amount_error && !$first_name_error && !$last_name_error && !$email_error && !$address_error && !$suburb_error && !$city_error && !$postal_error && !$terms_error && !$captchaError) {

		$first_name 			= sanitize_var($first_name);
		$last_name 				= sanitize_var($last_name);
		$phone_number 			= sanitize_var($phone_number);
		$email	 				= sanitize_var($email);
		$address 				= sanitize_var($address);
		$suburb 				= sanitize_var($suburb);
		$city	 				= sanitize_var($city);
		$post_code 				= sanitize_var($post_code);
		$ref_number				= sanitize_var($ref_number);
		$other_amount			= sanitize_var($other_amount);

		$form_is_valid = TRUE;

	}

}

 ?>