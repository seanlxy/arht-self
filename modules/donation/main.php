<?php 

$all_donations = fetch_all("SELECT dt.`page_meta_data_id`, dt.`icon_cls`,
	dt.`is_promoting`, pmd.`name`, pmd.`status`, pmd.`rank`, dt.`id` AS `page_id`,
	pmd.`full_url`
    FROM `donation_type` dt
    LEFT JOIN `page_meta_data` pmd
    ON(dt.`page_meta_data_id` = pmd.`id`)
    WHERE pmd.`status` = 'A'
    ORDER BY dt.`is_promoting`, pmd.`rank`");


require_once('inc/donation_nav.php');


if ($page == $page_home->url && !$segment1)
{
	require_once('inc/homepage_donation.php');
}
elseif ($page == $page_donations->url) {
	require_once('inc/form_process/vars.php');

	if (isset($_POST['submit']) && $form_is_valid) {
		require_once('inc/form_process/donation_insert.php');

		if ($process_payment) {
			require_once('inc/payment_process/pxpay.php');
			send_info();
		}

	}
	elseif($_GET['result'])	{
		require_once('inc/payment_process/pxpay.php');
		$donation_result = get_result_from_dps();


		if(isset($_GET['success'])) // If successful transaction include success file
		{
			require_once('inc/donation_form.php');
			require_once('inc/form_process/success.php');
		}
		elseif(isset($_GET['error'])) // Load shopping cart and form if transaction was unsuccessful
		{				
			require_once('inc/form_process/error.php');

			require_once('inc/donation_form.php');
		}
	}
	else{
		require_once('inc/donation_form.php');
	}	

	require_once('inc/otherpage_donation.php');
	
}







 ?>