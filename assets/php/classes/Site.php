<?php 

class Site {
    public $name;
    public $title;
    public $description;
    public $author;
    public $footer;
    public $style;
    
    public $login_logs;
    public $max_fails;
    public $login_timeout;
    public $user_logs;
    public $machine_logs;
    
    public $account_required;
    public $app_notifications;
    public $app_mailbox;
    public $app_usergroups;
    public $app_subscription;
    public $app_subscription_metric;
    public $app_subscription_metric_abbr;
    
    function __construct() {
        global $db;
        // SET SITE VARIABLES
        $this->name = mysqli_fetch_assoc(mysqli_query($db->connection,"SELECT value FROM site_settings WHERE field = 'name'"))['value'];
        $this->title = mysqli_fetch_assoc(mysqli_query($db->connection,"SELECT value FROM site_settings WHERE field = 'title'"))['value'];
        $this->description = mysqli_fetch_assoc(mysqli_query($db->connection,"SELECT value FROM site_settings WHERE field = 'description'"))['value'];
        $this->author = mysqli_fetch_assoc(mysqli_query($db->connection,"SELECT value FROM site_settings WHERE field = 'author'"))['value'];
        $this->footer = mysqli_fetch_assoc(mysqli_query($db->connection,"SELECT value FROM site_settings WHERE field = 'footer'"))['value'];
        $this->style = mysqli_fetch_assoc(mysqli_query($db->connection,"SELECT value FROM site_settings WHERE field = 'default_style'"))['value'];
        
        $this->login_logs = mysqli_fetch_assoc(mysqli_query($db->connection,"SELECT value FROM site_settings WHERE field = 'security_user_logins_logs'"))['value'];
        $this->max_fails = mysqli_fetch_assoc(mysqli_query($db->connection,"SELECT value FROM site_settings WHERE field = 'security_user_logins_max'"))['value'];
        $this->login_timeout = mysqli_fetch_assoc(mysqli_query($db->connection,"SELECT value FROM site_settings WHERE field = 'security_user_logins_timeout'"))['value'];
        $this->user_logs = mysqli_fetch_assoc(mysqli_query($db->connection,"SELECT value FROM site_settings WHERE field = 'max_user_logs'"))['value'];
        $this->machine_logs = mysqli_fetch_assoc(mysqli_query($db->connection,"SELECT value FROM site_settings WHERE field = 'max_machine_logs'"))['value'];
        
        $this->account_required = mysqli_fetch_assoc(mysqli_query($db->connection,"SELECT value FROM site_settings WHERE field = 'account_required'"))['value'];
        $this->app_notifications = mysqli_fetch_assoc(mysqli_query($db->connection,"SELECT value FROM site_settings WHERE field = 'app_notifications'"))['value'];
        $this->app_mailbox = mysqli_fetch_assoc(mysqli_query($db->connection,"SELECT value FROM site_settings WHERE field = 'app_mailbox'"))['value'];
        $this->app_usergroups = mysqli_fetch_assoc(mysqli_query($db->connection,"SELECT value FROM site_settings WHERE field = 'app_usergroups'"))['value'];
        $this->app_subscription = mysqli_fetch_assoc(mysqli_query($db->connection,"SELECT value FROM site_settings WHERE field = 'app_subscription'"))['value'];
        $this->app_subscription_metric = mysqli_fetch_assoc(mysqli_query($db->connection,"SELECT value FROM site_settings WHERE field = 'app_subscription_metric'"))['value'];
        $this->app_subscription_metric_abbr = mysqli_fetch_assoc(mysqli_query($db->connection,"SELECT value FROM site_settings WHERE field = 'app_subscription_metric_abbr'"))['value'];
    }
}

?>