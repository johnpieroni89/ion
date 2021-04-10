<?php
if(!empty($_SESSION['user_privs']['signalsanalysis_search']) || !empty($_SESSION['user_privs']['admin'])){
	$query_assetdata = mysqli_query($db->connection,"SELECT COUNT(data_signalsanalysis.uid) AS assetCount, entities_classes.uid AS assetClassId, entities_classes.class AS assetClass FROM data_signalsanalysis LEFT JOIN entities ON data_signalsanalysis.type = entities.uid LEFT JOIN entities_classes ON entities.class = entities_classes.uid WHERE owner = '$handle' GROUP BY entities_classes.class ORDER BY entities_classes.uid ASC");
	
	echo "
		<div class=\"block\" style=\"overflow:hidden;\">
			<div class=\"block-title bg-primary\">
				<h2>Asset Data</h2>
			</div>
			<div class=\"block-content col-sm-12\" style=\"margin:0px;padding:0px;\">
				<table class=\"table table-bordered table-striped table-responsive table-hover\">
					<tr><th>Type</th><th>Amount</th></tr>
	";
	
	if(mysqli_num_rows($query_assetdata) != 0){
		while($row = mysqli_fetch_assoc($query_assetdata)){
			$class = $row['assetClass'];
			if(empty($row['assetClass'])){ $class = "Station";}
			
			echo "<tr><td><a target=\"_blank\" href=\"signalsanalysis.php?type=Search+Details&inputQueryType=Standard&inputOwner=".urlencode($handle)."&inputClass=".urlencode($row['assetClassId'])."\">".$class."</a></td><td>".$row['assetCount']."</td></tr>";
		}
	}else{
		echo "<tr><td colspan=\"2\"><center><h2>No data</h2></center></td></tr>";
	}
	
	echo "
				</table>
			</div>
		</div>
	";
}

?>