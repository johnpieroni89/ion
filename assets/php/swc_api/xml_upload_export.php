<?php
if($_SESSION['user_privs']['signalsanalysis_upload'] == 1 && $_SESSION['user_privs']['admin'] == 0){
	$upload_db = "buffer";
	$upload_activity = 0;
}elseif($_SESSION['user_privs']['signalsanalysis_upload'] == 2 || $_SESSION['user_privs']['admin'] > 0){
	$upload_db = "live";
	$upload_activity = 1;
}
//Timeing data
$timestamp = $xml_file{'timestamp'};

//Set activity identifying data
$activity = md5("export".$timestamp);
$check_activity = mysqli_fetch_assoc(mysqli_query($db->connection,"SELECT COUNT(activity_id) AS count FROM data_signalsanalysis_activity WHERE activity_id = '$activity'"))["count"];
if($check_activity == 0){
	mysqli_query($db->connection,"INSERT INTO data_signalsanalysis_activity (activity_id, activity_collector, activity_time, status) VALUES ('$activity', '".$_SESSION['user_id']."', '$timestamp', '$upload_activity')");
	$activity_id = mysqli_fetch_assoc(mysqli_query($db->connection,"SELECT id FROM data_signalsanalysis_activity WHERE activity_id = '$activity'"))["id"];
	
	$count_new = 0;
	$count_update = 0;
	$count_archive = 0;
	$count_corrupt = 0;
	
	foreach($xml_file->entity as $item){
		$uid = mysqli_real_escape_string($db->connection,$item->uid);
		$name = mysqli_real_escape_string($db->connection,$item->name);
		$type_id = mysqli_real_escape_string($db->connection,str_replace("&amp;","&",$item->type_id));
		$owner = mysqli_real_escape_string($db->connection,$item->owner);
		$manager = mysqli_real_escape_string($db->connection,$item->manager);
		$operator = mysqli_real_escape_string($db->connection,$item->operator);
		$sector = mysqli_real_escape_string($db->connection,$item->sector_id);
		$system = mysqli_real_escape_string($db->connection,$item->system_id);
		$planet = mysqli_real_escape_string($db->connection,$item->planet_id);
		$galx = mysqli_real_escape_string($db->connection,$item->galx);
		$galy = mysqli_real_escape_string($db->connection,$item->galy);
		$sysx = mysqli_real_escape_string($db->connection,$item->sysx);
		$sysy = mysqli_real_escape_string($db->connection,$item->sysy);
		$atmox = mysqli_real_escape_string($db->connection,$item->atmox);
		$atmoy = mysqli_real_escape_string($db->connection,$item->atmoy);
		$surfx = mysqli_real_escape_string($db->connection,$item->surfx);
		$surfy = mysqli_real_escape_string($db->connection,$item->surfy);
		$passengers = mysqli_real_escape_string($db->connection,$item->passengers);
		$ships = mysqli_real_escape_string($db->connection,$item->ships);
		$vehicles = mysqli_real_escape_string($db->connection,$item->vehicles);
		$income = mysqli_real_escape_string($db->connection,$item->income);
		$timestamp = mysqli_real_escape_string($db->connection,$item->timestamp);
		$custom_image = mysqli_real_escape_string($db->connection,$item->custom_image);
		$custom_timestamp = mysqli_real_escape_string($db->connection,$item->custom_timestamp);
	
		if($custom_image != "")	{
			$custom = mysqli_fetch_assoc(mysqli_query($db->connection,"SELECT COUNT(uid) as count, timestamp FROM data_signalsanalysis_customs WHERE uid = '$uid'"));
			if($custom["count"] == 0){
				mysqli_query($db->connection,"INSERT INTO data_signalsanalysis_customs (uid, custom_image, timestamp) VALUES ('$uid', '$custom_image', '$custom_timestamp')");
			}elseif($custom["timestamp"] < $custom_timestamp){
				mysqli_query($db->connection,"UPDATE data_signalsanalysis_customs SET custom_image = '$custom_image', timestamp = '$custom_timestamp'  WHERE uid = '$uid'");
			}
		}

		if($upload_db == "live"){
			//Current or historical data
			$entity = mysqli_fetch_assoc(mysqli_query($db->connection,"SELECT COUNT(uid) as count, data_signalsanalysis.* FROM data_signalsanalysis WHERE uid = '$uid'"));
			if($entity["count"] == 0){ // INSERT
				mysqli_query($db->connection,"INSERT INTO data_signalsanalysis (uid, name, type_id, owner, manager, operator, sector, galx, galy, `system`, sysx, sysy, planet, atmox, atmoy, surfx, surfy, passengers, ships, vehicles, income, timestamp, activity) VALUES ('$uid', '$name', '$type_id', '$owner', ".(($manager=="")?"NULL":("'".$manager."'")).", ".(($operator=="")?"NULL":("'".$operator."'")).", ".(($sector=="")?"NULL":("'".$sector."'")).", '$galx', '$galy', ".(($system=="")?"NULL":("'".$system."'")).", '$sysx', '$sysy', ".(($planet=="")?"NULL":("'".$planet."'")).", ".(($atmox=="")?"NULL":("'".$atmox."'")).", ".(($atmoy=="")?"NULL":("'".$atmoy."'")).", ".(($surfx=="")?"NULL":("'".$surfx."'")).", ".(($surfy=="")?"NULL":("'".$surfy."'")).", ".(($passengers=="")?"NULL":("'".$passengers."'")).", ".(($ships=="")?"NULL":("'".$ships."'")).", ".(($vehicles=="")?"NULL":("'".$vehicles."'")).", ".(($income=="")?"NULL":("'".$income."'")).", '$timestamp', '$activity_id')");
				$count_new++;
			}elseif($entity["timestamp"] < $timestamp){ // UPDATE
				mysqli_query($db->connection,"INSERT INTO data_signalsanalysis_archive (uid, name, type_id, owner, manager, operator, sector, galx, galy, `system`, sysx, sysy, planet, atmox, atmoy, surfx, surfy, passengers, ships, vehicles, income, timestamp, activity) VALUES ('".$entity["uid"]."', '".$entity["name"]."', '".$entity["type_id"]."', '".$entity["owner"]."', ".(($entity["manager"]==NULL)?"NULL":("'".$entity["manager"]."'")).", ".(($entity["operator"]==NULL)?"NULL":("'".$entity["operator"]."'")).", ".(($entity["sector"]==NULL)?"NULL":("'".$entity["sector"]."'")).", '".$entity["galx"]."', '".$entity["galy"]."', ".(($entity["system"]==NULL)?"NULL":("'".$entity["system"]."'")).", '".$entity["sysx"]."', '".$entity["sysy"]."', ".(($entity["planet"]==NULL)?"NULL":("'".$entity["planet"]."'")).", ".(($entity["atmox"]==NULL)?"NULL":("'".$entity["atmox"]."'")).", ".(($entity["atmoy"]==NULL)?"NULL":("'".$entity["atmoy"]."'")).", ".(($entity["surfx"]==NULL)?"NULL":("'".$entity["surfx"]."'")).", ".(($entity["surfy"]==NULL)?"NULL":("'".$entity["surfy"]."'")).", ".(($entity["passengers"]==NULL)?"NULL":("'".$entity["passengers"]."'")).", ".(($entity["ships"]==NULL)?"NULL":("'".$entity["ships"]."'")).", ".(($entity["vehicles"]==NULL)?"NULL":("'".$entity["vehicles"]."'")).", ".(($entity["income"]=="")?"NULL":("'".$entity["income"]."'")).", '".$entity["timestamp"]."', ".(($entity["activity"]==NULL)?"NULL":("'".$entity["activity"]."'")).")");
				$archives = mysqli_fetch_assoc(mysqli_query($db->connection,"SELECT COUNT(uid) as count FROM data_signalsanalysis_archive WHERE uid = '$uid'"))["count"];
				if($archives > 10){mysqli_query($db->connection,"DELETE FROM data_signalsanalysis_archive WHERE uid = '$uid' ORDER BY timestamp DESC LIMIT 1");}
				mysqli_query($db->connection,"DELETE FROM data_signalsanalysis WHERE uid = '$uid'");
				mysqli_query($db->connection,"INSERT INTO data_signalsanalysis (uid, name, type_id, owner, manager, operator, sector, galx, galy, `system`, sysx, sysy, planet, atmox, atmoy, surfx, surfy, passengers, ships, vehicles, income, timestamp, activity) VALUES ('$uid', '$name', '$type_id', '$owner', ".(($manager=="")?"NULL":("'".$manager."'")).", ".(($operator=="")?"NULL":("'".$operator."'")).", ".(($sector=="")?"NULL":("'".$sector."'")).", '$galx', '$galy', ".(($system=="")?"NULL":("'".$system."'")).", '$sysx', '$sysy', ".(($planet=="")?"NULL":("'".$planet."'")).", ".(($atmox=="")?"NULL":("'".$atmox."'")).", ".(($atmoy=="")?"NULL":("'".$atmoy."'")).", ".(($surfx=="")?"NULL":("'".$surfx."'")).", ".(($surfy=="")?"NULL":("'".$surfy."'")).", ".(($passengers=="")?"NULL":("'".$passengers."'")).", ".(($ships=="")?"NULL":("'".$ships."'")).", ".(($vehicles=="")?"NULL":("'".$vehicles."'")).", ".(($income=="")?"NULL":("'".$income."'")).", '$timestamp', '$activity_id')");
				$count_update++;
			}else{ // OLD
				mysqli_query($db->connection,"INSERT INTO data_signalsanalysis_archive (uid, name, type_id, owner, manager, operator, sector, galx, galy, `system`, sysx, sysy, planet, atmox, atmoy, surfx, surfy, passengers, ships, vehicles, income, timestamp, activity) VALUES ('$uid', '$name', '$type_id', '$owner', ".(($manager=="")?"NULL":("'".$manager."'")).", ".(($operator=="")?"NULL":("'".$operator."'")).", ".(($sector=="")?"NULL":("'".$sector."'")).", '$galx', '$galy', ".(($system=="")?"NULL":("'".$system."'")).", '$sysx', '$sysy', ".(($planet=="")?"NULL":("'".$planet."'")).", ".(($atmox=="")?"NULL":("'".$atmox."'")).", ".(($atmoy=="")?"NULL":("'".$atmoy."'")).", ".(($surfx=="")?"NULL":("'".$surfx."'")).", ".(($surfy=="")?"NULL":("'".$surfy."'")).", ".(($passengers=="")?"NULL":("'".$passengers."'")).", ".(($ships=="")?"NULL":("'".$ships."'")).", ".(($vehicles=="")?"NULL":("'".$vehicles."'")).", ".(($income=="")?"NULL":("'".$income."'")).", '$timestamp', '$activity_id')");
				$archives = mysqli_fetch_assoc(mysqli_query($db->connection,"SELECT COUNT(uid) as count FROM data_signalsanalysis_archive WHERE uid = '$uid'"))["count"];
				if($archives > 10){mysqli_query($db->connection,"DELETE FROM data_signalsanalysis_archive WHERE uid = '$uid' ORDER BY timestamp DESC LIMIT 1");}
				$count_archive++;
			}
		}elseif($upload_db == "buffer"){
			mysqli_query($db->connection,"INSERT INTO data_signalsanalysis_buffer (uid, name, type_id, owner, manager, operator, sector, galx, galy, `system`, sysx, sysy, planet, atmox, atmoy, surfx, surfy, passengers, ships, vehicles, income, timestamp, activity) VALUES ('$uid', '$name', '$type_id', '$owner', ".(($manager=="")?"NULL":("'".$manager."'")).", ".(($operator=="")?"NULL":("'".$operator."'")).", ".(($sector=="")?"NULL":("'".$sector."'")).", '$galx', '$galy', ".(($system=="")?"NULL":("'".$system."'")).", '$sysx', '$sysy', ".(($planet=="")?"NULL":("'".$planet."'")).", ".(($atmox=="")?"NULL":("'".$atmox."'")).", ".(($atmoy=="")?"NULL":("'".$atmoy."'")).", ".(($surfx=="")?"NULL":("'".$surfx."'")).", ".(($surfy=="")?"NULL":("'".$surfy."'")).", ".(($passengers=="")?"NULL":("'".$passengers."'")).", ".(($ships=="")?"NULL":("'".$ships."'")).", ".(($vehicles=="")?"NULL":("'".$vehicles."'")).", ".(($income=="")?"NULL":("'".$income."'")).", '$timestamp', '$activity_id')");
			$count_new++;
		}
	}
	$session->alert = $session->alert."<div class=\"alert alert-success\" style=\"font-size:14px;\">".$file = $_FILES['file']['name'][$i]." - ".$count_new." entities have been added, ".$count_update." entities have been updated, ".$count_archive." entities have been archived, and ".$count_corrupt." entities are corrupted and weren't ingested!</div>";
	mysqli_query($db->connection,"UPDATE data_signalsanalysis_activity SET added = '$count_new', updated = '$count_update' WHERE id = '$activity_id'");
	$update_log = mysqli_real_escape_string($db->connection, "UPLOAD: ".$count_new." new records, ".$count_update." updated records, and ".$count_archive." records were submitted from <a href=\"../signalsanalysis.php?activity=".$activity."\" target=\"_blank\">".$activity."</a>");
	mysqli_query($db->connection, "INSERT INTO logs_activities (user_id, log_type_id, details, timestamp) VALUES ('".$_SESSION['user_id']."', '1', '$update_log', '".swc_time(time(),TRUE)["timestamp"]."')");
}else{
	$session->alert = $session->alert."<div class=\"alert alert-danger\" style=\"font-size:14px;\">".$file = $_FILES['file']['name'][$i]." - Data already has been ingested from this activity (<a href=\"signalsanalysis.php?activity=".$activity."\">".$activity."</a>)</div>";
}

?>