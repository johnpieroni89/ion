<?php
	$db = new database;
	$db->connect();
	
	if(isset($_POST['search'])){
		$search = $_POST['search'];
		$query = "SELECT data_tracking.*, data_signalsanalysis.name AS entity_name, galaxy_sectors.name AS sector_name, galaxy_systems.name AS system_name, galaxy_planets.name AS planet_name FROM data_tracking LEFT JOIN data_signalsanalysis ON data_tracking.entity = data_signalsanalysis.uid LEFT JOIN galaxy_sectors ON data_tracking.sector = galaxy_sectors.uid LEFT JOIN galaxy_systems ON data_tracking.system = galaxy_systems.uid LEFT JOIN galaxy_planets ON data_tracking.planet = galaxy_planets.uid WHERE tracking_id = '$search' OR target LIKE '%$search%' OR source = '$search' OR data_signalsanalysis.name LIKE '%$search%' OR galaxy_sectors.name LIKE '%$search%' OR galaxy_systems.name LIKE '%$search%' OR galaxy_planets.name LIKE '%$search%' ORDER BY timestamp DESC, tracking_id DESC";
		mysqli_query($db->connection, "INSERT INTO logs_activities (user_id, log_type, details, timestamp) VALUES ('".$_SESSION['user_id']."', '8', 'SEARCH: Broad search on \"".$search."\"', '".swc_time(time(),TRUE)["timestamp"]."')");
	}elseif(isset($_GET['search'])){
		$search = $_GET['search'];
		$query = "SELECT data_tracking.*, data_signalsanalysis.name AS entity_name, galaxy_sectors.name AS sector_name, galaxy_systems.name AS system_name, galaxy_planets.name AS planet_name FROM data_tracking LEFT JOIN data_signalsanalysis ON data_tracking.entity = data_signalsanalysis.uid LEFT JOIN galaxy_sectors ON data_tracking.sector = galaxy_sectors.uid LEFT JOIN galaxy_systems ON data_tracking.system = galaxy_systems.uid LEFT JOIN galaxy_planets ON data_tracking.planet = galaxy_planets.uid WHERE tracking_id = '$search' OR target LIKE '%$search%' OR source = '$search' OR data_signalsanalysis.name LIKE '%$search%' OR galaxy_sectors.name LIKE '%$search%' OR galaxy_systems.name LIKE '%$search%' OR galaxy_planets.name LIKE '%$search%' ORDER BY timestamp DESC, tracking_id DESC";
		mysqli_query($db->connection, "INSERT INTO logs_activities (user_id, log_type, details, timestamp) VALUES ('".$_SESSION['user_id']."', '8', 'SEARCH: Broad search on \"".$search."\"', '".swc_time(time(),TRUE)["timestamp"]."')");
	}else{
		$query = "SELECT data_tracking.*, data_signalsanalysis.name AS entity_name, galaxy_sectors.name AS sector_name, galaxy_systems.name AS system_name, galaxy_planets.name AS planet_name FROM data_tracking LEFT JOIN data_signalsanalysis ON data_tracking.entity = data_signalsanalysis.uid LEFT JOIN galaxy_sectors ON data_tracking.sector = galaxy_sectors.uid LEFT JOIN galaxy_systems ON data_tracking.system = galaxy_systems.uid LEFT JOIN galaxy_planets ON data_tracking.planet = galaxy_planets.uid ORDER BY timestamp DESC, tracking_id DESC";
	}
	
	$results_count = mysqli_num_rows(mysqli_query($db->connection,$query));
	echo "<center><h2><b>Geolocation Data</b></h2></center>";
	
	if($results_count != 0){
		
		$pages = new Paginator($results_count,9);
		
		echo "<div class=\"col-sm-6 text-left pull-left\" style=\"font-size:18px;\"><span class=\"form-inline\"><b>Results: ".number_format($results_count)."</b></span></div>";

		echo '<div class="col-sm-6 text-right pull-right hidden-print">';
		echo "<span class=\"form-inline\">".$pages->display_jump_menu().$pages->display_items_per_page()."</span>";
		echo '</div>';
		echo '<div class="clearfix"></div>';
		
		$limit = $pages->limit_start.','.$pages->limit_end;
		$query = mysqli_query($db->connection,$query." LIMIT ".$limit);
		
		echo "
			<table class=\"table table-bordered table-striped table-responsive table-hover sortable\" style=\"font-size:14px;\">
			<tr>
				<th>Timestamp</th>
				<th width=\"100\">Tracking #</th>
				<th>Target</th>
				<th>Entity</th>
				<th width=\"50\">Confidence</th>
				<th>Location</th>
				<th>Source</th>
			</tr>
		";

		$count = 1 + $pages->limit_start;
		while($data = mysqli_fetch_assoc($query)){
			$time = swc_time($data["timestamp"])["date"];
			$link = "<a target=\"_blank\" href=\"signalsanalysis.php?inputQueryType=Standard&inputID=".urlencode(explode(":", $data['entity'])[1])."&type=Search+Details\">";
			$confidence = array('1 - Low', '2 - Medium', '3 - High');
			if(isset($data['sector'])){ $loc_sec = "<a target=\"blank\" href=\"http://www.swcombine.com/rules/?Galaxy_Map&sectorID=".explode(":",$data['sector'])[1]."\">".$data['sector_name']."</a>";}else{ $loc_sec = "Deepspace";}
			if(isset($data['system'])){ $loc_sys = "<a target=\"blank\" href=\"http://www.swcombine.com/rules/?Galaxy_Map&systemID=".explode(":",$data['system'])[1]."\">".$data['system_name']."</a>";}else{ $loc_sys = "Deepspace";}
			if(isset($data['planet'])){ $loc_planet = "<a target=\"blank\" href=\"http://www.swcombine.com/rules/?Galaxy_Map&planetID=".explode(":",$data['planet'])[1]."\">".$data['planet_name']."</a>";}else{ $loc_planet = "Space ";}
			if(isset($data['atmox'])){ $atmosphere = "<br/>Atmosphere Position: (".$data['atmox'].", ".$data['atmoy'].")";}else{ $atmosphere = "";}
			if(isset($data['surfx'])){ $surface = "<br/>Surface Position: (".$data['surfx'].", ".$data['surfy'].")";}else{ $surface = "";}
			if($data['source'] != "Manual Entry"){
				$source = "<a target=\"_blank\" href=\"signalsanalysis.php?activity=".$data['source']."\">".$data['source']."</a>";
			}else{
				$source = "Manual Entry";
			}
			
			echo "
				<tr>
					<td align=\"center\">".$time."</td>
					<td align=\"center\">".$data['tracking_id']."</td>
					<td align=\"center\">".$data['target']."</td>
					<td align=\"center\">".$link.utf8_decode($data['entity_name'])."<br/>".$data['entity']."</a></td>
					<td align=\"center\">".$confidence[$data['confidence'] - 1]."</td>
					<td>".$loc_sec."<br/>".$loc_sys." (".$data['galx'].", ".$data['galy'].")<br/>".$loc_planet." (".$data['sysx'].", ".$data['sysy'].")".$atmosphere."".$surface."</td>
					<td align=\"center\">".$source."</td>
				</tr>";
			$count++;
		}
		echo "</table>";
		
		echo '<div class="col-sm-6 text-right pull-right hidden-print">';
		echo "<span class=\"form-inline\">".$pages->display_jump_menu().$pages->display_items_per_page()."</span>";
		echo '</div>';
		echo '<div class="clearfix"></div>';
		
	}else{
		echo "<div class=\"col-sm-12\"><center><h3><b>No results</b></h3></center></div>";
	}
	
?>