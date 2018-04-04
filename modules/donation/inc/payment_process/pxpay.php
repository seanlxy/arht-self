<?php
#******************************************************************************
#* Name          	: PxPay_Sample_Curl.php
#* Description   	: Direct Payment Solutions Payment Express PxPay PHP cURL Sample
#* Copyright	 	: Direct Payment Solutions 2009(c)
#* Date          	: 2009-10-21
#* References    	: http://www.paymentexpress.com/technical_resources/ecommerce_hosted/pxpay.html
#*@version 	        : 1.0
#* Author 		: Thomas Treadwell
#************************************ ******************************************

# This file is a sample demonstrating integration with the PxPay interface using PHP with the cURL extension installed.  

$PxPay_Url    = "https://sec.paymentexpress.com/pxpay/pxaccess.aspx";

// For testing

	// if (is_local) {
		
	// 	$PxPay_Userid = "Tomahawk_Dev"; #Important! Update with your UserId
	// 	$PxPay_Key    = "9d11e64e5f26792355ac0e16739a2bbc3d1818a14e4f165c2e307b0b8b117aa0"; #Important! Update with your Key
	// }else{
		
	// Customer account details for actual payments

		$PxPay_Userid = "RescueHelicopterPxPay2"; #Important! Update with your UserId
		$PxPay_Key    =  "4779897c1c9e9448806c22d7d567a86692544d24b36965202b1561ccc8493ca1"; #Important! Update with your Key
	// }



#Inlcude PxPay objects
include "$classdir/PxPay_Curl.inc.php";

$pxpay = new PxPay_Curl( $PxPay_Url, $PxPay_Userid, $PxPay_Key );


#******************************************************************************
# Database lookup to check the status of the order or shopping cart
#******************************************************************************

function isProcessed($TxnId)
{
	# Check database if order relating to TxnId has alread been processed
	$transaction = fetch_value("SELECT `id` FROM `donation_transaction` WHERE `txn_id` = '$TxnId' LIMIT 1");
	if($transaction) return TRUE;
	return FALSE;
}

function send_info()
{

	global $pxpay, $donation, $htmlroot, $segment1 , $page_donations;

	$request     = new PxPayRequest();
	$http_host   = getenv("HTTP_HOST");
	$request_uri = $page_donations->url;
	$server_url  = "http://$http_host";
	// $script_url = (version_compare(PHP_VERSION, "4.3.4", ">=")) ?"$server_url$request_uri" : "$server_url/$request_uri";
	$script_url = $htmlroot.'/'.$page_donations->url.'/'.$segment1;

	# the following variables are read from the form
	$MerchantReference = uniqid('ARHT');

	#Generate a unique identifier for the transaction
	$TxnId = uniqid("ID");


	if($donation)
	{

		$donation_details = fetch_row("SELECT `id`, `full_name`, `email`, `full_address`, `phone_number`, 
			`ref_number`, `amount`, `subscribe`, `donation_type_name` 
			FROM `donation`
			WHERE `id` = '$donation'
			LIMIT 1");

		$donation_amount = $donation_details['amount'];

		#Set PxPay properties
		$request->setMerchantReference($MerchantReference);
		$request->setAmountInput($donation_amount);
		$request->setTxnData1('');
		$request->setTxnData2('');
		$request->setTxnData3($donation);
		$request->setTxnType("Purchase");
		$request->setCurrencyInput("NZD");
		$request->setUrlFail($script_url.'?error');			# can be a dedicated failure page
		$request->setUrlSuccess($script_url.'?success');	# can be a dedicated success page
		$request->setTxnId($TxnId);  

		#The following properties are not used in this case
		$request->setEnableAddBillCard(1);    // Token Billing
		//$request->setBillingId($BillingId); // Comment out to let dps generate billing id
		# $request->setOpt($Opt);

		#Call makeRequest function to obtain input XML
		$request_string = $pxpay->makeRequest($request);

		#Obtain output XML
		$response = new MifMessage($request_string);

		#Parse output XML
		$url   = $response->get_element_text("URI");
		$valid = $response->get_attribute("valid");

		#Redirect to payment page
		header("Location: ".$url);
		exit();
	}
	else
	{
		return FALSE;
	}
}//send_info

function get_result_from_dps()
{
	global $pxpay, $tags_arr, $classdir, $incdir, $moddir, $htmlrootfull, $fromroot, $page,$root, $page_donations, $templates_dir, $htmlroot,
			$comp_emails;

	$enc_hex             = $_GET["result"];

	$update_arr          = array();
	$new_transaction_arr = array();
	$email_template_tags = array();

	#getResponse method in PxPay object returns PxPayResponse object
	#which encapsulates all the response data

	$response                                   = $pxpay->getResponse($enc_hex);


	$TxnId                                      = $response->getTxnId(); 
	$success                                    = $response->getSuccess();   # =1 when request succeeds
	$new_transaction_arr['amount_settlement']   = $AmountSettlement     = $response->getAmountSettlement();
	$new_transaction_arr['auth_code']           = $AuthCode             = $response->getAuthCode();  # from bank
	$new_transaction_arr['cc_name']             = $CardName             = $response->getCardName();  # e.g. "Visa"
	$new_transaction_arr['cc_holder_name']      = $CardHolderName       = $response->getCardHolderName();
	$new_transaction_arr['cc_number']           = $CardNumber           = $response->getCardNumber(); # Truncated card number
	$new_transaction_arr['cc_date_expire']      = $DateExpiry           = $response->getDateExpiry(); # in mmyy format
	$new_transaction_arr['dps_billing_id']      = $DpsBillingId         = $response->getDpsBillingId();
	$new_transaction_arr['dps_ref']             = $DpsTxnRef            = $response->getDpsTxnRef();
	$new_transaction_arr['type']                = $TxnType              = $response->getTxnType();
	$new_transaction_arr['data1']               = $TxnData1             = $response->getTxnData1();
	$new_transaction_arr['data2']               = $TxnData2             = $response->getTxnData2();
	$new_transaction_arr['data3']               = $TxnData3             = $response->getTxnData3();
	$new_transaction_arr['currency_settlement'] = $CurrencySettlement    = $response->getCurrencySettlement();
	$new_transaction_arr['client_ip']           = $ClientInfo           = $response->getClientInfo(); # The IP address of the user who submitted the transaction
	$new_transaction_arr['txn_id']              = $TxnId                = $response->getTxnId();
	$new_transaction_arr['currency_input']      = $CurrencyInput        = $response->getCurrencyInput();
	$new_transaction_arr['merchant_ref']        = $MerchantReference    = $response->getMerchantReference();
	$new_transaction_arr['response_text']       = $ResponseText         = $response->getResponseText();
	$new_transaction_arr['mac_address']         =  $TxnMac              = $response->getTxnMac(); # An indication as to the uniqueness of a card used in relation to others
	$new_transaction_arr['response_url']        = $enc_hex;
	$new_transaction_arr['date_processsed']     = date("Y-m-d h:i:s");
	$BillingId                                  = $response->getBillingId();

	$donor_id           = $response->getTxnData3();
	$transaction_id     = NULL;
	$new_transaction_id = NULL;


	if( !isProcessed($response->getTxnId()) )
	{

		$new_transaction_id = update_row($new_transaction_arr, 'donation_transaction', "WHERE data3 = '$donor_id'");
	}

	// Get current order details
	$query = "SELECT d.`id`, d.`first_name`, d.`last_name`, d.`full_name`, d.`email`, d.`phone_number`, d.`address`, d.`suburb`, d.`city`, d.`post_code`, 
		d.`full_address`, d.`ref_number`, d.`amount`, d.`subscribe`, d.`donation_type_name`, d.`is_notified`,
	    dt.`merchant_ref` AS dps_reference, dt.`response_text` AS dps_status, dt.`id` AS transaction_id, REPLACE(dt.`amount_settlement`, '.00', '') AS amount,
	    DATE_FORMAT(dt.`date_processsed`, '%e %M %Y') AS donation_date
		FROM `donation` d
		LEFT JOIN donation_transaction dt
		ON(dt.`data3` = d.`id`)
		WHERE d.`id` = '$donor_id'";

	$transaction_details = fetch_row($query);


	$is_notified = $transaction_details['is_notified'];


	// Get Company details

	// $company_info = fetch_row("SELECT `set_company` AS company_name, `set_email`, `set_demail_confirmation_msg` AS confirmation_msg
	// 	FROM `general_settings` 
	// 	WHERE `set_id` = '1' 
	// 	LIMIT 1
	// ");

	// // $company_info['set_email'] = 'talwinder@tomahawk.co.nz';

	
	// // Get donation information
	// $subject_customer = 'Donation Confirmation';
	// $subject_client   = 'New donation';
	
	// // prepare email tempalte tags
	// $email_template_tags['root']             = $htmlroot;
	// $email_template_tags['subject_customer'] = $subject_customer;
	// $email_template_tags['subject_client']   = $subject_client;

	// // Merge all tag together
	// $email_template_tags                     = array_merge($transaction_details, $company_info, $email_template_tags);

	// //Modify existing tags
	// $email_template_tags['company_email']    = mail_to($company_info['set_email']);
	// $email_template_tags['email_address']    = mail_to($email_template_tags['email_address']);
	// $email_template_tags['ref_number']       = $response->getDpsTxnRef();
	// $email_template_tags['response_msg']     = $response->getResponseText();


	// include_once "$classdir/class_phpmailer.php";

	// if($Success)
	// {
		

	// 	$email_template_customer = process_template("{$templates_dir}email/customer.html", $email_template_tags, '{', '}');


	// 	if($transaction_details['dps_status'] == 'APPROVED' && $is_notified == '0')
	// 	{

	// 		$client_template           = 'client_success';

	// 		// Send Email to customer
	// 		$customer_mail           = new PHPMailer();
	// 		$customer_mail->IsHTML();
	// 		$customer_mail->AddReplyTo($company_info['set_email']);
	// 		$customer_mail->AddAddress($transaction_details['email_address'], ($transaction_details['first_name'].' '.$transaction_details['last_name']));
	// 		$customer_mail->SetFrom($company_info['set_email']);
	// 		$customer_mail->FromName = $company_info['company_name'];
	// 		$customer_mail->Subject  = $subject_customer;
	// 		$customer_mail->msgHTML($email_template_customer);
			
	// 		if($customer_mail->Send())
	// 		{

	// 			$update_arr['status']      = 'A';
	// 			$update_arr['is_notified'] = '1';
				
	// 			update_row($update_arr, 'donor', "WHERE id = '{$donor_id}'");
				
	// 		}
	// 	}
	// }
	// elseif($Success == '0')
	// {

	// 	if($new_transaction_id)
	// 	{

	// 		$update_arr = array();

	// 		$update_arr['status']      = 'D';
	// 		$update_arr['is_notified'] = '1';

	// 		update_row($update_arr, 'donor', "WHERE id = '{$donor_id}'");
	// 		$client_template = 'client_fail';
	// 	}

	
	// 	$GLOBALS['first_name']        = $transaction_details['first_name'];
	// 	$GLOBALS['last_name']         = $transaction_details['last_name'];
	// 	$GLOBALS['email_address']     = $transaction_details['email_address'];
	// 	$GLOBALS['phone']             = $transaction_details['phone_number'];
	// 	$GLOBALS['message']           = $transaction_details['message'];
	// 	$GLOBALS['amount_sel']        = $transaction_details['amount_id'];

	// 	if($transaction_details['damount'])
	// 	{
	// 		$GLOBALS['amount_sel']   = 'O';
	// 		$GLOBALS['is_hidden']    = '';
	// 		$GLOBALS['amount_value'] = $transaction_details['damount'];
	// 	}
	

	// 	$GLOBALS['dps_response']      = ucwords(strtolower($new_transaction_arr['response_text']));

	// }
	

	// if($client_template)
	// {
	// 	$email_template_client = process_template("{$templates_dir}email/{$client_template}.html", $email_template_tags, '{', '}');

	// 	// Send success email to client
	// 	$subject_client   = ($Success) ? 'New donation' : 'Failed donation';
	// 	$client_mail           = new PHPMailer();
	// 	$client_mail->IsHTML();
	// 	$client_mail->AddAddress($company_info['set_email'], $company_info['company_name']);
	// 	$client_mail->SetFrom($company_info['set_email']);
	// 	$client_mail->FromName = $company_info['company_name'];
	// 	$client_mail->Subject  = $subject_client;
	// 	$client_mail->msgHTML($email_template_client);
	// 	$client_mail->Send();

	// }


	if($success && $is_notified === 'N'){

		$template_path = "{$moddir}/donation/inc/form_process/email_template.php";

		if (file_exists($template_path)) {

			$email_template                    = file_get_contents($template_path);
			
			$email_template_tags               = array();
			$email_template_tags['subject']    = "A donation have been received";
			$email_template_tags['root']       = $htmlrootfull;
			
			$email_template_tags               = array_merge($email_template_tags, $transaction_details);
			$email_template_tags['subscribe']  = ($transaction_details['subscribe'] === 'Y') ? 'Subscribed' : 'N/A';
			$email_template_tags['ref_number'] = ($transaction_details['ref_number'] === '0') ? 'N/A' : $transaction_details['ref_number'];

			foreach ($email_template_tags as $tag => $value) {
				$email_template = str_replace("{".$tag."}", $value, $email_template);
			}

			// Initiate php mailer class to send email
			require_once "$classdir/class_phpmailer.php";

			// get comany details i.e. name and emai laddress
			$company_details = fetch_row("SELECT `company_name`, `email_address` FROM `general_settings` WHERE `id` = '1'");
			$company_email   = ($comp_emails->primaryEmail) ? $comp_emails->primaryEmail : 'support@tomahawk.co.nz';
			$company_name    = $company_details['company_name'];


			// Send Email to ARHT
			$mail = new PHPMailer();
			$mail->IsHTML();
			$mail->AddReplyTo($email_template_tags['email']);
			$mail->AddAddress($company_email);
			if(count($comp_emails->list) > 0)
			{
				foreach ($comp_emails->list as $email)
				{
					$mail->AddCC($email);
				}
			}
			$mail->SetFrom($email_template_tags['email']);
			$mail->FromName = "{$email_template_tags['full_name']}";
			$mail->Subject  = $email_template_tags['subject'];
			$mail->msgHTML($email_template);
			$mail->Send();

			// Send Email to Donor
			$mail2 = new PHPMailer();
			$mail2->IsHTML();
			$mail2->AddReplyTo($company_email);
			$mail2->AddAddress($email_template_tags['email']);

			$mail2->SetFrom($company_email);
			$mail2->FromName = "{$company_name}";
			$mail2->Subject  = $email_template_tags['subject'];
			$mail2->msgHTML($email_template);

			$mail2->Send();

			run_query("UPDATE `donation` SET `is_notified` = 'Y' WHERE `id` = '{$transaction_details['id']}' LIMIT 1");

		}


	}


	return ($Success) ? $donor_id : false;
}

?>