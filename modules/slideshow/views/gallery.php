<?php

$primary_photo_details = fetch_row("SELECT pp.`photo_group_id`, pp.`full_path`,
	(SELECT COUNT(`id`)
		FROM `photo`
		WHERE `photo_group_id` = pp.`photo_group_id`
		AND pp.`full_path` != ''
	) AS total_photos
	FROM `photo` pp
	WHERE pp.`photo_group_id` = '{$slideshow_id}'
	ORDER BY pp.`rank`
	LIMIT 1");

if( $primary_photo_details )
{

	$photo_group_id = $primary_photo_details['photo_group_id'];

	$slideshow_view = '<div id="slider-container">';

	if ($primary_photo_details['total_photos'] > 1) {
		$slideshow_view .= '<a href="#" class="gallery-nav" data-launch-gallery="'.$photo_group_id.'"><i class="fa fa-chevron-left"></i> More Photos <i class="fa fa-chevron-right"></i></a>';
	}
	

	$slideshow_view .= '<div id="slideshow" class="has-bg" style="background-image:url('.$primary_photo_details['full_path'].');" data-launch-gallery="'.$photo_group_id.'">';
	$slideshow_view .= '</div>';
	
	$slideshow_view .= '</div>';

	$jsVars['data']['initGallery']       = true;
	$jsVars['templates']['galleryModal'] = file_get_contents("{$tmpldir}/underscore/gallery.tmpl");


}

?>