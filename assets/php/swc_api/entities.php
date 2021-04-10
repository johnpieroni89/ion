<?php
error_reporting(0);
include("../database.php");
$db = new database;
$db->connect();
$entity_types = array("ships","vehicles","stations","facilities");

foreach($entity_types as $value){

	$xml_data = parse_data(1,50,$value);

	while((int)$xml_data->types{'start'} <= (int)$xml_data->types{'total'}){
		
		foreach($xml_data->types->type as $entity){
			
			$uid = mysqli_real_escape_string($db->connection,$entity{'uid'});
			$name = mysqli_real_escape_string($db->connection,$entity{'name'});
			$link = $entity{'href'};
			
			$entity_data = fetch_entity($link);
			
			$class = mysqli_real_escape_string($db->connection,$entity_data->class{'uid'});
			$price = mysqli_real_escape_string($db->connection,$entity_data->price->credits);
			if($value == "facilities"){
				$image_small = mysqli_real_escape_string($db->connection,$entity_data->images->sets[0]->small);
			}else{
				$image_small = mysqli_real_escape_string($db->connection,$entity_data->images->small);
			}
			
			$query_entities = mysqli_num_rows(mysqli_query($db->connection,"SELECT * FROM entities WHERE uid='$uid'"));

			if($query_entities == 0){
				mysqli_query($db->connection,"INSERT INTO entities (uid, name, class, price, img_small) VALUES ('$uid','$name','$class','$price','$image_small')");
			}elseif($name != "" && $class != "" && $price != ""){
				mysqli_query($db->connection,"UPDATE entities SET name='$name', class='$class', price='$price', img_small='$image_small' WHERE uid='$uid'");
			}
		}
		
		$xml_data = parse_data($xml_data->types{'start'} + 50,50,$value);
		
	}
	
	mysqli_query($db->connection,"UPDATE site_settings SET value=".$xml_data{'timestamp-swc'}." WHERE field='last_update_entities'");
	$process = "http://www.swcombine.com/ws/v1.0/types/".$value."/?start_index=".$start."&item_count=50";
	$update_log = mysqli_real_escape_string($db->connection, "ENTITIES UPDATE: '".$process."' has been scanned");
	if(isset($_SESSION['user_id'])){
		mysqli_query($db->connection, "INSERT INTO logs_activities (user_id, log_type, details, timestamp) VALUES ('".$_SESSION['user_id']."', '1', '$update_log', '".swc_time(time(),TRUE)["timestamp"]."')");
	}else{
		mysqli_query($db->connection, "INSERT INTO logs_activities (user_id, log_type, details, timestamp) VALUES (NULL, '1', '$update_log', '".swc_time(time(),TRUE)["timestamp"]."')");
	}
}

function parse_data($start_index,$item_count,$value){
	$file = "http://www.swcombine.com/ws/v1.0/types/".$value."/?start_index=".$start_index."&item_count=".$item_count."";
	$xml_file = file_get_contents($file);
	$xml = simplexml_load_string($xml_file);
	return $xml;
}

function fetch_entity($link){
	$xml_file = file_get_contents($link);
	$xml = simplexml_load_string($xml_file);
	return $xml;
}

?>