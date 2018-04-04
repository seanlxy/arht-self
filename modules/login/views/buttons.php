<?php


$agent_login_btn_view = '';

if( $is_logged_in )
{

    $user_logged_in = $_SESSION['login']['user'];

    $agent_login_btn_view = '<span class="icon-link nh"><i class="glyphicons glyphicons-user"></i> Hi, '.$user_logged_in.' <a href="/?a=logout">Logout</a></span>';

    if( $page_id == $page_login->id )
    {
        header("Location: {$htmlroot}{$page_agent_welcome->full_url}");
        exit();
    }

    if( $page_id == $page_agent_welcome->id )
    {

        $tags_arr['heading'] = "Welcome {$user_logged_in}";

    }

}
else
{
    if( $page_login )
    {
        $agent_login_btn_view = '<a href="'.$page_login->full_url.'" title="'.$page_login->title.'" class="icon-link">
        <i class="glyphicons glyphicons-user"></i> 
        <span>Agent Login</span></a>';


        if( $page_id == $page_agent_welcome->id )
        {
            header("Location: {$htmlroot}{$page_login->full_url}");
            exit();
        }
    }
}

$tags_arr['agent_login_btn_view'] = $agent_login_btn_view;


if( isset($_GET['a']) && $_GET['a'] == 'logout' )
{

    unset($_SESSION['login']);

    header("Location: {$htmlroot}{$page_login->full_url}");
    exit();

}

?>