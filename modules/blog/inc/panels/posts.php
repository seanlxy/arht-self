<?php

$posts_list = fetch_all("SELECT pmd.`heading`, pmd.`full_url`, pmd.`title`
	FROM `blog_post` bp
	LEFT JOIN `page_meta_data` pmd
	ON(pmd.`id` = bp.`page_meta_data_id`)
	WHERE pmd.`status` = 'A'
	AND bp.`date_posted` != ''
	ORDER BY bp.`date_posted` DESC
	LIMIT 10");


if( !empty($posts_list) )
{
	$panels_view .= '<div class="well well-small"><h3>Recent posts</h3> <ul class="list-unstyled">';

	foreach ($posts_list as $post_item)
	{
		$panels_view .= ' <li><a href="'.$pg_full_url.$post_item['full_url'].'" title="'.$post_item['title'].'">'.$post_item['heading'].'</a></li>';
	}

	
	$panels_view .= '</ul></div>';
}

?>