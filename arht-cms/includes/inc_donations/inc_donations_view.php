<?php

############################################################################################################################
## Edit FAQ Item
############################################################################################################################

function view_item()
{

    global $message,$id,$do,$disable_menu,$valid,$htmladmin, $main_subheading, $js_vars;

    $disable_menu = "true";

    // $sql = "SELECT n.`id`, n.`news_url`, n.`date`, n.`page_meta_data_id`,
    //         pmd.`name`, pmd.`url`, pmd.`menu_label`, pmd.`heading`, pmd.`title`, pmd.`short_description`, pmd.`introduction`,
    //         pmd.`og_title`, pmd.`meta_description`, pmd.`og_image`, pmd.`thumb_photo`, pmd.`slideshow_id`, pmd.`gallery_id`
    //         FROM `news` n
    //         LEFT JOIN `page_meta_data` pmd
    //         ON(n.`page_meta_data_id` = pmd.`id`)
    //         WHERE n.`id` = '{$id}'
    //         LIMIT 1";

    $sql = "SELECT d.`id`, d.`first_name`, d.`last_name`, d.`full_name`, d.`email`, d.`phone_number`, 
            d.`address`, d.`suburb`, d.`city`, d.`post_code`, d.`full_address`, d.`ref_number`, d.`amount`, 
            d.`subscribe`, d.`donation_type_id`, d.`donation_type_name`, dt.`cc_name`, dt.`cc_holder_name`, 
            dt.`txn_id` , dt.`response_text`, dt.`date_processsed`, dt.`currency_input`
            FROM `donation` d
            LEFT JOIN `donation_transaction` dt
            ON(d.`id` = dt.`data3`)
            WHERE d.`id` = '{$id}'
            LIMIT 1";

    $row = fetch_row($sql);

    extract($row);

    ##------------------------------------------------------------------------------------------------------
    ## Page functions

    $page_functions = <<< HTML
    <ul class="page-action">
        <li><a class="btn btn-default" href="{$htmladmin}/?do={$do}"><i class="glyphicon glyphicon-arrow-left"></i> Back</a>
        </li>
    </ul>
HTML;

##------------------------------------------------------------------------------------------------------
## Settings tab content

$donation_details = <<<H

    <h1>Donor Details</h1>
    <hr>
    <table border="0" cellspacing="0" cellpadding="7" class="striped">
        <tbody>
            <tr>
                <td><b>First Name</b></td>
                <td>{$first_name}</td>
            </tr>
            <tr>
                <td><b>Last Name</b></td>
                <td>{$last_name}</td>
            </tr>
            <tr>
                <td><b>Email</b></td>
                <td><a href="mailto:{$email}">{$email}</a></td>
            </tr>
            <tr>
                <td><b>Phone Number</b></td>
                <td>{$phone_number}</td>
            </tr>
            <tr>
                <td><b>Address</b></td>
                <td>{$full_address}</td>
            </tr>
            <tr>
                <td><b>Donation Type</b></td>
                <td>{$donation_type_name}</td>
            </tr>
        </tbody>            
    </table>
    
    <br>
    <br>
    <h1>Payment Details</h1>
    <hr>
    <table border="0" cellspacing="0" cellpadding="7" class="striped">
        <tbody>
            <tr>
                <td><b>Transaction ID</b></td>
                <td>{$txn_id}</td>
            </tr>
            <tr>
                <td><b>Amount</b></td>
                <td>{$currency_input} {$amount}</td>
            </tr>
            <tr>
                <td><b>Card Holder Name</b></td>
                <td>{$cc_holder_name}</td>
            </tr>
            <tr>
                <td><b>Card Type</b></td>
                <td>{$cc_name}</td>
            </tr>
            <tr>
                <td><b>Status</b></td>
                <td>{$response_text}</td>
            </tr>
            <tr>
                <td><b>Date Processed</b></td>
                <td>{$date_processsed}</td>
            </tr>
        </tbody>            
    </table>

H;

##------------------------------------------------------------------------------------------------------
## tab arrays and build tabs

$temp_array_menutab = array();


$temp_array_menutab['Donation Details']          = $donation_details;

$counter = 0;
$tablist ="";
$contentlist="";

foreach($temp_array_menutab as $key => $value){

    $tablist.= "<li><a href=\"#tabs-".$counter."\">".$key."</a></li>";
    

    $contentlist.=" <div id=\"tabs-".$counter."\">".$value."</div>";

    $counter++;
}

$tablist="<div id=\"tabs\"><ul>$tablist</ul><div style=\"padding:10px;\">$contentlist</div></div>";

    $page_contents="<form action=\"$htmladmin/?do={$do}\" method=\"post\" name=\"pageList\" enctype=\"multipart/form-data\">
        $tablist
        <input type=\"hidden\" name=\"action\" value=\"\" id=\"action\">
        <input type=\"hidden\" name=\"do\" value=\"{$do}\">
        <input type=\"hidden\" name=\"id\" value=\"$id\">
        <input type=\"hidden\" name=\"meta_data_id\" value=\"$page_meta_data_id\">
    </form>";
require "resultPage.php";
echo $result_page;
exit();

}

?>
