<?php

$room_grades_view = '';

if( $suites_count > 0 )
{

	$suites = fetch_all("SELECT REPLACE(ss.`size_in_sq_metre`, '.00', '') AS size_sq_metre, REPLACE(ss.`size_in_sq_feet`, '.00', '') AS size_sq_feet,
		ss.`no_of_sleeps`, ss.`heading`, ss.`pricing_notes`, ss.`description`, ss.`gallery_id`,
		(SELECT `thumb_path`
			FROM `photo`
			WHERE `photo_group_id` = ss.`gallery_id`
			AND `thumb_path` != ''
			ORDER BY `rank`
			LIMIT 1
		) AS primary_photo_path,
		(SELECT COUNT(`id`)
			FROM `photo`
			WHERE `photo_group_id` = ss.`gallery_id`
			AND `thumb_path` != ''
		) AS total_photos
		FROM `ship_suite` ss
		WHERE ss.`ship_id` = '{$ship_id}'
		AND ss.`status` = 'A'
		ORDER BY ss.`rank`");


	if( !empty( $suites ) )
	{

		$room_grades_view = '<div class="inner">';

		foreach ($suites as $suite)
		{

			$gallery_id   = $suite['gallery_id'];
			$total_photos = $suite['total_photos'];

			$gallery_view = '';

			if( $total_photos > 1 )
			{
				$gallery_view = '<ul data-gallery="'.$gallery_id.'">
					<li class="current" style="background-image:url('.$suite['primary_photo_path'].');" data-index="0"></li>
				</ul>
				<a href="#" class="slider-nav prev"><i class="glyphicons glyphicons-chevron-left"></i></a>
				<a href="#" class="slider-nav next"><i class="glyphicons glyphicons-chevron-right"></i></a>';
			}
			elseif( $total_photos == 1 )
			{
				$gallery_view = '<img src="'.$suite['primary_photo_path'].'" alt="'.$suite['heading'].'">';
			}

			$room_grades_view .= '<div class="room">
				<div class="row">
					<div class="col-xs-12">
						<h3>'.$suite['heading'].'</h3>
					</div>
				</div>
				<div class="row">
					<div class="col-xs-12 col-sm-6 col-md-7">
						<div class="cruise-detail">
							<p><span>SIZE:<span>'.$suite['size_sq_metre'].'m2 / '.$suite['size_sq_feet'].'sqft</span></span><span>SLEEPS:<span>'.$suite['no_of_sleeps'].'</span></span></p>
						</div>
						<p class="descr">'.$suite['description'].'</p>
					</div>
					<div class="col-xs-12 col-sm-6 col-md-5">
						<div class="slider">
							'.$gallery_view.'
						</div>
					</div>
				</div>
			</div>';

		}

		$room_grades_view .= '</div>';

	}
}

?>