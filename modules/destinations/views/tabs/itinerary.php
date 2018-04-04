<?php

$itinerary_view = '';

if( $total_periods > 0 && $total_itinerary_days > 0 )
{
	$departure_years = fetch_all("SELECT DISTINCT YEAR(`start_date`) AS date_year
		FROM `cruise_itinerary_departure`
		WHERE `cruise_id` = '{$main_cruise_id}'
		AND YEAR(`start_date`) >= YEAR(CURRENT_DATE())
		ORDER BY `start_date`");


	$departure_years_opts = '';


	if( !empty($departure_years) )
	{
		
		foreach ($departure_years as $departure_year)
		{
			$departure_years_opts .= '<option value="'.$departure_year['date_year'].'">'.$departure_year['date_year'].'</option>';
		}

		$itinerary_view = '<div class="depart-wrap">
			<div class="row">
				<p class="col-xs-12">We have multiple departure dates for this cruise. Select a year and month to view itineraries for alternative departure dates.</p>
			</div>
			<div class="row">
				<div class="col-xs-12">
					<span class="label">SELECT MONTH:</span>
					<script id="sel-option-tmpl" type="text/html">
						<% if(options.length > 0) { %>
						<% _.each(options, function(option){ %>
						<option value="<%= option.value %>"><%= option.label %></option>
						<% }) %>
						<% } %>
					</script>
					<select name="itin-month" id="itin-month" class="form-control light"></select>
					<select name="itin-year" id="itin-year" class="form-control light">
						'.$departure_years_opts.'
					</select>
					<a href="#" class="btn olight" id="departure-dates-trigger">Show departures</a>
				</div>
			</div>
			<div class="row">
				<div class="col-xs-12">
					<div class="date-bar" style="display:none">
						<span class="label"><span class="month"></span> <span class="year"></span> departures:</span>
						<script id="tmpl-depdate-btn" type="text/html">
							<% if(departures.length > 0) { %>
							<% _.each(departures, function(departure){ %>
							<li><a href="#" data-token="<%= departure.token %>" data-date="<%= departure.rdate %>"><%= ( (departure.onSpecial ==  "1") ? \'<i class="glyphicons glyphicons-star"></i>\' : "") %><%= departure.label %></a></li>
							<% }) %>
							<% } %>
						</script>
						<ul class="dates" id="departure-dates"></ul>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-xs-12">
					<div class="price-detail" style="display:none">FROM
						<span class="custom-dd" data-type="cruise-itin" data-selection="'.$currency_code.'" data-token="">
							<i class="glyphicons glyphicons-chevron-down"></i><span class="current">'.$currency_code.'</span>
							<ul>
								'.$currency_list.'
							</ul>
						</span>
						<span class="price"><small>$</small><span class="val"></span></span>
						<span class="text-uppercase">Per Person</span> 
						<span>(<span class="sel-currency">Indicative pricing in <span>NZD</span>. </span>Sold in '.$currency_code.')</span>
					</div>
					<span class="spec">
						<i class="glyphicons glyphicons-star"></i>Special
					</span>
				</div>
			</div>
		</div>';


		$itinerary_view .= '<div class="itin-table" style="display:none">
			<div class="row">
				<div class="col-xs-12">
					<header>
						<div class="row">
							<div class="col-xs-3 col-sm-2">
								<span>DATE</span>
							</div>
							<div class="hidden-xs col-sm-2">
								<span>DAY</span>
							</div>
							<div class="col-xs-5 col-sm-4 col-md-3">
								<span>PORT</span>
							</div>
							<div class="col-xs-4 col-sm-2">
								<span class="visible-xs">ARR | DEP</span>
								<span class="hidden-xs">ARRIVE</span>
							</div>
							<div class="col-xs-3 col-sm-2 hidden-xs">
								<span>DEPART</span>
							</div>
							<div class="hidden-xs hidden-sm col-md-1">
								<span class="toggle-all">
									<span>Expand</span>
									<span>Collapse</span> All <i class="glyphicons glyphicons-chevron-down"></i>
								</span>
							</div>
						</div>
					</header>
				</div>
			</div>
			<div class="row">
				<script id="itni-day-tmpl" type="text/html">
					<% if( days.length > 0 ) { %>
					<% _.each(days, function(day, i){ %>
					<div class="table-row">
						<div class="row">
							<% dateArr = day.rdate.split(",") %>
							<div class="col-xs-3 col-sm-2 cell">
								<span><%= dateArr[1] %></span>
							</div>
							<div class="hidden-xs col-sm-2 cell">
								<span><%= dateArr[0] %></span>
							</div>
							<div class="col-xs-5 col-sm-4 col-md-3 cell">
								<span><%= day.portName %></span>
							</div>
							<div class="col-xs-4 col-sm-2 cell">
								<span class="visible-xs"><%= day.arrivalTime %> | <%= day.departureTime %></span>
								<span class="hidden-xs"><%= (( day.arrivalTime != "N/A" ) ? day.arrivalTime : "") %></span>
							</div>
							<div class="col-xs-3 hidden-xs col-sm-2 cell">
								<span><%= (( day.departureTime != "N/A" ) ? day.departureTime : "") %></span>
							</div>
							<div class="col-xs-12 col-md-1 cell">
								<a href="#" class="toggle">show more<i class="glyphicons glyphicons-chevron-down"></i></a>
							</div>
							<div class="col-xs-12 cell extra">
								<div class="row">
									<div class="col-xs-12 col-sm-4 hidden-xs">
										<img src="<%= day.photo %>" alt="<%= day.title %>" />
									</div>
									<div class="col-xs-12 col-sm-8">
										<h4><%= day.title %></h4>
										<p><%= day.details %></p>
									</div>
								</div>
							</div>
						</div>
					</div>
					<% }) %>
					<% } %>
				</script>
				<div class="col-xs-12" id="itni-days"></div>
			</div>
		</div>';

	}

}

?>