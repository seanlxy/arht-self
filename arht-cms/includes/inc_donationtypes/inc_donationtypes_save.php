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
       $page_data = fetch_row("SELECT dt.`page_meta_data_id`, pmd.`url`
            FROM `donation_type` dt
            LEFT JOIN `page_meta_data` pmd
            ON(dt.`page_meta_data_id` = pmd.`id`)
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

    $page_data = array();
    $donation_data = array();
    $page_meta_data_id = sanitize_input('meta_data_id', FILTER_VALIDATE_INT);

    $og_image_path      = sanitize_input('og_image');
    $og_image_full_path = "$rootfull{$og_image_path}";

    $posted_on = sanitize_input('posted_on');

    $posted_on = ( validate_date( $posted_on, 'd/m/Y' ) ) ? DateTime::createFromFormat('d/m/Y', $posted_on) : '';

	$page_data['heading']          = sanitize_input('heading');
	$page_data['name']             = sanitize_input('name');
    $page_data['menu_label']       = sanitize_input('menu_label');
    $page_data['url']              = prepare_item_url(sanitize_input('url'));
    $page_data['introduction']     = sanitize_input('introduction');

    $page_data['title']                = sanitize_input('title');
    $page_data['meta_description']     = sanitize_input('meta_description');
    $page_data['og_title']             = sanitize_input('og_title');
    $page_data['og_image']             = ( is_file($og_image_full_path) ) ? $og_image_path : '';
    $page_data['og_meta_description']  = sanitize_input('meta_description');
    $page_data['date_updated']         = date('Y-m-d H:i:s');
    $page_data['updated_by']           = $_SESSION['s_user_id'];


    $donation_data['is_promoting']      = ((sanitize_input('is_promoting') === 'Y') ? 'Y' : 'N');
    $donation_data['icon_cls']          = sanitize_input('icon_cls');
    $donation_data['subject']           = sanitize_input('subject');
    $donation_data['from_name']         = sanitize_input('from_name');
    $donation_data['from_email_address']= sanitize_input('from_email_address',FILTER_VALIDATE_EMAIL);
    $donation_data['email_template']    = $_POST['email_template'];
    
    update_row($donation_data, 'donation_type', "WHERE id = '{$id}' LIMIT 1");
    update_row($page_data, 'page_meta_data', "WHERE id = '{$page_meta_data_id}' LIMIT 1");

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