<?php


//fetch the latest blog post

$post_arr = fetch_row("SELECT pmd.`heading`, pmd.`title`, pmd.`full_url`, pmd.`description`,pmd.`thumb_photo`
			FROM `blog_post` bp
			LEFT JOIN `page_meta_data` pmd
			ON(pmd.`id` = bp.`page_meta_data_id`)
			WHERE pmd.`status` = 'A'
			ORDER BY bp.`date_posted` DESC
			LIMIT 1");

$blog_view = '';

if(!empty($post_arr))
{

	$full_url         = $page_blog->full_url.$post_arr['full_url'];
	$descr            = strip_tags($post_arr['description']);
	$long_description = substr($descr, 0, strpos($descr, '.')).'.';

	$col_class  = '';
	$thumb_view = '';

	if($post_arr['thumb_photo'])
	{
		$col_class = ' col-lg-7';
		$thumb_view = <<<H

		<div class="col-xs-12 col-lg-5">
            <img src="{$post_arr['thumb_photo']}" alt="">
        </div>
H;
	}

	$blog_view = <<<H

	<h2 class="serif">Latest Blog</h2>
    <div class="row">
        {$thumb_view}
        <div class="col-xs-12{$col_class}">
            <div class="blog-post">
                <h3 class="serif"><a href="{$full_url}">{$post_arr['heading']}</a></h3>
                <p>{$long_description}</p>
                <div><a href="{$full_url}" class="btn">Read more</a></div>
            </div>
        </div>
    </div>

H;

	$tags_arr['latest-blog'] = $blog_view;

}

?>