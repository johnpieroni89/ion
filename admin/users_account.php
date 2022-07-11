<?php
    include("../autoload.php");
    global $db;
    global $site;
    global $session;
    global $account;
    $session->check_login();
	if(!isset($_SESSION['user_id']) || $_SESSION['user_privs']['admin'] == 0){ header("Location: ../index.php");}
	
	if(isset($_GET['user_id']) && $_GET['user_id'] != ""){
		$user_id = mysqli_real_escape_string($db->connection,$_GET['user_id']);
		
		if($_POST){
			$username = mysqli_real_escape_string($db->connection,$_POST['username']);
			$firstname = mysqli_real_escape_string($db->connection,$_POST['firstname']);
			$lastname = mysqli_real_escape_string($db->connection,$_POST['lastname']);
			$email = mysqli_real_escape_string($db->connection,$_POST['email']);
			$status = mysqli_real_escape_string($db->connection,$_POST['status']);
			mysqli_query($db->connection,"UPDATE users SET username = '$username', first_name = '$firstname', last_name = '$lastname', email = '$email', status = '$status' WHERE user_id = '$user_id'");
			
			$pass = mysqli_real_escape_string($db->connection,$_POST['pass']);
			$passconfirm = mysqli_real_escape_string($db->connection,$_POST['passconfirm']);
			if($pass != "" && $passconfirm != ""){
				if($pass == $passconfirm){
					$password_salt = mysqli_real_escape_string($db->connection,randgen(32));
					$password_hash = hash("sha256","".PASSWORD_PEPPER."".mysqli_real_escape_string($db->connection,$pass)."".$password_salt."");
					mysqli_query($db->connection,"UPDATE users SET password_hash = '$password_hash', password_salt = '$password_salt' WHERE user_id = '$user_id'");
					$session->alert = "<div class=\"alert alert-success\" style=\"font-size:14px;\">Password has been changed.</div>";
				}else{
					$session->alert = "<div class=\"alert alert-warning\" style=\"font-size:14px;\">Passwords don't match, password was not updated.</div>";
				}
			}
			
			$session->alert = $session->alert."<div class=\"alert alert-success\" style=\"font-size:14px;\">Successfully modified the account for ".$username."</div>";
		}
		
		$user = mysqli_fetch_assoc(mysqli_query($db->connection,"SELECT * FROM users WHERE user_id = '".$user_id."'"));
		if(count($user) == 0){header("Location: users.php");}
		
	}else{
		header("Location: users.php");
	}
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
                                        <h1>Users - Account</h1>
                                    </div>
                                </div>
                                <div class="col-sm-6 hidden-xs">
                                    <div class="header-section">
                                        <ul class="breadcrumb breadcrumb-top">
                                            <li>Admin</li>
                                            <li><a href="panel.php">Panel</a></li>
											<li><a href="users.php">Users</a></li>
											<li><a href="">Users Account</a></li>
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
                                <h2><?php echo $user['username'];?></h2>
                            </div>
							<form class="form-horizontal" role="form" method="post" action="">
                            <div class="modal-body">
								<div class="form-group">
                                    <label class="col-sm-3" for="inputUser">User Id</label>
                                    <div class="col-sm-9">
                                        <input class="form-control" type="text" name="user_id" value="<?php echo $user['user_id'];?>" disabled>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-3" for="inputUser">Username</label>
                                    <div class="col-sm-9">
                                        <input class="form-control" type="text" name="username" value="<?php echo $user['username'];?>">
                                    </div>
                                </div>
								<div class="form-group">
                                    <label class="col-sm-3" for="inputUser">First Name</label>
                                    <div class="col-sm-9">
                                        <input class="form-control" type="text" name="firstname" value="<?php echo $user['first_name'];?>">
                                    </div>
                                </div>
								<div class="form-group">
                                    <label class="col-sm-3" for="inputUser">Last Name</label>
                                    <div class="col-sm-9">
                                        <input class="form-control" type="text" name="lastname" value="<?php echo $user['last_name'];?>">
                                    </div>
                                </div>
								<div class="form-group">
                                    <label class="col-sm-3" for="inputUser">Email</label>
                                    <div class="col-sm-9">
                                        <input class="form-control" type="text" name="email" value="<?php echo $user['email'];?>">
                                    </div>
                                </div>
								<div class="form-group">
                                    <label class="col-sm-3" for="inputUser">Registration Date</label>
                                    <div class="col-sm-9">
                                        <input class="form-control" type="text" name="registration" value="<?php echo $user['date_register'];?>" disabled>
                                    </div>
                                </div>
								<div class="form-group">
                                    <label class="col-sm-3" for="inputUser">Status</label>
                                    <div class="col-sm-9">
                                        <select class="form-control" name="status">
										<?php
											if($user['status'] == 0){ echo "<option value=\"0\" selected>Registered</option>";}else{echo "<option value=\"0\">Registered</option>";}
											if($user['status'] == 1){ echo "<option value=\"1\" selected>Activated</option>";}else{echo "<option value=\"1\">Activated</option>";}
											if($user['status'] == 2){ echo "<option value=\"2\" selected>Banned</option>";}else{echo "<option value=\"2\">Banned</option>";}
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
                            </div>
                            <div class="modal-footer">
                                <input id="submit" class="btn btn-primary " type="submit" value="Submit">
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