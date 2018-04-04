<?php 

$single_event_query = fetch_row("SELECT pmd.`name`, pmd.`status`, pmd.`rank`, e.`id`, e.`event_start_date`, e.`event_end_date`, e.`event_region`, 
			e.`page_meta_data_id`, pmd.`url`, pmd.`menu_label`, pmd.`heading`, pmd.`title`, pmd.`short_description`, pmd.`introduction`,
			pmd.`url`, pmd.`full_url`, pmd.`id`, pmd.`slideshow_id`,
 			pmd.`thumb_photo`
            FROM `events` e
            LEFT JOIN `page_meta_data` pmd
            ON(e.`page_meta_data_id` = pmd.`id`)
            WHERE pmd.`status` = 'A'
            AND pmd.`url` = '{$segment1}' 
            ORDER BY pmd.`status`, pmd.`rank`");

if (!empty($single_event_query)) {
	extract($single_event_query);

	$tags_arr['heading-view'] = '';
	$tags_arr['introduction-view'] = '';
	$tags_arr['content'] = '';
	$tags_arr['mod_view'] = '';

	$start_date = date('d M Y', strtotime($event_start_date));
	$end_date = date('d M Y', strtotime($event_end_date));

	if ($start_date != $end_date) {
		$start_date = date('d M', $start_date);
		$start_date .= ' - ';
	}else{
		$start_date = '';
	}

	$region = (!empty($event_region)) ? $event_region.' |' : '';

	$tags_arr['heading-view'] = <<<H
		<header class="section__header text-center">
			<div class="section__header__highlight section__header__highlight--red"></div>
			<h1 class="section__heading section__heading--normal">{$heading}</h1>
			<h4 class="section__sub-heading text-center">{$region} {$start_date} {$end_date}</h4>
		</header>
H;

	$tags_arr['introduction-view'] = <<<H
		<p class="text-center">
			{$introduction}
		</p>
H;

	$content = get_content($id);

	if (!empty($content)) {
		$tags_arr['content'] = $content;
		        
	}


$event_query = "SELECT pmd.`name`, pmd.`status`, pmd.`rank`, e.`id`, e.`event_start_date`, e.`event_end_date`, e.`event_region`, e.`page_meta_data_id`,
 			pmd.`url`, pmd.`menu_label`, pmd.`heading`, pmd.`title`, pmd.`short_description`, pmd.`introduction`, pmd.`url`, pmd.`full_url`,
 			pmd.`thumb_photo`
            FROM `events` e
            LEFT JOIN `page_meta_data` pmd
            ON(e.`page_meta_data_id` = pmd.`id`)
            WHERE pmd.`status` = 'A'
            AND e.`event_start_date` <= e.`event_end_date`
            AND e.`event_end_date` >= CURDATE()
            AND pmd.`id` != {$id}
            ORDER BY pmd.`status`, pmd.`rank`, e.`event_start_date`
            LIMIT 3";

	$events = fetch_all($event_query);

	foreach ($events as $key => $event) {

		extract($event);

		$start_date = date('d M Y', strtotime($event_start_date));
		$end_date = date('d M Y', strtotime($event_end_date));

		if ($start_date != $end_date) {
			$start_date = date('d M', $start_date);
			$start_date .= ' - ';
		}else{
			$start_date = '';
		}
		
		$intro_short = (strlen($introduction) > 100 ) ? substr($introduction, 0, 200) : $introduction; 

		$event_single .= <<<H
			<div class="col-md-4 events">
	            <div class="events__image events__image--red-border" style="background-image: url('{$thumb_photo}');">

	            </div>
	            <div class="events__details">
	                <h5 class="events__details__date">{$start_date}{$end_date}</h5>
	                <h4 class="events__details__title">{$heading}</h4>
	                <h5 class="events__details__location"><i class="fa fa-map-marker events__details__location__icon"></i>{$event_region}</h5>
	                <p class="events__details__text">{$intro_short}</p>
	                <a href="{$page_events->full_url}{$full_url}" class="btn btn--outline events__details__link">Find Out More</a>
	            </div>
	        </div>
H;

	}

	$events_view = <<<H
		<section class="section section--grey-bg">
	        <div class="container">
				<div class="row">
					<header class="section__header text-center">
						<h1 class="section__heading section__heading--normal">More Events</h1>
					</header>
				</div>
	            <div class="row">
	                {$event_single}
	            </div>
	            
	        </div>
	    </section>
H;

	$tags_arr['mod_view'] = $events_view;


}

 ?>