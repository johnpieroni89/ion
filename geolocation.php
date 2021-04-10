<?php
	error_reporting(0);
	include("assets/php/database.php");
	include("assets/php/functions.php");
	include("assets/php/session.php");
	include("assets/php/paginator.php");
	include("assets/php/acct/check.php");
	
	if ($_SESSION['user_privs']['geolocation'] == 0 && $_SESSION['user_privs']['admin'] == 0) {
		header("Location: index.php");
	}
	
	if(isset($_POST['submit'])){
		$db = new database;
		$db->connect();
		$target = mysqli_real_escape_string($db->connection, $_POST['inputTarget']);
		$confidence = mysqli_real_escape_string($db->connection, $_POST['inputConfidence']);
		$date = mysqli_real_escape_string($db->connection, $_POST['inputDate']);
		$time = mysqli_real_escape_string($db->connection, $_POST['inputTime']);
		$galx = mysqli_real_escape_string($db->connection, $_POST['inputGalX']);
		$galy = mysqli_real_escape_string($db->connection, $_POST['inputGalY']);
		$sysx = mysqli_real_escape_string($db->connection, $_POST['inputSysX']);
		$sysy = mysqli_real_escape_string($db->connection, $_POST['inputSysY']);
		$atmox = mysqli_real_escape_string($db->connection, $_POST['inputAtmoX']);
		$atmoy = mysqli_real_escape_string($db->connection, $_POST['inputAtmoY']);
		$surfx = mysqli_real_escape_string($db->connection, $_POST['inputSurfX']);
		$surfy = mysqli_real_escape_string($db->connection, $_POST['inputSurfY']);		
		
		if((!empty($sysx) && !empty($sysy)) && (!empty($atmox) && !empty($atmoy)) && (!empty($surfx) && !empty($surfy))){
			$galaxy = mysqli_fetch_assoc(mysqli_query($db->connection, "SELECT uid, `system`, sector FROM galaxy_planets WHERE galx='$galx' AND galy='$galy' AND sysx='$sysx' AND sysy='$sysy'"));
			mysqli_query($db->connection, "INSERT INTO data_tracking (target, confidence, source, timestamp, sector, galx, galy, `system`, sysx, sysy, planet, atmox, atmoy, surfx, surfy) VALUES ('$target', '$confidence', 'Manual Entry', '$timestamp', '".$galaxy['sector']."', '$galx', '$galy', '".$galaxy['system']."', '$sysx', '$sysy', '".$galaxy['uid']."', '$atmox', '$atmoy', '$surfx', '$surfy')");
		}elseif((!empty($sysx) && !empty($sysy)) && (!empty($atmox) && !empty($atmoy))){
			$galaxy = mysqli_fetch_assoc(mysqli_query($db->connection, "SELECT uid, `system`, sector FROM galaxy_planets WHERE galx='$galx' AND galy='$galy' AND sysx='$sysx' AND sysy='$sysy'"));
			mysqli_query($db->connection, "INSERT INTO data_tracking (target, confidence, source, timestamp, sector, galx, galy, `system`, sysx, sysy, planet, atmox, atmoy) VALUES ('$target', '$confidence', 'Manual Entry', '$timestamp', '".$galaxy['sector']."', '$galx', '$galy', '".$galaxy['system']."', '$sysx', '$sysy', '".$galaxy['uid']."', '$atmox', '$atmoy')");
		}elseif((!empty($sysx) && !empty($sysy))){
			$planet = mysqli_fetch_assoc(mysqli_query($db->connection, "SELECT uid FROM galaxy_planets WHERE galx='$galx' AND galy='$galy' AND sysx='$sysx' AND sysy='$sysy'"));
			$galaxy = mysqli_fetch_assoc(mysqli_query($db->connection, "SELECT uid, sector FROM galaxy_systems WHERE galx='$galx' AND galy='$galy' AND sysx='$sysx' AND sysy='$sysy'"));
		}else{
			
		}
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
                                        <h1>Geolocation</h1>
                                    </div>
                                </div>
                                <div class="col-sm-6 hidden-xs">
                                    <div class="header-section">
                                        <ul class="breadcrumb breadcrumb-top">
                                            <li>Databases</li>
                                            <li><a href="geolocation.php">Geolocation</a></li>
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
                                <h2>Geolocation Data</h2> 
								<?php
									if($_SESSION['user_privs']['geolocation'] == 2 || $_SESSION['user_privs']['admin'] != 0){
										echo "<button title=\"Add Geolocation\" class=\"btn btn-info pull-right hidden-print\" data-target=\"#modalAdd\" data-toggle=\"modal\"><span class=\"fas fa-plus\"></span> Add</button>";
									}
								?>
                            </div>
                            <!-- Example Content -->
                            <?php include("assets/php/geolocation/data.php"); ?>
                        </div>
						
                    </div>
					
					<div class="modal fade" id="modalAdd">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header">
									<button class="close" aria-hidden="true" type="button" data-dismiss="modal">×</button>
									<h4 class="modal-title">Add Geolocation</h4>
								</div>
								<form class="form-horizontal" role="form" method="post" action="">
									<div class="modal-body">
										<div class="form-group">
											<label class="col-sm-3" for="inputTarget">Target</label>
											<div class="col-sm-9"><input type="text" class="form-control" id="inputTarget" name="inputTarget" placeholder="{target}" required></div>
										</div>
										<div class="form-group">
											<label class="col-sm-3" for="inputConfidence">Confidence</label>
											<div class="col-sm-9">
												<select class="form-control" id="inputConfidence" name="inputConfidence">
													<option value="1">Low</option>
													<option value="2">Medium</option>
													<option value="3">High</option>
												</select>
											</div>
										</div>
										<div class="form-group">
											<label class="col-sm-3" for="inputDate">Date</label>
											<div class="col-sm-9"><input type="text" class="form-control" id="inputDate" name="inputDate" pattern="[0-9]{2} [0-9]{1,3}" placeholder="{YY DDD}" required></div>
										</div>
										<div class="form-group">
											<label class="col-sm-3" for="inputTime">Time</label>
											<div class="col-sm-9"><input type="text" class="form-control" id="inputTime" name="inputTime" pattern="[0-9]{2}:[0-9]{2}" placeholder="{hh:mm}" required></div>
										</div>
										<div class="form-group">
											<label class="col-sm-3" for="inputGalX">Gal-X</label>
											<div class="col-sm-9"><input type="number" class="form-control" id="inputGalX" name="inputGalX" placeholder="{galaxy x-coord}" required></div>
										</div>
										<div class="form-group">
											<label class="col-sm-3" for="inputGalY">Gal-Y</label>
											<div class="col-sm-9"><input type="number" class="form-control" id="inputGalY" name="inputGalY" placeholder="{galaxy y-coord}" required></div>
										</div>
										<div class="form-group">
											<label class="col-sm-3" for="inputSysX">Sys-X</label>
											<div class="col-sm-9"><input type="number" class="form-control" id="inputSysX" name="inputSysX" placeholder="{system x-coord}"></div>
										</div>
										<div class="form-group">
											<label class="col-sm-3" for="inputSysY">Sys-Y</label>
											<div class="col-sm-9"><input type="number" class="form-control" id="inputSysY" name="inputSysY" placeholder="{system y-coord}"></div>
										</div>
										<div class="form-group">
											<label class="col-sm-3" for="inputAtmoX">Atmo-X</label>
											<div class="col-sm-9"><input type="number" class="form-control" id="inputAtmoX" name="inputAtmoX" placeholder="{atmosphere x-coord}"></div>
										</div>
										<div class="form-group">
											<label class="col-sm-3" for="inputAtmoY">Atmo-Y</label>
											<div class="col-sm-9"><input type="number" class="form-control" id="inputAtmoY" name="inputAtmoY" placeholder="{atmosphere y-coord}"></div>
										</div>
										<div class="form-group">
											<label class="col-sm-3" for="inputSurfX">Surf-X</label>
											<div class="col-sm-9"><input type="number" class="form-control" id="inputSurfX" name="inputSurfX" placeholder="{surface x-coord}"></div>
										</div>
										<div class="form-group">
											<label class="col-sm-3" for="inputSurfY">Surf-Y</label>
											<div class="col-sm-9"><input type="number" class="form-control" id="inputSurfY" name="inputSurfY" placeholder="{surface y-coord}"></div>
										</div>
									</div>
									<div class="modal-footer">
										<button class="btn btn-default pull-left" type="button" data-dismiss="modal">Cancel</button>
										<input id="submit" class="btn btn-primary " type="submit" name="submit" value="Add">
									</div>
								</form>
							</div>
						</div>
					</div>
                </div>
            </div>
        </div>
		<?php include("assets/php/_end.php"); ?>
	</body>
</html>