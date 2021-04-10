<?php
error_reporting(1);
include("../database.php");
include("../functions.php");
$db = new database;
$db->connect();

mysqli_query($db->connection,"TRUNCATE TABLE factions");

$xml_data = parse_data(1,50);

while((int)$xml_data{'start'} <= (int)$xml_data{'total'}){
	
	foreach($xml_data->faction as $faction){
		$uid = mysqli_real_escape_string($db->connection,$faction{'uid'});
		$name = mysqli_real_escape_string($db->connection,$faction{'name'});
		mysqli_query($db->connection,"INSERT INTO factions (uid, name) VALUES ('$uid','$name') ON DUPLICATE KEY UPDATE name='$name'");
	}
	
	$iter = $xml_data{'start'} + 50;
	$xml_data = parse_data($iter,50);
}

mysqli_query($db->connection,"UPDATE site_settings SET value=".$xml_data{'timestamp-swc'}." WHERE field='last_update_factions'");
$process = "http://www.swcombine.com/ws/v1.0/factions/";
$update_log = mysqli_real_escape_string($db->connection, "FACTIONS UPDATE: '".$process."' has been scanned; ".$xml_data{'total'}." factions exist.");
if(isset($_SESSION['user_id'])){
	mysqli_query($db->connection, "INSERT INTO logs_activities (user_id, log_type, details, timestamp) VALUES ('".$_SESSION['user_id']."', '3', '$update_log', '".swc_time(time(),TRUE)["timestamp"]."')");
}

function parse_data($start_index,$item_count){
	$file = "http://www.swcombine.com/ws/v1.0/factions/?start_index=".$start_index."&item_count=".$item_count."";
	$xml_file = file_get_contents($file);
	$xml = simplexml_load_string($xml_file);
	return $xml;
}

function fetch_data($link){
	$xml_file = file_get_contents($link);
	$xml = simplexml_load_string($xml_file);
	return $xml;
}

?>