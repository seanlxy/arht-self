<?php 

$story_content = '';

foreach ($stories as $key => $story) {

	$introduction = (strlen($story['introduction']) >= 160) ? substr($story['introduction'], 0, 200) : $story['introduction'];
	$story_content .= <<<H
		<div class="col-md-6">
	        <header class="section__header text-center">
	            <h2 class="section__heading section__heading--red">Latest Personal Stories</h2>
	            <div class="section__border section__border--dark"></div>
	        </header>
	        <article class="section__article">
	            <div class="row">
	                <div class="col-md-4">
	                    <div class="section__article__img-outer section__article__img-outer--round" style="background-image: url({$story['thumb_photo']})">
	                    </div>
	                </div>
	                <div class="col-md-8">
	                    <h3 class="section__article__title">“{$story['heading']}”</h3>
	                    <p class="section__article__text">{$story['introduction']}</p>                            
	                    <a href="{$page_stories->full_url}{$story['full_url']}" class="link link--red section__article__link">Read More</a>
	                </div>
	            </div>
	        </article>
	        <a href="{$page_stories->full_url}" class="btn btn--red btn--full-width">More Personal Stories</a>
	    </div>
H;
}

$tags_arr['stories-home'] = $story_content;

$tags_arr['mod_view'] .= <<<H

	<section class="section section--white-bg">
        <div class="container">
            <div class="row">
               
               {$tags_arr['stories-home']}

               {$tags_arr['news-home']}
            </div>
        </div>
    </section>

H;


 ?>