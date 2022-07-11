<?php 

session_start();

class Session {
    public $avatar;
    public $alert;
    
    function __construct() {
        $this->get_avatar();
        if(isset($_SESSION['user_id'])) {
            $this->check_privs();
        }
    }
    
    function get_avatar() {
        // Check where placeholder avatar is
        if(glob("assets/img/placeholders/avatars/avatar.jpg")){
            $avatar = "assets/img/placeholders/avatars/avatar.jpg";
        }else{
            $avatar = "../assets/img/placeholders/avatars/avatar.jpg";
        }
        
        // Check if user has an avatar set
        if(isset($_SESSION['user_id'])){
            if(glob("assets/img/avatars")){
                $avatars = scandir("assets/img/avatars");
                $user_avatar = preg_grep("/".$_SESSION['user_id']."\.\.*/",$avatars);
                if(isset($user_avatar[0])){
                    $avatar = $user_avatar[0];
                }
            }else{
                $avatars = scandir("../assets/img/avatars");
                if($user_avatar = preg_grep("/".$_SESSION['user_id']."\.\.*/",$avatars)){
                    if($user_avatar[0]){
                        $avatar = $user_avatar[0];
                    }
                }
            }
        }
        
        $this->avatar = $avatar;
    }
    
    function check_alert(): void {
        if(isset($_SESSION['alert'])){
            $this->alert = $_SESSION['alert'];
            $_SESSION['alert'] = "";
        }
    }
    
    function check_privs(): void {
        global $db;
        // Check if privs need to be reset
        if (mysqli_query($db->connection,"SELECT * FROM users_privs WHERE user_id = '".$_SESSION['user_id']."'") && mysqli_fetch_assoc(mysqli_query($db->connection,"SELECT refresh FROM users WHERE user_id = '".$_SESSION['user_id']."'"))["refresh"] == 1) {
            // Set privs in session
            $groups = mysqli_query($db->connection,"SELECT usergroup_id FROM usergroups_members WHERE user_id = ".$_SESSION['user_id']."");
            $user_privs = mysqli_fetch_assoc(mysqli_query($db->connection,"SELECT * FROM users_privs WHERE user_id = '".$_SESSION['user_id']."'"));
            $_SESSION['user_privs']['admin'] = $user_privs['admin'];
            //Sentient Profiles
            $_SESSION['user_privs']['sentientprofiles_general'] = $user_privs['sentientprofiles_general'];
            $_SESSION['user_privs']['sentientprofiles_delete'] = $user_privs['sentientprofiles_delete'];
            //Galaxy Data
            $_SESSION['user_privs']['galaxydata_search'] = $user_privs['galaxydata_search'];
            $_SESSION['user_privs']['galaxydata_analytics'] = $user_privs['galaxydata_analytics'];
            $_SESSION['user_privs']['galaxydata_delete'] = $user_privs['galaxydata_delete'];
            //Geolocation
            $_SESSION['user_privs']['geolocation'] = $user_privs['geolocation'];
            //Faction Catalog
            $_SESSION['user_privs']['factioncatalog_general'] = $user_privs['factioncatalog_general'];
            $_SESSION['user_privs']['factioncatalog_delete'] = $user_privs['factioncatalog_delete'];
            //Signals Analysis
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
                    //Sentient Profiles
                    if ($group_privs['sentientprofiles_general'] > $_SESSION['user_privs']['sentientprofiles_general']) {
                        $_SESSION['user_privs']['sentientprofiles_general'] = $group_privs['sentientprofiles_general'];
                    }
                    if ($group_privs['sentientprofiles_delete'] > $_SESSION['user_privs']['sentientprofiles_delete']) {
                        $_SESSION['user_privs']['sentientprofiles_delete'] = $group_privs['sentientprofiles_delete'];
                    }
                    //Galaxy Data
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
                    //Signals Analysis
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
                    //Faction Catalog
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
            mysqli_query($db->connection,"UPDATE users SET refresh = '0' WHERE user_id = '".$_SESSION['user_id']."'");
        }
    }
    
    function check_login(): void {
        global $site;
        if($site->account_required == 1){
            if(!isset($_SESSION['user_id'])){
                if(glob("account/login.php")){
                    header("Location: account/login.php");
                }else{
                    header("Location: ../account/login.php");
                }
            }
        }
    }
}

?>