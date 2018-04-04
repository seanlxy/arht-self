<?php


function view_trash()
{

    global $message,$valid,$item_select,$testm_id,$testm_rank,$htmladmin, $do,$tbl_name;

    if($_POST['action'] === 'restore')
    {
        $items_to_restore = $_POST['item_select'];
        if(count($items_to_restore) > 0)
        {
            $page_ids = implode(', ', $items_to_restore);
            $query = "UPDATE $tbl_name SET status = 'H' WHERE id IN ($page_ids)";
            run_query($query);

            header("Location: $htmladmin/index.php?do=$do");
            exit();
        }
        else
        {
            $message = 'Plese select an item from list';
        }
    }

    $c             = 0;
    $active_pages  = "";
    $page_contents = "";
    $sql           = "SELECT `id`, CONCAT(`first_name`, ' ', `last_name`) AS full_name, `status`
                    FROM $tbl_name
                    WHERE `status` = 'D'
                    ORDER BY `status` ASC";

    $result = run_query($sql);
    while($row = mysql_fetch_assoc($result)) {
        extract($row);

        $date_created_obj = new DateTime($date_created);
       
        if ($c%2 == 1 ? $bgc = "#FFFFFF": $bgc = "#F6F8FD");
        $c++;

        $label       = ($full_name) ? $full_name : 'Untitled';
        $editlink    = "<a href=\"$htmladmin/index.php?do=$do&action=edit&id=$id\">$label</a>";
        $item_select = "<label class=\"custom-check\"><input type=\"checkbox\" name=\"item_select[]\" class =\"checkall\" value=\"$id\"><span></span></label>";

        $status = '<span class="label label-danger">Deleted</span>';

        $active_pages .= <<< HTML
            
            <tr>
                <td width="20" align="center">$item_select</td>
                <td width="450">$editlink</td>
                <td width="100">$status</td>
            </tr>
HTML;
    }
    
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

   $page_functions = <<< HTML
 
      <ul class="page-action">
        <li><button type="button" class="btn btn-default" onclick="submitForm('restore',1)"><i class="fa fa-history"></i> Restore</button></li>
    </ul>
HTML;
    
                     
    $page_contents.= <<< HTML
    <form action="$htmladmin/index.php?do=$do&view=trash" method="post" style="margin:0px;" name="pageList">
        <table width="100%" class="bordered">
            <thead>
                <tr style="height:auto; font-weight:bold;">
                    <th width="20" align="center">
                        <label class="custom-check">
                            <input type="checkbox" name="all" id="checkall">
                            <span></span>
                        </label>
                    </th>
                    <th width="450" align="left">LABEL</th>
                    <th width="100" align="left">STATUS</th>
                </tr>
            </thead>
        <tbody>
            $active_pages
        </tbody>
        </table>
        <input type="hidden" name="action" value="" id="action">
        <input type="hidden" name="do" value="$do">
    </form>
HTML;

    require "resultPage.php";
    echo $result_page;
    exit();
}




?>