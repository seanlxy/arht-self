<?php


$ex_query = '';

if( $page_id === $page_destinations->id && $segment2 )
{
	$rcruise_id = fetch_value("SELECT c.`id` 
		FROM `cruise` c
		LEFT JOIN `page_meta_data` pmd
		ON(pmd.`id` = c.`page_meta_data_id`)
		WHERE pmd.`status` = 'A'
		AND pmd.`url` = '{$segment2}'
		LIMIT 1");


	if( $rcruise_id )
	{
		$ex_query = "AND `cruise_id` = '{$rcruise_id}'";
	}

}

$customer_review = fetch_row("SELECT CONCAT(`person_name`, ', ', `person_location`) AS person_details,`description`
	FROM `review`
	WHERE `status` = 'A'
	AND `type` = 'P'
	{$ex_query}
	ORDER BY RAND()
	LIMIT 1");


$admin_review = fetch_row("SELECT CONCAT(`person_name`, ', ', `person_location`) AS person_details,`description`
	FROM `review`
	WHERE `status` = 'A'
	AND `type` = 'A'
	{$ex_query}
	ORDER BY RAND()
	LIMIT 1");



if( $customer_review || $admin_review )
{

	$reviews_view = '<div class="top container">
	    <div class="row">
	       <div class="col-xs-12">
	       		'.(($customer_review) ? '<div class="testm bdr">
	                <h2>What other cruisers say</h2>
	                <p>"'.$customer_review['description'].'"</p>
	                <p class="name">'.$customer_review['person_details'].'</p>
	                <div>
	                    <a href="'.$page_reviews->full_url.'?type=1">Read more reviews</a>
	                </div>
	            </div>' : '').(($admin_review) ? '<div class="testm">
	                <h2>What we say</h2>
	                <p>"'.$admin_review['description'].'"</p>
	                <p class="name">'.$admin_review['person_details'].'</p>
	                <div>
	                    <a href="'.$page_reviews->full_url.'?type=2">Read more reviews</a>
	                </div>
	            </div>' : '').'
	       </div>
	    </div>
	</div>';

}


?>