<?php 

$cruise_view = '';


$cruises = fetch_all("SELECT c.`public_token` AS token, c.`no_of_days`, c.`no_of_nights`,  IF(c.`standard_price`, c.`standard_price`, 'POA') AS rate, 
	c.`is_featured`, c.`on_special`, pmd.`menu_label`, pmd.`title`, pmd.`full_url`, pmd.`thumb_photo`, pmd.`short_description`,
	cr.`code` AS currency_code, cr.`symbol` AS currency_symbol, dpmd.`menu_label` AS region_label, spmd.`menu_label` AS ship_label
	FROM `cruise` c

	LEFT JOIN `page_meta_data` pmd
	ON(pmd.`id` = c.`page_meta_data_id`)

	LEFT JOIN `currency` cr
	ON(cr.`id` = c.`currency_id`)

	LEFT JOIN `destination` d
	ON(d.`id` = c.`destination_id`)

	LEFT JOIN `page_meta_data` dpmd
	ON(dpmd.`id` = d.`page_meta_data_id`)

	LEFT JOIN `ship` s
	ON(s.`id` = c.`ship_id`)

	LEFT JOIN `page_meta_data` spmd
	ON(spmd.`id` = s.`page_meta_data_id`)

	WHERE pmd.`status` = 'A'
	AND s.`id` = '{$ship_id}'
	ORDER BY pmd.`rank`");


if( !empty($cruises) )
{


foreach ($cruises as $cruise)
{

	$full_url    = $cruise['full_url'];
	$thumb_photo = $cruise['thumb_photo'];
	$title       = $cruise['title'];

	$is_featured = ( $cruise['is_featured'] === 'Y' ) ? '<span class="feat">Featured Expedition</span>' : '';
	$on_special  = ( $cruise['on_special'] === 'Y' ) ? '<span class="spec"> <i class="glyphicons glyphicons-star"></i>Special</span>' : '';


	$cruise_public_token = $cruise['token'];

	$is_shortlisted = in_array($cruise_public_token, $shortlist);
	
	$compare_btn_cls   = ($is_shortlisted) ? 'minus' : 'plus';
	$compare_btn_title = ($is_shortlisted) ? 'Remove from compare list.' : 'Add to compare list.';
	$compare_btn_label = ($is_shortlisted) ? 'Remove' : 'Compare';

	$cruise_view .= '<section class="list-item loaded">
	<div class="container">
		<div class="row">
			<div class="col-xs-12 col-sm-7 col-sm-push-5">
				<h3 class="serif"><a href="'.$full_url.'" title="'.$title.'">'.$cruise['menu_label'].'</a></h3>
				<div class="cruise-detail">
					<p class="price">From '.$cruise['currency_code'].' '.$cruise['currency_symbol'].$cruise['rate'].' <span>Per Person</span></p>
					<p>
						<span>DURATION:<span>'.$cruise['no_of_days'].' Days/ '.$cruise['no_of_nights'].' Nights</span></span>
						<span>REGION:<span>'.$cruise['region_label'].'</span></span>
						<span>SHIP:<span>'.$cruise['ship_label'].'</span></span>
					</p>
				</div>
				<p>'.$cruise['short_description'].'</p>
				<div class="btn-wrap">
					<a href="'.$full_url.'" title="'.$title.'" class="btn">Find Out More</a>
					<a href="'.$full_url.'#booking" title="Book '.$title.'" class="btn olight">Book Now</a>
            		<a href="#" data-shortlist="'.$cruise_public_token.'" class="comp'.(($is_shortlisted) ? ' added' : '').'" title="'.$compare_btn_title.'">
            			<i class="glyphicons glyphicons-circle-'.$compare_btn_cls.'"></i> 
            			<span>'.$compare_btn_label.'</span>
        			</a>
				</div>
			</div>
			<div class="col-xs-12 col-sm-5 col-sm-pull-7">
				<figure class="img" style="background-image:url('.$thumb_photo.');">
				'.$is_featured.$on_special.'
				</figure>
			</div>
		</div>
	</div>
</section>';
}

$cruise_view = <<< H

<div class="list-view" id="cruise-listing">
	<div class="container">
		<div class="row">
			<div class="col-xs-12">
				<h2 class="line text-uppercase"><span>{$ship_name} Expeditions</span></h2>
			</div>
		</div>
	</div>
	<div id="item-list-grid">'.$cruise_view.'</div>
</div>

H;

}



$tags_arr['mod_view'] .= $cruise_view;


?>