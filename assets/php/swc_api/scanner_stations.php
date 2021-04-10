<?php
error_reporting(0);
include("../database.php");
include("../functions.php");
$db = new database;
$db->connect();

$sql_insert = "";
$sql_update = "";

$start = mysqli_fetch_assoc(mysqli_query($db->connection,"SELECT value FROM site_settings WHERE field='update_stations_count'"))["value"];
$xml_data = parse_data($start,50);

$time = swc_time($xml_data{'timestamp-swc'});
$year = $time["year"];
$day = $time["day"];
$hour = $time["hour"];
$minute = $time["minute"];
$second = $time["second"];
$timestamp = $time["timestamp"];

while((int)$xml_data{'start'} <= (int)$xml_data{'total'}){
	
	foreach($xml_data->station as $station){
		
		$uid = mysqli_real_escape_string($db->connection,$station{'uid'});
		$name = mysqli_real_escape_string($db->connection,str_replace("&amp;", "&", $station{'name'}));
		$link = $station{'href'};
		
		$station_data = fetch_data($link);
		
		$type = mysqli_real_escape_string($db->connection,str_replace("&amp;", "&", $station_data->type));
		$type = mysqli_fetch_assoc(mysqli_query($db->connection,"SELECT uid FROM entities WHERE name='$type'")){"uid"};
		$owner = mysqli_real_escape_string($db->connection,str_replace("&amp;", "&", $station_data->owner{'name'}));
		$galx = mysqli_real_escape_string($db->connection,$station_data->coordinates->galaxy{'x'});
		$galy = mysqli_real_escape_string($db->connection,$station_data->coordinates->galaxy{'y'});
		$query_loc = mysqli_fetch_assoc(mysqli_query($db->connection,"SELECT uid, sector FROM galaxy_systems WHERE galx='".$galx."' AND galy='".$galy."'"));
		$sector = $query_loc["sector"];
		$sysx = mysqli_real_escape_string($db->connection,$station_data->coordinates->system{'x'});
		$sysy = mysqli_real_escape_string($db->connection,$station_data->coordinates->system{'y'});
		$system = $query_loc["uid"];
		$query_planet = mysqli_fetch_assoc(mysqli_query($db->connection,"SELECT uid FROM galaxy_planets WHERE galx = '".$galx."' AND galy = '".$galy."' AND sysx = '".$sysx."' AND sysy = '".$sysy."'"));
		if(!empty($query_planet['uid'])){
			$planet_ins = "'".$query_planet['uid']."'";
			$planet_upd = "planet = '".$query_planet['uid']."', ";
		}else{
			$planet_ins = "NULL";
			$planet_upd = "";
		}
		
		mysqli_query($db->connection, "INSERT INTO data_signalsanalysis (uid, name, type, owner, sector, galx, galy, `system`, sysx, sysy, planet, timestamp) VALUES ('$uid','$name','$type','$owner', '$sector', '$galx', '$galy', '$system', '$sysx', '$sysy', ".$planet_ins.", '$timestamp') ON DUPLICATE KEY UPDATE name='$name', type='$type', owner='$owner', sector='$sector', galx='$galx', galy='$galy', system='$system', sysx='$sysx', sysy='$sysy', ".$planet_upd."timestamp='$timestamp'");
		
	}

	$iter = $xml_data{'start'} + 50;
	$xml_data = parse_data($iter,50);
	mysqli_query($db->connection,"UPDATE site_settings SET value=".$iter." WHERE field='update_stations_count'");
}

mysqli_query($db->connection,"UPDATE site_settings SET value='1' WHERE field='update_stations_count'");
mysqli_query($db->connection,"UPDATE site_settings SET value=".$xml_data{'timestamp-swc'}." WHERE field='last_update_stations'");

function parse_data($start_index,$item_count){
	$file = "http://www.swcombine.com/ws/v1.0/galaxy/stations/?start_index=".$start_index."&item_count=".$item_count."";
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