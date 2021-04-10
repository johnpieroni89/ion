<?php
	error_reporting(0);
	include("assets/php/database.php");
	include("assets/php/functions.php");
	include("assets/php/session.php");
	$db = new database;
	$db->connect();
	
	$xml = new SimpleXMLElement('<xml/>');
	$xml->addAttribute("type", "export");
	$xml->addAttribute("timestamp", swc_time(time(),TRUE)["timestamp"]);

	foreach($_POST["entities"] as $entity){
		$exp_uid = explode(";",$entity)[0];
		$exp_timestamp = explode(";",$entity)[1];
		$entity_data = mysqli_fetch_assoc(mysqli_query($db->connection,"SELECT data_signalsanalysis.*, data_signalsanalysis_customs.custom_image, data_signalsanalysis_customs.timestamp AS custom_timestamp FROM data_signalsanalysis LEFT JOIN data_signalsanalysis_customs ON data_signalsanalysis.uid = data_signalsanalysis_customs.uid WHERE data_signalsanalysis.uid = '$exp_uid' AND data_signalsanalysis.timestamp = '$exp_timestamp'"));
		if($entity_data["uid"] == ""){
			$entity_data = mysqli_fetch_assoc(mysqli_query($db->connection,"SELECT data_signalsanalysis_archive.*, data_signalsanalysis_customs.custom_image, data_signalsanalysis_customs.timestamp AS custom_timestamp FROM data_signalsanalysis_archive LEFT JOIN data_signalsanalysis_customs ON data_signalsanalysis_archive.uid = data_signalsanalysis_customs.uid WHERE data_signalsanalysis_archive.uid = '$exp_uid' AND data_signalsanalysis_archive.timestamp = '$exp_timestamp'"));
		}
		$type = mysqli_fetch_assoc(mysqli_query($db->connection, "SELECT name FROM entities WHERE uid = '".$entity_data['type']."'"));
		$sector = mysqli_fetch_assoc(mysqli_query($db->connection, "SELECT name FROM galaxy_sectors WHERE uid = '".$entity_data['sector']."'"));
		$system = mysqli_fetch_assoc(mysqli_query($db->connection, "SELECT name FROM galaxy_systems WHERE uid = '".$entity_data['system']."'"));
		$planet = mysqli_fetch_assoc(mysqli_query($db->connection, "SELECT name FROM galaxy_planets WHERE uid = '".$entity_data['planet']."'"));
		
		$export = $xml->addChild('entity');
		$export->addChild('uid', $exp_uid);
		$export->addChild('name', $entity_data["name"]);
		$export->addChild('type_id', $entity_data["type"]);
		$export->addChild('type_name', $type["name"]);
		$export->addChild('owner', $entity_data["owner"]);
		$export->addChild('manager', $entity_data["manager"]);
		$export->addChild('operator', $entity_data["operator"]);
		$export->addChild('sector_id', $entity_data["sector"]);
		$export->addChild('sector', $sector["name"]);
		$export->addChild('galx', $entity_data["galx"]);
		$export->addChild('galy', $entity_data["galy"]);
		$export->addChild('system_id', $entity_data["system"]);
		$export->addChild('system', $system["name"]);
		$export->addChild('sysx', $entity_data["sysx"]);
		$export->addChild('sysy', $entity_data["sysy"]);
		$export->addChild('planet_id', $entity_data["planet"]);
		$export->addChild('planet', $planet["name"]);
		$export->addChild('atmox', $entity_data["atmox"]);
		$export->addChild('atmoy', $entity_data["atmoy"]);
		$export->addChild('surfx', $entity_data["surfx"]);
		$export->addChild('surfy', $entity_data["surfy"]);
		$export->addChild('passengers', $entity_data["passengers"]);
		$export->addChild('ships', $entity_data["ships"]);
		$export->addChild('vehicles', $entity_data["vehicles"]);
		$export->addChild('income', $entity_data["income"]);
		$export->addChild('timestamp', $exp_timestamp);
		$export->addChild('custom_image', $entity_data["custom_image"]);
		$export->addChild('custom_timestamp', $entity_data["custom_timestamp"]);
	}

	$name = strftime('export_%m_%d_%Y.xml');
	header('Content-Disposition: attachment;filename=' . $name);
	header('Content-Type: text/xml');
	echo $xml->asXML();
?>