<?php
## ----------------------------------------------------------------------------------------------------------------------
## NetZone 1.0
## inc_settings.php
##
## Author: Ton Jo Immanuel, Tomahawk Brand Management Ltd.
## Date: 19 April 2010
##
## Manage Settings
##
##
## ----------------------------------------------------------------------------------------------------------------------


function do_main()
{

    global $message,$valid,$htmladmin,$scripts_onload,$main_heading, $incdir;

    $action     = $_REQUEST['action'];

    $main_heading = 'General Settings';

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

    $sql = "SELECT `id`, `company_name`, `start_year`, `email_address`, `phone_number`,
        `address`, `js_code_head_close`, `js_code_body_open`, `js_code_body_close`, `adwords_code`,
        `slideshow_speed`, `homepage_slideshow_caption`, `mailchimp_api_key`, `mailchimp_list_id`,
        `map_latitude`, `map_longitude`, `map_address`, `map_styles`, `map_heading`, `map_description`,
        `map_zoom_level`, `map_marker_latitude`, `map_marker_longitude`, `tripadvisor_widget_code`, `donation_heading`, `donation_description`,
        `donation_terms`, `emergency_mode`, `emergency_mode_msg`
        FROM `general_settings`
        WHERE `id` = '1'
        LIMIT 1";

    $row = fetch_row($sql);

    extract($row);


    $map_latitude         = ( $map_latitude != 0 ) ? $map_latitude : '';
    $map_longitude        = ( $map_longitude != 0 ) ? $map_longitude : '';
    $map_zoom_level       = ( $map_zoom_level ) ? $map_zoom_level : 8;
    $map_marker_latitude  = ( $map_marker_latitude != 0 ) ? $map_marker_latitude : '';
    $map_marker_longitude = ( $map_marker_longitude != 0 ) ? $map_marker_longitude : '';

    $emergency_mode_checked = ($emergency_mode == '1') ? 'checked="checked"' : '';

    ##------------------------------------------------------------------------------------------------------
    ## Page functions

    $page_functions = <<< HTML
        <ul class="page-action">
            <li><button type="button" class="btn btn-default" onclick="submitForm('save',1)"><i class="glyphicon glyphicon-floppy-save"></i> Save</button></li>
        </ul>
HTML;

// Social Icons

 $social_icons = fetch_all("SELECT `id`, `label`, `url`, `widget_blob`, `has_widget` FROM `social_links` WHERE `is_active` = '1' ORDER BY `rank`");

    if(count($social_icons) > 0)
    {
        $links = '';
        foreach ($social_icons as $index => $social_icon)
        {
            $index++;

            if($social_icon['has_widget'])
            {
                $input = '<textarea style="width:700px;height:150px;" name="social-item[]" >'.$social_icon['widget_blob'].'</textarea>';
            }
            else
            {
                $input = '<input type="text" style="width:700px" name="social-item[]" value="'.$social_icon['url'].'" id="social-item-'.$index.'">';
            }
           $links .= <<< H
            <tr>
                <td width="150" valign="top"><label for="social-item-$index">{$social_icon['label']}</label></td>
                <td>
                    <input type="hidden" name="social-item-id[]" value="{$social_icon['id']}">
                    <input type="hidden" name="social-item-has-wdge[]" value="{$social_icon['has_widget']}">
                    $input
                </td>
            </tr>
H;
        }
    
    $social_links = <<< H
    
<table width="100%" border="0" cellspacing="0" cellpadding="4">
    $links
</table>

H;

}
else
{
    $social_links = '';
}


    ##------------------------------------------------------------------------------------------------------
    ## Important Pages

    $sql = "SELECT `imppage_name`, `imppage_id`, `page_id`
        FROM `general_importantpages`
        WHERE `imppage_showincms` = 'Y'";

    $imppages_arr = fetch_all($sql);

    $imppages_list = '<table cellspacing="0" cellpadding="5" border="0">';
    foreach($imppages_arr as $key => $array)
    {
        $imppage_name = ucwords($array['imppage_name']);
        $page_id      = $array['page_id'];
        $imppage_id   = $array['imppage_id'];

        $pages_select = page_list(false, 0, $page_id);

        $imppages_list .= <<< HTML
            <tr>
                <td>$imppage_name <input type="hidden" name="imppage_id[]" value="$imppage_id"/></td>
                <td>
                    <select name="page_id[]">
                        <option value="">--select--</option>
                        $pages_select
                    </select>
                </td>
            </tr>
HTML;
    }
    $imppages_list .= <<< HTML
        </table>
HTML;



   
    ##------------------------------------------------------------------------------------------------------
    ## Details Content

    $contact_details = fetch_all("SELECT * FROM `contact_details`");
   
    $address_table = '';

    foreach ($contact_details as $key => $contact_detail) {
        extract($contact_detail);

        $address_table .= <<<H
            <tr>
                <td>
                    <label for="contact_name">Name</label>
                    <input name="contact_name[]" id="contact_name" type="text" value="{$contact_name}" style="width:350px;" />
                </td>
                <td>
                    <label for="contact_address">Address</label>
                    <input name="contact_address[]" id="contact_address" type="text" value="{$contact_address}" style="width:350px;" />
                </td> 
                <td>
                    <label for="contact_phone_number">Phone Number</label>
                    <input name="contact_phone_number[]" id="contact_phone_number" type="text" value="{$contact_phone_number}" style="width:350px;" />
               </td> 
                <td>
                    <label for="contact_email">Email</label>
                    <input name="contact_email[]" id="contact_email" type="text" value="{$contact_email}" style="width:350px;" />
                    <input name="contact_id[]" type="hidden" value="{$id}" style="width:350px;" />
                </td>               
            </tr>
H;

    }

    $companydetails_content = <<< HTML
        <table width="100%" border="0" cellspacing="0" cellpadding="4">
            <tr>
                <td width="150"><label for="company_name">Company name</label></td>
                <td><input name="company_name" id="company_name" type="text" value="{$company_name}" style="width:350px;" /></td>
            </tr>
            <!--<tr>
                <td width="150"><label for="phone_number">Phone Number</label></td>
                <td>
                    <input name="phone_number" type="text" value="{$phone_number}" style="width:150px;" id="phone_number" />
                </td>
            </tr>
            <tr>
                <td width="150"><label for="start_year">Start year</label></td>
                <td><input name="start_year" type="text" value="{$start_year}" style="width:150px;" id="start_year" /></td>
            </tr> 
            <tr>
                <td width="150"><label for="slideshow_speed">Slideshow Speed</label></td>
                <td><input name="slideshow_speed" type="text" value="{$slideshow_speed}" style="width:150px;" id="slideshow_speed" /> <strong>&nbsp;seconds</strong></td>
            </tr>
            <tr>
                <td width="150"><label for="booking_url">Booking URL</label></td>
                <td><input name="booking_url" type="text" value="{$booking_url}" style="width:350px;" id="booking_url" /></td>
            </tr>
            <tr>
                <td width="150"><label for="homepage_slideshow_caption">Homepage Slideshow Caption</label></td>
                <td><input name="homepage_slideshow_caption" id="homepage_slideshow_caption" type="text" value="{$homepage_slideshow_caption}" style="width:350px;" /></td>
            </tr>-->
            <tr>
                <td width="150" valign="top"><label for="email_address">Donation Email(s)</label> <span data-title="Separate multiple email addresses with a semicolon ( ; )" data-placement="right" data-toggle="tooltip"></span></td>
                <td><textarea name="email_address" style="width:350px;min-height:100px;">{$email_address}</textarea></td>
            </tr>
            <!--<tr>
                <td width="150" valign="top"><label for="address">Address</label></td>
                <td><textarea name="address" style="width:350px;min-height:100px;">{$address}</textarea></td>
            </tr> -->
            <tr><td colspan="2">&nbsp;</td></tr>
        </table>

        <h1>Addresses</h1>
        <hr />

        <table width="100%" border="0" cellspacing="0" cellpadding="4">
            {$address_table}
        </table>

HTML;

    ##------------------------------------------------------------------------------------------------------
    ## Achievements Content

    $achievements_query = fetch_all("SELECT * FROM `achievements`");

    $achievements_table = '';

    foreach ($achievements_query as $key => $achievement) {

        extract($achievement);

        $achievements_table .= <<<H
            <tr>
                <td>
                    <input name="achievement_id[]" type="hidden" value="{$id}" style="width:350px;" />
                    <label for="achievement_count_details">Count Details</label>
                    <input name="achievement_count_details[]" id="achievement_count_details" type="text" value="{$achievement_count_details}" style="width:350px;" />
                </td>
                <td>
                    <label for="achievement_description">Description</label>
                    <input name="achievement_description[]" id="achievement_description" type="text" value="{$achievement_description}" style="width:350px;" />
                </td> 
                <td>
                    <label for="achievement_icon_class">Icon Class</label>
                    <input name="achievement_icon_class[]" id="achievement_icon_class" type="text" value="{$achievement_icon_class}" style="width:350px;" />                   
               </td> 
             
            </tr>
H;
    }
    
    $achievements = <<< HTML
        
        <table width="100%" border="0" cellspacing="0" cellpadding="4">
            {$achievements_table}
            <tr>
                <td colspan="3">
                    <p>Please select icons from <a href="http://fontawesome.io/icons/" target="_blank">FontAwesome</a></small>
                </td>
            </tr>
        </table>

HTML;


    ##------------------------------------------------------------------------------------------------------
    ## Important pages Content
    $importantpages_content = <<< HTML
        <table width="100%" border="0" cellspacing="0" cellpadding="4">
            <tr>
                <td colspan="4">$imppages_list</td>
            </tr>
        </table>
HTML;



    ##------------------------------------------------------------------------------------------------------
    ## Developer Content
    $developer_content = <<< HTML
        <table width="100%" border="0" cellspacing="0" cellpadding="4">
            <tr>
                <td valign="top"><label for="js_code_head_close">Head tag insert (close)</label></td>
                <td valign="top">
                    <textarea name="js_code_head_close" style="width:720px; height:150px;resize:none;" id="js_code_head_close">{$js_code_head_close}</textarea>
                    <span data-toggle="tooltip" data-placement="left" data-title="Insert code before closing head tag e.g Google Analytics, Facebook Pixel"></span>
                </td>
            </tr>
            <tr>
                <td valign="top"><label for="js_code_body_open">Body tag insert (open)</label></td>
                <td valign="top">
                    <textarea name="js_code_body_open" style="width:720px; height:150px;resize:none;" id="js_code_body_open">{$js_code_body_open}</textarea>
                    <span data-toggle="tooltip" data-placement="left" data-title="Insert code after opening body tag e.g Google Analytics, Facebook Pixel"></span>
                </td>
            </tr>
            <tr>
                <td valign="top"><label for="js_code_body_close">Body tag insert (close)</label></td>
                <td valign="top">
                    <textarea name="js_code_body_close" style="width:720px; height:150px;resize:none;" id="js_code_body_close">{$js_code_body_close}</textarea>
                    <span data-toggle="tooltip" data-placement="left" data-title="Insert code before closing body tag e.g Google Analytics, Facebook Pixel"></span>
                </td>
            </tr>
            <tr>
                <td valign="top"><label for="adwords_code">AdWords Tracking Code</label></td>
                <td valign="top">
                    <textarea name="adwords_code" style="width:720px; height:150px;resize:none;" id="adwords_code">{$adwords_code}</textarea>
                    <span data-toggle="tooltip" data-placement="left" data-title="Google AdWords Tracking Code"></span>
                </td>
            </tr>
        </table>
HTML;

$widgets_content = <<< HTML
            <table width="100%" border="0" cellspacing="0" cellpadding="4">
                <tr>
                    <td style="vertical-align:top;">Trip Advisor Widget</td>
                    <td colspan="3">
                        <textarea name="tripadvisor_widget" style="width:790px; height:200px;">$tripadvisor_widget</textarea>
                    </td>
                </tr>
                <tr>
                    <td style="vertical-align:top;">Facebook Widget</td>
                    <td colspan="3">
                        <textarea name="facebook_widget" style="width:790px; height:200px;">$facebook_widget</textarea>
                    </td>
                </tr>
            </table>
HTML;

$mailchimp_details = <<< HTML
    <table width="100%" border="0" cellspacing="0" cellpadding="4">
       
        <tr>
            <td width="150"><label for="mailchimp_list_id">List ID</label></td>
            <td><input name="mailchimp_list_id" type="text" value="{$mailchimp_list_id}" style="width:150px;" id="mailchimp_list_id" /></td>
        </tr>
        <tr>
            <td width="150"><label for="mailchimp_api_key">API Key</label></td>
            <td><input name="mailchimp_api_key" type="text" value="{$mailchimp_api_key}" style="width:350px;" id="mailchimp_api_key" /></td>
        </tr>
    </table>

HTML;

$homepage_headings = <<< HTML
    <table width="100%" border="0" cellspacing="0" cellpadding="4">
       
        <tr>
            <td width="150"><label for="donation_heading">Module Heading</label></td>
            <td><input name="donation_heading" type="text" value="{$donation_heading}" style="width:300px;" id="donation_heading" /></td>
        </tr>
        <tr>
            <td width="150" valign="top"><label for="donation_description">Short Description</label></td>
            <td><textarea name="donation_description" style="width:800px; height: 250px;" id="donation_description">$donation_description</textarea>
            </td>
        </tr>
    </table>

HTML;

$donation_terms = <<<HTML
    <table width="100%" border="0" cellspacing="0" cellpadding="4">       
        <tr>
            <td>
                <textarea name="donation_terms" class="check-max" style="width:800px; min-height: 250px;" id="donation_terms">$donation_terms</textarea>
                <script>
                    CKEDITOR.replace( 'donation_terms',
                    {
                        toolbar : 'MyToolbar',
                        forcePasteAsPlainText : true,
                        resize_enabled : false,
                        height : 600,
                        filebrowserBrowseUrl : jsVars.dataManagerUrl
                    });               
                </script>
            </td>
        </tr>
    </table>
HTML;


$emergency_mode = <<<HTML
    <table width="100%" border="0" cellspacing="0" cellpadding="4">
        <tr>
            <td width="250"><label for="emergency_mode">Put Website into Emergency Mode?</label></td>
            <td><input name="emergency_mode" type="checkbox" $emergency_mode_checked id="emergency_mode" value="1" /></td>
        </tr> 



        <tr>
            <td width="250"><label for="emergency_mode_msg">Emergency Mode Message:</label></td>
            <td>
                <textarea name="emergency_mode_msg" class="check-max" style="width:800px; min-height: 250px;" id="emergency_mode_msg">$emergency_mode_msg</textarea>
                <script>
                    CKEDITOR.replace( 'emergency_mode_msg',
                    {
                        toolbar : 'MyToolbar',
                        forcePasteAsPlainText : true,
                        resize_enabled : false,
                        height : 400,
                        filebrowserBrowseUrl : jsVars.dataManagerUrl
                    });               
                </script>
            </td>
        </tr>
    </table>
HTML;

$map_contents = <<< H

<table width="100%" border="0" cellspacing="0" cellpadding="6">
    <tr>
        <td width="100"><label for="map_heading">Marker Title</label></td>
        <td>
            <input type="text" style="width:350px;" id="map_heading" name="map_heading" value="{$map_heading}">
        </td>
    </tr>
    <tr>
        <td width="100"><label for="map_address">Map Address</label></td>
        <td>
            <input type="text" style="width:350px;" id="map_address" name="map_address" value="{$map_address}">
            <button type="button" id="get-map-address">Search</button>
        </td>
    </tr>
    <tr>
        <td colspan="2">
            <div id="gmap-canvas">
                <h3 style="font-size:18px;color:#000;padding:10px;font-weight:700;margin:0;">Loading map...</h3>
            </div>
        </td>
    </tr>
    <tr>
        <td colspan="2">
            <input type="hidden" id="map_latitude" name="map_latitude" value="{$map_latitude}">
            <input type="hidden" id="map_longitude" name="map_longitude" value="{$map_longitude}">
            <input type="hidden" id="map_zoom_level" name="map_zoom_level" value="{$map_zoom_level}">
            <input type="hidden" id="map_marker_latitude" name="map_marker_latitude" value="{$map_marker_latitude}">
            <input type="hidden" id="map_marker_longitude" name="map_marker_longitude" value="{$map_marker_longitude}">
            <textarea name="map_styles" id="map_styles" class="hidden">{$map_styles}</textarea>
        </td>
    </tr>
</table>

H;

    $tripadvisor_widget_html = <<< H
    
    <textarea name="tripadvisor_widget_code" id="tripadvisor_widget_code" style="height:300px;width:100%;resize:none;">{$tripadvisor_widget_code}</textarea>


H;
    

    $scripts_ext .= '<script src="http://maps.google.com/maps/api/js?key=AIzaSyASNvs-56CpZIS0wxatFziPERq9ALArajU"></script>';
    $scripts_ext .= '<script src="'.$htmladmin.'/js/general-map.js"></script>';

    // require_once

    ##------------------------------------------------------------------------------------------------------
    ## tab arrays and build tabs

    $temp_array_menutab = array();
    
    $temp_array_menutab ['Google Map']         = $map_contents;
    $temp_array_menutab ['Company Details']    = $companydetails_content;
    $temp_array_menutab ['MailChimp Details']  = $mailchimp_details;
    $temp_array_menutab ['Social Links']       = $social_links;
    $temp_array_menutab ['Home Page Headings'] = $homepage_headings;
    $temp_array_menutab ['Donations Terms']    = $donation_terms;
    $temp_array_menutab ['Emergency Mode']       = $emergency_mode;
    $temp_array_menutab ['Achievements']       = $achievements;
    $temp_array_menutab ['TripAdvisor Widget'] = $tripadvisor_widget_html;
    $temp_array_menutab ['Important Pages']    = $importantpages_content;
    $temp_array_menutab ['Template Code']      = $developer_content;
    

    $counter = 0;
    $tablist ="";
    $contentlist="";

    foreach($temp_array_menutab as $key => $value){

        $tablist.= "<li><a href=\"#tabs-".$counter."\">".$key."</a></li>";

        $contentlist.=" <div id=\"tabs-".$counter."\">".$value."</div>";

        $counter++;
    }

    $tablist="<div id=\"tabs\"><ul>".$tablist."</ul><div style=\"padding:10px;\">".$contentlist."</div></div>";

    $page_contents = <<< HTML
        <form action="$htmladmin/index.php" method="post" name="pageList" enctype="multipart/form-data">
            $tablist
            <input type="hidden" name="action" value="" id="action">
            <input type="hidden" name="do" value="settings">
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

    global $message,$id,$do,$disable_menu, $incdir;



    $temp_array_save['company_name']               = sanitize_input('company_name');
    $temp_array_save['email_address']              = sanitize_input('email_address');
    $temp_array_save['js_code_head_close']         = filter_input(INPUT_POST, 'js_code_head_close');
    $temp_array_save['js_code_body_open']          = filter_input(INPUT_POST, 'js_code_body_open');
    $temp_array_save['js_code_body_close']         = filter_input(INPUT_POST, 'js_code_body_close');
    $temp_array_save['adwords_code']               = filter_input(INPUT_POST, 'adwords_code');
    $temp_array_save['start_year']                 = sanitize_input('start_year');
    $temp_array_save['slideshow_speed']            = sanitize_input('slideshow_speed');
    $temp_array_save['phone_number']               = sanitize_input('phone_number');
    $temp_array_save['address']                    = sanitize_input('address');
    $temp_array_save['homepage_slideshow_caption'] = sanitize_input('homepage_slideshow_caption');
    $temp_array_save['mailchimp_api_key']          = sanitize_input('mailchimp_api_key');
    $temp_array_save['mailchimp_list_id']          = sanitize_input('mailchimp_list_id');
    $temp_array_save['map_latitude']               = sanitize_input('map_latitude');
    $temp_array_save['map_longitude']              = sanitize_input('map_longitude');
    $temp_array_save['map_heading']                = sanitize_input('map_heading');
    $temp_array_save['map_address']                = sanitize_input('map_address');
    $temp_array_save['map_description']            = sanitize_input('map_description');
    $temp_array_save['map_zoom_level']             = sanitize_input('map_zoom_level', FILTER_VALIDATE_INT);
    $temp_array_save['map_marker_latitude']        = sanitize_input('map_marker_latitude', FILTER_VALIDATE_FLOAT);
    $temp_array_save['map_marker_longitude']       = sanitize_input('map_marker_longitude', FILTER_VALIDATE_FLOAT);
    $temp_array_save['booking_url']                = sanitize_input('booking_url', FILTER_VALIDATE_URL);
    $temp_array_save['tripadvisor_widget_code']    = filter_input(INPUT_POST, 'tripadvisor_widget_code');
    $temp_array_save['donation_heading']           = sanitize_input('donation_heading');
    $temp_array_save['donation_description']       = sanitize_input('donation_description');
    $temp_array_save['donation_terms']             = sanitize_input('donation_terms');
    $temp_array_save['emergency_mode']             = sanitize_input('emergency_mode');
    $temp_array_save['emergency_mode_msg']         = $_POST['emergency_mode_msg'];


    if( update_row($temp_array_save, 'general_settings', "WHERE id = '1' LIMIT 1") )
    {
        $message = "Settings have been saved";
    }
    // save contact details

    $contact_name         = $_POST['contact_name'];
    $contact_address      = $_POST['contact_address'];
    $contact_phone_number = $_POST['contact_phone_number'];
    $contact_email        = $_POST['contact_email'];
    $contact_id           = $_POST['contact_id'];

    if ($contact_id > 0) {
       for ($i=0; $i < count($contact_id) ; $i++) { 
           $contact_array = array();

           $contact_array['contact_name']         = $contact_name[$i];
           $contact_array['contact_address']      = $contact_address[$i];
           $contact_array['contact_phone_number'] = $contact_phone_number[$i];
           $contact_array['contact_email']        = $contact_email[$i];

           update_row($contact_array, 'contact_details', "WHERE id = '{$contact_id[$i]}'");

       }
    }

    // save achievement details

    $achievement_count_details = $_POST['achievement_count_details'];
    $achievement_description   = $_POST['achievement_description'];
    $achievement_icon_class    = $_POST['achievement_icon_class'];
    $achievement_id            = $_POST['achievement_id'];

    if ($achievement_id > 0) {

        for ($i=0; $i < count($achievement_id) ; $i++) { 
            $achievement_array = array();

            $achievement_array['achievement_count_details'] = $achievement_count_details[$i];
            $achievement_array['achievement_description']   = $achievement_description[$i];
            $achievement_array['achievement_icon_class']    = $achievement_icon_class[$i];

            update_row($achievement_array, 'achievements', "WHERE id = '{$achievement_id[$i]}'");
        }
    }


    // save social urls

    $url_ids    = $_POST['social-item-id'];
    $urls       = $_POST['social-item'];
    $has_widget = $_POST['social-item-has-wdge'];

    if(count($url_ids) > 0)
    {
        for ($i=0; $i < count($url_ids); $i++)
        { 
            $url_save_arr = array();
            if($has_widget[$i]) $url_save_arr['widget_blob'] = $urls[$i];
            else $url_save_arr['url'] = $urls[$i];

            update_row($url_save_arr, 'social_links', "WHERE id = '{$url_ids[$i]}'");
        }
    }

    $imppage_id = $_REQUEST['imppage_id'];
    $page_id = $_REQUEST['page_id'];
    $i = 0;
    foreach($imppage_id as $key => $value){
        $end = "WHERE imppage_id = '$value'";
        $temp_array_save = '';
        $temp_array_save['page_id']     = $page_id[$i];
        update_row($temp_array_save, 'general_importantpages', $end);
        $i++;
    }


}

?>