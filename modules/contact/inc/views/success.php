<?php 

// $hashed_enquiry_id = $_GET['success'];

// $is_enquiry = fetch_value("SELECT `id` FROM `enquiry` WHERE MD5(`id`) = '{$hashed_enquiry_id}' LIMIT 1");
// if($is_enquiry)
// {

//     $tags_arr['heading'] = 'Success!';

// $tags_arr['content'] = '<div class="row">
//     <div class="col-xs-12" style="text-align:center;">
// 	    <p class="text-success">Thank you for your enquiry. We will get back to you as soon as possible.</p>
// 	</div>
// </div>';

// }
// else
// {
// 	header("Location: $htmlroot/{$page}");
// 	exit();
// }

$tags_arr['mod_view'] = <<<H

	<section class="section section--white-bg">
        <div class="container">
            <div class="row">
				<div class="col-md-12">
                   	<header class="section__header text-center"><div class="section__header__highlight section__header__highlight--red"></div><h1 class="section__heading section__heading--normal">Thank You for your enquiry!</h1>
                   	<p>We will contact you shortly regarding your enquiry. Thank you for your patience.</p>
                   	</header>
                </div>
            </div>
        </div>
    </section>

H;



?>