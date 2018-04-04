<?php

## ----------------------------------------------------------------------------------------------------------------------

## Save page
include_once('../../functions/func_all.php');
include_once("../../utility/config.php");
if(!$c_Connection->Connect()) {
    echo "Database connection failed";
    exit;
}


// Change this based on the ID of your preview page.
$id = 2;
$page_label             = $_REQUEST['page_label'];
$page_heading 		= $_REQUEST['page_heading'];
$current_page		= $_REQUEST['current_page'];
$page_title 		= $_REQUEST['page_title'];
$page_mkeyw             = $_REQUEST['page_mkeyw'];
$page_mdescr            = $_REQUEST['page_mdescr'];
$page_seokeyw           = $_REQUEST['page_seokeyw'];
$page_content 		= $_REQUEST['page_content'];
$page_imagepath 	= $_REQUEST['page_imagepath'];

// keep page menu and footer empty
$page_menu 		= '';
$page_footer 		= '';
$page_parentid 		= $_REQUEST['page_parentid'];
$imggrp_id              = $_REQUEST['imggrp_id'];
$imggrp_id_footer       = $_REQUEST['imggrp_id_footer'];
$tmpl_id                = $_REQUEST['tmpl_id'];
$page_accesslevel       = $_REQUEST['page_accesslevel'];
$mp_rank                = $_REQUEST['mp_rank'];
$mod_id			= $_REQUEST['mod_id'];
$page_introduction	= $_REQUEST['page_introduction'];
$page_linkname		= $_REQUEST['page_linkname'];
$page_quicklinktext	= $_REQUEST['page_quicklinktext'];
$quicklink_id           = $_REQUEST['quicklink_id'];
$esid                   = $_REQUEST['esid'];
$page_mcache            = $_REQUEST['page_mcache'];
$page_mrobots           = $_REQUEST['page_mrobots'];

//make sure page url is always 'preview'.
$page_url = 'preview';
$page_pictures_id	= $_REQUEST['page_pictures_id'];
$page_video_id		= $_REQUEST['page_video_id'];
$page_quicklinksnippet  = $_REQUEST['page_quicklinksnippet'];
$image_gallery_grp      = $_REQUEST['image_gallery_grp'];
$page_url               = (trim($page_url)=='' ? $page_title : $page_url);
$page_url 		= preg_replace('/[^_a-z0-9-]/i','',str_replace(' ','-',strtolower($page_url)));

//Saving the old page content to history with page status archive
$temp_array_backup ['page_id'] = $id;
$temp_array_backup ['page_content']= fetch_value("SELECT page_content FROM general_pages WHERE page_id= '$id'");
$temp_array_backup ['pagehistory_datearchived'] = date("Y-m-d H:i:s");
$temp_array_backup ['pagehistory_status'] = 'A';
insert_row($temp_array_backup,'general_pageshistory');


//Saving Quicklinks

// $sql = "DELETE
// 	FROM general_quicklinks
// 	WHERE quick_page_id = '$id'";

// run_query($sql);

// if(!empty($quicklink_id)){

//     foreach ($quicklink_id as $i){

// 	$temp_quick_array = array();

// 	$temp_quick_array['quick_page_id']      = $id;

// 	$temp_quick_array['page_id']  = $i;

// 	insert_row($temp_quick_array, 'general_quicklinks');

//     }

// }



//Saving Snippets

// $sql = "DELETE
// 	FROM general_snippets
// 	WHERE snippet_page_id = '$id'";
// run_query($sql);
// if(!empty ($esid)){
//     foreach ($esid as $i){
// 	$temp_quick_array = array();
// 	$temp_quick_array['snippet_page_id'] = $id;
// 	$temp_quick_array['snippet_page_linkto'] = $i;
// 	insert_row($temp_quick_array, 'general_snippets');
//     }
//}
	$temp_array_save['page_url'] = $page_url;
	$temp_array_save['page_label'] = $page_label;
	$temp_array_save['page_linkname'] = $page_linkname;
	$temp_array_save['page_heading'] = $page_heading;
	$temp_array_save['current_page'] = $current_page;
	$temp_array_save['page_title'] = $page_title;
	$temp_array_save['page_mkeyw'] = $page_mkeyw;
	$temp_array_save['page_mdescr'] = $page_mdescr;
	$temp_array_save['page_seokeyw'] = $page_seokeyw;
	$temp_array_save['page_content'] = $page_content;
	$temp_array_save['page_menu'] = $page_menu;
	$temp_array_save['page_footer'] = $page_footer;
	$temp_array_save['page_introduction'] = $page_introduction;
	$temp_array_save['page_dateupdated'] = date("Y-m-d H:i:s");
	$temp_array_save['page_imagepath'] = $page_imagepath;
	$temp_array_save['page_parentid'] = $page_parentid;
	$temp_array_save['imggrp_id'] = $imggrp_id;
	$temp_array_save['imggrp_id_footer'] = $imggrp_id_footer;
	$temp_array_save['tmpl_id'] = $tmpl_id;
	$temp_array_save['page_accesslevel'] = $page_accesslevel;
	$temp_array_save['page_mcache'] = $page_mcache;
	$temp_array_save['page_mrobots'] = $page_mrobots;
	$temp_array_save['page_quicklinktext'] = $page_quicklinktext;
	$temp_array_save['page_quicklinksnippet'] = $page_quicklinksnippet;
	$temp_array_save['imggrp_id_gallery']   = $image_gallery_grp;
	$temp_array_save['page_status']   = 'A';

if($thumbnails == 'Y'){
	$a = array();
	$a['thumbnails'] = 'Y';
	$end = "WHERE page_parentid = '$id'";
	update_row($a,'general_pages',$end);
}elseif($thumbnails == 'N'){
	$a = array();
	$a['thumbnails'] = 'N';
	$end = "WHERE page_parentid = '$id'";
	update_row($a,'general_pages',$end);
}

$end = "WHERE page_id='$id'";
update_row($temp_array_save,'general_pages',$end);


$sql = "DELETE mp.*
	FROM module_pages mp
	LEFT JOIN modules m ON m.mod_id = mp.mod_id
	WHERE mp.page_id = '$id'
	AND m.mod_showincms='Y'";
run_query($sql);

for($i=0;$i<=count($mod_id);$i++){

	if($mp_rank[$i] >0){

		$temp_array_modules['page_id'] = $id;
		$temp_array_modules['modpages_rank'] = $mp_rank[$i];
		$temp_array_modules['mod_id'] = $mod_id[$i];
		insert_row($temp_array_modules,'module_pages');
	}

}