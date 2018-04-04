<?php 

$single_story_query = fetch_row("SELECT s.`date`, s.`page_meta_data_id`, pmd.`name`, pmd.`status`, pmd.`rank`, 
 			pmd.`url`, pmd.`menu_label`, pmd.`heading`, pmd.`title`, pmd.`short_description`, pmd.`introduction`, pmd.`url`, pmd.`full_url`,
 			pmd.`thumb_photo`, pmd.`id`
            FROM `stories` s
            LEFT JOIN `page_meta_data` pmd
            ON(s.`page_meta_data_id` = pmd.`id`)
            WHERE pmd.`status` = 'A'
            AND pmd.`url` = '{$segment1}'
            ORDER BY pmd.`status`, pmd.`rank`");

if (!empty($single_story_query)) {

	$tags_arr['heading-view'] = '';
	$tags_arr['introduction-view'] = '';
	$tags_arr['content'] = '';
	$tags_arr['mod_view'] = '';


	$tags_arr['heading-view'] = <<<H
		<header class="section__header text-center">
			<div class="section__header__img" style="background-image: url({$single_story_query['thumb_photo']})"></div>
			<div class="section__header__highlight section__header__highlight--red"></div>			
			<h1 class="section__heading section__heading--normal section__heading--red">"{$single_story_query['heading']}"</h1>
		</header>
H;

$tags_arr['introduction-view'] = <<<H
		<p class="text-center">
			{$single_story_query['introduction']}
		</p>
H;
}

$content = get_content($single_story_query['id']);

if (!empty($content)) {
	$tags_arr['content'] = $content;
}



 ?>