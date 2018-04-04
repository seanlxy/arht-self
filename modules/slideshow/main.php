<?php

$slideshow_view = '';
$body_cls     = '';



if( $slideshow_id )
{

    if( $main_page_id == $page_home->id )
    {
        require_once "views/slider.php";
       
    }
    else
    {
        require_once "views/gallery.php";
    }
// require_once "views/slider.php";
}



$tags_arr['body_cls'] .= " {$body_cls}";

$tags_arr['slideshow_view'] = $slideshow_view;



if (empty($slideshow_view)) 
{
    $tags_arr['body_cls'] .= ' no-ss';
}


?>