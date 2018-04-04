<?php 

if ($page == 'news') 
{
	$tags_arr['body_cls']         = ' news-pg';
	
}


$news_query = "SELECT n.`date`, n.`news_url`, n.`page_meta_data_id`, pmd.`name`, pmd.`status`, pmd.`rank`, 
 			pmd.`url`, pmd.`menu_label`, pmd.`heading`, pmd.`title`, pmd.`short_description`, pmd.`introduction`, pmd.`url`, pmd.`full_url`,
 			pmd.`thumb_photo`
            FROM `news` n
            LEFT JOIN `page_meta_data` pmd
            ON(n.`page_meta_data_id` = pmd.`id`)
            WHERE pmd.`status` = 'A'
            ORDER BY pmd.`status`, pmd.`rank`, n.`date` DESC";

$news = fetch_all($news_query);

if($page == $page_news->url && !$segment1){

	require_once('inc/all_news.php');

}

 ?>