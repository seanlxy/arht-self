<?php
set_time_limit ( 1000 );


require_once 'classes/database.php';

$db1_config = array();
$db1_config['host']     = 'localhost';
$db1_config['db_name']  = 'new_payments';
$db1_config['username'] = 'root';
$db1_config['password'] = '';
$db1_config['driver']   = 'mysql';

$db1 = new Database($db1_config);



$db2_config = array();
$db2_config = array();
$db2_config['host']     = 'localhost';
$db2_config['db_name']  = 'payments';
$db2_config['username'] = 'root';
$db2_config['password'] = '';
$db2_config['driver']   = 'mysql';

$db2 = new Database($db2_config);

$status_arr = array(
	1 => 'P',
	3 => 'A',
	4 => 'D'
);


$history = array();

$raw_history = $db2->runQuery("SELECT `request_id`, `date`, `short_name`, `description`
	FROM `request_history`
	WHERE 1");

foreach ($raw_history['rows'] as $row)
{
	$history[$row['request_id']][] = $row;
}

$requests = $db2->fetchAll("SELECT `request_id`, `user_id`, `first_name`, `last_name`, `email_address`,
	`request_amount`, `request_link`, `email_subject`, `email_content`, `template`, `date_requested`,
	`date_completed`, `status`
	FROM `request`
	WHERE `user_id` = '2'");



$request_templates = $db2->fetchAll("SELECT `from_name`, `from_email` AS from_email_address, `content_subject` AS subject, `content_text` AS content FROM `user_template` WHERE `user_id` = 2");


echo "<pre>";



foreach ($request_templates['rows'] as $request_template)
{
	// $db1->insert($request_template, 'pmt_template');
}

die('done bro');
// foreach ($requests['rows'] as $request)
// {

// 	$new_request_url = "http://www.onceuponatripnz.com/payment/?pid=";

// 	$request_id        = $request['request_id'];
// 	$request_status_id = $request['status'];
// 	$first_name        = $request['first_name'];
// 	$last_name         = $request['last_name'];
// 	$email_address     = $request['email_address'];
// 	$request_amount    = $request['request_amount'];
// 	$request_link      = $request['request_link'];
// 	$email_subject     = $request['email_subject'];
// 	$email_content     = str_replace(array('http://tourwriter.webdirectionz.co.nz/payment/request/', 'http://payments.onceuponatripnz.com/payment/request/'), $new_request_url, $request['email_content']);
// 	$date_requested    = $request['date_requested'];
// 	$date_completed    = $request['date_completed'];
// 	$req_history       = $history[$request_id];
// 	$full_name         = trim("{$first_name} {$last_name}");

// 	$is_deleted  = ( $request_status_id == 7 );
// 	$is_approved = ( $status_arr[$request_status_id] == 'A' );
// 	$is_declined = ( $status_arr[$request_status_id] == 'D' );
	
	

	


// 	$ins_payer   = array();
// 	$ins_payer['first_name']    = $first_name;
// 	$ins_payer['last_name']     = $last_name;
// 	$ins_payer['full_name']     = $full_name;
// 	$ins_payer['email_address'] = $email_address;

// 	// $payer_id = $db1->insert($ins_payer, 'pmt_payer');

		
// 	$ins_txn = array();

// 	$ins_txn['amount_settlement']   = $request_amount;
// 	$ins_txn['data1']               = $request_link;
// 	$ins_txn['currency_settlement'] = 'NZD';
// 	$ins_txn['txn_id']              = uniqid('PMT-');
// 	$ins_txn['response_text']       = (!empty($is_approved)) ? 'A' : 'D';
// 	$ins_txn['pmt_account_id']      = 3;

// 	if( $date_completed != '0000-00-00' )
// 	{
// 		$ins_txn['date_processsed'] = $date_completed;
// 	}

// 	// $pmt_transaction_id = $db1->insert($ins_txn, 'pmt_transaction');


// 	$ins_request = array();

// 	$ins_request['public_token']                                           = $request_link;
// 	$ins_request['amount']                                                 = $request_amount;
// 	$ins_request['status']                                                 = ((!empty($status_arr[$request_status_id])) ? $status_arr[$request_status_id] : 'P');
// 	$ins_request['cms_status']                                             = ((!empty($is_deleted)) ? 'D' : 'A');
// 	$ins_request['request_url']                                            = "{$new_request_url}{$request_link}";
// 	$ins_request['email_sent']                                             = 'Y';
// 	$ins_request['email_subject']                                          = $email_subject;
// 	$ins_request['email_content']                                          = $email_content;

// 	if( $date_requested != '0000-00-00' )
// 	{
// 		$ins_request['created_on']                                             = $date_requested;
// 	}

// 	if( $date_completed != '0000-00-00' )
// 	{

// 		$ins_request[((!empty($is_approved)) ? 'approved_on' : 'declined_on')] = $date_completed;
// 	}


// 	$ins_request['pmt_payer_id']                                           = $payer_id;
// 	$ins_request['email_template_id']                                      = 1;
// 	$ins_request['pmt_transaction_id']                                     = $pmt_transaction_id;


// 	// $request_id = $db1->insert($ins_request, 'pmt_request');
	

// 	if( !empty($req_history) )
// 	{

// 		foreach ($req_history as $hrow)
// 		{
			 
// 			$ins_history = array();
// 			$ins_history['date_time'] = $hrow['date'];
// 			$ins_history['label'] = strtoupper($hrow['short_name']);
// 			$ins_history['details'] = $hrow['description'];
// 			$ins_history['pmt_request_id'] = $request_id;

// 			// $db1->insert($ins_history, 'pmt_request_history');
// 		}

// 	}


// }



$db1->closeConnection();
$db2->closeConnection();


die('done bro!');

?>