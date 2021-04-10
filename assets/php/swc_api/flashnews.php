<?php
error_reporting(0);
include("../database.php");
include("../functions.php");
$db = new database;
$db->connect();

$xml_data = parse_data();

foreach($xml_data->channel->item as $item) {
	$check = mysqli_num_rows(mysqli_query($db->connection, "SELECT * FROM flashnews WHERE guid = '".$item->guid."'"));
	if($check == 0){
		mysqli_query($db->connection,"INSERT INTO flashnews (guid, title, description, timestamp) VALUES ('".$item->guid."', '".$item->title."', '".$item->description."', '".strtotime($item->pubDate)."')");
		if(strpos($item->title, "renamed to")){
			preg_match("#(.*) renamed to (.*)#", $item->title, $match);
			$former = mysqli_real_escape_string($db->connection, $match[1]);
			$total = mysqli_num_rows(mysqli_query($db->connection, "SELECT * FROM data_signalsanalysis WHERE owner = '$former'"));
			$current = mysqli_real_escape_string($db->connection, $match[2]);
			mysqli_query($db->connection, "UPDATE data_signalsanalysis SET owner = '$current' WHERE owner = '$former'");
			mysqli_query($db->connection, "INSERT INTO logs_activities (user_id, log_type, details, timestamp) VALUES ('NULL', '7', \"$total records have been changed from being owned by $former to $current\", '".swc_time(time(),TRUE)["timestamp"]."')");
		}
	}
}

function parse_data(){
	$file = "http://www.swcombine.com/community/news/flashfeed.php";
	$xml_file = file_get_contents($file);
	$xml = simplexml_load_string($xml_file);
	return $xml;
}

$db->disconnect();
unset($db);
exit;
?>