<?php

if( $destination_public_token && $total_cruises > 0)
{



require_once "{$classdir}/pagination.php";


$pgn_config = array();

$pgn_config['base_url']          = $destination_full_url;
$pgn_config['per_page']          = MAX_LIST_ITEMS;
$pgn_config['total_rows']        = $total_cruises;
$pgn_config['page_query_string'] = '#';
$pgn_config['query_string']      = 'page';
$pgn_config['anchor_class']      = '';


$obj_pagination   = new Pagination( $pgn_config );

$pagination_links = $obj_pagination->generate_links();

$destinations_view = '<div class="container">
	<div class="row">
		<div class="col-xs-12">
			<div class="sort-bar text-center">
				<label>Sort By</label>
				<select name="sort-by" id="sort-by" class="form-control light">
					<option value="featured:desc">Featured cruises</option>
					<option value="rate:desc">Price (high to low)</option>
					<option value="rate:asc">Price (low to high)</option>
					<option value="duration:desc">Duration (most to least)</option>
					<option value="duration:asc">Duration (least to most)</option>
					<option value="date:desc">Latest listing</option>
				</select>
			</div>
		</div>
	</div>
</div>
<div class="list-view" id="cruise-listing">
	<div id="item-list-grid"></div>

	'.(($pagination_links) ? '
	<div class="container">
		<div class="row">
			<div class="col-xs-12">
				<div class="pagination-wrap">
					<span>View more</span>
					'.$pagination_links.'
				</div>
			</div>
		</div>
	</div>' : '').'

</div>';


$jsVars['data']['listAction']  = "action=fetch-list&dst={$destination_public_token}";
$jsVars['templates']['cruise'] = file_get_contents("{$tmpldir}/underscore/cruise.tmpl");

$tags_arr['script-ext'] .= '<script async src="'.get_file_path('/assets/js/cruise.js').'"></script>';

}


$tags_arr['mod_view'] .= $destinations_view;

?>