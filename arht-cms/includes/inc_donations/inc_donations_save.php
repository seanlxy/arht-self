<?php

############################################################################################################################
## Save Review Item
############################################################################################################################
function build_page_url($page_id, $reset = false)
{
    static $urls = array();
    static $count = 0;

    if($page_id)
    {
       $page_data = fetch_row("SELECT n.`page_meta_data_id`, pmd.`url`
            FROM `news` n
            LEFT JOIN `page_meta_data` pmd
            ON(n.`page_meta_data_id` = pmd.`id`)
            WHERE pmd.`id` = '{$page_id}'
            LIMIT 1");



        if($page_data)
        {
            $pg_url = (in_array($page_data['url'], array('/', 'home'))) ? '' : $page_data['url'];

            array_unshift($urls, $pg_url);

            $parent_id = $page_data['parent_id'];

            if($parent_id != 0)
            {
                build_page_url($parent_id);
            }

        }

    }

   
    $csv = implode('/', $urls);
    if($reset == true)
    {
        $urls = array();
    }
    return $csv;
}
function save_item()
{

    global $message,$id,$do,$disable_menu, $root, $rootfull, $rootadmin, $upload_dir;

    $page_data                                         = array();
    $donation_data                                     = array();
    $page_meta_data_id                                 = sanitize_input('meta_data_id', FILTER_VALIDATE_INT);
    
    $og_image_path                                     = sanitize_input('og_image');
    $og_image_full_path                                = "$rootfull{$og_image_path}";
    
    $posted_on                                         = sanitize_input('posted_on');
    
    $posted_on                                         = ( validate_date( $posted_on, 'd/m/Y' ) ) ? DateTime::createFromFormat('d/m/Y', $posted_on) : '';
    
    
    $page_data['heading']                              = sanitize_input('heading');
    $page_data['name']                                 = sanitize_input('name');
    $page_data['menu_label']                           = sanitize_input('menu_label');
    $page_data['url']                                  = prepare_item_url(sanitize_input('url'));
    $page_data['thumb_photo']                          = sanitize_input('thumb_photo');
    $page_data['introduction']                         = sanitize_input('introduction');
    $page_data['short_description']                    = sanitize_input('short_description');
    
    $page_data['title']                                = sanitize_input('title');
    $page_data['meta_description']                     = sanitize_input('meta_description');
    $page_data['og_title']                             = sanitize_input('og_title');
    $page_data['og_image']                             = ( is_file($og_image_full_path) ) ? $og_image_path : '';
    $page_data['og_meta_description']                  = sanitize_input('meta_description');
    $page_data['date_updated']                         = date('Y-m-d H:i:s');
    $page_data['updated_by']                           = $_SESSION['s_user_id'];
    $page_data['slideshow_id']                         = sanitize_input('slideshow_id', FILTER_SANITIZE_NUMBER_INT);
    $page_data['gallery_id']                           = sanitize_input('slideshow_id', FILTER_SANITIZE_NUMBER_INT);
    
    
    $news_data['date']                                 = sanitize_input('date');
    $news_data['news_url']                             = sanitize_input('news_url');
    
    
    update_row($news_data, 'news', "WHERE id           = '{$id}' LIMIT 1");
    update_row($page_data, 'page_meta_data', "WHERE id = '{$page_meta_data_id}'");

    if( $id != 1 )
    {
        $full_url = build_page_url($page_meta_data_id, true);

        if( $full_url )
        {
            update_row(array('full_url' => "/{$full_url}"), 'page_meta_data', "WHERE `id` = '{$page_meta_data_id}'");
        }
    }

    // get all exisitng row belong to this page's content
    $existing_rows = fetch_value("SELECT GROUP_CONCAT(`id`) FROM `content_row` WHERE `page_meta_data_id` = '$page_meta_data_id'");

    if($existing_rows)
    {
        // delete all columns
        run_query("DELETE FROM `content_column` WHERE `content_row_id` IN($existing_rows)");

        // delete all rows
        run_query("DELETE FROM `content_row` WHERE `id` IN($existing_rows)");
    }

    if( !empty($_POST['row-index']) && $page_meta_data_id )
    {

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
                $row_record['page_meta_data_id'] = $page_meta_data_id;

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

    $message = "Item has been saved";

}

?>