<?php
    include("../autoload.php");
    global $db;
    global $site;
    global $session;
    global $account;
    $session->check_login();
	if(!isset($_SESSION['user_id']) || $_SESSION['user_privs']['admin'] == 0){ header("Location: ../index.php");}
	
	if(isset($_POST['add_user'])){
		$user_id = mysqli_real_escape_string($db->connection, $_POST['add_user']);
		mysqli_query($db->connection, "INSERT INTO users_subscription (user_id) VALUES ('$user_id')");
		$session->alert = "<div class=\"alert alert-success\" style=\"font-size:14px;\">User has been enrolled in subscription service.</div>";
	}elseif(isset($_POST['add_30'])){
		$user_id = mysqli_real_escape_string($db->connection, $_POST['add_30']);
		mysqli_query($db->connection, "UPDATE users_subscription SET days = days + 30 WHERE user_id = '$user_id'");
		$session->alert = "<div class=\"alert alert-success\" style=\"font-size:14px;\">User has been granted 30 days of subscription.</div>";
	}elseif(isset($_POST['delete_user'])){
		$user_id = mysqli_real_escape_string($db->connection, $_POST['delete_user']);
		mysqli_query($db->connection, "DELETE FROM users_subscription WHERE user_id='$user_id'");
		$session->alert = "<div class=\"alert alert-success\" style=\"font-size:14px;\">User has been disenrolled from subscription service.</div>";
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
                                        <h1>Subscriptions</h1>
                                    </div>
                                </div>
                                <div class="col-sm-6 hidden-xs">
                                    <div class="header-section">
                                        <ul class="breadcrumb breadcrumb-top">
                                            <li>Admin</li>
                                            <li><a href="panel.php">Panel</a></li>
											<li><a href="">Subscriptions</a></li>
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
                                <h2>Subscriptions</h2>
                            </div>
							<?php
								if(isset($_POST['search'])){
									$search = mysqli_real_escape_string($db->connection,$_POST['search']);
									$users = mysqli_query($db->connection,"SELECT users.*, users_subscription.days FROM users LEFT JOIN users_subscription ON users.user_id = users_subscription.user_id WHERE username LIKE '%".$search."%' OR first_name LIKE '%".$search."%' OR last_name LIKE '%".$search."%' OR email LIKE '%".$search."%' ORDER BY username");
								}else{
									$users = mysqli_query($db->connection,"SELECT users.*, users_subscription.days FROM users LEFT JOIN users_subscription  ON users.user_id = users_subscription.user_id ORDER BY username");
								}
								echo "If a user is not enrolled in subscription service, then they are granted persistent access.";
								echo "<table class=\"table table-bordered table-striped table-responsive table-hover\"><tr><th>User Id</th><th>Username</th><th>First Name</th><th>Last Name</th><th>Email</th><th>Days</th><th>Management</th></tr>";
								while($data = mysqli_fetch_assoc($users)){
									$user_id = $data['user_id'];
									$username = $data['username'];
									$first_name = $data['first_name'];
									$last_name = $data['last_name'];
									$email = $data['email'];
									$days = $data['days'];
									
									echo "<tr><td>".$user_id."</td><td>".ucwords($username)."</td><td>".ucwords($first_name)."</td><td>".ucwords($last_name)."</td><td>".$email."</td><td>".$days."</td>";
									
									if($days == ""){
										echo "<td align=\"center\">
										<form method=\"post\" style=\"display:inline\" action=\"\"><input type=\"submit\" class=\"btn btn-sm btn-primary\" value=\"Enroll\"><input type=\"hidden\" name=\"add_user\" value=\"".$data['user_id']."\"></form>
										</td></tr>";
									}else{
										echo "<td align=\"center\">
										<form method=\"post\" style=\"display:inline\" action=\"\"><input type=\"submit\" class=\"btn btn-sm btn-primary\" value=\"Add 30\"><input type=\"hidden\" name=\"add_30\" value=\"".$data['user_id']."\"></form>
										<form method=\"post\" style=\"margin-left:10px;display:inline\" action=\"\"><input type=\"submit\" class=\"btn btn-sm btn-warning\" onclick=\"return confirm('Do you really want to disenroll the selected user?');\" value=\"Disenroll\"><input type=\"hidden\" name=\"delete_user\" value=\"".$data['user_id']."\"></form>
										</td></tr>";
									}
								}
								echo "</table>";
							?>  
                        </div>
						
                    </div>
                </div>
            </div>
        </div>
		<?php include("../assets/php/_end.php"); ?>
	</body>
</html>