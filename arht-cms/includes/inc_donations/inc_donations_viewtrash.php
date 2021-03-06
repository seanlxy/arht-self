<?php


function view_trash()
{

    global $message,$valid,$item_select,$testm_id,$testm_rank,$htmladmin, $main_heading, $do;

    $main_heading .= ' | Trash';

   if($_POST['action'] === 'restore')
    {
        $items_to_restore = $_POST['item_select'];

        if(count($items_to_restore) > 0)
        {
            $page_ids = implode(', ', $items_to_restore);

            $query = "UPDATE `page_meta_data` SET `status` = 'H' WHERE `id` IN ($page_ids)";

            run_query($query);

            header("Location: $htmladmin/?do={$do}");
            exit();
        }
        else
        {
            $message = 'Plese select an item from list';
        }
    }
    
    $c = 0;

    $active_pages = "";
    $page_contents = "";
    
    function generate_item_list($parent_id = 0)
    {
        global $c, $htmladmin, $do;

        $sql = "SELECT n.`page_meta_data_id`, pmd.`name`, pmd.`status`, pmd.`rank`, n.`id` AS `page_id`
            FROM `news` n
            LEFT JOIN `page_meta_data` pmd
            ON(n.`page_meta_data_id` = pmd.`id`)
            WHERE pmd.`status` = 'D'
            ORDER BY pmd.`rank`";
        
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
                
                $label = ($person_name) ? $person_name.(($person_location) ? ", {$person_location}" : '') : 'Untitled';

                $editlink="<a href=\"$htmladmin/?do={$do}&action=edit&id=$page_id\">$name</a>";

                $item_select="<label class=\"custom-check\"><input type=\"checkbox\" name=\"item_select[]\" class =\"checkall\" value=\"$page_meta_data_id\"><span></span></label>";

                $status = '<span class="label label-danger">Deleted</span>';

                $html .= <<< HTML
                <tr>
                    <td width="20" align="center">$item_select</td>
                    <td style="padding-left:{$indentation}px;">
                        <input type="hidden" name="testm_id[]" value="$page_meta_data_id">
                        <input type="text" name="testm_rank[]" value="$rank" style="color:#999999;margin-right:10px;margin-left:10px;width:30px;text-align:center;">
                        $editlink
                    </td>
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

    $page_functions = <<< HTML
 
      <ul class="page-action">
        <li><button type="button" class="btn btn-default" onclick="submitForm('restore',1)"><i class="fa fa-history"></i> Restore</button></li>
    </ul>
HTML;

                     
    $page_contents.= <<< HTML
      <form action="$htmladmin/?do={$do}&view=trash" method="post" style="margin:0px;" name="pageList">
        
        <table width="100%" class="bordered">
             <thead>
                <tr>
                    <th width="20"><label class="custom-check"><input type="checkbox" name="all" id="checkall"><span></span></label></td>
                    <th align="left">News</th>
                    <th align="left" width="100">Status</th>
                </tr>
            </thead>
            <tbody>
                $active_pages
            </tbody>
        </table>
        <input type="hidden" name="action" value="" id="action">
        <input type="hidden" name="do" value="{$do}">
    </form>
HTML;

    require "resultPage.php";
    echo $result_page;
    exit();
}




?>