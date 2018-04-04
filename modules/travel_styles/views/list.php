<?php

## variable $travel_styles_list defined in /includes/components/nav/header.php on line 13

if( !empty($travel_styles_list) )
{
	$travel_styles_view = '
	<div class="row">
	    <div class="col-xs-12">
			<div class="grid-wrapper square">
			    <header><p class="h2 intro line"><span>Find your Interests</span></p></header>
			    <div class="grid">';


	foreach ($travel_styles_list as $travel_style)
	{

		$thumb_photo = $travel_style['thumb_photo'];
		$title       = ($travel_style['title']) ? ' title="'.$travel_style['title'].'"' : '';

		$travel_styles_view .= '<div class="col">
            <a href="'.$travel_style['full_url'].'" style="background-image:url('.$thumb_photo.');"'.$title.'>
                <span class="inner">
                    <span class="name">'.$travel_style['menu_label'].'</span>
                    <span class="descr">'.$travel_style['short_description'].'</span>
                    <span class="btn">explore</span>
                </span>
            </a>
        </div>';
	}


	$travel_styles_view .= '
				</div><!-- /.grid -->
			</div><!-- /.grid-wrapper.square -->
		</div>
		'.(($page_home->id == $page_id) ? '<div class="col-xs-12 text-center">
	        <p><a href="'.$page_travel_styles->full_url.'" title="'.$page_travel_styles->title.'" class="btn">find out more</a></p>
	    </div>' : '').'
	</div>';

}

$travel_styles_view = '<div class="container" style="margin-bottom:25px;">'.$travel_styles_view.'</div>';


$tags_arr['mod_view'] .= $travel_styles_view;


?>