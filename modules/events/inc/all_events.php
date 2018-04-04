<?php 

$event_single = '';

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
	
	$intro_short = (strlen($introduction) > 80 ) ? substr($introduction, 0, 80).'...' : $introduction; 

    $region = (!empty($event_region)) ? '<h5 class="events__details__location"><i class="fa fa-map-marker events__details__location__icon"></i>'.$event_region.'</h5>' : '';

	$event_single .= <<<H
		<div class="col-md-4 events">
            <div class="events__image events__image--red-border" style="background-image: url('{$thumb_photo}');">

            </div>
            <div class="events__details">
                <h5 class="events__details__date">{$start_date}{$end_date}</h5>
                <h4 class="events__details__title">{$heading}</h4>
                {$region}
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
                {$event_single}
            </div>
            
        </div>
    </section>
H;

$tags_arr['mod_view'] = $events_view;

 ?>