<?php

$room_grades_view         = '';
$booking_room_grades_view = '';

if( $suites_count > 0 )
{

	$room_grades_view = '<script type="text/html" id="tmpl-room-grade">
	<% if( roomGrades.length > 0 ){ %>
	<% _.each(roomGrades, function(roomGrade){ rate = ((roomGrade.rate) ? roomGrade.rate : roomGrade.mainRate) %>
	<div class="room">
		<div class="row">
			<div class="col-xs-12">
				<h3><%= roomGrade.label %></h3>
			</div>
		</div>
		<div class="row">
			<div class="col-xs-12 col-sm-6 col-md-7">
				<div class="cruise-detail">
					<p><span>SIZE:<span><%= roomGrade.sizeSqMetre %> m2 / <%= roomGrade.sizeSqFeet %> sqft</span></span><span>SLEEPS:<span><%= roomGrade.noOfSleeps %></span></span></p>
				</div>
				<a href="#" data-token="<%= roomGrade.token %>" data-type="grade" class="btn toggle-book<%= ((roomGrade.isShortlisted) ? " added" : "") %>"><%= ((roomGrade.isShortlisted) ? "remove" : "request booking") %></a>
				<p class="descr"><%= roomGrade.details %></p>
				<div class="price-detail">
					<span class="custom-dd" data-type="room-grades" data-selection="'.$currency_code.'" data-rate="<%= rate %>">
						<i class="glyphicons glyphicons-chevron-down"></i><span class="current">'.$currency_code.'</span>
						<ul>
							'.$currency_list.'
						</ul>
					</span>
					<span class="price"><small>'.$currency_symbol.'</small><span class="val"><%= rate %></span></span>
					<span class="text-uppercase"><%= roomGrade.pricingNotes %></span> 
					<span>(<span class="sel-currency">Indicative pricing in <span>NZD</span>. </span>Cruise sold in '.$currency_code.')</span>
				</div>
			</div>
			<div class="col-xs-12 col-sm-6 col-md-5">
				<div class="slider">
					<% if( roomGrade.totalPhotos > 0 ) { navIsHidden = ( ( roomGrade.totalPhotos <= 1 ) ? " hidden" : "" ) %>
					<ul data-gallery="<%= roomGrade.galleryInd %>">
						<li class="current" style="background-image:url(<%= roomGrade.mainPhoto %>);" data-index="0"></li>
					</ul>
					<a href="#" class="slider-nav prev<%=navIsHidden %>"><i class="glyphicons glyphicons-chevron-left"></i></a>
					<a href="#" class="slider-nav next<%=navIsHidden %>"><i class="glyphicons glyphicons-chevron-right"></i></a>
					<% } %>
				</div>
			</div>
		</div>
	</div>
	<% }) %>
	<% } %>
	</script>';

	$room_grades_view .= '<div class="inner" id="room-grades-container" data-token="'.$ship_token.'"></div>';

}

?>