<?php
	
$form_is_valid = false;

$errors        = array();

$room_grades          = $_POST['booking-room-grade'];
$cruise_options       = $_POST['booking-cruise-option'];
$first_names          = $_POST['first-name'];
$last_names           = $_POST['last-name'];

$departure_date_token = sanitize_input('sel-departure-date');
$cruise_token         = sanitize_input('cruise-token');
$total_pax            = sanitize_input('total-pax', FILTER_VALIDATE_INT);
$email_address        = sanitize_input('email-address');
$phone_number         = sanitize_input('phone-number');
$message              = sanitize_input('message');
$inc_insurance        = sanitize_input('insurance', FILTER_VALIDATE_INT);





?>