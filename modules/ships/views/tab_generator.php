<?php

$tabs = array(
	
	array(
		'label' => 'Photos',
		'file_name' => 'photos',
		'content_id' => 'photos',
		'view_var' => 'photos_view'
	),
	array(
		'label' => 'Deck Plan',
		'file_name' => 'deck_plan',
		'content_id' => 'deck-plan',
		'view_var' => 'deck_plan_view'
	),
	array(
		'label' => 'Room Grades',
		'file_name' => 'room_grades',
		'content_id' => 'room-grades',
		'view_var' => 'room_grades_view'
	),
	array(
		'label' => 'Dining',
		'file_name' => 'dining',
		'content_id' => 'dining',
		'view_var' => 'dining_view'
	),
	array(
		'label' => 'Features',
		'file_name' => 'features',
		'content_id' => 'features',
		'view_var' => 'features_view'
	),
	array(
		'label' => 'Video',
		'file_name' => 'video',
		'content_id' => 'video',
		'view_var' => 'video_view'
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
		
		
		//  Include tab file
		$file_path = "tabs/{$file_name}.php";
		require_once $file_path;

		$view_var   = ${"{$tab['view_var']}"};

		if( $view_var )
		{
			$active_cls = (($c==0) ? '  active' : '');

			$tab_nav .= '<li'.(($active_cls) ? '  class="'.$active_cls.'"' : '').'><a href="#'.$content_id.'"><span>'.$label.'</span></a></li>';
			$tab_content .= '<div class="tab-content'.$active_cls.'" id="'.$content_id.'">
					<a href="#'.$content_id.'" class="mob-tab visible-xs visible-sm'.$active_cls.'">'.$label.'</a>
					<div class="inner">'.$view_var.'</div></div>';

			$c++;

		}

	
	}

}


if( $tab_content )
{

$ships_view = <<< H

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


$tags_arr['mod_view'] .= $ships_view;

?>