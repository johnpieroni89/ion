<?php
	$query_buffer = mysqli_query($db->connection, "SELECT data_signalsanalysis_activity.*, users.username FROM data_signalsanalysis_activity LEFT JOIN users ON users.user_id = data_signalsanalysis_activity.activity_collector WHERE data_signalsanalysis_activity.status = '0' ORDER BY activity_time DESC");
	if(mysqli_num_rows($query_buffer) != 0){
		if($_SESSION["user_privs"]["admin"] > 0){$admin_buffer_head = "<th>Collector</th>";}else{$admin_buffer_head = "";}
		echo "
			<h2>Buffer</h2>
			<table class=\"table table-bordered table-striped table-responsive table-hover\">
			<tr>".$admin_buffer_head."<th>Activity ID (Click to review)</th><th>Entities</th><th>Action</th></tr>
		";
		while($row = mysqli_fetch_assoc($query_buffer)){
			if($_SESSION["user_privs"]["admin"] > 0){$admin_buffer_row = "<td>".$row["username"]."</td>";}else{$admin_buffer_row = "";}
			echo "<tr>".$admin_buffer_row."<td><a href=\"signalsanalysis.php?type=buffer+review&activity_id=".$row["activity_id"]."\" target=\"_blank\">".$row["activity_id"]."</a></td><td>".$row["added"]."</td><td><form method=\"post\" action=\"\"><input type=\"submit\" class=\"btn btn-primary\" style=\"margin:2px;\" name=\"action\" value=\"Approve\"><input type=\"submit\" class=\"btn btn-danger\" style=\"margin:2px;\" name=\"action\" value=\"Deny\"><input type=\"hidden\" name=\"activity_id\" value=\"".$row["activity_id"]."\"></form></td></tr>";
		}
		echo "</table>";
	}else{
		echo "<center><h2>No results</h2></center>";
	}

?>