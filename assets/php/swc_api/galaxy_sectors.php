<?php
error_reporting(0);
include("../database.php");
include("../functions.php");
$db = new database;
$db->connect();

$start = mysqli_fetch_assoc(mysqli_query($db->connection,"SELECT value FROM site_settings WHERE field='update_sectors_count'"))["value"];
$xml_data = parse_data($start,50);

//while((int)$xml_data{'start'} <= (int)$xml_data{'total'}){
	
	foreach($xml_data->sector as $sector){
		
		$uid = mysqli_real_escape_string($db->connection,$sector{'uid'});
		$name = mysqli_real_escape_string($db->connection,$sector{'name'});
		$link = $sector{'href'};
		$controlled_by = mysqli_real_escape_string($db->connection,$sector->{'controlled-by'});
		$population = mysqli_real_escape_string($db->connection,$sector->population);
		
		$query_sector = mysqli_query($db->connection,"SELECT * FROM galaxy_sectors WHERE uid='$uid'");

		if(!isset($query_sector)){
			mysqli_query($db->connection,"INSERT INTO galaxy_sectors (uid, name, controlled_by, population) VALUES ('$uid','$name','$controlled_by','$population')");
		}elseif($name != ""){
			mysqli_query($db->connection,"UPDATE galaxy_sectors SET name='$name', controlled_by='$controlled_by', population='$population' WHERE uid='$uid'");
		}
	}
	//$xml_data = parse_data($xml_data{'start'} + 50,50);
	
	$iter = $start + 50;
	if($iter > $xml_data{'total'}){
		mysqli_query($db->connection,"UPDATE site_settings SET value='1' WHERE field='update_sectors_count'");
	}else{
		mysqli_query($db->connection,"UPDATE site_settings SET value=".$iter." WHERE field='update_sectors_count'");
	}
//}

mysqli_query($db->connection,"UPDATE site_settings SET value=".$xml_data{'timestamp-swc'}." WHERE field='last_update_sectors'");

function parse_data($start_index,$item_count){
	$file = "http://www.swcombine.com/ws/v1.0/galaxy/sectors/?start_index=".$start_index."&item_count=".$item_count."";
	//echo $file."<br/>";
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