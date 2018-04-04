<?php

$cruises = fetch_all("SELECT c.`public_token` AS cruise_token, c.`no_of_days`, c.`no_of_nights`,  IF(c.`standard_price`, c.`standard_price`, 'POA') AS rate,
	pmd.`full_url`, pmd.`menu_label` AS cruise_name, pmd.`photo`, pmd.`thumb_photo`, pmd.`title`, dpmd.`menu_label` AS region_label, spmd.`menu_label` AS ship_label, 
	CONCAT(dp.`name`, ', ', dpc.`name`) AS departure_port, CONCAT(ap.`name`, ', ', apc.`name`) AS arrival_port, cr.`code` AS currency_code,  cr.`symbol` AS currency_symbol,
	s.`no_of_guests`, s.`no_of_crew_members`
    FROM `cruise` c
    LEFT JOIN `page_meta_data` pmd
    ON(pmd.`id` = c.`page_meta_data_id`)
    LEFT JOIN `port` dp
    ON(dp.`id` = c.`depart_port_id`)
    LEFT JOIN `country` dpc
    ON(dpc.`id` = dp.`country_id`)
    LEFT JOIN `port` ap
    ON(ap.`id` = c.`arrival_port_id`)
    LEFT JOIN `country` apc
    ON(apc.`id` = ap.`country_id`)
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
    AND c.`public_token` IN('".implode("','", $shortlist)."')
    ORDER BY pmd.`rank`
    LIMIT {$shortlist_limit}");

if( !empty($cruises) )
{

	$compare_thumb_view    = '';
	$compare_rate_view     = '';
	$compare_dept_view     = '';
	$compare_arrival_view  = '';
	$compare_duration_view = '';
	$compare_region_view   = '';
	$compare_ship_view     = '';
	$compare_dates_view    = '';
	$compare_links_view    = '';



	foreach ($cruises as $i => $cruise)
	{

		$i++;

		$cell_cls = ( $i%2 == 0 ) ? ' tbl__cell--even' : '';
		

		$full_url    = $cruise['full_url'];
		$thumb_photo = $cruise['thumb_photo'];
		$title       = $cruise['title'];

		$cruise_public_token = $cruise['cruise_token'];

		$departure_dates = ( $cruise['departure_dates'] ) ? $cruise['departure_dates'] : '-';

		$compare_thumb_view    .= '<div class="tbl__cell'.$cell_cls.'">
			<img src="'.$thumb_photo.'" alt="'.$title.'" class="compare__img">
			<h3 class="compare__label"><a href="'.$cruise['full_url'].'" title="'.$cruise['title'].'">'.$cruise['cruise_name'].'</a></h3>
		</div>';
		########

		$compare_rate_view     .= '<div class="tbl__cell'.$cell_cls.'">
			<div class="compare__price">
				FROM '.$cruise['currency_code'].' '.$cruise['currency_symbol'].$cruise['rate'].' PER PERSON
			</div>
		</div>';
		########

		$compare_dept_view     .= '<div class="tbl__cell'.$cell_cls.'">
			<hr class="tbl__cell--divider">
			<div class="compare__item">
				<strong class="compare__item--lbl">Departing From:</strong> '.$cruise['departure_port'].'
			</div>
		</div>';
		########

		$compare_arrival_view  .= '<div class="tbl__cell'.$cell_cls.'">
			<hr class="tbl__cell--divider">
			<div class="compare__item">
				<strong class="compare__item--lbl">Final Destination:</strong> '.$cruise['arrival_port'].'
			</div>
		</div>';
		########

		$compare_duration_view .= '<div class="tbl__cell'.$cell_cls.'">
			<hr class="tbl__cell--divider">
			<div class="compare__item">
				<strong class="compare__item--lbl">Duration:</strong> '.$cruise['no_of_days'].' Days/ '.$cruise['no_of_nights'].' Nights
			</div>
		</div>';
		########

		$compare_region_view   .= '<div class="tbl__cell'.$cell_cls.'">
			<hr class="tbl__cell--divider">
			<div class="compare__item">
				<strong class="compare__item--lbl">Region:</strong> '.$cruise['region_label'].'
			</div>
		</div>';
		########

		$compare_ship_view     .= '<div class="tbl__cell'.$cell_cls.'">
			<hr class="tbl__cell--divider">
			<div class="compare__item">
				<strong class="compare__item--lbl">Ship:</strong> '.$cruise['ship_label'].'
			</div>
		</div>';
		########

		$compare_pax_view     .= '<div class="tbl__cell'.$cell_cls.'">
			<hr class="tbl__cell--divider">
			<div class="compare__item">
				<strong class="compare__item--lbl">Passengers:</strong> '.$cruise['no_of_guests'].'
			</div>
		</div>';
		########

		$compare_crew_view     .= '<div class="tbl__cell'.$cell_cls.'">
			<hr class="tbl__cell--divider">
			<div class="compare__item">
				<strong class="compare__item--lbl">Crew:</strong> '.$cruise['no_of_crew_members'].'
			</div>
		</div>';
		########

		$compare_links_view .= '<div class="tbl__cell'.$cell_cls.'">
			<hr class="tbl__cell--divider">
			<div class="compare__item">
                <a href="'.$cruise['full_url'].'" title="'.$cruise['title'].'" class="btn compare__item--link">MORE INFO</a>
			</div>
		</div>';



	}

	$tbl_cls .= ( $shortlist_count == 1 ) ? ' tbl--col-1' : '';

	if( $shortlist_count == 1 )
	{
		$tags_arr['heading'] = 'Choose another cruise to compare with this one.';
	}

	$compare_list_view = <<< H

<div class="compare">

<div class="tbl{$tbl_cls}">
	<div class="tbl__row">
		{$compare_thumb_view}
	</div>
	<div class="tbl__row">
		{$compare_rate_view}
	</div>
	<div class="tbl__row">
		{$compare_region_view}
	</div>
	<div class="tbl__row">
		{$compare_dept_view}
	</div>
	<div class="tbl__row">
		{$compare_arrival_view}
	</div>
	<div class="tbl__row">
		{$compare_duration_view}
	</div>
	<div class="tbl__row">
		{$compare_ship_view}
	</div>
	<div class="tbl__row">
		{$compare_pax_view}
	</div>
	<div class="tbl__row">
		{$compare_crew_view}
	</div>
	<div class="tbl__row">
		{$compare_dates_view}
	</div>
	<div class="tbl__row">
		{$compare_links_view}
	</div>

</div>

<div class="text-center" style="margin-top:40px;">
	<a class="btn" href="#" id="destroy-comparison-list">Close comparison</a>
</div>

</div>

H;

}

$jsVars['globals']['doCompareReload'] = true;

?>