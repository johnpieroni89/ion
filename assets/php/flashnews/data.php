<?php
    global $db;
	if(isset($_POST['search'])){
		$search = $_POST['search'];
		$query = "SELECT title, description, timestamp FROM flashnews WHERE title LIKE '%$search%' OR description LIKE '%$search%' ORDER BY timestamp DESC";
		mysqli_query($db->connection, "INSERT INTO logs_activities (user_id, log_type, details, timestamp) VALUES ('".$_SESSION['user_id']."', '6', 'SEARCH: Broad search on \"".$search."\"', '".swc_time(time(),TRUE)["timestamp"]."')");
	}elseif(isset($_GET['search'])){
		$search = $_GET['search'];
		$query = "SELECT title, description, timestamp FROM flashnews WHERE title LIKE '%$search%' OR description LIKE '%$search%' ORDER BY timestamp DESC";
		mysqli_query($db->connection, "INSERT INTO logs_activities (user_id, log_type, details, timestamp) VALUES ('".$_SESSION['user_id']."', '6', 'SEARCH: Broad search on \"".$search."\"', '".swc_time(time(),TRUE)["timestamp"]."')");
	}else{
		$query = "SELECT title, description, timestamp FROM flashnews ORDER BY timestamp DESC";
	}
	
	$results_count = mysqli_num_rows(mysqli_query($db->connection,$query));
	echo "<center><h2><b>Flashnews Feed</b></h2></center>";
	
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
				<th width=\"100\">Timestamp</th>
				<th>Title</th>
				<th>Description</th>
			</tr>
		";

		$count = 1 + $pages->limit_start;
		while($data = mysqli_fetch_assoc($query)){
			$time = swc_time($data["timestamp"], TRUE);
			
			echo "
				<tr>
					<td style=\"width:200px;\" align=\"center\">".$time['date']."</td>
					<td style=\"width:400px;\">".$data['title']."</td>
					<td>".str_replace("<a href=\"/", "<a target=\"_blank\" href=\"https:\/\/www.swcombine.com/", $data['description'])."</td>
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