<?php 
	$donation_data                       = array();
	$donation_data['first_name']         = $first_name;
	$donation_data['last_name']          = $last_name;
	$donation_data['full_name']          = $first_name.' '.$last_name;
	$donation_data['email']              = $email;
	$donation_data['phone_number']       = $phone_number;
	$donation_data['address']            = $address;
	$donation_data['suburb']             = $suburb;
	$donation_data['city']               = $city;
	$donation_data['post_code']          = $post_code;
	$donation_data['full_address']       = $address.', '.$suburb.', '.$city.', '.$post_code;
	$donation_data['ref_number']         = $ref_number;
	$donation_data['amount']             = $amount;
	$donation_data['subscribe']          = $subscribe;
	$donation_data['donation_type_id']   = $donation_type_id;
	$donation_data['donation_type_name'] = $donation_type_name;

	$donation = insert_row($donation_data, 'donation');

	if ($donation) {
		$process_payment = TRUE;
		insert_row(array('data3' => $donation), 'donation_transaction');
	}
	else{
		$process_payment = FALSE;
	}

 ?>