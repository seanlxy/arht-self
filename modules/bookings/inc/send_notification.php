<?php


$system_settings = fetch_row("SELECT `company_name`, `phone_number`, `email_address`, `booking_request_email_body`
	FROM `general_settings`
	WHERE `id` = '1'
	LIMIT 1");

$company_emails             = get_email_list( $system_settings['email_address'] );
$company_email              = $company_emails->primaryEmail;
$company_name               = $system_settings['company_name'];
$company_phone_number       = $system_settings['phone_number'];
$booking_request_email_body = $system_settings['booking_request_email_body'];

$booking_data = fetch_row("SELECT b.`total_passenger`, b.`cruise_name`, DATE_FORMAT(b.`departure_date`, '%e %M %Y') AS departure_on,
	b.`message`, DATE_FORMAT(b.`record_date`, '%e %M %Y') AS booking_date, p.`first_name`, p.`last_name`, p.`email_address`,
	p.`contact_number`, IF(b.`inc_insurance_details` = 'Y', 'Yes', 'No') AS has_insurance,
	(SELECT GROUP_CONCAT( TRIM( CONCAT(`first_name`, ' ', `last_name`) ) ) FROM `passenger` WHERE `primary_passenger_id` = p.`id`) AS extra_passengers,
	(SELECT GROUP_CONCAT(`name`) FROM `booking_has_cruise_option` WHERE `booking_id` = b.`id`) AS cruise_option,
	(SELECT GROUP_CONCAT(`name`) FROM `booking_has_ship_suite` WHERE `booking_id` = b.`id`) AS ship_suites
	FROM `booking` b
	LEFT JOIN `passenger` p
	ON(p.`id` = b.`passenger_id`)
	WHERE b.`id` = '{$booking_id}'
	AND p.`primary_passenger_id` = '0'
	LIMIT 1");


$email_tags = array();
$email_tags['root']             = $htmlroot;
$email_tags['admin_root']       = $htmladmin;
$email_tags['subject_customer'] = "{$booking_data['cruise_name']} booking request";
$email_tags['subject_client']   = "New booking request recieved from website";
$email_tags['booking_heading']  = 'Booking Request';
$email_tags['booking_id']       = $booking_id;
$email_tags['company_name']     = $company_name;
$email_tags['company_phone']    = $company_phone_number;
$email_tags['company_email']    = $company_email;
$email_tags['email_body']       = $booking_request_email_body;


$booking_data = array_merge($booking_data, $email_tags);


$company_email = 'talwindersingh8@gmail.com';

$compiled_customer_email = process_template("{$tmpldir}/email/customer.tmpl", $booking_data);

$compiled_client_email   = process_template("{$tmpldir}/email/client.tmpl", $booking_data);

require_once "{$classdir}/class_phpmailer.php";


if($compiled_client_email)
{
	// Send Email Client
	$mailer = new PHPMailer();
	$mailer->IsHTML();
	$mailer->AddReplyTo($booking_data['email_address']);
	$mailer->AddAddress($company_email);
	
	if( !empty($company_emails->list) )
	{
		foreach ($company_emails->list as $email)
		{
			$mailer->AddCC($email);
		}
	}

	$mailer->SetFrom($company_email);
	$mailer->FromName = "{$booking_data['first_name']} {$booking_data['last_name']}";
	$mailer->Subject  = $email_tags['subject_client'];
	$mailer->msgHTML($compiled_client_email);
	$mailer->Send();

}

if($compiled_customer_email)
{
	
	// Send Email Customer
	$mail = new PHPMailer();
	$mail->IsHTML();
	$mail->AddReplyTo($company_email);
	$mail->AddAddress($booking_data['email_address']);
	$mail->SetFrom($company_email);
	$mail->FromName = $company_name;
	$mail->Subject  = $email_tags['subject_customer'];
	$mail->msgHTML($compiled_customer_email);
	$mail->Send();

}


?>