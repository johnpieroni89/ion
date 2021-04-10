<?php

$db = new database;
$db->connect();
echo "<center><h1><b>Reports</b></h1></center><hr/>";

if($_SESSION['user_privs']['reporting_view'] > 0 || $_SESSION['user_privs']['admin'] != 0){
	if(isset($_POST['search'])){
		$search = mysqli_real_escape_string($db->connection,$_POST['search']);
		if($_SESSION['user_privs']['admin'] != 0){ //View All and author
			$reports = mysqli_query($db->connection,"SELECT reporting_reports.*, users.username FROM reporting_reports LEFT JOIN users ON users.user_id = reporting_reports.author WHERE (users.username LIKE '%".$search."%' OR serial LIKE '%".$search."%' OR title LIKE '%".$search."%' OR content LIKE '%".$search."%') ORDER BY timestamp DESC");
		}else if($_SESSION['user_privs']['reporting_view'] == 3){ //View all
			$reports = mysqli_query($db->connection,"SELECT * FROM reporting_reports WHERE (serial LIKE '%".$search."%' OR title LIKE '%".$search."%' OR content LIKE '%".$search."%') ORDER BY timestamp DESC");
		}else if($_SESSION['user_privs']['reporting_view'] == 2){ //View COI
			$coi_list = array();
			$groups = mysqli_query($db->connection,"SELECT usergroup_id FROM usergroups_members WHERE user_id = ".$_SESSION['user_id']."");
			while($row = mysqli_fetch_assoc($groups)){
				$group_cois = mysqli_query($db->connection, "SELECT need_id FROM reporting_needs_coi_usergroups WHERE usergroup_id = '".$row['usergroup_id']."'");
				while($row_2 = mysqli_fetch_assoc($group_cois)){
					array_push($coi_list,"need_id = '".$row_2['need_id']."'");
				}
			}
			$user_cois = mysqli_query($db->connection, "SELECT need_id FROM reporting_needs_coi_users WHERE user_id = '".$_SESSION['user_id']."'");
			while($row = mysqli_fetch_assoc($user_cois)){
				array_push($coi_list,"need_id = '".$row['need_id']."'");
			}
			$author_cois = mysqli_query($db->connection, "SELECT need_id FROM reporting_needs WHERE author = '".$_SESSION['user_id']."'");
			while($row = mysqli_fetch_assoc($author_cois)){
				array_push($coi_list,"need_id = '".$row['need_id']."'");
			}
			$coi_list = array_unique($coi_list);
			$coi_list = trim(implode(" OR ", $coi_list));
			$needs = mysqli_query($db->connection, "SELECT report_id FROM reporting_reports_coi WHERE (".$coi_list.")");
			$report_list = array();
			while($row = mysqli_fetch_assoc($needs)){
				array_push($report_list,"report_id = '".$row['report_id']."'");
			}
			$report_list = array_unique($report_list);
			$report_list = trim(implode(" OR ", $report_list));
			$reports = mysqli_query($db->connection,"SELECT * FROM reporting_reports WHERE (".$report_list.") AND (serial LIKE '%".$search."%' OR title LIKE '%".$search."%' OR content LIKE '%".$search."%') ORDER BY timestamp DESC");
		}else{ //View self
			$reports = mysqli_query($db->connection,"SELECT * FROM reporting_reports WHERE author = '".$_SESSION['user_id']."' ORDER BY timestamp DESC");
		}
		mysqli_query($db->connection, "INSERT INTO logs_activities (user_id, log_type, details, timestamp) VALUES ('".$_SESSION['user_id']."', '4', 'SEARCH: Broad search on reports containing \"$search\"', '".swc_time(time(),TRUE)["timestamp"]."')");
	}else{
		if($_SESSION['user_privs']['admin'] != 0){ //View All and author
			$reports = mysqli_query($db->connection,"SELECT reporting_reports.*, users.username FROM reporting_reports LEFT JOIN users ON users.user_id = reporting_reports.author ORDER BY timestamp DESC");
		}else if($_SESSION['user_privs']['reporting_view'] == 3){ //View all
			$reports = mysqli_query($db->connection,"SELECT * FROM reporting_reports ORDER BY timestamp DESC");
		}else if($_SESSION['user_privs']['reporting_view'] == 2){ //View COI
			$coi_list = array();
			$groups = mysqli_query($db->connection,"SELECT usergroup_id FROM usergroups_members WHERE user_id = ".$_SESSION['user_id']."");
			while($row = mysqli_fetch_assoc($groups)){
				$group_cois = mysqli_query($db->connection, "SELECT need_id FROM reporting_needs_coi_usergroups WHERE usergroup_id = '".$row['usergroup_id']."'");
				while($row_2 = mysqli_fetch_assoc($group_cois)){
					array_push($coi_list,"need_id = '".$row_2['need_id']."'");
				}
			}
			$user_cois = mysqli_query($db->connection, "SELECT need_id FROM reporting_needs_coi_users WHERE user_id = '".$_SESSION['user_id']."'");
			while($row = mysqli_fetch_assoc($user_cois)){
				array_push($coi_list,"need_id = '".$row['need_id']."'");
			}
			$author_cois = mysqli_query($db->connection, "SELECT need_id FROM reporting_needs WHERE author = '".$_SESSION['user_id']."'");
			while($row = mysqli_fetch_assoc($author_cois)){
				array_push($coi_list,"need_id = '".$row['need_id']."'");
			}
			$coi_list = array_unique($coi_list);
			$coi_list = trim(implode(" OR ", $coi_list));
			$needs = mysqli_query($db->connection, "SELECT report_id FROM reporting_reports_coi WHERE (".$coi_list.")");
			$report_list = array();
			while($row = mysqli_fetch_assoc($needs)){
				array_push($report_list,"report_id = '".$row['report_id']."'");
			}
			$report_list = array_unique($report_list);
			$report_list = trim(implode(" OR ", $report_list));
			$reports = mysqli_query($db->connection,"SELECT * FROM reporting_reports WHERE author = '".$_SESSION['user_id']."' OR (".$report_list.") ORDER BY timestamp DESC");
		}else{ //View self
			$reports = mysqli_query($db->connection,"SELECT * FROM reporting_reports WHERE author = '".$_SESSION['user_id']."' ORDER BY timestamp DESC");
		}
	}
	
	if(mysqli_num_rows($reports) != 0){
		echo "<table class=\"table table-bordered table-striped table-responsive table-hover\"><tr><th>Serial</th><th>Focus</th><th>Title</th>";
		if($_SESSION['user_privs']['admin'] != 0){ echo "<th>Author</th>";}
		echo "<th>Timestamp</th></tr>";

		while($data = mysqli_fetch_assoc($reports)){
			$report_id = $data['report_id'];
			$serial = $data['serial'];
			$title = $data['title'];
			$author = ucwords($data['username']);
			$timestamp = swc_time($data['timestamp']);

			if($data['focus'] == "NIL"){
				$focus = "None";
			}else if($data['focus'] == "ECO"){
				$focus = "Economy";
			}else if($data['focus'] == "IND"){
				$focus = "Industry";
			}else if($data['focus'] == "INS"){
				$focus = "Instability";
			}else if($data['focus'] == "MIL"){
				$focus = "Military";
			}else if($data['focus'] == "OPS"){
				$focus = "Operations";
			}else if($data['focus'] == "POL"){
				$focus = "Politics";
			}else if($data['focus'] == "SOC"){
				$focus = "Social";
			}else if($data['focus'] == "TEC"){
				$focus = "Technology";
			}

			echo "<tr><td><a href=\"reporting.php?view=1&serial=".$serial."\">".$serial."</a></td><td>".$focus."</td><td>".$title."";
			if (file_exists("reports/".$serial."/")){
					echo "<i class=\"pull-right fas fa-paperclip\"></i>";
			}
			echo "</td>";
			if($_SESSION['user_privs']['admin'] != 0){ echo "<td>".$author."</td>";}
			echo "<td>Year ".$timestamp['year']." Day ".$timestamp['day']." ".$timestamp['hour'].":".$timestamp['minute']."</td></tr>";
		}

		echo "</table>";
	}else{
		echo "<center>No results</center>";
	}
}else{
	echo "<center>No results!</center>";
}


?>

