<?php 
    include("../autoload.php");
    global $db;
    global $site;
    global $session;
    global $account;
    $session->check_login();
	if(!isset($_SESSION['user_id']) || $_SESSION['user_privs']['admin'] == 0){ header("Location: ../index.php");}
		
	if($_POST['submit_bootstrap']){
		$file1 = $_POST['inputBootstrap3css'];
		$file2 = $_POST['inputBootstrap3js'];
		$file_headers1 = @get_headers($file1);
		$file_headers2 = @get_headers($file2);
		$info1 = pathinfo($file1);
		$info2 = pathinfo($file2);
		if(((!$file_headers1 || $file_headers1[0] == 'HTTP/1.1 404 Not Found') || (!$file_headers2 || $file_headers2[0] == 'HTTP/1.1 404 Not Found')) || $info1["extension"] != "css" || $info2["extension"] == "js"){
			$session->alert = "<div class=\"alert alert-warning\" style=\"font-size:14px;\">URL does not exist. Changes have been reverted.</div>";
		}else{
			mysqli_query($db->connection,"UPDATE site_settings SET value = '".$_POST['inputBootstrap3css']."' WHERE field = 'plugin_bootstrap3_css'");
			mysqli_query($db->connection,"UPDATE site_settings SET value = '".$_POST['inputBootstrap3js']."' WHERE field = 'plugin_bootstrap3_js'");
			$session->alert = "<div class=\"alert alert-success\" style=\"font-size:14px;\">Bootstrap 3 plugins have been updated.</div>";
		}
	}elseif($_POST['submit_chartjs']){
		$file = $_POST['inputchartjs'];
		$file_headers = @get_headers($file);
		$info = pathinfo($file);
		if(!$file_headers || $file_headers[0] == 'HTTP/1.1 404 Not Found' || $info["extension"] != "js"){
			$session->alert = "<div class=\"alert alert-warning\" style=\"font-size:14px;\">URL does not exist. Changes have been reverted.</div>";
		}else{
			mysqli_query($db->connection,"UPDATE site_settings SET value = '".$_POST['inputchartjs']."' WHERE field = 'plugin_chartjs'");
			$session->alert = "<div class=\"alert alert-success\" style=\"font-size:14px;\">ChartJS plugin has been updated.</div>";
		}
	}elseif($_POST['submit_fontawesome']){
		$file = $_POST['inputFontAwesomecss'];
		$file_headers = @get_headers($file);
		$info = pathinfo($file);
		if(!$file_headers || $file_headers[0] == 'HTTP/1.1 404 Not Found' || $info["extension"] != "css"){
			$session->alert = "<div class=\"alert alert-warning\" style=\"font-size:14px;\">URL does not exist. Changes have been reverted.</div>";
		}else{
			mysqli_query($db->connection,"UPDATE site_settings SET value = '".$_POST['inputFontAwesomecss']."' WHERE field = 'plugin_fontawesome'");
			$session->alert = "<div class=\"alert alert-success\" style=\"font-size:14px;\">FontAwesome plugin has been updated.</div>";
		}
	}elseif($_POST['submit_jquery']){
		$file = $_POST['inputJqueryjs'];
		$file_headers = @get_headers($file);
		$info = pathinfo($file);
		if(!$file_headers || $file_headers[0] == 'HTTP/1.1 404 Not Found' || $info["extension"] != "js"){
			$session->alert = "<div class=\"alert alert-warning\" style=\"font-size:14px;\">URL does not exist. Changes have been reverted.</div>";
		}else{
			mysqli_query($db->connection,"UPDATE site_settings SET value = '".$_POST['inputJqueryjs']."' WHERE field = 'plugin_jquery'");
			$session->alert = "<div class=\"alert alert-success\" style=\"font-size:14px;\">jQuery plugin has been updated.</div>";
		}
	}elseif($_POST['submit_modernizr']){
		$file = $_POST['inputModernizrjs'];
		$file_headers = @get_headers($file);
		$info = pathinfo($file);
		if(!$file_headers || $file_headers[0] == 'HTTP/1.1 404 Not Found' || $info["extension"] != "js"){
			$session->alert = "<div class=\"alert alert-warning\" style=\"font-size:14px;\">URL does not exist. Changes have been reverted.</div>";
		}else{
			mysqli_query($db->connection,"UPDATE site_settings SET value = '".$_POST['inputModernizrjs']."' WHERE field = 'plugin_modernizr'");
			$session->alert = "<div class=\"alert alert-success\" style=\"font-size:14px;\">Modernizr plugin has been updated.</div>";
		}
	}
	
	$plugin_bootstrap3_css = mysqli_fetch_assoc(mysqli_query($db->connection, "SELECT value FROM site_settings WHERE field = 'plugin_bootstrap3_css'"));
	$plugin_bootstrap3_js = mysqli_fetch_assoc(mysqli_query($db->connection, "SELECT value FROM site_settings WHERE field = 'plugin_bootstrap3_js'"));
	$plugin_chartjs = mysqli_fetch_assoc(mysqli_query($db->connection, "SELECT value FROM site_settings WHERE field = 'plugin_chartjs'"));
	$plugin_fontawesome = mysqli_fetch_assoc(mysqli_query($db->connection, "SELECT value FROM site_settings WHERE field = 'plugin_fontawesome'"));
	$plugin_jquery = mysqli_fetch_assoc(mysqli_query($db->connection, "SELECT value FROM site_settings WHERE field = 'plugin_jquery'"));
	$plugin_modernizr = mysqli_fetch_assoc(mysqli_query($db->connection, "SELECT value FROM site_settings WHERE field = 'plugin_modernizr'"));
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
                                        <h1>Plugins</h1>
                                    </div>
                                </div>
                                <div class="col-sm-6 hidden-xs">
                                    <div class="header-section">
                                        <ul class="breadcrumb breadcrumb-top">
                                            <li>Admin</li>
                                            <li><a href="panel.php">Panel</a></li>
											<li><a href="">Plugins</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
						<?php if(isset($session->alert)){echo $session->alert;} ?>

                        <!-- Block -->
                        <div class="block col-xs-12 col-sm-12 col-md-12 col-lg-12" style="overflow: auto;">
                            <!-- Title -->
                            <div class="block-title">
                                <h2>Bootstrap v3</h2>
                            </div>
							Current Version: <?php preg_match('/https:\/\/maxcdn.bootstrapcdn.com\/bootstrap\/(.*)\/css\/bootstrap.min.css/', $plugin_bootstrap3_css['value'], $bootstrap_c); echo $bootstrap_c[1] ?><br>
							<a href="https://getbootstrap.com/docs/versions/" target="_blank">Check here</a> for Bootstrap versions<br><br>
							
							<form method="post">
								<div class="form-group">
									<label for="inputBootstrap3css">Bootstrap 3 Style Sheet (.css)</label>
									<input type="url" class="form-control" id="inputBootstrap3css" name="inputBootstrap3css" placeholder="URL" value="<?php echo $plugin_bootstrap3_css['value'];?>">
								</div>
								<div class="form-group">
									<label for="inputBootstrap3js">Bootstrap 3 JavaScript (.js)</label>
									<input type="url" class="form-control" id="inputBootstrap3js" name="inputBootstrap3js" placeholder="URL" value="<?php echo $plugin_bootstrap3_js['value'];?>">
								</div>
								<input type="submit" class="btn btn-primary" name="submit_bootstrap" value="Save">
							</form>
                        </div>
						
						<!-- Block -->
                        <div class="block col-xs-12 col-sm-12 col-md-12 col-lg-12" style="overflow: auto;">
                            <!-- Title -->
                            <div class="block-title">
                                <h2>ChartJS</h2>
                            </div>
							Current Version: <?php preg_match('/https:\/\/cdn\.jsdelivr\.net\/npm\/chart\.js@(.*)\/dist\/Chart\.min\.js/', $plugin_chartjs['value'], $chartjs); echo $chartjs[1] ?><br>
							<a href="https://www.jsdelivr.com/package/npm/chart.js" target="_blank">Check here</a> for ChartJS versions<br><br>
							
							<form method="post">
								<div class="form-group">
									<label for="inputBootstrap3css">ChartJS (.js)</label>
									<input type="url" class="form-control" id="inputchartjs" name="inputchartjs" placeholder="URL" value="<?php echo $plugin_chartjs['value'];?>">
								</div>
								<input type="submit" class="btn btn-primary" name="submit_chartjs" value="Save">
							</form>
                        </div>
						
						<!-- Block -->
                        <div class="block col-xs-12 col-sm-12 col-md-12 col-lg-12" style="overflow: auto;">
                            <!-- Title -->
                            <div class="block-title">
                                <h2>Font Awesome</h2>
                            </div>
							Current Version: <?php preg_match('/https:\/\/use.fontawesome.com\/releases\/v(.*)\/css\/all.css/', $plugin_fontawesome['value'], $fontawesome_c); echo $fontawesome_c[1] ?><br>
							<a href="https://fontawesome.com/start" target="_blank">Check here</a> for latest Font Awesome version<br><br>
							
							<form method="post">
								<div class="form-group">
									<label for="inputFontAwesomecss">Font Awesome Style Sheet (.css)</label>
									<input type="url" class="form-control" id="inputFontAwesomecss" name="inputFontAwesomecss" placeholder="URL" value="<?php echo $plugin_fontawesome['value'];?>">
								</div>
								<input type="submit" class="btn btn-primary" name="submit_fontawesome" value="Save">
							</form>
                        </div>
						
						<!-- Block -->
                        <div class="block col-xs-12 col-sm-12 col-md-12 col-lg-12" style="overflow: auto;">
                            <!-- Title -->
                            <div class="block-title">
                                <h2>jQuery</h2>
                            </div>
							Current Version: <?php preg_match('/https:\/\/code.jquery.com\/jquery-(.*).min.js/', $plugin_jquery['value'], $jquery_c); echo $jquery_c[1] ?><br>
							<a href="https://code.jquery.com/jquery/" target="_blank">Check here</a> for latest jQuery version<br><br>
							
							<form method="post">
								<div class="form-group">
									<label for="inputJqueryjs">jQuery JavaScript (.js)</label>
									<input type="url" class="form-control" id="inputJqueryjs" name="inputJqueryjs" placeholder="URL" value="<?php echo $plugin_jquery['value'];?>">
								</div>
								<input type="submit" class="btn btn-primary" name="submit_jquery" value="Save">
							</form>
                        </div>
						
						<!-- Block -->
                        <div class="block col-xs-12 col-sm-12 col-md-12 col-lg-12" style="overflow: auto;">
                            <!-- Title -->
                            <div class="block-title">
                                <h2>Modernizr</h2>
                            </div>
							Current Version: <?php preg_match('/([0-9*]\.[0-9*]\.[0-9*])/', $plugin_modernizr['value'], $modernizr_c); echo $modernizr_c[1] ?><br>
							<a href="https://modernizr.com/resources" target="_blank">Check here</a> for latest Modernizr version<br><br>
							
							<form method="post">
								<div class="form-group">
									<label for="inputModernizrjs">Modernizr JavaScript (.js)</label>
									<input type="url" class="form-control" id="inputModernizrjs" name="inputModernizrjs" placeholder="URL" value="<?php echo $plugin_modernizr['value'];?>">
								</div>
								<input type="submit" class="btn btn-primary" name="submit_modernizr" value="Save">
							</form>
                        </div>
						
                    </div>
                </div>
            </div>
        </div>
		<?php include("../assets/php/_end.php"); ?>
	</body>
</html>