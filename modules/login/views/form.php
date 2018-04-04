<?php


if( $is_logged_in && $page_id == $page_login->id )
{
	header("Location: {$htmlroot}{$page_agent_welcome->full_url}");
	exit();
}
elseif( $page_id == $page_login->id )
{

$output_view = <<< H

<div class="container">
	<div class="row">
		<div class="col-xs-12 col-sm-6 col-sm-offset-3 col-lg-4 col-lg-offset-4">
			<form action="{$page_login->full_url}" method="post" accept-charset="utf-8" class="form-login">
				{$error_msg}
				<div class="form-group">
			        <label for="username" class="control-label">Username <span class="text-danger">*</span></label>
			        <input autocomplete="off" type="text" id="username" class="form-control" name="ag-username" tabindex="1" required>
			    </div>
			    <div class="form-group">
			        <label for="password" class="control-label">Password <span class="text-danger">*</span></label>
			        <input autocomplete="off" type="password" id="password" class="form-control" name="ag-password" tabindex="2" required>
			    </div>
			    <div class="form-group">
			        <button type="submit" name="login" value="1" class="btn">Login</button>
			    </div>
			</form>
		</div>
	</div>
</div>

H;
}
?>