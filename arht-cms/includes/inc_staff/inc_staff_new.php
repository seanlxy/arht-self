<?php

############################################################################################################################
## New FAQ Item
############################################################################################################################

function new_item()
{
	global $message,$id,$htmladmin, $do,$tbl_name;

	$temp_array_new['first_name']     = 'Untitled';
	$temp_array_new['status']         = 'H';
	
	$id      = insert_row($temp_array_new,$tbl_name);
	$message = "New item has been added and ready to edit";
        
    @include('inc_'.$do.'_edit.php');
	edit_item();
}

?>
