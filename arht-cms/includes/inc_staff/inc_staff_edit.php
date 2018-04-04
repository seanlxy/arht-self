<?php

############################################################################################################################
## Edit FAQ Item
############################################################################################################################

function edit_item()
{

    global $message,$id,$do,$disable_menu,$valid,$htmladmin, $main_subheading,$tbl_name;

    $disable_menu = "true";

    $sql = "SELECT * FROM $tbl_name WHERE `id` = '$id' LIMIT 1";

    $row = fetch_row($sql);
    @extract($row);

    $main_subheading = 'Editing: '.$first_name.' '.$last_name;

    $check = ($show_contact_details == '1') ? 'checked' : '';

    ##------------------------------------------------------------------------------------------------------
    ## Page functions

    $page_functions = <<< HTML

        <ul class="page-action">
            <li><button type="button" class="btn btn-default" onclick="submitForm('save',1)"><i class="glyphicon glyphicon-floppy-save"></i> Save</button></li>
            <li><button type="button" class="btn btn-default" onclick="submitForm('cancelpagesave',1)"><i class="glyphicon glyphicon-arrow-left"></i> Cancel</button>
            </li>
        </ul>
HTML;

##------------------------------------------------------------------------------------------------------
    ## Staff Departments

    $sql = "SELECT `id`,`name` FROM `staff_department` WHERE `status` = 'A' ORDER BY `rank`";
    $depart_arr = fetch_all($sql);

    $attached_ids     = fetch_value("SELECT GROUP_CONCAT(`staff_department_id`) FROM `staff_has_department` WHERE `staff_id` = '$id'");
    $attached_ids_arr = explode(',', $attached_ids);

    $depart_list = '';

    if(!empty($depart_arr)){

        $depart_list = '<ul>';

        foreach ($depart_arr as $depart) {
            
            $checked = (in_array($depart['id'], $attached_ids_arr)) ? 'checked' : '';
            $depart_list .= '<li><label><input type="checkbox" '.$checked.' name="depart_id[]" value="'.$depart['id'].'"><span style="vertical-align:top;margin:4px 0 0 10px;display:inline-block;">'.$depart['name'].'</span></label></li>';
        }

        $depart_list .= '</ul>';
    }

##------------------------------------------------------------------------------------------------------
    ## Services

    $sql2 = "SELECT s.`id`,pmd.`name`
    FROM `service` s
    LEFT JOIN `page_meta_data` pmd
    ON (pmd.`id` = s.`page_meta_data_id`)
    WHERE pmd.`status` != 'D'
    ORDER BY pmd.`rank`";

    $serv_arr = fetch_all($sql2);

    $attached_ids2     = fetch_value("SELECT GROUP_CONCAT(`service_id`) FROM `staff_has_service` WHERE `staff_id` = '$id'");
    $attached_ids_arr2 = explode(',', $attached_ids2);

    $serv_list = '';

    if(!empty($serv_arr)){

        $serv_list = '<ul>';

        foreach ($serv_arr as $service) {
            
            $checked = (in_array($service['id'], $attached_ids_arr2)) ? 'checked' : '';
            $serv_list .= '<li><label><input type="checkbox" '.$checked.' name="service_id[]" value="'.$service['id'].'"><span style="vertical-align:top;margin:4px 0 0 10px;display:inline-block;">'.$service['name'].'</span></label></li>';
        }

        $serv_list .= '</ul>';
    }

##------------------------------------------------------------------------------------------------------
    ## Products

    $sql2 = "SELECT p.`id`,pmd.`name`
    FROM `product` p
    LEFT JOIN `page_meta_data` pmd
    ON (pmd.`id` = p.`page_meta_data_id`)
    WHERE pmd.`status` != 'D'
    ORDER BY pmd.`rank`";

    $prod_arr = fetch_all($sql2);

    $attached_ids3     = fetch_value("SELECT GROUP_CONCAT(`product_id`) FROM `staff_has_product` WHERE `staff_id` = '$id'");
    $attached_ids_arr3 = explode(',', $attached_ids3);

    $prod_list = '';

    if(!empty($prod_arr)){

        $prod_list = '<ul>';

        foreach ($prod_arr as $prod) {
            
            $checked = (in_array($prod['id'], $attached_ids_arr3)) ? 'checked' : '';
            $prod_list .= '<li><label><input type="checkbox" '.$checked.' name="product_id[]" value="'.$prod['id'].'"><span style="vertical-align:top;margin:4px 0 0 10px;display:inline-block;">'.$prod['name'].'</span></label></li>';
        }

        $prod_list .= '</ul>';
    }

    
       ##------------------------------------------------------------------------------------------------------
       ## Settings tab content

    $settings_content = <<< HTML

        <table width="100%" border="0" cellspacing="0" cellpadding="8">
            <tr>
                <td width="150"><b>First Name:</b></td>
                <td>
                    <input name="first_name" type="text" id="first_name" value="$first_name" style="width:300px;height:30px;" />
                </td>
                <td width="150"><b>Last Name:</b></td>
                <td>
                    <input name="last_name" type="text" id="last_name" value="$last_name" style="width:300px;height:30px;" />
                </td>
            </tr>
            <tr>
                <td width="150"><b>Position:</b></td>
                <td>
                    <input name="position" type="text" id="position" value="$position" style="width:300px;height:30px;" />
                </td>
                <td width="150"><b>Email Address:</b></td>
                <td>
                    <input name="email_address" type="text" id="email_address" value="$email_address" style="width:300px;height:30px;" />
                </td>
            </tr>
            <tr>
                <td width="150"><b>Mobile Phone:</b></td>
                <td>
                    <input name="mobile_phone" type="text" id="mobile_phone" value="$mobile_phone" style="width:300px;height:30px;" />
                </td>
                <td width="150"><b>Office Phone Extension:</b></td>
                <td>
                    <input name="phone_extension" type="text" id="phone_extension" value="$phone_extension" style="width:300px;height:30px;" />
                </td>
            </tr>
            <tr>
                <td width="150"><b>Show contact details:</b></td>
                <td>
                    <input name="show_contact" {$check} type="checkbox" id="show_contact" value="1" />
                </td>
            </tr>
            <tr>
                <td valign="top"><b>Short Description: <br><small>(max 250 char.)</small></b></td>
                <td valign="top">
                    <textarea name="short_descr" maxlength="250" id="short_descr" style="width:300px;height:120px;">$short_descr</textarea>
                </td>
                <td valign="top"><b>Photo:</td>
                <td colspan="3" valign="top">
                    <input name="photo_path" type="text" id="photo_path" value="$photo_path" style="margin-right:5px;width:300px;height:30px;float:left;" /><br>
                    <input type="button" onclick="openFileBrowser('photo_path')" style="height:30px;padding:1px 5px;" value="Browse">
                    <input type="button" value="clear" onclick="clearValue('photo_path')" style="height:30px;"><br>
                    <div><small>suggested size:H300px X W300px - JPG format</small></div>
                </td>
            </tr>
            
            <!--<tr>
                <td valign="top"><b>Signature</td>
                <td colspan="3">
                    <input name="signature_path" type="text" id="signature" value="$signature_path" style="margin-right:5px;width:300px;height:30px;float:left;" />
                    <input type="button" onclick="openFileBrowser('signature_path')" style="height:30px;padding:1px 5px;" value="Browse">
                    <input type="button" value="clear" onclick="clearValue('signature_path')" style="height:30px;"><br>
                    <div><small>suggested size:H150px X W300px - PNG format</small></div>
                </td>
            </tr>-->
        </table>
HTML;

$cat_content = <<< HTML

        <table width="100%" border="0" cellspacing="0" cellpadding="8">
            <tr>
                <td valign="top" width="150"><b>Staff Department(s):</b></td>
                <td valign="top">
                    $depart_list
                </td>
            </tr>
        </table>
HTML;

$long_content = <<< HTML

        <table width="100%" border="0" cellspacing="0" cellpadding="8">
            <tr>
                <td valign="top"><label for="long_descr">Long Description:</label> </td>
                <td colspan="3">
                    <textarea name="long_descr" style="width:770px;min-height:120px;">$long_descr</textarea>
                    <script type="text/javascript">
                        CKEDITOR.replace( 'long_descr',
                        {
                                toolbar : 'MyToolbar',
                                forcePasteAsPlainText : true,
                                resize_enabled : false,
                                width:770,
                                height : 600,
                                filebrowserBrowseUrl : jsVars.dataManagerUrl
                        });                   
                    </script>
                </td>
            </tr>
        </table>
HTML;

       ##------------------------------------------------------------------------------------------------------
       ## tab arrays and build tabs

    $temp_array_menutab                      = array();
    $temp_array_menutab ['Details']          = $settings_content;
    $temp_array_menutab ['Categories']       = $cat_content;
    $temp_array_menutab ['Long Description'] = $long_content;

    $counter     = 0;
    $tablist     = "";
    $contentlist = "";

    foreach($temp_array_menutab as $key => $value){

        $tablist.= "<li><a href=\"#tabs-".$counter."\">".$key."</a></li>";
        $contentlist.=" <div id=\"tabs-".$counter."\">".$value."</div>";
        $counter++;
    }

    $tablist="<div id=\"tabs\"><ul>$tablist</ul><div style=\"padding:10px;\">$contentlist</div></div>";

    $page_contents="<form action=\"$htmladmin/index.php\" method=\"post\" name=\"pageList\" enctype=\"multipart/form-data\">
                            $tablist
                            <input type=\"hidden\" name=\"action\" value=\"\" id=\"action\">
                            <input type=\"hidden\" name=\"do\" value=\"$do\">
                            <input type=\"hidden\" name=\"id\" value=\"$id\">
                    </form>";
    require "resultPage.php";
    echo $result_page;
    exit();
}

?>
