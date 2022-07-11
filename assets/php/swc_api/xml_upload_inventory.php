<?php

global $db;
global $session;

if($_SESSION['user_privs']['signalsanalysis_upload'] == 1 && $_SESSION['user_privs']['admin'] == 0){
	$upload_db = "buffer";
	$upload_activity = 0;
}elseif($_SESSION['user_privs']['signalsanalysis_upload'] == 2 || $_SESSION['user_privs']['admin'] > 0){
	$upload_db = "live";
	$upload_activity = 1;
}

//Timeing data
$timestamp = swc_time($xml_file{'unixtime'},TRUE)["timestamp"];

//Set activity identifying data
$activity = md5("inventory".$timestamp);
$check_activity = mysqli_fetch_assoc(mysqli_query($db->connection,"SELECT COUNT(activity_id) AS count FROM data_signalsanalysis_activity WHERE activity_id = '$activity'"))["count"];
if($check_activity == 0){
	mysqli_query($db->connection,"INSERT INTO data_signalsanalysis_activity (activity_id, activity_collector, activity_time, status) VALUES ('$activity', '".$_SESSION['user_id']."', '$timestamp', '$upload_activity')");
	$activity_id = mysqli_fetch_assoc(mysqli_query($db->connection,"SELECT id FROM data_signalsanalysis_activity WHERE activity_id = '$activity'"))["id"];
	
	$count_new = 0;
	$count_update = 0;
	$count_archive = 0;
	$count_corrupt = 0;
	
	foreach($xml_file->ENTITY as $item){
		//Location data
		$sector = mysqli_real_escape_string($db->connection,$item->SECTOR_UID);
		$system = mysqli_real_escape_string($db->connection,$item->SYSTEM_UID);
		$planet = mysqli_real_escape_string($db->connection,$item->PLANET_UID);
		$galx = mysqli_real_escape_string($db->connection,$item->GALX);
		$galy = mysqli_real_escape_string($db->connection,$item->GALY);
		$sysx = mysqli_real_escape_string($db->connection,$item->SYSX);
		$sysy = mysqli_real_escape_string($db->connection,$item->SYSY);
		$atmox = mysqli_real_escape_string($db->connection,$item->SURFX);
		$atmoy = mysqli_real_escape_string($db->connection,$item->SURFY);
		$groundx = mysqli_real_escape_string($db->connection,$item->GROUNDX);
		$groundy = mysqli_real_escape_string($db->connection,$item->GROUNDY);
	
		//Strip the entity data
		$name = mysqli_real_escape_string($db->connection,$item->NAME);
		$entityType = mysqli_real_escape_string($db->connection,str_replace("&amp;","&",$item->TYPE_UID));
		$entityID = mysqli_real_escape_string($db->connection,$item->UID);
		$owner = mysqli_real_escape_string($db->connection,$item->OWNER_NAME);
		$commander = mysqli_real_escape_string($db->connection,$item->COMMANDER_NAME);
		$pilot = mysqli_real_escape_string($db->connection,$item->PILOT_NAME);
		$passengers = mysqli_real_escape_string($db->connection,$item->PASSENGERSTOTAL - $item->PASSENGERSREMAINING);
		$passengers = mysqli_real_escape_string($db->connection,$item->PASSENGERSTOTAL - $item->PASSENGERSREMAINING);
		$income = mysqli_real_escape_string($db->connection,$item->AMOUNTEARNED);

		if($upload_db == "live"){
			//Current or historical data
			$entity = mysqli_fetch_assoc(mysqli_query($db->connection,"SELECT COUNT(uid) as count, data_signalsanalysis.* FROM data_signalsanalysis WHERE uid = '$entityID'"));
			if($entityType == "" || $galx == "" || (explode(":",$entityType)[0] != 2 && explode(":",$entityType)[0] != 3 && explode(":",$entityType)[0] != 4 && explode(":",$entityType)[0] != 5)){
				$count_corrupt++;
			}elseif($entity["count"] == 0){ // INSERT
				mysqli_query($db->connection,"INSERT INTO data_signalsanalysis (uid, name, type, owner, manager, operator, sector, galx, galy, `system`, sysx, sysy, planet, atmox, atmoy, surfx, surfy, passengers, income, timestamp, activity) VALUES ('$entityID', '$name', '$entityType', '$owner', ".(($commander=="")?"NULL":("'".$commander."'")).", ".(($pilot=="")?"NULL":("'".$pilot."'")).", ".(($sector=="")?"NULL":("'".$sector."'")).", '$galx', '$galy', ".(($system=="")?"NULL":("'".$system."'")).", '$sysx', '$sysy', ".(($planet=="")?"NULL":("'".$planet."'")).", ".(($atmox=="")?"NULL":("'".$atmox."'")).", ".(($atmoy=="")?"NULL":("'".$atmoy."'")).", ".(($groundx=="")?"NULL":("'".$groundx."'")).", ".(($groundy=="")?"NULL":("'".$groundy."'")).", ".(($passengers==0)?"NULL":("'".$passengers."'")).", ".(($income=="")?"NULL":("'".$income."'")).", '$timestamp', '$activity_id')");
				$count_new++;
			}elseif($entity["timestamp"] < $timestamp){ // UPDATE
				mysqli_query($db->connection,"INSERT INTO data_signalsanalysis_archive (uid, name, type, owner, manager, operator, sector, galx, galy, `system`, sysx, sysy, planet, atmox, atmoy, surfx, surfy, passengers, ships, vehicles, income, timestamp, activity) VALUES ('".$entity["uid"]."', '".$entity["name"]."', '".$entity["type"]."', '".$entity["owner"]."', ".(($entity["manager"]==NULL)?"NULL":("'".$entity["manager"]."'")).", ".(($entity["operator"]==NULL)?"NULL":("'".$entity["operator"]."'")).", ".(($entity["sector"]==NULL)?"NULL":("'".$entity["sector"]."'")).", '".$entity["galx"]."', '".$entity["galy"]."', ".(($entity["system"]==NULL)?"NULL":("'".$entity["system"]."'")).", '".$entity["sysx"]."', '".$entity["sysy"]."', ".(($entity["planet"]==NULL)?"NULL":("'".$entity["planet"]."'")).", ".(($entity["atmox"]==NULL)?"NULL":("'".$entity["atmox"]."'")).", ".(($entity["atmoy"]==NULL)?"NULL":("'".$entity["atmoy"]."'")).", ".(($entity["surfx"]==NULL)?"NULL":("'".$entity["surfx"]."'")).", ".(($entity["surfy"]==NULL)?"NULL":("'".$entity["surfy"]."'")).", ".(($entity["passengers"]==NULL)?"NULL":("'".$entity["passengers"]."'")).", ".(($entity["ships"]==NULL)?"NULL":("'".$entity["ships"]."'")).", ".(($entity["vehicles"]==NULL)?"NULL":("'".$entity["vehicles"]."'")).", ".(($entity["income"]=="")?"NULL":("'".$entity["income"]."'")).", '".$entity["timestamp"]."', ".(($entity["activity"]==NULL)?"NULL":("'".$entity["activity"]."'")).")");
				$archives = mysqli_fetch_assoc(mysqli_query($db->connection,"SELECT COUNT(uid) as count FROM data_signalsanalysis_archive WHERE uid = '$entityID'"))["count"];
				if($archives > 10){mysqli_query($db->connection,"DELETE FROM data_signalsanalysis_archive WHERE uid = '$entityID' ORDER BY timestamp DESC LIMIT 1");}
				mysqli_query($db->connection,"DELETE FROM data_signalsanalysis WHERE uid = '$entityID'");
				mysqli_query($db->connection,"INSERT INTO data_signalsanalysis (uid, name, type, owner, manager, operator, sector, galx, galy, `system`, sysx, sysy, planet, atmox, atmoy, surfx, surfy, passengers, income, timestamp, activity) VALUES ('$entityID', '$name', '$entityType', '$owner', ".(($commander=="")?"NULL":("'".$commander."'")).", ".(($pilot=="")?"NULL":("'".$pilot."'")).", ".(($sector=="")?"NULL":("'".$sector."'")).", '$galx', '$galy', ".(($system=="")?"NULL":("'".$system."'")).", '$sysx', '$sysy', ".(($planet=="")?"NULL":("'".$planet."'")).", ".(($atmox=="")?"NULL":("'".$atmox."'")).", ".(($atmoy=="")?"NULL":("'".$atmoy."'")).", ".(($groundx=="")?"NULL":("'".$groundx."'")).", ".(($groundy=="")?"NULL":("'".$groundy."'")).", ".(($passengers==0)?"NULL":("'".$passengers."'")).", ".(($income=="")?"NULL":("'".$income."'")).", '$timestamp', '$activity_id')");
				$count_update++;
			}else{ // OLD
				mysqli_query($db->connection,"INSERT INTO data_signalsanalysis_archive (uid, name, type, owner, manager, operator, sector, galx, galy, `system`, sysx, sysy, planet, atmox, atmoy, surfx, surfy, passengers, income, timestamp, activity) VALUES ('$entityID', '$name', '$entityType', '$owner', ".(($commander=="")?"NULL":("'".$commander."'")).", ".(($pilot=="")?"NULL":("'".$pilot."'")).", ".(($sector=="")?"NULL":("'".$sector."'")).", '$galx', '$galy', ".(($system=="")?"NULL":("'".$system."'")).", '$sysx', '$sysy', ".(($planet=="")?"NULL":("'".$planet."'")).", ".(($atmox=="")?"NULL":("'".$atmox."'")).", ".(($atmoy=="")?"NULL":("'".$atmoy."'")).", ".(($groundx=="")?"NULL":("'".$groundx."'")).", ".(($groundy=="")?"NULL":("'".$groundy."'")).", ".(($passengers==0)?"NULL":("'".$passengers."'")).", ".(($income=="")?"NULL":("'".$income."'")).", '$timestamp', '$activity_id')");
				$archives = mysqli_fetch_assoc(mysqli_query($db->connection,"SELECT COUNT(uid) as count FROM data_signalsanalysis_archive WHERE uid = '$entityID'"))["count"];
				if($archives > 10){mysqli_query($db->connection,"DELETE FROM data_signalsanalysis_archive WHERE uid = '$entityID' ORDER BY timestamp DESC LIMIT 1");}
				$count_archive++;
			}
		}elseif($upload_db == "buffer"){
			mysqli_query($db->connection,"INSERT INTO data_signalsanalysis_buffer (uid, name, type, owner, manager, operator, sector, galx, galy, `system`, sysx, sysy, planet, atmox, atmoy, surfx, surfy, passengers, income, timestamp, activity) VALUES ('$entityID', '$name', '$entityType', '$owner', ".(($commander=="")?"NULL":("'".$commander."'")).", ".(($pilot=="")?"NULL":("'".$pilot."'")).", ".(($sector=="")?"NULL":("'".$sector."'")).", '$galx', '$galy', ".(($system=="")?"NULL":("'".$system."'")).", '$sysx', '$sysy', ".(($planet=="")?"NULL":("'".$planet."'")).", ".(($atmox=="")?"NULL":("'".$atmox."'")).", ".(($atmoy=="")?"NULL":("'".$atmoy."'")).", ".(($groundx=="")?"NULL":("'".$groundx."'")).", ".(($groundy=="")?"NULL":("'".$groundy."'")).", ".(($passengers==0)?"NULL":("'".$passengers."'")).", ".(($income=="")?"NULL":("'".$income."'")).", '$timestamp', '$activity_id')");
			$count_new++;
		}
	}
	$session->alert = $session->alert."<div class=\"alert alert-success\" style=\"font-size:14px;\">".$file = $_FILES['file']['name'][$i]." - ".$count_new." entities have been added, ".$count_update." entities have been updated, ".$count_archive." entities have been archived, and ".$count_corrupt." entities are corrupted and weren't ingested!</div>";
	mysqli_query($db->connection,"UPDATE data_signalsanalysis_activity SET added = '$count_new', updated = '$count_update' WHERE id = '$activity_id'");
	$update_log = mysqli_real_escape_string($db->connection, "UPLOAD: ".$count_new." new records, ".$count_update." updated records, and ".$count_archive." records were submitted from <a href=\"../signalsanalysis.php?activity=".$activity."\" target=\"_blank\">".$activity."</a>");
	mysqli_query($db->connection, "INSERT INTO logs_activities (user_id, log_type, details, timestamp) VALUES ('".$_SESSION['user_id']."', '1', '$update_log', '".swc_time(time(),TRUE)["timestamp"]."')");
}else{
	$session->alert = $session->alert."<div class=\"alert alert-danger\" style=\"font-size:14px;\">".$file = $_FILES['file']['name'][$i]." - Data already has been ingested from this activity (<a href=\"signalsanalysis.php?activity=".$activity."\">".$activity."</a>)</div>";
}

?>