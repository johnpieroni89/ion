<?php 
	include("../assets/php/database.php");
	include("../assets/php/session.php");
	include("../assets/php/functions.php");
	include("../assets/php/acct/check.php");
	if(!isset($_SESSION['user_id']) || $_SESSION['user_privs']['admin'] == 0){ header("Location: ../index.php");}
	
	if($_POST){
		$db = new database;
		$db->connect();
		
		if($_POST['appMailbox']){
			mysqli_query($db->connection,"UPDATE site_settings SET value = 1 WHERE field = 'app_mailbox'");
			$_SESSION['app_mailbox'] = 1;
		}else{
			mysqli_query($db->connection,"UPDATE site_settings SET value = 0 WHERE field = 'app_mailbox'");
			$_SESSION['app_mailbox'] = 0;
		}
		if($_POST['appNotifications']){
			mysqli_query($db->connection,"UPDATE site_settings SET value = 1 WHERE field = 'app_notifications'");
			$_SESSION['app_notifications'] = 1;
		}else{
			mysqli_query($db->connection,"UPDATE site_settings SET value = 0 WHERE field = 'app_notifications'");
			$_SESSION['app_notifications'] = 0;
		}
		if($_POST['appSubscriptions']){
			mysqli_query($db->connection,"UPDATE site_settings SET value = 1 WHERE field = 'app_subscription'");
			$_SESSION['app_subscription'] = 1;
		}else{
			mysqli_query($db->connection,"UPDATE site_settings SET value = 0 WHERE field = 'app_subscription'");
			$_SESSION['app_subscription'] = 0;
		}
		if($_POST['appUsergroups']){
			mysqli_query($db->connection,"UPDATE site_settings SET value = 1 WHERE field = 'app_usergroups'");
			$_SESSION['app_usergroups'] = 1;
		}else{
			mysqli_query($db->connection,"UPDATE site_settings SET value = 0 WHERE field = 'app_usergroups'");
			$_SESSION['app_usergroups'] = 0;
		}
		
		$alert = "<div class=\"alert alert-success\" style=\"font-size:14px;\">Module configurations have been saved.</div>";
		
		$db->disconnect();
		unset($db);
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
                                        <h1>Modules</h1>
                                    </div>
                                </div>
                                <div class="col-sm-6 hidden-xs">
                                    <div class="header-section">
                                        <ul class="breadcrumb breadcrumb-top">
                                            <li>Admin</li>
                                            <li><a href="panel.php">Panel</a></li>
											<li><a href="">Modules</a></li>
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
                                <h2>Modules</h2>
                            </div>
							<form method="post">
								<div class="checkbox">
									<label>
										<input type="checkbox" name="appMailbox" value="1" <?php if($_SESSION['app_mailbox'] == 1){echo "checked";}?>>
										Mailbox
									</label>
								</div>
								<div class="checkbox">
									<label>
										<input type="checkbox" name="appNotifications" value="1" <?php if($_SESSION['app_notifications'] == 1){echo "checked";}?>>
										Notifications
									</label>
								</div>
								<div class="checkbox">
									<label>
										<input type="checkbox" name="appSubscriptions" value="1" <?php if($_SESSION['app_subscription'] == 1){echo "checked";}?>>
										Subscriptions
									</label>
								</div>
								<div class="checkbox">
									<label>
										<input type="checkbox" name="appUsergroups" value="1" <?php if($_SESSION['app_usergroups'] == 1){echo "checked";}?>>
										Usergroups
									</label>
								</div><br>
								<input type="submit" class="btn btn-primary" name="submit" value="Save">
							</form>
                        </div>
						
                    </div>
                </div>
            </div>
        </div>
		<?php include("../assets/php/_end.php"); ?>
	</body>
</html>