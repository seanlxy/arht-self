<?php
## ----------------------------------------------------------------------------------------------------------------------
## NetZone 1.0
## inc_shopify.php
##
## Author: Jed Diaz, Tomahawk Brand Management Ltd.
## Date: 21 November 2017
##
## Manage Settings
##
##
## ----------------------------------------------------------------------------------------------------------------------

function do_main()
{

    global $message,$valid,$htmladmin,$scripts_onload,$main_heading, $incdir, $htmlroot;

    $action     = $_REQUEST['action'];

    $main_heading = 'Shopify Settings';

    switch ($action) {

        case 'save':

            $return = save_item();
            break;

    }
    
    if ($message != "") {

        $page_contents .= <<< HTML
          <div class="alert alert-warning page">
             <i class="glyphicon glyphicon-info-sign"></i>
              <strong>$message</strong>
          </div>
HTML;
    

    }

    $sql = "SELECT `id`, `shopify_url`, `shopify_api_key`, `shopify_api_password`
        FROM `spty_settings`
        WHERE `id` = '1'
        LIMIT 1";

    $row = fetch_row($sql);

    extract($row);

    $map_latitude         = ( $map_latitude != 0 ) ? $map_latitude : '';
    $map_longitude        = ( $map_longitude != 0 ) ? $map_longitude : '';
    $map_zoom_level       = ( $map_zoom_level ) ? $map_zoom_level : 8;
    $map_marker_latitude  = ( $map_marker_latitude != 0 ) ? $map_marker_latitude : '';
    $map_marker_longitude = ( $map_marker_longitude != 0 ) ? $map_marker_longitude : '';


    ##------------------------------------------------------------------------------------------------------
    ## Page functions

    $page_functions = <<< HTML
        <ul class="page-action">
            <li><button type="button" class="btn btn-default" onclick="submitForm('save',1)"><i class="glyphicon glyphicon-floppy-save"></i> Save</button></li>
        </ul>
HTML;

    ##------------------------------------------------------------------------------------------------------
    ## Details Content
    $main_content = <<< HTML
        <table width="100%" border="0" cellspacing="0" cellpadding="4">
            <tr>
                <td width="150"><label for="shopify_url">Shop URL</label></td>
                <td><input name="shopify_url" type="text" value="{$shopify_url}" style="width:350px;" id="shopify_url" /></td>
            </tr>
            <tr>
                <td width="150"><label for="shopify_api_key">API Key</label></td>
                <td><input name="shopify_api_key" type="text" value="{$shopify_api_key}" style="width:350px;" id="shopify_api_key" /></td>
            </tr>
            <tr>
                <td width="150"><label for="shopify_api_password">API Password</label></td>
                <td><input name="shopify_api_password" type="text" value="{$shopify_api_password}" style="width:350px;" id="shopify_api_password" /></td>
            </tr>
            <tr><td colspan="2">&nbsp;</td></tr>
        </table>
HTML;

    ##------------------------------------------------------------------------------------------------------
    ## tab arrays and build tabs

    $temp_array_menutab = array();

    
    $temp_array_menutab ['Main Settings']      = $main_content;
    
    $counter     = 0;
    $tablist     = "";
    $contentlist = "";

    foreach($temp_array_menutab as $key => $value)
    {

        $tablist.= "<li><a href=\"#tabs-".$counter."\">".$key."</a></li>";

        $contentlist.=" <div id=\"tabs-".$counter."\">".$value."</div>";

        $counter++;
    }

    $tablist="<div id=\"tabs\"><ul>".$tablist."</ul><div style=\"padding:10px;\">".$contentlist."</div></div>";

    $page_contents = <<< HTML
        <form action="$htmladmin/index.php" method="post" name="pageList" enctype="multipart/form-data">
            $tablist
            <input type="hidden" name="action" value="" id="action">
            <input type="hidden" name="do" value="shopify">
            <input type="hidden" name="id" value="$id">
            <input type="hidden" name="set_id" value="$id">
        </form>
HTML;

    require "resultPage.php";
    echo $result_page;
    exit();
}

function save_item()
{

    global $message, $id, $do, $disable_menu, $incdir, $htmladmin, $rootfull, $rootadmin, $upload_dir;

    $temp_array_save['shopify_url']                = sanitize_input('shopify_url');
    $temp_array_save['shopify_api_key']            = sanitize_input('shopify_api_key');
    $temp_array_save['shopify_api_password']       = sanitize_input('shopify_api_password');
    
    if( update_row($temp_array_save, 'spty_settings', "WHERE id = '1' LIMIT 1") )
    {
        $message = "Settings have been saved";
    }

    header("Location: {$htmladmin}/?do={$do}");
    exit();


}

?>