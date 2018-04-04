<?php
## variable $destinations_list defined in /includes/components/nav/header.php on line 5

$destinations_list_view = '';

$destinations_opts = fetch_all("SELECT d.`public_token`, pmd.`menu_label`
    FROM `destination` d
    LEFT JOIN `page_meta_data` pmd
    ON(pmd.`id` = d.`page_meta_data_id`)
    WHERE pmd.`status` = 'A'
    ORDER BY pmd.`menu_label`");

if( !empty($destinations_opts) )
{
	foreach ($destinations_opts as $destinations_opt)
	{

        $dis_selected = ( $searched_destination_id == $destinations_opt['public_token'] ) ? ' selected="selected"' : '';

		$destinations_list_view .= '<option value="'.$destinations_opt['public_token'].'"'.$dis_selected.'>'.$destinations_opt['menu_label'].'</option>';
	}
}

$search_panel_view = '';

$search_panel_view = '<div id="search-panel">
    <form action="'.$page_search->full_url.'" method="post">
        <div class="form-group close-wrap">
            <a href="#search-panel" class="toggle-elm close"><i class="glyphicons glyphicons-remove-2"></i></a>
        </div>

        <div class="form-group">
            <label class="sr-only" for="destination">Destination</label>
            <div class="input-group">
                <div class="select-wrap">
                    <select name="destination" id="destination" class="form-control input-sm">
                        <option value="">Destination</option>
                        '.$destinations_list_view.'
                    </select>
                    <span class="addon"><i class="glyphicons glyphicons-chevron-down"></i></span>
                </div>
            </div>
        </div>

        <div class="form-group">
            <label class="sr-only" for="departure-date">Departure</label>
            <div class="input-group">
                <input type="text" class="form-control input-sm" id="departure-date" name="departure" placeholder="Departure" value="'.$searched_date.'">
                <label class="input-group-addon" for="departure-date"><i class="glyphicons glyphicons-calendar"></i></label>
            </div>
        </div>

        <button type="submit" name="do-search" value="1" class="btn olight bdr text-uppercase">Search</button>

        <div class="clearfix"></div>
    </form>

</div><!-- /#search-panel -->';


$tags_arr['search_panel_view'] = $search_panel_view;

?>