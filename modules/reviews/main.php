<?php

$reviews_view = '';


if( $page_reviews->url === $page )
{
    require_once 'inc/list.php';
}
else
{
    require_once 'inc/single.php';
}



$tags_arr['reviews_view'] = $reviews_view;

?>