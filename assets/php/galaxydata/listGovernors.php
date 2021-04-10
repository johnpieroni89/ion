<?php
	
	echo "
		<h2><center><b>Governor List</b></center></h2>
		<div class=\"hidden-print\">Note: Click on Column headers for sorting.</div>
		<table id=\"sortTable\" class=\"table table-bordered table-striped table-responsive table-hover sortable\">
		<tr>
			<th>Governor Name</th>
			<th align=\"center\">Planet Count</th>
		</tr>
	";
	$db = new database;
	$db->connect();
	$query = mysqli_query($db->connection,"SELECT COUNT(uid) AS count, governor FROM galaxy_planets WHERE governor != '' GROUP BY governor ORDER BY governor");
	while($data = mysqli_fetch_assoc($query)){
		echo "
			<tr>
				<td><a target=\"_blank\" href=\"galaxydata.php?type=search&inputSearchType=planets&inputGovernor=".urlencode($data['governor'])."\">".$data['governor']."</a></td>
				<td align=\"center\">".number_format($data['count'])."</td>
			</tr>";
	}
	echo "</table>";

?>