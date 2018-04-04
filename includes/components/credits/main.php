<?php

$tags_arr['websitecredits'] = '';

# COPYRIGHT
    // $company = $settings_arr['set_company'];
    $copyright = $settings_arr['copyright'] = <<<HTML

    <p class="footer__copyright__text">Copyright &copy; $thisyear $company. We are registered with the NZ Charities Commission. Our Registered Charity Number is CC21935.</p>

HTML;

# SIGNATURE
    $signature = $settings_arr['signature'] = <<< HTML
    <p class="footer__copyright__text">
    Website by <a href="http://tomahawk.co.nz" class="link link--red footer__copyright__link">Tomahawk</a>
    </p>

HTML;


# STRUCTURE

$websitecredits = <<< HTML
 <div class="row">
    <div class="col-xs-12 footer__copyright">
    	$copyright 
    	$signature
    </div>
</div>

HTML;

$tags_arr['websitecredits'] = $websitecredits;

?>