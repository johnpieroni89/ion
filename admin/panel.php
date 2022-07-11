<?php 
    include("../autoload.php");
    global $db;
    global $site;
    global $session;
    global $account;
    $session->check_login();
	if(!isset($_SESSION['user_id'])){ header("Location: ../index.php");}
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
                                        <h1>Admin Panel</h1>
                                    </div>
                                </div>
                                <div class="col-sm-6 hidden-xs">
                                    <div class="header-section">
                                        <ul class="breadcrumb breadcrumb-top">
                                            <li>Admin</li>
                                            <li><a href="">Panel</a></li>
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
                                <h2>Content Management</h2>
                            </div>
							<div class="block col-xs-3 col-sm-4 col-md-3 col-lg-1" style="margin-right:5px;text-align:center;">
								<a href="content_themes.php" style="color:black;" title="CSS Themes">
								<div class="block-title" style="font-size:14px;margin-bottom:0px;">Themes</div>
								<img src="../assets/img/icons/themes.png" height="60" width="60"></a>
							</div>
                        </div>
						
						<!-- Block -->
                        <div class="block col-xs-12 col-sm-12 col-md-12 col-lg-12">
                            <!-- Title -->
                            <div class="block-title">
                                <h2>Database</h2>
                            </div>
							<div class="block col-xs-3 col-sm-4 col-md-3 col-lg-1" style="margin-right:5px;text-align:center;">
								<a href="database_api.php" style="color:black;" title="Automated Programming Interface">
								<div class="block-title" style="font-size:14px;margin-bottom:0px;">API</div>
								<img src="../assets/img/icons/APIs.png" height="60" width="60"></a>
							</div>
							<div class="block col-xs-3 col-sm-4 col-md-3 col-lg-1" style="margin-right:5px;text-align:center;">
								<a href="database.php" style="color:black;" title="Manage Database">
								<div class="block-title" style="font-size:14px;margin-bottom:0px;">Database</div>
								<img src="../assets/img/icons/database.png" height="60" width="60"></a>
							</div>
                        </div>
						
						<!-- Block -->
                        <div class="block col-xs-12 col-sm-12 col-md-12 col-lg-12">
                            <!-- Title -->
                            <div class="block-title">
                                <h2>Security Center</h2>
                            </div>
							<div class="block col-xs-3 col-sm-4 col-md-3 col-lg-1" style="margin-right:5px;text-align:center;">
								<a href="security_logs.php" style="color:black;" title="System & Security Logs">
								<div class="block-title" style="font-size:14px;margin-bottom:0px;">Activity Logs</div>
								<img src="../assets/img/icons/logs.png" height="60" width="60"></a>
							</div>
							<div class="block col-xs-3 col-sm-4 col-md-3 col-lg-1" style="margin-right:5px;text-align:center;">
								<a href="discord_logs.php" style="color:black;" title="Discord Bot Logs">
								<div class="block-title" style="font-size:14px;margin-bottom:0px;">Discord Logs</div>
								<img src="../assets/img/icons/discord_logs.png" height="60" width="60"></a>
							</div>
							<div class="block col-xs-3 col-sm-4 col-md-3 col-lg-1" style="margin-right:5px;text-align:center;">
								<a href="scan_filters.php" style="color:black;" title="Scan Filters">
								<div class="block-title" style="font-size:14px;margin-bottom:0px;">Scan Filters</div>
								<img src="../assets/img/icons/filter.png" height="60" width="60"></a>
							</div>
							
                        </div>
						
						<!-- Block -->
                        <div class="block col-xs-12 col-sm-12 col-md-12 col-lg-12">
                            <!-- Title -->
                            <div class="block-title">
                                <h2>Site Configuration</h2>
                            </div>
							<div class="block col-xs-3 col-sm-4 col-md-3 col-lg-1" style="margin-right:5px;text-align:center;">
								<a href="site_modules.php" style="color:black;" title="Site Modules">
								<div class="block-title" style="font-size:14px;margin-bottom:0px;">Modules</div>
								<img src="../assets/img/icons/modules.png" height="60" width="60"></a>
							</div>
							<div class="block col-xs-3 col-sm-4 col-md-3 col-lg-1" style="margin-right:5px;text-align:center;">
								<a href="phpinfo.php" style="color:black;" title="Show PHP Info">
								<div class="block-title" style="font-size:14px;margin-bottom:0px;">PHP Info</div>
								<img src="../assets/img/icons/php_info.png" height="60" width="60"></a>
							</div>
							<div class="block col-xs-3 col-sm-4 col-md-3 col-lg-1" style="margin-right:5px;text-align:center;">
								<a href="site_plugins.php" style="color:black;" title="Manage plugins and dependencies">
								<div class="block-title" style="font-size:14px;margin-bottom:0px;">Plugins</div>
								<img src="../assets/img/icons/plugins.png" height="60" width="60"></a>
							</div>
							<div class="block col-xs-3 col-sm-4 col-md-3 col-lg-1" style="margin-right:5px;text-align:center;">
								<a href="site_settings.php" style="color:black;" title="General Site Settings">
								<div class="block-title" style="font-size:14px;margin-bottom:0px;">Settings</div>
								<img src="../assets/img/icons/site_settings.png" height="60" width="60"></a>
							</div>
                        </div>
						
						<!-- Block -->
                        <div class="block col-xs-12 col-sm-12 col-md-12 col-lg-12">
                            <!-- Title -->
                            <div class="block-title">
                                <h2>User Management</h2>
                            </div>
                            <div class="block col-xs-3 col-sm-4 col-md-3 col-lg-1" style="margin-right:5px;text-align:center;">
								<a href="administrators.php" style="color:black;">
								<div class="block-title" style="font-size:14px;margin-bottom:0px;">Admins</div>
								<img src="../assets/img/icons/administrators.png" height="60" width="60"></a>
							</div>
							<div class="block col-xs-3 col-sm-4 col-md-3 col-lg-1" style="margin-right:5px;text-align:center;">
								<a href="subscriptions.php" style="color:black;">
								<div class="block-title" style="font-size:14px;margin-bottom:0px;">Subscription</div>
								<img src="../assets/img/icons/subscriptions.png" height="60" width="60"></a>
							</div>
							<div class="block col-xs-3 col-sm-4 col-md-3 col-lg-1" style="margin-right:5px;text-align:center;">
								<a href="usergroups.php" style="color:black;">
								<div class="block-title" style="font-size:14px;margin-bottom:0px;">Usergroups</div>
								<img src="../assets/img/icons/usergroups.png" height="60" width="60"></a>
							</div>
							<div class="block col-xs-3 col-sm-4 col-md-3 col-lg-1" style="margin-right:5px;text-align:center;">
								<a href="users.php" style="color:black;">
								<div class="block-title" style="font-size:14px;margin-bottom:0px;">Users</div>
								<img src="../assets/img/icons/users.png" height="60" width="60"></a>
							</div>
							<div class="block col-xs-3 col-sm-4 col-md-3 col-lg-1" style="margin-right:5px;text-align:center;">
								<a href="discord_subscriptions.php" style="color:black;">
								<div class="block-title" style="font-size:14px;margin-bottom:0px;">Bot Access</div>
								<img src="../assets/img/icons/discord.png" height="60" width="60"></a>
							</div>
                        </div>
						
                    </div>
                </div>
            </div>
        </div>
		<?php include("../assets/php/_end.php"); ?>
	</body>
</html>