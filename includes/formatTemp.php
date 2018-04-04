<?php
#### File: inc_formattemp.php
############################################################################################################################
## Format the page/settings array keys into useful page items
############################################################################################################################

        $startyear                                      = $formatted_arr['set_startyear'];
        $thisyear                                       = date('Y');
        $thisyear > $startyear ? $year =("$startyear - $thisyear") : ($year = $thisyear);
        $company                                        = $formatted_arr['set_company'];

echo $startyear;

    # COPYRIGHT
        $formatted_arr['set_copyright']                      =<<<HTML
        &copy; Copyright $thisyear.  $websitename.
HTML;

    

    # RENAME CACHE
    switch ($formatted_arr['page_cache']) {
        case 1:     $formatted_arr['page_cache'] = "public";             break;
        case 2:     $formatted_arr['page_cache'] = "private";            break;
        case 3:     $formatted_arr['page_cache'] = "no-cache";           break;
        case 4:     $formatted_arr['page_cache'] = "no-store";           break;
        default:    $formatted_arr['page_cache'] = "public";             break;
    }

    # RENAME ROBOTS
    switch ($formatted_arr['page_robots']) {
        case 1:     $formatted_arr['page_robots'] = "all";               break;
        case 2:     $formatted_arr['page_robots'] = "none";              break;
        case 3:     $formatted_arr['page_robots'] = "noindex, follow";   break;
        case 4:     $formatted_arr['page_robots'] = "index, nofollow";   break;
        case 5:     $formatted_arr['page_robots'] = "noarchive";         break;
        default:    $formatted_arr['page_robots'] = "all";               break;
    }

    # Fetch the URLs for all of the important pages
    function fetchPageUrls(){
        $page_id_settings = $formatted_arr["set_page$pagename"];
        $sql = "SELECT page_url, imppage_name
                FROM general_importantpages ip, general_pages gp
                WHERE gp.page_id = ip.page_id";
        foreach(fetch_all($sql) as $key => $array){
            $formatted_arr['imppage_'.$array['imppage_name']] = $page_url;
        }
    }

    fetchPageUrls();




?>