<?php

include_once "{$classdir}/mobile_detect.php";

if( !function_exists('main_navigation') )
{
 	function main_navigation($parent = 0, $current_page = '')
    {

    	global $max_pages, $max_pages_generation, $page_arr, $rootfull, $page_shortlist,
            $shortlist_count, $destinations_list, $travel_styles_list, $ships_list, $segment1, $segment2, $page_home;

        static $level = 1;

    	$sql = "SELECT gp.`id` AS page_id, pmd.`menu_label`, pmd.`title`, pmd.`full_url`, pmd.`url`, gp.`parent_id`,
        (SELECT GROUP_CONCAT(`mod_id`) FROM `module_pages` WHERE `page_id` = gp.`id`) AS page_mods
        FROM general_pages gp
        LEFT JOIN `page_meta_data` pmd
        ON(pmd.`id` = gp.`page_meta_data_id`)
        WHERE pmd.`status` = 'A'
        AND gp.`parent_id` = '$parent'
        AND (pmd.`menu_label` != '')
        ORDER BY pmd.`rank` ASC";

		$pages  = fetch_all($sql);

		$html   = '';

    	if( !empty($pages) )
    	{
            $level++;
    		foreach ($pages as $page)
    		{

                $page_id       = $page['page_id'];
                $menu_heading  = $page['menu_heading'];
                $page_mods_arr = explode(',', $page['page_mods']);
                $item_cls      = ($page['url'] === $current_page || $page_id === $page_arr['parent_id']) ? 'active' : '';
                $url           = $page['full_url'];
                $page_menu     = $page['menu_label'];

                $sub_menu = '';
                $icon     = '';
                $home_cls = '';

                $sub_menu .= main_navigation($page_id, $current_page );


                if( $sub_menu )
                {
                    $icon = '<i class="fa fa-chevron-down toggle-snav hidden-md hidden-lg"></i>';
                    $item_cls .= ' has-menu';
                }
                
                if($item_cls) $item_cls = trim($item_cls);
                
		    	$html .= '<li class="main-menu__list__item  '.$item_cls.'"'.(($page_shortlist->id === $page_id) ? ' id="shortlist-counter"'.(($shortlist_count > 0) ? ' data-label="'.$shortlist_count.'"' : '') : '').'>';
                $html .= '<a class="menu__list__item__link" href="'.$url.'" title="'.$page['title'].'">'.$page_menu.'</a>';
                $html .= $icon;
		    	$html .= ($sub_menu) ? '<ul class="sub-menu">'.$sub_menu.'</ul>' : '';
		    	$html .= '</li>';


    		}
            $level--;

    	}

    	return $html;
    }
}


$menu = main_navigation(0, $page);

$tags_arr['nav-main'] = ($menu) ? '<ul class="main-menu__list">'.$menu.'</ul>' : '';



?>