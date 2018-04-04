<?php 

$news_content = '';

foreach ($news as $key => $news) {
	$news_content .= <<<H

		<div class="col-md-6">
	        <header class="section__header text-center">
	            <h2 class="section__heading section__heading--blue">Latest News</h2>
	            <div class="section__border section__border--dark"></div>
	        </header>
	        <article class="section__article">
	            <div class="row">
	                <div class="col-md-4">
	                    <div class="section__article__img-outer section__article__img-outer--round" style="background-image: url({$news['thumb_photo']})">
	                       
	                    </div>
	                </div>
	                <div class="col-md-8">
	                    <h3 class="section__article__title">{$news['heading']}</h3>
	                    <p class="section__article__text">{$news['short_description']}
	</p>                            
	                    <a href="{$news['news_url']}" class="link section__article__link">Read More</a>
	                </div>
	            </div>
	        </article>
	        <a href="{$page_news->full_url}" class="btn btn--full-width">More News</a>
	    </div>

H;
}

$tags_arr['news-home'] = $news_content;

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