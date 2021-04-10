<?php

if(isset($_POST['activate'])){
	$db = new database;
	$db->connect();
	
	$code = mysqli_real_escape_string($db->connection,$_POST['activatecode']);
	
	$fetch_user = mysqli_query($db->connection,"SELECT * FROM users WHERE activation_code = '".$code."'");
	if(mysqli_num_rows($fetch_user) == 1){
		mysqli_query($db->connection,"UPDATE users SET status = '1' WHERE activation_code = '".$code."'");
		$_SESSION['alert'] = "<div class=\"alert alert-success\" style=\"font-size:14px;\">Your account has been activated. You may now login.</div>";
		header("Location: login.php");
	}else{
		$alert = "<div class=\"alert alert-warning\" style=\"font-size:14px;\">Invalid activation code!</div>";
	}
	
	$db->disconnect();
	unset($db);
}

?>