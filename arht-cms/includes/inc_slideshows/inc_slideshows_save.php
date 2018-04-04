<?php

############################################################################################################################
## Save slideshow
############################################################################################################################

function save_item()
{

    global $message, $id, $disable_menu, $htmladmin, $rootfull, $rootadmin, $root, $upload_dir;

    $photo_group_data = array();

    $photo_group_data['name'] = sanitize_input('label');

    update_row($photo_group_data, 'photo_group', "WHERE `id` = '{$id}'");


    run_query("DELETE FROM `photo` WHERE `photo_group_id` = '{$id}'");

    //  Save new group photos
    $full_path  = $_POST['photo-full-path'];
    $rank       = $_POST['photo-rank'];
    $slide_url  = $_POST['photo-url'];


    if( !empty($full_path) )
    {
        for($i=0; $i < count($full_path); $i++)
        {

            $photo_data = array();

            $photo_full_path  = "{$rootfull}{$full_path[$i]}";

            if( is_file($photo_full_path) )
            {

                $photo_details = getimagesize($photo_full_path);


                $photo_data['full_path']      = $full_path[$i];
                $photo_data['rank']           = sanitize_var($rank[$i], FILTER_SANITIZE_NUMBER_INT);
                $photo_data['url']            = sanitize_var($slide_url[$i], FILTER_VALIDATE_URL);
                $photo_data['width']          = $photo_details[0];
                $photo_data['height']         = $photo_details[1];
                $photo_data['photo_group_id'] = $id;

                insert_row($photo_data, 'photo');
            }

        }
    }

    $message = "Item has been saved";

}


?>