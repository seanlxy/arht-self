<?php 

$stories_query = "SELECT s.`date`, s.`page_meta_data_id`, pmd.`name`, pmd.`status`, pmd.`rank`, 
 			pmd.`url`, pmd.`menu_label`, pmd.`heading`, pmd.`title`, pmd.`short_description`, pmd.`introduction`, pmd.`url`, pmd.`full_url`,
 			pmd.`thumb_photo`
            FROM `stories` s
            LEFT JOIN `page_meta_data` pmd
            ON(s.`page_meta_data_id` = pmd.`id`)
            WHERE pmd.`status` = 'A'
            ORDER BY pmd.`status`, pmd.`rank`, s.`date` DESC";

$stories_query .= ($page == $page_home->url) ? ' LIMIT 1' : '';        

$stories = fetch_all($stories_query);

if($page == $page_stories->url && !$segment1){

	require_once('inc/all_stories.php');

}elseif($page_stories && $segment1){
	require_once('inc/single_story.php');
}


 ?>