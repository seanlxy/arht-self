<?php 

$achievements_query = fetch_all("SELECT * FROM `achievements`");

if (!empty($achievements_query)) {

	$achievement_content = '';


	foreach ($achievements_query as $key => $achievement) {

		$achievement_icon = $achievement['achievement_icon_class'];
		$achievement_count = $achievement['achievement_count_details'];
		$achievement_description = $achievement['achievement_description'];

		if (!empty($achievement_icon) && !empty($achievement_count) && !empty($achievement_description)) {
			$achievement_content .= <<<H

				<div class="col-md-4 section__count">
	                <i class="fa {$achievement_icon} section__count__icon"></i>
	                <div class="section__count__text">
	                    <h2 class="section__count__text__number"><span class="section__count__text__number--span">{$achievement_count}</span></h2>
	                    {$achievement_description}
	                </div>                        
	            </div>

H;
		}

	}	

}

$tags_arr['achievements'] = <<<H
	<section class="section section--blue-bg">
        <div class="container">
            <div class="row donation-btn-group">

                {$achievement_content}
                      
            </div>
        </div>
    </section>

H;

 ?>