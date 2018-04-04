<?php

$ship_details_view = '';

if( $ship_id )
{

	$ship_details_view = '<div class="ship-overview">
	<div class="row">
		<div class="col-xs-12">
			<h2>'.$ship_label.'</h2>
		</div>
	</div>
	<div class="row">
		<div class="col-xs-12 col-sm-4">
			<p><img src="'.$ship_thumb_photo.'" alt="'.$ship_label.'"></p>
		</div>
		<div class="col-xs-12 col-sm-8">
			'.$ship_details.'
			<div class="table two-col">
				<h2>Key Facts</h2>
				'.(( $no_of_crew_members ) ? '<div> <div>Crew</div> <div>'.$no_of_crew_members.'</div> </div>' : '').
				(( $officers ) ? '<div> <div>Officers</div> <div>'.$officers.'</div> </div>' : '').
				(( $no_of_guests ) ? '<div> <div>Guests</div> <div>'.$no_of_guests.'</div> </div>' : '').
				(( $tonnage ) ? '<div> <div>Tonnage</div> <div>'.$tonnage.'</div> </div>' : '').
				(( $feet_length ) ? '<div> <div>Length</div> <div>'.$feet_length.' Feet / '.$metre_length.' Metres</div> </div>' : '').
				(( $feet_width ) ? '<div> <div>Width</div> <div>'.$feet_width.' Feet</div> </div>' : '').
				(( $knot_speed ) ? '<div> <div>Speed</div> <div>'.$knot_speed.' Knots</div> </div>' : '').
				(( $no_of_pax_deck ) ? '<div> <div>Passenger Decks</div> <div>'.$no_of_pax_deck.'</div> </div>' : '').
				(( $year_built ) ? '<div> <div>Built</div> <div>'.$year_built.'</div> </div>' : '').
				(( $registry ) ? '<div> <div>Registry</div> <div>'.$registry.'</div> </div>' : '').'
			</div>
		</div>
	</div>
</div>';


}

?>