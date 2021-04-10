<?php

if($_SESSION['site_account_required'] == 1){
	if(!isset($_SESSION['user_id'])){
		if(glob("account/login.php")){
			header("Location: account/login.php");
		}else{
			header("Location: ../account/login.php");
		}
		
	}
}

?>