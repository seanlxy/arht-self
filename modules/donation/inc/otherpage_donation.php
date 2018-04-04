<?php 

$promotion_btn    = '';
$sponsors_buttons = '';


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

        
    </div>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="btn-group">
                    {$promotion_btn}
                    {$sponsors_buttons}
                </div>
            </div>
        </div>
    </div>
</section>
H;

$tags_arr['mod_view'] .= $donation_view;

 ?>