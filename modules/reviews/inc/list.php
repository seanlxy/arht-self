<?php


if($page_url == $page_reviews->url)
{

	$review_type = 'P';
	$review_list = '';

	if(isset($_GET['type']) && $_GET['type'] == '2')
	{
		$review_type = 'A';
	}

	$sql = "SELECT CONCAT(`person_name`, ', ', `person_location`) AS person_details,`description`
			FROM `review`
			WHERE `status` = 'A'
			AND `type` = '$review_type'
			ORDER BY `rank`";

	$review_arr = fetch_all($sql);

	if(!empty($review_arr))
	{
		foreach ($review_arr as $review) {
			
			$review_list .= <<<H

				<div class="row">
					<div class="col-xs-12">
						<div class="testm">
			                <p>"{$review['description']}"</p>
			                <p class="name">{$review['person_details']}</p>
			            </div>
					</div>
				</div>
H;
		}
	}

	$tags_arr['content'] .= $review_list;
}


?>