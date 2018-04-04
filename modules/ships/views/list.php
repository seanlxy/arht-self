<?php

## variable $ships_list defined in /includes/components/nav/header.php on line 13

if( !empty($ships_list) )
{

require_once "{$classdir}/pagination.php";

$total_ships = fetch_value("SELECT COUNT(s.`id`)
    FROM `ship` s
    LEFT JOIN `page_meta_data` pmd
    ON(pmd.`id` = s.`page_meta_data_id`)
    WHERE pmd.`status` = 'A'");


$pgn_config = array();

$pgn_config['base_url']          = $page_ships->full_url;
$pgn_config['per_page']          = SHIPS_PER_PAGE;
$pgn_config['total_rows']        = $total_ships;
$pgn_config['page_query_string'] = '#';
$pgn_config['query_string']      = 'page';
$pgn_config['anchor_class']      = '';


$obj_pagination   = new Pagination( $pgn_config );

$pagination_links = $obj_pagination->generate_links();


$ships_view = '<div class="row">
    <div class="col-xs-12">
		<div class="grid-wrapper square">
		    <header><p class="h2 intro line"><span>Find your Ship</span></p></header>
		    <script id="ship-list-tmpl" type="text/html">
		    	<% if(ships.length > 0){ %>
		    	<% _.each(ships, function(ship){ %>
				<div class="col">
		            <a href="<%= ship.uri %>" style="background-image:url(<%= ship.thumb %>);" title="<%= ship.title %>">
		                <span class="inner">
		                    <span class="name"><%= ship.label %></span>
		                    <span class="descr"><%= ship.details %></span>
		                    <span class="btn">explore</span>
		                </span>
		            </a>
		        </div>
		        <% }) %>
		        <% } %>
		    </script>
		    <div class="grid" id="ship-list"></div><!-- /.grid -->
		</div><!-- /.grid-wrapper.square -->
		'.(($pagination_links) ? '
			<div class="col-xs-12">
				<div class="pagination-wrap">
					<span>View more</span>
					'.$pagination_links.'
				</div>
			</div>' : '').'
	</div>
</div>';

}

$tags_arr['content'] .= $ships_view;


?>