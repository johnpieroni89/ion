<?php 
	error_reporting(0);
	include("assets/php/database.php");
	include("assets/php/functions.php");
	include("assets/php/session.php");
	include("assets/php/acct/check.php");
?>

	<?php include("assets/php/_head.php"); ?>
    <body>
        <!-- Page Wrapper -->
        <div id="page-wrapper" class="page-loading">
            <?php include("assets/php/_preloader.php"); ?>
			<!-- Page Container -->
            <div id="page-container" class="header-fixed-top sidebar-visible-lg-full">
                
                <?php include("assets/php/_sidebar-alt.php"); ?>
                <?php include("assets/php/_sidebar.php"); ?>

                <!-- Main Container -->
                <div id="main-container">
                    <?php include("assets/php/_header.php"); ?>

                    <!-- Page content -->
                    <div id="page-content" style="overflow:auto;">
                        <!-- Page Header -->
                        <div class="content-header">
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="header-section">
                                        <h1>Dashboard</h1>
                                    </div>
                                </div>
                                <div class="col-sm-6 hidden-xs">
                                    <div class="header-section">
                                        <ul class="breadcrumb breadcrumb-top">
                                            <li><a href="">Dashboard</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
						<?php if(isset($alert)){echo $alert;} ?>
                        <!-- Example Block -->
                        <div class="block" style="min-height:700px;">
                            <!-- Example Title -->
                            <div class="block-title">
                                <h2><?php echo $_SESSION['site_description']; ?></h2>
                            </div>
                            <!-- Example Content -->
							<div class="block-content col-sm-12">
								<center><img class="img-responsive" style="max-height:600px;" src="<?php if(glob("assets/img/logo.png")){ echo "assets/img/logo.png";}else{echo "../assets/img/logo.png";} ?>"></center>
							</div>
                        </div>
						
                    </div>
                </div>
            </div>
        </div>
		<?php include("assets/php/_end.php"); ?>
	</body>
</html>