<?php
	$db = new database;
	$db->connect();
	
	$planet_uid = mysqli_real_escape_string($db->connection,$_GET['planet_uid']);
	$query_planet = mysqli_fetch_assoc(mysqli_query($db->connection,"SELECT name FROM galaxy_planets WHERE uid = '".$planet_uid."'"))["name"];
	echo "
		<center><b><h1>".$query_planet."</h1></b></center><hr>
		<h3>Total Materials</h3>
		<table class=\"table table-bordered table-striped table-responsive table-hover\">
		<tr><th align=\"right\">Material</th><th align=\"right\">Amount</th></tr>
	";
	
	$query_deposits = mysqli_query($db->connection,"SELECT type, SUM(size) as size FROM data_galaxydata_deposits WHERE planet_uid='".$planet_uid."' GROUP BY type ORDER BY type");
	while($data = mysqli_fetch_assoc($query_deposits)){
		echo "<tr><td>".ucfirst($data['type'])."</td><td>".number_format($data['size'])."</td></tr>";
	}
	echo "
		</table><hr>
	";
	
	$query_deposits = mysqli_query($db->connection,"SELECT type, size, x, y, timestamp FROM data_galaxydata_deposits WHERE planet_uid='".$planet_uid."' ORDER BY x, y");
	
	echo "
		<h3>Material Deposits</h3>
		<table class=\"table table-bordered table-striped table-responsive table-hover\">
		<tr><th align=\"right\">X</th><th align=\"right\">Y</th><th align=\"right\">Material</th><th align=\"right\">Amount</th></tr>
	";
	
	while($data = mysqli_fetch_assoc($query_deposits)){
		echo "<tr><td>".$data['x']."</td><td>".$data['y']."</td><td>".ucfirst($data['type'])."</td><td>".number_format($data['size'])."</td></tr>";
	}
	echo "
		</table><hr>
	";
	
	
	$db->disconnect();
	unset($db);
	

?>