<?php
error_reporting(0);
include("../database.php");
$db = new database;
$db->connect();

mysqli_query($db->connection,"DELETE FROM data_galaxydata_planets WHERE timestamp <= '".(time() - 31449600)."'");
$time = time();

$query = mysqli_query($db->connection, "SELECT uid, population, cities, civilization FROM galaxy_planets WHERE type <> 'sun' AND type <> 'black hole'"); // 3270 instead of 5725

while ($row = mysqli_fetch_assoc($query)){
	mysqli_query($db->connection,"INSERT INTO data_galaxydata_planets (uid, pop, cities, civ, timestamp) VALUES ('".$row['uid']."', '".$row['population']."', '".$row['cities']."', '".$row['civilization']."', '".$time."')");
}

?>