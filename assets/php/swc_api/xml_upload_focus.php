<?php
if($_SESSION['user_privs']['signalsanalysis_upload'] == 1 && $_SESSION['user_privs']['admin'] == 0){
	$upload_db = "buffer";
	$upload_activity = 0;
}elseif($_SESSION['user_privs']['signalsanalysis_upload'] == 2 || $_SESSION['user_privs']['admin'] > 0){
	$upload_db = "live";
	$upload_activity = 1;
}
//Timeing data
$year = $xml_file->channel->cgt->years;
$day = $xml_file->channel->cgt->days;
$hour = $xml_file->channel->cgt->hours;
$minute = $xml_file->channel->cgt->minutes;
$second = $xml_file->channel->cgt->seconds;

//Set activity identifying data
$activity = md5($year.$day.$hour.$minute.$second.$xml_file->channel->cgt->{'seconds'}.$galx.$galy.$file);
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
	if($system == ""){$system = NULL;}
	$sector = $query_galaxy['sector'];
	if($sector == ""){$sector = NULL;}
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
	}

	//Strip the entity data
	$name = mysqli_real_escape_string($db->connection,$xml_file->channel->item->name);
	$typeName = mysqli_real_escape_string($db->connection,str_replace("&amp;","&",$xml_file->channel->item->typeName));
	$entityType = mysqli_fetch_assoc(mysqli_query($db->connection,"SELECT uid FROM entities WHERE name = '$typeName'"))["uid"];
	$entityID = explode(":",$entityType)[0].":".mysqli_real_escape_string($db->connection,$xml_file->channel->item->entityID);
	$travelDirection = mysqli_real_escape_string($db->connection,$xml_file->channel->item->travelDirection);
	$ownerName = mysqli_real_escape_string($db->connection,$xml_file->channel->item->ownerName);
		//Custom image
		if(strpos($xml_file->channel->item->image,"img.swcombine.com") || strpos($xml_file->channel->item->image,"custom.swcombine.com")){
			$image = mysqli_real_escape_string($db->connection,$xml_file->channel->item->image);
			mysqli_query($db->connection,"INSERT INTO data_signalsanalysis_customs (uid, custom_image, timestamp) VALUES ('$entityID', '$image', '$activity_time') ON DUPLICATE KEY UPDATE custom_image = '$image', timestamp = '$activity_time'");
		}
		
		//Cargo
		$passengers = mysqli_real_escape_string($db->connection,$xml_file->channel->item->cargo->passengers);
		$ships = mysqli_real_escape_string($db->connection,$xml_file->channel->item->cargo->ships);
		$vehicles = mysqli_real_escape_string($db->connection,$xml_file->channel->item->cargo->vehicles);

	//Figure out where generic x/y goes
	if(isset($xml_file->channel->location->groundX)){ //Ground
		$groundx = mysqli_real_escape_string($db->connection,$xml_file->channel->item->x);
		$groundy = mysqli_real_escape_string($db->connection,$xml_file->channel->item->y);
	}elseif(isset($xml_file->channel->location->surfX)){ //Atmosphere
		$atmox = mysqli_real_escape_string($db->connection,$xml_file->channel->item->x);
		$atmoy = mysqli_real_escape_string($db->connection,$xml_file->channel->item->y);
	}else{ //Space
		$sysx = mysqli_real_escape_string($db->connection,$xml_file->channel->item->x);
		$sysy = mysqli_real_escape_string($db->connection,$xml_file->channel->item->y);
	}
	
	$query_planet = mysqli_fetch_assoc(mysqli_query($db->connection,"SELECT uid FROM galaxy_planets WHERE galx = '".$galx."' AND galy = '".$galy."' AND sysx = '".$sysx."' AND sysy = '".$sysy."'"));
	if($query_planet['uid'] != ""){
		$planet_ins = $query_planet['uid'];
	}else{
		$planet_ins = NULL;
	}
	
	//Geolocation
	if($item->travelDirection != "Stationary" && $item->travelDirection != ""){
		mysqli_query($db->connection, "INSERT INTO data_tracking (target, entity, confidence, `source`, timestamp, sector, galx, galy, `system`, sysx, sysy, planet, atmox, atmoy, surfx, surfy) VALUES ('$ownerName', '$entityID', '2', '$activity', '$activity_time', ".(($sector==NULL)?"NULL":("'".$sector."'")).", '$galx', '$galy', ".(($system==NULL)?"NULL":("'".$system."'")).", '$sysx', '$sysy', ".(($planet_ins==NULL)?"NULL":("'".$planet_ins."'")).", ".(($atmox==NULL)?"NULL":("'".$atmox."'")).", ".(($atmoy==NULL)?"NULL":("'".$atmoy."'")).", ".(($groundx==NULL)?"NULL":("'".$groundx."'")).", ".(($groundy==NULL)?"NULL":("'".$groundy."'")).")");
	}
	
	if($upload_db == "live"){
		//Current or historical data
		$entity = mysqli_fetch_assoc(mysqli_query($db->connection,"SELECT COUNT(uid) as count, data_signalsanalysis.* FROM data_signalsanalysis WHERE uid = '$entityID'"));
		if($entityType == ""){
			if(empty($item->isWorking) && !empty($item->ownerName) && $item->ownerName == $item->name && !empty($item->raceName)){
				add_character($item->name);
				mysqli_query($db->connection, "INSERT INTO data_tracking (target, confidence, `source`, timestamp, sector, galx, galy, `system`, sysx, sysy, planet, atmox, atmoy, surfx, surfy) VALUES ('$ownerName', '3', '$activity', '$activity_time', ".(($sector==NULL)?"NULL":("'".$sector."'")).", '$galx', '$galy', ".(($system==NULL)?"NULL":("'".$system."'")).", '$sysx', '$sysy', ".(($planet_ins==NULL)?"NULL":("'".$planet_ins."'")).", ".(($atmox==NULL)?"NULL":("'".$atmox."'")).", ".(($atmoy==NULL)?"NULL":("'".$atmoy."'")).", ".(($groundx==NULL)?"NULL":("'".$groundx."'")).", ".(($groundy==NULL)?"NULL":("'".$groundy."'")).")");
			}else{
				$alert = $alert."<div class=\"alert alert-danger\" style=\"font-size:14px;\">".$file = $_FILES['file']['name'][$i]." - 1 entity is corrupted and was not ingested!</div>";
			}
		}elseif($entity["count"] == 0){ // INSERT
			mysqli_query($db->connection,"INSERT INTO data_signalsanalysis (uid, name, type, owner, sector, galx, galy, `system`, sysx, sysy, planet, atmox, atmoy, surfx, surfy, passengers, ships, vehicles, timestamp, activity) VALUES ('$entityID', '$name', '$entityType', '$ownerName', ".(($sector==NULL)?"NULL":("'".$sector."'")).", '$galx', '$galy', ".(($system==NULL)?"NULL":("'".$system."'")).", '$sysx', '$sysy', ".(($planet_ins==NULL)?"NULL":("'".$planet_ins."'")).", ".(($atmox==NULL)?"NULL":("'".$atmox."'")).", ".(($atmoy==NULL)?"NULL":("'".$atmoy."'")).", ".(($groundx==NULL)?"NULL":("'".$groundx."'")).", ".(($groundy==NULL)?"NULL":("'".$groundy."'")).", '$passengers', '$ships', '$vehicles', '$activity_time', '$activity_id')");
			$alert = $alert."<div class=\"alert alert-success\" style=\"font-size:14px;\">".$file = $_FILES['file']['name'][$i]." - 1 entity has been added from <a href=\"../signalsanalysis.php?activity=".$activity."\" target=\"_blank\">".$activity."</a>!</div>";
			mysqli_query($db->connection,"UPDATE data_signalsanalysis_activity SET added = '1' WHERE id = '$activity_id'");
		}elseif($entity["timestamp"] < $activity_time){ // UPDATE
			mysqli_query($db->connection,"INSERT INTO data_signalsanalysis_archive (uid, name, type, owner, manager, operator, sector, galx, galy, `system`, sysx, sysy, planet, atmox, atmoy, surfx, surfy, passengers, ships, vehicles, timestamp, activity) VALUES ('".$entity["uid"]."', '".$entity["name"]."', '".$entity["type"]."', '".$entity["owner"]."', ".(($entity["manager"]==NULL)?"NULL":("'".$entity["manager"]."'")).", ".(($entity["operator"]==NULL)?"NULL":("'".$entity["operator"]."'")).", ".(($entity["sector"]==NULL)?"NULL":("'".$entity["sector"]."'")).", '".$entity["galx"]."', '".$entity["galy"]."', ".(($entity["system"]==NULL)?"NULL":("'".$entity["system"]."'")).", '".$entity["sysx"]."', '".$entity["sysy"]."', ".(($entity["planet"]==NULL)?"NULL":("'".$entity["planet"]."'")).", ".(($entity["atmox"]==NULL)?"NULL":("'".$entity["atmox"]."'")).", ".(($entity["atmoy"]==NULL)?"NULL":("'".$entity["atmoy"]."'")).", ".(($entity["surfx"]==NULL)?"NULL":("'".$entity["surfx"]."'")).", ".(($entity["surfy"]==NULL)?"NULL":("'".$entity["surfy"]."'")).", ".(($entity["passengers"]==NULL)?"NULL":("'".$entity["passengers"]."'")).", ".(($entity["ships"]==NULL)?"NULL":("'".$entity["ships"]."'")).", ".(($entity["vehicles"]==NULL)?"NULL":("'".$entity["vehicles"]."'")).", '".$entity["timestamp"]."', ".(($entity["activity"]==NULL)?"NULL":("'".$entity["activity"]."'")).")");
			$archives = mysqli_fetch_assoc(mysqli_query($db->connection,"SELECT COUNT(uid) as count FROM data_signalsanalysis_archive WHERE uid = '$entityID'"))["count"];
			if($archives > 10){mysqli_query($db->connection,"DELETE FROM data_signalsanalysis_archive WHERE uid = '$entityID' ORDER BY timestamp DESC LIMIT 1");}
			mysqli_query($db->connection,"DELETE FROM data_signalsanalysis WHERE uid = '$entityID'");
			mysqli_query($db->connection,"INSERT INTO data_signalsanalysis (uid, name, type, owner, sector, galx, galy, `system`, sysx, sysy, planet, atmox, atmoy, surfx, surfy, passengers, ships, vehicles, timestamp, activity) VALUES ('$entityID', '$name', '$entityType', '$ownerName', ".(($sector==NULL)?"NULL":("'".$sector."'")).", '$galx', '$galy', ".(($system==NULL)?"NULL":("'".$system."'")).", '$sysx', '$sysy', ".(($planet_ins==NULL)?"NULL":("'".$planet_ins."'")).", ".(($atmox==NULL)?"NULL":("'".$atmox."'")).", ".(($atmoy==NULL)?"NULL":("'".$atmoy."'")).", ".(($groundx==NULL)?"NULL":("'".$groundx."'")).", ".(($groundy==NULL)?"NULL":("'".$groundy."'")).", '$passengers', '$ships', '$vehicles', '$activity_time', '$activity_id')");
			$alert = $alert."<div class=\"alert alert-success\" style=\"font-size:14px;\">".$file = $_FILES['file']['name'][$i]." - 1 entity has been updated from <a href=\"../signalsanalysis.php?activity=".$activity."\" target=\"_blank\">".$activity."</a>!</div>";
			mysqli_query($db->connection,"UPDATE data_signalsanalysis_activity SET updated = '1' WHERE id = '$activity_id'");
		}else{ // OLD
			mysqli_query($db->connection,"INSERT INTO data_signalsanalysis_archive (uid, name, type, owner, sector, galx, galy, `system`, sysx, sysy, planet, atmox, atmoy, surfx, surfy, passengers, ships, vehicles, timestamp, activity) VALUES ('$entityID', '$name', '$entityType', '$ownerName', ".(($sector==NULL)?"NULL":("'".$sector."'")).", '$galx', '$galy', ".(($system==NULL)?"NULL":("'".$system."'")).", '$sysx', '$sysy', ".(($planet_ins==NULL)?"NULL":("'".$planet_ins."'")).", ".(($atmox==NULL)?"NULL":("'".$atmox."'")).", ".(($atmoy==NULL)?"NULL":("'".$atmoy."'")).", ".(($groundx==NULL)?"NULL":("'".$groundx."'")).", ".(($groundy==NULL)?"NULL":("'".$groundy."'")).", '$passengers', '$ships', '$vehicles', '$activity_time', '$activity_id')");
			$archives = mysqli_fetch_assoc(mysqli_query($db->connection,"SELECT COUNT(uid) as count FROM data_signalsanalysis_archive WHERE uid = '$entityID'"))["count"];
			if($archives > 10){mysqli_query($db->connection,"DELETE FROM data_signalsanalysis_archive WHERE uid = '$entityID' ORDER BY timestamp DESC LIMIT 1");}
			$alert = $alert."<div class=\"alert alert-success\" style=\"font-size:14px;\">".$file = $_FILES['file']['name'][$i]." - 1 entity has been archived from <a href=\"../signalsanalysis.php?activity=".$activity."\" target=\"_blank\">".$activity."</a></div>";
		}
	}elseif($upload_db == "buffer"){
		mysqli_query($db->connection,"INSERT INTO data_signalsanalysis_buffer (uid, name, type, owner, sector, galx, galy, `system`, sysx, sysy, planet, atmox, atmoy, surfx, surfy, passengers, ships, vehicles, timestamp, activity) VALUES ('$entityID', '$name', '$entityType', '$ownerName', ".(($sector==NULL)?"NULL":("'".$sector."'")).", '$galx', '$galy', ".(($system==NULL)?"NULL":("'".$system."'")).", '$sysx', '$sysy', ".(($planet_ins==NULL)?"NULL":("'".$planet_ins."'")).", ".(($atmox==NULL)?"NULL":("'".$atmox."'")).", ".(($atmoy==NULL)?"NULL":("'".$atmoy."'")).", ".(($groundx==NULL)?"NULL":("'".$groundx."'")).", ".(($groundy==NULL)?"NULL":("'".$groundy."'")).", '$passengers', '$ships', '$vehicles', '$activity_time', '$activity_id')");
		$count_new++;
	}
	$update_log = mysqli_real_escape_string($db->connection, "UPLOAD: 1 record was submitted from <a href=\"../signalsanalysis.php?activity=".$activity."\" target=\"_blank\">".$activity."</a>");
	mysqli_query($db->connection, "INSERT INTO logs_activities (user_id, log_type, details, timestamp) VALUES ('".$_SESSION['user_id']."', '1', '$update_log', '".swc_time(time(),TRUE)["timestamp"]."')");
}else{
	$alert = $alert."<div class=\"alert alert-danger\" style=\"font-size:14px;\">".$file = $_FILES['file']['name'][$i]." - Data already has been ingested from this activity (<a href=\"signalsanalysis.php?activity=".$activity."\">".$activity."</a>)</div>";
}

?>