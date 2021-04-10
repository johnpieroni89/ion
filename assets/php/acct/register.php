<?php

if(isset($_POST['register'])){
	$db = new database;
	$db->connect();
	
	$username = mysqli_real_escape_string($db->connection,$_POST['register-username']);
	$firstname = mysqli_real_escape_string($db->connection,$_POST['register-firstname']);
	$lastname = mysqli_real_escape_string($db->connection,$_POST['register-lastname']);
	$email = mysqli_real_escape_string($db->connection,$_POST['register-email']);
	$password_salt = mysqli_real_escape_string($db->connection,randgen(32));
	$password_hash = hash("sha256","".$password_pepper."".mysqli_real_escape_string($db->connection,$_POST['register-password'])."".$password_salt."");
	$password_hash_verify = hash("sha256","".$password_pepper."".mysqli_real_escape_string($db->connection,$_POST['register-password-verify'])."".$password_salt."");
	
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
	
	$db->disconnect();
	unset($db);
}
?>