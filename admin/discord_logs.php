<?php
	
    include("../autoload.php");
    global $db;
    global $site;
    global $session;
    global $account;
    $session->check_login();
	if(!isset($_SESSION['user_id']) || $_SESSION['user_privs']['admin'] == 0){ header("Location: ../index.php");}
	
	$max_user_logs = mysqli_fetch_assoc(mysqli_query($db->connection, "SELECT value FROM site_settings WHERE field = 'max_user_logs'"))['value'];
	$time = swc_time(time(), TRUE)['timestamp'] - ($max_user_logs * 2628288);
	mysqli_query($db->connection, "DELETE FROM logs_activities WHERE timestamp < '$time'");
	$max_machine_logs = mysqli_fetch_assoc(mysqli_query($db->connection, "SELECT value FROM site_settings WHERE field = 'max_machine_logs'"))['value'];
	$time = swc_time(time(), TRUE)['timestamp'] - ($max_machine_logs * 2628288);
	mysqli_query($db->connection, "DELETE FROM logs WHERE UNIX_TIMESTAMP(timestamp) < '$time'");
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
                                        <h1>Discord Logs</h1>
                                    </div>
                                </div>
                                <div class="col-sm-6 hidden-xs">
                                    <div class="header-section">
                                        <ul class="breadcrumb breadcrumb-top">
                                            <li>Admin</li>
                                            <li><a href="panel.php">Panel</a></li>
											<li><a href="">Discord Logs</a></li>
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
                                <h2>Discord Bot Logs</h2>
                            </div>							
							
							<?php
								if(isset($_POST['search'])){
									$search = mysqli_real_escape_string($db->connection,$_POST['search']);
									$search_string = "SELECT * FROM logs_discord WHERE discord_id LIKE '%".$search."%' OR type LIKE '%".$search."%' OR keywords LIKE '%".$search."%' ORDER BY timestamp DESC";
									$results_count = mysqli_num_rows(mysqli_query($db->connection, $search_string));
								}else{
									$search_string = "SELECT * FROM logs_discord ORDER BY timestamp DESC";
									$results_count = mysqli_num_rows(mysqli_query($db->connection, $search_string));
								}
								
								$pages = new Paginator($results_count,9);
								echo '<div class="col-sm-6 text-right pull-right hidden-print">';
								echo "<span class=\"form-inline\">".$pages->display_jump_menu().$pages->display_items_per_page()."</span>";
								echo '</div>';
								echo '<div class="clearfix"></div>';
								$limit = $pages->limit_start.','.$pages->limit_end;
								$query = mysqli_query($db->connection,$search_string." LIMIT ".$limit);
								
								echo "<table class=\"table table-bordered table-striped table-responsive table-hover\"><tr><th>Log Id</th><th>Discord ID</th><th>Type</th><th>Keywords</th><th>Timestamp</th></tr>";
								while($data = mysqli_fetch_assoc($query)){
									$log_id = $data['id'];
									$discord_id = $data['discord_id'];
									$type = $data['type'];
									$keywords = $data['keywords'];
									$timestamp = swc_time($data['timestamp'])['date'];

									echo "<tr><td>".$log_id."</td><td>".$discord_id."</td><td>".$type."</td><td>".$keywords."</td><td>".$timestamp."</td></tr>";
								}
								echo "</table>";
								echo '<div class="col-sm-6 text-right pull-right hidden-print">';
								echo "<span class=\"form-inline\">".$pages->display_jump_menu().$pages->display_items_per_page()."</span>";
								echo '</div>';
								echo '<div class="clearfix"></div>';
							?>  
                        </div>
						
                    </div>
                </div>
            </div>
        </div>
		<?php include("../assets/php/_end.php"); ?>
	</body>
</html>