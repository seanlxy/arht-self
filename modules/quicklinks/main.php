<?php

###############################################################################################################################
## Make the Quicklinks
###############################################################################################################################


$quicklinks_view = '';


$quicklinks = fetch_all("SELECT IF((pmd.`quicklink_heading` != ''), pmd.`quicklink_heading`, pmd.`menu_label`) AS label, pmd.`thumb_photo`, pmd.`title`,
    pmd.`short_description`, pmd.`full_url`
    FROM `general_pages` gp
    LEFT JOIN `page_meta_data` pmd
    ON(pmd.`id` = gp.`page_meta_data_id`)
    LEFT JOIN `page_has_quicklink` phq
    ON(phq.`quicklink_page_id` = gp.`id`)
    WHERE pmd.`status` = 'A'
    AND phq.`page_id` = '{$page_id}'
    AND pmd.`thumb_photo` != ''
    ORDER BY phq.`rank`");


if( !empty($quicklinks) )
{

    foreach ($quicklinks as $quicklink)
    {

        $label             = $quicklink['label'];
        $title             = $quicklink['title'];
        $thumb_photo       = $quicklink['thumb_photo'];
        $full_url          = $quicklink['full_url'];
        $short_description = $quicklink['short_description'];

        $quicklinks_view .= '<div class="col-xs-12 col-sm-6 col-md-4 clm">
            <figure style="background-image:url('.$thumb_photo.')"></figure>
            <h3><a href="'.$full_url.'" title="'.$title.'">'.$label.'</a></h3>
            <p class="descr">'.$short_description.'</p>
            <div class="foot">
                <a href="'.$full_url.'" title="'.$title.'" class="btn">Find Out More</a>
            </div>
        </div>';
    }

    $quicklinks_view = '<div class="grid ctr">
        <div class="container">
            <div class="row">
                <header class="col-xs-12">
                    <p class="h1 sm"><span>You May Want to Know</span></p>
                </header>
                '.$quicklinks_view.'
            </div>
        </div>
    </div><!-- /.grid.ctr -->';

}

$tags_arr['mod_view'] .= $quicklinks_view;
        
?>