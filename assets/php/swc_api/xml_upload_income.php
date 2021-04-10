<?php

$count_income = 0;

foreach($xml_file->channel->item as $item){
	//Strip the entity data
	$entityID = "4:".mysqli_real_escape_string($db->connection,$item->buildingID);
	$name = mysqli_real_escape_string($db->connection,$item->name);
	$manager = mysqli_real_escape_string($db->connection,$item->manager);
	$income = mysqli_real_escape_string($db->connection,$item->amount);
	$timestamp = mysqli_real_escape_string($db->connection,$item->date);
	
	$queryFacility = mysqli_fetch_assoc(mysqli_query($db->connection,"SELECT COUNT(uid) AS count FROM data_signalsanalysis WHERE uid = '$entityID'"))["count"];
	
	if($queryFacility == 0){
		$typeName = mysqli_real_escape_string($db->connection,str_replace("&amp;","&",$item->typeName));
			$entityType = mysqli_fetch_assoc(mysqli_query($db->connection,"SELECT uid FROM entities WHERE name = '$typeName'"))["uid"];
		$planetName = mysqli_real_escape_string($db->connection,$item->planetName);
			$queryPlanet = mysqli_fetch_assoc(mysqli_query($db->connection,"SELECT * FROM galaxy_planets WHERE name = '$planetName'"));
			$sector = $queryPlanet['sector'];
			$system = $queryPlanet['system'];
			$planet = $queryPlanet['uid'];
			$galx = $queryPlanet['galx'];
			$galy = $queryPlanet['galy'];
			$sysx = $queryPlanet['sysx'];
			$sysy = $queryPlanet['sysy'];
		mysqli_query($db->connection,"INSERT INTO data_signalsanalysis (uid, name, type, manager, sector, galx, galy, `system`, sysx, sysy, planet, income, timestamp) VALUES ('$entityID', '$name', '$entityType', '$manager', '$sector', '$galx', '$galy', '$system', '$sysx', '$sysy', '$planet', '$income', '$timestamp')");
		$count_income++;
	}else if($queryFacility != 0){
		mysqli_query($db->connection,"UPDATE data_signalsanalysis SET name = '$name', manager = '$manager', income = '$income', timestamp = '$timestamp' WHERE uid = '$entityID'");
		$count_income++;
	}
}

$alert = $alert."<div class=\"alert alert-success\" style=\"font-size:14px;\">".$file = $_FILES['file']['name'][$i]." - ".$count_income." facilities have been updated with income records!</div>";
$update_log = mysqli_real_escape_string($db->connection, "UPLOAD: ".$count_income." facilities have been updated with income records!");
mysqli_query($db->connection, "INSERT INTO logs_activities (user_id, log_type, details, timestamp) VALUES ('".$_SESSION['user_id']."', '1', '$update_log', '".swc_time(time(),TRUE)["timestamp"]."')");

?>