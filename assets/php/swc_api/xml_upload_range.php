<?php
if($_SESSION['user_privs']['signalsanalysis_upload'] == 1 && $_SESSION['user_privs']['admin'] == 0){
	$upload_db = "buffer";
	$upload_activity = 0;
}elseif($_SESSION['user_privs']['signalsanalysis_upload'] == 2 || $_SESSION['user_privs']['admin'] > 0){
	$upload_db = "live";
	$upload_activity = 1;
}

$db = new database;
$db->connect();
	
//Timeing data
$year = $xml_file->channel->cgt->years;
$day = $xml_file->channel->cgt->days;
$hour = $xml_file->channel->cgt->hours;
$minute = $xml_file->channel->cgt->minutes;
$second = $xml_file->channel->cgt->seconds;

//Set activity identifying data
$activity = md5($year.$day.$hour.$minute.$second.$xml_file->channel->cgt->{'seconds'}.$xml_file->channel->location->galX.$xml_file->channel->location->galY);
$check_activity = mysqli_fetch_assoc(mysqli_query($db->connection,"SELECT COUNT(activity_id) AS count FROM data_signalsanalysis_activity WHERE activity_id = '$activity'"))["count"];
if($check_activity == 0){
	$activity_time = real_time($year,$day,$hour,$minute,$second);
	$activity_time = swc_time($activity_time)["timestamp"];
	mysqli_query($db->connection,"INSERT INTO data_signalsanalysis_activity (activity_id, activity_collector, activity_time, status) VALUES ('$activity', '".$_SESSION['user_id']."', '$activity_time', '$upload_activity')");
	$activity_id = mysqli_fetch_assoc(mysqli_query($db->connection,"SELECT id FROM data_signalsanalysis_activity WHERE activity_id = '$activity'"))["id"];

	//Location data
	$galx = mysqli_real_escape_string($db->connection,$xml_file->channel->location->galX);
	$galy = mysqli_real_escape_string($db->connection,$xml_file->channel->location->galY);
	
	$query_galaxy = mysqli_fetch_assoc(mysqli_query($db->connection,"SELECT uid, sector FROM galaxy_systems WHERE galx = '$galx' AND galy = '$galy'"));
	$system = $query_galaxy['uid'];
	
	if(empty($system)){
		$system = NULL;
	}else{
		$query_planets = mysqli_query($db->connection,"SELECT uid, sysx, sysy FROM galaxy_planets WHERE `system` = '".$system."'");
		$planet_array = array();
		while($row = mysqli_fetch_assoc($query_planets)){
			$planet_array[$row['uid']] = $row['sysx'].",".$row['sysy'];
		}
	}
	
	if(empty($query_galaxy['sector'])){
		$sector = "NULL";
	}else{
		$sector = "'".$query_galaxy['sector']."'";
	}
	
	
	//Discern scope of location
	if(isset($xml_file->channel->location->groundX)){ //Ground
		$sysx = mysqli_real_escape_string($db->connection,$xml_file->channel->location->sysX);
		$sysy = mysqli_real_escape_string($db->connection,$xml_file->channel->location->sysY);
		$atmox = mysqli_real_escape_string($db->connection,$xml_file->channel->location->surfX);
		$atmoy = mysqli_real_escape_string($db->connection,$xml_file->channel->location->surfY);
	}elseif(isset($xml_file->channel->location->surfX)){ //Atmosphere
		$sysx = mysqli_real_escape_string($db->connection,$xml_file->channel->location->sysX);
		$sysy = mysqli_real_escape_string($db->connection,$xml_file->channel->location->sysY);
		$groundx = NULL;
		$groundy = NULL;
	}else{ //Space
		$atmox = NULL;
		$atmoy = NULL;
		$groundx = NULL;
		$groundy = NULL;
		$scope = "space";
	}
	
	$query_entities = mysqli_query($db->connection,"SELECT uid, name FROM entities");
	$entity_array = array();
	while($row = mysqli_fetch_assoc($query_entities)){
		$entity_array[$row['uid']] = $row['name'];
	}
	
	$count_new = 0;
	$count_update = 0;
	$count_archive = 0;
	$count_corrupt = 0;
	
	foreach($xml_file->channel->item as $item){
		//Strip the entity data
		$name = mysqli_real_escape_string($db->connection,$item->name);
		$typeName = mysqli_real_escape_string($db->connection,str_replace("&amp;","&",$item->typeName));
		$entityType = array_search($typeName, $entity_array);
		$entityID = explode(":",$entityType)[0].":".$item->entityID;
		$travelDirection = mysqli_real_escape_string($db->connection,$item->travelDirection);
		$ownerName = mysqli_real_escape_string($db->connection,$item->ownerName);
			//Custom image
			if(strpos($item->image,"img.swcombine.com") || strpos($item->image,"custom.swcombine.com")){
				$image = mysqli_real_escape_string($db->connection,$item->image);
				mysqli_query($db->connection,"INSERT INTO data_signalsanalysis_customs (uid, custom_image, timestamp) VALUES ('$entityID', '$image', '$activity_time') ON DUPLICATE KEY UPDATE custom_image = '$image', timestamp = '$activity_time'");
			}

		//Figure out where generic x/y goes
		if(isset($xml_file->channel->location->groundX)){ //Ground
			$groundx = $item->x;
			$groundy = $item->y;
		}elseif(isset($xml_file->channel->location->surfX)){ //Atmosphere
			$atmox = $item->x;
			$atmoy = $item->y;
			if($atmox == "" && $atmoy == ""){
				$atmox = mysqli_real_escape_string($db->connection,$xml_file->channel->location->surfX);
				$atmoy = mysqli_real_escape_string($db->connection,$xml_file->channel->location->surfY);
			}
		}else{ //Space
			$sysx = $item->x;
			$sysy = $item->y;
			if($sysx == "" && $sysy == ""){
				$sysx = mysqli_real_escape_string($db->connection,$xml_file->channel->location->sysX);
				$sysy = mysqli_real_escape_string($db->connection,$xml_file->channel->location->sysY);
			}
		}
		
		if(!empty($system)){
			$planet_ins = array_search($sysx.",".$sysy,$planet_array);
			if(empty($planet_ins)){
				$planet_ins = "NULL";
			}else{
				$planet_ins = "'".$planet_ins."'";
			}
		}else{
			$planet_ins = "NULL";
		}
		
		//Geolocation
		if($item->travelDirection != "Stationary" && $item->travelDirection != ""){
			mysqli_query($db->connection, "INSERT INTO data_tracking (target, entity, confidence, `source`, timestamp, sector, galx, galy, `system`, sysx, sysy, planet, atmox, atmoy, surfx, surfy) VALUES ('$ownerName', '$entityID', '2', '$activity', '$activity_time', ".$sector.", '$galx', '$galy', ".(($system==NULL)?"NULL":("'".$system."'")).", '$sysx', '$sysy', ".$planet_ins.", ".(($atmox==NULL)?"NULL":("'".$atmox."'")).", ".(($atmoy==NULL)?"NULL":("'".$atmoy."'")).", ".(($groundx==NULL)?"NULL":("'".$groundx."'")).", ".(($groundy==NULL)?"NULL":("'".$groundy."'")).")");
			/*
			if(verify_character($ownerName)){ //target is character
				
			}else{ //target is faction
				
			}
			*/
		}
		
//".(($sector==NULL)?"NULL":("'".$sector."'"))."
		if($upload_db == "live"){
			//Current or historical data
			$entity = mysqli_fetch_assoc(mysqli_query($db->connection,"SELECT * FROM data_signalsanalysis WHERE uid = '$entityID'"));
			if(empty($entityType)){
				if(empty($item->isWorking) && !empty($item->ownerName) && $item->ownerName == $item->name && !empty($item->raceName)){
					add_character($item->name);
					mysqli_query($db->connection, "INSERT INTO data_tracking (target, confidence, `source`, timestamp, sector, galx, galy, `system`, sysx, sysy, planet, atmox, atmoy, surfx, surfy) VALUES ('$ownerName', '3', '$activity', '$activity_time', ".$sector.", '$galx', '$galy', ".(($system==NULL)?"NULL":("'".$system."'")).", '$sysx', '$sysy', ".$planet_ins.", ".(($atmox==NULL)?"NULL":("'".$atmox."'")).", ".(($atmoy==NULL)?"NULL":("'".$atmoy."'")).", ".(($groundx==NULL)?"NULL":("'".$groundx."'")).", ".(($groundy==NULL)?"NULL":("'".$groundy."'")).")");
				}else{
					$count_corrupt++;
				}
			}elseif(empty($entity['uid'])){ // INSERT
				mysqli_query($db->connection,"INSERT INTO data_signalsanalysis (uid, name, type, owner, sector, galx, galy, `system`, sysx, sysy, planet, atmox, atmoy, surfx, surfy, timestamp, activity) VALUES ('$entityID', '$name', '$entityType', '$ownerName', ".$sector.", '$galx', '$galy', ".(($system==NULL)?"NULL":("'".$system."'")).", '$sysx', '$sysy', ".$planet_ins.", ".(($atmox==NULL)?"NULL":("'".$atmox."'")).", ".(($atmoy==NULL)?"NULL":("'".$atmoy."'")).", ".(($groundx==NULL)?"NULL":("'".$groundx."'")).", ".(($groundy==NULL)?"NULL":("'".$groundy."'")).", '$activity_time', '$activity_id')");
				$count_new++;
			}elseif($entity['timestamp'] < $activity_time){ // UPDATE
				mysqli_query($db->connection,"INSERT INTO data_signalsanalysis_archive (uid, name, type, owner, manager, operator, sector, galx, galy, `system`, sysx, sysy, planet, atmox, atmoy, surfx, surfy, passengers, ships, vehicles, materials, timestamp, activity) VALUES ('".$entity["uid"]."', '".$entity["name"]."', '".$entity["type"]."', '".$entity["owner"]."', ".(($entity["manager"]==NULL)?"NULL":("'".$entity["manager"]."'")).", ".(($entity["operator"]==NULL)?"NULL":("'".$entity["operator"]."'")).", ".(($entity["sector"]==NULL)?"NULL":("'".$entity["sector"]."'")).", '".$entity["galx"]."', '".$entity["galy"]."', ".(($entity["system"]==NULL)?"NULL":("'".$entity["system"]."'")).", '".$entity["sysx"]."', '".$entity["sysy"]."', ".(($entity["planet"]==NULL)?"NULL":("'".$entity["planet"]."'")).", ".(($entity["atmox"]==NULL)?"NULL":("'".$entity["atmox"]."'")).", ".(($entity["atmoy"]==NULL)?"NULL":("'".$entity["atmoy"]."'")).", ".(($entity["surfx"]==NULL)?"NULL":("'".$entity["surfx"]."'")).", ".(($entity["surfy"]==NULL)?"NULL":("'".$entity["surfy"]."'")).", ".(($entity["passengers"]==NULL)?"NULL":("'".$entity["passengers"]."'")).", ".(($entity["ships"]==NULL)?"NULL":("'".$entity["ships"]."'")).", ".(($entity["vehicles"]==NULL)?"NULL":("'".$entity["vehicles"]."'")).", ".(($entity["materials"]==NULL)?"NULL":("'".$entity["materials"]."'")).", '".$entity["timestamp"]."', ".(($entity["activity"]==NULL)?"NULL":("'".$entity["activity"]."'")).")");
				
				mysqli_query($db->connection,"UPDATE data_signalsanalysis SET name = '$name', type = '$entityType', owner = '$ownerName', sector = ".$sector.", galx = '$galx', galy = '$galy', `system` = ".(($system==NULL)?"NULL":("'".$system."'")).", sysx = '$sysx', sysy = '$sysy', planet = ".$planet_ins.", atmox = ".(($atmox==NULL)?"NULL":("'".$atmox."'")).", atmoy = ".(($atmoy==NULL)?"NULL":("'".$atmoy."'")).", surfx = ".(($groundx==NULL)?"NULL":("'".$groundx."'")).", surfy = ".(($groundy==NULL)?"NULL":("'".$groundy."'")).", timestamp = '$activity_time', activity = '$activity_id' WHERE uid = '$entityID'");
				$count_update++;
			}else{ // OLD
				mysqli_query($db->connection,"INSERT INTO data_signalsanalysis_archive (uid, name, type, owner, sector, galx, galy, `system`, sysx, sysy, planet, atmox, atmoy, surfx, surfy, timestamp, activity) VALUES ('$entityID', '$name', '$entityType', '$ownerName', ".$sector.", '$galx', '$galy', ".(($system==NULL)?"NULL":("'".$system."'")).", '$sysx', '$sysy', ".$planet_ins.", ".(($atmox==NULL)?"NULL":("'".$atmox."'")).", ".(($atmoy==NULL)?"NULL":("'".$atmoy."'")).", ".(($groundx==NULL)?"NULL":("'".$groundx."'")).", ".(($groundy==NULL)?"NULL":("'".$groundy."'")).", '$activity_time', '$activity_id')");
				$count_archive++;
			}
		}elseif($upload_db == "buffer"){
			mysqli_query($db->connection,"INSERT INTO data_signalsanalysis_buffer (uid, name, type, owner, sector, galx, galy, `system`, sysx, sysy, planet, atmox, atmoy, surfx, surfy, timestamp, activity) VALUES ('$entityID', '$name', '$entityType', '$ownerName', ".$sector.", '$galx', '$galy', ".(($system==NULL)?"NULL":("'".$system."'")).", '$sysx', '$sysy', ".$planet_ins.", ".(($atmox==NULL)?"NULL":("'".$atmox."'")).", ".(($atmoy==NULL)?"NULL":("'".$atmoy."'")).", ".(($groundx==NULL)?"NULL":("'".$groundx."'")).", ".(($groundy==NULL)?"NULL":("'".$groundy."'")).", '$activity_time', '$activity_id')");
			$count_new++;
		}
	}
	
	if($scope == "space" && !empty($system) && $xml_file->channel->sensorPower > 1){
		if($upload_db == "buffer"){
			mysqli_query($db->connection,"INSERT INTO data_signalsanalysis_scantime_buffer (`system`, timestamp, activity) VALUES ('$system', '$activity_time', '$activity_id')");
		}elseif($upload_db == "live"){
			if($scan_current = mysqli_fetch_assoc(mysqli_query($db->connection,"SELECT id FROM data_signalsanalysis_scantime WHERE `system` = '".$system."'"))){
				if($scan_current["timestamp"] < $activity_time){
					mysqli_query($db->connection,"UPDATE data_signalsanalysis_scantime SET timestamp = '".$activity_time."' WHERE `system` = '".$system."'");
				}
			}else{
				mysqli_query($db->connection,"INSERT INTO data_signalsanalysis_scantime (`system`, timestamp) VALUES ('$system', '$activity_time')");
			}
		}
		
	}
	
	$alert = $alert."<div class=\"alert alert-success\" style=\"font-size:14px;\">".$file = $_FILES['file']['name'][$i]." - ".$count_new." new records, ".$count_update." updated records, and ".$count_archive." archived records were submitted from <a href=\"signalsanalysis.php?activity=".$activity."\" target=\"_blank\">".$activity."</a></div>";
	mysqli_query($db->connection,"UPDATE data_signalsanalysis_activity SET added = '$count_new', updated = '$count_update' WHERE id = '$activity_id'");
	$update_log = mysqli_real_escape_string($db->connection, "UPLOAD: ".$count_new." new records, ".$count_update." updated records, and ".$count_archive." archived records were submitted from <a href=\"../signalsanalysis.php?activity=".$activity."\" target=\"_blank\">".$activity."</a>");
	mysqli_query($db->connection, "INSERT INTO logs_activities (user_id, log_type, details, timestamp) VALUES ('".$_SESSION['user_id']."', '1', '$update_log', '".swc_time(time(),TRUE)["timestamp"]."')");
}else{
	$alert = $alert."<div class=\"alert alert-danger\" style=\"font-size:14px;\">".$file = $_FILES['file']['name'][$i]." - Data already has been ingested from this activity (<a href=\"signalsanalysis.php?activity=".$activity."\">".$activity."</a>)</div>";
}

?>