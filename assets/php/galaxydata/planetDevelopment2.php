<?php

$from = mysqli_real_escape_string($db->connection, $_GET['inputFrom']);
$to = mysqli_real_escape_string($db->connection, $_GET['inputTo']);
$controlledBy = mysqli_real_escape_string($db->connection, $_GET['inputControlled']);
//select all planets from latest timestamp and left join planet details -
//select all planets from earliest timestamp -
//iterate through each planet from latest timestamp -
//if planet exists in earliest timestamp, then compare the planet data and print it -

if(!empty($controlledBy)){
	$query_1 = mysqli_query($db->connection, "SELECT data_galaxydata_planets.*, galaxy_planets.size, galaxy_planets.name, galaxy_planets.img_small, galaxy_planets.controlled_by FROM data_galaxydata_planets LEFT JOIN galaxy_planets ON galaxy_planets.uid = data_galaxydata_planets.uid WHERE timestamp = '".$to."' AND controlled_by = '".$controlledBy."' ORDER BY galaxy_planets.name ASC");

	$query_2 = mysqli_query($db->connection, "SELECT data_galaxydata_planets.* FROM data_galaxydata_planets WHERE timestamp = '".$from."' ORDER BY uid ASC");
}else{
	$query_1 = mysqli_query($db->connection, "SELECT data_galaxydata_planets.*, galaxy_planets.size, galaxy_planets.name, galaxy_planets.img_small, galaxy_planets.controlled_by FROM data_galaxydata_planets LEFT JOIN galaxy_planets ON galaxy_planets.uid = data_galaxydata_planets.uid WHERE timestamp = '".$to."' ORDER BY galaxy_planets.name ASC");

	$query_2 = mysqli_query($db->connection, "SELECT data_galaxydata_planets.* FROM data_galaxydata_planets WHERE timestamp = '".$from."' ORDER BY uid ASC");
}

while($row_2 = mysqli_fetch_array($query_2)){
	$data_array[] = $row_2;
}
?>

	<h2><center><b>Planet Development</b></center></h2>
	<h4><center>From <?php echo swc_time($from, TRUE)['date']; ?> to <?php echo swc_time($to, TRUE)['date']; ?></center></h4>
	<div class="hidden-print">Note: Click on Column headers for sorting</div>
		<table class="table table-bordered table-striped table-responsive table-hover sortable">
		<tr>
			<th>Img</th>
			<th>Size</th>
			<th>Planet</th>
			<th>Owner</th>
			<th align="right">Population</th>
			<th align="right">Pop Diff</th>
			<th align="right">Pop Change</th>
			<th align="right">Cities</th>
			<th align="right">Cities Diff</th>
			<th align="right">Cities Change</th>
			<th align="right">Civ Lvl</th>
			<th align="right">Civ Diff</th>
			<th align="right">Civ Change</th>
		</tr>
	
<?php
	while($data_1 = mysqli_fetch_assoc($query_1)){
		if($data_2 = array_search($data_1['uid'], array_column($data_array, 'uid'))){
			if(($data_1['pop'] > $data_array[$data_2][2]) || ($data_1['cities'] > $data_array[$data_2][3]) || ($data_1['civ'] > $data_array[$data_2][4])){
				$popDiff = number_format($data_1['pop'] - $data_array[$data_2][2]);
				if($popDiff > 0){ $popDiff = "<span style=\"color:green;\">$popDiff</span>";}elseif($popDiff < 0){ $popDiff = "<span style=\"color:red;\">$popDiff</span>";}
				$popChange = number_format((number_format($data_1['pop'] / $data_array[$data_2][2],4) - 1) * 100, 2);
				if($popChange > 0){ $popChange = "<span style=\"color:green;\">$popChange</span>";}elseif($popChange < 0){ $popChange = "<span style=\"color:red;\">$popChange</span>";}
				$citiesDiff = number_format($data_1['cities'] - $data_array[$data_2][3]);
				if($citiesDiff > 0){ $citiesDiff = "<span style=\"color:green;\">$citiesDiff</span>";}elseif($citiesDiff < 0){ $citiesDiff = "<span style=\"color:red;\">$citiesDiff</span>";}
				$citiesChange = number_format((number_format($data_1['cities'] / $data_array[$data_2][3],4) - 1) * 100, 2);
				if($citiesChange > 0){ $citiesChange = "<span style=\"color:green;\">$citiesChange</span>";}elseif($citiesChange < 0){ $citiesChange = "<span style=\"color:red;\">$citiesChange</span>";}
				$civDiff = number_format($data_1['civ'] - $data_array[$data_2][4],2);
				if($civDiff > 0){ $civDiff = "<span style=\"color:green;\">$civDiff</span>";}elseif($civDiff < 0){ $civDiff = "<span style=\"color:red;\">$civDiff</span>";}
				$civChange = number_format((number_format($data_1['civ'] / $data_array[$data_2][4],4) - 1) * 100, 2);
				if($civChange > 0){ $civChange = "<span style=\"color:green;\">$civChange</span>";}elseif($civChange < 0){ $civChange = "<span style=\"color:red;\">$civChange</span>";}
				echo "
					<tr>
						<td align=\"center\"><img style=\"max-height:20px;\" src=\"".$data_1['img_small']."\"></td>
						<td align=\"center\">".$data_1['size']."</td>
						<td><a target=\"_blank\" href=\"http://www.swcombine.com/rules/?Galaxy_Map&planetID=".explode(":",$data_1['uid'])[1]."\">".$data_1['name']."</a></td>
						<td>".$data_1['controlled_by']."</td>
						<td align=\"right\">".number_format($data_1['pop'])."</td>
						<td align=\"right\">".$popDiff."</td>
						<td align=\"right\">".$popChange."%</td>
						<td align=\"right\">".number_format($data_1['cities'])."</td>
						<td align=\"right\">".$citiesDiff."</td>
						<td align=\"right\">".$citiesChange."%</td>
						<td align=\"right\">".$data_1['civ']."</td>
						<td align=\"right\">".$civDiff."</td>
						<td align=\"right\">".$civChange."%</td>
					</tr>";
			}
		}
	}
?>

</table>