<?php

############################################################################################################################
## Save Testimonial Item
############################################################################################################################

function save_item()
{

    global $message,$id,$do,$disable_menu, $root, $rootfull, $rootadmin,$tbl_name;

	$update_arr                         = array();
	
	$update_arr['first_name']           = $_POST['first_name'];
	$update_arr['last_name']            = $_POST['last_name'];
	$update_arr['position']             = $_POST['position'];
	$update_arr['email_address']        = $_POST['email_address'];
	$update_arr['mobile_phone']         = $_POST['mobile_phone'];
	$update_arr['phone_extension']      = $_POST['phone_extension'];
	$update_arr['signature_path']       = $_POST['signature_path'];
	$update_arr['photo_path']           = $_POST['photo_path'];
	$update_arr['short_descr']          = $_POST['short_descr'];
	$update_arr['long_descr']           = $_POST['long_descr'];
	$update_arr['show_contact_details'] = $_POST['show_contact'];

	// save department
	run_query("DELETE FROM `staff_has_department` WHERE `staff_id` = '{$id}'");

	$depart_id = $_POST['depart_id'];

	if(count($depart_id) > 0)
	{
		for($i=0;$i<count($depart_id);$i++)
		{
			$ins_arr = array();
			$ins_arr['staff_department_id'] = $depart_id[$i];
			$ins_arr['staff_id']            = $id;

			insert_row($ins_arr, 'staff_has_department');

		}
	}

	// save service
	run_query("DELETE FROM `staff_has_service` WHERE `staff_id` = '{$id}'");

	$service_id = $_POST['service_id'];

	if(count($service_id) > 0)
	{
		for($i=0;$i<count($service_id);$i++)
		{
			$ins_arr = array();
			$ins_arr['service_id'] = $service_id[$i];
			$ins_arr['staff_id']   = $id;

			insert_row($ins_arr, 'staff_has_service');

		}
	}

	// save product
	run_query("DELETE FROM `staff_has_product` WHERE `staff_id` = '{$id}'");

	$product_id = $_POST['product_id'];

	if(count($product_id) > 0)
	{
		for($i=0;$i<count($product_id);$i++)
		{
			$ins_arr = array();
			$ins_arr['product_id'] = $product_id[$i];
			$ins_arr['staff_id']   = $id;

			insert_row($ins_arr, 'staff_has_product');

		}
	}


    $end = "WHERE id = '$id'";
    update_row($update_arr,$tbl_name, $end);

    $message = "Staff Member has been saved";

 
}

?>
