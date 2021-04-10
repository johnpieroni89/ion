<?php

if(isset($_POST['avatar_url'])){
	if(filter_var($url, FILTER_VALIDATE_URL)){
		//copy("$_FILE['']", "/tmp/file.jpeg");
	}
	$alert = "<div class=\"alert alert-danger\" style=\"font-size:14px;\"><strong>You have entered an invalid url!</strong></div>";
}

$alert = "<div class=\"alert alert-danger\" style=\"font-size:14px;\"><strong>".$_SESSION['user_avatar']."</strong></div>";

?>