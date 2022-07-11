<?php 
    include("autoload.php");
    global $db;
    global $site;
    global $session;
    global $account;
    $session->check_login();
	
	if(isset($_POST['submit'])){
		$firstname = mysqli_real_escape_string($db->connection,$_POST['firstname']);
		$lastname = mysqli_real_escape_string($db->connection,$_POST['lastname']);
		$email = mysqli_real_escape_string($db->connection,$_POST['email']);
		mysqli_query($db->connection,"UPDATE users SET first_name = '$firstname', last_name = '$lastname', email = '$email' WHERE user_id = '".$_SESSION['user_id']."'");
		
		$pass = mysqli_real_escape_string($db->connection,$_POST['pass']);
		$passconfirm = mysqli_real_escape_string($db->connection,$_POST['passconfirm']);
		if($pass != "" && $passconfirm != ""){
			if($pass == $passconfirm){
				$password_salt = mysqli_real_escape_string($db->connection,randgen(32));
				$password_hash = hash("sha256","".PASSWORD_PEPPER."".mysqli_real_escape_string($db->connection,$pass)."".$password_salt."");
				mysqli_query($db->connection,"UPDATE users SET password_hash = '$password_hash', password_salt = '$password_salt' WHERE user_id = '".$_SESSION['user_id']."'");
				$session->alert = "<div class=\"alert alert-success\" style=\"font-size:14px;\">Password has been changed.</div>";
			}else{
				$session->alert = "<div class=\"alert alert-warning\" style=\"font-size:14px;\">Passwords don't match, password was not updated.</div>";
			}
		}
		
		$session->alert = $session->alert."<div class=\"alert alert-success\" style=\"font-size:14px;\">Successfully modified account information</div>";
	}
	
	$user_data = mysqli_fetch_assoc(mysqli_query($db->connection, "SELECT * FROM users WHERE user_id='".$_SESSION['user_id']."'"));
?>

	<?php include("../assets/php/_head.php"); ?>
    <body>
        <!-- Page Wrapper -->
        <div id="page-wrapper" class="page-loading">
            <?php include("../assets/php/_preloader.php"); ?>
			<!-- Page Container -->
            <div id="page-container" class="header-fixed-top sidebar-visible-lg-full">
                
                <?php include("../assets/php/_sidebar-alt.php"); ?>
                <?php include("../assets/php/_sidebar.php"); ?>

                <!-- Main Container -->
                <div id="main-container">
                    <?php include("../assets/php/_header.php"); ?>

                    <!-- Page content -->
                    <div id="page-content" style="overflow:auto;">
                        <!-- Page Header -->
                        <div class="content-header">
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="header-section">
                                        <h1>Profile</h1>
                                    </div>
                                </div>
                                <div class="col-sm-6 hidden-xs">
                                    <div class="header-section">
                                        <ul class="breadcrumb breadcrumb-top">
                                            <li>Account</li>
                                            <li><a href="profile.php">Profile</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
						<?php if(isset($session->alert)){echo $session->alert;} ?>

                        <!-- Block -->
                        <div class="block col-xs-12 col-sm-12 col-md-12 col-lg-12">
                            <!-- Title -->
							<div class="block-title">
                                <h2><?php echo $user_data['username'];?></h2>
                            </div>
							<form class="form-horizontal" role="form" method="post" action="profile.php">
                            <div class="modal-body">
								<div class="form-group">
                                    <label class="col-sm-3" for="inputUser">User Id</label>
                                    <div class="col-sm-9">
                                        <input class="form-control" type="text" name="user_id" value="<?php echo $user_data['user_id'];?>" disabled>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-3" for="inputUser">Username</label>
                                    <div class="col-sm-9">
                                        <input class="form-control" type="text" name="username" value="<?php echo $user_data['username'];?>" disabled>
                                    </div>
                                </div>
								<div class="form-group">
                                    <label class="col-sm-3" for="inputUser">First Name</label>
                                    <div class="col-sm-9">
                                        <input class="form-control" type="text" name="firstname" value="<?php echo $user_data['first_name'];?>">
                                    </div>
                                </div>
								<div class="form-group">
                                    <label class="col-sm-3" for="inputUser">Last Name</label>
                                    <div class="col-sm-9">
                                        <input class="form-control" type="text" name="lastname" value="<?php echo $user_data['last_name'];?>">
                                    </div>
                                </div>
								<div class="form-group">
                                    <label class="col-sm-3" for="inputUser">Email</label>
                                    <div class="col-sm-9">
                                        <input class="form-control" type="text" name="email" value="<?php echo $user_data['email'];?>">
                                    </div>
                                </div>
								<div class="form-group">
                                    <label class="col-sm-3" for="inputUser">Registration Date</label>
                                    <div class="col-sm-9">
                                        <input class="form-control" type="text" name="registration" value="<?php echo $user_data['date_register'];?>" disabled>
                                    </div>
                                </div>
								<div class="form-group">
                                    <label class="col-sm-3" for="inputUser">Status</label>
                                    <div class="col-sm-9">
                                        <select class="form-control" name="status" disabled>
										<?php
											if($user_data['status'] == 0){ echo "<option value=\"0\" selected>Registered</option>";}else{echo "<option value=\"0\">Registered</option>";}
											if($user_data['status'] == 1){ echo "<option value=\"1\" selected>Activated</option>";}else{echo "<option value=\"1\">Activated</option>";}
											if($user_data['status'] == 2){ echo "<option value=\"2\" selected>Banned</option>";}else{echo "<option value=\"2\">Banned</option>";}
										?>
										</select>
                                    </div>
                                </div>
								<hr/>
								<div class="form-group">
                                    <label class="col-sm-3" for="inputUser">Enter Password</label>
                                    <div class="col-sm-9">
                                        <input class="form-control" type="text" name="pass" value="" placeholder="Only fill if changing password">
                                    </div>
                                </div>
								<div class="form-group">
                                    <label class="col-sm-3" for="inputUser">Confirm Password</label>
                                    <div class="col-sm-9">
                                        <input class="form-control" type="text" name="passconfirm" value="" placeholder="Only fill if changing password">
                                    </div>
                                </div>
								<input id="submit" class="form-control btn btn-primary" name="submit" type="submit" value="Submit">
                            </div>
                        </form>
                        </div>
						
                    </div>
                </div>
            </div>
        </div>
		<?php include("../assets/php/_end.php"); ?>
	</body>
</html>