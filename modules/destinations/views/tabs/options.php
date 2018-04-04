<?php

$options_view         = '';
$options_booking_view = '';

$options = fetch_all("SELECT `id`, `public_token`, `heading`, `description`,
	IF(`price_from`, FORMAT(REPLACE(`price_from`, '.00', ''), 0), 'POA') AS price, `pricing_notes`, `thumb_photo_path`
	FROM `cruise_option`
	WHERE `cruise_id` = '{$main_cruise_id}'
	ORDER BY `rank`");


if( !empty($options) )
{

	$shortlisted_options = ( isset($_SESSION['options']) ) ? $_SESSION['options'] : array();




	foreach ($options as $option)
	{

		$option_name         = $option['heading'];
		$option_public_token = $option['public_token'];

		$option_is_shortlisted = in_array($option_public_token, $shortlisted_options);

		$shortlist_item_cls   = ( $option_is_shortlisted ) ? ' added' : '';
		$shortlist_item_title = ( $option_is_shortlisted ) ? 'remove' : 'request booking';
		$booking_opt_checked  = ( $option_is_shortlisted ) ? ' checked="checked"' : '';


		$options_view .= '<div class="option">
			<div class="row">
				<div class="col-xs-12 col-sm-6 col-sm-push-6 col-md-8 col-md-push-4">
					<h3>'.$option_name.'</h3>
					<p>'.$option['description'].'</p>
					<div class="price-detail">FROM
						<span class="custom-dd" data-type="cruise-option" data-selection="'.$currency_code.'" data-token="'.$option_public_token.'">
							<i class="glyphicons glyphicons-chevron-down"></i><span class="current">'.$currency_code.'</span>
							<ul>
								'.$currency_list.'
							</ul>
						</span>
						<span class="price"><small>'.$currency_symbol.'</small><span class="val">'.$option['price'].'</span></span>
						<span class="text-uppercase">'.$option['pricing_notes'].'</span> 
						<span>(<span class="sel-currency">Indicative pricing in <span>NZD</span>. </span>Sold in '.$currency_code.')</span>
					</div>
					<a href="#" data-token="'.$option_public_token.'" data-type="option" class="btn toggle-book'.$shortlist_item_cls.'">'.$shortlist_item_title.'</a>
				</div>
				<div class="col-xs-12 col-sm-6 col-sm-pull-6 col-md-4 col-md-pull-8">
					<img src="'.$option['thumb_photo_path'].'" alt="'.$option_name.'" />
				</div>
			</div>
		</div>';


		$options_booking_view .= '<div class="row booking-sel'.$shortlist_item_cls.'">
			<div class="col-xs-12 col-sm-5 col-lg-4">
				<label class="custom-check">
					<input type="checkbox" name="booking-cruise-option[]" value="'.$option_public_token.'"'.$booking_opt_checked.'>
					<span></span>
					'.$option_name.'
				</label>
			</div>
			<div class="col-xs-12 col-sm-7 col-lg-8">
				<div class="price-detail">FROM
					'.$currency_code.'
					<span class="price"><small>'.$currency_symbol.'</small><span class="val">'.$option['price'].'</span></span>
					<span class="text-uppercase">'.$option['pricing_notes'].'</span> 
					<span>(<span class="sel-currency">Indicative pricing in <span>NZD</span>. </span>Sold in '.$currency_code.')</span>
				</div>
			</div>
		</div>';

	}


	$options_view = '<div class="row">
		<p class="col-xs-12">The following additional options may be added to your cruise.</p>
	</div>
	<div class="row">
		<div class="col-xs-12">
			'.$options_view.'
		</div>
	</div>';
}



?>