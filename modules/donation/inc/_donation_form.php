<?php 

$donation_types = fetch_row("SELECT dt.`page_meta_data_id`, dt.`icon_cls`,
	dt.`is_promoting`, pmd.`name`, pmd.`status`, dt.`id` AS `page_id`,
	pmd.`full_url`, pmd.`heading`, pmd.`introduction`, pmd.`title`, pmd.`meta_description`, pmd.`og_title`, pmd.`og_image`
    FROM `donation_type` dt
    LEFT JOIN `page_meta_data` pmd
    ON(dt.`page_meta_data_id` = pmd.`id`)
    WHERE pmd.`url` = '$segment1'
    LIMIT 1");


if (!empty($donation_types)) {

	extract($donation_types);
	// preprint_r($donation_types);
	// die;

    $tags_arr['title'] = $title;
    $tags_arr['mdescr'] = $meta_description;
    $tags_arr['og_title'] = $og_title;
    $tags_arr['og_image'] = $og_image;
	$tags_arr['heading-view'] = '';

	if ($is_promoting == 'Y') {

		$tags_arr['heading-view'] = <<<H
		<header class="section__header text-center">
            <div class="section__header__logo section__header__logo--lg section__header__logo--yellow">
                <i class="icon {$icon_cls}--lg-dark"></i>
                <h3 class="section__heading section__heading--bold section__heading--white section__header__logo__heading">{$name}</h3>
            </div>
            <div class="section__header__highlight section__header__highlight--red"></div>
            <h1 class="section__heading section__heading--normal">{$heading}</h1>
        </header>	
H;
	}
	else{
		$tags_arr['heading-view'] = <<<H
		<header class="section__header text-center">
            <div class="section__header__logo section__header__logo">
                <i class="icon {$icon_cls}--lg"></i>
                <h3 class="section__heading section__heading--bold section__heading--red section__header__logo__heading">{$name}</h3>
            </div>
            <div class="section__header__highlight section__header__highlight--red"></div>
            <h1 class="section__heading section__heading--normal">{$heading}</h1>
        </header>
H;
	}

    $content = get_content($page_meta_data_id);

    $tags_arr['introduction-view'] = $introduction;
    
    $tags_arr['content'] = $content;

	$donation_form = '';

    $subscribe_check = ($subscribe == 'Y') ? 'checked' : '';
    $terms_check = ($terms == 'Y') ? 'checked' : '';

    if (!empty($error_message)) {
        $error = <<<H
            <div class="row">
                <div class="col-md-12">
                    <div class="alert alert-danger" role="alert">{$error_message}</div>
                </div>
            </div>
H;
    }


	$donation_form = <<<H

    <section class="section section--white-bg">
        <div class="container">
            <form action="{$htmlrootfull}$fromroot/{$page}/{$segment1}" method="POST" id="donation-form">
            {$error}
                <div class="row donation-radio-group">
                    <div class="col-md-12 text-center">
                        <div class="donation-radio">
                            <input class="donation-radio__input" type="radio" id="r1" name="donation_radio" value="30" {$nzd30} />
                            <label class="donation-radio__label" for="r1">$30</label>
                        </div>

                        <div class="donation-radio">
                            <input class="donation-radio__input" type="radio" id="r2" name="donation_radio" value="60" {$nzd60} />
                            <label class="donation-radio__label" for="r2">$60</label>
                        </div>

                        <div class="donation-radio">
                            <input class="donation-radio__input" type="radio" id="r3" name="donation_radio" value="100" {$nzd100} />
                            <label class="donation-radio__label" for="r3">$100</label>
                        </div>

                        <div class="donation-radio">
                            <input class="donation-radio__input" type="radio" id="r4" name="donation_radio" value="200" {$nzd200} />
                            <label class="donation-radio__label" for="r4">$200</label>
                        </div>

                        <div class="donation-radio">
                            <input class="donation-radio__input" type="radio" id="r5" name="donation_radio" value="1000" {$nzd1000} />
                            <label class="donation-radio__label" for="r5">$1000</label>
                        </div>                        
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group form-inline">
                            <label class="checkbox-inline">
                              <input type="checkbox" id="other-amount-checkbox" value="subscribe" name="other_amount_checkbox" {$NZDother}> Other Amount
                            </label>
                            <div class="input-group section__donation-form__input-group {$visibility}" id="other-amount-input">
                                <div class="input-group-addon"><div class="fa fa-usd"></div></div>
                                <input class="form-control section__donation-form__input" id="other-amount" name="other_amount" type="text" value="{$amount}" placeholder="Enter your amount">
                            </div>
                                              
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-12">
                        <header class="section__header section__donation-form__header">
                            <h3 class="section__heading section__heading--normal section__heading--red">Your Details</h3>
                        </header>                        
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <input class="form-control form__input" type="text" name="first_name" placeholder="First Name *" value={$first_name}> 
                        <input class="form-control form__input" type="text" name="last_name" placeholder="Last Name *" value={$last_name}> 
                        <input class="form-control form__input" type="text" name="phone_number" placeholder="Phone Number *" value={$phone_number}> 
                        <input class="form-control form__input" type="email" name="email" placeholder="Email *" value={$email}> 
                        <input class="form-control form__input" type="text" name="address" id="address" placeholder="Address *" value={$address}> 
                        <input class="form-control form__input" type="text" name="suburb" id="sublocality_level_1" placeholder="Suburb" value={$suburb}> 
                        <input class="form-control form__input" type="text" name="city" id="locality" placeholder="City" value={$city}> 
                        <input class="form-control form__input" type="text" name="post_code" id="postal_code" placeholder="Post Code" value={$post_code}> 
                        <input class="form-control form__input" type="text" name="ref_number" placeholder="Reference Number" value={$ref_number}>       


                        <div class="form-group">
                            <label class="checkbox-inline">
                              <input type="checkbox" value="Y" name="subscribe" {$subscribe_check}> Subscribe to our newsletter
                            </label>
                            <label class="checkbox-inline">
                              <input type="checkbox" value="Y" name="terms" id="terms_check" {$terms_check}> Agree to the <a href="" class="link--red" data-toggle="modal" data-target="#terms">terms and conditions</a> *
                            </label>
                        </div> 

                        <input type="hidden" name="page_id" value="{$page_id}">
                        <input type="hidden" name="meta_page_id" value="{$page_meta_data_id}">           
                        <input type="hidden" name="meta_page_name" value="{$name}">           
                        <input type="hidden" id="street_number" value="">           
                        <input type="hidden" id="route" value="">           
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-12">
                        <button type="submit" name="submit" class="btn btn--full-width btn--red form__btn">Donate Now</button>
                    </div>
                </div>
            </form>
        </div>
    </section>

H;


}

$tags_arr['mod_view'] .= $donation_form;


include_once('donation_terms_modal.php');

 ?>