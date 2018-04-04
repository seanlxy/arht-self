<?php

$video_view = '';

if( sanitize_var( $vedio_url, FILTER_VALIDATE_URL ) )
{
	$video_view = '<div class="embed-responsive embed-responsive-16by9">
	 	<iframe class="embed-responsive-item" src="'.$vedio_url.'" frameborder="0" allowfullscreen></iframe>
	</div>';
}

?>