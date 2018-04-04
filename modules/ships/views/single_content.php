<?php

$tags_arr['content'] = '<div class="ship-overview">
	<div class="row">
		<div class="col-xs-12 col-sm-6">
			<h2>Ship Overview</h2>
			'.$description.'
			<p><a href="#cruise-listing" class="btn scroll-to">View expeditions</a></p>
		</div>
		<div class="col-xs-12 col-sm-6">
			<div class="table">
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

?>