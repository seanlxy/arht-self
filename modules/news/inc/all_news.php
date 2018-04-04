<?php 

$news_content = '';

foreach ($news as $key => $news) {

	$introduction = (strlen($news['introduction']) >= 160) ? substr($news['introduction'], 0, 200) : $news['introduction'];
	$news_content .= <<<H
		<div class="col-md-6 section__article--outter">
	        <article class="section__article section__article--ht">
                <div class="row">
                    <div class="col-md-4">
                        <div class="section__article__img-outer section__article__img-outer--round" style="background-image: url({$news['thumb_photo']})">
                           
                        </div>
                    </div>
                    <div class="col-md-8">
                        <h3 class="section__article__title">{$news['heading']}</h3>
                        <p class="section__article__text">{$news['short_description']}
    </p>                            
                        <a href="{$news['news_url']}" target="_blank" class="link section__article__link">Read More</a>
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
			{$news_content}
            </div>
        </div>
    </section>

H;


 ?>
