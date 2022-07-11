<?php
//Set activity identifying data
$activity = mysqli_real_escape_string($db->connection,$_POST["activity_id"]);
$query_activity = mysqli_fetch_assoc(mysqli_query($db->connection,"SELECT id, activity_id AS count FROM data_signalsanalysis_activity WHERE activity_id = '$activity'"));
$activity_id = $query_activity["id"];
	
$count_new = 0;
$count_update = 0;
$count_archive = 0;

$query_buffer = mysqli_query($db->connection,"SELECT * FROM data_signalsanalysis_buffer WHERE activity = '".$activity_id."'");
while($row = mysqli_fetch_assoc($query_buffer)){
	$uid = $row["uid"];
	$name = $row["name"];
	$type = $row["type"];
	$owner = $row["owner"];
	$manager = $row["manager"];
	$operator = $row["operator"];
	$sector = $row["sector"];
	$system = $row["system"];
	$planet = $row["planet"];
	$galx = $row["galx"];
	$galy = $row["galy"];
	$sysx = $row["sysx"];
	$sysy = $row["sysy"];
	$atmox = $row["atmox"];
	$atmoy = $row["atmoy"];
	$surfx = $row["surfx"];
	$surfy = $row["surfy"];
	$passengers = $row["passengers"];
	$ships = $row["ships"];
	$vehicles = $row["vehicles"];
	$income = $row["income"];
	$timestamp = $row["timestamp"];

	//Current or historical data
	$entity = mysqli_fetch_assoc(mysqli_query($db->connection,"SELECT COUNT(uid) as count, data_signalsanalysis.* FROM data_signalsanalysis WHERE uid = '$uid'"));
	if($entity["count"] == 0){ // INSERT
		mysqli_query($db->connection,"INSERT INTO data_signalsanalysis (uid, name, type, owner, manager, operator, sector, galx, galy, `system`, sysx, sysy, planet, atmox, atmoy, surfx, surfy, passengers, ships, vehicles, income, timestamp, activity) VALUES ('$uid', '$name', '$type', '$owner', ".(($manager=="")?"NULL":("'".$manager."'")).", ".(($operator=="")?"NULL":("'".$operator."'")).", ".(($sector=="")?"NULL":("'".$sector."'")).", '$galx', '$galy', ".(($system=="")?"NULL":("'".$system."'")).", '$sysx', '$sysy', ".(($planet=="")?"NULL":("'".$planet."'")).", ".(($atmox=="")?"NULL":("'".$atmox."'")).", ".(($atmoy=="")?"NULL":("'".$atmoy."'")).", ".(($surfx=="")?"NULL":("'".$surfx."'")).", ".(($surfy=="")?"NULL":("'".$surfy."'")).", ".(($passengers=="")?"NULL":("'".$passengers."'")).", ".(($ships=="")?"NULL":("'".$ships."'")).", ".(($vehicles=="")?"NULL":("'".$vehicles."'")).", ".(($income=="")?"NULL":("'".$income."'")).", '$timestamp', '$activity_id')");
		$count_new++;
	}elseif($entity["timestamp"] < $timestamp){ // UPDATE
		mysqli_query($db->connection,"INSERT INTO data_signalsanalysis_archive (uid, name, type, owner, manager, operator, sector, galx, galy, `system`, sysx, sysy, planet, atmox, atmoy, surfx, surfy, passengers, ships, vehicles, income, timestamp, activity) VALUES ('".$entity["uid"]."', '".$entity["name"]."', '".$entity["type"]."', '".$entity["owner"]."', ".(($entity["manager"]==NULL)?"NULL":("'".$entity["manager"]."'")).", ".(($entity["operator"]==NULL)?"NULL":("'".$entity["operator"]."'")).", ".(($entity["sector"]==NULL)?"NULL":("'".$entity["sector"]."'")).", '".$entity["galx"]."', '".$entity["galy"]."', ".(($entity["system"]==NULL)?"NULL":("'".$entity["system"]."'")).", '".$entity["sysx"]."', '".$entity["sysy"]."', ".(($entity["planet"]==NULL)?"NULL":("'".$entity["planet"]."'")).", ".(($entity["atmox"]==NULL)?"NULL":("'".$entity["atmox"]."'")).", ".(($entity["atmoy"]==NULL)?"NULL":("'".$entity["atmoy"]."'")).", ".(($entity["surfx"]==NULL)?"NULL":("'".$entity["surfx"]."'")).", ".(($entity["surfy"]==NULL)?"NULL":("'".$entity["surfy"]."'")).", ".(($entity["passengers"]==NULL)?"NULL":("'".$entity["passengers"]."'")).", ".(($entity["ships"]==NULL)?"NULL":("'".$entity["ships"]."'")).", ".(($entity["vehicles"]==NULL)?"NULL":("'".$entity["vehicles"]."'")).", ".(($entity["income"]=="")?"NULL":("'".$entity["income"]."'")).", '".$entity["timestamp"]."', ".(($entity["activity"]==NULL)?"NULL":("'".$entity["activity"]."'")).")");
		//$archives = mysqli_fetch_assoc(mysqli_query($db->connection,"SELECT COUNT(uid) as count FROM data_signalsanalysis_archive WHERE uid = '$uid'"))["count"];
		//if($archives > 10){mysqli_query($db->connection,"DELETE FROM data_signalsanalysis_archive WHERE uid = '$uid' ORDER BY timestamp DESC LIMIT 1");}
		mysqli_query($db->connection,"DELETE FROM data_signalsanalysis WHERE uid = '$uid'");
		mysqli_query($db->connection,"INSERT INTO data_signalsanalysis (uid, name, type, owner, manager, operator, sector, galx, galy, `system`, sysx, sysy, planet, atmox, atmoy, surfx, surfy, passengers, ships, vehicles, income, timestamp, activity) VALUES ('$uid', '$name', '$type', '$owner', ".(($manager=="")?"NULL":("'".$manager."'")).", ".(($operator=="")?"NULL":("'".$operator."'")).", ".(($sector=="")?"NULL":("'".$sector."'")).", '$galx', '$galy', ".(($system=="")?"NULL":("'".$system."'")).", '$sysx', '$sysy', ".(($planet=="")?"NULL":("'".$planet."'")).", ".(($atmox=="")?"NULL":("'".$atmox."'")).", ".(($atmoy=="")?"NULL":("'".$atmoy."'")).", ".(($surfx=="")?"NULL":("'".$surfx."'")).", ".(($surfy=="")?"NULL":("'".$surfy."'")).", ".(($passengers=="")?"NULL":("'".$passengers."'")).", ".(($ships=="")?"NULL":("'".$ships."'")).", ".(($vehicles=="")?"NULL":("'".$vehicles."'")).", ".(($income=="")?"NULL":("'".$income."'")).", '$timestamp', '$activity_id')");
		$count_update++;
	}else{ // OLD
		mysqli_query($db->connection,"INSERT INTO data_signalsanalysis_archive (uid, name, type, owner, manager, operator, sector, galx, galy, `system`, sysx, sysy, planet, atmox, atmoy, surfx, surfy, passengers, ships, vehicles, income, timestamp, activity) VALUES ('$uid', '$name', '$type', '$owner', ".(($manager=="")?"NULL":("'".$manager."'")).", ".(($operator=="")?"NULL":("'".$operator."'")).", ".(($sector=="")?"NULL":("'".$sector."'")).", '$galx', '$galy', ".(($system=="")?"NULL":("'".$system."'")).", '$sysx', '$sysy', ".(($planet=="")?"NULL":("'".$planet."'")).", ".(($atmox=="")?"NULL":("'".$atmox."'")).", ".(($atmoy=="")?"NULL":("'".$atmoy."'")).", ".(($surfx=="")?"NULL":("'".$surfx."'")).", ".(($surfy=="")?"NULL":("'".$surfy."'")).", ".(($passengers=="")?"NULL":("'".$passengers."'")).", ".(($ships=="")?"NULL":("'".$ships."'")).", ".(($vehicles=="")?"NULL":("'".$vehicles."'")).", ".(($income=="")?"NULL":("'".$income."'")).", '$timestamp', '$activity_id')");
		$archives = mysqli_fetch_assoc(mysqli_query($db->connection,"SELECT COUNT(uid) as count FROM data_signalsanalysis_archive WHERE uid = '$uid'"))["count"];
		if($archives > 10){mysqli_query($db->connection,"DELETE FROM data_signalsanalysis_archive WHERE uid = '$uid' ORDER BY timestamp DESC LIMIT 1");}
		$count_archive++;
	}
}
	
$session->alert = $session->alert."<div class=\"alert alert-success\" style=\"font-size:14px;\">".$count_new." entities have been added, ".$count_update." entities have been updated, and ".$count_archive." entities have been archived</div>";
mysqli_query($db->connection,"UPDATE data_signalsanalysis_activity SET added = '$count_new', updated = '$count_update' WHERE id = '$activity_id'");
$update_log = mysqli_real_escape_string($db->connection, "UPLOAD: ".$count_new." new records, ".$count_update." updated records, and ".$count_archive." records were approved from the buffer <a href=\"../signalsanalysis.php?activity=".$activity."\" target=\"_blank\">".$activity."</a>");
mysqli_query($db->connection, "INSERT INTO logs_activities (user_id, log_type, details, timestamp) VALUES ('".$_SESSION['user_id']."', '1', '$update_log', '".swc_time(time(),TRUE)["timestamp"]."')");
mysqli_query($db->connection, "DELETE FROM data_signalsanalysis_buffer WHERE activity = '".$activity_id."'");
mysqli_query($db->connection, "UPDATE data_signalsanalysis_activity SET status = '1' WHERE id = '".$activity_id."'");
if($scan = mysqli_fetch_assoc(mysqli_query($db->connection,"SELECT * FROM data_signalsanalysis_scantime_buffer WHERE activity = '".$activity_id."'"))){
	if($scan_current = mysqli_fetch_assoc(mysqli_query($db->connection,"SELECT * FROM data_signalsanalysis_scantime WHERE system = '".$scan["system"]."'"))){
		if($scan["timestamp"] > $scan_current["timestamp"]){
			mysqli_query($db->connection,"UPDATE data_signalsanalysis_scantime SET timestamp = '".$scan["timestamp"]."' WHERE system = '".$scan["system"]."'");
		}
	}else{
		mysqli_query($db->connection,"INSERT INTO data_signalsanalysis_scantime (`system`, timestamp) VALUES ('".$scan["system"]."', '".$scan["timestamp"]."')");
	}
	mysqli_query($db->connection,"DELETE FROM data_signalsanalysis_scantime_buffer WHERE activity = '".$activity_id."'");
}

?>