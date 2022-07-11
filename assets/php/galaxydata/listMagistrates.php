<?php
	echo "
		<h2><center><b>Magistrate List</b></center></h2>
		<div class=\"hidden-print\">Note: Click on Column headers for sorting.</div>
		<table id=\"sortTable\" class=\"table table-bordered table-striped table-responsive table-hover sortable\">
		<tr>
			<th>Magistrate Name</th>
			<th align=\"center\">Planet Count</th>
		</tr>
	";
	$query = mysqli_query($db->connection,"SELECT COUNT(uid) AS count, magistrate FROM galaxy_planets WHERE magistrate != '' GROUP BY magistrate ORDER BY magistrate");
	while($data = mysqli_fetch_assoc($query)){
		echo "
			<tr>
				<td><a target=\"_blank\" href=\"galaxydata.php?type=search&inputSearchType=planets&inputMagistrate=".urlencode($data['magistrate'])."\">".$data['magistrate']."</a></td>
				<td align=\"center\">".number_format($data['count'])."</td>
			</tr>";
	}
	echo "</table>";
?>