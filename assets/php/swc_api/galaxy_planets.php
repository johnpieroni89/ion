<?php
error_reporting(0);
include("../database.php");
include("../functions.php");
$db = new database;
$db->connect();

$start = mysqli_fetch_assoc(mysqli_query($db->connection,"SELECT value FROM site_settings WHERE field='update_planets_count'"))["value"];
$xml_data = parse_data($start,50);

//while((int)$xml_data{'start'} <= (int)$xml_data{'total'}){
	
	foreach($xml_data->planet as $planet){
		
		$uid = mysqli_real_escape_string($db->connection,$planet{'uid'});
		$name = mysqli_real_escape_string($db->connection,$planet{'name'});
		$link = "http://www.swcombine.com/ws/v1.0/galaxy/planets/".$uid;
		$controlled_by = mysqli_real_escape_string($db->connection,$planet->{'controlled-by'});
		$population = mysqli_real_escape_string($db->connection,$planet->population);
		$cities = mysqli_real_escape_string($db->connection,$planet->cities);
		
		$planet_data = fetch_data($link);
		
		$size = mysqli_real_escape_string($db->connection,$planet_data->size);
		$governor = mysqli_real_escape_string($db->connection,$planet_data->governor);
		$magistrate = mysqli_real_escape_string($db->connection,$planet_data->magistrate);
		$sector = mysqli_real_escape_string($db->connection,$planet_data->sector{'uid'});
		$system = mysqli_real_escape_string($db->connection,$planet_data->system{'uid'});
		$galx = mysqli_real_escape_string($db->connection,$planet_data->coordinates->galaxy{'x'});
		$galy = mysqli_real_escape_string($db->connection,$planet_data->coordinates->galaxy{'y'});
		$sysx = mysqli_real_escape_string($db->connection,$planet_data->coordinates->system{'x'});
		$sysy = mysqli_real_escape_string($db->connection,$planet_data->coordinates->system{'y'});
		$img_small = mysqli_real_escape_string($db->connection,$planet_data->images->small);
		$type = mysqli_real_escape_string($db->connection, $planet_data->type{'value'});
		$civilization = mysqli_real_escape_string($db->connection,$planet_data->{'civilisation-level'});
		$tax = mysqli_real_escape_string($db->connection,$planet_data->{'tax-level'});
		$terrain = mysqli_real_escape_string($db->connection,$planet_data->{'terrain-map'});
		
		/*
			b = desert
			c = forest
			d = jungle
			e = swamp
			f = grassland
			g = ocean
			h = river
			i = crater
			j = rock
			k = glacier
			l = mountain
			m = volcanic
			n = cave
			o = gas giant
			y = black hole
			z = sun
		*/
		
		mysqli_query($db->connection, "INSERT INTO galaxy_planets (uid, name, controlled_by, population, governor, magistrate, sector, `system`, galx, galy, sysx, sysy, img_small, size, type, terrain, cities, civilization, tax) VALUES ('$uid','$name','$controlled_by','$population', '$governor', '$magistrate', '$sector', '$system', '$galx', '$galy', '$sysx', '$sysy', '$img_small', '$size', '$type', '$terrain', '$cities', '$civilization', '$tax') ON DUPLICATE KEY UPDATE name='$name', controlled_by='$controlled_by', population='$population', governor='$governor', magistrate='$magistrate', sector='$sector', `system`='$system', galx='$galx', galy='$galy', sysx='$sysx', sysy='$sysy', img_small='$img_small', size='$size', type='$type', terrain='$terrain', cities='$cities', civilization='$civilization', tax='$tax'");
		
		
		$query_planet = mysqli_query($db->connection,"SELECT * FROM galaxy_planets WHERE uid='$uid'");

		if(isset($query_planet)){
			mysqli_query($db->connection,"UPDATE galaxy_planets SET name='$name', controlled_by='$controlled_by', population='$population', governor='$governor', magistrate='$magistrate', sector='$sector', `system`='$system', galx='$galx', galy='$galy', sysx='$sysx', sysy='$sysy', img_small='$img_small', size='$size', civilization='$civilization', tax='$tax' WHERE uid='$uid'");
		}else{
			mysqli_query($db->connection,"INSERT INTO galaxy_planets (uid, name, controlled_by, population, governor, magistrate, sector, `system`, galx, galy, sysx, sysy, img_small, size, civilization, tax) VALUES ('$uid','$name','$controlled_by','$population', '$governor', '$magistrate', '$sector', '$system', '$galx', '$galy', '$sysx', '$sysy', '$img_small', '$size', '$civilization', '$tax')");
		}
		
	}
	$iter = $start + 50;
	//$iter = $xml_data{'start'} + 50;
	//$xml_data = parse_data($iter,50);
	if($iter > $xml_data{'total'}){
		mysqli_query($db->connection,"UPDATE site_settings SET value='1' WHERE field='update_planets_count'");
	}else{
		mysqli_query($db->connection,"UPDATE site_settings SET value=".$iter." WHERE field='update_planets_count'");
	}
	
//}
mysqli_query($db->connection,"UPDATE site_settings SET value=".$xml_data{'timestamp-swc'}." WHERE field='last_update_planets'");

function parse_data($start_index,$item_count){
	$file = "http://www.swcombine.com/ws/v1.0/galaxy/planets/?start_index=".$start_index."&item_count=".$item_count."";
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