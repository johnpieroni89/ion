<?php 
	error_reporting(0);
	include("assets/php/database.php");
	include("assets/php/functions.php");
	include("assets/php/session.php");
	include("assets/php/paginator.php");
	include("assets/php/acct/check.php");
	
	if ($_SESSION['user_privs']['galaxydata_search'] == 0 && $_SESSION['user_privs']['galaxydata_analytics'] == 0 && $_SESSION['user_privs']['admin'] == 0) {
		header("Location: index.php");
	}
	
	$db = new database;
	$db->connect();
	
	if(($_POST || $_GET) && !isset($_POST['search'])){
		if(isset($_POST["Delete"]) && ($_SESSION['user_privs']['galaxydata_delete'] > 0 || $_SESSION['user_privs']['admin'] > 0)){
			$count_del = 0;
			foreach($_POST["entries"] as $entry){
				mysqli_query($db->connection,"DELETE FROM data_galaxydata WHERE id = '$entry'");
				$count_del++;
			}
			mysqli_query($db->connection, "INSERT INTO logs_activities (user_id, log_type, details, timestamp) VALUES ('".$_SESSION['user_id']."', '2', 'DELETE: ".$count_del." records were deleted', '".swc_time(time(),TRUE)["timestamp"]."')");
			$alert = "<div class=\"alert alert-warning\" style=\"font-size:14px;\">".$count_del." records have been deleted.</div>";
		}
		
		if(isset($_POST["type"])){$sessionType = $_POST["type"];}elseif(isset($_GET["type"])){$sessionType = $_GET["type"];}
		
		if($sessionType == "search"){
			if($_POST["type"] == "search"){
				$_GET = "";
				$searchType = mysqli_real_escape_string($db->connection,$_POST["inputSearchType"]);
				$sector = mysqli_real_escape_string($db->connection,$_POST["inputSector"]);
				$system = mysqli_real_escape_string($db->connection,$_POST["inputSystem"]);
				$planet = mysqli_real_escape_string($db->connection,$_POST["inputPlanet"]);
				$type = mysqli_real_escape_string($db->connection,$_POST["inputType"]);
				$size = mysqli_real_escape_string($db->connection,$_POST["inputSize"]);
				$terrain = mysqli_real_escape_string($db->connection,$_POST["inputTerrain"]);
				$controlled_by = mysqli_real_escape_string($db->connection,$_POST["inputControlledBy"]);
				$governor = mysqli_real_escape_string($db->connection,$_POST["inputGovernor"]);
				$magistrate = mysqli_real_escape_string($db->connection,$_POST["inputMagistrate"]);
				$population = mysqli_real_escape_string($db->connection,$_POST["inputPopulation"]);
				$populationOperator = mysqli_real_escape_string($db->connection,$_POST["inputPopulationOperator"]);
			}elseif($_GET["type"] == "search"){
				$searchType = mysqli_real_escape_string($db->connection,$_GET["inputSearchType"]);
				$sector = mysqli_real_escape_string($db->connection,$_GET["inputSector"]);
				$system = mysqli_real_escape_string($db->connection,$_GET["inputSystem"]);
				$planet = mysqli_real_escape_string($db->connection,$_GET["inputPlanet"]);
				$type = mysqli_real_escape_string($db->connection,$_GET["inputType"]);
				$size = mysqli_real_escape_string($db->connection,$_GET["inputSize"]);
				$terrain = mysqli_real_escape_string($db->connection,$_GET["inputTerrain"]);
				$controlled_by = mysqli_real_escape_string($db->connection,$_GET["inputControlledBy"]);
				$governor = mysqli_real_escape_string($db->connection,$_GET["inputGovernor"]);
				$magistrate = mysqli_real_escape_string($db->connection,$_GET["inputMagistrate"]);
				$population = mysqli_real_escape_string($db->connection,$_GET["inputPopulation"]);
				$populationOperator = mysqli_real_escape_string($db->connection,$_GET["inputPopulationOperator"]);
			}
			
			$search_array = array();
			if($searchType == "planets"){
				if($sector != ""){array_push($search_array,"galaxy_planets.sector='".$sector."'");}
				if($system != ""){array_push($search_array,"galaxy_planets.system='".$system."'");}
				if($planet != ""){array_push($search_array,"galaxy_planets.uid='".$planet."'");}
				if($type != ""){array_push($search_array,"galaxy_planets.type='".$type."'");}
				if($size != ""){array_push($search_array,"galaxy_planets.size='".$size."'");}
				if($terrain != ""){array_push($search_array,"galaxy_planets.terrain LIKE '%".$terrain."%'");}
				if($controlled_by != ""){array_push($search_array,"galaxy_planets.controlled_by LIKE '%".$controlled_by."%'");}
				if($governor != ""){array_push($search_array,"galaxy_planets.governor LIKE '%".$governor."%'");}
				if($magistrate != ""){array_push($search_array,"galaxy_planets.magistrate LIKE '%".$magistrate."%'");}
				if($population != ""){array_push($search_array,"galaxy_planets.population ".$populationOperator." '".$population."'");}
			}elseif($searchType == "deposits"){
				if($sector != ""){array_push($search_array,"galaxy_planets.sector='".$sector."'");}
				if($system != ""){array_push($search_array,"galaxy_planets.system='".$system."'");}
				if($planet != ""){array_push($search_array,"galaxy_planets.uid='".$planet."'");}
				if($type != ""){array_push($search_array,"galaxy_planets.type='".$type."'");}
				if($size != ""){array_push($search_array,"galaxy_planets.size='".$size."'");}
				if($terrain != ""){array_push($search_array,"galaxy_planets.terrain LIKE '%".$terrain."%'");}
				if($controlled_by != ""){array_push($search_array,"galaxy_planets.controlled_by LIKE '%".$controlled_by."%'");}
				if($governor != ""){array_push($search_array,"galaxy_planets.governor LIKE '%".$governor."%'");}
				if($magistrate != ""){array_push($search_array,"galaxy_planets.magistrate LIKE '%".$magistrate."%'");}
				if($population != ""){array_push($search_array,"galaxy_planets.population ".$populationOperator." '".$population."'");}
			}
			$search_string = implode(" AND ", $search_array);
			$search_parameters = $search_string;
			
			if($searchType == "planets"){
				$results_count = mysqli_num_rows(mysqli_query($db->connection,"SELECT galaxy_planets.uid FROM galaxy_planets LEFT JOIN galaxy_systems ON galaxy_planets.system = galaxy_systems.uid LEFT JOIN galaxy_sectors ON galaxy_planets.sector = galaxy_sectors.uid WHERE ".$search_string.""));
				$search_string = "SELECT galaxy_planets.*, galaxy_systems.name AS systemname, galaxy_sectors.name AS sectorname FROM galaxy_planets LEFT JOIN galaxy_systems ON galaxy_planets.system = galaxy_systems.uid LEFT JOIN galaxy_sectors ON galaxy_planets.sector = galaxy_sectors.uid WHERE ".$search_string." ORDER BY galaxy_planets.name ASC";
			}elseif($searchType == "deposits"){
				$where = $search_string;
				$search_string = "SELECT data_galaxydata_deposits.type, SUM(data_galaxydata_deposits.size) as size FROM data_galaxydata_deposits LEFT JOIN galaxy_planets ON data_galaxydata_deposits.planet_uid = galaxy_planets.uid WHERE ".$where." GROUP BY type ORDER BY type";
				$source_planets = "SELECT DISTINCT galaxy_planets.name, galaxy_planets.uid FROM data_galaxydata_deposits LEFT JOIN galaxy_planets ON galaxy_planets.uid = data_galaxydata_deposits.planet_uid WHERE ".$where." AND (data_galaxydata_deposits.planet_uid IS NOT NULL OR data_galaxydata_deposits.planet_uid = '') ORDER BY galaxy_planets.name ASC";
			}
			$log_string = mysqli_real_escape_string($db->connection,str_replace("galaxy_planets.", "", $search_parameters));
			mysqli_query($db->connection, "INSERT INTO logs_activities (user_id, log_type, details, timestamp) VALUES ('".$_SESSION['user_id']."', '2', '".strtoupper($searchType)." SEARCH: ".$log_string."', '".swc_time(time(),TRUE)["timestamp"]."')");
			
		}elseif($sessionType == "dataFactions"){
			//$order = mysqli_real_escape_string($db->connection,$_POST["orderBy"]);
			if($order == ""){$order = "controlled_by ASC";}
			$search_string = "SELECT SUM(population) AS pop, COUNT(galaxy_planets.uid) AS planets, SUM(cities) AS cities, AVG(civilization) AS civ, AVG(tax) AS taxes, controlled_by FROM galaxy_planets LEFT JOIN factions ON galaxy_planets.controlled_by = factions.name WHERE galaxy_planets.type <> 'sun' GROUP BY controlled_by ORDER BY ".$order."";
			mysqli_query($db->connection, "INSERT INTO logs_activities (user_id, log_type, details, timestamp) VALUES ('".$_SESSION['user_id']."', '2', 'ANALYTICS: Data by all Factions', '".swc_time(time(),TRUE)["timestamp"]."')");
			if(isset($_POST['action'])){
				$alert = "<div class=\"alert alert-success\" style=\"font-size:14px;\">Faction galaxy data has been captured.</div>";
			}
		}elseif($sessionType == "historyFactions"){
			$faction = mysqli_real_escape_string($db->connection,$_GET["faction"]);
			$faction_uid = mysqli_fetch_assoc(mysqli_query($db->connection,"SELECT uid FROM factions WHERE name = '".$faction."'"))['uid'];
			if($faction_uid != ""){
				$search_string_current = "SELECT SUM(population) AS pop, COUNT(galaxy_planets.uid) AS planets, SUM(cities) AS cities, AVG(civilization) AS civ, AVG(tax) AS taxes, controlled_by FROM galaxy_planets LEFT JOIN factions ON galaxy_planets.controlled_by = factions.name WHERE controlled_by = '".$faction."' GROUP BY controlled_by";
				$search_string_history = "SELECT data_galaxydata.*, factions.name AS factionName FROM data_galaxydata LEFT JOIN factions ON data_galaxydata.faction = factions.uid WHERE faction = '".$faction_uid."' ORDER BY timestamp DESC";
				mysqli_query($db->connection, "INSERT INTO logs_activities (user_id, log_type, details, timestamp) VALUES ('".$_SESSION['user_id']."', '2', 'ANALYTICS: Data for ".$faction."', '".swc_time(time(),TRUE)["timestamp"]."')");
			}else{
				$_SESSION["alert"] = "<div class=\"alert alert-danger\" style=\"font-size:14px;\">No faction exists by that name.</div>";
				header("Location: galaxydata.php");
			}
		}elseif($sessionType == "upload"){
			$len = count($_FILES['file']['tmp_name']);

			for($i = 0; $i < $len; $i++) {
				$file = $_FILES['file']['tmp_name'][$i];

				if ($_FILES["file"]["error"][$i] > 0){
					$notice = "<div class='error'>Error: ".$_FILES["file"]["error"][$i]."</div>";
				}else{
					if(($_FILES['file']['type'][$i] == "text/xml") || ($_FILES['file']['type'][$i] == "application/xml")){
						$xml_file = simplexml_load_string(file_get_contents($file));
						
						$planet_name = mysqli_real_escape_string($db->connection,$xml_file{'planet_name'});
						$planet_uid = mysqli_real_escape_string($db->connection,$xml_file{'planet_uid'});
						$timestamp = mysqli_real_escape_string($db->connection,$xml_file{'timestamp-swc'});
						
						foreach($xml_file->deposit as $deposit){
							$x = mysqli_real_escape_string($db->connection,$deposit{'x'});
							$y = mysqli_real_escape_string($db->connection,$deposit{'y'});
							$deposit_uid = mysqli_real_escape_string($db->connection,$deposit{'deposit_uid'});
							$type = mysqli_real_escape_string($db->connection,$deposit->type);
							$size = mysqli_real_escape_string($db->connection,$deposit->size);
							mysqli_query($db->connection, "DELETE FROM data_galaxydata_deposits WHERE planet_uid = '$planet_uid'");
							
							$exists = mysqli_fetch_assoc(mysqli_query($db->connection,"SELECT COUNT(deposit_uid) as count FROM data_galaxydata_deposits WHERE deposit_uid = '$deposit_uid'"))["count"];
							if($exists == 1){
								mysqli_query($db->connection,"UPDATE data_galaxydata_deposits SET type='$type', size='$size', timestamp='$timestamp' WHERE deposit_uid = '$deposit_uid'");
							}else{
								mysqli_query($db->connection,"INSERT INTO data_galaxydata_deposits (deposit_uid, planet_uid, x, y, type, size, timestamp) VALUES ('$deposit_uid', '$planet_uid', '$x', '$y', '$type', '$size', '$timestamp')");
							}
						}
						
						$alert = $alert."<div class=\"alert alert-success\" style=\"font-size:14px;\">Material deposit data uploaded for the planet ".$planet_name.".</div>";
					}else{
						$alert = $alert."<div class=\"alert alert-danger\" style=\"font-size:14px;\">".$file = $_FILES['file']['name'][$i]." - Incorrect filetype: Only xml files are allowed.</div>";
					}
				}
			}
		}elseif($sessionType == "planetDevelopment2"){
			if($_GET['inputFrom'] >= $_GET['inputTo']){
				$_SESSION['alert'] = "<div class=\"alert alert-danger\" style=\"font-size:14px;\">Incorrect date range. Make sure 'FROM' date is older than the 'TO' date.</div>";
				header("Location: galaxydata.php");
			}
		}
	}elseif(!empty($_POST['search'])){
		$searchType = "planets";
		$search = mysqli_real_escape_string($db->connection, $_POST['search']);
		$results_count = mysqli_num_rows(mysqli_query($db->connection,"SELECT galaxy_planets.uid FROM galaxy_planets LEFT JOIN galaxy_systems ON galaxy_planets.system = galaxy_systems.uid LEFT JOIN galaxy_sectors ON galaxy_planets.sector = galaxy_sectors.uid WHERE galaxy_planets.name LIKE '%$search%' OR galaxy_systems.name LIKE '%$search%' OR galaxy_sectors.name LIKE '%$search%' OR galaxy_planets.controlled_by LIKE '%$search%' OR galaxy_planets.governor LIKE '%$search%' OR galaxy_planets.magistrate LIKE '%$search%'"));
		$search_string = "SELECT galaxy_planets.*, galaxy_systems.name AS systemname, galaxy_sectors.name AS sectorname FROM galaxy_planets LEFT JOIN galaxy_systems ON galaxy_planets.system = galaxy_systems.uid LEFT JOIN galaxy_sectors ON galaxy_planets.sector = galaxy_sectors.uid WHERE galaxy_planets.name LIKE '%$search%' OR galaxy_systems.name LIKE '%$search%' OR galaxy_sectors.name LIKE '%$search%' OR galaxy_planets.controlled_by LIKE '%$search%' OR galaxy_planets.governor LIKE '%$search%' OR galaxy_planets.magistrate LIKE '%$search%' ORDER BY galaxy_planets.name ASC";
		$log_string = mysqli_real_escape_string($db->connection,"Broad search on \"$search\"");
		mysqli_query($db->connection, "INSERT INTO logs_activities (user_id, log_type, details, timestamp) VALUES ('".$_SESSION['user_id']."', '2', '".strtoupper($searchType)." SEARCH: ".$log_string."', '".swc_time(time(),TRUE)["timestamp"]."')");
	}elseif(!empty($_GET['search'])){
		$searchType = "planets";
		$search = mysqli_real_escape_string($db->connection, $_GET['search']);
		$results_count = mysqli_num_rows(mysqli_query($db->connection,"SELECT galaxy_planets.uid FROM galaxy_planets LEFT JOIN galaxy_systems ON galaxy_planets.system = galaxy_systems.uid LEFT JOIN galaxy_sectors ON galaxy_planets.sector = galaxy_sectors.uid WHERE galaxy_planets.name LIKE '%$search%' OR galaxy_systems.name LIKE '%$search%' OR galaxy_sectors.name LIKE '%$search%' OR galaxy_planets.controlled_by LIKE '%$search%' OR galaxy_planets.governor LIKE '%$search%' OR galaxy_planets.magistrate LIKE '%$search%'"));
		$search_string = "SELECT galaxy_planets.*, galaxy_systems.name AS systemname, galaxy_sectors.name AS sectorname FROM galaxy_planets LEFT JOIN galaxy_systems ON galaxy_planets.system = galaxy_systems.uid LEFT JOIN galaxy_sectors ON galaxy_planets.sector = galaxy_sectors.uid WHERE galaxy_planets.name LIKE '%$search%' OR galaxy_systems.name LIKE '%$search%' OR galaxy_sectors.name LIKE '%$search%' OR galaxy_planets.controlled_by LIKE '%$search%' OR galaxy_planets.governor LIKE '%$search%' OR galaxy_planets.magistrate LIKE '%$search%' ORDER BY galaxy_planets.name ASC";
		$log_string = mysqli_real_escape_string($db->connection,"Broad search on \"$search\"");
		mysqli_query($db->connection, "INSERT INTO logs_activities (user_id, log_type, details, timestamp) VALUES ('".$_SESSION['user_id']."', '2', '".strtoupper($searchType)." SEARCH: ".$log_string."', '".swc_time(time(),TRUE)["timestamp"]."')");
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
                                        <h1>Galaxy Data</h1>
                                    </div>
                                </div>
                                <div class="col-sm-6 hidden-xs">
                                    <div class="header-section">
                                        <ul class="breadcrumb breadcrumb-top">
                                            <li>Databases</li>
                                            <li><a href="galaxydata.php">Galaxy Data</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
						<?php if(isset($alert)){echo $alert;} ?>
						
                        <!-- Example Block -->
                        <div class="block" style="min-height:700px;">
                            <!-- Example Title -->
                            <div class="block-title hidden-print">
								<h2>Data</h2>
								<?php 
									if($_SESSION['user_privs']['galaxydata_search'] > 0 || $_SESSION['user_privs']['admin'] > 0){
										echo "
											<button title=\"Search\" class=\"btn btn-primary\" data-target=\"#modalSearch\" data-toggle=\"modal\">
												<span class=\"fas fa-search\"></span> Search
											</button>
										";
									}
									if ($_SESSION['user_privs']['galaxydata_analytics'] > 0 || $_SESSION['user_privs']['admin'] > 0) {
										echo "<form style=\"margin:0px;display:inline;\" method=\"post\" action=\"\"><input id=\"type\" type=\"hidden\" name=\"type\" value=\"dataFactions\"><button type=\"submit\" title=\"Overview\" class=\"btn btn-primary\" style=\"margin-right:3px;\"><span class=\"fas fa-info-circle\"></span> Overview</button></form>";
										echo "<button title=\"Analytics\" class=\"btn btn-primary\" data-target=\"#modalAnalytics\" data-toggle=\"modal\"><span class=\"fas fa-chart-bar\"></span> Analytics</button>";
										echo "<button style=\"margin-left:3px;\" title=\"Upload Deposits\" class=\"btn btn-info\" data-target=\"#modalUpload\" data-toggle=\"modal\"><span class=\"fas fa-upload\"></span> Upload Deposits</button>";
									}	
								?>
								<h2 class="pull-right">
									<?php 
										if(isset($results_count)){
											echo "Results: ".$results_count."";
										}
									?>
								</h2>
							</div>
							<div class="block-content col-sm-12">
							<?php 
								if((isset($search_string) && (isset($sessionType))) || isset($_POST['search']) || isset($_GET['search'])){
									if(($sessionType == "search" && $searchType == "planets") || isset($_POST['search'])){
										include("assets/php/galaxydata/search.php");
									}elseif($sessionType == "search" && $searchType == "deposits"){
										include("assets/php/galaxydata/searchDeposits.php");
									}elseif($sessionType == "dataFactions" && ($_SESSION['user_privs']['galaxydata_analytics'] > 0 || $_SESSION['user_privs']['admin'] != 0)){
										include("assets/php/galaxydata/dataFactions.php");
									}
								}elseif($sessionType == "deposits"){
									include("assets/php/galaxydata/deposits.php");
								}elseif($sessionType == "historyFactions" && ($_SESSION['user_privs']['galaxydata_analytics'] > 0 || $_SESSION['user_privs']['admin'] != 0)){
									include("assets/php/galaxydata/historyFactions.php");
								}elseif($sessionType == "planetDevelopment2"){
									include("assets/php/galaxydata/planetDevelopment2.php");
									mysqli_query($db->connection, "INSERT INTO logs_activities (user_id, log_type, details, timestamp) VALUES ('".$_SESSION['user_id']."', '2', 'ANALYTICS: Planetary Development', '".swc_time(time(),TRUE)["timestamp"]."')");
								}elseif($sessionType == "listGovernors"){
									include("assets/php/galaxydata/listGovernors.php");
									mysqli_query($db->connection, "INSERT INTO logs_activities (user_id, log_type, details, timestamp) VALUES ('".$_SESSION['user_id']."', '2', 'ANALYTICS: Governor List', '".swc_time(time(),TRUE)["timestamp"]."')");
								}elseif($sessionType == "listMagistrates"){
									include("assets/php/galaxydata/listMagistrates.php");
									mysqli_query($db->connection, "INSERT INTO logs_activities (user_id, log_type, details, timestamp) VALUES ('".$_SESSION['user_id']."', '2', 'ANALYTICS: Magistrate List', '".swc_time(time(),TRUE)["timestamp"]."')");
								}else{
									$db = new database;
									$db->connect();
									
									$sectors = mysqli_fetch_assoc(mysqli_query($db->connection, "SELECT COUNT(uid) as count FROM galaxy_sectors"))["count"];
									$systems = mysqli_fetch_assoc(mysqli_query($db->connection, "SELECT COUNT(uid) as count FROM galaxy_systems"))["count"];
									$planets = mysqli_fetch_assoc(mysqli_query($db->connection, "SELECT COUNT(uid) as count FROM galaxy_planets"))["count"];
									$owners = mysqli_num_rows(mysqli_query($db->connection, "SELECT DISTINCT controlled_by FROM galaxy_planets WHERE controlled_by != '' AND controlled_by IS NOT NULL"));
									$governors = mysqli_num_rows(mysqli_query($db->connection, "SELECT DISTINCT governor FROM galaxy_planets WHERE governor != '' AND governor IS NOT NULL"));
									$magistrates = mysqli_num_rows(mysqli_query($db->connection, "SELECT DISTINCT magistrate FROM galaxy_planets WHERE magistrate != '' AND magistrate IS NOT NULL"));
									$deposits = mysqli_fetch_assoc(mysqli_query($db->connection, "SELECT COUNT(deposit_uid) as count FROM data_galaxydata_deposits"))["count"];
									
									echo "
										<div class=\"row\">
											<div class=\"col-sm-12\"><center><h1><b>Statistics</b></h1></center></div>
										</div>
										<div class=\"row\">
											<div class=\"col-sm-12\" style=\"margin: auto;\">
												<center>
												<table class=\"table table-bordered table-striped table-hover\">
													<tr><td>Sectors:</td><td>".number_format($sectors)."</td></tr>
													<tr><td>Systems:</td><td>".number_format($systems)."</td></tr>
													<tr><td>Planets:</td><td>".number_format($planets)."</td></tr>
													<tr><td>Owners (Unique):</td><td>".number_format($owners)."</td></tr>
													<tr><td>Governors (Unique):</td><td>".number_format($governors)."</td></tr>
													<tr><td>Magistrates (Unique):</td><td>".number_format($magistrates)."</td></tr>
													<tr><td>Material Deposits:</td><td>".number_format($deposits)."</td></tr>
												</table>
												</center>
											</div>
										</div>
									";
									
									echo "
										<div class=\"row\">
											<div class=\"col-sm-12\" style=\"position: relative;text-align:center;float:none;margin: auto;\">
												<center><h1><b>Galaxy Map</b></h1></center>
											</div>
										</div>
										<div class=\"row\">
											<div class=\"col-sm-12\" style=\"position: relative;height:1000px;width:1000px;float:none;margin:0 auto;\">
												<img style=\"height:1000px; width:1000px; position: absolute;top:0px;left:0px;z-index:1;\" src=\"assets/img/graphics/galaxyReference.jpg\">
											</div>
										</div>
									";
									
									$db->disconnect();
									unset($db);
								}
							?>
							</div>
                            
							<div class="modal fade" id="modalSearch">
								<div class="modal-dialog">
									<div class="modal-content">
										<div class="modal-header">
											<button class="close" aria-hidden="true" type="button" data-dismiss="modal">×</button>
											<h4 class="modal-title">Search Query</h4>
										</div>
										<form class="form-horizontal" role="form" method="post" action="galaxydata.php">
											<div class="modal-body">
												<div class="form-group">
													<label class="col-sm-3" for="inputSearchType">Search Type</label>
													<div class="col-sm-9">
														<select class="form-control" id="inputSearchType" name="inputSearchType">
															<option value="planets">Planets</option>
															<option value="deposits">Material Deposits</option>
														</select>
													</div>
												</div>
												<div class="form-group">
													<label class="col-sm-3" for="inputSector">Sector</label>
													<div class="col-sm-9">
														<select class="form-control" id="inputSector" name="inputSector">
															<option value="">-- All --</option>
															<?php
																$db = new database;
																$db->connect();
																$query = mysqli_query($db->connection,"SELECT uid, name FROM galaxy_sectors ORDER BY name ASC");
																while($data = mysqli_fetch_assoc($query)){
																	echo "<option value=\"".$data['uid']."\">".$data['name']."</option>";
																}
																$db->disconnect();
																unset($db);
															?>
														</select>
													</div>
												</div>
												<div class="form-group">
													<label class="col-sm-3" for="inputSystem">System</label>
													<div class="col-sm-9">
														<select class="form-control" id="inputSystem" name="inputSystem">
															<option value="">-- All --</option>
															<?php
																$db = new database;
																$db->connect();
																$query = mysqli_query($db->connection,"SELECT uid, name FROM galaxy_systems ORDER BY name ASC");
																while($data = mysqli_fetch_assoc($query)){
																	echo "<option value=\"".$data['uid']."\">".$data['name']."</option>";
																}
																$db->disconnect();
																unset($db);
															?>
														</select>
													</div>
												</div>
												<div class="form-group">
													<label class="col-sm-3" for="inputPlanet">Planet</label>
													<div class="col-sm-9">
														<select class="form-control" id="inputPlanet" name="inputPlanet">
															<option value="">-- All --</option>
															<?php
																$db = new database;
																$db->connect();
																$query = mysqli_query($db->connection,"SELECT uid, name FROM galaxy_planets ORDER BY name ASC");
																while($data = mysqli_fetch_assoc($query)){
																	echo "<option value=\"".$data['uid']."\">".$data['name']."</option>";
																}
																$db->disconnect();
																unset($db);
															?>
														</select>
													</div>
												</div>
												<div class="form-group">
													<label class="col-sm-3" for="inputSize">Size</label>
													<div class="col-sm-9">
														<select class="form-control" id="inputSize" name="inputSize">
															<option value="">-- Any --</option>
															<option value="1">1</option>
															<option value="2">2</option>
															<option value="3">3</option>
															<option value="4">4</option>
															<option value="5">5</option>
															<option value="6">6</option>
															<option value="7">7</option>
															<option value="8">8</option>
															<option value="9">9</option>
															<option value="10">10</option>
															<option value="11">11</option>
															<option value="12">12</option>
															<option value="13">13</option>
															<option value="14">14</option>
															<option value="15">15</option>
															<option value="16">16</option>
															<option value="17">17</option>
															<option value="18">18</option>
															<option value="19">19</option>
															<option value="20">20</option>
														</select>
													</div>
												</div>
												<div class="form-group">
													<label class="col-sm-3" for="inputType">Type</label>
													<div class="col-sm-9">
														<select class="form-control" id="inputType" name="inputType">
															<option value="">-- All --</option>
															<option value="asteroid field">Asteroid Field</option>
															<option value="cold/breathable">Cold/breathable</option>
															<option value="cold/no atmosphere">Cold/no atmosphere</option>
															<option value="cold/toxic atmosphere">Cold/toxic atmosphere</option>
															<option value="comet">Comet</option>
															<option value="gas giant">Gas Giant</option>
															<option value="hot/breathable">Hot/breathable</option>
															<option value="hot/no atmosphere">Hot/no atmosphere</option>
															<option value="hot/toxic atmosphere">Hot/toxic atmosphere</option>
															<option value="moon">Moon</option>
															<option value="temperate/breathable">Temperate/breathable</option>
														</select>
													</div>
												</div>
												<div class="form-group">
													<label class="col-sm-3" for="inputTerrain">Terrain</label>
													<div class="col-sm-9">
														<select class="form-control" id="inputTerrain" name="inputTerrain">
															<option value="">-- Any --</option>
															<option value="n">Cave</option>
															<option value="i">Crater</option>
															<option value="b">Desert</option>
															<option value="c">Forest</option>
															<option value="o">Gas Giant</option>
															<option value="k">Glacier</option>
															<option value="f">Grassland</option>
															<option value="d">Jungle</option>
															<option value="l">Mountain</option>
															<option value="g">Ocean</option>
															<option value="h">River</option>
															<option value="j">Rock</option>
															<option value="e">Swamp</option>
															<option value="m">Volcanic</option>
														</select>
													</div>
												</div>
												<div class="form-group">
													<label class="col-sm-3" for="inputControlledBy">Controlled By</label>
													<div class="col-sm-9"><input class="form-control" id="inputControlledBy" name="inputControlledBy" type="text" placeholder="{Name of controlling faction}"></div>
												</div>
												<div class="form-group">
													<label class="col-sm-3" for="inputGovernor">Governor</label>
													<div class="col-sm-9"><input class="form-control" id="inputGovernor" name="inputGovernor" type="text" placeholder="{Name of governor}"></div>
												</div>
												<div class="form-group">
													<label class="col-sm-3" for="inputMagistrate">Magistrate</label>
													<div class="col-sm-9"><input class="form-control" id="inputMagistrate" name="inputMagistrate" type="text" placeholder="{Name of magistrate}"></div>
												</div>
												<div class="form-group">
													<label class="col-sm-3" for="inputPopulation">Population</label>
													<div class="col-sm-2">
														<select class="form-control" id="inputPopulationOperator" name="inputPopulationOperator">
															<option value="<="><=</option>
															<option value="=">=</option>
															<option value=">=">>=</option>
														</select>
													</div>
													<div class="col-sm-7"><input class="form-control" id="inputPopulation" name="inputPopulation" type="number" placeholder="{Amount of population}"></div>
												</div>
											</div>
											<div class="modal-footer">
												<button class="btn btn-default pull-left" type="button" data-dismiss="modal">Cancel</button>
												<input id="submit" class="btn btn-primary " type="submit" value="Submit">
												<input id="type" type="hidden" name="type" value="search">
											</div>
										</form>
									</div>
								</div>
							</div>
							
							<div class="modal fade" id="modalAnalytics">
								<div class="modal-dialog">
									<div class="modal-content" style="overflow:auto;">
										<div class="modal-header">
											<button class="close" aria-hidden="true" type="button" data-dismiss="modal">×</button>
											<h4 class="modal-title">Analytics</h4>
										</div>
										<div class="modal-body" style="overflow:auto;">
											<div class="form-group" style="overflow:auto;">
												<label class="col-sm-5" for="dataFactions">Faction Historical Analysis</label>
												<div class="col-sm-5">
													<form class="form-horizontal" role="form" method="get" action="galaxydata.php">
													<input type="text" name="faction" style="width:100%;" placeholder="{Faction name}" required>
												</div>
												<div class="col-sm-2">
														<input id="historyFactions" class="btn btn-primary " type="submit" value="Run">
														<input id="type" type="hidden" name="type" value="historyFactions">
													</form>
												</div>
											</div><hr/>
											<div class="form-group" style="overflow:auto;">
												<label class="col-sm-5" for="listGovernors">Governor List</label>
												<div class="col-sm-5">
													<form class="form-horizontal" role="form" method="get" action="galaxydata.php">
												</div>
												<div class="col-sm-2">
														<input id="listGovernors" class="btn btn-primary " type="submit" value="Run">
														<input id="type" type="hidden" name="type" value="listGovernors">
													</form>
												</div>
											</div><hr/>
											<div class="form-group" style="overflow:auto;">
												<label class="col-sm-5" for="listMagistrates">Magistrate List</label>
												<div class="col-sm-5">
													<form class="form-horizontal" role="form" method="get" action="galaxydata.php">
												</div>
												<div class="col-sm-2">
														<input id="listMagistrates" class="btn btn-primary " type="submit" value="Run">
														<input id="type" type="hidden" name="type" value="listMagistrates">
													</form>
												</div>
											</div><hr/>
											<div class="form-group" style="overflow:auto;">
												<label class="col-sm-5" for="planetDevelopment2">Planet Development</label>
												<div class="col-sm-5">
													<form class="form-horizontal" role="form" method="get" action="galaxydata.php">
														From: 
														<select class="form-control" id="inputFrom" name="inputFrom">
															<?php
																$db = new database;
																$db->connect();
																$query = mysqli_query($db->connection,"SELECT DISTINCT timestamp FROM data_galaxydata_planets ORDER BY timestamp DESC");
																$count = 1;
																while($data = mysqli_fetch_assoc($query)){
																	if($count == 2){
																		echo "<option value=\"".$data['timestamp']."\" selected>Year ".(swc_time($data['timestamp'], TRUE)['year'])." Day ".(swc_time($data['timestamp'], TRUE)['day'])."</option>";
																	}else{
																		echo "<option value=\"".$data['timestamp']."\">Year ".(swc_time($data['timestamp'], TRUE)['year'])." Day ".(swc_time($data['timestamp'], TRUE)['day'])."</option>";
																	}
																	$count++;
																}
																$db->disconnect();
																unset($db);
															?>
														</select>
														To: 
														<select class="form-control" id="inputTo" name="inputTo">
															<?php
																$db = new database;
																$db->connect();
																$query = mysqli_query($db->connection,"SELECT DISTINCT timestamp FROM data_galaxydata_planets ORDER BY timestamp DESC");
																while($data = mysqli_fetch_assoc($query)){
																	echo "<option value=\"".$data['timestamp']."\">Year ".(swc_time($data['timestamp'], TRUE)['year'])." Day ".(swc_time($data['timestamp'], TRUE)['day'])."</option>";
																}
																$db->disconnect();
																unset($db);
															?>
														</select>
														Controlled By:
														<input type="text" name="inputControlled" style="width:100%;" placeholder="{optional}">
												</div>
												<div class="col-sm-2">
														<input id="planetDevelopment2" class="btn btn-primary " type="submit" value="Run">
														<input id="type" type="hidden" name="type" value="planetDevelopment2">
													</form>
												</div>
											</div>
										</div>
										<div class="modal-footer">
											<button class="btn btn-default pull-left" type="button" data-dismiss="modal">Cancel</button>
										</div>
									</div>
								</div>
							</div>
							
							<div class="modal fade" id="modalUpload">
								<div class="modal-dialog">
									<div class="modal-content">
										<div class="modal-header">
											<button class="close" aria-hidden="true" type="button" data-dismiss="modal">×</button>
											<h4 class="modal-title">Upload Deposits XML file</h4>
										</div>
										<div class="modal-body">
											<div class="form-group">
												<div class="col-sm-12">
													<form class="form-horizontal" role="form" method="post" enctype="multipart/form-data" action="galaxydata.php" id="uploadForm">
														<input type="file" name="file[]" multiple>
														<input type="hidden" name="type" value="upload">
												</div>
											</div>
										</div>
										<div class="modal-footer">
											<button class="btn btn-default pull-left" type="button" data-dismiss="modal">Cancel</button>
											<input id="submit" class="btn btn-primary " type="submit" value="Submit">
											</form>
										</div>
									</div>
								</div>
							</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
		<?php include("assets/php/_end.php"); ?>
		<script>
		$("#checkAll").click(function(){
			$("input:checkbox").prop('checked', true);
		});
		
		$("#uncheckAll").click(function(){
			$("input:checkbox").prop('checked', false);
		});
		
		$("#checkAll2").click(function(){
			$("input:checkbox").prop('checked', true);
		});
		
		$("#uncheckAll2").click(function(){
			$("input:checkbox").prop('checked', false);
		});
		</script>
	</body>
</html>