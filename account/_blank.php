<?php 
	include("../assets/php/classes/Database.php");
	include("../assets/php/functions.php");
	include("../assets/php/session.php");
	include("../assets/php/acct/check.php");
	if(!isset($_SESSION['user_id'])){ header("Location: ../index.php");}
	
	if(isset($_POST['avatar_url'])){
		if(filter_var($url, FILTER_VALIDATE_URL)){
			
		}
		$alert = "<div class=\"alert alert-danger\" style=\"font-size:14px;\"><strong>You have entered an invalid url!</strong></div>";
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
                                        <h1>Profile</h1>
                                    </div>
                                </div>
                                <div class="col-sm-6 hidden-xs">
                                    <div class="header-section">
                                        <ul class="breadcrumb breadcrumb-top">
                                            <li>Account</li>
                                            <li><a href="profile.php">Profile</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
						<?php if(isset($alert)){echo $alert;} ?>

                        <!-- Block -->
                        <div class="block">
                            <!-- Example Title -->
                            <div class="block-title" style="border:2px solid black;">
								<ul class="nav nav-tabs">
									<li class="nav-item">
										<a class="nav-link active" href="profile.php">Profile</a>
									</li>
									<li class="nav-item">
										<a class="nav-link" href="avatar.php">Avatar</a>
									</li>
									<li class="nav-item">
										<a class="nav-link" href="security.php">Security</a>
									</li>
									<li class="nav-item">
										<a class="nav-link" href="logs.php">Logs</a>
									</li>
								</ul>
                            </div>
                            <!-- Content -->
							<div class="tab-content">
								<div class="tab-pane active" id="profile">
									Profile
								</div>
							</div>
                        </div>
						
                    </div>
                </div>
            </div>
        </div>
		<?php include("../assets/php/_end.php"); ?>
	</body>
</html>