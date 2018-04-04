<?php
###############################################################################################################################
## Fetch Website Settings
###############################################################################################################################

function fetchSettings()
{
    $sql = "SELECT gs.`company_name`, gs.`start_year`, gs.`email_address`, gs.`phone_number`,
        gs.`address`, gs.`js_code_head_close`, gs.`js_code_body_open`, gs.`js_code_body_close`,
        gs.`adwords_code`, gs.`slideshow_speed`, gs.`homepage_slideshow_caption`, gs.`mailchimp_api_key`, gs.`mailchimp_list_id`,
        gs.`map_latitude`, gs.`map_longitude`, gs.`map_heading`, gs.`map_description`, gs.`booking_url`, gs.`tripadvisor_widget_code`,
        gs.`donation_heading`, gs.`donation_description`, gs.`donation_terms`, gs.`emergency_mode`, gs.`emergency_mode_msg`
        FROM `general_settings` gs
        WHERE gs.`id` = '1'
        LIMIT 1";

    return fetch_row($sql);

}

function fetchImportantPages()
{

    $sql = "SELECT ip.`imppage_name` AS name, pmd.`url`, pmd.`full_url`, pmd.`name` AS menu_name, pmd.`menu_label`,
    pmd.`title`, gp.`id` AS pg_id
    FROM `general_importantpages` ip
    LEFT JOIN `general_pages` gp
    ON(gp.`id` = ip.`page_id`)
    LEFT JOIN `page_meta_data` pmd
    ON(gp.`page_meta_data_id` = pmd.`id`)
    WHERE pmd.`status` = 'A'
    AND pmd.`url` != ''";

    $result = fetch_all($sql);

    $return_arr = array();

    foreach( $result as $key => $array )
    {

        $this_importantpage_url  = ($this_importantpage_name != '')  ? $array['url'] : 'home' ;

        $this_importantpage_name = strtolower(str_replace(' ','',$array['name']));

        $return_arr["impage_{$this_importantpage_name}"] = (object) array(
            'menu_label' => (($array['menu_label'])?$array['menu_label']:$array['menu_name']),
            'url' => $this_importantpage_url,
            'full_url' => $array['full_url'],
            'id' => $array['pg_id'],
            'title' => $array['title']
        );
    }



    return $return_arr;
}

$settings_arr = array_merge( fetchSettings(), fetchImportantPages() );

include "$incdir/formatSettings.php";

###############################################################################################################################
## Fetch Page Information
###############################################################################################################################

function get_content( $pg_meta_id )
{

    $output = '';
    
    if( $pg_meta_id )
    {

        $rows = fetch_all("SELECT `id` FROM `content_row` WHERE `page_meta_data_id` = '{$pg_meta_id}' ORDER BY `rank`");

        if( !empty($rows) )
        {
            foreach ($rows as $row)
            {
                $columns = fetch_all("SELECT `content`, `css_class` FROM `content_column` WHERE `content_row_id` = '{$row['id']}' ORDER BY `rank`");

                if( !empty($columns) )
                {
                    $output .= '<section class="section section--white-bg"><div class="container"><div class="row content-row">';
                    
                    foreach ($columns as $column)
                    {
                        $output .= '<div class="'.$column['css_class'].'">'.$column['content'].'</div>';
                    }
               
                    $output .= '</div></div></section>';
                }
            }
        }
    }

    return $output;
}


// function generate_content_from_obj( $obj, $tmpl_tags = array() )
// {
//     $html = '';
//     if(!empty($obj))
//     {
//         foreach($obj as $row)
//         {
//             if(!empty($row->columns))
//             {
//                 $html .= '<div class="row content-ro">';

//                 foreach ($row->columns as $column)
//                 {
//                     if(!empty($tmpl_tags))
//                     {
//                         foreach ($tmpl_tags as $tag => $value)
//                         {
//                             $column->content = str_replace('{'.$tag.'}', $value, $column->content);
//                         }
//                     }
//                     $html .= '<div class="'.$column->class.'">'.$column->content.'</div>';
//                 }

//                 $html .= '</div>';
//             }
//         }
//     }


//     return $html;
// }


function fetchPageInfo( $pg_url )
{

    global $settings_arr;

    $sql = "SELECT pmd.`id` AS page_meta_id, gp.`id` AS page_id, pmd.`menu_label`, pmd.`heading`, pmd.`sub_heading`, pmd.`introduction`, pmd.`url`, pmd.`full_url`, pmd.`description`, pmd.`short_description`, 
        pmd.`quicklink_heading`, pmd.`photo`, pmd.`thumb_photo`, pmd.`title`, pmd.`meta_description`, pmi.`value` AS mrobots, gp.`parent_id`, pmd.`slideshow_id`, pmd.`gallery_id`,
        gp.`template_id`, gp.`slideshow_type`,pmd.`og_title`, pmd.`og_meta_description`, pmd.`og_image`
        FROM `general_pages` gp
        LEFT JOIN `page_meta_data` pmd
        ON(pmd.`id` = gp.`page_meta_data_id`)
        LEFT JOIN `page_meta_index` pmi
        ON(pmi.`id` = pmd.`page_meta_index_id`)
        WHERE pmd.`url` = '{$pg_url}'
        AND pmd.`status` = 'A'
        LIMIT 1";


    $page_data = fetch_row($sql);

    if( !empty($page_data) )
    {
       $page_data['content'] = get_content( $page_data['page_meta_id'] );
    }

    return $page_data;
}

$page_home = $settings_arr['impage_home']->url;
$page_404  = $settings_arr['impage_404']->url;



$total_url_segments = count($uri_segments);
$ignore_urls        = array($settings_arr['impage_blog']->url, $settings_arr['impage_shopify']->url);
$current_full_url   = implode('/', $uri_segments);
$page_index         = 0;


if( empty($uri_segments) )
{
    $page_url = $page_home;
  
}
elseif($total_url_segments > 0)
{
  
    for ($i=($total_url_segments - 1); $i >=0 ; $i--)
    { 
        $segment = $uri_segments[$i];

        $page_url = fetch_value("SELECT pmd.`url`
            FROM `general_pages` gp
            LEFT JOIN `page_meta_data` pmd
            ON(pmd.`id` = gp.`page_meta_data_id`)
            WHERE pmd.`url` = '{$segment}'
            AND pmd.`status` = 'A'
            LIMIT 1");

        if( $page_url )
        {
            break;
        }
    }

    if( $page_url )
    {


        $page_index            = (array_search($page_url, $uri_segments) + 1);
        $page_options          = array_slice($uri_segments, $page_index);
        $page_options_full_url = implode('/', $page_options);


        $is_valid_url = fetch_value("SELECT `id` FROM `page_meta_data`
            WHERE (`full_url` = '/{$current_full_url}' OR `full_url` = '/{$page_options_full_url}')
            AND `status` = 'A'
            LIMIT 1");

        // echo $is_valid_url;
        // die();


        if( !$is_valid_url && !in_array($page_url, $ignore_urls))
        {
            
            $page_url = $page_404;
            header("HTTP/1.1 404 Not Found");
        }

    }
    else
    {
        $page_url = $page_404;
        header("HTTP/1.1 404 Not Found");
    }

}



$page_arr = fetchPageInfo($page_url);


###############################################################################################################################
## Page Insert Tags
###############################################################################################################################

$formatted_arr = array_merge($page_arr, $settings_arr);


$tags_arr = array();

// Page Inserts
$page_title                 = $tags_arr['title']          = $formatted_arr['title'];                                              ## Metatag Title >> inc_formattemp.php
$tags_arr['og_title']       = ($formatted_arr['og_title']) ? $formatted_arr['og_title'] : $formatted_arr['title'];                                              ## Metatag Title >> inc_formattemp.php
$tags_arr['og_mdescr']      = ($formatted_arr['og_meta_description']) ? $formatted_arr['og_meta_description'] : $formatted_arr['meta_description'];  
$tags_arr['mdescr']         = $formatted_arr['meta_description'];                                   ## Metatag Description
$mrobots                    = $tags_arr['mrobots']                    = $formatted_arr['mrobots'];      ## Metatag Robots >> inc_formattemp.php
$tags_arr['mauthor']        = 'Tomahawk';                                                           ## Metatag Author
$heading                    = $tags_arr['heading']                    = $formatted_arr['heading'];
$introduction               = $tags_arr['introduction']                = $formatted_arr['introduction'];
$short_description          = $tags_arr['short_description']                = $formatted_arr['short_description'];
$quicklink_heading          = $tags_arr['quicklink_heading']                = $formatted_arr['quicklink_heading'];
$sub_heading                = $tags_arr['sub_heading']                = $formatted_arr['sub_heading'];
$page_language_code         = $tags_arr['lang_iso_code']              = $formatted_arr['iso_code'];
$tags_arr['content']        = $formatted_arr['content'];                                            ## Page Content

$tags_arr['heading-view']  = ($heading) ? '<header class="section__header text-center"><div class="section__header__highlight section__header__highlight--red"></div><h1 class="section__heading section__heading--normal">'.$heading.'</h1></header>' : '';
$tags_arr['introduction-view']   = ($introduction) ? '<p class="text-center">'.$introduction.'</p>' : '';

// Company/Website Inserts
$company_name = $company                    = $tags_arr['company']                    = $formatted_arr['company_name'];             ## Company Name
$tags_arr['copyright']      = $formatted_arr['copyright'];                                                       ## e.g. Copyright 2007 - 2010. Company. >> inc_formattemp.php
$tags_arr['credits']        = $formatted_arr['credits'];                                                         ## e.g. Website design by Webdirectionz @ Tomahawk >> inc_formattemp.php
$booking_url                = $tags_arr['booking_url']                 = $formatted_arr['booking_url'];                
$company_email_address      = $tags_arr['company_email_address']      = $formatted_arr['email_address'];      ## Company email(s)
$phone_number               = $tags_arr['phone_number']               = $formatted_arr['phone_number'];
$free_phone_number          = $tags_arr['free_phone_number']               = $formatted_arr['free_phone_number'];
$skype_username             = $tags_arr['skype_username']               = $formatted_arr['skype_username'];
$company_address            = $tags_arr['company_address']            = nl2br($formatted_arr['address']);       ## Company address
$homepage_slideshow_caption = $tags_arr['homepage_slideshow_caption'] = $formatted_arr['homepage_slideshow_caption'];
$homepage_slideshow_url     = $tags_arr['homepage_slideshow_url'] = $formatted_arr['homepage_slideshow_url'];
$mailchimp_api_key          = $tags_arr['mailchimp_api_key'] = $formatted_arr['mailchimp_api_key'];
$mailchimp_list_id          = $tags_arr['mailchimp_list_id'] = $formatted_arr['mailchimp_list_id'];


$comp_emails                = get_email_list($company_email_address);
$primary_email              = $tags_arr['primary_email'] = $comp_emails->primaryEmail;


$adwards            = $tags_arr['adwards']            = $formatted_arr['adwords_code'];
$js_code_head_close = $tags_arr['js_code_head_close'] = $formatted_arr['js_code_head_close'];
$js_code_body_open  = $tags_arr['js_code_body_open']  = $formatted_arr['js_code_body_open'];
$js_code_body_close = $tags_arr['js_code_body_close'] = $formatted_arr['js_code_body_close'];


$tags_arr['root']      = $htmlroot;                                                                     ## For use to direct the template to the root of the website for css, js & image files
$tags_arr['fromroot']  = $fromroot;

// Code Variables                                                 ## Variables with information about the current page
$main_page_id         = $page_id                                = $formatted_arr['page_id'];              ## Page Id
$page_full_url        = $formatted_arr['full_url'];              ## Full Url
$page                 = $page_url                               = $formatted_arr['url'];
$template_id          = $formatted_arr['template_id'];          ## Template Id
$slideshow_type       = $formatted_arr['slideshow_type'];    

$page_parent_id       = $formatted_arr['parent_id'];            ## Page Parent Id
$absparent_id         = getAbsoluteParentId($page_id);          ## Absolute Parent Id
$slideshow_id         = $formatted_arr['slideshow_id'];         ## Slideshow Id
$gallery_id           = $formatted_arr['gallery_id'];           ## gallery Id
$page_photo           = ($formatted_arr['photo']) ? $formatted_arr['photo'] : '';
$page_thumb_photo     = ($formatted_arr['thumb_photo']) ? $formatted_arr['thumb_photo'] : '';
$page_photo_caption   = ($formatted_arr['photo_caption']) ? $formatted_arr['photo_caption'] : '';
$page_menu_label      = $formatted_arr['menu_label'];
$og_page_photo        = (is_file("{$rootfull}{$formatted_arr['og_image']}")) ? $formatted_arr['og_image'] : $page_photo;
$tags_arr['og_image'] = ($og_page_photo) ? "{$htmlroot}{$og_page_photo}" : '';
$tags_arr['og_url']   = "{$htmlroot}{$_SERVER['REQUEST_URI']}";

$donation_heading     = $formatted_arr['donation_heading'];
$donation_description = $formatted_arr['donation_description'];
$donation_terms       = $formatted_arr['donation_terms'];


###### Dynamically generated page segments/options ##########
$segment1 = ${"option{$page_index}"};
$segment2 = ${"option".($page_index+1)};
$segment3 = ${"option".($page_index+2)};
$segment4 = ${"option".($page_index+3)};

$number_of_module_tags   = fetch_value("SELECT tn.`tmpl_nummoduletags`
    FROM `templates_normal` tn
    WHERE tn.`tmpl_id` = '$template_id'");

// Important Pages
$page_home      = $formatted_arr['impage_home'];
$page_donations = $formatted_arr['impage_donations'];
$page_events    = $formatted_arr['impage_events'];
$page_stories   = $formatted_arr['impage_stories'];
$page_news      = $formatted_arr['impage_news'];
$page_contact   = $formatted_arr['impage_contact'];
$page_blog      = $formatted_arr['impage_blog'];
$page_team      = $formatted_arr['impage_team'];

// Initializing Empty Tags                                              ## Tags made for later use
$tags_arr['scripts-load-top'] = '';
$tags_arr['style-int']        = '';                                   ## Position held for internal styles
$tags_arr['style-ext']        = '';                                   ## Position held for external styles
$tags_arr['script-ext']       = '';                                   ## Position held for external scripts
$tags_arr['script-onload']    = '';                                   ## Position held for onload scripts
$tags_arr['module']           = '';
$tags_arr['body_cls']         = '';
$tags_arr['mod_view']         = '';
$tags_arr['script-inline']    = '';
$tags_arr['body_html']        = '';
$tags_arr['donation-view']    = '';
$tags_arr['donation-nav']     = '';
$tags_arr['stories-section']  = '';
$tags_arr['sponsors-section'] = '';
$tags_arr['contact-details']  = '';
$tags_arr['achievements']     = '';
$tags_arr['newsletter']       = '';
$tags_arr['team-view']        = '';
$tags_arr['modal']            = '';


// Template assets file paths
$tags_arr['favicon_path']   = get_file_path('/favicon.ico');
// $tags_arr['css_path']       = get_file_path('/assets/css/'.(($is_local) ? '_main_xl.css' : 'main.css'));
$tags_arr['css_path']       = get_file_path('/assets/css/main.css');
$tags_arr['modernizr_path'] = get_file_path('/assets/js/libs/min/modernizr-2.8.3.min.js');
$tags_arr['vender_js_path'] = get_file_path('/assets/js/libs/min/vendor.js');
$tags_arr['js_path']        = get_file_path('/assets/js/'.(($is_local) ? 'unmin/main.js' : 'min/main.js'));

$noindex_url_pattern = '/^\/news\/blog\/((\d{4})|author|archive|category).*/';

if( preg_match($noindex_url_pattern, $_SERVER['REQUEST_URI'])) {
    $tags_arr['ex_meta_taga'] = <<< H
        <meta name="robots" content="noindex">
H;
} else {
    $tags_arr['ex_meta_taga'] = <<< H
<meta name="robots" content="{$mrobots}">
\t<meta name="googlebot" content="{$mrobots}">
H;
}


$phone_icon_view = '';
if( $phone_number )
{
    $phone_icon_view = '<a href="tel:'.$phone_number.'" class="phone-trigger"><i class="glyphicons glyphicons-earphone"></i></a>';
    
}

if( $free_phone_number )
{
    $phone_icon_view .= '<span class="phone-trigger-md">Freecall: '.$free_phone_number.'<br>(within Australia)</span>';
    
}

$tags_arr['phone_icon_view'] = $phone_icon_view;

$newsletter_view = '';

if( $mailchimp_api_key &&  $mailchimp_api_key )
{
    $newsletter_view = '<div class="col-xs-12">
        <h3 class="serif">Get latest updates and offers from us</h3>
        <form action="#" id="news-signup-form" method="post">
            <div class="form-group">
                <input type="email" class="form-control" name="signup-email" id="signup-email" placeholder="Your email address" autocomplete="off">
            </div>
            <div class="form-group">
                <button type="submit" id="newsletter-submit" name="signup" value="singup" class="btn">Sign Up<i class="glyphicons glyphicons-chevron-right"></i></button>
            </div>
            <div class="clearfix"></div>
            <p class="msg"></p>
        </form>
    </div>';
}

$tags_arr['newsletter_view'] = $newsletter_view;

$fetch_stories = fetch_all("SELECT s.`date`, s.`page_meta_data_id`, pmd.`name`, pmd.`status`, pmd.`rank`, 
            pmd.`url`, pmd.`menu_label`, pmd.`heading`, pmd.`title`, pmd.`short_description`, pmd.`introduction`, pmd.`url`, pmd.`full_url`,
            pmd.`thumb_photo`
            FROM `stories` s
            LEFT JOIN `page_meta_data` pmd
            ON(s.`page_meta_data_id` = pmd.`id`)
            WHERE pmd.`status` = 'A'
            ORDER BY pmd.`status`, pmd.`rank`, s.`date` DESC
            LIMIT 1");

$fetch_news = fetch_all("SELECT n.`date`, n.`news_url`, n.`page_meta_data_id`, pmd.`name`, pmd.`status`, pmd.`rank`, 
            pmd.`url`, pmd.`menu_label`, pmd.`heading`, pmd.`title`, pmd.`short_description`, pmd.`introduction`, pmd.`url`, pmd.`full_url`,
            pmd.`thumb_photo`
            FROM `news` n
            LEFT JOIN `page_meta_data` pmd
            ON(n.`page_meta_data_id` = pmd.`id`)
            WHERE pmd.`status` = 'A'
            ORDER BY pmd.`status`, pmd.`rank`, n.`date`
            LIMIT 1");

if ($page == $page_home->url) {

    if (!empty($fetch_stories) || !empty($fetch_news)) {

        if(!empty($fetch_stories)){

            $story_content = '';

            foreach ($fetch_stories as $key => $story) {

                $introduction = (strlen($story['introduction']) >= 80) ? substr($story['introduction'], 0, 80).'...' : $story['introduction'];
                $story_content .= <<<H
                    <div class="col-md-6 section__article--outter">
                        <header class="section__header text-center">
                            <h2 class="section__heading section__heading--red">Latest Personal Stories</h2>
                            <div class="section__border section__border--dark"></div>
                        </header>
                        <article class="section__article section__article--news">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="section__article__img-outer section__article__img-outer--round" style="background-image: url({$story['thumb_photo']})">
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <h3 class="section__article__title">“{$story['heading']}”</h3>
                                    <p class="section__article__text">{$introduction}</p>                            
                                    <a href="{$page_stories->full_url}{$story['full_url']}" class="link link--red section__article__link">Read More</a>
                                </div>
                            </div>
                        </article>
                        <a href="{$page_stories->full_url}" class="btn btn--red btn--full-width">More Personal Stories</a>
                    </div>
H;
            }
            
        }
        if (!empty($fetch_news)) {
            $news_content = '';

            foreach ($fetch_news as $key => $news) {
                $news_content .= <<<H

                    <div class="col-md-6 section__article--outter">
                        <header class="section__header text-center">
                            <h2 class="section__heading section__heading--blue">Latest News</h2>
                            <div class="section__border section__border--dark"></div>
                        </header>
                        <article class="section__article section__article--news">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="section__article__img-outer section__article__img-outer--round" style="background-image: url({$news['thumb_photo']})">
                                       
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <h3 class="section__article__title">{$news['heading']}</h3>
                                    <p class="section__article__text">{$news['short_description']}
                </p>                            
                                    <a href="{$news['news_url']}" target="_blank" class="link section__article__link">Read More</a>
                                </div>
                            </div>
                        </article>
                        <a href="{$page_news->full_url}" class="btn btn--full-width">More News</a>
                    </div>
H;
            }
        }

        $tags_arr['stories-section'] .= <<<H

                <section class="section section--white-bg">
                    <div class="container">
                        <div class="row">
                           
                           {$story_content}

                           {$news_content}
                        </div>
                    </div>
                </section>

H;
    }
}


$tags_arr['newsletter'] = <<<H

    <h3 class="footer__heading">Join our Newsletter</h3>

    <div class="form-group">  
        <form action="" method="POST" id="news-signup-form">
            <input type="email" class="form-control form-control--no-border" id="signup-email" placeholder="Email">
            <button type="submit" class="btn btn--red btn--full-width" id="newsletter-submit" name="signup">Sign Up</button>
            <p class="msg text-center"></p>
        </form>
    </div>
    
H;


//Website 'Emergency / Maintenance Mode'

// $tags_arr['emergency_mode_msg_view'] = '';

// $emergency_mode     = $formatted_arr['emergency_mode'];

// $emergency_mode_msg = $tags_arr['emergency_mode_msg_view'] = $formatted_arr['emergency_mode_msg'];

// if ($emergency_mode == 1) 
// {
//     $template_id = 2;
//     $tags_arr['body_cls'] .= 'emergency-mode';
// }


?>