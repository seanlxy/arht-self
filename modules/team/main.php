<?php

$team_view = '';
$nav_view  = '';
$modal     = '';

// if($page_id == $page_team->id){

    $depart_ids_csv  = fetch_value("SELECT GROUP_CONCAT(DISTINCT(staff_department_id)) FROM `staff_has_department`");

    //Generate the filter nav
    $sql = "SELECT sd.`id`, MD5(sd.`id`) AS hash,sd.`name` 
    FROM `staff_department` sd
    WHERE sd.`status` = 'A' 
    AND sd.`id` IN ({$depart_ids_csv})
    ORDER BY `rank`";
    $depart_arr = fetch_all($sql);

    if(!empty($depart_arr)){

        $nav = '<ul class="filter-nav">';
        $nav .= '<li><a class="active btn btn--col btn--white btn--border-yellow" href="" data-group="all">All Members</li></li>';

        foreach ($depart_arr as $row) {

            $nav .= '<li><a href="" data-group="'.$row['hash'].'" class=" btn btn--col btn--white btn--border-yellow" >'.$row['name'].'</a></li></li>';
        }

        $nav .= '</ul>';

        $nav_view = <<<H

            <div class="container">
                <div class="row">
                    <div class="col-xs-12 text-center btn-group">
                        {$nav}
                    </div>
                </div>
            </div>
H;
    }

    //Generate staff
    $sql2 = "SELECT * FROM `staff` WHERE `status` = 'A' ORDER BY `rank`";
    $team_arr = fetch_all($sql2);

    if(!empty($team_arr)){

        $team        = '';
        $modal_items = '';
        $index       = 0;

        foreach ($team_arr as $row) {
           
            @extract($row);

            $key = MD5($id);
            $index++;

            $depart_ids  = fetch_value("SELECT GROUP_CONCAT(MD5(staff_department_id)) FROM `staff_has_department` WHERE `staff_id` = '$id'");
            $depart_ids_arr = explode(',', $depart_ids);

            $json = json_encode($depart_ids_arr);

            $clear = ($index % 2 == 0) ? '<div class="clearfix"></div>' : '';

            $contact_mob   = '';
            $contact_dsk   = '';
            $contact_modal = '';
            $blog_link     = '';

            if($show_contact_details == 1){

                $phone_view = ($mobile_phone) ? '<a href="tel:'.$mobile_phone.'"><i class="fa fa-phone"></i></a>' : '';
                $email_view = ($email_address) ? '<a href="mailto:'.$email_address.'"><i class="fa fa-envelope"></i></a>' : '';

                $contact_mob = '<div class="visible-xs contact-link">'.$phone_view.$email_view.'</div>';

                $contact_modal = '<h4 class="hl">Get in <span>touch</span></h4>';
                $contact_modal .= ($email_address) ? '<p><i class="fa fa-envelope-o"></i> <a href="mailto:'.$email_address.'">'.$email_address.'</a></p>' : '';
                $contact_modal .= ($mobile_phone) ? '<p><i class="fa fa-mobile"></i> '.$mobile_phone.'</p>' : '';
                $contact_modal .= ($phone_extension && $phone_number) ? '<p class="hl"><i class="fa fa-phone"></i> '.$phone_number.' <span>&nbsp;&nbsp;ext.</span> '.$phone_extension.'</p>' : '';
            }

            $team .= <<<H

                <div class="col-xs-12 col-sm-6 team" data-groups='{$json}'>
                    <div class="row">
                        <div class="col-xs-12 col-md-6 col-lg-5">
                            <div class="img">
                                <a href="" data-team="{$key}" style="background-image:url('{$photo_path}');" class="team-heroshot"></a>
                            </div>
                        </div>
                        <div class="col-xs-12 col-md-6 col-lg-7">
                            <h3>{$first_name} {$last_name}</h3>
                            <p class="sub-title">{$position}</p>
                            <p class="bio">{$short_descr}</p>
                            {$contact_mob}
                            <p class="hidden-xs"><a href="" data-team="{$key}" class="btn">find out more</a></p>
                        </div>
                    </div>
                </div>
                {$clear}

H;
            
            $modal_items .= <<<H

                <div class="team-member" data-team="{$key}">
                    <div class="row">
                        <div class="col-sm-12 col-lg-4">
                            <div class="row">
                                <div class="col-sm-5 col-md-4 col-lg-12">
                                    <span class="img" style="background-image:url('{$photo_path}');"></span>
                                </div>
                                <div class="col-sm-7 col-md-8 col-lg-12">
                                    <div class="details">
                                        <h3>{$first_name} {$last_name}</h3>
                                        <p>{$position}</p>
                                        {$contact_modal}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12 col-lg-8">
                            <div class="long_descr">{$long_descr}</div>
                        </div>
                    </div>
                </div>

H;

        }
    }

     $team_view = <<<H
        
        <div class="team-wrap">
            <div class="container">
                <div class="row">
                    <div id="shuffle">
                        {$team}
                    </div>
                </div>
            </div>
        </div>
            
H;

require_once 'modal.php';

// }

$tags_arr['team-view'] = $nav_view;
$tags_arr['team-view'] .= $team_view;
$tags_arr['modal'] = $modal;

?>