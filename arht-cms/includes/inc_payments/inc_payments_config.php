<?php


function config()
{
	global $message, $id, $htmladmin, $do, $obj_payment,
		$disable_menu, $valid, $main_heading, $js_vars;


	$disable_menu      = "true";
	$valid             = 1;
	$pr_templates_view = '';

    $main_heading = "Payment Requests - Config";
	

	if( filter_input(INPUT_POST, 'update-accounts', FILTER_VALIDATE_INT) === 1 )
	{


		$account_users         = $_POST['account-user'];
		$account_keys          = $_POST['account-key'];
		$accounts_credit_cards = $_POST['account-cc'];


		if( !empty($account_users) )
		{

			$acc_query           = '';
			$acc_cc_query        = '';
			$acc_cc_delete_query = '';

			foreach ($account_users as $account_id => $account_user)
			{
				$acc_query .= ",('{$account_id}','{$account_user}','{$account_keys[$account_id]}')";

				$account_credit_cards = $accounts_credit_cards[$account_id];

				$acc_cc_delete_query .= ",{$account_id}";


				if( !empty($account_credit_cards) )
				{

					$acc_cc_query .= ",({$account_id},".implode("),({$account_id},", $account_credit_cards).")";

				}
				
			}



		
			if( !empty($acc_query) )
			{

				$acc_query           = ltrim($acc_query, ',');
				$acc_cc_delete_query = ltrim($acc_cc_delete_query, ',');
				$acc_cc_query        = ltrim($acc_cc_query, ',');


				run_query("INSERT INTO `pmt_account`(`id`, `user`, `api_key`) VALUES {$acc_query} ON DUPLICATE KEY UPDATE `user` = VALUES(`user`), `api_key` = VALUES(`api_key`)");

				
				if( !empty($acc_cc_delete_query) )
				{
					run_query("DELETE FROM `pmt_account_has_pmt_credit_card` WHERE `pmt_account_id` IN({$acc_cc_delete_query})");
				}

				if( !empty($acc_cc_query) )
				{
					run_query("INSERT INTO `pmt_account_has_pmt_credit_card`(`pmt_account_id`, `pmt_credit_card_id`) VALUES {$acc_cc_query}");
				}
				

			}

		}


		$_SESSION['flash_msg'] = 'Changes has been saved successfully.';
		header("Location: {$htmladmin}?do={$do}&action=config");
		exit();

	}
    else
    {

    	$payment_accounts = $obj_payment->getAccounts();

		$payment_credit_cards  = $obj_payment->getCreditCards();

		$accounts_credit_cards = $obj_payment->getAccountCreditCards();


    	if( !empty($payment_accounts) )
    	{

    		foreach($payment_accounts as $i => $payment_account)
    		{
				$payment_account_id      = $payment_account['id'];
				$payment_account_is_test = ($payment_account['is_live'] === Payment::FLAG_YES) ? '<span class="label label-success" style="vertical-align:middle;margin:-5px 0 0 7px;">LIVE</span>' : '<span style="vertical-align:middle;margin:-5px 0 0 7px;" class="label label-default">TEST</span>';


				// CREDIT CARDS VIEW
				$cc_view = '';

				if( $payment_account['has_cc'] === Payment::FLAG_YES  )
				{

					foreach ($payment_credit_cards as $credit_card)
					{

						$credit_card_id = $credit_card['id'];

						$is_checked = !empty( $accounts_credit_cards[$payment_account_id][$credit_card_id] );

						$cc_view .= '<label class="checkbox-inline">
							<input style="margin-top:1px;" type="checkbox" name="account-cc['.$payment_account_id.'][]" value="'.$credit_card_id.'"'.(($is_checked) ? ' checked' : '').'> 
						'.$credit_card['name'].'</label>';

					}


					$cc_view = ' <tr>
	                    <td width="90" valign="top"><strong>Credit Cards:</strong></td>
	                    <td>
	                        '.$cc_view.'
	                    </td>
	                </tr>';
					
				}



				$hr = ( $i != 0 ) ? '<tr>
                    <td colspan="2" valign="top">
                    	<hr style="border-color:#000;">
                	</td>
                </tr>' : '';

    			$pr_templates_view .= $hr.'<tr>
                    <td colspan="2" valign="top">
                    	<strong style="font-size:17px;">'.$payment_account['label'].'</strong> '.$payment_account_is_test.'
                	</td>
                </tr>
                <tr>
                    <td width="90" valign="top"><label for="account-user-{$i}">User:</label></td>
                    <td>
                        <input name="account-user['.$payment_account_id.']" type="text" id="account-user-{$i}" value="'.$payment_account['user'].'" style="width:300px;" autocomplete="off" />
                    </td>
                </tr>
                <tr>
                    <td width="90" valign="top"><label for="account-key-{$i}">API Key:</label></td>
                    <td>
                        <input name="account-key['.$payment_account_id.']" type="text" id="account-key-{$i}" value="'.$payment_account['api_key'].'" style="width:650px;" autocomplete="off" />
                    </td>
                </tr>'.$cc_view;


    		}


    		$pr_templates_view = '<div id="tabs">
    			<div style="padding:10px;">
					<table width="100%" border="0" cellspacing="0" cellpadding="5">
					'.$pr_templates_view.'
					</table>
					<input type="hidden" name="update-accounts" value="1">
				</div>
			</div>';

    	}


    	$page_functions = <<< HTML

           <ul class="page-action">
                <li><button type="button" class="btn btn-default" onclick="submitForm('update-accounts', 1)"><i class="glyphicon glyphicon-save"></i> Update</button></li>
                <li><a class="btn btn-default" href="{$htmladmin}/?do={$do}"><i class="glyphicon glyphicon-arrow-left"></i> Back</a></li>
            </ul>
HTML;

    }

   if ($message != "") {

        $page_contents .= <<< HTML
          <div class="alert alert-warning page">
             <i class="glyphicon glyphicon-info-sign"></i>
              <strong>$message</strong>
          </div>
HTML;
    }


	##------------------------------------------------------------------------------------------------------
	## tab arrays and build tabs

	$temp_array_menutab = array();

	$temp_array_menutab['Details'] = $pr_templates_view;


	$counter = 0;
	$tablist ="";
	$contentlist="";

	foreach($temp_array_menutab as $key => $value)
	{

	    $tablist.= "<li><a href=\"#tabs-".$counter."\">".$key."</a></li>";

	    $contentlist.=" <div id=\"tabs-".$counter."\">".$value."</div>";

	    $counter++;
	}


	// $tablist="<div id=\"tabs\"><ul>$tablist</ul><div style=\"padding:10px;\">{$contentlist}</div></div>";

	    $page_contents.="<form action=\"$htmladmin/?do={$do}&action=config\" method=\"post\" name=\"pageList\" enctype=\"multipart/form-data\">
	        {$pr_templates_view}
	    </form>";

	require "resultPage.php";
	echo $result_page;
	exit();
}

?>