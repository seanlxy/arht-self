<?php

$body_cls = ' home';

$slideshow_photos = fetch_all("SELECT `full_path`, `url`, `width`, `height`
    FROM `photo`
    WHERE `photo_group_id` = '{$slideshow_id}'
    AND `full_path` != ''
    ORDER BY `rank`");


$slideshow_photos_count = count($slideshow_photos);

$slideshow_view = '<div class="main-slider__slick">';

foreach ($slideshow_photos as $i => $slideshow_photo)
{

    $photo_path = $slideshow_photo['full_path'];
    $photo_url  = $slideshow_photo['url'];

    $slide_item = '<img src="'.$photo_path.'" >';

    $target_url_root = parse_url($photo_url, PHP_URL_HOST);
    $site_url_root   = parse_url($htmlrootfull, PHP_URL_HOST);

    $url_target = (!empty( $photo_url ) && $target_url_root === $site_url_root) ? '_self' : '_blank';

    $slideshow_view .= '
    <div class="main-slider__slick__slide">
        '.((!empty( $photo_url )) ? '<a href="'.$photo_url.'" target="'.$url_target.'">'.$slide_item.'</a>' : $slide_item ).'        
    </div>';

}

$slideshow_view .= '</div>';

$scroll_btn = '';

if ($page_home) {
    $scroll_btn .= <<<H
    <div class="container">
        <div class="row">
            <div class="col-md-12 text-center">
                <a href="#" class="link link--red text-center main-slider__link scroll-trigger">Scroll Down <i class="fa fa-angle-double-down main-slider__link__icon"></i></a> 
            </div>
        </div>
    </div>
H;
}else{
    $scroll_btn = '';
}

$slideshow_view .= $scroll_btn;

?>