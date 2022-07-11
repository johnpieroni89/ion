<?php
    include("../autoload.php");
    global $db;
    global $site;
    global $session;
    global $account;
    $session->check_login();
	if(!isset($_SESSION['user_id']) || $_SESSION['user_privs']['admin'] == 0){ header("Location: ../index.php");}
	
	//$db = new database;
	//$db->connect();
	
	//$max_user_logs = mysqli_fetch_assoc(mysqli_query($db->connection, "SELECT value FROM site_settings WHERE field = 'max_user_logs'"))['value'];
	//$time = swc_time(time(), TRUE)['timestamp'] - ($max_user_logs * 2628288);
	//mysqli_query($db->connection, "DELETE FROM logs_activities WHERE timestamp < '$time'");
	//$max_machine_logs = mysqli_fetch_assoc(mysqli_query($db->connection, "SELECT value FROM site_settings WHERE field = 'max_machine_logs'"))['value'];
	//$time = swc_time(time(), TRUE)['timestamp'] - ($max_machine_logs * 2628288);
	//mysqli_query($db->connection, "DELETE FROM logs WHERE UNIX_TIMESTAMP(timestamp) < '$time'");
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
                                        <h1>Security Logs</h1>
                                    </div>
                                </div>
                                <div class="col-sm-6 hidden-xs">
                                    <div class="header-section">
                                        <ul class="breadcrumb breadcrumb-top">
                                            <li>Admin</li>
                                            <li><a href="panel.php">Panel</a></li>
											<li><a href="">Logs</a></li>
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
                                <h2><?php if(!empty($user)){ echo $user['username'];} ?> Logs</h2>
                            </div>
							<div class="hidden-print">
								<button class="btn btn-primary">
									<a style="color:white;" href="security_logs.php?view=0">View User Activity</a>
								</button>
								<button class="btn btn-primary">
									<a style="color:white;" href="security_logs.php?view=1">View Machine Activity</a>
								</button>
								<hr/>
							</div>
							
							
							<?php								
								//mysqli_query($db->connection, "DELETE FROM logs_activities ORDER BY timestamp DESC LIMIT $limit, 100000");
								
								if($_GET['view'] == 0 || $_GET['view'] == ""){
									if(isset($_POST['search'])){
										$search = mysqli_real_escape_string($db->connection,$_POST['search']);
										$search_string = "SELECT logs_activities.*, users.username FROM logs_activities LEFT JOIN users ON users.user_id = logs_activities.user_id WHERE (users.username LIKE '%".$search."%' OR logs_activities.id LIKE '%".$search."%' OR logs_activities.details LIKE '%".$search."%') ORDER BY logs_activities.timestamp DESC";
										$results_count = mysqli_num_rows(mysqli_query($db->connection, $search_string));
									}else{
										$search_string = "SELECT logs_activities.*, users.username FROM logs_activities LEFT JOIN users ON users.user_id = logs_activities.user_id ORDER BY logs_activities.timestamp DESC";
										$results_count = mysqli_num_rows(mysqli_query($db->connection, $search_string));
									}
									
									$pages = new Paginator($results_count,9);
									echo '<div class="col-sm-6 text-right pull-right hidden-print">';
									echo "<span class=\"form-inline\">".$pages->display_jump_menu().$pages->display_items_per_page()."</span>";
									echo '</div>';
									echo '<div class="clearfix"></div>';
									$limit = $pages->limit_start.','.$pages->limit_end;
									$query = mysqli_query($db->connection,$search_string." LIMIT ".$limit);
									
									echo "<table class=\"table table-bordered table-striped table-responsive table-hover\"><tr><th>Log Id</th><th>System</th><th>Username</th><th>Details</th><th>Timestamp</th></tr>";
									while($data = mysqli_fetch_assoc($query)){
										$log_id = $data['id'];
										$username = $data['username'];
										if($data['log_type'] == 0){
											$system = "<i class=\"fas fa-male\" style=\"font-size:20px;\" title=\"Sentient Profiles\"></i>";
										}else if($data['log_type'] == 1){
											$system = "<i class=\"fas fa-broadcast-tower\" style=\"font-size:20px;\" title=\"Signals Analysis\"></i>";
										}else if($data['log_type'] == 2){
											$system = "<i class=\"fas fa-globe\" style=\"font-size:20px;\" title=\"Galaxy Data\"></i>";
										}else if($data['log_type'] == 3){
											$system = "<i class=\"fas fa-sitemap\" style=\"font-size:20px;\" title=\"Faction Catalog\"></i>";
										}else if($data['log_type'] == 4){
											$system = "<i class=\"fas fa-book\" style=\"font-size:20px;\" title=\"Reporting\"></i>";
										}else if($data['log_type'] == 5){
											$system = "<i class=\"fas fa-search-dollar\" style=\"font-size:20px;\" title=\"Transactions\"></i>";
										}else if($data['log_type'] == 6){
											$system = "<i class=\"fas fa-rss-square\" style=\"font-size:20px;\" title=\"Flashnews\"></i>";
										}else if($data['log_type'] == 7){
											$system = "<i class=\"fas fa-database\" style=\"font-size:20px;\" title=\"Database\"></i>";
										}else if($data['log_type'] == 8){
											$system = "<i class=\"fas fa-map-marked-alt\" style=\"font-size:20px;\" title=\"Geolocation\"></i>";
										}
										$details = $data['details'];
										$username = ucwords($data['username']);
										if($username == ""){$username = "(System)";}
										$timestamp = swc_time($data['timestamp']);

										echo "<tr><td>".$log_id."</td><td align=\"center\">".$system."</td><td>".$username."</td><td>".$details."</td><td>Year ".$timestamp["year"]." Day ".$timestamp["day"]." ".$timestamp["hour"].":".$timestamp["minute"].":".$timestamp["second"]."</td></tr>";
									}
									echo "</table>";
									echo '<div class="col-sm-6 text-right pull-right hidden-print">';
									echo "<span class=\"form-inline\">".$pages->display_jump_menu().$pages->display_items_per_page()."</span>";
									echo '</div>';
									echo '<div class="clearfix"></div>';
									
								}else if($_GET['view'] == 1){
									if(isset($_POST['search'])){
										$search = mysqli_real_escape_string($db->connection,$_POST['search']);
										$search_string = "SELECT logs.*, users.username FROM logs LEFT JOIN users ON users.user_id = logs.user_id WHERE (users.username LIKE '%".$search."%' OR logs.event_id LIKE '%".$search."%' OR logs.ip LIKE '%".$search."%' OR logs.details LIKE '%".$search."%') ORDER BY logs.timestamp DESC";
										$results_count = mysqli_num_rows(mysqli_query($db->connection, $search_string));
									}else{
										$search_string = "SELECT logs.*, users.username FROM logs LEFT JOIN users ON users.user_id = logs.user_id ORDER BY logs.timestamp DESC";
										$results_count = mysqli_num_rows(mysqli_query($db->connection, $search_string));
									}
									
									$pages = new Paginator($results_count,9);
									echo '<div class="col-sm-6 text-right pull-right hidden-print">';
									echo "<span class=\"form-inline\">".$pages->display_jump_menu().$pages->display_items_per_page()."</span>";
									echo '</div>';
									echo '<div class="clearfix"></div>';
									$limit = $pages->limit_start.','.$pages->limit_end;
									$query = mysqli_query($db->connection,$search_string." LIMIT ".$limit);
									
									echo "<table class=\"table table-bordered table-striped table-responsive table-hover\"><tr><th>Log Id</th><th>Type</th><th>Username</th><th>IP</th><th>Details</th><th>Timestamp</th></tr>";
									while($data = mysqli_fetch_assoc($query)){
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
										$username = ucwords($data['username']);
										$ip = $data['ip'];
										$details = $data['details'];
										$timestamp = $data['timestamp'];

										echo "<tr><td>".$log_id."</td><td>".$system."</td><td>".$username."</td><td>".$ip."</td><td>".$details."</td><td>".$timestamp."</td></tr>";
									}
									echo "</table>";
									echo '<div class="col-sm-6 text-right pull-right hidden-print">';
									echo "<span class=\"form-inline\">".$pages->display_jump_menu().$pages->display_items_per_page()."</span>";
									echo '</div>';
									echo '<div class="clearfix"></div>';
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