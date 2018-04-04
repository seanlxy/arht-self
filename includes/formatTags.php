<?php

############################################################################################################################
## Format the Settings Array (input:$settings_arr) into useful settings (output:$settings_arr)
############################################################################################################################

    # WEBSITE PAGE TITLE
    $pagetitle = trim($page_arr['page_title']);
    $websitetitle = trim($settings_arr['set_websitetitle']);
    if($websitetitle != ''){                                                    ## If the client had put in a website title for every page,
        $page_arr['page_title'] = $pagetitle.' | '.$websitetitle;               ## ... then add that wesbsite title to ever meta title with a preceeding ' | '
    }

    # RENAME CACHE
        switch ($page_arr['page_mcache']) {
            case 1:     $page_arr['page_mcache'] = "public";             break;
            case 2:     $page_arr['page_mcache'] = "private";            break;
            case 3:     $page_arr['page_mcache'] = "no-cache";           break;
            case 4:     $page_arr['page_mcache'] = "no-store";           break;
        }

    # RENAME ROBOTS
        // switch ($page_arr['page_mrobots']) {
        //     case 1:     $page_arr['page_mrobots'] = "all";               break;
        //     case 2:     $page_arr['page_mrobots'] = "none";              break;
        //     case 3:     $page_arr['page_mrobots'] = "noindex, follow";   break;
        //     case 4:     $page_arr['page_mrobots'] = "index, nofollow";   break;
        //     case 5:     $page_arr['page_mrobots'] = "noarchive";         break;
        // }
# Fetch the URLs for all of the important pages
    function fetchPageUrls(){
        global $settings_arr;
        $sql = "SELECT gp.page_url, imppage_name, ip.page_id
                FROM general_importantpages ip
                LEFT JOIN general_pages gp ON ip.page_id = gp.page_id";
        $imppages = fetch_all($sql);
        foreach($imppages as $key => $array){
            $settings_arr['imppage_'.strtolower($array['imppage_name'])] = $array['page_url'];
            ${'page_'.$array['imppage_name']} = $htmlroot.'/'.$array['page_url'];
        }
    }
    
     

    fetchPageUrls();

?>