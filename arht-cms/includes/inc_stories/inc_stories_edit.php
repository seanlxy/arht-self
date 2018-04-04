<?php

############################################################################################################################
## Edit FAQ Item
############################################################################################################################

function edit_item()
{

    global $message,$id,$do,$disable_menu,$valid,$htmladmin, $main_subheading, $js_vars;

    $disable_menu = "true";

    $sql = "SELECT s.`id`, s.`date`, s.`page_meta_data_id`,
            pmd.`name`, pmd.`url`, pmd.`menu_label`, pmd.`heading`, pmd.`title`, pmd.`short_description`, pmd.`introduction`,
            pmd.`og_title`, pmd.`meta_description`, pmd.`og_image`, pmd.`thumb_photo`, pmd.`slideshow_id`, pmd.`gallery_id`
            FROM `stories` s
            LEFT JOIN `page_meta_data` pmd
            ON(s.`page_meta_data_id` = pmd.`id`)
            WHERE s.`id` = '{$id}'
            LIMIT 1";

    $row = fetch_row($sql);

    extract($row);

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
 


$details_content = <<< HTML
    <table width="100%" border="0" cellspacing="0" cellpadding="8">
       
        <tr>
            <td width="130"><label for="heading">Heading:</label></td>
            <td colspan="3"><input name="heading" type="text" id="heading" value="$heading" style="width:814px;" /></td>
        </tr>
        <tr>
            <td width="130"><label for="name">Story Name:</label></td>
            <td><input name="name" type="text" id="name" value="$name" style="width:300px;" /></td>

            <td width="130"><label for="menu_label">Menu Label:</label></td>
            <td><input name="menu_label" type="text" id="menu_label" value="$menu_label" style="width:300px;" /></td>
        </tr>
        <tr>
            <td width="130"><label for="url">URL:</label></td>
            <td><input name="url" type="text" id="url" value="$url" style="width:300px;" /></td> 

            <td width="130"><label for="date">Date:</label></td>
            <td><input name="date" type="text" id="date" value="$date" style="width:300px;" /></td>   

        </tr>

        <tr>
            <td width="150" valign="top"><label for="thumb_photo">Please select a photo:</label></td>
            <td>
                <input name="thumb_photo" type="text" value="$thumb_photo" style="width:300px;" id="thumb_photo" readonly autocomplete="off">
                <input type="button" value="browse" onclick="openFileBrowser('thumb_photo')"> 
                <input type="button" value="clear" onclick="clearValue('thumb_photo')"><br>
            </td>
        </tr>

        <tr>
            <td width="130" valign="top"><label for="introduction">Introduction:</label></td>
            <td colspan="3">
                <textarea name="introduction" type="text" id="introduction" style="width:814px; height: 250px;">$introduction</textarea>
            </td>
        </tr>
    </table>

     <script>
        $('#date').attr({autocomplete:'off', readonly:true}).datepicker({
            dateFormat:'yy-mm-dd'
        });


    </script>
    
HTML;




##------------------------------------------------------------------------------------------------------
## Content Tab
$max_coulmns_dd = generate_num_dd(1, MAX_COLUMNS);


$content_rows = fetch_all("SELECT `id`, `rank`
        FROM `content_row`
        WHERE `page_meta_data_id` = '{$page_meta_data_id}'
        ORDER BY `rank`");

    $content_view = '';

    if( !empty($content_rows) ) 
    {
        foreach ($content_rows as $inx => $content_row)
        {

$content_view .= <<< H

        <div class="row sortable-item clear" id="row-{$inx}">
H;

            $rank = $inx+1;

            $row_columns = fetch_all("SELECT `content`, `css_class`, `rank` FROM `content_column` WHERE `content_row_id` = '{$content_row['id']}' ORDER BY `rank`");

            foreach ($row_columns as $cindx => $row_column)
            {
                $content_view .= <<< H

                <div class="{$row_column['css_class']} res-col sortable-item" id="col-{$inx}-{$cindx}">
                    <ul class="action">
                        <li><input type="checkbox" class="col-merge" value="1"><li/>
                        <li><a href="#" title="drag to change the rank" class="move-col"><i class="glyphicon glyphicon-move"></i></a><li/>
                        <li><a href="#" data-to-remove=".res-col" title="click to remove section"  class="remove-col"><i class="glyphicon glyphicon-remove"></i></a><li/>
                    </ul>
                    <div class="editable-column-content" title="Click to edit this content section.">
                        <textarea id="content-{$inx}-{$cindx}" name="content-{$inx}-text[]">{$row_column['content']}</textarea>
                    </div>
                    <input type="hidden" value="{$row_column['rank']}" class="col-rank" name="content-{$inx}-rank[]">
                    <input type="hidden" value="{$row_column['css_class']}" name="content-{$inx}-class[]" class="col-cls">
                </div>

H;
            }

           $content_view .= <<< H
            <input type="hidden" value="{$inx}" name="row-index[]">
            <input type="hidden" value="{$content_row['rank']}" name="row-rank[]" class="row-rank">
            <div class="clear"></div>
            <ul class="roww action">
                <li><a href="#" title="add new column to this row" class="add-col"><i class="glyphicon glyphicon-plus-sign"></i></a><li/>
                <li><a href="#" title="drag to change the rank" class="move-col"><i class="glyphicon glyphicon-move"></i></a><li/>
                <li><a href="#" title="click to remove row" data-to-remove=".row"  class="remove-col"><i class="glyphicon glyphicon-remove"></i></a><li/>
            </ul>
        </div>

H;
        }
    }

$main_content = <<< HTML
        <p>Add new row with &nbsp;<select name="column-num" id="column-num" class="column-num">
            {$max_coulmns_dd}
        </select> &nbsp;columns. <button type="button" class="add-row">Go</button></p>


        <div id="grid-holder" class="grid-holder">   
            
            {$content_view}
        </div>
HTML;


##-----------------------------------------------------------------------------------------------------
## meta content tab

$meta_content = <<< HTML
   <table width="100%" border="0" cellspacing="0" cellpadding="6" >
        <tr>
            <td width="150" valign="top"><label for="title">Title:</label></td>
            <td>
                <input name="title" type="text" id="title" class="check-max" value="$title" style="width:600px;"><br>
                <span class="text-muted"><small>Page titles should be under 65 characters (including spaces) <em></em></small></span>
            </td>
        </tr>
        <tr>
            <td valign="top"><label for="meta_description">Meta Description:</label> <span class="tooltip" title="This description is hidden from the user but useful to some search engines and appears in search results"></span></td>
            <td>
                <textarea name="meta_description" class="check-max" style="width:600px; font-family: sans-serif, Verdana, Arial, Helvetica;" rows="5" id="meta_description">$meta_description</textarea>
                <br><span class="text-muted"><small>Meta descriptions should be between 150-160 characters (including spaces) <em></em></small></span>
            </td>
        </tr>
        <tr><td>&nbsp;</td></tr>
        <tr>
            <td width="150" valign="top"><label for="og_title">Open Graph Title:</label></td>
            <td>
                <input name="og_title" type="text" id="og_title" class="check-max" value="$og_title" style="width:600px;"><br>
                <span class="text-muted"><small>Page titles should be under 65 characters (including spaces) <em></em></small></span>
            </td>
        </tr>
        <tr>
            <td width="150" valign="top"><label for="og_image">Open Graph Photo:</label></td>
            <td>
                <input name="og_image" type="text" value="$og_image" style="width:350px;" id="og_image" readonly autocomplete="off">
                <input type="button" value="browse" onclick="openFileBrowser('og_image')"> 
                <input type="button" value="clear" onclick="clearValue('og_image')"><br>
            </td>
        </tr>
    </table>
HTML;


##------------------------------------------------------------------------------------------------------
## tab arrays and build tabs

$temp_array_menutab = array();


$temp_array_menutab['Details']          = $details_content;
$temp_array_menutab['Content']          = $main_content;
$temp_array_menutab['Meta Content']     = $meta_content;

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
        <input type=\"hidden\" name=\"meta_data_id\" value=\"$page_meta_data_id\">
    </form>";
require "resultPage.php";
echo $result_page;
exit();

}

?>
