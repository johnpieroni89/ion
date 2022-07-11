<?php
	
	if($_SESSION['user_privs']['galaxydata_delete'] > 0 || $_SESSION['user_privs']['admin'] > 0){
		echo "<form method=\"post\" action=\"\" id=\"dataForm\">";
		echo "
			<button title=\"Select All\" type=\"button\" class=\"btn btn-primary\" id=\"checkAll\">
				<span class=\"far fa-check-square\"></span> Select All
			</button>
			<button title=\"Select All\" type=\"button\" class=\"btn btn-primary\" id=\"uncheckAll\">
				<span class=\"far fa-square\"></span> Unselect All
			</button>
		";
		echo "<input type=\"submit\" class=\"btn btn-warning fa fa-input\" style=\"height:34px;margin:1px;\" name=\"Delete\" value=\"&#xf1f8 Delete\" onclick=\"return confirm('Do you really want to delete the selected records?');\">";
	}
	echo "
		<table class=\"table table-bordered table-striped table-responsive table-hover\">
		<tr>
			<th>#</th>
			<th>Faction</th>
			<th align=\"right\">Total Population</th>
			<th align=\"right\">Sectors</th>
			<th align=\"right\">Systems</th>
			<th align=\"right\">Planets</th>
			<th align=\"right\">Visible Cities</th>
			<th align=\"right\">Avg Civ Lvl</th>
			<th align=\"right\">Avg Tax Lvl</th>
			<th>Date</th>
		</tr>
	";
	$query = mysqli_query($db->connection,$search_string_current);
	while($data = mysqli_fetch_assoc($query)){
		$query_sectors = mysqli_fetch_assoc(mysqli_query($db->connection,"SELECT COUNT(uid) as count FROM galaxy_sectors WHERE controlled_by = '".$data['controlled_by']."'"))["count"];
		$query_systems = mysqli_fetch_assoc(mysqli_query($db->connection,"SELECT COUNT(uid) as count FROM galaxy_systems WHERE controlled_by = '".$data['controlled_by']."'"))["count"];
		echo "
			<tr>
				<td>1</td>
				<td>".$data['controlled_by']."</td>
				<td align=\"right\">".number_format($data['pop'])."</td>
				<td align=\"right\">".number_format($query_sectors)."</td>
				<td align=\"right\">".number_format($query_systems)."</td>
				<td align=\"right\">".number_format($data['planets'])."</td>
				<td align=\"right\">".number_format($data['cities'])."</td>
				<td align=\"right\">".number_format($data['civ'],2)."</td>
				<td align=\"right\">".number_format($data['taxes'],2)."</td>
				<td>(Current)</td>
			</tr>";
	}
	$query = mysqli_query($db->connection,$search_string_history);
	$count = 2;
	while($data = mysqli_fetch_assoc($query)){
		$year = swc_time($data['timestamp'],TRUE)['year'];
		$day = swc_time($data['timestamp'],TRUE)['day'];
		$hour = swc_time($data['timestamp'],TRUE)['hour'];
		$minute = swc_time($data['timestamp'],TRUE)['minute'];
		if($_SESSION['user_privs']['galaxydata_delete'] > 0 || $_SESSION['user_privs']['admin'] > 0){
			$checkbox = "<input style=\"float:right;\" type=\"checkbox\" name=\"entries[]\" value=\"".$data['id']."\">";
		}else{$delete_checkbox = "";}
		echo "
			<tr>
				<td>".$count.$checkbox."</td>
				<td>".$data['factionName']."</td>
				<td align=\"right\">".number_format($data['total_population'])."</td>
				<td align=\"right\">".number_format($data['sectors'])."</td>
				<td align=\"right\">".number_format($data['systems'])."</td>
				<td align=\"right\">".number_format($data['planets'])."</td>
				<td align=\"right\">".number_format($data['cities'])."</td>
				<td align=\"right\">".number_format($data['avg_civilization'],2)."</td>
				<td align=\"right\">".number_format($data['avg_tax'],2)."</td>
				<td>Year ".$year." Day ".$day." ".$hour.":".$minute."</td>
				</tr>";
		$count++;
	}
	echo "</table>";
	if($_SESSION['user_privs']['galaxydata_delete'] > 0 || $_SESSION['user_privs']['admin'] > 0){
		echo "
			<button title=\"Select All\" type=\"button\" class=\"btn btn-primary\" id=\"checkAll2\">
				<span class=\"far fa-check-square\"></span> Select All
			</button>
			<button title=\"Select All\" type=\"button\" class=\"btn btn-primary\" id=\"uncheckAll2\">
				<span class=\"far fa-square\"></span> Unselect All
			</button>
		";
		echo "<input type=\"submit\" class=\"btn btn-warning fa fa-input\" style=\"height:34px;margin:1px;\" name=\"Delete\" value=\"&#xf1f8 Delete\" onclick=\"return confirm('Do you really want to delete the selected records?');\">";
		echo "</form>";
	}

?>