<?php 

require_once 'inc/vars.php';

if(isset($_POST['submit']) && $form_is_valid == TRUE)
{	
	require_once 'inc/insert_data.php';
}
elseif( isset($_GET['success']) )
{
	require_once 'inc/views/success.php';
}
else
{
	require_once 'inc/views/form.php';
}

if($segment1 == 'leave-a-legacy'){

	$tags_arr['content'] = $tags_arr['content'];
 
}else{
	$tags_arr['content'] = $output;
}
	
?>