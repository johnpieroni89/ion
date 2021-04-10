<?php

	echo "
		<h2><center><b>Galaxy Overview</b></center></h2>
		<div class=\"hidden-print\">Note: Click on Column headers for sorting.</div>
		<table id=\"sortTable\" class=\"table table-bordered table-striped table-responsive table-hover sortable\">
		<tr>
			<th>Faction</th>
			<th align=\"right\">Total Population</th>
			<th align=\"right\">Sectors</th>
			<th align=\"right\">Systems</th>
			<th align=\"right\">Planets</th>
			<th align=\"right\">Visible Cities</th>
			<th align=\"right\">Avg Civ Lvl</th>
			<th align=\"right\">Avg Tax Lvl</th>
		</tr>
	";
	$db = new database;
	$db->connect();
	$query = mysqli_query($db->connection,$search_string);
	while($data = mysqli_fetch_assoc($query)){
		$query_sectors = mysqli_fetch_assoc(mysqli_query($db->connection,"SELECT COUNT(uid) as count FROM galaxy_sectors WHERE controlled_by = '".$data['controlled_by']."'"))["count"];
		$query_systems = mysqli_fetch_assoc(mysqli_query($db->connection,"SELECT COUNT(uid) as count FROM galaxy_systems WHERE controlled_by = '".$data['controlled_by']."'"))["count"];
		echo "
			<tr>
				<td>".(($data['controlled_by']=="")?"(Independent Planets)":($data['controlled_by']))."</td>
				<td align=\"right\">".number_format($data['pop'])."</td>
				<td align=\"right\">".number_format($query_sectors)."</td>
				<td align=\"right\">".number_format($query_systems)."</td>
				<td align=\"right\">".number_format($data['planets'])."</td>
				<td align=\"right\">".number_format($data['cities'])."</td>
				<td align=\"right\">".number_format($data['civ'],2)."</td>
				<td align=\"right\">".number_format($data['taxes'],2)."</td>
			</tr>";
		if(isset($_POST['action'])){
			mysqli_query($db->connection,"INSERT INTO data_galaxydata (faction, total_population, sectors, systems, planets, avg_population, avg_civilization, avg_tax, total_tax_income, timestamp) VALUES ('".$data['faction']."', '".$data['pop']."', '".$query_sectors."', '".$query_systems."', '".$data['planets']."', '".$data['pop'] / $data['planets']."', '".number_format($data['civ'],2)."', '".number_format($data['taxes'],2)."', '".$data['taxIncome']."', '".time()."')");
		}
	}
	$db->disconnect();
	unset($db);
	echo "</table>";

?>