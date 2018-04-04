<?php

############################################################################################################################
## Edit FAQ Item
############################################################################################################################

function edit_item()
{

    global $message,$id,$do,$disable_menu,$valid,$htmladmin, $main_subheading, $js_vars;

    $disable_menu = "true";

    $sql = "SELECT n.`id`, n.`news_url`, n.`date`, n.`page_meta_data_id`,
            pmd.`name`, pmd.`url`, pmd.`menu_label`, pmd.`heading`, pmd.`title`, pmd.`short_description`, pmd.`introduction`,
            pmd.`og_title`, pmd.`meta_description`, pmd.`og_image`, pmd.`thumb_photo`, pmd.`slideshow_id`, pmd.`gallery_id`
            FROM `news` n
            LEFT JOIN `page_meta_data` pmd
            ON(n.`page_meta_data_id` = pmd.`id`)
            WHERE n.`id` = '{$id}'
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
            <td width="130"><label for="news_url">URL:</label></td>
            <td colspan="3">
                <input name="news_url" type="text" id="news_url" value="$news_url" maxlength="255" style="width:600px; float:left;" />
                <input type="button" value="Get info" id="get_info" style="float:left;margin:-1px 0 0 10px">
                <div id="facebookG" class="preloader">
                    <div id="blockG_1" class="facebook_blockG">
                    </div>
                    <div id="blockG_2" class="facebook_blockG">
                    </div>
                    <div id="blockG_3" class="facebook_blockG">
                    </div>
                </div>
                <div class="clear"></div>
                <small>Paste URL in above field to fetch information.</small>
            </td>
        </tr>
        <tr>
            <td width="100">Title</td>
            <td><input name="title" type="text" id="title" value="$title" maxlength="255" style="width:385px;" /></td>
        </tr>
        <tr>
            <td width="100" valign="top">Name:</td>
            <td><input name="name" type="text" id="name" value="$name" maxlength="255" style="width:385px;" /></td>
        </tr>
        <tr>
            <td width="100" valign="top">Date:</td>
            <td>
                <input name="date" type="text" id="date" value="$date" maxlength="10" style="width:85px;" readonly>
                <script>
                    $('#date').attr({autocomplete:'off', readonly:true}).datepicker({
                        dateFormat:'yy-mm-dd'
                    });
                </script>
            </td>
        </tr>
        <tr>
            <td width="100" valign="top">Heading:</td>
            <td colspan="3"><input name="heading" type="text" id="heading" value="$heading" maxlength="255" style="width:750px;" /></td>
        </tr>
        <tr>
            <td width="150" valign="top">Please select a photo:</td>
            <td>
                <input name="thumb_photo" type="text" value="$thumb_photo" id="thumb_photo" style="width:300px;" id="thumb_photo" readonly>
                <input type="button" value="browse" onclick="openFileBrowser('thumb_photo')"> 
                <input type="button" value="clear" onclick="clearValue('thumb_photo')"><br>
            </td>
        </tr>
        <tr>
            <td width="100" valign="top">Short Description:</td>
            <td colspan="3">
                <textarea name="short_description" id="short_description" style="width:795px;height:150px;">$short_description</textarea>
            </td>
        </tr>
        <script>
            window.requestArr = [];
            $('#get_info').on('click', function(){
                var self = $('#news_url'),
                url = self.val(),
                preloader = $('.preloader');
                

                if(url)
                {
                    preloader.show();
                    var r = /:\/\/(.[^/]+)/,
                    domain = url.match(r)[1];

                            $(window.requestArr).each(function(a, b){
                                b.abort();                                         
                            });

                   window.requestArr.push(
                        $.get('$htmladmin/ajax/ajax_functions.php', 'action=page-source&rurl='+encodeURIComponent(url), function(data){
                           
                            if(data.status === 'OK')
                            {
                                var source = $(data.source);
                                var dataObj = {
                                    title:source.filter('title').text(),
                                    description:source.filter('meta[name="description"]').attr('content'),
                                    // longDescription:source.find('.content, #content, main, #main, .main, [role="content"], .main-wrapper, #main-wrapper').html(),
                                    heading:((source.find('[role="main"] h1').length) ? source.find('[role="main"] h1:first').text() : source.find('h1:first').text()),
                                    og:{
                                        title:source.filter('meta[property="og:title"]'),
                                        description:source.filter('meta[property="og:description"]').attr('content'),
                                        images:source.filter('meta[property="og:image"]:first').attr('content'),
                                        url:source.filter('meta[property="og:url"]'),
                                    }
                                };
                                var title = (dataObj.title) ? dataObj.title :((dataObj.og.title) ? dataObj.og.title : ''),
                                    description = (dataObj.description) ? dataObj.description :((dataObj.og.description) ? dataObj.og.description : '');
                                    image = (dataObj.og.images) ? dataObj.og.images : '';
                            }

                            setTimeout(function()
                            {
                                $('#title').val(title);
                                $('#heading, #name').val(dataObj.heading);
                                $('#short_description').val(description);
                                $('#thumb_photo').val(image);
                                $('#desc-warn').remove();
                                if(!description)
                                {
                                    $('<div id="desc-warn"><strong>No description found.</strong></div>').insertBefore('#short_description');
                                }
                                preloader.hide();

                            }, 1300);

                            
                        }, 'json')
                    );
                }

            });
            
        </script>
    </table>
    

HTML;

##------------------------------------------------------------------------------------------------------
## tab arrays and build tabs

$temp_array_menutab = array();


$temp_array_menutab['Details']          = $details_content;

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
