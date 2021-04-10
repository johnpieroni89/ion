<?php
include("../database.php");
$db = new database;
$db->connect();

$search_string = "SELECT SUM(population) AS pop, COUNT(galaxy_planets.uid) AS planets, SUM(cities) AS cities, AVG(civilization) AS civ, AVG(tax) AS taxes, controlled_by FROM galaxy_planets LEFT JOIN factions ON galaxy_planets.controlled_by = factions.name WHERE galaxy_planets.type <> 'sun' GROUP BY controlled_by ORDER BY controlled_by ASC";
$query = mysqli_query($db->connection,$search_string);
while($data = mysqli_fetch_assoc($query)){
	$query_sectors = mysqli_fetch_assoc(mysqli_query($db->connection,"SELECT COUNT(uid) as count FROM galaxy_sectors WHERE controlled_by = '".$data['controlled_by']."'"))["count"];
	$query_systems = mysqli_fetch_assoc(mysqli_query($db->connection,"SELECT COUNT(uid) as count FROM galaxy_systems WHERE controlled_by = '".$data['controlled_by']."'"))["count"];
	$faction_uid = mysqli_fetch_assoc(mysqli_query($db->connection,"SELECT uid FROM factions WHERE name = '".$data['controlled_by']."'"))["uid"];
	mysqli_query($db->connection,"INSERT INTO data_galaxydata (faction, total_population, sectors, systems, planets, cities, avg_civilization, avg_tax, timestamp) VALUES ('".$faction_uid."', '".$data['pop']."', '".$query_sectors."', '".$query_systems."', '".$data['planets']."', '".$data['cities']."', '".number_format($data['civ'],2)."', '".number_format($data['taxes'],2)."', '".time()."')");
}

?>