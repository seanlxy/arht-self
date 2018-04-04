<?php 

$story_content = '';

foreach ($stories as $key => $story) {

	$introduction = (strlen($story['introduction']) >= 150) ? substr($story['introduction'], 0, 150) : $story['introduction'];
	$story_content .= <<<H
		<div class="col-md-6 section__article--outter">
	        <article class="section__article section__article--stories">
	            <div class="row">
	                <div class="col-md-4">
	                    <div class="section__article__img-outer section__article__img-outer--round" style="background-image: url({$story['thumb_photo']})">
	                    </div>
	                </div>
	                <div class="col-md-8">
	                    <h3 class="section__article__title">“{$story['heading']}”</h3>
	                    <p class="section__article__text">{$introduction}</p>                            
	                    <a href="{$page_stories->full_url}{$story['full_url']}" class="link link--red section__article__link">Read More</a>
	                </div>
	            </div>
	        </article>
	    </div>
H;
}

$tags_arr['mod_view'] = <<<H

	<section class="section section--white-bg">
        <div class="container">
            <div class="row">
			{$story_content}
            </div>
        </div>
    </section>

H;


 ?>
