<?php
	error_reporting(0);
	include("../assets/php/database.php");
	include("../assets/php/session.php");
	include("../assets/php/functions.php");
	include("../assets/php/acct/check.php");
	if(!isset($_SESSION['user_id']) || $_SESSION['user_privs']['admin'] == 0){ header("Location: ../index.php");}
	
	if(isset($_GET['d'])){
		$db = new database;
		$db->connect();
		$user_id = mysqli_real_escape_string($db->connection, $_GET['d']);
		mysqli_query($db->connection, "DELETE FROM users WHERE user_id = '$user_id'");
		mysqli_query($db->connection, "DELETE FROM users_privs WHERE user_id = '$user_id'");
		mysqli_query($db->connection, "DELETE FROM users_login WHERE user_id = '$user_id'");
		mysqli_query($db->connection, "DELETE FROM users_mailbox WHERE user_id = '$user_id'");
		mysqli_query($db->connection, "DELETE FROM usergroups_members WHERE user_id = '$user_id'");
		mysqli_query($db->connection, "DELETE FROM logs WHERE user_id = '$user_id'");
		mysqli_query($db->connection, "DELETE FROM character_targets WHERE user_id = '$user_id'");
		mysqli_query($db->connection, "DELETE FROM logs_activities WHERE user_id = '$user_id'");
		mysqli_query($db->connection, "DELETE FROM reporting_needs_coi_users WHERE user_id = '$user_id'");
		mysqli_query($db->connection, "UPDATE usergroups SET usergroup_moderator = '' WHERE usergroup_moderator = '$user_id'");
		$_SESSION['alert'] = "<div class=\"alert alert-success\" style=\"font-size:14px;\">Account has been deleted.</div>";
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
                                        <h1>Users</h1>
                                    </div>
                                </div>
                                <div class="col-sm-6 hidden-xs">
                                    <div class="header-section">
                                        <ul class="breadcrumb breadcrumb-top">
                                            <li>Admin</li>
                                            <li><a href="panel.php">Panel</a></li>
											<li><a href="users.php">Users</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
						<?php if(isset($alert)){echo $alert;} ?>

                        <!-- Block -->
                        <div class="block col-xs-12 col-sm-12 col-md-12 col-lg-12">
                            <!-- Title -->
                            <div class="block-title">
                                <h2>Users</h2>
                            </div>
							<?php
								$db = new database;
								$db->connect();
								
								if(isset($_POST['search'])){
									$search = mysqli_real_escape_string($db->connection,$_POST['search']);
									$users = mysqli_query($db->connection,"SELECT * FROM users LEFT JOIN users_privs ON users.user_id = users_privs.user_id WHERE username LIKE '%".$search."%' OR first_name LIKE '%".$search."%' OR last_name LIKE '%".$search."%' OR email LIKE '%".$search."%' ORDER BY username");
								}else{
									$users = mysqli_query($db->connection,"SELECT * FROM users LEFT JOIN users_privs ON users.user_id = users_privs.user_id ORDER BY username");
								}
								echo "<table class=\"table table-bordered table-striped table-responsive table-hover\"><tr><th>User Id</th><th>Username</th><th>First Name</th><th>Last Name</th><th>Email</th><th>Status</th><th>Actions</th></tr>";
								while($data = mysqli_fetch_assoc($users)){
									$user_id = $data['user_id'];
									$username = $data['username'];
									$first_name = $data['first_name'];
									$last_name = $data['last_name'];
									$email = $data['email'];
									if($data['status'] == 0){
										$status = "Registered";
									}else if($data['status'] == 1){
										$status = "Activated";
									}else if($data['status'] == 2){
										$status = "Banned";
									}
									if($data['admin'] != 2){
										$delete =  "- <a href=\"users.php?d=".$user_id."\" onclick=\"return confirm('Do you really want to delete the selected account?');\">[Delete]</a>";
									}else{
										$delete = "";
									}
									echo "<tr><td>".$user_id."</td><td>".ucwords($username)."</td><td>".ucwords($first_name)."</td><td>".ucwords($last_name)."</td><td>".$email."</td><td>".$status."</td><td><a href=\"users_account.php?user_id=".$user_id."\">[Edit]</a> - <a href=\"users_privs.php?user_id=".$user_id."\">[Privs]</a> - <a href=\"users_logs.php?user_id=".$user_id."\">[Logs]</a>".$delete."</td></tr>";
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