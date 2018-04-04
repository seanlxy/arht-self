<?php


$tags_arr['heading'] = 'Your Search Results';

$search_results_view = '<div class="container" id="sort-panel">
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
</div>';


$jsVars['data']['listAction']  = "action=search-results&".http_build_query($_SESSION['search_data']);
$jsVars['templates']['cruise'] = file_get_contents("{$tmpldir}/underscore/cruise.tmpl");

$tags_arr['script-ext'] .= '<script async src="'.get_file_path('/assets/js/cruise.js').'"></script>';


$tags_arr['mod_view'] .= $search_results_view;

$tags_arr['content'] = '<div class="row content-row hidden" id="msg-holder"><div class="col-xs-12"><h5 class="msg text-center"></h5></div></div>';

?>