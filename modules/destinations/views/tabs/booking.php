<?php

$booking_view = '';

$departure_dates = fetch_all("SELECT `public_token`, DATE_FORMAT(`start_date`, '%e %b %Y') AS date_label,
	`start_date`
	FROM `cruise_itinerary_departure`
	WHERE `cruise_id` = '{$main_cruise_id}'
	AND `start_date` >= CURRENT_DATE()
	ORDER BY `start_date`");


if( !empty($departure_dates) )
{

	$sel_departure_dates_view = '';

	foreach ($departure_dates as $departure_date)
	{
		$sel_departure_dates_view .= '<option value="'.$departure_date['public_token'].'" data-date="'.$departure_date['start_date'].'">'.$departure_date['date_label'].'</option>';
	}


$booking_view = <<< H

<div class="book-wrap">

	<div class="row">
		<div class="col-xs-12 col-sm-6">
			<h3>Book your next cruise</h3>
		</div>
		<div class="col-xs-12 col-sm-6">
			<p class="note">Please note: This cruise is sold in {$currency_code}</p>
		</div>
		<div class="col-xs-12">
			{$booking_notes}
		</div>
	</div>
	
	<form id="cruise-booking-form" action="#" method="post">
		<div class="row">
			<div class="col-xs-12">
				<h4>CRUISE SELECTION</h4>
				<div class="row">
					<div class="col-xs-4 col-sm-3 col-md-2">
						<label>Cruise:</label>
					</div>
					<div class="col-xs-8 col-sm-9 col-md-10">
						<span>{$heading}</span>
					</div>
				</div>
				<div class="row">
					<div class="col-xs-4 col-sm-3 col-md-2">
						<label for="sel-departure-date">Departure Date:</label>
					</div>
					<div class="col-xs-8 col-sm-9 col-md-10">
						<div class="invisible-sel">
							<select name="sel-departure-date" id="sel-departure-date">
								{$sel_departure_dates_view}
							</select>
							<label for="sel-departure-date" class="btn olight sml tag">Change</label>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-xs-12">
				<h4>ROOM GRADE SELECTION</h4>
				<script id="tmpl-sel-room-grade" type="text/html">
					<% if(roomGrades.length > 0) { %>
					<% _.each(roomGrades, function(roomGrade, i){ %>
					<div class="row booking-sel<%= ((roomGrade.isShortlisted) ? " added" : "") %>">
						<div class="col-xs-12 col-sm-5 col-lg-4">
							<label class="custom-check">
								<input type="checkbox" name="booking-room-grade[]" value="<%= roomGrade.token %>"<%= ((roomGrade.isShortlisted
) ? ' checked="checked"' : '') %>>
								<span></span>
								<%= roomGrade.label %>
							</label>
						</div>
						<div class="col-xs-12 col-sm-7 col-lg-8">
							<div class="price-detail">
								{$currency_code}
								<span class="price"><small>{$currency_symbol}</small><span class="val"><%= ((roomGrade.rate) ? roomGrade.rate : roomGrade.mainRate) %></span></span>
								<span class="text-uppercase"><%= roomGrade.pricingNotes %></span> 
								<span>(<span class="sel-currency">Indicative pricing in <span>NZD</span>. </span>Cruise sold in {$currency_code})</span>
							</div>
						</div>
					</div>
					<% }) %>
					<% } %>
				</script>
				<div id="booking-room-grades"></div>
			</div>
		</div>
		<div class="row">
			<div class="col-xs-12">
				<h4>ADDITIONAL OPTIONS</h4>
				{$options_booking_view}
			</div>
		</div>
		<div class="row">
			<div class="col-xs-12 pass-detail">
				<h4>BOOKING AND PASSENGER DETAILS</h4>
				<input type="hidden" name="cruise-token" value="{$cruise_token}">
				<div class="row">
					<div class="col-xs-12 col-sm-6 col-md-3">
						<label for="first-name-0">First Name:</label>
					</div>
					<div class="col-xs-12 col-sm-6 col-md-4">
						<input type="text" class="form-control light" id="first-name-0" name="first-name[]" required>
						<span class="required">*</span>
					</div>
				</div>
				<div class="row">
					<div class="col-xs-12 col-sm-6 col-md-3">
						<label for="last-name-0">Last Name:</label>
					</div>
					<div class="col-xs-12 col-sm-6 col-md-4">
						<input type="text" class="form-control light" id="last-name-0" name="last-name[]" required>
						<span class="required">*</span>
						<p class="error"></p>
					</div>
				</div>
				<div class="row">
					<div class="col-xs-12 col-sm-6 col-md-3">
						<label for="email-address">Email Address:</label>
					</div>
					<div class="col-xs-12 col-sm-6 col-md-4">
						<input type="email" class="form-control light" id="email-address" name="email-address" required>
						<span class="required">*</span>
					</div>
				</div>
				<div class="row">
					<div class="col-xs-12 col-sm-6 col-md-3">
						<label for="phone-number">Phone Number:</label>
					</div>
					<div class="col-xs-12 col-sm-6 col-md-4">
						<input type="text" class="form-control light" id="phone-number" name="phone-number" required>
						<span class="required">*</span>
					</div>
				</div>
				<div class="row">
					<div class="col-xs-12 col-sm-6 col-md-3">
						<label for="total-pax">Total # of Passengers:</label>
					</div>
					<div class="col-xs-12 col-sm-6 col-md-4">
						<input type="number" class="form-control light sml" id="total-pax" name="total-pax" min="1" value="1" required>
						<span class="required">*</span>
					</div>
				</div>
				<div id="extra-pax-wrap" style="display:none;">
					<script id="ex-pax-tmpl" type="text/html">
						<div class="ex-pax" id="ex-pax-<%= i %>">
							<div class="row">
								<div class="col-xs-12 col-sm-6 col-md-3">
									<label for="first-name-<%= i %>">First Name:</label>
								</div>
								<div class="col-xs-12 col-sm-6 col-md-4">
									<input type="text" class="form-control light" id="first-name-<%= i %>" name="first-name[]" required>
									<span class="required">*</span>
								</div>
							</div>
							<div class="row">
								<div class="col-xs-12 col-sm-6 col-md-3">
									<label for="last-name-<%= i %>">Last Name:</label>
								</div>
								<div class="col-xs-12 col-sm-6 col-md-4">
									<input type="text" class="form-control light" id="last-name-<%= i %>" name="last-name[]" required>
									<span class="required">*</span>
								</div>
							</div>
						</div>
					</script>
					<h5>ADDITIONAL PASSENGER DETAILS</h5>
					<div id="ex-pax-holder"></div>
				</div>
				<div class="row">
					<div class="col-xs-12 col-sm-6 col-md-3">
						<label for="message">Message:</label>
					</div>
					<div class="col-xs-12 col-sm-6 col-md-4">
						<textarea name="message" id="message" class="form-control light"></textarea>
					</div>
				</div>
				<div class="row">
					<div class="col-xs-12 col-sm-6 col-md-3">
						<label>Please provide me with information regarding your recommended travel insurance policy:</label>
					</div>
					<div class="col-xs-12 col-sm-6 col-md-4">
						<label class="radio-inline"><input type="radio" name="insurance" value="1"> Yes</label>
						<label class="radio-inline"><input type="radio" name="insurance" value="0" checked="checked"> No</label>
					</div>
				</div>
				<div class="row">
					<div class="col-xs-12 col-sm-6 col-md-3">
						
					</div>
					<div class="col-xs-12 col-sm-6 col-md-4">
						<button type="submit" id="do-booking" name="do-booking" value="1" class="btn olight">SUBMIT BOOKING REQUEST</button>
					</div>
				</div>
			</div>
		</div>
	</form>
</div>

<div id="booking-msg" class="alert" style="display:none;"></div>


H;


}

?>