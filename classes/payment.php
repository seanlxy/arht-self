<?php

require_once 'payment_constants.php';
require_once 'class_phpmailer.php';

class Payment extends PaymentConstants
{
	
	protected $productionMode;

	protected $hasAccounts;

	protected $paymentURL;

	protected $requestEmailTmplPath;

	protected $emailTmplData = array();


	public function __construct( $config )
	{
		$this->setConfig($config);
	}


	public function create($data)
	{

		if( !empty($data) )
		{

			$subject         = $data['subject'];
			$templateContent = $data['content'];
			$firstName       = $data['first_name'];
			$lastName        = $data['last_name'];
			$fullName        = trim("{$firstName} {$lastName}");
			$emailAddress    = $data['email_address'];
			$amount          = $data['amount'];
			$emailTemplateId = $data['email_template_id'];
			$requestToken    = self::createUniqueId();
			$requestUrl      = "{$this->paymentURL}/?pid={$requestToken}";



			$templateBodyTags = array();

			$templateBodyTags['full_name']      = $fullName;
			$templateBodyTags['payment_amount'] = $amount;
			$templateBodyTags['payment_link']   = '<a href="'.$requestUrl.'" target="_blank">'.$requestUrl.'</a>';


			if( !empty($templateBodyTags) )
			{
				$validTags = $this->getTemplateTagKeys();

				foreach ( $templateBodyTags as $key => $value )
				{
					if( in_array($key, $validTags) )
					{
						$templateContent = str_replace('{'.$key.'}', $value, $templateContent);
					}
				}

			}

			
			/* ==| CREATE NEW PAYER RECORD ==================================================== */

			$payerData = array();

			$payerData['first_name']    = $firstName;
			$payerData['last_name']     = $lastName;
			$payerData['full_name']     = $fullName;
			$payerData['email_address'] = $emailAddress;

			$payerId = insert_row($payerData, 'pmt_payer');

			if( !empty($payerId) )
			{

				$transactionId = insert_row(array('amount_settlement' => $amount), 'pmt_transaction');

				$payment_data = array();
				$payment_data['public_token']       = $requestToken;
				$payment_data['amount']             = $amount;
				$payment_data['status']             = self::FLAG_PENDING;
				$payment_data['request_url']        = $requestUrl;
				$payment_data['email_sent']         = self::FLAG_NO;
				$payment_data['email_subject']      = $subject;
				$payment_data['email_content']      = $templateContent;
				$payment_data['created_on']         = self::getCurrentDateTime();
				$payment_data['pmt_payer_id']       = $payerId;
				$payment_data['email_template_id']  = $emailTemplateId;
				$payment_data['pmt_transaction_id'] = $transactionId;

				$paymentRequestId = insert_row($payment_data, 'pmt_request');

				if( !empty($paymentRequestId) )
				{

					self::logHistory( self::HISTORY_NEW_ID, $paymentRequestId );

					/* ==| SENT REQUEST EMAIL TO CLIENT ==================================================== */
					$this->sendRequestEmail($paymentRequestId);

					return $paymentRequestId;

				}
			
			}
		}

		return false;
	}

	public function sendRequestEmail( $paymentRequestId )
	{
		$paymentRequestId = filter_var($paymentRequestId, FILTER_VALIDATE_INT);

		$paymentDetails = $this->getDetails($paymentRequestId);

		if( !empty($paymentDetails) )
		{

			$templateTags = array();
			
			$templateTags['email_body'] = $paymentDetails['email_content'];
			$templateTags['subject']    = $paymentDetails['email_subject'];
			$templateTags               = array_merge($this->emailTmplData, $templateTags);

			$compiledTemplate = self::processTemplate($this->requestEmailTmplPath, $templateTags);

			/* ==| SEND EMAIL TO CLIENT ==================================================== */

			$templateDetails                     = self::getTemplateDetails($paymentDetails['email_template_id']);
			$templateDetails['subject']          = $paymentDetails['email_subject'];
			$templateDetails['email_body']       = $compiledTemplate;
			$templateDetails['to_email_address'] = $paymentDetails['email_address'];


			if( !empty($templateDetails) )
			{
				
				$emailIsSent = self::sendEmail($templateDetails);

				if( $emailIsSent )
				{
					self::logHistory( self::HISTORY_SENT_ID, $paymentRequestId );
				}

				return $emailIsSent;
			}

		}

		return false;
	}

	public function getDetails( $paymentToken )
	{
		if( !empty($paymentToken) )
		{

			$paymentDetails = fetch_row("SELECT pr.`id`, pr.`public_token`, pr.`amount`, pr.`status`, pr.`request_url`,
				pr.`email_template_id`, pr.`email_sent`, pr.`email_subject`, pr.`email_content`, pr.`created_on`, pr.`approved_on`,
				pr.`declined_on`, pr.`pmt_payer_id`, pr.`pmt_transaction_id`, pp.`first_name`, pp.`last_name`, pp.`full_name`,
				pp.`email_address`, pt.`amount_settlement`, pt.`auth_code`, pt.`dps_billing_id`, pt.`dps_ref`, pt.`currency_settlement`,
				pt.`txn_id`, pt.`currency_input`, pt.`merchant_ref`, pt.`response_text`, pt.`response_url`, pt.`date_processsed`,
				pt.`pmt_account_id`
				FROM `pmt_request` pr
				LEFT JOIN `pmt_payer` pp
				ON(pp.`id` = pr.`pmt_payer_id`)
				LEFT JOIN `pmt_transaction` pt
				ON(pt.`id` = pr.`pmt_transaction_id`)
				WHERE ".( (is_numeric($paymentToken)) ? "pr.`id` = '{$paymentToken}'" : "pr.`public_token` = '{$paymentToken}'")."
				LIMIT 1" );

			return $paymentDetails;

		}

		return false;
	}

	public function updateDetails( $paymentData,  $paymentId )
	{

	}

	public static function logHistory( $statusId, $requestId )
	{

		if( !empty($statusId) && !empty($requestId) )
		{

			$data = self::getHistoryStatusDetails($statusId);

			$data['pmt_request_id'] = $requestId;



			$historyId = insert_row($data, 'pmt_request_history');

			return $historyId;

		}

		return false;
	}

	public function delete($paymentId)
	{
		return self::updateCmsStatus($paymentId, self::FLAG_DELETED);
	}

	public function getCreditCards()
	{

		$creditCards =  fetch_all("SELECT `id`, `name`, `image_path`
			FROM `pmt_credit_card`
			WHERE 1");

		return $creditCards;
	}

	public function getAccountCreditCards($accountId = '')
	{

		$creditCards = array();

		$accountId = filter_var($accountId, FILTER_VALIDATE_INT) ;

		$query =  run_query("SELECT pcc.`id`, pcc.`name`, pcc.`image_path`,
			pahpcc.`pmt_account_id`
			FROM `pmt_credit_card` pcc
			LEFT JOIN `pmt_account_has_pmt_credit_card` pahpcc
			ON(pahpcc.`pmt_credit_card_id` = pcc.`id`)
			WHERE ".((!empty($accountId)) ? "pahpcc.`pmt_account_id` = '{$accountId}'" : '1') );

		if( mysql_num_rows($query) > 1 )
		{

			while ( $creditCard = mysql_fetch_assoc($query) )
			{
				$creditCards[$creditCard['pmt_account_id']][$creditCard['id']] = $creditCard;
			}

		}

		return $creditCards;
	}

	public function getAccounts()
	{

		// --AND `is_live` = '".(($this->productionMode === true) ? self::FLAG_YES : self::FLAG_NO)."' 
		
		$accounts =  fetch_all("SELECT `id`, `label`, `user`,
			`api_key`,`is_live`, `has_cc`
			FROM `pmt_account`
			WHERE `user` != '' 
			
			AND `api_key` != ''
			ORDER BY `is_live` DESC");

		$this->hasAccounts = !empty($accounts);

		return $accounts;
	}

	public function getTemplateTagKeys()
	{
		$keys = array();

		$keys_query =  run_query("SELECT `key`
			FROM `pmt_template_tag`
			WHERE `key` != ''");

		if( mysql_num_rows($keys_query) > 1 )
		{
			while ( $key = mysql_fetch_assoc($keys_query) )
			{
				$keys[] = $key['key'];
			}
		}

		return $keys;
	}

	public function getTemplateTags()
	{
		$tags =  fetch_all("SELECT `id`, `label`, `key`, `description`
			FROM `pmt_template_tag`
			WHERE `key` != ''");

		return $tags;
	}

	public function getTemplates()
	{
		$templates =  fetch_all("SELECT `id`, `name`, `short_description`, `from_name`,
			`from_email_address`, `subject`, `content`
			FROM `pmt_template`
			WHERE 1");

		return $templates;
	}

	public static function getTemplateDetails($templateId)
	{
		$templateId = filter_var($templateId, FILTER_VALIDATE_INT);

		if( !empty($templateId) )
		{
			$template_details =  fetch_row("SELECT `name`, `short_description`, `from_name`,
				`from_email_address`, `subject`, `content`
				FROM `pmt_template`
				WHERE `id` = '{$templateId}'
				LIMIT 1");

			return $template_details;
		}

		return false;
	}

	public function getHistory($requestId)
	{
		$requestId = filter_var($requestId, FILTER_VALIDATE_INT);

		if( !empty($requestId) )
		{
			$history =  fetch_all("SELECT `label`, `details`,
				DATE_FORMAT(`date_time`, '%d %b %Y at %h:%i %p') AS date_time
				FROM `pmt_request_history`
				WHERE `pmt_request_id` = '{$requestId}'
				ORDER BY `date_time`");

			return $history;

		}

		return false;
	}


	public static function updateStatus( $paymentId, $status )
	{
		$paymentId = filter_var($paymentId, FILTER_VALIDATE_INT);

		if( !empty($paymentId) && !empty($status) )
		{
			run_query("UPDATE `pmt_request`
				SET `status` = '{$status}'
				WHERE `id` = '{$paymentId}' 
				LIMIT 1");

			return true;
		}

		return false;
	}

	private static function updateCmsStatus( $paymentId, $status )
	{
		$paymentId = filter_var($paymentId, FILTER_VALIDATE_INT);

		if( !empty($paymentId) && !empty($status) )
		{
			run_query("UPDATE `pmt_request`
				SET `cms_status` = '{$status}'
				WHERE `id` = '{$paymentId}' 
				LIMIT 1");

			return true;
		}

		return false;
	}


	private static function getHistoryStatusDetails($statusId)
	{
		$statusId = filter_var($statusId, FILTER_VALIDATE_INT);

		if( !empty($statusId) )
		{

			return fetch_row("SELECT NOW() AS date_time, UPPER(`label`) AS label, 
				`description` AS details
				FROM `pmt_history_status`
				WHERE `id` = '{$statusId}'
				LIMIT 1");

		}

		return false;
	}


	public static function getMsg($messageId)
	{
		$messageId = filter_var($messageId, FILTER_VALIDATE_INT);

		if( !empty($messageId) )
		{
			return fetch_value("SELECT `description`
				FROM `pmt_message`
				WHERE `id` = '{$messageId}'
				LIMIT 1");

		}

		return false;
	}

	private static function sendEmail($data)
	{

		if( !empty($data) )
		{

			$fromName         = $data['from_name'];
			$fromEmailAddress = $data['from_email_address'];
			$toEmailAddress   = $data['to_email_address'];
			$subject          = $data['subject'];
			$emailBody        = $data['email_body'];

			$mailer = new PHPMailer();

			$mailer->IsHTML();
			$mailer->AddReplyTo($fromEmailAddress);
			$mailer->AddAddress($toEmailAddress);
			$mailer->SetFrom($fromEmailAddress);
			$mailer->FromName = $fromName;
			$mailer->Subject  = $subject;
			$mailer->msgHTML($emailBody);

			return $mailer->Send();
			
		}

		return false;
	}

	private static function processTemplate($path, $tags = array(), $start_tag = '{', $end_tag = '}')
	{

		if(file_exists($path))
		{
			// read email tempalte file
			$template = file_get_contents($path);

			// replace tags with value
			foreach ($tags as $tag => $value)
			{
				$value    = ($value) ? $value : '';
				$template = str_replace("$start_tag{$tag}$end_tag", $value, $template);
			}

			return $template;
			
		}

		return false;

	}

	private static function createUniqueId($length = 15)
	{

		$id = (uniqid().session_id());
		$id = str_shuffle($id);

		$id = ( $length > 0 ) ? substr($id, 0, $length) : $id;

		return $id;

	}

	private static function getCurrentDateTime()
	{

		$current_date = new DateTime();

		return $current_date->format('Y-m-d H:i:s');

	}

	private function setConfig($config)
	{
		if( !empty($config) )
		{
			
			foreach ($config as $property => $value)
			{

				if( property_exists($this, $property) )
				{
					$this->$property = $value;
				}
			}
		}
	}


};

?>