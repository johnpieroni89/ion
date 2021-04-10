<?php
error_reporting(0);
include("../database.php");
$db = new database;
$db->connect();

mysqli_query($db->connection,"TRUNCATE TABLE entities_races");
$xml_data = parse_data(1,50);

while((int)$xml_data->types{'start'} <= (int)$xml_data->types{'total'}){

	foreach($xml_data->types as $types){
		foreach($types->type as $type){
			
			$uid = mysqli_real_escape_string($db->connection,$type{'uid'});
			$race = mysqli_real_escape_string($db->connection,$type{'name'});
			
			$query_race = mysqli_fetch_assoc(mysqli_query($db->connection,"SELECT COUNT(uid) AS count FROM entities_races WHERE uid='$uid'"))["count"];

			if($query_race == 0){
				mysqli_query($db->connection,"INSERT INTO entities_races (uid, race) VALUES ('$uid','$race')");
			}else{
				mysqli_query($db->connection,"UPDATE entities_races SET race='$race' WHERE uid='$uid'");
			}
		}
	}
	$iter = (int)$xml_data->types{'start'} + 50;
	$xml_data = parse_data($iter,50);
}

mysqli_query($db->connection,"UPDATE site_settings SET value=".$xml_data{'timestamp-swc'}." WHERE field='last_update_races'");
$process = "https://www.swcombine.com/ws/v1.0/types/races/";
$update_log = mysqli_real_escape_string($db->connection, "ENTITIES RACES UPDATE: '".$process."' has been scanned");
if(isset($_SESSION['user_id'])){
	mysqli_query($db->connection, "INSERT INTO logs_activities (user_id, log_type, details, timestamp) VALUES ('".$_SESSION['user_id']."', '0', '$update_log', '".swc_time(time(),TRUE)["timestamp"]."')");
}else{
	mysqli_query($db->connection, "INSERT INTO logs_activities (user_id, log_type, details, timestamp) VALUES (NULL, '0', '$update_log', '".swc_time(time(),TRUE)["timestamp"]."')");
}

function parse_data($start_index,$item_count){
	$file = "https://www.swcombine.com/ws/v1.0/types/races/?start_index=".$start_index."&item_count=".$item_count.""; 
	$xml_file = file_get_contents($file);
	$xml = simplexml_load_string($xml_file);
	return $xml;
}

?>