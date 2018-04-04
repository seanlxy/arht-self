<?php

$tabs = array(
	
	array(
		'label' => 'Itinerary',
		'file_name' => 'itinerary',
		'content_id' => 'itinerary',
		'view_var' => 'itinerary_view'
	),
	array(
		'label' => 'Ship Details',
		'file_name' => 'ship_details',
		'content_id' => 'ship-details',
		'view_var' => 'ship_details_view'
	),
	array(
		'label' => 'Room Grades',
		'file_name' => 'room_grades',
		'content_id' => 'room-grades',
		'view_var' => 'room_grades_view'
	),
	array(
		'label' => 'Route Map',
		'file_name' => 'map',
		'content_id' => 'map',
		'view_var' => 'map_view'
	),
	array(
		'label' => 'Photos',
		'file_name' => 'photos',
		'content_id' => 'photos',
		'view_var' => 'photos_view'
	),
	array(
		'label' => 'Options',
		'file_name' => 'options',
		'content_id' => 'options',
		'view_var' => 'options_view'
	),
	array(
		'label' => 'Inclusions',
		'file_name' => 'inclusions',
		'content_id' => 'inclusions',
		'view_var' => 'inclusions_view'
	),
	array(
		'label' => 'Book Now',
		'tab_cls' => 'orange',
		'file_name' => 'booking',
		'content_id' => 'booking',
		'view_var' => 'booking_view'
	),
);

$tab_nav     = '';
$tab_content = '';


if( !empty($tabs) )
{

	$c = 0;
	
	foreach ($tabs as $i => $tab)
	{
		$label          = $tab['label'];
		$file_name      = $tab['file_name'];
		$content_id     = $tab['content_id'];
		$tab_cls        = ( $tab['tab_cls'] ) ? ' orange' : '';
		$tab_cls        = ( $tab['tab_cls'] ) ? ' orange' : '';
		
		
		//  Include tab file
		$file_path = "tabs/{$file_name}.php";
		require_once $file_path;

		$view_var   = ${"{$tab['view_var']}"};

		if( $view_var )
		{
			$active_cls = (($c==0) ? '  active' : '');

			$tab_nav .= '<li'.(($active_cls) ? '  class="'.$active_cls.'"' : '').'>
			<a href="#'.$content_id.'"'.(($tab_cls) ? ' class="'.$tab_cls.'"' : '').'><span>'.$label.'</span></a>
			</li>';


			$tab_content .= '<div class="tab-content'.$active_cls.'" id="'.$content_id.'">
				<a href="#'.$content_id.'" class="mob-tab visible-xs visible-sm'.$active_cls.'">'.$label.'</a>
				<div class="inner">'.$view_var.'</div>
			</div>';

			$c++;

		}

	
	}

}


if( $tab_content )
{

$cruise_view = <<< H

<div class="container hidden-xs hidden-sm">
	<div class="row">
		<div class="col-xs-12">
			<ul class="tabs" id="ux-tabs">
				{$tab_nav}
			</ul>
		</div>
	</div>
</div>

<div class="tab-wrap" id="ux-tabs-content">
	<div class="container">
		<div class="row">
			<div class="col-xs-12">
				{$tab_content}
			</div>
		</div>
	</div>
</div>


H;

}


$tags_arr['mod_view'] .= $cruise_view;

?>