<?php 

$response = $pxpay->getResponse($_GET['result']);
$donor_id = $response->getTxnData3();

$query = fetch_row("SELECT d.`id`, d.`first_name`, d.`last_name`, d.`full_name`, d.`email`, d.`phone_number`, d.`address`, d.`suburb`, d.`city`, d.`post_code`, 
    d.`full_address`, d.`ref_number`, d.`amount`, d.`subscribe`, d.`donation_type_name`,
    dt.`merchant_ref` AS dps_reference, dt.`response_text` AS dps_status, dt.`id` AS transaction_id, REPLACE(dt.`amount_settlement`, '.00', '') AS amount,
    DATE_FORMAT(dt.`date_processsed`, '%e %M %Y') AS donation_date
    FROM `donation` d
    LEFT JOIN donation_transaction dt
    ON(dt.`data3` = d.`id`)
    WHERE d.`id` = '$donor_id'");

extract($query);


if (!empty($amount)) {
    switch ($amount) {
        case '25':
            $nzd25 = 'checked';
            break;

        case '50':
            $nzd50 = 'checked';
            break;

        case '100':
            $nzd100 = 'checked';
            break;

        case '200':
            $nzd200 = 'checked';
            break;

        default:
            $NZDother = 'checked';
            $visibility = 'visible';
            break;
    }
}

$tags_arr['mod_view'] = <<<H

    <section class="section section--white-bg">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <header class="section__header text-center"><div class="section__header__highlight section__header__highlight--red"></div><h1 class="section__heading section__heading--normal">Sorry! Your transaction could not be processed!</h1>
                    </header>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 text-center">
                    <p>Your Transaction have been {$response->getResponseText()}</p>
                </div>
            </div>
        </div>
    </section>

H;
 ?>
