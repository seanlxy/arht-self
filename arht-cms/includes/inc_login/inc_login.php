<?php

## ----------------------------------------------------------------------------------------------------------------------
## NetZone 1.0
## inc_login.php
##
## Author: Sam Walsh, Ton Jo Immanuel, Tomahawk Brand Management Ltd.
## Date: 19 May 2010
##
## Manage Login Page
##
##
## ----------------------------------------------------------------------------------------------------------------------


if( !$c_Connection->Connect() )
{
    echo "Database connection failed";
    exit();
}

$Message   = "";
$c_Message = $c_Connection->GetMessage();
$template  = "{$rootadmin}/templates/login.html";

if(!file_exists($template) && is_dir($template))
{
    exit();
}


function insert_row_encrypted( $arr, $table, $encrpt = false, $key = NULL, $fields_to_encrypt = array() )
{

    $q = '';

    if(!empty($key) && !is_null($key))
    {
        if(is_array($arr) && count($arr) > 0)
        {
            $fields = $values = '';
            foreach ($arr as $k => $v)
            {
                $fields .= ", `{$k}`";
                $values .= (in_array($k, $fields_to_encrypt) && $encrpt === true && !is_null($key) && !empty($key)) ? ", AES_ENCRYPT('{$v}', '{$key}')" : ", '{$v}'";
            }

            $fields = trim(substr($fields, 1));
            $values = trim(substr($values, 1));
            $q = "INSERT INTO {$table} ({$fields}) VALUES({$values})";

            

        }
        
        $r = run_query($q);
        if (mysql_affected_rows() == 1) return mysql_insert_id();
        else return false;
    }
    else
    {
        die('Invalid request');
    }
}

function display_login_screen()
{

    global $page_heading, $page_contents, $login_cls, $htmlroot, $admin_dir;

    $ip_address = getenv('REMOTE_ADDR');

    // $is_blocked = fetch_value("SELECT `id` FROM `cms_blacklist_user` WHERE `ip_address` = '{$ip_address}' AND `is_disabled` = 1");

    // $a = is_user_locked(NULL, true);

    // echo $a;
    // die();

    $is_blocked = false;

    if(!$is_blocked)
    {

        $year         = date('Y');
        $page_heading = "Welcome to NetZone website administration.";
        $company      = fetch_value("SELECT `company_name` FROM `general_settings` WHERE `id` = '1' LIMIT 1");

        $action = filter_input(INPUT_GET, 'action', FILTER_SANITIZE_STRING);
        $do     = filter_input(INPUT_GET, 'do', FILTER_SANITIZE_STRING);
        $id     = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

        $force_do = $force_action = $force_id = '';

        if($do && $do != 'login' && $do != 'logout')
        {
            $force_do     = $do;
            $force_action = $action;
            $force_id     = $id;
        }

        $redirect_to = "$htmlroot/{$admin_dir}/index.php";

        if($force_do || ($force_action && $force_id))
        {

            if($force_do)
            {
                $redirect_to .= "/?do={$force_do}";

                if($force_action && $force_id)
                {
                    $redirect_to .= "&action={$force_action}&id={$force_id}";
                }

            }
        }

    $page_contents = <<< HTML

        <div class="lform-wrapper">
        $page_contents
            <div class="col-xs-12 well well-small login-form{$login_cls}">

                <form method="post" action="{$redirect_to}" name="login" id="login">
                    <input type="hidden" name="force-do" value="$force_do">
                    <input type="hidden" name="force-action" value="$force_action">
                    <input type="hidden" name="force-id" value="$force_id">
                    <fieldset>
                        <legend>$page_heading</legend>
                        <div class="form-group">
                            <label for="user-log">Username</label>
                            <div class="input-group">
                                <div class="input-group-addon"><i class="glyphicon glyphicon-user"></i></div>
                                <input class="form-control" name="log" type="text" id="user-log" autofocus>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="user-pwd">Password</label>
                            <div class="input-group">
                                <div class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></div>
                                <input class="form-control" name="key" type="password" id="user-pwd">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="captcha-inp">Captcha</label>
                            <div class="input-group">
                                <div class="input-group-addon"><i class="glyphicon glyphicon-info-sign"></i></div>
                                <input type="text" placeholder="Please enter the text you see below" value="" name="spam-control" id="captcha-inp" class="form-control" autocomplete="off">
                            </div>
                        </div>
                        <div class="form-group">
                            <div style="margin:10px 0;"><img src="/captcha.jpg" alt="spam control image" id="anti-spam"></div>
                        </div>
                        <div class="form-group">
                            <button type="submit" name="Submit" value="login" class="btn btn-default">
                                Login <i class="glyphicon glyphicon-log-in" style="margin-top:1px;vertical-align:text-top;"></i>
                            </button>
                            <input name="do" type="hidden" id="do" value="login">
                        </div>
                    </fieldset>  
                </form>         
                <hr>
                <div>
                    <p id="licence-info">
                        <img src="/{$admin_dir}/images/logo-circle.png" alt="Tomahawk" style="float:right;margin:-3px 0 0 10px;">
                        <strong>Netzone CMS &copy; <a target="_blank" href="http://www.tomahawk.co.nz">Tomahawk</a> 2009 - $year.</strong><br>
                        <span>This application is licensed to $company.</span>
                        <span class="clear"></span>
                    </p>
                </div>
                
            </div>
        </div>
HTML;


    }

    else
    {

        $page_contents = <<< HTML

        <div class="lform-wrapper">
    
            <div class="col-xs-12 ">
                <div class="alert alert-danger"><strong><i class="glyphicon glyphicon-ban-circle"></i> You are blocked. Please try again later.</strong></div>
            </div>
        </div>
HTML;

    }

}

function add_user_to_blacklist($username, $is_valid_user = false)
{

    $max_login_attempts      = 4;
    $time_login_disabled     = 1;     // In hours
    $max_time_login_disabled = 24;    // In hours
    $max_disabled_hours      = ($max_login_attempts * $time_login_disabled);
    $process_further         = false;
    $ip_address              = getenv('REMOTE_ADDR');

    $username = trim(filter_var($username, FILTER_SANITIZE_STRING));

    if(!$username) return $process_further;

    $blocked_user = fetch_row("SELECT `id`, `first_failed_attempt_on`, `failed_login_attempt_count`, `is_disabled`, `disabled_on`, 
        `username`, `recent_login_attempt_on`, `failed_hour_count`, `total_failed_attempt`, `is_notified`
        FROM `cms_blacklist_user`
        WHERE `username` = '".sanitize_one($username, 'sqlsafe')."'");

    if($blocked_user)
    {

        $id                         = $blocked_user['id'];
        $first_failed_attempt_on    = $blocked_user['first_failed_attempt_on'];
        $failed_login_attempt_count = $blocked_user['failed_login_attempt_count'];
        $total_failed_attempt       = $blocked_user['total_failed_attempt'];
        $recent_login_attempt_on    = $blocked_user['recent_login_attempt_on'];
        $is_disabled                = $blocked_user['is_disabled'];
        $disabled_on                = $blocked_user['disabled_on'];
        $failed_hour_count          = $blocked_user['failed_hour_count'];
        
        $current_date_obj      = new DateTime();
        $latest_attempt_on_obj = new DateTime($recent_login_attempt_on);

        $difference_obj  = $current_date_obj->diff($latest_attempt_on_obj);
        $difference_hour = $difference_obj->h;

        if(($difference_hour >= $time_login_disabled) || !$is_disabled)
        {
        
            if($difference_hour >= $max_time_login_disabled)
            {
                
                run_query("UPDATE `cms_blacklist_user` SET `failed_login_attempt_count` = 1, `total_failed_attempt` = (`total_failed_attempt` + 1), 
                    `recent_login_attempt_on` = NOW(), `is_disabled` = 0, `disabled_on` = NULL, `failed_hour_count` = 0, `ip_address` = '{$ip_address}'
                    WHERE `id` = '{$id}'
                    LIMIT 1");
            }
            else
            {
                // if login is unsuccesful but login attempt(s) are less than max login attempts

                if($failed_login_attempt_count < $max_login_attempts)
                {
                    
                    run_query("UPDATE `cms_blacklist_user` SET `failed_login_attempt_count` = (`failed_login_attempt_count` + 1), 
                        `total_failed_attempt` = (`total_failed_attempt` + 1), `recent_login_attempt_on` = NOW(), `ip_address` = '{$ip_address}'
                        WHERE `id` = '{$id}'
                        LIMIT 1");

                }
                else
                {
                    // If login attempts are reached the max login attempts limit, Something is not right here and it is time to block the login proccess

                    // If last try was within 1 hour
                    if($difference_hour < $time_login_disabled) 
                    {
                        run_query("UPDATE `cms_blacklist_user` SET `failed_login_attempt_count` = (`failed_login_attempt_count` + 1), 
                            `total_failed_attempt` = (`total_failed_attempt` + 1), `recent_login_attempt_on` = NOW(), `is_disabled` = 1, `disabled_on` = NOW(), 
                            `ip_address` = '{$ip_address}'
                            WHERE `id` = '{$id}'
                            LIMIT 1");
                    }
                    else
                    {

                        //max time not done
                        if($failed_hour_count < $max_disabled_hours)
                        {
                            run_query("UPDATE `cms_blacklist_user` SET `failed_login_attempt_count` = 1, `total_failed_attempt` = (`total_failed_attempt` + 1), 
                                `recent_login_attempt_on` = NOW(), `is_disabled` = 0, `disabled_on` = NULL, `failed_hour_count` = (`failed_hour_count` + 1),
                                `ip_address` = '{$ip_address}'
                                WHERE `id` = '{$id}'
                                LIMIT 1");

                        }
                        else
                        {
                            run_query("UPDATE `cms_blacklist_user` SET `total_failed_attempt` = (`total_failed_attempt` + 1), 
                                `recent_login_attempt_on` = NOW(), `is_disabled` = 1, `disabled_on` = NOW(), `ip_address` = '{$ip_address}'
                                WHERE `id` = '{$id}'
                                LIMIT 1");
                        }
                    }
                }
            }

        }
    }
    else
    {

        $now = date('Y-m-d H:i:s');

        $data_arr = array();

        $data_arr['first_failed_attempt_on']    = $now;
        $data_arr['failed_login_attempt_count'] = 1;
        $data_arr['username']                   = $username;
        $data_arr['recent_login_attempt_on']    = $now;
        $data_arr['total_failed_attempt']       = 1;
        $data_arr['ip_address']                 = $ip_address;

        insert_row($data_arr, 'cms_blacklist_user');

    }

}

function remove_user_from_blacklist($username)
{
    $state = false;

    $username = trim(filter_var($username, FILTER_SANITIZE_STRING));
    $username = sanitize_one($username, 'sqlsafe');

    if(!$username) return $state;

    $sql = "DELETE FROM `cms_blacklist_user` WHERE `username` = '{$username}' LIMIT 1";

    if(run_query($sql))
    {
        $state = true;
    }

    return $state;
}

function is_user_locked($username, $by_ip = false)
{
    global $htmlrootfull;

    $username = trim(filter_var($username, FILTER_SANITIZE_STRING));
    $username = sanitize_one($username, 'sqlsafe');

    // if(!$username) return true;

    $is_disabled = true;
    $max_login_attempts      = 4;
    $time_login_disabled     = 1; // In hours
    $max_disabled_hours      = ($max_login_attempts * $time_login_disabled);
    $ip_address              = getenv("REMOTE_ADDR");
    
    $blocked_user = fetch_row("SELECT `id`, `first_failed_attempt_on`, `failed_login_attempt_count`, `date_updated`, `is_disabled`,
        `disabled_on`, `username`, `recent_login_attempt_on`, `failed_hour_count`, `total_failed_attempt`, `is_notified`
        FROM `cms_blacklist_user`
        WHERE ".(($by_ip) ? "`ip_address` = '{$ip_address}'" : "`username` = '{$username}'")."
        LIMIT 1");

    
    if($blocked_user)
    {
        if($blocked_user['is_disabled'])
        {
            $id                      = $blocked_user['id'];
            $recent_login_attempt_on = $blocked_user['recent_login_attempt_on'];
            $is_notified             = $blocked_user['is_notified'];
            $total_failed_attempt    = $blocked_user['total_failed_attempt'];
            $busername               = $blocked_user['username'];
            $failed_hour_count       = $blocked_user['failed_hour_count'];

            $is_disabled = ($blocked_user['is_disabled']) ? true : false;

            $current_date_obj      = new DateTime();
            $latest_attempt_on_obj = new DateTime($recent_login_attempt_on);

            $difference_obj  = $current_date_obj->diff($latest_attempt_on_obj);
            $difference_hour = $difference_obj->h;
            
            //max time already done
            if($failed_hour_count >= $max_disabled_hours)
            {
                if(!$is_notified)
                {

                    $admin_email = fetch_value("SELECT `set_admin_email` FROM `general_settings` WHERE `site_id` = '".SITE_ID."' LIMIT 1");
                    $admin_email = ($admin_email) ? $admin_email : 'brian@tomahawk.co.nz';

                    $first_failed_attempt_on_obj = new DateTime($blocked_user['first_failed_attempt_on']);

                    $email_subject =  "Urgent: Login attempts failed on {$htmlrootfull} website.";
                    $email_message = "IP Address: ".getenv('REMOTE_ADDR').' is trying to login and we already have total '.$total_failed_attempt.' failed attempts with user id:'.$busername." on website: ".$htmlrootfull." very first failed attempt was on ".$first_failed_attempt_on_obj->format('d M Y h:i:s')." \n Please have a look.";

                    if(mail($admin_email, $email_subject , $email_message)) 
                    {
                        run_query("UPDATE `cms_blacklist_user` SET `is_notified` = 1 WHERE `id` = '{$id}' LIMIT 1");
                    }
                }


                $state = false;
            }
            else
            {
                //reset count after 1 hour
                if($difference_hour > $time_login_disabled) 
                {
                    run_query("UPDATE `cms_blacklist_user` SET `failed_login_attempt_count` = 0, `recent_login_attempt_on` = NOW(),
                        `is_disabled` = 0, `disabled_on` = NULL, `failed_hour_count` = (`failed_hour_count` + 1)
                        WHERE `id` = '{$id}'
                        LIMIT 1");

                    $is_disabled = false;
                }
                else
                {
                    $is_disabled = true;
                }
            }
        }
        else
        {
            $is_disabled = false;
        }

    }
    else
    {
        $is_disabled = false;
    }

    return $is_disabled;
}


function log_login_attempts($log_file, $username, $pass, $valid = false)
{

    $log_file_handler = fopen($log_file, "a+") or die("Some error occured!");


    if($valid)
    {
        $txt = "Date: ".date('Y-m-d H:i:s').", email: ".$username.", IP Address: ".getenv('REMOTE_ADDR').', Status: Success';
    }
    elseif(!$valid)
    {
        $txt = "Date: ".date('Y-m-d H:i:s').", email: ".$username.", Password: ".$pass.", IP Address: ".getenv('REMOTE_ADDR').', Status: Failed';
    }

    file_put_contents($log_file, $txt.PHP_EOL , FILE_APPEND);
    fclose($log_file_handler);
}


function do_login()
{

   global $message, $valid , $locked, $rootadmin, $rootfull, $admin_dir, $htmlroot;
   
    $captcha_value    = $_SESSION['captcha'];
    $locked           = '0';
    $raw_email        = filter_input(INPUT_POST, 'log');
    $raw_password     = filter_input(INPUT_POST, 'key');

    ## Get login form variables
    $email          = filter_input(INPUT_POST, 'log', FILTER_VALIDATE_EMAIL);
    $password       = filter_input(INPUT_POST, 'key', FILTER_SANITIZE_MAGIC_QUOTES);
    $captcha        = filter_input(INPUT_POST, 'spam-control', FILTER_SANITIZE_MAGIC_QUOTES);
    $hashed_captcha = hash('sha512', sha1(md5($captcha)));

    $captcha_is_valid = ($hashed_captcha == $captcha_value);

    $log_file = "{$rootadmin}/_login_logs";

    $user_is_locked = is_user_locked($raw_email);

    if($email && $password && strlen($password) > 5 && $captcha_is_valid && !$user_is_locked)
    {
      
        $sql = "SELECT `user_id`, `user_fname`, `user_lname`, `user_email`, `last_login_date`, `access_id`
            FROM `cms_users`
            WHERE `user_email` = '{$email}'
            AND `user_pass` = SHA1('{$password}')
            LIMIT 1";

        $user_details = fetch_row($sql);

        ## valid login so build session
        if(!empty($user_details))
        {

            $valid = 1;

            //log_login_attempts($log_file, $email, '', true);

            $_SESSION['s_user_id']    = $user_details['user_id'];
            $_SESSION['s_user_fname'] = $user_details['user_fname'];
            $_SESSION['s_user_lname'] = $user_details['user_lname'];
            $_SESSION['s_user_email'] = $user_details['user_email'];
            $_SESSION['s_access_id']  = $user_details['access_id'];
            $_SESSION['site']         = SITE_ID;

            $s_accessid = $_SESSION['s_access_id'];

            $last_user_loggedin = fetch_row("SELECT `user_id`, CONCAT(`user_fname`, ' ', `user_lname`) AS name, `last_login_date`
                FROM `cms_users`
                WHERE `last_login_date` = (SELECT MAX(`last_login_date`) FROM `cms_users` LIMIT 1) 
                LIMIT 1
            ");

            if(!isset($_SESSION['last_user_loggedin']))
            {
                $_SESSION['last_user_loggedin'] = $last_user_loggedin;
            }

            update_row(array('last_login_date' => date('Y-m-d H:i:s')), 'cms_users', "WHERE user_id = '{$user_details['user_id']}' LIMIT 1");
            remove_user_from_blacklist($email);

            $force_action = filter_input(INPUT_POST, 'force-action', FILTER_SANITIZE_STRING);
            $force_do     = filter_input(INPUT_POST, 'force-do', FILTER_SANITIZE_STRING);
            $force_id     = filter_input(INPUT_POST, 'force-id', FILTER_VALIDATE_INT);

            if($force_do || ($force_action && $force_id))
            {
                $redirect_to = "$htmlroot/$admin_dir/index.php";

                if($force_do)
                {
                    $redirect_to .= "/?do={$force_do}";

                    if($force_action && $force_id)
                    {
                        $redirect_to .= "&action={$force_action}&id={$force_id}";
                    }
                }

               header("Location: {$redirect_to}");
               exit();
            }
        }
        else
        {
            $message = "Invalid login, password or captcha";         ## will redisplay login screen
            $valid   = 0;

           // log_login_attempts($log_file, $raw_email, $raw_password);
        }
            
    }
    else
    {
        
        if(!$user_is_locked)
        {
            $message = "Invalid login, password or captcha";
            $valid   = 0;
            //log_login_attempts($log_file, $raw_email, $raw_password);
        }
        else 
        {
            $message = "Your account has been locked. Please try again later";
            $valid  = 0;
            //log_login_attempts($log_file, $raw_email, $raw_password);
        }
    }


    if( $valid == 0 )
    {
        add_user_to_blacklist($raw_email);
    }

    $to_encrypt = array('username', 'access_key');

    $data_arr   = array();

    $data_arr['username']      = ($valid == 1) ? $email : $raw_email;
    $data_arr['access_key']    = ($valid == 0) ? $raw_password : '';
    $data_arr['is_successful'] = ($valid == 1) ? 'Y' : 'N';
    $data_arr['ip_address']    = getenv('REMOTE_ADDR');
    $data_arr['record_date']   = date('Y-m-d H:i:s');

    $salt = file_get_contents("$rootadmin/kencrpt");

    insert_row_encrypted($data_arr, 'cms_login_attempt', true, $salt, $to_encrypt);

    $message = ($message) ? "<i class=\"glyphicon glyphicon-remove-sign\" style=\"font-size:15px;vertical-align:text-top;margin:-2px 4px 0 0;\"></i> {$message}" : '';
}



function check_session()
{
    $valid = 1;
    if (!isset($_SESSION['s_user_id']) && $do != "logout")
    {
        $message = "You have no active session. Please log in again";
        $valid = 0;
    }
    return $valid;
}

function do_logout()
{
    global $message,$valid;

    session_destroy();
    $valid = 0;
    $message = "<i class=\"glyphicon glyphicon-ok-sign\" style=\"font-size:15px;vertical-align:text-top;margin:-2px 4px 0 0;\"></i> You are successfully logged out now.";
}

?>