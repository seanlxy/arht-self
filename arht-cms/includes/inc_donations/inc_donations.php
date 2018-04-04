<?php
## ----------------------------------------------------------------------------------------------------------------------
## NetZone 1.0
## inc_faq.php
##
## Author: Ton Jo Immanuel, Tomahawk Brand Management Ltd.
## Date: 19 April 2010
##
## Manage FAQ
##
##
## ----------------------------------------------------------------------------------------------------------------------

function do_main(){

    global $message, $valid, $item_select, $testm_id, $testm_rank, $htmladmin, $main_heading, $do, $action;

    $action       = ($_GET['view']) ? $_GET['view'] : $action;
    $item_select  = $_POST['item_select'];
    $testm_id     = $_POST['testm_id'];

    $main_heading = 'Donations Received';

    switch($action)
    {
        case 'view':
            @include_once('inc_'.$do.'_view.php');
            $return = view_item();
            break;
    }

    $c = 0;

    $active_pages = "";
    $page_contents = "";
    
    function generate_item_list($parent_id = 0)
    {
        global $c, $htmladmin, $do;

        $sql = "SELECT d.`id`, d.`full_name` AS `name`, d.`email`, d.`amount`, dt.`response_text` AS `status`, dt.`date_processsed` 
                FROM `donation` d
                LEFT JOIN `donation_transaction` dt
                ON(d.`id` = dt.`data3`)
                ORDER BY dt.`date_processsed` DESC";

        
        $rows = fetch_all($sql);

        $html = '';
        $indentation = 0;
        $c++;

        if( !empty($rows) )
        {

            for ($i=1; $i < $c; $i++) $indentation += 48;

            foreach ($rows as $index => $row)
            {
                extract($row);
                $bgc = (($index % 2) == 1) ? '#fff' : '#f6f8fd';
                
                $editlink="<a href=\"$htmladmin/?do={$do}&action=view&id=$id\">$name</a>";

                $status = '<span class="label label-primary">'.$status.'</span>';

                $html .= <<< HTML
                <tr>
                    <td style="padding-left:{$indentation}px; padding: 10px;">
                        $editlink
                    </td>
                    <td width="100">$amount</td>
                    <td width="150">$date_processsed</td>
                    <td width="100">$status</td>
                </tr>
HTML;
                

            }

        }

        $c--;


        return $html;
    }


    $active_pages = generate_item_list();


   if ($message != "") {

        $page_contents .= <<< HTML
          <div class="alert alert-warning page">
             <i class="glyphicon glyphicon-info-sign"></i>
              <strong>$message</strong>
          </div>
HTML;
    }
    ############################################################################################################################
    ## Get the page functions
    ############################################################################################################################

//     $page_functions = <<< HTML
//         <ul class="page-action">
//             <li class="pull-right">
//                 <a href="$htmladmin/?do={$do}&view=trash" class="btn btn-default">
//                     <i class="glyphicon glyphicon-trash"></i> View trash
//                 </a>
//             </li>
//             <li><button type="button" class="btn btn-default" onclick="submitForm('new',1)"><i class="glyphicon glyphicon-plus-sign"></i> New</button></li>
//             <li><button type="button" class="btn btn-default" onclick="submitForm('delete')"><i class="glyphicon glyphicon-trash"></i> Move to trash</button></li>
//             <li><button type="button" class="btn btn-default" onclick="submitForm('publish')"><i class="glyphicon glyphicon-eye-open"></i> Publish</button></li>
//             <li><button type="button" class="btn btn-default" onclick="submitForm('hide')"><i class="glyphicon glyphicon-eye-close"></i> Hide</button></li>
//             <li><button type="button" class="btn btn-default" onclick="submitForm('saverank', 1)"><i class="glyphicon glyphicon-sort-by-order"></i> Save Rank</button></li>
//         </ul>
// HTML;
					 
    $page_contents.= <<< HTML

    <form action="{$htmladmin}/?do={$do}" method="post" style="margin:0px;" name="pageList">
        <table width="100%" class="bordered">
            <thead>
                <tr>
                    <th align="left" style="padding:10px;">Donations</th>
                    <th align="left" width="100" style="padding:10px;">Amount</th>
                    <th align="left" width="150" style="padding:10px;">Date Received</th>
                    <th align="left" width="100" style="padding:10px;">Status</th>
                </tr>
            </thead>
            <tbody>
                $active_pages
            </tbody>
        </table>
        <input type="hidden" name="do" value="{$do}">
        <input type="hidden" name="page_meta_data_id" value="{$page_meta_data_id}">
    </form>
HTML;

require "resultPage.php";
echo $result_page;
exit();

}

?>