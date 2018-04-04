<?php 

$promotion_btn    = '';
$sponsors_buttons = '';

$col_cls_span = 5;
$c = 0;

foreach ($all_donations as $index => $single_donation) {
	
	extract($single_donation);


	if($is_promoting == 'Y' && empty($promotion_btn)){

		$col_cls_span = 4;

		$promotion_btn = <<<H
			<div class="row">
                <div class="col-md-6 col-md-push-3 text-center">
                    <a class="btn btn--yellow btn--xl" href="{$page_donations->full_url}{$full_url}">
	                    <i class="icon {$icon_cls}--lg-dark"></i>
	                    <span>{$name}</span>
	                    <i class="fa fa-angle-right icon--fa-lg"></i>
                    </a>                    
                </div>
            </div>

            <div class="row">
                <div class="col-xs-12">
                    <div class="section__border"></div>
                </div>
            </div>
H;
		
		

	}else{

		
		$sponsors_buttons .= <<<H
			<a class="btn btn--col btn--col--{$col_cls_span} btn--white btn--border-yellow" href="{$page_donations->full_url}{$full_url}">
				<i class ="icon {$icon_cls}"></i>
				<span>{$name}</span>
				<i class ="fa fa-angle-right icon--fa"></i>
          	</a>
H;

		
	}


}	


$container_cls = ( $col_cls_span == 5 ) ? 'container-fluid' : 'container';

$donation_view = <<<H

<section class="section section--background-image">
    <div class="section--overlay"></div>

    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <header class="section__header text-center">
                    <div class="section__header__highlight section__header__highlight--yellow"></div>
                    <h1 class="section__heading section__heading--white section__heading--bold">{$donation_heading}</h1>                    
                </header>
            </div>
        </div>

        <div class="row">
            <div class="col-md-8 col-md-push-2">
                <p class="text-center">{$donation_description}</p>                
            </div>
        </div>

        {$promotion_btn}
	</div>
	<div class="{$container_cls}">
        <div class="row">
            <div class="col-md-12">
                <header class="section__header text-center">
                    <h2 class="section__heading section__heading--white section__heading--normal">Help us in other ways</h2>
                </header>

                <div class="btn-group">
                  {$sponsors_buttons}
                </div>
            </div>
        </div>

        <div class="row">
            
        </div>
        
    </div>
</section>
H;

$tags_arr['mod_view'] .= $donation_view;

 ?>