<?php

############################################################################################################################
## New FAQ Item
############################################################################################################################

function new_item()
{
	global $message,$id,$pages_maximum, $do, $htmladmin;

	$now = date("Y-m-d H:i:s");

	$name = 'Untitled Slideshow';

    $slideshow_data['name']        = $name;
    $slideshow_data['type']        = 'S';
    $slideshow_data['show_in_cms'] = 'N';
    

    $slideshow_id = insert_row($slideshow_data, 'photo_group');


	$temp_array_new['name']               = 'Untitled';
	$temp_array_new['url']                = prepare_item_url($now);
	$temp_array_new['date_created']       = $now;
	$temp_array_new['created_by']         = $_SESSION['s_user_id'];
	$temp_array_new['status']             = 'H';
	$temp_array_new['slideshow_id']       = $slideshow_id;
	$temp_array_new['page_meta_index_id'] = 1;

    $meta_id = insert_row($temp_array_new,'page_meta_data');

    $public_token = md5( sha1( create_rand_chars() ) );

    $id = insert_row( array( 'page_meta_data_id' => $meta_id, 'public_token' => $public_token), 'destination' );


    $message = "New item has been added and ready to edit";

    if( $id )
    {
    	header("Location: {$htmladmin}?do={$do}&id={$id}&action=edit");
    	exit();
    }

        
}

?>
