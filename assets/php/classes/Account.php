<?php 

class Account {
    function activate() {
        global $db;
        
        $code = mysqli_real_escape_string($db->connection,$_POST['activatecode']);
        
        $fetch_user = mysqli_query($db->connection,"SELECT * FROM users WHERE activation_code = '".$code."'");
        if(mysqli_num_rows($fetch_user) == 1){
            mysqli_query($db->connection,"UPDATE users SET status = '1' WHERE activation_code = '".$code."'");
            $_SESSION['alert'] = "<div class=\"alert alert-success\" style=\"font-size:14px;\">Your account has been activated. You may now login.</div>";
            header("Location: login.php");
        }else{
            $alert = "<div class=\"alert alert-warning\" style=\"font-size:14px;\">Invalid activation code!</div>";
        }
    }
    
    function register() {
        global $db;
        
        $username = mysqli_real_escape_string($db->connection,$_POST['register-username']);
        $firstname = mysqli_real_escape_string($db->connection,$_POST['register-firstname']);
        $lastname = mysqli_real_escape_string($db->connection,$_POST['register-lastname']);
        $email = mysqli_real_escape_string($db->connection,$_POST['register-email']);
        $password_salt = mysqli_real_escape_string($db->connection,randgen(32));
        $password_hash = hash("sha256","".PASSWORD_PEPPER."".mysqli_real_escape_string($db->connection,$_POST['register-password'])."".$password_salt."");
        $password_hash_verify = hash("sha256","".PASSWORD_PEPPER."".mysqli_real_escape_string($db->connection,$_POST['register-password-verify'])."".$password_salt."");
        
        if(mysqli_num_rows(mysqli_query($db->connection,"SELECT * FROM users WHERE username = '".$username."'")) == 0) { //Check for unique username
            if(mysqli_num_rows(mysqli_query($db->connection,"SELECT * FROM users WHERE email = '".$email."'")) == 0) { //Check for unique email
                if(isset($username) && isset($firstname) && isset($lastname) && filter_var($email, FILTER_VALIDATE_EMAIL) ){ //VALIDATE INPUT
                    if($password_hash == $password_hash_verify){
                        $activation_code = hash("md5","".$username."".$firstname."".$lastname."".$email."".date()."");
                        mysqli_query($db->connection,"INSERT INTO users (username, first_name, last_name, email, password_hash, password_salt, activation_code) VALUES ('$username', '$firstname', '$lastname', '$email', '$password_hash', '$password_salt', '$activation_code')");
                        $user_id = mysqli_fetch_assoc(mysqli_query($db->connection,"SELECT user_id FROM users WHERE username = '$username'"))["user_id"];
                        mysqli_query($db->connection,"INSERT INTO users_privs (user_id) VALUES (".$user_id.")");
                        include("../mail/mail_registration.php");
                        $_SESSION['alert'] = "<div class=\"alert alert-success\" style=\"font-size:14px;\"><strong>Registration complete.</strong> Check your email or contact and administrator to activate your account.</div>";
                        //$_SESSION['activation_code'] = $activation_code;
                        header("Location: activate.php");
                    }else{
                        $_SESSION['alert'] = "<div class=\"alert alert-warning\" style=\"font-size:14px;\">Password Verification failed. Make sure the passwords match.</div>";
                    }
                }else{
                    $_SESSION['alert'] = "<div class=\"alert alert-danger\" style=\"font-size:14px;\">An error has occurred!</div>";
                }
            }else{
                $_SESSION['alert'] = "<div class=\"alert alert-danger\" style=\"font-size:14px;\">That email is already in use.</div>";
            }
        }else{
            $_SESSION['alert'] = "<div class=\"alert alert-danger\" style=\"font-size:14px;\">That username is already in use.</div>";
        }
    }
    
    function login() {
        global $db;
        
        $id = mysqli_real_escape_string($db->connection,$_POST['login-id']);
        if(filter_var($id, FILTER_VALIDATE_EMAIL)){
            $acct_check = mysqli_query($db->connection,"SELECT * FROM users WHERE email = '".$id."'");
        }else{
            $acct_check = mysqli_query($db->connection,"SELECT * FROM users WHERE username = '".$id."'");
        }
        if(mysqli_num_rows($acct_check) == 1){ // Account Exists
            $acct_check = mysqli_fetch_assoc($acct_check);
            $password_salt = $acct_check['password_salt'];
            $password_hash = hash("sha256","".PASSWORD_PEPPER."".mysqli_real_escape_string($db->connection,$_POST['login-password'])."".$password_salt."");
            $password_hash_verify = $acct_check['password_hash'];
            
            $security_user_logins_timeout = mysqli_fetch_assoc(mysqli_query($db->connection,"SELECT value FROM site_settings WHERE field = 'security_user_logins_timeout'"))['value'];
            $starttime = time() - $security_user_logins_timeout;
            $failed_logins = mysqli_query($db->connection,"SELECT * FROM logs WHERE user_id = ".$acct_check['user_id']." AND event_type = '2' AND details = 'Failed Login' AND timestamp >= FROM_UNIXTIME(".$starttime.") ORDER BY timestamp DESC");
            $login_timeout = mysqli_query($db->connection,"SELECT * FROM logs WHERE user_id = ".$acct_check['user_id']." AND event_type = '2' AND details = 'Login Timeout' AND timestamp >= FROM_UNIXTIME(".$starttime.") ORDER BY timestamp DESC");
            $security_user_logins_max = mysqli_fetch_assoc(mysqli_query($db->connection,"SELECT value FROM site_settings WHERE field = 'security_user_logins_max'"))['value'];
            
            if(mysqli_num_rows($failed_logins) < $security_user_logins_max){
                if(mysqli_num_rows($login_timeout) == 0){
                    if($password_hash == $password_hash_verify){ // Login Success
                        if($acct_check['status'] == 1){
                            //Check subscription status
                            $subscription_check = mysqli_fetch_assoc(mysqli_query($db->connection,"SELECT * FROM users_subscription WHERE user_id = '".$acct_check['user_id']."'"));
                            if($subscription_check == TRUE && $subscription_check['days'] <= 0){
                                $alert = "<div class=\"alert alert-warning\" style=\"font-size:14px;\">That account has an expired subscription.</div>";
                            }else{
                                // Set Session Variables
                                $_SESSION['user_id'] = $acct_check['user_id'];
                                $_SESSION['username'] = $acct_check['username'];
                                $_SESSION['email'] = $acct_check['email'];
                                $_SESSION['client_ip'] = mysqli_real_escape_string($db->connection,$_SERVER['REMOTE_ADDR']);
                                $_SESSION['client_user_agent'] = mysqli_real_escape_string($db->connection,$_SERVER['HTTP_USER_AGENT']);
                                
                                $verify_ip = mysqli_fetch_assoc(mysqli_query($db->connection,"SELECT ip FROM logs WHERE user_id = '".$_SESSION['user_id']."' AND ip = '".$_SESSION['client_ip']."' ORDER BY timestamp DESC LIMIT 1"))['ip'];
                                $_SESSION['country'] = mysqli_real_escape_string($db->connection,geolocate($_SERVER['REMOTE_ADDR']));
                                if($_SESSION['country'] == ""){$_SESSION['country'] = "N/A";}
                                if($verify_ip == ""){
                                    mysqli_query($db->connection,"INSERT INTO logs (event_type, user_id, ip, details) VALUES ('2', ".$acct_check['user_id'].", '".$_SESSION['client_ip']."', 'New Geolocation (".$_SESSION['country'].")')");
                                }
                                
                                mysqli_query($db->connection,"INSERT INTO logs (event_type, user_id, ip, details) VALUES ('0', ".$_SESSION['user_id'].", '".$_SESSION['client_ip']."', 'Login Success from ".$_SESSION['country']."<br/>Useragent (".$_SESSION['client_user_agent'].")')");
                                
                                $security_user_logins_logs = mysqli_fetch_assoc(mysqli_query($db->connection,"SELECT value FROM site_settings WHERE field = 'security_user_logins_logs'"))['value'];
                                
                                if(mysqli_num_rows(mysqli_query($db->connection,"SELECT * FROM users_login WHERE user_id = ".$_SESSION['user_id']."")) > $security_user_logins_logs){
                                    mysqli_query($db->connection,"DELETE FROM users_login WHERE user_id = ".$_SESSION['user_id']." ORDER BY timestamp ASC LIMIT 1");
                                }
                                if($_POST['login-remember-me']){
                                    $cookie_values = mysqli_fetch_assoc(mysqli_query($db->connection,"SELECT user_id, username, email FROM users WHERE user_id = ".$acct_check['user_id'].""));
                                    setcookie("token", hash("sha256",$cookie_values['username'] + $cookie_values['email']), time() + (86400 * 30), "/");
                                    setcookie("user_id", $cookie_values['user_id'], time() + (86400 * 30), "/");
                                }
                                
                                // Set privs in session
                                $groups = mysqli_query($db->connection,"SELECT usergroup_id FROM usergroups_members WHERE user_id = ".$_SESSION['user_id']."");
                                $user_privs = mysqli_fetch_assoc(mysqli_query($db->connection,"SELECT * FROM users_privs WHERE user_id = '".$_SESSION['user_id']."'"));
                                $_SESSION['user_privs']['admin'] = $user_privs['admin'];
                                //Sentient Profiles [FRACTAL HARMONY]
                                $_SESSION['user_privs']['sentientprofiles_general'] = $user_privs['sentientprofiles_general'];
                                $_SESSION['user_privs']['sentientprofiles_delete'] = $user_privs['sentientprofiles_delete'];
                                //Galaxy Data [MORPHIC GEODE]
                                $_SESSION['user_privs']['galaxydata_search'] = $user_privs['galaxydata_search'];
                                $_SESSION['user_privs']['galaxydata_analytics'] = $user_privs['galaxydata_analytics'];
                                $_SESSION['user_privs']['galaxydata_delete'] = $user_privs['galaxydata_delete'];
                                //Geolocation
                                $_SESSION['user_privs']['geolocation'] = $user_privs['geolocation'];
                                //Faction Catalog [SPLENDID CUBE]
                                $_SESSION['user_privs']['factioncatalog_general'] = $user_privs['factioncatalog_general'];
                                $_SESSION['user_privs']['factioncatalog_delete'] = $user_privs['factioncatalog_delete'];
                                //Signals Analysis [RIGID HARBOR]
                                $_SESSION['user_privs']['signalsanalysis_search'] = $user_privs['signalsanalysis_search'];
                                $_SESSION['user_privs']['signalsanalysis_upload'] = $user_privs['signalsanalysis_upload'];
                                $_SESSION['user_privs']['signalsanalysis_export'] = $user_privs['signalsanalysis_export'];
                                $_SESSION['user_privs']['signalsanalysis_analytics'] = $user_privs['signalsanalysis_analytics'];
                                $_SESSION['user_privs']['signalsanalysis_delete'] = $user_privs['signalsanalysis_delete'];
                                //Reporting
                                $_SESSION['user_privs']['reporting_publish'] = $user_privs['reporting_publish'];
                                $_SESSION['user_privs']['reporting_view'] = $user_privs['reporting_view'];
                                $_SESSION['user_privs']['reporting_delete'] = $user_privs['reporting_delete'];
                                //Transactions
                                $_SESSION['user_privs']['transactions'] = $user_privs['transactions'];
                                //Flashnews
                                $_SESSION['user_privs']['flashnews'] = $user_privs['flashnews'];
                                if (mysqli_num_rows($groups) != 0) {
                                    $group_ids = mysqli_fetch_array($groups, MYSQLI_NUM);
                                    for ($i = 0; $i < count($group_ids); $i++) {
                                        $group_privs = mysqli_fetch_assoc(mysqli_query($db->connection,"SELECT * FROM usergroups_privs WHERE group_id = '".$group_ids[$i]."'"));
                                        if ($group_privs['admin'] > $_SESSION['user_privs']['admin']) {
                                            $_SESSION['user_privs']['admin'] = $group_privs['admin'];
                                        }
                                        //Sentient Profiles [FRACTAL HARMONY]
                                        if ($group_privs['sentientprofiles_general'] > $_SESSION['user_privs']['sentientprofiles_general']) {
                                            $_SESSION['user_privs']['sentientprofiles_general'] = $group_privs['sentientprofiles_general'];
                                        }
                                        if ($group_privs['sentientprofiles_delete'] > $_SESSION['user_privs']['sentientprofiles_delete']) {
                                            $_SESSION['user_privs']['sentientprofiles_delete'] = $group_privs['sentientprofiles_delete'];
                                        }
                                        //Galaxy Data [MORPHIC GEODE]
                                        if ($group_privs['galaxydata_search'] > $_SESSION['user_privs']['galaxydata_search']) {
                                            $_SESSION['user_privs']['galaxydata_search'] = $group_privs['galaxydata_search'];
                                        }
                                        if ($group_privs['galaxydata_analytics'] > $_SESSION['user_privs']['galaxydata_analytics']) {
                                            $_SESSION['user_privs']['galaxydata_analytics'] = $group_privs['galaxydata_analytics'];
                                        }
                                        if ($group_privs['galaxydata_delete'] > $_SESSION['user_privs']['galaxydata_delete']) {
                                            $_SESSION['user_privs']['galaxydata_delete'] = $group_privs['galaxydata_delete'];
                                        }
                                        //Geolocation Data
                                        if ($group_privs['geolocation'] > $_SESSION['user_privs']['geolocation']) {
                                            $_SESSION['user_privs']['geolocation'] = $group_privs['geolocation'];
                                        }
                                        //Signals Analysis [RIGID HARBOR]
                                        if ($group_privs['signalsanalysis_search'] > $_SESSION['user_privs']['signalsanalysis_search']) {
                                            $_SESSION['user_privs']['signalsanalysis_search'] = $group_privs['signalsanalysis_search'];
                                        }
                                        if ($group_privs['signalsanalysis_upload'] > $_SESSION['user_privs']['signalsanalysis_upload']) {
                                            $_SESSION['user_privs']['signalsanalysis_upload'] = $group_privs['signalsanalysis_upload'];
                                        }
                                        if ($group_privs['signalsanalysis_export'] > $_SESSION['user_privs']['signalsanalysis_export']) {
                                            $_SESSION['user_privs']['signalsanalysis_export'] = $group_privs['signalsanalysis_export'];
                                        }
                                        if ($group_privs['signalsanalysis_analytics'] > $_SESSION['user_privs']['signalsanalysis_analytics']) {
                                            $_SESSION['user_privs']['signalsanalysis_analytics'] = $group_privs['signalsanalysis_analytics'];
                                        }
                                        if ($group_privs['signalsanalysis_delete'] > $_SESSION['user_privs']['signalsanalysis_delete']) {
                                            $_SESSION['user_privs']['signalsanalysis_delete'] = $group_privs['signalsanalysis_delete'];
                                        }
                                        //Faction Catalog [SPLENDID CUBE]
                                        if ($group_privs['factioncatalog_general'] > $_SESSION['user_privs']['factioncatalog_general']) {
                                            $_SESSION['user_privs']['factioncatalog_general'] = $group_privs['factioncatalog_general'];
                                        }
                                        if ($group_privs['factioncatalog_delete'] > $_SESSION['user_privs']['factioncatalog_delete']) {
                                            $_SESSION['user_privs']['factioncatalog_delete'] = $group_privs['factioncatalog_delete'];
                                        }
                                        //Reporting
                                        if ($group_privs['reporting_publish'] > $_SESSION['user_privs']['reporting_publish']) {
                                            $_SESSION['user_privs']['reporting_publish'] = $group_privs['reporting_publish'];
                                        }
                                        if ($group_privs['reporting_view'] > $_SESSION['user_privs']['reporting_view']) {
                                            $_SESSION['user_privs']['reporting_view'] = $group_privs['reporting_view'];
                                        }
                                        if ($group_privs['reporting_delete'] > $_SESSION['user_privs']['reporting_delete']) {
                                            $_SESSION['user_privs']['reporting_delete'] = $group_privs['reporting_delete'];
                                        }
                                        //Transactions
                                        if ($group_privs['transactions'] > $_SESSION['user_privs']['transactions']) {
                                            $_SESSION['user_privs']['transactions'] = $group_privs['transactions'];
                                        }
                                        //Flashnews
                                        if ($group_privs['flashnews'] > $_SESSION['user_privs']['flashnews']) {
                                            $_SESSION['user_privs']['flashnews'] = $group_privs['flashnews'];
                                        }
                                    }
                                }
                                
                                header("Location: ../index.php");
                            }
                        }else{
                            $alert = "<div class=\"alert alert-warning\" style=\"font-size:14px;\">That account is currently disabled.</div>";
                        }
                    }else{ // Login Failure
                        $alert = "<div class=\"alert alert-warning\" style=\"font-size:14px;\">Password Verification failed.</div>";
                        mysqli_query($db->connection,"INSERT INTO logs (event_type, user_id, ip, details) VALUES ('2', ".$acct_check['user_id'].", '".mysqli_real_escape_string($db->connection,$_SERVER['REMOTE_ADDR'])."', 'Failed Login')");
                    }
                }else{
                    $locktime = strtotime(mysqli_fetch_assoc($login_timeout)['timestamp']);
                    $unlocktime = $locktime + $security_user_logins_timeout;
                    $duration = $unlocktime - time();
                    $alert = "<div class=\"alert alert-danger\" style=\"font-size:14px;\">The account is temporarily locked. You may attempt to login in ".$duration." seconds.</div>";
                }
            }else{
                if(mysqli_num_rows($login_timeout) == 0){mysqli_query($db->connection,"INSERT INTO logs (event_type, user_id, ip, details) VALUES ('2', ".$acct_check['user_id'].", '".mysqli_real_escape_string($db->connection,$_SERVER['REMOTE_ADDR'])."', 'Login Timeout')");}
                $locktime = strtotime(mysqli_fetch_assoc($login_timeout)['timestamp']);
                $unlocktime = $locktime + $security_user_logins_timeout;
                $duration = $unlocktime - time();
                $alert = "<div class=\"alert alert-danger\" style=\"font-size:14px;\">The account is temporarily locked. You may attempt to login in ".$duration." seconds.</div>";
            }
        }else{ // Account Does Not Exist
            $alert = "<div class=\"alert alert-danger\" style=\"font-size:14px;\">No account is associated with that email address!</div>";
        }
    }
    
    function set_avatar() {
        if(isset($_POST['avatar_url'])){
            if(filter_var($url, FILTER_VALIDATE_URL)){
                //copy("$_FILE['']", "/tmp/file.jpeg");
            }
            $session->alert = "<div class=\"alert alert-danger\" style=\"font-size:14px;\"><strong>You have entered an invalid url!</strong></div>";
        }
        
        $session->alert = "<div class=\"alert alert-danger\" style=\"font-size:14px;\"><strong>".$_SESSION['user_avatar']."</strong></div>";
    }
}

?>