<?php
error_reporting(0);
$db = new database;
$db->connect();

session_start();

// CHECK COOKIES
/*
if(isset($_COOKIE["user_id"])){
	if($_COOKIE["user_id"] && $_COOKIE['token'] && !$_SESSION['user_id']){
		$acct_check = mysqli_fetch_assoc(mysqli_query($db->connection,"SELECT * FROM users WHERE user_id = '".$_COOKIE["user_id"]."'"));
		if(hash("sha256",$acct_check['username'] + $acct_check['email']) == $_COOKIE['token']){
			// SET SESSION VARIABLES
			$_SESSION['user_id'] = $acct_check['user_id'];
			$_SESSION['username'] = $acct_check['username'];
			$_SESSION['email'] = $acct_check['email'];
			$_SESSION['client_ip'] = mysqli_real_escape_string($db->connection,$_SERVER['REMOTE_ADDR']);
			$_SESSION['client_user_agent'] = mysqli_real_escape_string($db->connection,$_SERVER['HTTP_USER_AGENT']);
			$last_login_ip = mysqli_fetch_assoc(mysqli_query($db->connection,"SELECT ip FROM users_login WHERE user_id = '".$_SESSION['user_id']."' ORDER BY timestamp DESC LIMIT 1"))['ip'];
			$last_login_country = mysqli_fetch_assoc(mysqli_query($db->connection,"SELECT country FROM users_login WHERE user_id = '".$_SESSION['user_id']."' ORDER BY timestamp DESC LIMIT 1"))['country'];
			if($last_login_ip != $_SESSION['client_ip']){
				$_SESSION['country'] = mysqli_real_escape_string($db->connection,geolocate($_SERVER['REMOTE_ADDR']));
			}else{
				$_SESSION['country'] = $last_login_country;
			}
			if($_SESSION['country'] == ""){$_SESSION['country'] = "N/A";}
			if($last_login_country != $_SESSION['country']){ mysqli_query($db->connection,"INSERT INTO logs (event_type, user_id, ip, details) VALUES ('2', ".$acct_check['user_id'].", '".$_SESSION['client_ip']."', 'New Geolocation (".$_SESSION['country'].")')"); }
		}
	}
}*/

// SET SITE VARIABLES
$_SESSION['site_name'] = mysqli_fetch_assoc(mysqli_query($db->connection,"SELECT value FROM site_settings WHERE field = 'name'"))['value'];
$_SESSION['site_title'] = mysqli_fetch_assoc(mysqli_query($db->connection,"SELECT value FROM site_settings WHERE field = 'title'"))['value'];
$_SESSION['site_description'] = mysqli_fetch_assoc(mysqli_query($db->connection,"SELECT value FROM site_settings WHERE field = 'description'"))['value'];
$_SESSION['site_author'] = mysqli_fetch_assoc(mysqli_query($db->connection,"SELECT value FROM site_settings WHERE field = 'author'"))['value'];
$_SESSION['site_footer'] = mysqli_fetch_assoc(mysqli_query($db->connection,"SELECT value FROM site_settings WHERE field = 'footer'"))['value'];
$_SESSION['site_style'] = mysqli_fetch_assoc(mysqli_query($db->connection,"SELECT value FROM site_settings WHERE field = 'default_style'"))['value'];

$_SESSION['site_login_logs'] = mysqli_fetch_assoc(mysqli_query($db->connection,"SELECT value FROM site_settings WHERE field = 'security_user_logins_logs'"))['value'];
$_SESSION['site_max_fails'] = mysqli_fetch_assoc(mysqli_query($db->connection,"SELECT value FROM site_settings WHERE field = 'security_user_logins_max'"))['value'];
$_SESSION['site_login_timeout'] = mysqli_fetch_assoc(mysqli_query($db->connection,"SELECT value FROM site_settings WHERE field = 'security_user_logins_timeout'"))['value'];
$_SESSION['max_user_logs'] = mysqli_fetch_assoc(mysqli_query($db->connection,"SELECT value FROM site_settings WHERE field = 'max_user_logs'"))['value'];
$_SESSION['max_machine_logs'] = mysqli_fetch_assoc(mysqli_query($db->connection,"SELECT value FROM site_settings WHERE field = 'max_machine_logs'"))['value'];

$_SESSION['site_account_required'] = mysqli_fetch_assoc(mysqli_query($db->connection,"SELECT value FROM site_settings WHERE field = 'account_required'"))['value'];
$_SESSION['app_notifications'] = mysqli_fetch_assoc(mysqli_query($db->connection,"SELECT value FROM site_settings WHERE field = 'app_notifications'"))['value'];
$_SESSION['app_mailbox'] = mysqli_fetch_assoc(mysqli_query($db->connection,"SELECT value FROM site_settings WHERE field = 'app_mailbox'"))['value'];
$_SESSION['app_usergroups'] = mysqli_fetch_assoc(mysqli_query($db->connection,"SELECT value FROM site_settings WHERE field = 'app_usergroups'"))['value'];
$_SESSION['app_subscription'] = mysqli_fetch_assoc(mysqli_query($db->connection,"SELECT value FROM site_settings WHERE field = 'app_subscription'"))['value'];
$_SESSION['app_subscription_metric'] = mysqli_fetch_assoc(mysqli_query($db->connection,"SELECT value FROM site_settings WHERE field = 'app_subscription_metric'"))['value'];
$_SESSION['app_subscription_metric_abbr'] = mysqli_fetch_assoc(mysqli_query($db->connection,"SELECT value FROM site_settings WHERE field = 'app_subscription_metric_abbr'"))['value'];

// Check_avatar
if(glob("assets/img/placeholders/avatars/avatar.jpg")){
	$_SESSION['user_avatar'] = "assets/img/placeholders/avatars/avatar.jpg";
}else{
	$_SESSION['user_avatar'] = "../assets/img/placeholders/avatars/avatar.jpg";
}

if($_SESSION['user_id']){
	if(glob("assets/img/avatars")){
		$avatars = scandir("assets/img/avatars");
		$user_avatar = preg_grep("/".$_SESSION['user_id']."\.\.*/",$avatars);
		if(isset($user_avatar[0])){
			$_SESSION['user_avatar'] = $user_avatar[0];
		}
	}else{
		$avatars = scandir("../assets/img/avatars");
		if($user_avatar = preg_grep("/".$_SESSION['user_id']."\.\.*/",$avatars)){
			if($user_avatar[0]){
				$_SESSION['user_avatar'] = $user_avatar[0];
			}
		}
	}
}


//If the remote host ip address is not the same as the logged in user's session ip addrss, then end session
/*
if(isset($_SESSION['user_id'])){
	if($_SERVER['REMOTE_ADDR'] != $_SESSION['client_ip']){
		if(glob("account/logout.php")){
			header("Location: account/logout.php");
		}elseif(glob("logout.php")){
			header("Location: logout.php");
		}else{
			header("Location: ../account/logout.php");
		}
	}
}
*/

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
	
// Set user variables
$_SESSION['user_subscription_current'] = 75;
$_SESSION['user_subscription_max'] = 100;

if(isset($_SESSION['alert'])){
	$alert = $_SESSION['alert'];
	$_SESSION['alert'] = "";
}

$db->disconnect();
unset($db);

?>