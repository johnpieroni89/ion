<?php 
	include("../assets/php/database.php");
	include("../assets/php/session.php");
	include("../assets/php/functions.php");
	include("../assets/php/acct/check.php");
	if(!isset($_SESSION['user_id']) || $_SESSION['user_privs']['admin'] == 0){ header("Location: ../index.php");}

	$db = new database;
	$db->connect();
		
	if(isset($_POST['submit_definitions'])){
		$title = mysqli_real_escape_string($db->connection, $_POST['inputTitle']);
		$name = mysqli_real_escape_string($db->connection, $_POST['inputName']);
		$description = mysqli_real_escape_string($db->connection, $_POST['inputDescription']);
		$author = mysqli_real_escape_string($db->connection, $_POST['inputAuthor']);
		$footer = mysqli_real_escape_string($db->connection, $_POST['inputFooter']);
		mysqli_query($db->connection,"UPDATE site_settings SET value = '$title' WHERE field = 'title'");
		mysqli_query($db->connection,"UPDATE site_settings SET value = '$name' WHERE field = 'name'");
		mysqli_query($db->connection,"UPDATE site_settings SET value = '$description' WHERE field = 'description'");
		mysqli_query($db->connection,"UPDATE site_settings SET value = '$author' WHERE field = 'author'");
		mysqli_query($db->connection,"UPDATE site_settings SET value = '$footer' WHERE field = 'footer'");
		$_SESSION['alert'] = "<div class=\"alert alert-success\" style=\"font-size:14px;\">Site definitions have been updated.</div>";
		header("Location: site_settings.php");
	}elseif(isset($_POST['submit_security'])){
		$accountRequired = mysqli_real_escape_string($db->connection, $_POST['inputAcctReq']);
		$loginRecords = mysqli_real_escape_string($db->connection, $_POST['inputLoginRecords']);
		$loginFails = mysqli_real_escape_string($db->connection, $_POST['inputLoginFailures']);
		$loginTimeout = mysqli_real_escape_string($db->connection, $_POST['inputLoginTimeout']);
		mysqli_query($db->connection,"UPDATE site_settings SET value = '$accountRequired' WHERE field = 'account_required'");
		mysqli_query($db->connection,"UPDATE site_settings SET value = '$loginRecords' WHERE field = 'security_user_logins_logs'");
		mysqli_query($db->connection,"UPDATE site_settings SET value = '$loginFails' WHERE field = 'security_user_logins_max'");
		mysqli_query($db->connection,"UPDATE site_settings SET value = '$loginTimeout' WHERE field = 'security_user_logins_timeout'");
		$_SESSION['alert'] = "<div class=\"alert alert-success\" style=\"font-size:14px;\">Site security settings have been updated.</div>";
		header("Location: site_settings.php");
	}elseif(isset($_POST['submit_logs'])){
		$max_user_logs = mysqli_real_escape_string($db->connection, $_POST['inputMaxUserLogs']);
		$max_machine_logs = mysqli_real_escape_string($db->connection, $_POST['inputMaxMachineLogs']);
		mysqli_query($db->connection,"UPDATE site_settings SET value = '$max_user_logs' WHERE field = 'max_user_logs'");
		mysqli_query($db->connection,"UPDATE site_settings SET value = '$max_machine_logs' WHERE field = 'max_machine_logs'");
		$_SESSION['alert'] = "<div class=\"alert alert-success\" style=\"font-size:14px;\">Site log settings have been updated.</div>";
		header("Location: site_settings.php");
	}
	
	$db->disconnect();
	unset($db);
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
                                        <h1>Site Settings</h1>
                                    </div>
                                </div>
                                <div class="col-sm-6 hidden-xs">
                                    <div class="header-section">
                                        <ul class="breadcrumb breadcrumb-top">
                                            <li>Admin</li>
                                            <li><a href="panel.php">Panel</a></li>
											<li><a href="">Site Settings</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
						<?php if(isset($alert)){echo $alert;} ?>

                        <!-- Block -->
                        <div class="block col-xs-12 col-sm-12 col-md-12 col-lg-12" style="overflow: auto;">
                            <!-- Title -->
                            <div class="block-title">
                                <h2>Definitions</h2>
                            </div>
							<div class="block-content">
								<form class="form-horizontal" method="post">
									<div class="form-group">
										<label class="col-sm-1" for="inputTitle">Title</label>
										<div class="col-sm-11"><input type="text" class="form-control" name="inputTitle" value="<?php echo $_SESSION['site_title'];?>"></div>
									</div>
									<div class="form-group">
										<label class="col-sm-1" for="inputName">Name</label>
										<div class="col-sm-11"><input type="text" class="form-control" name="inputName" value="<?php echo $_SESSION['site_name'];?>"></div>
									</div>
									<div class="form-group">
										<label class="col-sm-1" for="inputDescription">Description</label>
										<div class="col-sm-11"><input type="text" class="form-control" name="inputDescription" value="<?php echo $_SESSION['site_description'];?>"></div>
									</div>
									<div class="form-group">
										<label class="col-sm-1" for="inputAuthor">Author</label>
										<div class="col-sm-11"><input type="text" class="form-control" name="inputAuthor" value="<?php echo $_SESSION['site_author'];?>"></div>
									</div>
									<div class="form-group">
										<label class="col-sm-1" for="inputFooter">Footer</label>
										<div class="col-sm-11"><textarea rows="1" class="form-control" name="inputFooter"><?php echo $_SESSION['site_footer'];?></textarea></div>
									</div>
									<input type="submit" class="btn btn-primary" name="submit_definitions" value="Save">
								</form>
							</div>
                        </div>
						
						<!-- Block -->
                        <div class="block col-xs-12 col-sm-12 col-md-12 col-lg-12" style="overflow: auto;">
                            <!-- Title -->
                            <div class="block-title">
                                <h2>Security Settings</h2>
                            </div>
							<div class="block-content">
								<form class="form-horizontal" method="post">
									<div class="form-group">
										<label class="col-sm-2" for="inputAcctReq">Account Required</label>
										<div class="col-sm-10">
											<select class="form-control" name="inputAcctReq">
												<option value="0">No</option>
												<option value="1" <?php if($_SESSION['site_account_required'] == 1){echo "selected";} ?>>Yes</option>
											</select>
										</div>
									</div>
									<div class="form-group">
										<label class="col-sm-2" for="inputLoginRecords">Max Login Records</label>
										<div class="col-sm-10"><input type="text" class="form-control" name="inputLoginRecords" value="<?php echo $_SESSION['site_login_logs'];?>"></div>
									</div>
									<div class="form-group">
										<label class="col-sm-2" for="inputLoginFailures">Max Login Failures</label>
										<div class="col-sm-10"><input type="text" class="form-control" name="inputLoginFailures" value="<?php echo $_SESSION['site_max_fails'];?>"></div>
									</div>
									<div class="form-group">
										<label class="col-sm-2" for="inputLoginTimeout">Login Timeout (secs)</label>
										<div class="col-sm-10"><input type="text" class="form-control" name="inputLoginTimeout" value="<?php echo $_SESSION['site_login_timeout'];?>"></div>
									</div>
									<input type="submit" class="btn btn-primary" name="submit_security" value="Save">
								</form>
							</div>
                        </div>
						
						<!-- Block -->
                        <div class="block col-xs-12 col-sm-12 col-md-12 col-lg-12" style="overflow: auto;">
                            <!-- Title -->
                            <div class="block-title">
                                <h2>Log Settings</h2>
                            </div>
							<div class="block-content">
								<form class="form-horizontal" method="post">
									<div class="form-group">
										<label class="col-sm-2" for="inputMaxUserLogs">Max User Logs</label>
										<div class="col-sm-10">
											<select class="form-control" name="inputMaxUserLogs">
												<option value="1" <?php if($_SESSION['max_user_logs'] == 1){echo "selected";} ?>>1 month</option>
												<option value="2" <?php if($_SESSION['max_user_logs'] == 2){echo "selected";} ?>>2 months</option>
												<option value="3" <?php if($_SESSION['max_user_logs'] == 3){echo "selected";} ?>>3 months</option>
												<option value="4" <?php if($_SESSION['max_user_logs'] == 4){echo "selected";} ?>>4 months</option>
												<option value="5" <?php if($_SESSION['max_user_logs'] == 5){echo "selected";} ?>>5 months</option>
												<option value="6" <?php if($_SESSION['max_user_logs'] == 6){echo "selected";} ?>>6 months</option>
												<option value="7" <?php if($_SESSION['max_user_logs'] == 7){echo "selected";} ?>>7 months</option>
												<option value="8" <?php if($_SESSION['max_user_logs'] == 8){echo "selected";} ?>>8 months</option>
												<option value="9" <?php if($_SESSION['max_user_logs'] == 9){echo "selected";} ?>>9 months</option>
												<option value="10" <?php if($_SESSION['max_user_logs'] == 10){echo "selected";} ?>>10 months</option>
												<option value="11" <?php if($_SESSION['max_user_logs'] == 11){echo "selected";} ?>>11 months</option>
												<option value="12" <?php if($_SESSION['max_user_logs'] == 12){echo "selected";} ?>>12 months</option>
											</select>
										</div>
									</div>
									<div class="form-group">
										<label class="col-sm-2" for="inputMaxMachineLogs">Max Machine Logs</label>
										<div class="col-sm-10">
											<select class="form-control" name="inputMaxMachineLogs">
												<option value="1" <?php if($_SESSION['max_machine_logs'] == 1){echo "selected";} ?>>1 month</option>
												<option value="2" <?php if($_SESSION['max_machine_logs'] == 2){echo "selected";} ?>>2 months</option>
												<option value="3" <?php if($_SESSION['max_machine_logs'] == 3){echo "selected";} ?>>3 months</option>
												<option value="4" <?php if($_SESSION['max_machine_logs'] == 4){echo "selected";} ?>>4 months</option>
												<option value="5" <?php if($_SESSION['max_machine_logs'] == 5){echo "selected";} ?>>5 months</option>
												<option value="6" <?php if($_SESSION['max_machine_logs'] == 6){echo "selected";} ?>>6 months</option>
												<option value="7" <?php if($_SESSION['max_machine_logs'] == 7){echo "selected";} ?>>7 months</option>
												<option value="8" <?php if($_SESSION['max_machine_logs'] == 8){echo "selected";} ?>>8 months</option>
												<option value="9" <?php if($_SESSION['max_machine_logs'] == 9){echo "selected";} ?>>9 months</option>
												<option value="10" <?php if($_SESSION['max_machine_logs'] == 10){echo "selected";} ?>>10 months</option>
												<option value="11" <?php if($_SESSION['max_machine_logs'] == 11){echo "selected";} ?>>11 months</option>
												<option value="12" <?php if($_SESSION['max_machine_logs'] == 12){echo "selected";} ?>>12 months</option>
											</select>
										</div>
									</div>
									<input type="submit" class="btn btn-primary" name="submit_logs" value="Save">
								</form>
							</div>
                        </div>
						
                    </div>
                </div>
            </div>
        </div>
		<?php include("../assets/php/_end.php"); ?>
	</body>
</html>