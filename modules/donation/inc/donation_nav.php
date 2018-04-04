<?php 

foreach ($all_donations as $index => $single_donation) {
	
	extract($single_donation);


	if($is_promoting == 'Y' && empty($promotion_btn)){

		$promotion_btn = <<<H
			<a class="btn btn--col btn--col--5 btn--yellow btn--border-yellow" href="{$page_donations->full_url}{$full_url}">
            	<i class="icon {$icon_cls}--dark"></i>
                	<span>{$name}</span>
                <i class="fa fa-angle-right icon--fa"></i>
            </a>
H;
		
		

	}else{

		
		$sponsors_buttons .= <<<H

          	<a class="btn btn--col btn--col--5 btn--white btn--border-yellow" href="{$page_donations->full_url}{$full_url}">
	            <i class ="icon {$icon_cls}"></i>
				<span>{$name}</span>
				<i class ="fa fa-angle-right icon--fa"></i>
	        </a>
H;

		
	}


}	


$donation_nav = <<<H

	<section class="section section--blue-bg section--abs" id="donation-section" style="display: none;">
        <span class="close-btn hidden-md hidden-lg"><i class="fa fa-close"></i></span>
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">

                    <header class="section__header text-center">
                        <div class="section__header__highlight section__header__highlight--yellow"></div>
                        <h2 class="section__heading section__heading--white section__heading--bold">Make a Donation</h2>
                        <h4>Choose you preferred method to help us help the community</h4>
                    </header>
                </div>
            </div>
            
            <div class="btn-group">
                {$promotion_btn}
                {$sponsors_buttons}
            </div>
        </div>
    </section>
H;
 ?>