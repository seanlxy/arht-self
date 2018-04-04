<?php 

$footer_contact = fetch_all("SELECT * FROM `contact_details`");

if (!empty($footer_contact)) {

    $contact_info = '';

    foreach ($footer_contact as $key => $contact) {

        $contact_address = (!empty($contact['contact_address'])) ? '<p class="footer__text">'.$contact['contact_address'].'</p>' : '';
        $contact_phone = (!empty($contact['contact_phone_number'])) ? '<p class="footer__text"><i class="fa fa-phone footer__text__icon"></i>'.$contact['contact_phone_number'].'</p>' : '';
        $contact_email = (!empty($contact['contact_email'])) ? '<p class="footer__text"><i class="fa fa-envelope-o footer__text__icon"></i>'.$contact['contact_email'].'</p>' : '';

        $contact_info .= <<<H
            <div class="col-md-4 footer__address">
                <h4 class="footer__heading--red text-uppercase">{$contact['contact_name']}</h4>
                {$contact_address}
                {$contact_phone}
                {$contact_email}
            </div>
H;
    }

    $tags_arr['contact-details'] = $contact_info;

}

 ?>