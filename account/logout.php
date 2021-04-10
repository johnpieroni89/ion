<?php
	session_start();
	setcookie("token", "", 0, "/");
	setcookie("user_id", "", 0, "/");

	session_unset();
	session_destroy();
	session_write_close();
    setcookie(session_name(),'',0,'/');
    session_regenerate_id(true);
	header("Location: ../account/login.php?logout=1");
?>