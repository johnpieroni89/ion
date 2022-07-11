<?php

preg_match_all("/var clockYear = (.*);\s*var clockDay = (.*);\s*var clockHour = (.*);\s*var clockMinute = (.*);\s*var clockSecond = (.*);/",$html,$match_time); // TIME
$time = swc_time(real_time($match_time[1][0], $match_time[2][0], $match_time[3][0], $match_time[4][0], $match_time[5][0]))["timestamp"];

//Set activity identifying data
$activity = md5($time."html");
$check_activity = mysqli_fetch_assoc(mysqli_query($db->connection,"SELECT COUNT(activity_id) AS count FROM data_signalsanalysis_activity WHERE activity_id = '$activity'"))["count"];
if($check_activity == 0){
	$activity_time = $time;
	mysqli_query($db->connection,"INSERT INTO data_signalsanalysis_activity (activity_id, activity_collector, activity_time, status) VALUES ('$activity', '".$_SESSION['user_id']."', '$activity_time', '1')");
	$activity_id = mysqli_fetch_assoc(mysqli_query($db->connection,"SELECT id FROM data_signalsanalysis_activity WHERE activity_id = '$activity'"))["id"];
	
	preg_match_all("/title=\"Go to: Character Sheet\">(.*)<\/a>/",$html,$match_character); // CHARACTER
	preg_match_all("/target=\"_blank\">\s*<strong>(.*)<\/strong>\s*<\/a>/",$html,$match_faction); // FACTION
	preg_match_all("/<span class=\"invSelected\">\s*(.*)\s*<\/span>/",$html,$match_selected); // SELECTED INVENTORY
	if($match_selected[1][0] == "Personal"){$ownerName = $match_character[1][0];}else{$ownerName = $match_faction[1][0];}

	preg_match_all("/invent_(.{1,10})\"/",$html,$match_uid); // UID
	preg_match_all("/data\-name=\"(.*)\" data/",$html,$match_name); // NAME
	preg_match_all("/data\-typename=\"(.*)\">/",$html,$match_type); // TYPE
	preg_match_all("/\(([\-0-9]{1,4}), ([\-0-9]{1,4})\)<br>/",$html,$match_gal); // GAL X/Y
	preg_match_all("/\(([0-9]*), ([0-9]*)\)<\/td>\s*<td valign=\"top\"/",$html,$match_sys); // SYS X/Y
	preg_match_all("/Commander:\s*<span id=\".*\" class=\".*\" ajax-class=\".*\" ajax-method=\".*\" ajax-args=\".*\">\s*(.*)\s*<\/span>/",$html,$match_commander); // COMMANDER
	preg_match_all("/Pilot:\s*<span id=\".*\" class=\".*\" ajax-class=\".*\" ajax-method=\".*\" ajax-args=\".*\">\s*(.*)\s*<\/span>/",$html,$match_pilot); // PILOT

	$count = 0;
	$count_new = 0;
	$count_update = 0;
	$count_archive = 0;
	$count_corrupt = 0;
	
	foreach($match_uid[1] as $match){
		$entityID = mysqli_real_escape_string($db->connection,$match);
		$name = mysqli_real_escape_string($db->connection,$match_name[1][$count]);
		$entityTypeName = mysqli_real_escape_string($db->connection,$match_type[1][$count]);
		$entityType = mysqli_fetch_assoc(mysqli_query($db->connection,"SELECT uid FROM entities WHERE name = '$entityTypeName'"))["uid"];
		$galx = mysqli_real_escape_string($db->connection,$match_gal[1][$count]);
		$galy = mysqli_real_escape_string($db->connection,$match_gal[2][$count]);
		$sysx = mysqli_real_escape_string($db->connection,$match_sys[1][$count]);
		$sysy = mysqli_real_escape_string($db->connection,$match_sys[2][$count]);
		$atmox = NULL;
		$atmoy = NULL;
		$surfx = NULL;
		$surfy = NULL;
		
		if(trim($match_commander[1][$count]) == "None"){$manager = NULL;}else{$manager = "'".trim($match_commander[1][$count])."'";}
		if(trim($match_pilot[1][$count]) == "None"){$operator = NULL;}else{$operator = "'".trim($match_pilot[1][$count])."'";}
		
		$galaxy_query = mysqli_fetch_assoc(mysqli_query($db->connection,"SELECT uid, sector FROM galaxy_systems WHERE galx = '".$match_gal[1][$count]."' AND galy = '".$match_gal[2][$count]."'"));
		
		if($galaxy_query["sector"] == ""){$sector = NULL;}else{$sector = "'".$galaxy_query["sector"]."'";}
		if($galaxy_query["uid"] == ""){$system = NULL;}else{$system = "'".$galaxy_query["uid"]."'";}
		
		$entity = mysqli_fetch_assoc(mysqli_query($db->connection,"SELECT COUNT(uid) as count, data_signalsanalysis.* FROM data_signalsanalysis WHERE uid = '$entityID'"));
		
		if($entityType == ""){
			$count_corrupt++;
		}elseif($entity["count"] == 0){ // INSERT
			mysqli_query($db->connection,"INSERT INTO data_signalsanalysis (uid, name, type, owner, manager, operator, sector, galx, galy, system, sysx, sysy, atmox, atmoy, surfx, surfy, timestamp, activity) VALUES ('$entityID', '$name', '$entityType', '$ownerName', ".(($manager==NULL)?"NULL":($manager)).", ".(($operator==NULL)?"NULL":($operator)).", ".(($sector==NULL)?"NULL":($sector)).", '$galx', '$galy', ".(($system==NULL)?"NULL":($system)).", '$sysx', '$sysy', ".(($atmox==NULL)?"NULL":("'".$atmox."'")).", ".(($atmoy==NULL)?"NULL":("'".$atmoy."'")).", ".(($surfx==NULL)?"NULL":("'".$surfx."'")).", ".(($surfy==NULL)?"NULL":("'".$surfy."'")).", '$time', '$activity_id')");
			$count_new++;
		}elseif($entity["timestamp"] < $activity_time){ // UPDATE
			mysqli_query($db->connection,"INSERT INTO data_signalsanalysis_archive (uid, name, type, owner, manager, operator, sector, galx, galy, system, sysx, sysy, atmox, atmoy, surfx, surfy, passengers, ships, vehicles, timestamp, activity) VALUES ('".$entity["uid"]."', '".$entity["name"]."', '".$entity["type"]."', '".$entity["owner"]."', ".(($entity["manager"]==NULL)?"NULL":("'".$entity["manager"]."'")).", ".(($entity["operator"]==NULL)?"NULL":("'".$entity["operator"]."'")).", ".(($entity["sector"]==NULL)?"NULL":("'".$entity["sector"]."'")).", '".$entity["galx"]."', '".$entity["galy"]."', ".(($entity["system"]==NULL)?"NULL":("'".$entity["system"]."'")).", '".$entity["sysx"]."', '".$entity["sysy"]."', ".(($entity["atmox"]==NULL)?"NULL":("'".$entity["atmox"]."'")).", ".(($entity["atmoy"]==NULL)?"NULL":("'".$entity["atmoy"]."'")).", ".(($entity["surfx"]==NULL)?"NULL":("'".$entity["surfx"]."'")).", ".(($entity["surfy"]==NULL)?"NULL":("'".$entity["surfy"]."'")).", ".(($entity["passengers"]==NULL)?"NULL":("'".$entity["passengers"]."'")).", ".(($entity["ships"]==NULL)?"NULL":("'".$entity["ships"]."'")).", ".(($entity["vehicles"]==NULL)?"NULL":("'".$entity["vehicles"]."'")).", '".$entity["timestamp"]."', ".(($entity["activity"]==NULL)?"NULL":("'".$entity["activity"]."'")).")");
			$archives = mysqli_fetch_assoc(mysqli_query($db->connection,"SELECT COUNT(uid) as count FROM data_signalsanalysis_archive WHERE uid = '$entityID'"))["count"];
			if($archives > 10){mysqli_query($db->connection,"DELETE FROM data_signalsanalysis_archive WHERE uid = '$entityID' ORDER BY timestamp DESC LIMIT 1");}
			mysqli_query($db->connection,"DELETE FROM data_signalsanalysis WHERE uid = '$entityID'");
			mysqli_query($db->connection,"INSERT INTO data_signalsanalysis (uid, name, type, owner, manager, operator, sector, galx, galy, system, sysx, sysy, atmox, atmoy, surfx, surfy, timestamp, activity) VALUES ('$entityID', '$name', '$entityType', '$ownerName', ".(($manager==NULL)?"NULL":($manager)).", ".(($operator==NULL)?"NULL":($operator)).", ".(($sector==NULL)?"NULL":($sector)).", '$galx', '$galy', ".(($system==NULL)?"NULL":($system)).", '$sysx', '$sysy', ".(($atmox==NULL)?"NULL":("'".$atmox."'")).", ".(($atmoy==NULL)?"NULL":("'".$atmoy."'")).", ".(($surfx==NULL)?"NULL":("'".$surfx."'")).", ".(($surfy==NULL)?"NULL":("'".$surfy."'")).", '$time', '$activity_id')");
			$count_update++;
		}else{ // OLD
			mysqli_query($db->connection,"INSERT INTO data_signalsanalysis (uid, name, type, owner, manager, operator, sector, galx, galy, system, sysx, sysy, atmox, atmoy, surfx, surfy, timestamp, activity) VALUES ('$entityID', '$name', '$entityType', '$ownerName', ".(($manager==NULL)?"NULL":($manager)).", ".(($operator==NULL)?"NULL":($operator)).", ".(($sector==NULL)?"NULL":($sector)).", '$galx', '$galy', ".(($system==NULL)?"NULL":($system)).", '$sysx', '$sysy', ".(($atmox==NULL)?"NULL":("'".$atmox."'")).", ".(($atmoy==NULL)?"NULL":("'".$atmoy."'")).", ".(($surfx==NULL)?"NULL":("'".$surfx."'")).", ".(($surfy==NULL)?"NULL":("'".$surfy."'")).", '$time', '$activity_id')");
			$archives = mysqli_fetch_assoc(mysqli_query($db->connection,"SELECT COUNT(uid) as count FROM data_signalsanalysis_archive WHERE uid = '$entityID'"))["count"];
			if($archives > 10){mysqli_query($db->connection,"DELETE FROM data_signalsanalysis_archive WHERE uid = '$entityID' ORDER BY timestamp DESC LIMIT 1");}
			$count_archive++;
		}
		
		$count++;
	}
	
	$session->alert = $session->alert."<div class=\"alert alert-success\" style=\"font-size:14px;\">".$file = $_FILES['file']['name'][$i]." - ".$count_new." entities have been added, ".$count_update." entities have been updated, ".$count_archive." entities have been archived, and ".$count_corrupt." entities are corrupted and weren't ingested!</div>";
	mysqli_query($db->connection,"UPDATE data_signalsanalysis_activity SET added = '$count_new', updated = '$count_update' WHERE id = '$activity_id'");
	
}else{
	$session->alert = $session->alert."<div class=\"alert alert-danger\" style=\"font-size:14px;\">".$file = $_FILES['file']['name'][$i]." - Data already has been ingested from this activity (".$activity.")</div>";
}
?>