<?php 
    include("autoload.php");
    global $db;
    global $site;
    global $session;
    global $account;
    $session->check_login();
    error_reporting(0);
	/*
		PRIVS
		Search 1 allows sanitized queries, Search 2 allows view of manager/operator/income
		Delete allows record deletion
		Upload 1 allows upload into data buffer for approval, upload 2 allows upload directly into live table
		Export allows export of data from database in xml format
		Analytics allows advanced analysis output of collection data
		Admin of 1 or greater overrides and allows all
	*/
	
	if ($_SESSION['user_privs']['signalsanalysis_search'] == 0 && $_SESSION['user_privs']['signalsanalysis_upload'] == 0 && $_SESSION['user_privs']['signalsanalysis_analytics'] == 0 && $_SESSION['user_privs']['admin'] == 0) {
		header("Location: index.php");
	}
	
	$filters = checkFilters($_SESSION['user_id']);
	$filter = compileFilters($filters);

	if(($_POST || $_GET) && !isset($_POST['search'])){

		if(isset($_POST["type"])){
			$sessionType = $_POST["type"];
		}elseif(isset($_GET["type"])){
			$sessionType = $_GET["type"];
		}elseif(isset($_GET["activity"])){
			$sessionType = "activity";
		}
		
		if(isset($_POST["Delete"]) && ($_SESSION['user_privs']['signalsanalysis_delete'] > 0 || $_SESSION['user_privs']['admin'] > 0)){
			$count_del = 0;
			foreach($_POST["entities"] as $entity){
				$del_uid = explode(";",$entity)[0];
				$del_timestamp = explode(";",$entity)[1];
				mysqli_query($db->connection,"DELETE FROM data_signalsanalysis WHERE uid = '$del_uid' AND timestamp = '$del_timestamp'");
				mysqli_query($db->connection,"DELETE FROM data_signalsanalysis_archive WHERE uid = '$del_uid' AND timestamp = '$del_timestamp'");
				$count_del++;
			}
			mysqli_query($db->connection, "INSERT INTO logs_activities (user_id, log_type, details, timestamp) VALUES ('".$_SESSION['user_id']."', '1', 'DELETE: ".$count_del." records were deleted', '".swc_time(time(),TRUE)["timestamp"]."')");
			$session->alert = "<div class=\"alert alert-warning\" style=\"font-size:14px;\">".$count_del." records have been deleted.</div>";
		}
		
		if($sessionType == "Search Details" || $sessionType == "Search Summary"){
			if(isset($_POST["type"])){
				$_GET = "";
				$queryType = mysqli_real_escape_string($db->connection,$_POST["inputQueryType"]);
				$activity_id = mysqli_real_escape_string($db->connection,$_POST["inputActivity"]);
				$sector = mysqli_real_escape_string($db->connection,$_POST["inputSector"]);
				$system = mysqli_real_escape_string($db->connection,$_POST["inputSystem"]);
				$galx = mysqli_real_escape_string($db->connection,$_POST["inputGalX"]);
				$galy = mysqli_real_escape_string($db->connection,$_POST["inputGalY"]);
				$planet = mysqli_real_escape_string($db->connection,$_POST["inputPlanet"]);
				$sysx = mysqli_real_escape_string($db->connection,$_POST["inputSysX"]);
				$sysy = mysqli_real_escape_string($db->connection,$_POST["inputSysY"]);
				$atmox = mysqli_real_escape_string($db->connection,$_POST["inputAtmoX"]);
				$atmoy = mysqli_real_escape_string($db->connection,$_POST["inputAtmoY"]);
				$surfx = mysqli_real_escape_string($db->connection,$_POST["inputSurfX"]);
				$surfy = mysqli_real_escape_string($db->connection,$_POST["inputSurfY"]);
				$owner = mysqli_real_escape_string($db->connection,$_POST["inputOwner"]);
				$manager = mysqli_real_escape_string($db->connection,$_POST["inputManager"]);
				$operator = mysqli_real_escape_string($db->connection,$_POST["inputOperator"]);
				$ent_id = mysqli_real_escape_string($db->connection,$_POST["inputID"]);
				$ent_name = mysqli_real_escape_string($db->connection,$_POST["inputName"]);
				$ent_cat = mysqli_real_escape_string($db->connection,$_POST["inputCategory"]);
				$ent_class = mysqli_real_escape_string($db->connection,$_POST["inputClass"]);
				$ent_type = mysqli_real_escape_string($db->connection,$_POST["inputType"]);
				$form_link = "signalsanalysis.php?page=1&ipp=200&inputQueryType=$queryType&activity=$activity_id&inputSector=$sector&inputSystem=$system&inputGalX=$galx&inputGalY=$galy&inputPlanet=$planet&inputSysX=$sysx&inputSysY=$sysy&inputAtmoX=$atmox&inputAtmoY=$atmoy&inputSurfX=$surfx&inputSurfY=$surfy&inputOwner=$owner&inputManager=$manager&inputOperator=$operator&inputID=$ent_id&inputName=$ent_name&inputCategory=$ent_cat&inputClass=$ent_class&inputType=$ent_type&type=".urlencode($sessionType)."";
			}elseif(isset($_GET["type"])){
				$queryType = mysqli_real_escape_string($db->connection,$_GET["inputQueryType"]);
				$activity_id = mysqli_real_escape_string($db->connection,$_GET["inputActivity"]);
				$sector = mysqli_real_escape_string($db->connection,$_GET["inputSector"]);
				$system = mysqli_real_escape_string($db->connection,$_GET["inputSystem"]);
				$galx = mysqli_real_escape_string($db->connection,$_GET["inputGalX"]);
				$galy = mysqli_real_escape_string($db->connection,$_GET["inputGalY"]);
				$planet = mysqli_real_escape_string($db->connection,$_GET["inputPlanet"]);
				$sysx = mysqli_real_escape_string($db->connection,$_GET["inputSysX"]);
				$sysy = mysqli_real_escape_string($db->connection,$_GET["inputSysY"]);
				$atmox = mysqli_real_escape_string($db->connection,$_GET["inputAtmoX"]);
				$atmoy = mysqli_real_escape_string($db->connection,$_GET["inputAtmoY"]);
				$surfx = mysqli_real_escape_string($db->connection,$_GET["inputSurfX"]);
				$surfy = mysqli_real_escape_string($db->connection,$_GET["inputSurfY"]);
				$owner = mysqli_real_escape_string($db->connection,$_GET["inputOwner"]);
				$manager = mysqli_real_escape_string($db->connection,$_GET["inputManager"]);
				$operator = mysqli_real_escape_string($db->connection,$_GET["inputOperator"]);
				$ent_id = mysqli_real_escape_string($db->connection,$_GET["inputID"]);
				$ent_name = mysqli_real_escape_string($db->connection,$_GET["inputName"]);
				$ent_cat = mysqli_real_escape_string($db->connection,$_GET["inputCategory"]);
				$ent_class = mysqli_real_escape_string($db->connection,$_GET["inputClass"]);
				$ent_type = mysqli_real_escape_string($db->connection,$_GET["inputType"]);
				$form_link = "";
			}
			
			$search_array = array();
			if($activity_id != ""){array_push($search_array,"data_signalsanalysis_activity.activity_id = '".$activity_id."'");}
			if($sector != ""){if($sector == "NULL"){array_push($search_array,"data_signalsanalysis.sector IS NULL");}else{array_push($search_array,"data_signalsanalysis.sector = '".$sector."'");}}
			if($system != ""){if($system == "NULL"){array_push($search_array,"data_signalsanalysis.system IS NULL");}else{array_push($search_array,"data_signalsanalysis.system = '".$system."'");}}
			if($planet != ""){array_push($search_array,"data_signalsanalysis.planet = '".$planet."'");}
			if($galx != ""){array_push($search_array,"data_signalsanalysis.galx = '".$galx."'");}
			if($galy != ""){array_push($search_array,"data_signalsanalysis.galy = '".$galy."'");}
			if($sysx != ""){array_push($search_array,"data_signalsanalysis.sysx = '".$sysx."'");}
			if($sysy != ""){array_push($search_array,"data_signalsanalysis.sysy = '".$sysy."'");}
			if($atmox != ""){array_push($search_array,"data_signalsanalysis.atmox = '".$atmox."'");}
			if($atmoy != ""){array_push($search_array,"data_signalsanalysis.atmoy = '".$atmoy."'");}
			if($surfx != ""){array_push($search_array,"data_signalsanalysis.surfx = '".$surfx."'");}
			if($surfy != ""){array_push($search_array,"data_signalsanalysis.surfy = '".$surfy."'");}
			if($owner != ""){
				/*
				$owner_array_AND = array(); //-
				$owner_array_OR = array(); //+
				$owners = explode("AND",$owner);
				foreach($owners as $ownerItem){
					if(substr($ownerItem, 0, 1) == "-"){
						array_push($owner_array_AND,substr($ownerItem, 1));
					}elseif(substr($ownerItem, 0, 1) == "+"){
						array_push($owner_array_OR,substr($ownerItem, 1));
					}else{
						array_push($owner_array_OR,substr($ownerItem, 0));
					}
				}
				*/
				array_push($search_array,"data_signalsanalysis.owner LIKE '%".$owner."%'");
			}
			if($manager != ""){array_push($search_array,"data_signalsanalysis.manager LIKE '%".$manager."%'");}
			if($operator != ""){array_push($search_array,"data_signalsanalysis.operator LIKE '%".$operator."%'");}
			if($ent_id != ""){array_push($search_array,"data_signalsanalysis.uid LIKE '%:".$ent_id."'");}
			if($ent_name != ""){array_push($search_array,"data_signalsanalysis.name LIKE '%".$ent_name."%'");}
			if($ent_cat != ""){array_push($search_array,"data_signalsanalysis.uid LIKE '".$ent_cat."'");}
			if($ent_class != ""){array_push($search_array,"entities_classes.uid = '$ent_class'");}
			if($ent_type != ""){
				$ent_type = urldecode($ent_type);
				if(strstr($ent_type,";")){
					$typeSet = explode(";",$ent_type);
					$typeArray = array();
					foreach($typeSet as $type){
						array_push($typeArray, "data_signalsanalysis.type = '$type'");
					}
					$search_string_type = implode(" OR ", $typeArray);
					$typeString = "($search_string_type)";
					array_push($search_array,$typeString);
				}else{
					array_push($search_array,"data_signalsanalysis.type = '$ent_type'");
				}
			}
			$search_string = implode(" AND ", $search_array);
			$log_string = mysqli_real_escape_string($db->connection,str_replace("data_signalsanalysis.", "", $search_string));
			mysqli_query($db->connection, "INSERT INTO logs_activities (user_id, log_type, details, timestamp) VALUES ('".$_SESSION['user_id']."', '1', 'SEARCH: ".$log_string."', '".swc_time(time(),TRUE)["timestamp"]."')");
			
			if($queryType == "Historical"){
				$search_array_historical = array();
				if($sector != ""){array_push($search_array_historical,"data_signalsanalysis_archive.sector = '".$sector."'");}
				if($system != ""){if($system == "NULL"){array_push($search_array_historical,"data_signalsanalysis_archive.system IS NULL");}else{array_push($search_array_historical,"data_signalsanalysis_archive.system = '".$system."'");}}
				if($planet != ""){array_push($search_array_historical,"data_signalsanalysis_archive.planet = '".$planet."'");}
				if($galx != ""){array_push($search_array_historical,"data_signalsanalysis_archive.galx = '".$galx."'");}
				if($galy != ""){array_push($search_array_historical,"data_signalsanalysis_archive.galy = '".$galy."'");}
				if($sysx != ""){array_push($search_array_historical,"data_signalsanalysis_archive.sysx = '".$sysx."'");}
				if($sysy != ""){array_push($search_array_historical,"data_signalsanalysis_archive.sysy = '".$sysy."'");}
				if($atmox != ""){array_push($search_array_historical,"data_signalsanalysis_archive.atmox = '".$atmox."'");}
				if($atmoy != ""){array_push($search_array_historical,"data_signalsanalysis_archive.atmoy = '".$atmoy."'");}
				if($surfx != ""){array_push($search_array_historical,"data_signalsanalysis_archive.surfx = '".$surfx."'");}
				if($surfy != ""){array_push($search_array_historical,"data_signalsanalysis_archive.surfy = '".$surfy."'");}
				if($owner != ""){array_push($search_array_historical,"data_signalsanalysis_archive.owner LIKE '%".$owner."%'");}
				if($manager != ""){array_push($search_array_historical,"data_signalsanalysis_archive.manager LIKE '%".$manager."%'");}
				if($operator != ""){array_push($search_array_historical,"data_signalsanalysis_archive.operator LIKE '%".$operator."%'");}
				if($ent_id != ""){array_push($search_array_historical,"data_signalsanalysis_archive.uid LIKE '%:".$ent_id."'");}
				if($ent_name != ""){array_push($search_array_historical,"data_signalsanalysis_archive.name LIKE '%".$ent_name."%'");}
				if($ent_cat != ""){array_push($search_array_historical,"data_signalsanalysis_archive.uid LIKE '".$ent_cat."'");}
				if($ent_class != ""){array_push($search_array_historical,"entities_classes.uid = '$ent_class'");}
				if($ent_type != ""){array_push($search_array_historical,"data_signalsanalysis_archive.type = '$ent_type'");}
				$search_string_historical = implode(" AND ", $search_array_historical);
			}
			
			if($queryType == "Standard"){
				$results_count = mysqli_fetch_assoc(mysqli_query($db->connection,"SELECT COUNT(data_signalsanalysis.uid) AS count FROM data_signalsanalysis LEFT JOIN entities ON data_signalsanalysis.type = entities.uid LEFT JOIN entities_classes ON entities.class = entities_classes.uid LEFT JOIN data_signalsanalysis_customs ON data_signalsanalysis.uid = data_signalsanalysis_customs.uid LEFT JOIN galaxy_sectors ON data_signalsanalysis.sector = galaxy_sectors.uid LEFT JOIN galaxy_systems ON data_signalsanalysis.system = galaxy_systems.uid LEFT JOIN galaxy_planets ON data_signalsanalysis.planet = galaxy_planets.uid LEFT JOIN data_signalsanalysis_activity ON data_signalsanalysis.activity = data_signalsanalysis_activity.id WHERE ".$search_string.$filter))["count"];
				if($sessionType == "Search Details"){
					$search_string = "SELECT data_signalsanalysis.*, data_signalsanalysis_activity.activity_id, entities_classes.class AS ent_class, galaxy_sectors.name AS loc_sec, galaxy_systems.name AS loc_sys, entities.img_small, data_signalsanalysis_customs.custom_image, entities.name AS type_name, galaxy_planets.name AS loc_planet FROM data_signalsanalysis LEFT JOIN entities ON data_signalsanalysis.type = entities.uid LEFT JOIN entities_classes ON entities.class = entities_classes.uid LEFT JOIN data_signalsanalysis_customs ON data_signalsanalysis.uid = data_signalsanalysis_customs.uid LEFT JOIN galaxy_sectors ON data_signalsanalysis.sector = galaxy_sectors.uid LEFT JOIN galaxy_systems ON data_signalsanalysis.system = galaxy_systems.uid LEFT JOIN galaxy_planets ON data_signalsanalysis.planet = galaxy_planets.uid LEFT JOIN data_signalsanalysis_activity ON data_signalsanalysis.activity = data_signalsanalysis_activity.id WHERE ".$search_string."".$filter." ORDER BY timestamp DESC";
				}elseif($sessionType == "Search Summary"){
					$search_string_owners = "SELECT COUNT(data_signalsanalysis.uid) AS count, data_signalsanalysis.owner FROM data_signalsanalysis LEFT JOIN entities ON data_signalsanalysis.type = entities.uid LEFT JOIN entities_classes ON entities.class = entities_classes.uid LEFT JOIN data_signalsanalysis_customs ON data_signalsanalysis.uid = data_signalsanalysis_customs.uid LEFT JOIN galaxy_sectors ON data_signalsanalysis.sector = galaxy_sectors.uid LEFT JOIN galaxy_systems ON data_signalsanalysis.system = galaxy_systems.uid LEFT JOIN galaxy_planets ON data_signalsanalysis.planet = galaxy_planets.uid LEFT JOIN data_signalsanalysis_activity ON data_signalsanalysis.activity = data_signalsanalysis_activity.id WHERE ".$search_string."".$filter." GROUP BY owner ORDER BY data_signalsanalysis.owner ASC";
					$search_string_classes = "SELECT COUNT(data_signalsanalysis.uid) AS count, entities_classes.class AS ent_class FROM data_signalsanalysis LEFT JOIN entities ON data_signalsanalysis.type = entities.uid LEFT JOIN entities_classes ON entities.class = entities_classes.uid LEFT JOIN data_signalsanalysis_customs ON data_signalsanalysis.uid = data_signalsanalysis_customs.uid LEFT JOIN galaxy_sectors ON data_signalsanalysis.sector = galaxy_sectors.uid LEFT JOIN galaxy_systems ON data_signalsanalysis.system = galaxy_systems.uid LEFT JOIN galaxy_planets ON data_signalsanalysis.planet = galaxy_planets.uid LEFT JOIN data_signalsanalysis_activity ON data_signalsanalysis.activity = data_signalsanalysis_activity.id WHERE ".$search_string."".$filter." GROUP BY ent_class ORDER BY ent_class ASC";
					$search_string_types = "SELECT COUNT(data_signalsanalysis.uid) AS count, entities.name AS type_name FROM data_signalsanalysis LEFT JOIN entities ON data_signalsanalysis.type = entities.uid LEFT JOIN entities_classes ON entities.class = entities_classes.uid LEFT JOIN data_signalsanalysis_customs ON data_signalsanalysis.uid = data_signalsanalysis_customs.uid LEFT JOIN galaxy_sectors ON data_signalsanalysis.sector = galaxy_sectors.uid LEFT JOIN galaxy_systems ON data_signalsanalysis.system = galaxy_systems.uid LEFT JOIN galaxy_planets ON data_signalsanalysis.planet = galaxy_planets.uid LEFT JOIN data_signalsanalysis_activity ON data_signalsanalysis.activity = data_signalsanalysis_activity.id WHERE ".$search_string."".$filter." GROUP BY type_name ORDER BY entities.name ASC";
				}
			}elseif($queryType == "Historical"){
				$results_count = mysqli_num_rows(mysqli_query($db->connection,"SELECT data_signalsanalysis.uid FROM data_signalsanalysis LEFT JOIN entities ON data_signalsanalysis.type = entities.uid LEFT JOIN entities_classes ON entities.class = entities_classes.uid LEFT JOIN data_signalsanalysis_customs ON data_signalsanalysis.uid = data_signalsanalysis_customs.uid LEFT JOIN galaxy_sectors ON data_signalsanalysis.sector = galaxy_sectors.uid LEFT JOIN galaxy_systems ON data_signalsanalysis.system = galaxy_systems.uid LEFT JOIN galaxy_planets ON data_signalsanalysis.planet = galaxy_planets.uid LEFT JOIN data_signalsanalysis_activity ON data_signalsanalysis.activity = data_signalsanalysis_activity.id WHERE ".$search_string." UNION ALL SELECT data_signalsanalysis_archive.uid FROM data_signalsanalysis_archive LEFT JOIN entities ON data_signalsanalysis_archive.type = entities.uid LEFT JOIN entities_classes ON entities.class = entities_classes.uid LEFT JOIN data_signalsanalysis_customs ON data_signalsanalysis_archive.uid = data_signalsanalysis_customs.uid LEFT JOIN galaxy_sectors ON data_signalsanalysis_archive.sector = galaxy_sectors.uid LEFT JOIN galaxy_systems ON data_signalsanalysis_archive.system = galaxy_systems.uid LEFT JOIN galaxy_planets ON data_signalsanalysis_archive.planet = galaxy_planets.uid LEFT JOIN data_signalsanalysis_activity ON data_signalsanalysis_archive.activity = data_signalsanalysis_activity.id WHERE ".$search_string_historical.$filter));
				$search_string = "SELECT data_signalsanalysis.*, data_signalsanalysis_activity.activity_id, entities_classes.class AS ent_class, galaxy_sectors.name AS loc_sec, galaxy_systems.name AS loc_sys, entities.img_small, data_signalsanalysis_customs.custom_image, entities.name AS type_name, galaxy_planets.name AS loc_planet FROM data_signalsanalysis LEFT JOIN entities ON data_signalsanalysis.type = entities.uid LEFT JOIN entities_classes ON entities.class = entities_classes.uid LEFT JOIN data_signalsanalysis_customs ON data_signalsanalysis.uid = data_signalsanalysis_customs.uid LEFT JOIN galaxy_sectors ON data_signalsanalysis.sector = galaxy_sectors.uid LEFT JOIN galaxy_systems ON data_signalsanalysis.system = galaxy_systems.uid LEFT JOIN galaxy_planets ON data_signalsanalysis.planet = galaxy_planets.uid LEFT JOIN data_signalsanalysis_activity ON data_signalsanalysis.activity = data_signalsanalysis_activity.id WHERE ".$search_string." UNION ALL SELECT data_signalsanalysis_archive.uid, data_signalsanalysis_archive.name, data_signalsanalysis_archive.type, data_signalsanalysis_archive.owner, data_signalsanalysis_archive.manager, data_signalsanalysis_archive.operator, data_signalsanalysis_archive.sector, data_signalsanalysis_archive.galx, data_signalsanalysis_archive.galy, data_signalsanalysis_archive.system, data_signalsanalysis_archive.sysx, data_signalsanalysis_archive.sysy, data_signalsanalysis_archive.planet, data_signalsanalysis_archive.atmox, data_signalsanalysis_archive.atmoy, data_signalsanalysis_archive.surfx, data_signalsanalysis_archive.surfy, data_signalsanalysis_archive.passengers, data_signalsanalysis_archive.ships, data_signalsanalysis_archive.vehicles, data_signalsanalysis_archive.income, data_signalsanalysis_archive.timestamp, data_signalsanalysis_archive.activity, data_signalsanalysis_activity.activity_id, entities_classes.class AS ent_class, galaxy_sectors.name AS loc_sec, galaxy_systems.name AS loc_sys, entities.img_small, data_signalsanalysis_customs.custom_image, entities.name AS type_name, galaxy_planets.name AS loc_planet FROM data_signalsanalysis_archive LEFT JOIN entities ON data_signalsanalysis_archive.type = entities.uid LEFT JOIN entities_classes ON entities.class = entities_classes.uid LEFT JOIN data_signalsanalysis_customs ON data_signalsanalysis_archive.uid = data_signalsanalysis_customs.uid LEFT JOIN galaxy_sectors ON data_signalsanalysis_archive.sector = galaxy_sectors.uid LEFT JOIN galaxy_systems ON data_signalsanalysis_archive.system = galaxy_systems.uid LEFT JOIN galaxy_planets ON data_signalsanalysis_archive.planet = galaxy_planets.uid LEFT JOIN data_signalsanalysis_activity ON data_signalsanalysis_archive.activity = data_signalsanalysis_activity.id WHERE ".$search_string_historical."".$filter." ORDER BY timestamp DESC";
			}
		}elseif($sessionType == "activity"){
			$activity = mysqli_real_escape_string($db->connection,$_GET["activity"]);
			$query_activity = mysqli_fetch_assoc(mysqli_query($db->connection,"SELECT id FROM data_signalsanalysis_activity WHERE activity_id = '".$activity."'"))["id"];
			$results_count = mysqli_num_rows(mysqli_query($db->connection,"SELECT data_signalsanalysis.uid FROM data_signalsanalysis LEFT JOIN entities ON data_signalsanalysis.type = entities.uid LEFT JOIN entities_classes ON entities.class = entities_classes.uid LEFT JOIN data_signalsanalysis_customs ON data_signalsanalysis.uid = data_signalsanalysis_customs.uid LEFT JOIN galaxy_sectors ON data_signalsanalysis.sector = galaxy_sectors.uid LEFT JOIN galaxy_systems ON data_signalsanalysis.system = galaxy_systems.uid LEFT JOIN galaxy_planets ON data_signalsanalysis.planet = galaxy_planets.uid LEFT JOIN data_signalsanalysis_activity ON data_signalsanalysis.activity = data_signalsanalysis_activity.id WHERE data_signalsanalysis.activity = '".$query_activity."' UNION ALL SELECT data_signalsanalysis_archive.uid FROM data_signalsanalysis_archive LEFT JOIN entities ON data_signalsanalysis_archive.type = entities.uid LEFT JOIN entities_classes ON entities.class = entities_classes.uid LEFT JOIN data_signalsanalysis_customs ON data_signalsanalysis_archive.uid = data_signalsanalysis_customs.uid LEFT JOIN galaxy_sectors ON data_signalsanalysis_archive.sector = galaxy_sectors.uid LEFT JOIN galaxy_systems ON data_signalsanalysis_archive.system = galaxy_systems.uid LEFT JOIN galaxy_planets ON data_signalsanalysis_archive.planet = galaxy_planets.uid LEFT JOIN data_signalsanalysis_activity ON data_signalsanalysis_archive.activity = data_signalsanalysis_activity.id WHERE data_signalsanalysis_archive.activity = '".$query_activity."'".$filter.""));
			$search_string = "SELECT data_signalsanalysis.*, data_signalsanalysis_activity.activity_id, entities_classes.class AS ent_class, galaxy_sectors.name AS loc_sec, galaxy_systems.name AS loc_sys, entities.img_small, data_signalsanalysis_customs.custom_image, entities.name AS type_name, galaxy_planets.name AS loc_planet FROM data_signalsanalysis LEFT JOIN entities ON data_signalsanalysis.type = entities.uid LEFT JOIN entities_classes ON entities.class = entities_classes.uid LEFT JOIN data_signalsanalysis_customs ON data_signalsanalysis.uid = data_signalsanalysis_customs.uid LEFT JOIN galaxy_sectors ON data_signalsanalysis.sector = galaxy_sectors.uid LEFT JOIN galaxy_systems ON data_signalsanalysis.system = galaxy_systems.uid LEFT JOIN galaxy_planets ON data_signalsanalysis.planet = galaxy_planets.uid LEFT JOIN data_signalsanalysis_activity ON data_signalsanalysis.activity = data_signalsanalysis_activity.id WHERE data_signalsanalysis.activity = '".$query_activity."' UNION ALL SELECT data_signalsanalysis_archive.uid, data_signalsanalysis_archive.name, data_signalsanalysis_archive.type, data_signalsanalysis_archive.owner, data_signalsanalysis_archive.manager, data_signalsanalysis_archive.operator, data_signalsanalysis_archive.sector, data_signalsanalysis_archive.galx, data_signalsanalysis_archive.galy, data_signalsanalysis_archive.system, data_signalsanalysis_archive.sysx, data_signalsanalysis_archive.sysy, data_signalsanalysis_archive.planet, data_signalsanalysis_archive.atmox, data_signalsanalysis_archive.atmoy, data_signalsanalysis_archive.surfx, data_signalsanalysis_archive.surfy, data_signalsanalysis_archive.passengers, data_signalsanalysis_archive.ships, data_signalsanalysis_archive.vehicles, data_signalsanalysis_archive.income, data_signalsanalysis_archive.timestamp, data_signalsanalysis_archive.activity, data_signalsanalysis_activity.activity_id, entities_classes.class AS ent_class, galaxy_sectors.name AS loc_sec, galaxy_systems.name AS loc_sys, entities.img_small, data_signalsanalysis_customs.custom_image, entities.name AS type_name, galaxy_planets.name AS loc_planet FROM data_signalsanalysis_archive LEFT JOIN entities ON data_signalsanalysis_archive.type = entities.uid LEFT JOIN entities_classes ON entities.class = entities_classes.uid LEFT JOIN data_signalsanalysis_customs ON data_signalsanalysis_archive.uid = data_signalsanalysis_customs.uid LEFT JOIN galaxy_sectors ON data_signalsanalysis_archive.sector = galaxy_sectors.uid LEFT JOIN galaxy_systems ON data_signalsanalysis_archive.system = galaxy_systems.uid LEFT JOIN galaxy_planets ON data_signalsanalysis_archive.planet = galaxy_planets.uid LEFT JOIN data_signalsanalysis_activity ON data_signalsanalysis_archive.activity = data_signalsanalysis_activity.id WHERE data_signalsanalysis_archive.activity = '".$query_activity."'".$filter." ORDER BY timestamp DESC";
			mysqli_query($db->connection, "INSERT INTO logs_activities (user_id, log_type, details, timestamp) VALUES ('".$_SESSION['user_id']."', '1', 'SEARCH: activity = ".$activity."', '".swc_time(time(),TRUE)["timestamp"]."')");
			//$session->alert = $search_string;
		}elseif($sessionType == "buffer review"){
			$activity_id = mysqli_real_escape_string($db->connection,$_GET["activity_id"]);
			$query_activity = mysqli_fetch_assoc(mysqli_query($db->connection,"SELECT id FROM data_signalsanalysis_activity WHERE activity_id = '".$activity_id."'"))["id"];
			$results_count = mysqli_fetch_assoc(mysqli_query($db->connection,"SELECT COUNT(data_signalsanalysis_buffer.id) AS count FROM data_signalsanalysis_buffer LEFT JOIN entities ON data_signalsanalysis_buffer.type = entities.uid LEFT JOIN entities_classes ON entities.class = entities_classes.uid LEFT JOIN data_signalsanalysis_customs ON data_signalsanalysis_buffer.uid = data_signalsanalysis_customs.uid LEFT JOIN galaxy_sectors ON data_signalsanalysis_buffer.sector = galaxy_sectors.uid LEFT JOIN galaxy_systems ON data_signalsanalysis_buffer.system = galaxy_systems.uid LEFT JOIN galaxy_planets ON data_signalsanalysis_buffer.planet = galaxy_planets.uid LEFT JOIN data_signalsanalysis_activity ON data_signalsanalysis_buffer.activity = data_signalsanalysis_activity.id WHERE data_signalsanalysis_buffer.activity = '".$query_activity."'"))["count"];
			$search_string = "SELECT data_signalsanalysis_buffer.*, data_signalsanalysis_activity.activity_id, entities_classes.class AS ent_class, galaxy_sectors.name AS loc_sec, galaxy_systems.name AS loc_sys, entities.img_small, data_signalsanalysis_customs.custom_image, entities.name AS type_name, galaxy_planets.name AS loc_planet FROM data_signalsanalysis_buffer LEFT JOIN entities ON data_signalsanalysis_buffer.type = entities.uid LEFT JOIN entities_classes ON entities.class = entities_classes.uid LEFT JOIN data_signalsanalysis_customs ON data_signalsanalysis_buffer.uid = data_signalsanalysis_customs.uid LEFT JOIN galaxy_sectors ON data_signalsanalysis_buffer.sector = galaxy_sectors.uid LEFT JOIN galaxy_systems ON data_signalsanalysis_buffer.system = galaxy_systems.uid LEFT JOIN galaxy_planets ON data_signalsanalysis_buffer.planet = galaxy_planets.uid LEFT JOIN data_signalsanalysis_activity ON data_signalsanalysis_buffer.activity = data_signalsanalysis_activity.id WHERE data_signalsanalysis_buffer.activity = '".$query_activity."'";
		}elseif($sessionType == "InventoryList" || $sessionType == "InventoryLocation"){
			$owner = mysqli_real_escape_string($db->connection,$_POST["owner"]);
			if($sessionType == "InventoryList"){
				$log_entry = mysqli_real_escape_string($db->connection, "ANALYTIC (Inventory List): owner = ".$_POST["owner"]);
				mysqli_query($db->connection, "INSERT INTO logs_activities (user_id, log_type, details, timestamp) VALUES ('".$_SESSION['user_id']."', '1', '".$log_entry."', '".swc_time(time(),TRUE)["timestamp"]."')");
			}elseif($sessionType == "InventoryLocation"){
				$log_entry = mysqli_real_escape_string($db->connection, "ANALYTIC (Inventory Location): owner = ".$_POST["owner"]);
				mysqli_query($db->connection, "INSERT INTO logs_activities (user_id, log_type, details, timestamp) VALUES ('".$_SESSION['user_id']."', '1', '".$log_entry."', '".swc_time(time(),TRUE)["timestamp"]."')");
			}
		}elseif($sessionType == "FacilityIncome"){
			$focus = mysqli_real_escape_string($db->connection,$_POST["focus"]);
			$log_entry = mysqli_real_escape_string($db->connection, "ANALYTIC (Facility Income): Focus = ".$focus."");
			mysqli_query($db->connection, "INSERT INTO logs_activities (user_id, log_type, details, timestamp) VALUES ('".$_SESSION['user_id']."', '1', '".$log_entry."', '".swc_time(time(),TRUE)["timestamp"]."')");
			if($focus == "owner"){
				$queryIncome = mysqli_query($db->connection,"SELECT owner AS focus, SUM(income) as facilityincome FROM data_signalsanalysis WHERE uid LIKE '4:%' AND owner IS NOT NULL GROUP BY focus HAVING facilityincome > 0 ORDER BY focus");
			}else if($focus == "manager"){
				$queryIncome = mysqli_query($db->connection,"SELECT manager AS focus, SUM(income) as facilityincome FROM data_signalsanalysis WHERE uid LIKE '4:%' AND manager IS NOT NULL GROUP BY focus HAVING facilityincome > 0  ORDER BY focus");
			}else if($focus == "operator"){
				$queryIncome = mysqli_query($db->connection,"SELECT operator AS focus, SUM(income) as facilityincome FROM data_signalsanalysis WHERE uid LIKE '4:%' AND operator IS NOT NULL GROUP BY focus HAVING facilityincome > 0  ORDER BY focus");
			}
		}elseif($sessionType == "FactionDataByType"){
			//Work in progress
		}elseif($sessionType == "upload"){
			include("assets/php/swc_api/xml_upload.php");
		}elseif($sessionType == "buffer"){
			if($_POST["action"] == "Deny"){
				$activity_id = mysqli_real_escape_string($db->connection, $_POST["activity_id"]);
				$activity_query = mysqli_fetch_assoc(mysqli_query($db->connection, "SELECT id, activity_time FROM data_signalsanalysis_activity WHERE activity_id = '$activity_id'"));
				$activity = $activity_query["id"];
				$activity_timestamp = $activity_query["timestamp"];
				mysqli_query($db->connection, "DELETE FROM data_signalsanalysis_buffer WHERE activity = '".$activity."'");
				mysqli_query($db->connection, "DELETE FROM data_signalsanalysis_activity WHERE id = '".$activity."'");
				mysqli_query($db->connection, "DELETE FROM data_signalsanalysis_scantime WHERE timestamp = '".$$activity_timestamp."' AND status = '0'");
				$session->alert = "<div class=\"alert alert-success\" style=\"font-size:14px;\">The data from activity (".$activity_id.") has been deleted from the buffer</div>";
			}elseif($_POST["action"] == "Approve"){
				include("assets/php/swc_api/xml_upload_buffer.php");
			}
		}
	}elseif(isset($_POST['search'])){
		$sessionType = "Search Details";
		$search = mysqli_real_escape_string($db->connection, $_POST['search']);
		$results_count = mysqli_fetch_assoc(mysqli_query($db->connection,"SELECT COUNT(data_signalsanalysis.uid) AS count FROM data_signalsanalysis LEFT JOIN entities ON data_signalsanalysis.type = entities.uid LEFT JOIN entities_classes ON entities.class = entities_classes.uid LEFT JOIN data_signalsanalysis_customs ON data_signalsanalysis.uid = data_signalsanalysis_customs.uid LEFT JOIN galaxy_sectors ON data_signalsanalysis.sector = galaxy_sectors.uid LEFT JOIN galaxy_systems ON data_signalsanalysis.system = galaxy_systems.uid LEFT JOIN galaxy_planets ON data_signalsanalysis.planet = galaxy_planets.uid LEFT JOIN data_signalsanalysis_activity ON data_signalsanalysis.activity = data_signalsanalysis_activity.id WHERE owner LIKE '%$search%' OR data_signalsanalysis.uid LIKE '%:$search' OR data_signalsanalysis.name LIKE '%$search%' OR data_signalsanalysis_activity.activity_id = '$search'".$filter.""))["count"];
		$search_string = "SELECT data_signalsanalysis.*, data_signalsanalysis_activity.activity_id, entities_classes.class AS ent_class, galaxy_sectors.name AS loc_sec, galaxy_systems.name AS loc_sys, entities.img_small, data_signalsanalysis_customs.custom_image, entities.name AS type_name, galaxy_planets.name AS loc_planet FROM data_signalsanalysis LEFT JOIN entities ON data_signalsanalysis.type = entities.uid LEFT JOIN entities_classes ON entities.class = entities_classes.uid LEFT JOIN data_signalsanalysis_customs ON data_signalsanalysis.uid = data_signalsanalysis_customs.uid LEFT JOIN galaxy_sectors ON data_signalsanalysis.sector = galaxy_sectors.uid LEFT JOIN galaxy_systems ON data_signalsanalysis.system = galaxy_systems.uid LEFT JOIN galaxy_planets ON data_signalsanalysis.planet = galaxy_planets.uid LEFT JOIN data_signalsanalysis_activity ON data_signalsanalysis.activity = data_signalsanalysis_activity.id WHERE owner LIKE '%$search%' OR data_signalsanalysis.uid LIKE '%:$search' OR data_signalsanalysis.name LIKE '%$search%' OR data_signalsanalysis_activity.activity_id = '$search'".$filter." ORDER BY timestamp DESC";
		$log_entry = mysqli_real_escape_string($db->connection, "SEARCH: Broad search on \"$search\"");
		mysqli_query($db->connection, "INSERT INTO logs_activities (user_id, log_type, details, timestamp) VALUES ('".$_SESSION['user_id']."', '1', '".$log_entry."', '".swc_time(time(),TRUE)["timestamp"]."')");
	}
?>

	<?php include("assets/php/_head.php"); ?>
    <body style="position: relative;">
        <!-- Page Wrapper -->
        <div id="page-wrapper" class="page-loading" style="position: relative;">
            <?php include("assets/php/_preloader.php"); ?>
			<!-- Page Container -->
            <div id="page-container" class="header-fixed-top sidebar-visible-lg-full" style="position:relative;">
                
                <?php include("assets/php/_sidebar-alt.php"); ?>
                <?php include("assets/php/_sidebar.php"); ?>

                <!-- Main Container -->
                <div id="main-container" style="position:relative;">
                    <?php include("assets/php/_header.php"); ?>

                    <!-- Page content -->
                    <div id="page-content" style="overflow:auto; position:relative;">
                        <!-- Page Header -->
                        <div class="content-header">
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="header-section">
                                        <h1>Signals Analysis</h1>
                                    </div>
                                </div>
                                <div class="col-sm-6 hidden-xs">
                                    <div class="header-section">
                                        <ul class="breadcrumb breadcrumb-top">
                                            <li>Databases</li>
                                            <li><a href="signalsanalysis.php">Signals Analysis</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
						<?php if(isset($session->alert)){echo $session->alert;} ?>
						
                        <!-- Example Block -->
                        <div class="block" style="overflow:auto; min-height:1000px; position:relative;">
                            <!-- Example Title -->
                            <div class="block-title hidden-print" style="overflow: auto;">
								<h2 class="pull-left">Data</h2>
								<?php
									$db = new Database;
									$db->connect();
									if($_SESSION['user_privs']['signalsanalysis_search'] > 0 || $_SESSION['user_privs']['admin'] > 0){
										echo "
											<button title=\"Search\" class=\"btn btn-primary\" data-target=\"#modalSearch\" data-toggle=\"modal\">
												<span class=\"fas fa-search\"></span> Search
											</button>
										";
									}
									if($_SESSION['user_privs']['signalsanalysis_analytics'] > 0 || $_SESSION['user_privs']['admin'] > 0){
										echo "
											<button title=\"Analytics\" class=\"btn btn-primary\" data-target=\"#modalAnalytics\" data-toggle=\"modal\">
												<span class=\"fas fa-chart-bar\"></span> Analytics
											</button>
										";
									}
									if($_SESSION['user_privs']['signalsanalysis_upload'] > 0 || $_SESSION['user_privs']['admin'] > 0){
										echo "
											<button title=\"Upload\" class=\"btn btn-info\" data-target=\"#modalUpload\" data-toggle=\"modal\">
												<span class=\"fas fa-upload\"></span> Upload
											</button>
										";
									}
									
									$buffer_count = mysqli_fetch_assoc(mysqli_query($db->connection, "SELECT COUNT(id) AS count FROM data_signalsanalysis_activity WHERE status = '0'"))["count"];
									if(($_SESSION['user_privs']['signalsanalysis_upload'] == 2 || $_SESSION['user_privs']['admin'] > 0) && $buffer_count != 0){
										echo "
											<form method=\"post\" action=\"signalsanalysis.php?type=buffer\" id=\"bufferForm\" style=\"margin:1px;display:inline;\">
												<input type=\"submit\" class=\"btn btn-info fa fa-input\" style=\"height:33px;\" id=\"buffer\" name=\"Buffer\" value=\"&#xf1b3 Buffer (".$buffer_count.")\">
											</form>
										";
									}
								?>
								<h2 class="pull-right"><?php if(isset($results_count)){echo "Results: ".$results_count;}?></h2>
							</div>
							<div class="block-content col-sm-12">
							<?php 
								if(isset($search_string)){
									if($sessionType == "Search Details" || $sessionType == "Search Summary" || $sessionType == "activity" || $sessionType == "buffer review" || isset($_POST['search'])){
										include("assets/php/signalsanalysis/search.php");
									}
								}elseif(isset($sessionType)) {
								    if($sessionType == "InventoryList"){ //NEEDS FIXED FOR SPEED
								        include("assets/php/signalsanalysis/inventoryList.php");
								    }elseif($sessionType == "InventoryLocation"){
								        include("assets/php/signalsanalysis/inventoryLocation.php");
								    }elseif($sessionType == "FacilityIncome"){
								        include("assets/php/signalsanalysis/facilityIncome.php");
								    }elseif($sessionType == "buffer"){
								        include("assets/php/signalsanalysis/buffer.php");
								    }
								}else{
									include("assets/php/signalsanalysis/pingMap.php");
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
										<form class="form" role="form" method="get" action="signalsanalysis.php" id="searchForm">
											<div class="modal-body">
												<div class="form-group">
													<label class="col-sm-3" for="inputQueryType">Query Type</label>
													<div class="col-sm-9">
														<select class="form-control" id="inputQueryType" name="inputQueryType">
															<option value="Standard">Standard</option>
															<option value="Historical">Historical</option>
														</select>
													</div>
												</div>
												<div class="form-group">
													<label class="col-sm-3" for="inputActivity">Activity</label>
													<div class="col-sm-9">
														<input class="form-control" id="inputActivity" name="inputActivity" type="text" placeholder="{Activity ID}">
													</div>
												</div>
												<div class="form-group">
													<label class="col-sm-3" for="inputSector">Sector</label>
													<div class="col-sm-9">
														<select class="form-control" id="inputSector" name="inputSector">
															<option value="">-- Any --</option>
															<option value="NULL">Deepspace</option>
															<?php
																$db = new Database;
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
															<option value="">-- Any --</option>
															<option value="NULL">Deepspace</option>
															<?php
																$db = new Database;
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
													<label class="col-sm-3" for="inputGalX">Gal-X</label>
													<div class="col-sm-3">
														<select class="form-control" id="inputGalX" name="inputGalX">
															<option value="">-- Any --</option>
															<?php
																$count = -500;
																while($count <= 500){
																	echo "<option value=\"".$count."\">".$count."</option>";
																	$count++;
																}
															?>
														</select>
													</div>
												</div>
												<div class="form-group">
													<label class="col-sm-3" for="inputGalY">Gal-Y</label>
													<div class="col-sm-3">
														<select class="form-control" id="inputGalY" name="inputGalY">
															<option value="">-- Any --</option>
															<?php
																$count = -500;
																while($count <= 500){
																	echo "<option value=\"".$count."\">".$count."</option>";
																	$count++;
																}
															?>
														</select>
													</div>
												</div>
												<div class="form-group">
													<label class="col-sm-3" for="inputPlanet">Planet</label>
													<div class="col-sm-9">
														<select class="form-control" id="inputPlanet" name="inputPlanet">
															<option value="">-- Any --</option>
															<?php
																$db = new Database;
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
													<label class="col-sm-3" for="inputSysX">Sys-X</label>
													<div class="col-sm-3">
														<select class="form-control" id="inputSysX" name="inputSysX">
															<option value="">-- Any --</option>
															<?php
																$count = 0;
																while($count <= 19){
																	echo "<option value=\"".$count."\">".$count."</option>";
																	$count++;
																}
															?>
														</select>
													</div>
												</div>
												<div class="form-group">
													<label class="col-sm-3" for="inputSysY">Sys-Y</label>
													<div class="col-sm-3">
														<select class="form-control" id="inputSysY" name="inputSysY">
															<option value="">-- Any --</option>
															<?php
																$count = 0;
																while($count <= 19){
																	echo "<option value=\"".$count."\">".$count."</option>";
																	$count++;
																}
															?>
														</select>
													</div>
												</div>
												<div class="form-group">
													<label class="col-sm-3" for="inputAtmoX">Atmo-X</label>
													<div class="col-sm-3">
														<select class="form-control" id="inputAtmoX" name="inputAtmoX">
															<option value="">-- Any --</option>
															<?php
																$count = 0;
																while($count <= 19){
																	echo "<option value=\"".$count."\">".$count."</option>";
																	$count++;
																}
															?>
														</select>
													</div>
												</div>
												<div class="form-group">
													<label class="col-sm-3" for="inputAtmoY">Atmo-Y</label>
													<div class="col-sm-3">
														<select class="form-control" id="inputAtmoY" name="inputAtmoY">
															<option value="">-- Any --</option>
															<?php
																$count = 0;
																while($count <= 19){
																	echo "<option value=\"".$count."\">".$count."</option>";
																	$count++;
																}
															?>
														</select>
													</div>
												</div>
												<div class="form-group">
													<label class="col-sm-3" for="inputSurfX">Surf-X</label>
													<div class="col-sm-3">
														<select class="form-control" id="inputSurfX" name="inputSurfX">
															<option value="">-- Any --</option>
															<?php
																$count = 0;
																while($count <= 21){
																	echo "<option value=\"".$count."\">".$count."</option>";
																	$count++;
																}
															?>
														</select>
													</div>
												</div>
												<div class="form-group">
													<label class="col-sm-3" for="inputSurfY">Surf-Y</label>
													<div class="col-sm-3">
														<select class="form-control" id="inputSurfY" name="inputSurfY">
															<option value="">-- Any --</option>
															<?php
																$count = 0;
																while($count <= 21){
																	echo "<option value=\"".$count."\">".$count."</option>";
																	$count++;
																}
															?>
														</select>
													</div>
												</div>
												<div class="form-group" style="vertical-align:middle;">
													<label class="col-sm-3" for="inputOwner">Owner</label>
													<div class="col-sm-9"><input class="form-control" id="inputOwner" name="inputOwner" type="text" placeholder="{Name of owner}"></div>
												</div>
												<div class="form-group">
													<label class="col-sm-3" for="inputManager">Manager</label>
													<div class="col-sm-9"><input class="form-control" id="inputManager" name="inputManager" type="text" placeholder="{Name of manager}"></div>
												</div>
												<div class="form-group">
													<label class="col-sm-3" for="inputOperator">Operator</label>
													<div class="col-sm-9"><input class="form-control" id="inputOperator" name="inputOperator" type="text" placeholder="{Name of operator}"></div>
												</div>
												<div class="form-group" style="vertical-align:middle;">
													<label class="col-sm-3" for="inputID">Entity ID</label>
													<div class="col-sm-9"><input class="form-control" id="inputID" name="inputID" type="text" placeholder="{ID of entity}"></div>
												</div>
												<div class="form-group" style="vertical-align:middle;">
													<label class="col-sm-3" for="inputName">Entity Name</label>
													<div class="col-sm-9"><input class="form-control" id="inputName" name="inputName" type="text" placeholder="{Name of entity}"></div>
												</div>
												<div class="form-group">
													<label class="col-sm-3" for="inputCategory">Entity Category</label>
													<div class="col-sm-9">
														<select class="form-control" id="inputCategory" name="inputCategory">
															<option value="">-- Any --</option>
															<option value="4:%">Facilities</option>
															<option value="2:%">Ships</option>
															<option value="5:%">Stations</option>
															<option value="3:%">Vehicles</option>
														</select>
													</div>
												</div>
												<div class="form-group">
													<label class="col-sm-3" for="inputClass">Entity Class</label>
													<div class="col-sm-9">
														<select class="form-control" id="inputClass" name="inputClass">
															<option value="">-- Any --</option>
															<?php
																$db = new Database;
																$db->connect();
																$query = mysqli_query($db->connection,"SELECT uid, class FROM entities_classes WHERE (uid LIKE '301:%' OR uid LIKE '302:%' OR uid LIKE '303:%') AND (class NOT LIKE 'CP Bonus%' AND class NOT LIKE 'Rare%' AND class <> 'Debris' AND class <> 'Wrecks') ORDER BY class ASC");
																while($data = mysqli_fetch_assoc($query)){
																	echo "<option value=\"".$data['uid']."\">".$data['class']."</option>";
																}
																$db->disconnect();
																unset($db);
															?>
														</select>
													</div>
												</div>
												<div class="form-group">
													<label class="col-sm-3" for="inputType">Entity Type</label>
													<div class="col-sm-9">
														<select class="form-control" id="inputType" name="inputType">
															<option value="">-- Any --</option>
															<?php
																$db = new Database;
																$db->connect();
																$query = mysqli_query($db->connection,"SELECT uid, name FROM entities ORDER BY name ASC");
																while($data = mysqli_fetch_assoc($query)){
																	echo "<option value=\"".$data['uid']."\">".$data['name']."</option>";
																}
																$db->disconnect();
																unset($db);
															?>
														</select>
													</div>
												</div>
											</div>
											<div class="modal-footer">
												<button class="btn btn-default pull-left" type="button" data-dismiss="modal">Cancel</button>
												<input id="submit1" class="btn btn-primary " type="submit" name="type" value="Search Summary">
												<input id="submit2" class="btn btn-primary " type="submit" name="type" value="Search Details">
											</div>
										</form>
									</div>
								</div>
							</div>
							
							<div class="modal fade" id="modalAnalytics">
								<div class="modal-dialog">
									<div class="modal-content">
										<div class="modal-header">
											<button class="close" aria-hidden="true" type="button" data-dismiss="modal">×</button>
											<h4 class="modal-title">Analytics</h4>
										</div>
										<div class="modal-body">
											<div class="form-group">
												<label class="col-sm-5" for="InventoryList">Inventory List</label>
												<div class="col-sm-5">
													<form class="form-horizontal" role="form" method="post" action="signalsanalysis.php" id="analyticsForm">
													<input type="text" name="owner" style="width:100%;" placeholder="{Owner name}" required>
												</div>
												<div class="col-sm-2">
													<input id="InventoryList" class="btn btn-primary " type="submit" value="Run">
													<input type="hidden" name="type" value="InventoryList">
													</form>
												</div>
											</div><br/><hr/><br/>
											<div class="form-group">
												<label class="col-sm-5" for="InventoryLocation">Inventory Location</label>
												<div class="col-sm-5">
													<form class="form-horizontal" role="form" method="post" action="signalsanalysis.php">
													<input type="text" name="owner" style="width:100%;" placeholder="{Owner name}" required>
													<select style="width:100%;" name="mapType">
														<option value="all" selected>All</option>
														<option value="ship">Ships</option>
														<option value="medical">Medical</option>
														<option value="production">Production</option>
														<option value="recycling">Recycling</option>
														<option value="resources">Resources</option>
													</select>
												</div>
												<div class="col-sm-2">
													<input id="InventoryLocation" class="btn btn-primary " type="submit" value="Run">
													<input type="hidden" name="type" value="InventoryLocation">
													</form>
												</div>
											</div><br/><hr/><br/>
											<div class="form-group">
												<label class="col-sm-5" for="FacilityIncome">Facility Income</label>
												<div class="col-sm-5">
													<form class="form-horizontal" role="form" method="post" action="signalsanalysis.php">
													<select style="width:100%;" name="focus">
														<option value="owner">Owner</option>
														<option value="manager" selected>Manager (Best results)</option>
														<option value="operator">Operator</option>
													</select>
												</div>
												<div class="col-sm-2">
													<input id="FacilityIncome" class="btn btn-primary " type="submit" value="Run">
													<input type="hidden" name="type" value="FacilityIncome">
													</form>
												</div>
											</div><br/><hr/><br/>
											<!--
											<div class="form-group">
												<label class="col-sm-5" for="InventoryLocation">Faction Data by Type</label>
												<div class="col-sm-5">
													<form class="form-horizontal" role="form" method="post" action="signalsanalysis.php">
												</div>
												<div class="col-sm-2">
													<input id="FactionDataByType" class="btn btn-primary " type="submit" value="Run">
													<input id="type" type="hidden" name="type" value="FactionDataByType">
													</form>
												</div>
											</div>
											-->
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
											<h4 class="modal-title">Upload</h4>
										</div>
										<div class="modal-body">
											<div class="form-group">
												<div class="col-sm-12">
													<form class="form-horizontal" role="form" method="post" enctype="multipart/form-data" action="signalsanalysis.php" id="uploadForm">
														<input type="file" name="file[]" multiple>
														<input type="hidden" name="type" value="upload">
												</div>
											</div><br/><br/>
										</div>
										<div class="modal-footer">
											<button class="btn btn-default pull-left" type="button" data-dismiss="modal">Cancel</button>
											<input id="upload" class="btn btn-primary " type="submit" value="Upload">
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