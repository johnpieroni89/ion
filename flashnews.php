<?php
include("autoload.php");
global $db;
global $site;
global $session;
global $account;
$session->check_login();
	
if ($_SESSION['user_privs']['flashnews'] == 0 && $_SESSION['user_privs']['admin'] == 0) {
	header("Location: index.php");
}
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
                                        <h1>Flashnews</h1>
                                    </div>
                                </div>
                                <div class="col-sm-6 hidden-xs">
                                    <div class="header-section">
                                        <ul class="breadcrumb breadcrumb-top">
                                            <li>Databases</li>
                                            <li><a href="flashnews.php">Flashnews</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
						<?php if(isset($session->alert)){echo $session->alert;} ?>
						
                        <!-- Example Block -->
                        <div class="block">
                            <!-- Example Title -->
                            <div class="block-title">
                                <h2>Flashnews Feed</h2>
                            </div>
                            <!-- Example Content -->
                            <?php include("assets/php/flashnews/data.php"); ?>
                        </div>
						
                    </div>
                </div>
            </div>
        </div>
		<?php include("assets/php/_end.php"); ?>
	</body>
</html>