<?php
$collections = $shop->getCollections();

$list = '';
$currCount = 1;
foreach($collections['custom_collections'] as $item){

	$id         = $item['id'];
	$title      = $item['title'];
	$detail     = $item['body_html'];
	$photo_path = $item['image']['src'];
	$title_url  = str_replace('+','-',strtolower($title));
	$title_url  = str_replace('\'','-',$title_url);
	$title_url  = str_replace(' ','-',$title_url);
	$full_url   = '/shop/'.$title_url;

if($currCount % 3 == 0){
	$vertical_hr = '';
}else {
	$vertical_hr = 'vertical_hr';
}

	$list .= <<<HTML
				<div class="col-xs-12 col-md-4 service__item__wrapper">
					<div class="service__item {$vertical_hr}">
						<figure class="service__item_content zoom__in" style="background-image:url({$photo_path})">
						</figure>
						<figcaption>
							<h5 class="service__item__title">{$title}</h5>
							<div class="service__item__details">{$detail}</div>
							<div class="service__item__btns">
								<a href="{$full_url}" class="btn btn--dark-blue btn--border-yellow">
									Shop
								</a>
							</div>
						</figcaption>
					</div>
				</div>
HTML;
$currCount ++;
}

$collections_view = <<<HTML
			<div class="container-fluid container--fluid-fw container--white service__list__wrapper">	        			
				<div class="row service__item__row">	
					{$list}
				</div>				
			</div>
HTML;

$tags_arr['mod_view'] .= $collections_view;

?>