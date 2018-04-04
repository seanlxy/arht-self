<?php

$deck_plan_view = '';

if( is_file("{$rootfull}{$deck_plan_path}") )
{
	$deck_plan_view = '<img src="'.$deck_plan_path.'" alt="'.$heading.' Floorplan" />';
}

?>