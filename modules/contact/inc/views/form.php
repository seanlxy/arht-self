<?php
$footer_contact = fetch_all("SELECT * FROM `contact_details`");

if (!empty($footer_contact)) {

    $contact_info = '';

    foreach ($footer_contact as $key => $contact) {

        $contact_address = (!empty($contact['contact_address'])) ? '<p class="footer__text">'.$contact['contact_address'].'</p>' : '';
        $contact_phone = (!empty($contact['contact_phone_number'])) ? '<p class="footer__text"><i class="fa fa-phone footer__text__icon"></i>'.$contact['contact_phone_number'].'</p>' : '';
        $contact_email = (!empty($contact['contact_email'])) ? '<p class="footer__text"><i class="fa fa-envelope-o footer__text__icon"></i>'.$contact['contact_email'].'</p>' : '';

        $contact_info .= <<<H
            <div class="col-md-12 footer__address">
                <h4 class="footer__heading--red text-uppercase">{$contact['contact_name']}</h4>
                {$contact_address}
                {$contact_phone}
                {$contact_email}
            </div>
H;
    }

}


if (!empty($error_message)) {
        $error = <<<H
            <div class="row">
                <div class="col-md-12">
                    <div class="alert alert-danger" role="alert">{$error_message}</div>
                </div>
            </div>
H;
    }

$contact_form = <<< H

<section class="section section--white-bg">
    <div class="container">       
        <div class="row">
            <div class="col-md-6">
                <form action="$fromroot/{$page}/{$segment1}" method="POST" id="contact-form">
                {$error}
                    <input class="form-control form__input" type="text" name="first_name" placeholder="First Name *" value={$first_name}> 
                    <input class="form-control form__input" type="text" name="last_name" placeholder="Last Name *" value={$last_name}> 
                    <input class="form-control form__input" type="text" name="contact_number" placeholder="Phone Number" value={$contact_number}> 
                    <input class="form-control form__input" type="email" name="email_address" placeholder="Email *" value={$email_address}>
                    <textarea class="form-control form__input" name="message" placeholder="Message">{$message}</textarea>    

                    <div class="form-group">
                        <div>
                            <p>In order to assist us in reducing spam, please type the characters you see:</p>
                            <div style="margin-bottom:10px;"><img src="/captcha.jpg" alt="spam control image" id="anti-spam"></div>
                            <input type="text" value="" name="captcha" id="captcha-inp" class="form-control form__input" style="width:120px" autocomplete="off" tabindex="7">
                        </div>
                    </div>  

                    <button type="submit" name="submit" value="submit" class="btn btn--full-width btn--red form__btn">Submit</button>   
                </form>  
            </div>

            <div class="col-md-6">
                {$contact_info}
            </div>
        </div>
    </div>
</section>



H;

$output .= $contact_form; 

if( $tags_arr['content'] )
{

    $output = $output.'<div class="col-xs-12 col-md-5 col-md-offset-1">'.$tags_arr['content'].'</div>';

}

?>