<?php
error_reporting(0);
include("../database.php");
$db = new database;
$db->connect();

mysqli_query($db->connection,"TRUNCATE TABLE entities_classes");
$xml_data = parse_data();

foreach($xml_data->classes as $classes){
	foreach($classes->class as $class){
		
		$uid = mysqli_real_escape_string($db->connection,$class{'uid'});
		$name = mysqli_real_escape_string($db->connection,$class);
		
		$query_entities = mysqli_fetch_assoc(mysqli_query($db->connection,"SELECT COUNT(uid) AS count FROM entities_classes WHERE uid='$uid'"))["count"];

		if($query_entities == 0){
			mysqli_query($db->connection,"INSERT INTO entities_classes (uid, class) VALUES ('$uid','$name')");
		}else{
			mysqli_query($db->connection,"UPDATE entities_droids SET name='$name' WHERE uid='$uid'");
		}
	}
}

mysqli_query($db->connection,"UPDATE site_settings SET value=".$xml_data{'timestamp-swc'}." WHERE field='last_update_classes'");
$process = "http://www.swcombine.com/ws/v1.0/types/classes/";
$update_log = mysqli_real_escape_string($db->connection, "ENTITIES CLASSES UPDATE: '".$process."' has been scanned");
if(isset($_SESSION['user_id'])){
	mysqli_query($db->connection, "INSERT INTO logs_activities (user_id, log_type, details, timestamp) VALUES ('".$_SESSION['user_id']."', '2', '$update_log', '".swc_time(time(),TRUE)["timestamp"]."')");
}else{
	mysqli_query($db->connection, "INSERT INTO logs_activities (user_id, log_type, details, timestamp) VALUES (NULL, '2', '$update_log', '".swc_time(time(),TRUE)["timestamp"]."')");
}

function parse_data(){
	$file = "http://www.swcombine.com/ws/v1.0/types/classes/";
	$xml_file = file_get_contents($file);
	$xml = simplexml_load_string($xml_file);
	return $xml;
}

?>