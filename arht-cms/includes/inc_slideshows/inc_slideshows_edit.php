<?php
## ----------------------------------------------------------------------------------------------------------------------
## Edit page
function edit_item() {

    global $message,$id,$do,$disable_menu,$valid,$htmladmin,$rootadmin,$rootfull, $main_subheading;

    $disable_menu = "true";
    
    $sql  = "SELECT `name`
        FROM `photo_group`
        WHERE `id` = '{$id}'
        AND `type` = 'S'
        AND `show_in_cms` = 'Y'
        LIMIT 1";
    $row         = fetch_row($sql);
    $name        = $row['name'];
    
    $main_subheading = 'Editing slideshow: '.$name;

    ##------------------------------------------------------------------------------------------------------
    ## Page functions

    $page_functions = <<< HTML
        <ul class="page-action">
            <li><button type="button" class="btn btn-default" onclick="submitForm('save',1)"><i class="glyphicon glyphicon-floppy-save"></i> Save</button></li>
            <li><a class="btn btn-default" href="{$htmladmin}/?do={$do}"><i class="glyphicon glyphicon-arrow-left"></i> Cancel</a>
            </li>
        </ul>
HTML;

    $details_content = <<< HTML
                        <table width="100%" border="0" cellspacing="0" cellpadding="4" >
                            <tr>
                                <td width="150"><label for="label">Name:</label></td>
                                <td><input name="label" class="textbox" type="text" id="label" value="$name" style="width:300px;" /></td>
                            </tr>
                        </table>
HTML;


    ##------------------------------------------------------------------------------------------------------
    ## Photos
    

    $sql = "SELECT `full_path`, `thumb_path`, `caption_heading`, `caption`, `alt_text`, `button_label`, `url`,`rank`
        FROM `photo`
        WHERE `photo_group_id` = '{$id}'
        ORDER BY `rank`";

    $result = fetch_all($sql);
    $photocount = 1;
    $photolist="";

    $photolist .= '<ul class="slides">';
    if(!empty($result)) {     

        foreach ($result as $row)
        {

            $slide_id                = $row['id'];
            $full_path               = $row['full_path'];
            $thumb_path              = $row['thumb_path'];
            $caption_heading         = $row['caption_heading'];
            $caption                 = $row['caption'];
            $button_label            = $row['button_label'];
            $slide_url               = $row['url'];
            $rank                    = $row['rank'];
            $alt_text                = $row['alt_text'];

       
            // Get new dimensions
            $width  = 150;
            $height = 150;
            
            list($width_orig, $height_orig) = getimagesize("$rootfull$full_path");

            if($height_orig!=0 && $width_orig !=0) {
                $ratio_orig = $width_orig/$height_orig;
                if ($width/$height > $ratio_orig) {
                    $width = $height*$ratio_orig;
                } else {
                    $height = $width/$ratio_orig;
                }
                $photolist .= <<< HTML
                        
               <li id="photo_$photocount">
                    <div class="to-left">
                        <div class="img-wrap">
                            <img src="$full_path" alt="Slide Image $photocount">
                            <input type="hidden" value="$full_path" name="photo-full-path[]">
                        </div>
                    </div>
                    <div class="to-left padded">
                        <ul>
                            <li>
                                <label for="caption-$photocount">URL:</label>
                                <input type="text" maxlength="150" id="caption-$photocount" name="photo-url[]" value="$slide_url" class="input-xxlrg">
                            </li>
                            <li>
                                <label for="rank-$photocount">Rank:</label>
                                <input type="text" id="rank-$photocount" name="photo-rank[]" value="$rank" class="input-small">
                                <a href="javascript:;" onClick="removePhoto($photocount);">remove</a>
                            </li>
                        </ul>
                    </div>
                    <div class="clearfix clear"></div>
               </li>
HTML;
            $photocount = $photocount + 1;
            }
            
        }
       
    }
    $photolist .= '</ul>';
    $photocount = $photocount - 1;
    
$photo_content = <<< HTML
                        <p><strong>Recommend size: 1920x1080px and format: jpg</strong></p>
                        <table width="100%" cellpadding="0" cellspacing="0">
                            <tr>
                                <td><div style="margin-bottom:10px;"><a href="javascript:;" onClick="addPhoto();" class="btn btn-primary" style="color:#fff"><i class="glyphicon glyphicon-plus-sign" style="vertical-align:text-top;margin:0px 4px 0 0"></i> add new slide</a></div>$photolist
                                    <div id="newPhotos"></div>
                                    <input type="hidden" value="$photocount" id="lineValue" />
                                    <input type="hidden" id="tempPhoto" name="tempPhoto" value="">
                                </td>
                            </tr>
                        </table>

                        <script type="text/javascript">


                        function unSelectVal(elm)
                        {
                            var jElm = $(elm);
                            
                            if(jElm.length)
                            {
                                jElm.on('change', function(){
                                    
                                    var self = $(this),
                                    targetElm = $(self.data('set-default-of')),
                                    opts = targetElm.find('option');
                                    opts.attr('selected', false);
                                    opts.first().attr('selected', true);
                                    
                                    
                                    targetElm = $(self.data('set-default-for')),
                                    opts = targetElm.find('option');
                                    opts.attr('selected', false);
                                    opts.first().attr('selected', true);
                                });
                            }
                        }

                        unSelectVal('.trigger-default');

                            function removePhoto(id) {
                                var id;
                                id = "photo_" + id;
                                $('#'+id).remove();
                            }

                            function addPhoto() {

                                var winl = (screen.width - 1000) / 2;
                                var wint = (screen.height - 700) / 2;
                                var mypage = jsVars.dataManagerUrl+"&NetZone=tempPhoto";
                                var myname = "imageSelector";
                                winprops = 'status=yes,height=700,width=1000,top='+wint+',left='+winl+',scrollbars=auto,resizable'
                                win = window.open(mypage, myname, winprops)
                                if (parseInt(navigator.appVersion) >= 4) { win.window.focus(); }
                            }

                            function SetUrl(p,w,h) {
                                var p;
                                var w;
                                var h;
                                document.getElementById('tempPhoto').value=p;
                                setNewPhoto();
                            }
                            function setNewPhoto() {
                                var ni = $('.slides');
                                var numi = parseInt(ni.find('[id^="photo_"]').size(), 10);
                                
                                var num = (document.getElementById('lineValue').value -1)+ 2;
                                numi.value = num;
                                var newdiv = document.createElement('div');
                
                
                                var divIdName = 'photo_'+num;
                                newdiv.setAttribute('id',divIdName);
                                newdiv.setAttribute('style','float:left; width:160px; height:180px; margin-right:10px; margin-bottom:10px;');
                                var newPhotoUrl = document.getElementById('tempPhoto').value;

// <li>\
//      <label for="caption-'+num+'">Heading:</label>\
//      <input type="text" id="caption-heading-'+num+'" maxlength="150" name="photo_caption_heading[]" value="" class="input-xxlrg">\
// </li>\

var newSlide = '<li id="photo_'+num+'">\
<div class="to-left">\
    <div class="img-wrap">\
        <img src="'+newPhotoUrl+'" alt="Slide Image '+num+'">\
        <input type="hidden" value="'+document.getElementById('tempPhoto').value + '" name="photo-full-path[]">\
    </div>\
</div>\
<div class="to-left padded">\
    <ul>\
        <li>\
            <label for="caption-'+num+'">URL:</label>\
            <input type="text" id="caption-'+num+'" maxlength="150" name="photo-url[]" value="" class="input-xxlrg">\
        </li>\
        <li>\
            <label for="rank-'+num+'" >Rank:</label>\
            <input type="text" id="rank-'+num+'" name="photo-rank[]" value="" class="input-small">\
            <a href="javascript:;" onClick="removePhoto('+num+');">remove</a>\
        </li>\
    </ul>\
</div>\
<div class="clearfix clear"></div>\
</li>';

                              

                              ni.append(newSlide);

                              unSelectVal('.trigger-default');
                            }
                        </script>
HTML;

    ##------------------------------------------------------------------------------------------------------
    ## tab arrays and build tabs

    $temp_array_menutab = array();

    $temp_array_menutab ['Settings']    = $details_content;
    $temp_array_menutab ['Photos']      = $photo_content;

    $counter = 0;
    $tablist ="";
    $contentlist="";

    foreach($temp_array_menutab as $key => $value) {

        $tablist.= "<li><a href=\"#tabs-$counter\">$key</a></li>";

        $contentlist.=" <div id=\"tabs-$counter\">$value</div>";

        $counter++;
    }

    $tablist="<div id=\"tabs\"><ul>$tablist</ul><div style=\"padding:10px;\">$contentlist</div></div>";

    $page_contents = <<< HTML
                        <form action="$htmladmin/index.php" method="post" name="pageList" enctype="multipart/form-data">
                $tablist
                            <input type="hidden" name="action" value="" id="action">
                            <input type="hidden" name="do" value="{$do}">
                            <input type="hidden" name="id" value="{$id}">
                        </form>
HTML;
    require "resultPage.php";
    echo $result_page;
    exit();


}

?>