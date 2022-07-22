<?php 

include('config.php');
include('assets/php/classes/Database.php');

$db = new Database();
$db->connect();

include('assets/php/classes/Site.php');
$site = new Site();

include('assets/php/classes/Session.php');
$session = new Session();
$session->check_alert();
if(isset($_SESSION['user_id'])) {
    $session->check_privs();
}

include('assets/php/classes/Account.php');
$account = new Account();

include('assets/php/classes/Utility.php');
include('assets/php/classes/Analytics.php');
include('assets/php/classes/SwcApiProcessor.php');
include('assets/php/functions.php');
include('assets/php/paginator.php');

include('oauth/AuthorizationResult.php');
include('oauth/ContentTypes.php');
include('oauth/GrantTypes.php');
include('oauth/OAuthToken.php');
include('oauth/RequestMethod.php');
include('oauth/SWC.php');
include('oauth/SWCombineWSException.php');

?>