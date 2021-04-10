<?php

	$db = new database;
	$db->connect();
	$query_types = mysqli_query($db->connection,"SELECT * FROM entities_classes WHERE (uid LIKE '301:%' OR uid LIKE '302:%' OR uid LIKE '303:%') AND (class NOT LIKE 'CP Bonus%' AND class NOT LIKE 'Rare%' AND class <> 'Debris' AND class <> 'Wrecks')");
	echo "<center><h1><b>".ucwords($owner)." assets</b></h1></center><div class=\"col-sm-6 col-sm-offset-3\">";
	while($type = mysqli_fetch_assoc($query_types)){
		$query_type_set = mysqli_query($db->connection,"SELECT COUNT(data_signalsanalysis.type) AS count, entities_classes.class AS ent_class, entities.name AS type_name, entities.uid AS entity FROM data_signalsanalysis LEFT JOIN entities ON data_signalsanalysis.type = entities.uid LEFT JOIN entities_classes ON entities.class = entities_classes.uid WHERE owner = '".$owner."' AND entities_classes.uid = '".$type["uid"]."'".$filter." GROUP BY entities.name ORDER BY entities_classes.uid IS NULL, entities_classes.uid ASC, entities.name ASC");
		if(!empty($query_type_set)){
			if(explode(":",$type["uid"])[0] == 303){$class = mysqli_fetch_assoc(mysqli_query($db->connection,"SELECT class FROM entities_classes WHERE uid = '303:".(explode(":",$type["uid"])[1] - 1)."'"))["class"];}else{$class = $type["class"];}
			echo "
			<h2>".$class."</h2>
			<table class=\"table table-bordered table-striped table-responsive table-hover\">
			<tr><th class=\"col-xs-9\">Type</th><th class=\"col-xs-3\">Count</th></tr>
			";
			while($type_set = mysqli_fetch_assoc($query_type_set)){
				echo "<tr><td class=\"col-xs-9\"><a target=\"_blank\" href=\"signalsanalysis.php?page=1&ipp=200&type=Search+Details&inputQueryType=Standard&inputOwner=".urlencode($owner)."&inputType=".$type_set["entity"]."\">".$type_set["type_name"]."</td><td class=\"col-xs-3\">".$type_set["count"]."</td></tr>";
			}
			echo "</table>";
		}
	}
	$query_type_set = mysqli_query($db->connection,"SELECT COUNT(data_signalsanalysis.type) AS count, entities_classes.class AS ent_class, entities.name AS type_name, entities.uid AS entity FROM data_signalsanalysis LEFT JOIN entities ON data_signalsanalysis.type = entities.uid LEFT JOIN entities_classes ON entities.class = entities_classes.uid WHERE owner = '".$owner."' AND entities_classes.uid IS NULL".$filter." GROUP BY entities.name ORDER BY entities_classes.uid IS NULL, entities_classes.uid ASC, entities.name ASC");
	if(!empty($query_type_set)){
		echo "
			<h2>Stations</h2>
			<table class=\"table table-bordered table-striped table-responsive table-hover\">
			<tr><th class=\"col-xs-9\">Type</th><th class=\"col-xs-3\">Count</th></tr>
		";
		while($type_set = mysqli_fetch_assoc($query_type_set)){
			echo "<tr><td class=\"col-xs-9\"><a target=\"_blank\" href=\"signalsanalysis.php?page=1&ipp=200&type=Search+Details&inputQueryType=Standard&inputOwner=".urlencode($owner)."&inputType=".$type_set["entity"]."\">".$type_set["type_name"]."</td><td class=\"col-xs-3\">".$type_set["count"]."</a></td></tr>";
		}
		echo "</table>";
	}else{}
	echo "</div>";

?>