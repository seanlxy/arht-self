<?php 

$photos = fetch_all("SELECT `full_path`, `thumb_path`, `rank`, `type` 
    FROM `photo`
    WHERE `photo_group_id` = '{$slideshow_id}'
    ORDER BY `rank`");


$photos_html = '';

    
if( !empty($photos) )
{

    foreach ($photos as $photocount => $photo)
    {

        $photocount++;

        $full_path       = $photo['full_path'];
        $thumb_path      = $photo['thumb_path'];
        $rank            = $photo['rank'];
        $type            = $photo['type'];

        $img_src = ($thumb_path) ? $thumb_path : $full_path;

        $is_primary = ( $type === 'P' ) ? ' checked="checked"' : '';

        $photos_html .= <<< HTML

        <li id="photo_{$photocount}" class="img-thumbnail" title="Click to make this photo primary">
            <label for="is-main-{$photocount}" class="overlay-lbl">
                <div class="actions to-corner">
                    <input type="text" value="{$rank}" name="photo-rank[]" style="text-align:center">
                    <a  href="#" title="Remove this photo" class="remove-photo"><i class="fa fa-times"></i></a>
                </div>
                <div class="img" style="background-image:url({$img_src})"></div>
                <input type="hidden" value="{$thumb_path}" name="photo-thumb-path[]">
                <input type="hidden" value="{$full_path}" name="photo-full-path[]">
                <input type="radio" name="is-main" id="is-main-{$photocount}" value="{$photocount}" class="sel-item"{$is_primary}>
                <span data-caption="Primary photo"></span>
            </label>
       </li>
HTML;
        
        
    }

   
}

    
$slideshow_content = <<< HTML
<input type="hidden" id="tempPhoto" name="tempPhoto" value="">
<input type="hidden" id="lineValue" name="lineValue" value="$total_photos">
<div style="margin-bottom:10px;">
    <a href="javascript:;" onClick="addPhoto();" class="btn btn-primary" style="color:#fff">
        <i class="glyphicon glyphicon-plus-sign" style="vertical-align:text-top;margin:0px 4px 0 0"></i> add new photo
    </a>
</div>
<ul class="photos-grid" id="photos">
    {$photos_html}
</ul>


<script type="text/javascript">

function addPhoto()
{
    var winl = (screen.width - 1000) / 2;
    var wint = (screen.height - 700) / 2;
    var mypage = jsVars.dataManagerUrl+"&NetZone=tempPhoto";
    var myname = "imageSelector";
    winprops = 'status=yes,height=700,width=1000,top='+wint+',left='+winl+',scrollbars=auto,resizable'
    win = window.open(mypage, myname, winprops)
    if (parseInt(navigator.appVersion) >= 4) { win.window.focus(); }
}

$('#photos').on('click', '.remove-photo', function(e){
    e.preventDefault();
    
    var self = $(this);
    self.parents('li').remove();

});

function SetUrl(p,w,h)
{
    var p;
    var w;
    var h;
    document.getElementById('tempPhoto').value=p;
    setNewPhoto();
}

function setNewPhoto()
{
    var ni = $('#photos');
    var numi = parseInt(ni.find('[id^="photo_"]').size(), 10);
    var num = numi+1;
    numi.value = num;
    var newdiv = document.createElement('div');


    var divIdName = 'photo-'+num;

    newdiv.setAttribute('id',divIdName);

    newdiv.setAttribute('style','float:left; width:160px; height:180px; margin-right:10px; margin-bottom:10px;');
    var newPhotoUrl = document.getElementById('tempPhoto').value;

var newSlide = '<li id="photo_'+num+'" class="img-thumbnail" title="Click to make this photo primary">\
            <label for="is-main-'+num+'" class="overlay-lbl">\
            <div class="actions to-corner">\
                <input type="text" value="" name="photo-rank[]" style="text-align:center">\
                <a  href="#" title="Remove this photo" class="remove-photo"><i class="fa fa-times"></i></a>\
            </div>\
            <div class="img" style="background-image:url('+newPhotoUrl+')"></div>\
            <input type="hidden" value="" name="photo-thumb-path[]">\
            <input type="hidden" value="'+newPhotoUrl+'" name="photo-full-path[]">\
            <input type="radio" name="is-main" id="is-main-'+num+'" value="'+num+'" class="sel-item">\
            <span data-caption="Primary photo"></span>\
        </label>\
       </li>';

ni.append(newSlide);


}

</script>

HTML;


?>