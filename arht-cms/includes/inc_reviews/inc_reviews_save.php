<?php

############################################################################################################################
## Save Review Item
############################################################################################################################

function save_item()
{

    global $message,$id,$do,$disable_menu, $root, $rootfull, $rootadmin, $upload_dir;

    $page_data = array();


    $posted_on = sanitize_input('posted_on');

    $posted_on = ( validate_date( $posted_on, 'd/m/Y' ) ) ? DateTime::createFromFormat('d/m/Y', $posted_on) : '';

	$page_data['person_name']     = sanitize_input('person_name');
	$page_data['person_location'] = sanitize_input('person_location');
	$page_data['date_posted']     = ($posted_on) ? $posted_on->format('Y-m-d') : '';
	$page_data['cruise_id']       = sanitize_input('cruise_id', FILTER_VALIDATE_INT);
	$page_data['description']     = sanitize_input('description');
    
    update_row($page_data, 'review', "WHERE id = '{$id}' LIMIT 1");


    $message = "Item has been saved";

}

?>