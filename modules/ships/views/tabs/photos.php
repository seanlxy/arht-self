<?php

$photos_view = '';


if( $photos_count > 0  && $gallery_id )
{

	$gallery_photos = fetch_all("SELECT `full_path`, `thumb_path`, CONCAT(`width`, 'x', `height`) AS size
		FROM `photo`
		WHERE `photo_group_id` = '{$gallery_id}'
		AND `thumb_path` != ''
		ORDER BY `rank`");

	if( !empty($gallery_photos) )
	{

		$photos_view = '<div class="inner"><div class="gallery">';

		foreach ($gallery_photos as $i => $gallery_photo)
		{

			$i++;
			
			$photos_view .= '<div class="item">
				<a href="'.$gallery_photo['full_path'].'" data-size="'.$gallery_photo['size'].'" class="launch-gallery">
					<img src="'.$gallery_photo['thumb_path'].'" alt="'.$heading.' Photo '.$i.'" />
				</a>	
			</div>';
		}

		$photos_view .= '</div></div>';


	}

}

?>