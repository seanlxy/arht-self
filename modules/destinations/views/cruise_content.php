<?php

$content = '';

$is_shortlisted = in_array($cruise_token, $shortlist);
	
$compare_btn_cls      = ($is_shortlisted) ? ' added' : '';
$compare_btn_icon_cls = ($is_shortlisted) ? 'minus' : 'plus';
$compare_btn_title    = ($is_shortlisted) ? 'Remove from compare list.' : 'Add to compare list.';
$compare_btn_label    = ($is_shortlisted) ? 'Remove' : 'Compare';

$departure_port_country = ($show_country_label) ? ", {$departure_port_country}" : '';
$arrival_port_country = ($show_country_label) ? ", {$arrival_port_country}" : '';

$content .= <<< H

<div class="row">
	<div class="col-xs-12 text-center">
		<div class="cruise-detail grey">
			<p><span>DEPARTING FROM:<span>{$departure_port}{$departure_port_country}</span></span><span>FINAL DESTINATION:<span>{$arrival_port}{$arrival_port_country}</span></span></p>
			<div>
				<a href="#" data-shortlist="{$cruise_token}" title="{$compare_btn_title}" class="comp{$compare_btn_cls}">
				<i class="glyphicons glyphicons-circle-{$compare_btn_icon_cls}"></i> 
				<span>{$compare_btn_label}</span>
				</a>
			</div>
		</div>
	</div>
</div>
<div class="row">
	<div class="col-xs-12 text-center">
		<div class="cruise-detail">
			<p><span>DURATION:<span>{$no_of_days} Days/ {$no_of_nights} Nights</span></span><span>REGION:<span>{$region_label}</span></span><span>SHIP:<span>{$ship_label}</span></span></p>
		</div>
	</div>
</div>

H;

$content .= get_content($page_meta_data_id);

$tags_arr['content'] = $content;


require_once 'tab_generator.php';

?>