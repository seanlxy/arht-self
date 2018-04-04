<?php

############################################################################################################################
## Save page positions
############################################################################################################################

function save_ranking () {
	
	global $message,$testm_id,$testm_rank,$htmladmin,$tbl_name;

		$testm_id   = $_REQUEST['testm_id'];
		$testm_rank = $_REQUEST['testm_rank'];

		for($i=0;$i<=count($testm_id);$i++){
			
			$temp_array_ranking ['rank'] = $testm_rank[$i];
			$end = "WHERE id='".$testm_id[$i]."'";
			update_row($temp_array_ranking,$tbl_name,$end);
		}

		$message = "Item ranking has been saved";
}

?>
