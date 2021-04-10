<?php
	error_reporting(0);
	include("assets/php/database.php");
	include("assets/php/functions.php");
	include("assets/php/session.php");
	include("assets/php/paginator.php");
	include("assets/php/acct/check.php");
	
	if ($_SESSION['user_privs']['transactions'] == 0 && $_SESSION['user_privs']['admin'] == 0) {
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
                                        <h1>Transactions</h1>
                                    </div>
                                </div>
                                <div class="col-sm-6 hidden-xs">
                                    <div class="header-section">
                                        <ul class="breadcrumb breadcrumb-top">
                                            <li>Databases</li>
                                            <li><a href="transactions.php">Transactions</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
						<?php if(isset($alert)){echo $alert;} ?>
						
                        <!-- Example Block -->
                        <div class="block">
                            <!-- Example Title -->
                            <div class="block-title">
                                <h2>Transactions Log</h2>
                            </div>
                            <!-- Example Content -->
                            <?php include("assets/php/transactions/data.php"); ?>
                        </div>
						
                    </div>
                </div>
            </div>
        </div>
		<?php include("assets/php/_end.php"); ?>
	</body>
</html>