<?php

############################################################################################################################
## Edit FAQ Item
############################################################################################################################

function edit_item()
{

    global $message,$id,$do,$disable_menu,$valid,$htmladmin, $main_subheading, $js_vars;

    $disable_menu = "true";

    $sql = "SELECT `person_name`, `person_location`, `description`,
    IF(`date_posted`, DATE_FORMAT(`date_posted`, '%d/%m/%Y'), '' )  AS posted_on,
    `cruise_id`
    FROM `review`
    WHERE `id` = '{$id}'
    AND `type` = 'P'
    LIMIT 1";

    $row = fetch_row($sql);



    @extract($row);



    $cruise_list = create_item_list("SELECT c.`id` AS ind, pmd.`name` AS label
        FROM `cruise` c
        LEFT JOIN `page_meta_data` pmd
        ON(pmd.`id` = c.`page_meta_data_id`)
        WHERE pmd.`status` != 'D'
        AND pmd.`name` != ''
        ORDER BY pmd.`name`", $cruise_id);



    ##------------------------------------------------------------------------------------------------------
    ## Page functions

    $page_functions = <<< HTML
    <ul class="page-action">
        <li><button type="button" class="btn btn-default" onclick="submitForm('save',1)"><i class="glyphicon glyphicon-floppy-save"></i> Save</button></li>
        <li><a class="btn btn-default" href="{$htmladmin}/?do={$do}"><i class="glyphicon glyphicon-arrow-left"></i> Cancel</a>
        </li>
    </ul>
HTML;


##------------------------------------------------------------------------------------------------------
## Settings tab content
$settings_content = <<< HTML
    <table width="100%" border="0" cellspacing="0" cellpadding="8">
       
        <tr>
            <td width="130"><label for="person_name">Person Name:</label></td>
            <td><input name="person_name" type="text" id="person_name" value="$person_name" style="width:300px;" /></td>
        </tr>
        <tr>
            <td width="130"><label for="person_location">Person Location:</label></td>
            <td><input name="person_location" type="text" id="person_location" value="$person_location" style="width:300px;" /></td>
        </tr>
        <tr>
            <td width="130"><label for="posted_on">Posted On:</label></td>
            <td><input name="posted_on" type="text" id="posted_on" value="$posted_on" style="width:300px;cursor:text;" /></td>
        </tr>
        <tr>
            <td width="130"><label for="cruise_id">Cruise:</label></td>
            <td>
                <select name="cruise_id" id="cruise_id" data-deselect="#motorcycle_id" style="width:300px;" class="watch-change">
                    <option value="">-- select --</option>
                    {$cruise_list}
                </select>
            </td>
        </tr>
        <tr>
            <td width="130" valign="top"><label for="description">Description:</label></td>
            <td valign="top">
                <textarea name="description" id="description" style="width:718px;min-height:200px;">$description</textarea>
            </td>
        </tr>
    </table>
    <script>
        $('.watch-change').on('change', function(){
            var self = $(this),
            target = $(self.data('deselect'));

            if( target.length )
            {
                target.find('option').prop('selected', false);
            }
        });

        $('#posted_on').attr({autocomplete:'off', readonly:true}).datepicker({
            dateFormat:'dd/mm/yy'
        });

    </script>

HTML;


##------------------------------------------------------------------------------------------------------
## tab arrays and build tabs

$temp_array_menutab = array();


$temp_array_menutab['Details']   = $settings_content;



$counter = 0;
$tablist ="";
$contentlist="";

foreach($temp_array_menutab as $key => $value){

	$tablist.= "<li><a href=\"#tabs-".$counter."\">".$key."</a></li>";
	

	$contentlist.=" <div id=\"tabs-".$counter."\">".$value."</div>";

	$counter++;
}

$tablist="<div id=\"tabs\"><ul>$tablist</ul><div style=\"padding:10px;\">$contentlist</div></div>";

    $page_contents="<form action=\"$htmladmin/?do={$do}\" method=\"post\" name=\"pageList\" enctype=\"multipart/form-data\">
        $tablist
        <input type=\"hidden\" name=\"action\" value=\"\" id=\"action\">
        <input type=\"hidden\" name=\"do\" value=\"{$do}\">
        <input type=\"hidden\" name=\"id\" value=\"$id\">
        <input type=\"hidden\" name=\"meta_data_id\" value=\"$id\">
    </form>";
require "resultPage.php";
echo $result_page;
exit();

}

?>
