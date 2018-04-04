<?php 

$event_query = "SELECT pmd.`name`, pmd.`status`, pmd.`rank`, e.`id`, e.`event_start_date`, e.`event_end_date`, e.`event_region`, e.`page_meta_data_id`,
 			pmd.`url`, pmd.`menu_label`, pmd.`heading`, pmd.`title`, pmd.`short_description`, pmd.`introduction`, pmd.`url`, pmd.`full_url`,
 			pmd.`thumb_photo`
            FROM `events` e
            LEFT JOIN `page_meta_data` pmd
            ON(e.`page_meta_data_id` = pmd.`id`)
            WHERE pmd.`status` = 'A'
            AND e.`event_start_date` <= e.`event_end_date`
            AND e.`event_end_date` >= CURDATE()
            ORDER BY pmd.`status`, pmd.`rank`, e.`event_start_date`";

$event_query .= ($page == $page_home->url) ? 'LIMIT 3' : '';        

$events = fetch_all($event_query);


if ($page == $page_home->url) {

	require_once('inc/events_home.php');

}elseif($page_events && !$segment1){

	require_once('inc/all_events.php');

}elseif($page_events && $segment1){

	$event_query .= 'LIMIT 3';        
	require_once('inc/single_event.php');

}

 ?>