<?php 
	error_reporting(0);
	include("../assets/php/database.php");
	include("../assets/php/session.php");
	include("../assets/php/acct/check.php");
	include("../assets/php/functions.php");
	if(!isset($_SESSION['user_id']) || $_SESSION['user_privs']['admin'] == 0){ header("Location: ../index.php");}
	
	$db = new database;
	$db->connect();
	
	if(isset($_POST['type'])){
		if($_POST["type"] == "sectors"){
			include("../assets/php/swc_api/galaxy_sectors.php");
			$alert = "<div class=\"alert alert-success\" style=\"font-size:14px;\">Reference tables for all sectors have been updated.</div>";
		}elseif($_POST["type"] == "systems"){
			include("../assets/php/swc_api/galaxy_systems.php");
			$alert = "<div class=\"alert alert-success\" style=\"font-size:14px;\">Reference tables for all systems have been updated.</div>";
		}elseif($_POST["type"] == "planets"){
			include("../assets/php/swc_api/galaxy_planets.php");
			$alert = "<div class=\"alert alert-success\" style=\"font-size:14px;\">Reference tables for all planets have been updated.</div>";
		}elseif($_POST["type"] == "entities"){
			include("../assets/php/swc_api/entities.php");
			$alert = "<div class=\"alert alert-success\" style=\"font-size:14px;\">Reference tables for all entity types have been updated.</div>";
		}elseif($_POST["type"] == "classes"){
			include("../assets/php/swc_api/entities_classes.php");
			$alert = "<div class=\"alert alert-success\" style=\"font-size:14px;\">Reference tables for all entity classes have been updated.</div>";
		}elseif($_POST["type"] == "races"){
			include("../assets/php/swc_api/entities_races.php");
			$alert = "<div class=\"alert alert-success\" style=\"font-size:14px;\">Reference tables for all races have been updated.</div>";
		}elseif($_POST["type"] == "factions"){
			include("../assets/php/swc_api/factions.php");
			$alert = "<div class=\"alert alert-success\" style=\"font-size:14px;\">Reference tables for all factions have been updated.</div>";
		}elseif($_POST["type"] == "stations"){
			include("../assets/php/swc_api/scanner_stations.php");
			$alert = "<div class=\"alert alert-success\" style=\"font-size:14px;\">Reference tables for all public space stations have been updated.</div>";
		}
	}elseif(isset($_POST['changeOwner'])){
		$former = ucwords(mysqli_real_escape_string($db->connection, $_POST['inputFormer']));
		$current = ucwords(mysqli_real_escape_string($db->connection, $_POST['inputCurrent']));
		if(!empty($former) && !empty($current)){
			$total = mysqli_num_rows(mysqli_query($db->connection, "SELECT uid FROM data_signalsanalysis WHERE owner = '$former'"));
			mysqli_query($db->connection, "UPDATE data_signalsanalysis SET owner = '$current' WHERE owner = '$former'");
			$alert = "<div class=\"alert alert-success\" style=\"font-size:14px;\">$total records have been changed to being owned by $current.</div>";
			mysqli_query($db->connection, "INSERT INTO logs_activities (user_id, log_type, details, timestamp) VALUES ('".$_SESSION['user_id']."', '7', \"$total records have been changed from being owned by $former to $current\", '".swc_time(time(),TRUE)["timestamp"]."')");
		}else{
			$alert = "<div class=\"alert alert-warning\" style=\"font-size:14px;\">You must enter a former and current owner to make changes.</div>";
		}
	}
	
	$sectors = mysqli_fetch_assoc(mysqli_query($db->connection, "SELECT COUNT(uid) as count FROM galaxy_sectors"))["count"];
	$sectors_update = mysqli_fetch_assoc(mysqli_query($db->connection, "SELECT value FROM site_settings WHERE field='last_update_sectors'"))["value"];
	$sectors_update = swc_time($sectors_update);
	$sectors_start = mysqli_fetch_assoc(mysqli_query($db->connection, "SELECT value FROM site_settings WHERE field='update_sectors_count'"))["value"];
	$systems = mysqli_fetch_assoc(mysqli_query($db->connection, "SELECT COUNT(uid) as count FROM galaxy_systems"))["count"];
	$systems_update = mysqli_fetch_assoc(mysqli_query($db->connection, "SELECT value FROM site_settings WHERE field='last_update_systems'"))["value"];
	$systems_update = swc_time($systems_update);
	$systems_start = mysqli_fetch_assoc(mysqli_query($db->connection, "SELECT value FROM site_settings WHERE field='update_systems_count'"))["value"];
	$planets = mysqli_fetch_assoc(mysqli_query($db->connection, "SELECT COUNT(uid) as count FROM galaxy_planets"))["count"];
	$planets_update = mysqli_fetch_assoc(mysqli_query($db->connection, "SELECT value FROM site_settings WHERE field='last_update_planets'"))["value"];
	$planets_update = swc_time($planets_update);
	$planets_start = mysqli_fetch_assoc(mysqli_query($db->connection, "SELECT value FROM site_settings WHERE field='update_planets_count'"))["value"];
	
	$entities_facilities = mysqli_fetch_assoc(mysqli_query($db->connection, "SELECT COUNT(uid) as count FROM entities WHERE uid LIKE '4:%'"))["count"];
	$entities_ships = mysqli_fetch_assoc(mysqli_query($db->connection, "SELECT COUNT(uid) as count FROM entities WHERE uid LIKE '2:%'"))["count"];
	$entities_stations = mysqli_fetch_assoc(mysqli_query($db->connection, "SELECT COUNT(uid) as count FROM entities WHERE uid LIKE '5:%'"))["count"];
	$entities_vehicles = mysqli_fetch_assoc(mysqli_query($db->connection, "SELECT COUNT(uid) as count FROM entities WHERE uid LIKE '3:%'"))["count"];
	$entities_update = mysqli_fetch_assoc(mysqli_query($db->connection, "SELECT value FROM site_settings WHERE field='last_update_entities'"))["value"];
	$entities_update = swc_time($entities_update);
	$entities_classes = mysqli_fetch_assoc(mysqli_query($db->connection, "SELECT COUNT(uid) as count FROM entities_classes"))["count"];
	$entities_classes_update = mysqli_fetch_assoc(mysqli_query($db->connection, "SELECT value FROM site_settings WHERE field='last_update_classes'"))["value"];
	$entities_classes_update = swc_time($entities_classes_update);
	$races_update = mysqli_fetch_assoc(mysqli_query($db->connection, "SELECT value FROM site_settings WHERE field='last_update_races'"))["value"];
	$races_update = swc_time($races_update);
	$entities_races = mysqli_fetch_assoc(mysqli_query($db->connection, "SELECT COUNT(uid) as count FROM entities_races"))["count"];
	
	$factions = mysqli_fetch_assoc(mysqli_query($db->connection, "SELECT COUNT(uid) as count FROM factions"))["count"];
	$factions_update = mysqli_fetch_assoc(mysqli_query($db->connection, "SELECT value FROM site_settings WHERE field='last_update_factions'"))["value"];
	$factions_update = swc_time($factions_update);
	
	$stations = mysqli_fetch_assoc(mysqli_query($db->connection, "SELECT COUNT(uid) as count FROM data_signalsanalysis WHERE uid LIKE '5:%' AND `system` IS NOT NULL"))["count"];
	$stations_update = mysqli_fetch_assoc(mysqli_query($db->connection, "SELECT value FROM site_settings WHERE field='last_update_stations'"))["value"];
	$stations_update = swc_time($stations_update);
	$stations_start = mysqli_fetch_assoc(mysqli_query($db->connection, "SELECT value FROM site_settings WHERE field='update_stations_count'"))["value"];
	
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
                                        <h1>Database</h1>
                                    </div>
                                </div>
                                <div class="col-sm-6 hidden-xs">
                                    <div class="header-section">
                                        <ul class="breadcrumb breadcrumb-top">
                                            <li>Admin</li>
                                            <li><a href="panel.php">Panel</a></li>
											<li><a href="">Database</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
						<?php if(isset($alert)){echo $alert;} ?>

                        <!-- Block -->
                        <div class="block col-xs-12 col-sm-12 col-md-12 col-lg-12">
                            <!-- Title -->
                            <div class="block-title bg-primary">
                                <h2>Galaxy Tables</h2>
                            </div>
							
							<div class="col-sm-12" style="border:1px solid black;margin:2px;">
								<div class="col-sm-3 col-md-1">Sectors</div>
								<div class="col-sm-3 col-md-1"><?php echo $sectors;?></div>
								<div class="col-sm-3 col-md-1">
									<form method="post">
										<input type="submit" class="btn btn-primary" name="update" value="Update">
										<input type="hidden" name="type" value="sectors">
									</form>
								</div>
								<div class="col-sm-3 col-md-2">Last Updated: <?php echo "Year ".$sectors_update["year"]." Day ".$sectors_update["day"]." ".$sectors_update["hour"].":".$sectors_update["minute"]; ?></div>
								<div title="Used to reference the last count of the scraper if it times out, so that it can pick up where it left off." class="col-sm-2 col-md-2">Last Count: <?php echo $sectors_start; ?></div>
							</div>
							
							<div class="col-sm-12" style="border:1px solid black;margin:2px;">
								<div class="col-sm-3 col-md-1">Systems</div>
								<div class="col-sm-3 col-md-1"><?php echo $systems;?></div>
								<div class="col-sm-3 col-md-1">
									<form method="post">
										<input type="submit" class="btn btn-primary" name="update" value="Update">
										<input type="hidden" name="type" value="systems">
									</form>
								</div>
								<div class="col-sm-3 col-md-2">Last Updated: <?php echo "Year ".$systems_update["year"]." Day ".$systems_update["day"]." ".$systems_update["hour"].":".$systems_update["minute"]; ?></div>
								<div title="Used to reference the last count of the scraper if it times out, so that it can pick up where it left off." class="col-sm-2 col-md-2">Last Count: <?php echo $systems_start; ?></div>
							</div>
							
							<div class="col-sm-12" style="border:1px solid black;margin:2px;">
								<div class="col-sm-3 col-md-1">Planets</div>
								<div class="col-sm-3 col-md-1"><?php echo $planets;?></div>
								<div class="col-sm-3 col-md-1">
									<form method="post">
										<input type="submit" class="btn btn-primary" name="update" value="Update">
										<input type="hidden" name="type" value="planets">
									</form>
								</div>
								<div class="col-sm-3 col-md-2">Last Updated: <?php echo "Year ".$planets_update["year"]." Day ".$planets_update["day"]." ".$planets_update["hour"].":".$planets_update["minute"]; ?></div>
								<div title="Used to reference the last count of the scraper if it times out, so that it can pick up where it left off." class="col-sm-2 col-md-2">Last Count: <?php echo $planets_start; ?></div>
							</div>
                        </div>
						
						<!-- Block -->
                        <div class="block col-xs-12 col-sm-12 col-md-12 col-lg-12">
                            <!-- Title -->
                            <div class="block-title bg-primary">
                                <h2>Entity Tables</h2>
                            </div>
							
							<div class="col-sm-12" style="border:1px solid black;margin:2px;">
								<div class="col-sm-3 col-md-1">Facilities</div>
								<div class="col-sm-3 col-md-1"><?php echo $entities_facilities;?></div>
							</div>
							
							<div class="col-sm-12" style="border:1px solid black;margin:2px;">
								<div class="col-sm-3 col-md-1">Ships</div>
								<div class="col-sm-3 col-md-1"><?php echo $entities_ships;?></div>
							</div>
							
							<div class="col-sm-12" style="border:1px solid black;margin:2px;">
								<div class="col-sm-3 col-md-1">Stations</div>
								<div class="col-sm-3 col-md-1"><?php echo $entities_stations;?></div>
							</div>
							
							<div class="col-sm-12" style="border:1px solid black;margin:2px;">
								<div class="col-sm-3 col-md-1">Vehicles</div>
								<div class="col-sm-3 col-md-1"><?php echo $entities_vehicles;?></div>
							</div>
							
							<div class="col-sm-12" style="border:1px solid black;margin:2px;">
								<div class="col-sm-3 col-md-1">All Entities</div>
								<div class="col-sm-3 col-md-1"></div>
								<div class="col-sm-3 col-md-1">
									<form method="post">
										<input type="submit" class="btn btn-primary" name="update" value="Update">
										<input type="hidden" name="type" value="entities">
									</form>
								</div>
								<div class="col-sm-3 col-md-2">Last Updated: <?php echo "Year ".$entities_update["year"]." Day ".$entities_update["day"]." ".$entities_update["hour"].":".$entities_update["minute"]; ?></div>
							</div>
							
							<div class="col-sm-12" style="border:1px solid black;margin:2px;">
								<div class="col-sm-3 col-md-1">Entity Classes</div>
								<div class="col-sm-3 col-md-1"><?php echo $entities_classes;?></div>
								<div class="col-sm-3 col-md-1">
									<form method="post">
										<input type="submit" class="btn btn-primary" name="update" value="Update">
										<input type="hidden" name="type" value="classes">
									</form>
								</div>
								<div class="col-sm-3 col-md-2">Last Updated: <?php echo "Year ".$entities_classes_update["year"]." Day ".$entities_classes_update["day"]." ".$entities_classes_update["hour"].":".$entities_classes_update["minute"]; ?></div>
							</div>
							
							<div class="col-sm-12" style="border:1px solid black;margin:2px;">
								<div class="col-sm-3 col-md-1">Races</div>
								<div class="col-sm-3 col-md-1"><?php echo $entities_races;?></div>
								<div class="col-sm-3 col-md-1">
									<form method="post">
										<input type="submit" class="btn btn-primary" name="update" value="Update">
										<input type="hidden" name="type" value="races">
									</form>
								</div>
								<div class="col-sm-3 col-md-2">Last Updated: <?php echo "Year ".$races_update["year"]." Day ".$races_update["day"]." ".$races_update["hour"].":".$races_update["minute"]; ?></div>
							</div>
                        </div>
						
						<!-- Block -->
                        <div class="block col-xs-12 col-sm-12 col-md-12 col-lg-12">
                            <!-- Title -->
                            <div class="block-title bg-primary">
                                <h2>Faction Tables</h2>
                            </div>
							
							<div class="col-sm-12" style="border:1px solid black;margin:2px;">
								<div class="col-sm-2 col-md-1">Factions</div>
								<div class="col-sm-3 col-md-1"><?php echo $factions;?></div>
								<div class="col-sm-2 col-md-1">
									<form method="post">
										<input type="submit" class="btn btn-primary" name="update" value="Update">
										<input type="hidden" name="type" value="factions">
									</form>
								</div>
								<div class="col-sm-3 col-md-2">Last Updated: <?php echo "Year ".$factions_update["year"]." Day ".$factions_update["day"]." ".$factions_update["hour"].":".$factions_update["minute"]; ?></div>
							</div>
                        </div>
						
						<!-- Block -->
                        <div class="block col-xs-12 col-sm-12 col-md-12 col-lg-12">
                            <!-- Title -->
                            <div class="block-title bg-primary">
                                <h2>Public Space Station Data</h2>
                            </div>
							
							<div class="col-sm-12" style="border:1px solid black;margin:2px;">
								<div class="col-sm-2 col-md-1">Stations</div>
								<div class="col-sm-3 col-md-1"><?php echo $stations;?></div>
								<div class="col-sm-2 col-md-1">
									<form method="post">
										<input type="submit" class="btn btn-primary" name="update" value="Update">
										<input type="hidden" name="type" value="stations">
									</form>
								</div>
								<div class="col-sm-3 col-md-2">Last Updated: <?php echo "Year ".$stations_update["year"]." Day ".$stations_update["day"]." ".$stations_update["hour"].":".$stations_update["minute"]; ?></div>
								<div title="Used to reference the last count of the scraper if it times out, so that it can pick up where it left off." class="col-sm-2 col-md-2">Last Count: <?php echo $stations_start; ?></div>
							</div>
                        </div>
						
						<!-- Block -->
                        <div class="block col-xs-12 col-sm-12 col-md-12 col-lg-12">
                            <!-- Title -->
                            <div class="block-title" style="background-color: red; color: white;">
                                <h2>Mass Entity Change of Owner (Caution: This changes all entities from one owner to another)</h2>
                            </div>
							<div class="col-sm-12">
								<form method="post" class="form-horizontal" action="">
									<div class="form-group" style="vertical-align:middle;">
										<label class="col-sm-2" for="inputFormer">Former Owner</label>
										<div class="col-sm-10"><input class="form-control" id="inputFormer" name="inputFormer" type="text" placeholder="{Former Owner}"></div>
									</div>
									<div class="form-group" style="vertical-align:middle;">
										<label class="col-sm-2" for="inputCurrent">Current Owner</label>
										<div class="col-sm-10"><input class="form-control" id="inputCurrent" name="inputCurrent" type="text" placeholder="{Current Owner}"></div>
									</div>
									<div class="form-group" style="vertical-align:middle;">
										<div class="col-sm-12"><input class="form-control pull-right btn-primary" style="color:white;" id="changeOwner" name="changeOwner" type="submit"></div>
									</div>
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