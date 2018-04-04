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

    $main_subheading = 'Editing: '.$name;


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
       ## Settings tab content

    $settings_content = <<< HTML

        <table width="100%" border="0" cellspacing="0" cellpadding="8">
            <tr>
                <td width="200"><b>Name:</b></td>
                <td>
                    <input name="name" type="text" id="name" value="$name" style="width:300px;height:30px;" />
                </td>
            </tr>
        </table>
HTML;

       ##------------------------------------------------------------------------------------------------------
       ## tab arrays and build tabs

    $temp_array_menutab                     = array();
    $temp_array_menutab ['Details'] = $settings_content;

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
