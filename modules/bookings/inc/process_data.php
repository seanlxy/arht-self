<?php



// Validate Email Address
if( empty($email_address) )
{
	$errors["email-address"] = 'Email Address is required';
}
else if( !filter_var($email_address, FILTER_VALIDATE_EMAIL) )
{
	$errors["email-address"] = 'Invalid Email Address provided';
}
else
{
	$form_is_valid = true;
}


// Validate departure date
if( empty($departure_date_token) )
{
	$errors["sel-departure-date"] = 'Invalid departure date';

	$form_is_valid = false;
}
else
{
	$form_is_valid = true;
}

// Validate Phone Number
if( empty($phone_number) )
{
	$errors["phone-number"] = 'Phone Number is required';

	$form_is_valid = false;
}
else
{
	$form_is_valid = true;
}


// Validate Total Number of passengers
if( $total_pax <= 0 )
{

	$errors['total-pax'] = 'Number of passenger(s) are required';

	$form_is_valid = false;
}
else
{
	$form_is_valid = true;
}

$primary_passenger_id = null;

for ($i=0; $i < $total_pax; $i++)
{

	$pax_data   = array();

	$first_name = sanitize_var( $first_names[$i] );
	$last_name  = sanitize_var( $last_names[$i] );

	// Validate First Name(s)
	if( empty($first_name) )
	{
		$errors["first-name-{$i}"] = 'First Name is required';
		$form_is_valid = false;
	}
	else
	{
		$form_is_valid = true;
	}

	// Validate Last Name(s)
	if( empty($last_name) )
	{
		$errors["last-name-{$i}"] = 'Last Name is required';
		$form_is_valid = false;
	}
	else
	{
		$form_is_valid = true;
	}


	if( $form_is_valid && empty($errors) )
	{

		$pax_data['first_name'] = $first_name;
		$pax_data['last_name']  = $last_name;

		if( is_null( $primary_passenger_id ) && $i == 0 )
		{
			$pax_data['email_address']  = $email_address;
			$pax_data['contact_number'] = $phone_number;
			
			$primary_passenger_id = insert_row($pax_data, 'passenger');

		}
		elseif($primary_passenger_id)
		{
			$pax_data['primary_passenger_id'] = $primary_passenger_id;

			$passenger_id = insert_row($pax_data, 'passenger');
		}
	}
}


if( empty($errors) )
{


	$itinerary_date_data = fetch_row("SELECT cid.`id`, cid.`start_date`, cid.`price`,
		cid.`cruise_id`, pmd.`heading` AS cruise_name
		FROM `cruise_itinerary_departure` cid
		LEFT JOIN `cruise` c
		ON(c.`id` = cid.`cruise_id`)
		LEFT JOIN `page_meta_data` pmd
		ON(pmd.`id` = c.`page_meta_data_id`)
		WHERE cid.`public_token` = '{$departure_date_token}'
		LIMIT 1");


	if( $primary_passenger_id && !empty($itinerary_date_data) )
	{
		$booking_data = array();

		$total_booking_amount = $itinerary_date_data['price'];


		//  Save booking record
		$booking_data['public_token']                  = md5( sha1( create_rand_chars() ) );
		$booking_data['total_passenger']               = $total_pax;
		$booking_data['total_amount']                  = $total_booking_amount;
		$booking_data['cruise_name']                   = $itinerary_date_data['cruise_name'];
		$booking_data['departure_date']                = $itinerary_date_data['start_date'];
		$booking_data['message']                       = $message;
		$booking_data['inc_insurance_details']         = (($inc_insurance == 1) ? 'Y' : 'N');
		$booking_data['record_date']                   = date('Y-m-d H:i:s');
		$booking_data['ip_address']                    = getenv('REMOTE_ADDR');
		$booking_data['cruise_id']                     = $itinerary_date_data['cruise_id'];
		$booking_data['passenger_id']                  = $primary_passenger_id;
		$booking_data['cruise_itinerary_departure_id'] = $itinerary_date_data['id'];


		$booking_id = insert_row($booking_data, 'booking');


		//  Save ships suites if any selected
		if( !empty($room_grades) && $booking_id )
		{

			$ship_suites = fetch_all("SELECT ss.`id`, ss.`heading`, ss.`price_from`,
				(SELECT `price`
					FROM `cruise_departure_has_ship_suite`
					WHERE `ship_id` = ss.`ship_id`
					AND `cruise_departure_id` = '{$itinerary_date_data['id']}'
					AND `ship_suite_id` = ss.`id`
					LIMIT 1) AS rate
				FROM `ship_suite` ss
				WHERE ss.`public_token` IN('".implode("','", $room_grades)."')");

			$ship_suites_query = '';


			if( !empty($ship_suites) )
			{

				foreach ($ship_suites as $ship_suite)
				{
					$ship_suite_price = ($ship_suite['rate']) ? $ship_suite['rate'] : $ship_suite['price_from'];

					$ship_suites_query .= ",('{$ship_suite['heading']}',{$ship_suite_price},{$booking_id},{$ship_suite['id']})";

					$total_booking_amount += $ship_suite_price;
				}

				$ship_suites_query = ltrim($ship_suites_query, ',');


				if( $ship_suites_query )
				{
					run_query("INSERT INTO `booking_has_ship_suite`(`name`, `price_per_person`, `booking_id`, `ship_suite_id`) VALUES {$ship_suites_query}");
				}
			}

		}


		//  Save Cruise Options if any selected
		if( !empty($cruise_options) && $booking_id )
		{
			$cruise_opts = fetch_all("SELECT `id`, `heading`, `price_from`
				FROM `cruise_option`
				WHERE `public_token` IN('".implode("','", $cruise_options)."')");

			$cruise_opts_query = '';


			if( !empty($cruise_opts) )
			{

				foreach ($cruise_opts as $cruise_opt)
				{
					$cruise_opt_price = $cruise_opt['price_from'];

					$cruise_opts_query .= ",('{$cruise_opt['heading']}',{$cruise_opt_price},{$booking_id},{$cruise_opt['id']})";

					$total_booking_amount += $cruise_opt_price;
				}

				$cruise_opts_query = ltrim($cruise_opts_query, ',');


				if( $cruise_opts_query )
				{
					run_query("INSERT INTO `booking_has_cruise_option`(`name`, `price_per_person`, `booking_id`, `cruise_option_id`) VALUES {$cruise_opts_query}");
				}
			}
		}

		//  Updated the booking total amount
		run_query("UPDATE `booking` SET `total_amount` = '{$total_booking_amount}' WHERE `id` = '{$booking_id}' LIMIT 1");


		if( $booking_id  )
		{

			require_once 'send_notification.php';

			$state   = 'success';

			$msg = <<< H
			<p><strong>Thank you for choosing to book with {$company_name}.</strong></p>
			<p>An email has just been sent to your email address ({$booking_data['email_address']}) detailing your booking request. We are currently checking cruise availability and our reservations team will be in touch shortly to confirm your booking with you.</p>
H;

			
			unset($_SESSION['options']);
			unset($_SESSION['grades']);

		}

	}

}
?>