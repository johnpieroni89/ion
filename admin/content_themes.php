<?php 
    include("../autoload.php");
    global $db;
    global $site;
    global $session;
    global $account;
    $session->check_login();

    if(!isset($_SESSION['user_id']) || $_SESSION['user_privs']['admin'] == 0 || $_SESSION['user_privs']['admin'] == 1){ header("Location: ../index.php");}
	
	if($_POST){
		$theme = mysqli_real_escape_string($db->connection,$_POST['inputTheme']);
		mysqli_query($db->connection,"UPDATE site_settings SET value = '$theme' WHERE field = 'default_style'");
		header("Location: content_themes.php?success=true");
	}elseif($_GET){
		$session->alert = "<div class=\"alert alert-success\" style=\"font-size:14px;\">Successfully updated site theme!</div>";
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
                                        <h1>Themes</h1>
                                    </div>
                                </div>
                                <div class="col-sm-6 hidden-xs">
                                    <div class="header-section">
                                        <ul class="breadcrumb breadcrumb-top">
                                            <li>Admin</li>
                                            <li><a href="panel.php">Panel</a></li>
                                            <li><a href="">Themes</a></li>
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
                                <h2>Themes</h2>
                            </div>
							<form class="form-horizontal" role="form" method="post" action="content_themes.php">
								<div class="modal-body">
									<div class="form-group">
										<label class="col-sm-3" for="inputTheme">Theme</label>
										<div class="col-sm-9">
											<select class="form-control" id="inputTheme" name="inputTheme">
												<?php
													$files = glob("../assets/css/themes/*.css");
													foreach($files as $file){
														if(basename($file) == $_SESSION['site_style'].".css"){
															echo "<option selected value=\"".explode(".",basename($file))[0]."\">".ucfirst(explode(".",basename($file))[0])."</option>";
														}else{
															echo "<option value=\"".explode(".",basename($file))[0]."\">".ucfirst(explode(".",basename($file))[0])."</option>";
														}
													}
												?>
											</select>
											<input id="submit" class="btn btn-primary " type="submit" value="Submit">
										</div>
									</div>
								</div>
							</form>
                                      
                        </div>
                        
                    </div>
                </div>
            </div>
        </div>
        <?php include("../assets/php/_end.php"); ?>
    </body>
</html>