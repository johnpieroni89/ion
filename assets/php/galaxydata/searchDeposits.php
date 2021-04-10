<?php
	$db = new database;
	$db->connect();
	
	echo "
		<center><b><h1>Material Deposits Search Summary</h1></b></center><hr>
		<h3>Total Materials</h3>
		<table class=\"table table-bordered table-striped table-responsive table-hover\">
		<tr><th align=\"right\">Material</th><th align=\"right\">Amount</th><th align=\"right\"># of Deposits</th></tr>
	";
	
	$query_deposits = mysqli_query($db->connection,$search_string);
	while($data = mysqli_fetch_assoc($query_deposits)){
		$query_deposits_count = mysqli_fetch_assoc(mysqli_query($db->connection, "SELECT COUNT(data_galaxydata_deposits.type) AS count FROM data_galaxydata_deposits LEFT JOIN galaxy_planets ON data_galaxydata_deposits.planet_uid = galaxy_planets.uid WHERE ".$where." AND data_galaxydata_deposits.type = '".$data['type']."'"))['count'];
		echo "<tr><td>".ucfirst($data['type'])."</td><td>".number_format($data['size'])."</td><td>".$query_deposits_count."</td></tr>";
	}
	echo "
		</table><hr>
	";
	
	$source_planets = mysqli_query($db->connection, $source_planets);
	
	echo "
		<h3>Source Planets</h3>
		<table class=\"table table-bordered table-striped table-responsive table-hover\">
		<tr><th align=\"right\">Planet</th><th align=\"right\">Details</th></tr>
	";
	
	while($data = mysqli_fetch_assoc($source_planets)){
		echo "<tr><td>".$data['name']."</td><td><a href=\"galaxydata.php?planet_uid=".$data['uid']."&type=deposits\" target=\"blank\">View Source</a></td></tr>";
	}
	echo "
		</table><hr>
	";
	
	$db->disconnect();
	unset($db);
?>