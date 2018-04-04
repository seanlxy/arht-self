<?php

## variable $destinations_list defined in /includes/components/nav/header.php on line 5

if( !empty($destinations_list) )
{
	$destinations_view = '
	<div class="row">
	    <div class="col-xs-12">
			<div class="grid-wrapper square">
			    <header><p class="h2 intro line"><span>Find your destination</span></p></header>
			    <div class="grid">';


	foreach ($destinations_list as $destination)
	{

		$thumb_photo = $destination['thumb_photo'];
		$title       = ($destination['title']) ? ' title="'.$destination['title'].'"' : '';

		$destinations_view .= '<div class="col">
            <a href="'.$destination['full_url'].'" style="background-image:url('.$thumb_photo.');"'.$title.'>
                <span class="inner">
                    <span class="name">'.$destination['menu_label'].'</span>
                    <span class="descr">'.$destination['short_description'].'</span>
                    <span class="btn">explore</span>
                </span>
            </a>
        </div>';
	}


	$destinations_view .= '
				</div><!-- /.grid -->
			</div><!-- /.grid-wrapper.square -->
		</div>
		'.(($page_home->id == $page_id) ? '<div class="col-xs-12 text-center">
	        <p><a href="'.$page_destinations->full_url.'" title="'.$page_destinations->title.'" class="btn">find out more</a></p>
	    </div>' : '').'
	</div>';

}

$destinations_view = '<div class="container">'.$destinations_view.'</div>';

$destinations_view .= '<div id="map-canvas" class="xl"></div>';

$tags_arr['mod_view'] .= $destinations_view;


$tags_arr['scripts-load-top'] .= '<script src="https://maps.googleapis.com/maps/api/js"></script>';
$tags_arr['scripts-load-top'] .= '<script src="'.get_file_path('/assets/js/libs/markerclusterer.min.js').'"></script>';

?>