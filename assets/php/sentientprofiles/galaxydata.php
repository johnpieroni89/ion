<?php
if(!empty($_SESSION['user_privs']['galaxydata_search']) || !empty($_SESSION['user_privs']['admin'])){
	$query_galaxydata = mysqli_query($db->connection,"SELECT * FROM galaxy_planets WHERE governor = '$handle' OR magistrate = '$handle' ORDER BY name ASC");
	
	echo "
	<div class=\"block\" style=\"overflow:hidden;\">
		<div class=\"block-title bg-primary\">
			<h2>Galaxy Data</h2>
		</div>
		<div class=\"block-content col-sm-12\" style=\"margin:0px;padding:0px;\">
			<table class=\"table table-bordered table-striped table-responsive table-hover\">
				<tr><th>Planet</th><th>Assignment</th></tr>
	";
	
	if(mysqli_num_rows($query_galaxydata) != 0){
		//include("assets/php/sentientprofiles/galaxydata.php");
		while($row = mysqli_fetch_assoc($query_galaxydata)){
			if($row['governor'] == $handle){
				echo "<tr><td><a target=\"_blank\" href=\"https://www.swcombine.com/rules/?Galaxy_Map&planetID=".explode(":",$row['uid'])[1]."\">".$row['name']."</a></td><td>Governor</td></tr>";
			}elseif($row['magistrate'] == $handle){
				echo "<tr><td><a target=\"_blank\" href=\"https://www.swcombine.com/rules/?Galaxy_Map&planetID=".explode(":",$row['uid'])[1]."\">".$row['name']."</a></td><td>Magistrate</td></tr>";
			}
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