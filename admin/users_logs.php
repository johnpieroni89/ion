<?php
	error_reporting(0);
	include("../assets/php/database.php");
	include("../assets/php/session.php");
	include("../assets/php/functions.php");
	include("../assets/php/acct/check.php");
	if(!isset($_SESSION['user_id']) || $_SESSION['user_privs']['admin'] == 0){ header("Location: ../index.php");}
	
	if(isset($_GET['user_id']) && $_GET['user_id'] != ""){
		$db = new database;
		$db->connect();
		$user_id = mysqli_real_escape_string($db->connection,$_GET['user_id']);
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
                                        <h1>User Logs</h1>
                                    </div>
                                </div>
                                <div class="col-sm-6 hidden-xs">
                                    <div class="header-section">
                                        <ul class="breadcrumb breadcrumb-top">
                                            <li>Admin</li>
                                            <li><a href="panel.php">Panel</a></li>
											<li><a href="users.php">Users</a></li>
											<li><a href="users_logs.php?user_id=<?php echo $user['user_id']; ?>">User Logs</a></li>
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
                                <h2><?php echo $user['username']; ?></h2>
                            </div>
							<button class="btn btn-primary">
								<a style="color:white;" href="users_logs.php?user_id=<?php echo $user['user_id']; ?>&view=0">View User Activity</a>
							</button>
							<button class="btn btn-primary">
								<a style="color:white;" href="users_logs.php?user_id=<?php echo $user['user_id']; ?>&view=1">View Machine Activity</a>
							</button>
							<hr/>
							
							<?php
								$db = new database;
								$db->connect();
								
								if($_GET['view'] == 0 || $_GET['view'] == ""){
									if(isset($_POST['search'])){
										$search = mysqli_real_escape_string($db->connection,$_POST['search']);
										$logs = mysqli_query($db->connection,"SELECT logs_activities.*, users.username FROM logs_activities LEFT JOIN users ON users.user_id = logs_activities.user_id WHERE logs_activities.user_id = '$user_id' AND (logs_activities.id LIKE '%".$search."%' OR logs_activities.details LIKE '%".$search."%') ORDER BY logs_activities.timestamp DESC");
									}else{
										$logs = mysqli_query($db->connection,"SELECT logs_activities.*, users.username FROM logs_activities LEFT JOIN users ON users.user_id = logs_activities.user_id WHERE logs_activities.user_id = '$user_id' ORDER BY logs_activities.timestamp DESC");
									}
									
									echo "<table class=\"table table-bordered table-striped table-responsive table-hover\"><tr><th>Log Id</th><th>System</th><th>Details</th><th>Timestamp</th></tr>";
									while($data = mysqli_fetch_assoc($logs)){
										$log_id = $data['id'];
										$username = $data['username'];
										if($data['log_type'] == 0){
											$system = "Sentient Profiles";
										}else if($data['log_type'] == 1){
											$system = "Signals Analysis";
										}else if($data['log_type'] == 2){
											$system = "Galaxy Data";
										}else if($data['log_type'] == 3){
											$system = "Faction Catalog";
										}else if($data['log_type'] == 4){
											$system = "Reporting";
										}
										$details = $data['details'];
										$timestamp = swc_time($data['timestamp']);

										echo "<tr><td>".$log_id."</td><td>".$system."</td><td>".$details."</td><td>Year ".$timestamp["year"]." Day ".$timestamp["day"]." ".$timestamp["hour"].":".$timestamp["minute"].":".$timestamp["second"]."</td></tr>";
									}
									echo "</table>";
									
								}else if($_GET['view'] == 1){
									if(isset($_POST['search'])){
										$search = mysqli_real_escape_string($db->connection,$_POST['search']);
										$logs = mysqli_query($db->connection,"SELECT logs.*, users.username FROM logs LEFT JOIN users ON users.user_id = logs.user_id WHERE logs.user_id = '$user_id' AND (logs.event_id LIKE '%".$search."%' OR logs.ip LIKE '%".$search."%' OR logs.details LIKE '%".$search."%') ORDER BY logs.timestamp DESC");
									}else{
										$logs = mysqli_query($db->connection,"SELECT logs.*, users.username FROM logs LEFT JOIN users ON users.user_id = logs.user_id WHERE logs.user_id = '$user_id' ORDER BY logs.timestamp DESC");
									}
									
									echo "<table class=\"table table-bordered table-striped table-responsive table-hover\"><tr><th>Log Id</th><th>Type</th><th>IP</th><th>Details</th><th>Timestamp</th></tr>";
									while($data = mysqli_fetch_assoc($logs)){
										$log_id = $data['event_id'];
										if($data['event_type'] == 0){
											$system = "User";
										}else if($data['event_type'] == 1){
											$system = "Admin";
										}else if($data['event_type'] == 2){
											$system = "Security";
										}else if($data['event_type'] == 3){
											$system = "System";
										}
										$ip = $data['ip'];
										$details = $data['details'];
										$timestamp = swc_time(strtotime($data['timestamp']), TRUE);

										echo "<tr><td>".$log_id."</td><td>".$system."</td><td>".$ip."</td><td>".$details."</td><td>".$timestamp['date']."</td></tr>";
									}
									echo "</table>";
								}
							?>  
                        </div>
						
                    </div>
                </div>
            </div>
        </div>
		<?php include("../assets/php/_end.php"); ?>
	</body>
</html>