<?php

############################################################################################################################
## Save Testimonial Item
############################################################################################################################

function save_item()
{

    global $message,$id,$do,$disable_menu, $root, $rootfull, $rootadmin, $upload_dir;


    include_once("$rootadmin/classes/class_imageresizer.php");

    $resizer_class = new images();

    $upload_dir_full_path = "{$rootfull}{$upload_dir}";

    $meta_data = $page_data = array();

    //  Save  page meta  data
    $meta_data_id    = sanitize_input('meta_data_id', FILTER_SANITIZE_NUMBER_INT);

    $name    = sanitize_input('label');
    $heading = sanitize_input('heading');

    $url = (sanitize_input('url')) ? sanitize_input('url') : sanitize_input('menu_label');


    // Get main page URL
    $main_page_url = fetch_value("SELECT pmd.`full_url`
        FROM `general_importantpages` gip
        LEFT JOIN `general_pages` gp
        ON(gp.`id` = gip.`page_id`)
        LEFT JOIN `page_meta_data` pmd
        ON(pmd.`id` = gp.`page_meta_data_id`)
        WHERE gip.`imppage_id` = '5'
        LIMIT 1");

    $final_url = prepare_item_url($url);

    $meta_data['name']              = $name;
    $meta_data['menu_label']        = sanitize_input('menu_label');
    $meta_data['heading']           = $heading;
    $meta_data['url']               = $final_url;
    $meta_data['full_url']          = "{$main_page_url}/{$final_url}";
    $meta_data['photo']             = '';
    $meta_data['thumb_photo']       = '';
    $meta_data['short_description'] = sanitize_input('short_description');
    $meta_data['title']             = sanitize_input('title');
    $meta_data['meta_description']  = sanitize_input('meta_description');
    $meta_data['og_title']          = sanitize_input('og_title');
    $meta_data['og_image']          = ( is_file($og_image_full_path) ) ? $og_image_path : '';
    $meta_data['date_updated']      = date('Y-m-d H:i:s');
    $meta_data['updated_by']        = $_SESSION['s_user_id'];

    update_row($meta_data,'page_meta_data', "WHERE id = '{$meta_data_id}'");



    $page_data['latitude']          = sanitize_input('latitude');
    $page_data['longitude']         = sanitize_input('longitude');
    $page_data['formatted_address'] = sanitize_input('address');


    update_row($page_data, 'destination', "WHERE `id` = '{$id}' LIMIT 1");

    ### save page responsive content ###
    // Check if content record exist for this page


    if( !empty($_POST['row-index']) && $meta_data_id )
    {

        // get all exisitng row belong to this page's content
        $existing_rows = fetch_value("SELECT GROUP_CONCAT(`id`) FROM `content_row` WHERE `page_meta_data_id` = '$meta_data_id'");

        if($existing_rows)
        {
            // delete all columns
            run_query("DELETE FROM `content_column` WHERE `content_row_id` IN($existing_rows)");

            // delete all rows
            run_query("DELETE FROM `content_row` WHERE `id` IN($existing_rows)");
        }

        // save new content rows and columns
        $rows      = $_POST['row-index'];
        $rows_rank = $_POST['row-rank'];
        $row_count = count($rows);

        if($row_count > 0)
        {
            for ($i=0; $i < $row_count; $i++)
            { 
                $row_record = array();
                $row_record['rank']              = ($rows_rank[$i]);
                $row_record['page_meta_data_id'] = $meta_data_id;

                $row_id = insert_row($row_record, 'content_row');

                if( $row_id )
                {
                    
                    $columns_rank    = $_POST["content-{$rows[$i]}-rank"];
                    $columns_content = $_POST["content-{$rows[$i]}-text"];
                    $columns_class   = $_POST["content-{$rows[$i]}-class"];

                    $total_row_columns = count($columns_content);

                    if($total_row_columns > 0)
                    {
                        for ($k=0; $k < $total_row_columns; $k++) 
                        { 
                            $column_record                   = array();
                            
                            $column_record['content']        = $columns_content[$k];
                            $column_record['css_class']      = $columns_class[$k];
                            $column_record['rank']           = $columns_rank[$k];
                            $column_record['content_row_id'] = $row_id;

                            insert_row($column_record, 'content_column');
                        }
                    }

                }
            }
        }
            
        

    }



    //  Save gallery and gallery photos
    $slideshow_id = sanitize_input('slideshow_id', FILTER_VALIDATE_INT);

    if( $slideshow_id )
    {

        update_row( array('name' => "Destination {$name}", 'menu_label' => $name), 'photo_group', "WHERE `id` = '{$slideshow_id}' LIMIT 1" );

        
        //  Remove previous photos
        $prev_photo_thumb = fetch_value("SELECT `thumb_path` FROM `photo` WHERE `photo_group_id` = '{$slideshow_id}' AND `thumb_path` != '' AND `type` = 'P'");

        if( $prev_photo_thumb )
        {
            $prev_photo_thumb_full_path = "{$rootfull}{$prev_photo_thumb}";

            if( is_file($prev_photo_thumb_full_path) )
            {
                unlink($prev_photo_thumb_full_path);
            }
        }
        

        run_query("DELETE FROM `photo` WHERE `photo_group_id` = '{$slideshow_id}'");

        $full_path        = $_POST['photo-full-path'];
        $thumb_path       = $_POST['photo-thumb-path'];
        $rank             = $_POST['photo-rank'];
        $main_photo_index = sanitize_input('is-main', FILTER_VALIDATE_INT);


        if( !empty($full_path) )
        {
            for($i=0; $i < count($full_path); $i++)
            {

                $photo_data = array();

                $photo_path       = $full_path[$i];
                $photo_full_path  = "{$rootfull}{$photo_path}";
                $photo_thumb_path = "{$rootfull}{$thumb_path[$i]}";
                $type             = 'N';

                if( is_file($photo_full_path) )
                {

                    $photo_details = getimagesize($photo_full_path);

                    $new_thumb_path = '';

                    if( $i == ($main_photo_index - 1) )
                    {

                        $thumb_name = uniqid('img-');

                        $new_thumb_path = "{$upload_dir}/{$thumb_name}.jpg";


                        update_row(array('photo '=> $photo_path, 'thumb_photo' => $new_thumb_path), 'page_meta_data', "WHERE `id` = '{$meta_data_id}' LIMIT 1");


                        $resizer_class->resizer($upload_dir_full_path, $photo_full_path, 425, 355, $thumb_name);
                        $type = 'P';
                    }


                    $photo_data['full_path']      = $full_path[$i];
                    $photo_data['thumb_path']     = $new_thumb_path;
                    $photo_data['type']           = $type;
                    $photo_data['rank']           = sanitize_var($rank[$i], FILTER_SANITIZE_NUMBER_INT);
                    $photo_data['width']          = $photo_details[0];
                    $photo_data['height']         = $photo_details[1];
                    $photo_data['photo_group_id'] = $slideshow_id;

                    insert_row($photo_data, 'photo');

                }

            }
        }
    }
    

    $message = "Item has been saved";
}

?>