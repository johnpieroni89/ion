<?php
	error_reporting(0);
	include("../assets/php/database.php");
	include("../assets/php/session.php");
	include("../assets/php/functions.php");
	include("../assets/php/acct/check.php");
	if(!isset($_SESSION['user_id']) || $_SESSION['user_privs']['admin'] == 0){ header("Location: ../index.php");}
	
	$db = new database;
	$db->connect();
	
	if(isset($_POST['user_id'])){
		$user_id = mysqli_real_escape_string($db->connection, $_POST['user_id']);
		$api_key = md5(time().$user_id);
		mysqli_query($db->connection, "INSERT INTO users_api (api_key, user_id) VALUES ('$api_key', '$user_id') ON DUPLICATE KEY UPDATE api_key='$api_key'");
		$alert = "<div class=\"alert alert-success\" style=\"font-size:14px;\">API key has been generated for the selected user.</div>";
	}elseif(isset($_POST['delete_key'])){
		$api_key = mysqli_real_escape_string($db->connection, $_POST['delete_key']);
		mysqli_query($db->connection, "DELETE FROM users_api WHERE api_key='$api_key'");
		$alert = "<div class=\"alert alert-success\" style=\"font-size:14px;\">API key has been deleted for the selected user.</div>";
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
                                        <h1>API</h1>
                                    </div>
                                </div>
                                <div class="col-sm-6 hidden-xs">
                                    <div class="header-section">
                                        <ul class="breadcrumb breadcrumb-top">
                                            <li>Admin</li>
                                            <li><a href="panel.php">Panel</a></li>
											<li><a href="">API</a></li>
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
                                <h2>Automated Programming Interface</h2>
                            </div>
							<?php
								$db = new database;
								$db->connect();
								
								if(isset($_POST['search'])){
									$search = mysqli_real_escape_string($db->connection,$_POST['search']);
									$users = mysqli_query($db->connection,"SELECT users.*, users_api.api_key FROM users LEFT JOIN users_privs ON users.user_id = users_privs.user_id LEFT JOIN users_api ON users.user_id = users_api.user_id WHERE username LIKE '%".$search."%' OR first_name LIKE '%".$search."%' OR last_name LIKE '%".$search."%' OR email LIKE '%".$search."%' ORDER BY username");
								}else{
									$users = mysqli_query($db->connection,"SELECT users.*, users_api.api_key FROM users LEFT JOIN users_privs ON users.user_id = users_privs.user_id LEFT JOIN users_api ON users.user_id = users_api.user_id ORDER BY username");
								}
								echo "<table class=\"table table-bordered table-striped table-responsive table-hover\"><tr><th>User Id</th><th>Username</th><th>First Name</th><th>Last Name</th><th>Email</th><th>API Key</th><th>Key Management</th></tr>";
								while($data = mysqli_fetch_assoc($users)){
									$user_id = $data['user_id'];
									$username = $data['username'];
									$first_name = $data['first_name'];
									$last_name = $data['last_name'];
									$email = $data['email'];
									$key = $data['api_key'];
									echo "<tr><td>".$user_id."</td><td>".ucwords($username)."</td><td>".ucwords($first_name)."</td><td>".ucwords($last_name)."</td><td>".$email."</td><td>".$key."</td><td align=\"center\"><form method=\"post\" style=\"display:inline\" action=\"\"><input type=\"submit\" class=\"btn btn-sm btn-primary\" value=\"Generate Key\"><input type=\"hidden\" name=\"user_id\" value=\"".$data['user_id']."\"></form><form method=\"post\" style=\"margin-left:10px;display:inline\" action=\"\"><input type=\"submit\" class=\"btn btn-sm btn-warning\" onclick=\"return confirm('Do you really want to delete the selected api key?');\" value=\"Delete Key\"><input type=\"hidden\" name=\"delete_key\" value=\"".$data['api_key']."\"></form></td></tr>";
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