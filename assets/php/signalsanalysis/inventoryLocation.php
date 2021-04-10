<?php
	$db = new database;
	$db->connect();
	echo "
		<center><h1><b>".ucwords($owner)." (".$_POST["mapType"]." assets)</b></h1></center>
	";
	
	echo "
		<div class=\"row\">
			<div class=\"col-sm-12\"><h2><b>Legend</b></h2></div>
		</div>
		<div class=\"row\">
			<div class=\"col-sm-12\" style=\"margin:0 auto;\">
				<center>
				<table class=\"table table-bordered table-striped table-hover\">
					<tr><td>Any entity exists:</td><td><img src=\"assets/img/graphics/scanned_green.png\"> - Green</td></tr>
					<tr><td>5% - 14% of total entities:</td><td><img src=\"assets/img/graphics/scanned_yellow.png\"> - Yellow</td></tr>
					<tr><td>15% - 29% of total entities:</td><td><img src=\"assets/img/graphics/scanned_orange.png\"> - Orange</td></tr>
					<tr><td>Over 30% of total entities:</td><td><img src=\"assets/img/graphics/scanned_red.png\"> - Red</td></tr>
				</table>
				</center>
			</div>
		</div>
	";
	
	echo "
		<div class=\"col-sm-12\" style=\"position: relative;height:1000px;width:1000px;float:none;margin:0 auto;\">
			<img style=\"height:1000px; width:1000px; position: absolute;top:0px;left:0px;z-index:1;\" src=\"https://www.swcombine.com/rules/the_universe/galaxy_map/galaxyMap.php?mode=gray\">
	";
	// ship-2; vehicle-3; facility-4; station-5;
	if($_POST["mapType"] == "all"){
		$count_all = mysqli_fetch_assoc(mysqli_query($db->connection,"SELECT COUNT(uid) AS count FROM data_signalsanalysis WHERE owner = '$owner'"))["count"];
		$query_map = mysqli_query($db->connection,"SELECT COUNT(data_signalsanalysis.uid) AS count, data_signalsanalysis.galx, data_signalsanalysis.galy, galaxy_systems.name AS sysName FROM data_signalsanalysis LEFT JOIN galaxy_systems ON galaxy_systems.uid = data_signalsanalysis.system WHERE owner = '".$owner."' GROUP BY data_signalsanalysis.galx, data_signalsanalysis.galy");
	}elseif($_POST["mapType"] == "ship"){
		$count_all = mysqli_fetch_assoc(mysqli_query($db->connection,"SELECT COUNT(uid) AS count FROM data_signalsanalysis WHERE owner = '$owner' AND data_signalsanalysis.type LIKE '2:%'"))["count"];
		$query_map = mysqli_query($db->connection,"SELECT COUNT(data_signalsanalysis.uid) AS count, data_signalsanalysis.galx, data_signalsanalysis.galy, galaxy_systems.name AS sysName FROM data_signalsanalysis LEFT JOIN galaxy_systems ON galaxy_systems.uid = data_signalsanalysis.system WHERE owner = '".$owner."' AND data_signalsanalysis.type LIKE '2:%' GROUP BY data_signalsanalysis.galx, data_signalsanalysis.galy");
	}elseif($_POST["mapType"] == "medical"){
		$count_all = mysqli_fetch_assoc(mysqli_query($db->connection,"SELECT COUNT(uid) AS count FROM data_signalsanalysis WHERE owner = '$owner' AND (data_signalsanalysis.type = '5:25' OR data_signalsanalysis.type = '5:27' OR data_signalsanalysis.type = '2:149' OR data_signalsanalysis.type = '2:148' OR data_signalsanalysis.type = '3:83' OR data_signalsanalysis.type = '4:57' OR data_signalsanalysis.type = '4:65' OR data_signalsanalysis.type = '4:33' OR data_signalsanalysis.type = '4:90' OR data_signalsanalysis.type = '4:66')"))["count"];
		$query_map = mysqli_query($db->connection,"SELECT COUNT(data_signalsanalysis.uid) AS count, data_signalsanalysis.galx, data_signalsanalysis.galy, galaxy_systems.name AS sysName FROM data_signalsanalysis LEFT JOIN galaxy_systems ON galaxy_systems.uid = data_signalsanalysis.system WHERE owner = '".$owner."' AND (data_signalsanalysis.type = '5:25' OR data_signalsanalysis.type = '5:27' OR data_signalsanalysis.type = '2:149' OR data_signalsanalysis.type = '2:148' OR data_signalsanalysis.type = '3:83' OR data_signalsanalysis.type = '4:57' OR data_signalsanalysis.type = '4:65' OR data_signalsanalysis.type = '4:33' OR data_signalsanalysis.type = '4:90' OR data_signalsanalysis.type = '4:66') GROUP BY data_signalsanalysis.galx, data_signalsanalysis.galy");
	}elseif($_POST["mapType"] == "production"){
		$count_all = mysqli_fetch_assoc(mysqli_query($db->connection,"SELECT COUNT(uid) AS count FROM data_signalsanalysis WHERE owner = '$owner' AND (data_signalsanalysis.type = '5:6' OR data_signalsanalysis.type = '5:7' OR data_signalsanalysis.type = '5:8' OR data_signalsanalysis.type = '5:26' OR data_signalsanalysis.type = '5:5' OR data_signalsanalysis.type = '4:34' OR data_signalsanalysis.type = '4:73')"))["count"];
		$query_map = mysqli_query($db->connection,"SELECT COUNT(data_signalsanalysis.uid) AS count, data_signalsanalysis.galx, data_signalsanalysis.galy, galaxy_systems.name AS sysName FROM data_signalsanalysis LEFT JOIN galaxy_systems ON galaxy_systems.uid = data_signalsanalysis.system WHERE owner = '".$owner."' AND (data_signalsanalysis.type = '5:6' OR data_signalsanalysis.type = '5:7' OR data_signalsanalysis.type = '5:8' OR data_signalsanalysis.type = '5:26' OR data_signalsanalysis.type = '5:5' OR data_signalsanalysis.type = '4:34' OR data_signalsanalysis.type = '4:73') GROUP BY data_signalsanalysis.galx, data_signalsanalysis.galy");
	}elseif($_POST["mapType"] == "recycling"){
		$count_all = mysqli_fetch_assoc(mysqli_query($db->connection,"SELECT COUNT(uid) AS count FROM data_signalsanalysis WHERE owner = '$owner' AND (data_signalsanalysis.type = '3:64' OR data_signalsanalysis.type = '4:120' OR data_signalsanalysis.type = '5:9' OR data_signalsanalysis.type = '5:10' OR data_signalsanalysis.type = '5:11' OR data_signalsanalysis.type = '5:12')"))["count"];
		$query_map = mysqli_query($db->connection,"SELECT COUNT(data_signalsanalysis.uid) AS count, data_signalsanalysis.galx, data_signalsanalysis.galy, galaxy_systems.name AS sysName FROM data_signalsanalysis LEFT JOIN galaxy_systems ON galaxy_systems.uid = data_signalsanalysis.system WHERE owner = '".$owner."' AND (data_signalsanalysis.type = '3:64' OR data_signalsanalysis.type = '4:120' OR data_signalsanalysis.type = '5:9' OR data_signalsanalysis.type = '5:10' OR data_signalsanalysis.type = '5:11' OR data_signalsanalysis.type = '5:12') GROUP BY data_signalsanalysis.galx, data_signalsanalysis.galy");
	}elseif($_POST["mapType"] == "resources"){
		$count_all = mysqli_fetch_assoc(mysqli_query($db->connection,"SELECT COUNT(uid) AS count FROM data_signalsanalysis WHERE owner = '$owner' AND (data_signalsanalysis.type = '4:36' OR data_signalsanalysis.type = '2:244' OR data_signalsanalysis.type = '3:60' OR data_signalsanalysis.type = '3:45')"))["count"];
		$query_map = mysqli_query($db->connection,"SELECT COUNT(data_signalsanalysis.uid) AS count, data_signalsanalysis.galx, data_signalsanalysis.galy, galaxy_systems.name AS sysName FROM data_signalsanalysis LEFT JOIN galaxy_systems ON galaxy_systems.uid = data_signalsanalysis.system WHERE owner = '".$owner."' AND (data_signalsanalysis.type = '4:36' OR data_signalsanalysis.type = '2:244' OR data_signalsanalysis.type = '3:60' OR data_signalsanalysis.type = '3:45') GROUP BY data_signalsanalysis.galx, data_signalsanalysis.galy");
	}
	while($item = mysqli_fetch_assoc($query_map)){
		if(empty(["sysName"])){$system = "Deepspace";}else{$system = $item["sysName"];}
		
		$entities = $item['count'];
		
		$percentage = $entities / $count_all;
		if($percentage >= 0.05 && $percentage <= 0.14){
			$blip = "assets/img/graphics/scanned_yellow.png";
		}elseif($percentage >= 0.15 && $percentage <= 0.29){
			$blip = "assets/img/graphics/scanned_orange.png";
		}elseif($percentage >= 0.30){
			$blip = "assets/img/graphics/scanned_red.png";
		}else{
			$blip = "assets/img/graphics/scanned_green.png";
		}
		
		$entities = "Entities: &#8194;&#8201;".$entities;
		
		$x = 496 + $item["galx"];
		$y = 496 - $item["galy"];
		
		if($_POST["mapType"] == "all"){
			echo "
				<a target=\"_blank\" href=\"signalsanalysis.php?page=1&ipp=200&type=Search+Details&inputQueryType=Standard&inputOwner=".urlencode($owner)."&inputGalX=".$item["galx"]."&inputGalY=".$item["galy"]."\"><img title=\"".$system." (".$item["galx"].", ".$item["galy"].")&#010;".$entities."\" style=\"position:absolute;z-index:2; top:".$y."px; left:".$x."px\" src=\"".$blip."\"></a>
			";
		}elseif($_POST["mapType"] == "ship"){
			echo "
				<a target=\"_blank\" href=\"signalsanalysis.php?page=1&ipp=200&type=Search+Details&inputQueryType=Standard&inputOwner=".urlencode($owner)."&inputCategory=".urlencode("2:%")."&inputGalX=".$item["galx"]."&inputGalY=".$item["galy"]."\"><img title=\"".$system." (".$item["galx"].", ".$item["galy"].")&#010;".$entities."\" style=\"position:absolute;z-index:2; top:".$y."px; left:".$x."px\" src=\"".$blip."\"></a>
			";
		}elseif($_POST["mapType"] == "medical"){
			echo "
				<a target=\"_blank\" href=\"signalsanalysis.php?page=1&ipp=200&type=Search+Details&inputQueryType=Standard&inputOwner=".urlencode($owner)."&inputCategory=".urlencode("5:25;5:27;2:149;2:148;3:83;4:57;4:65;4:33;4:90;4:66")."&inputGalX=".$item["galx"]."&inputGalY=".$item["galy"]."\"><img title=\"".$system." (".$item["galx"].", ".$item["galy"].")&#010;".$entities."\" style=\"position:absolute;z-index:2; top:".$y."px; left:".$x."px\" src=\"".$blip."\"></a>
			";
		}elseif($_POST["mapType"] == "production"){
			echo "
				<a target=\"_blank\" href=\"signalsanalysis.php?page=1&ipp=200&type=Search+Details&inputQueryType=Standard&inputOwner=".urlencode($owner)."&inputType=".urlencode("5:5;5:6;5:7;5:8;5:26;4:34")."&inputGalX=".$item["galx"]."&inputGalY=".$item["galy"]."\"><img title=\"".$system." (".$item["galx"].", ".$item["galy"].")&#010;".$entities."\" style=\"position:absolute;z-index:2; top:".$y."px; left:".$x."px\" src=\"".$blip."\"></a>
			";
		}elseif($_POST["mapType"] == "recycling"){
			echo "
				<a target=\"_blank\" href=\"signalsanalysis.php?page=1&ipp=200&type=Search+Details&inputQueryType=Standard&inputOwner=".urlencode($owner)."&inputType=".urlencode("3:64;4:120;5:9;5:10;5:11;5:12;")."&inputGalX=".$item["galx"]."&inputGalY=".$item["galy"]."\"><img title=\"".$system." (".$item["galx"].", ".$item["galy"].")&#010;".$entities."\" style=\"position:absolute;z-index:2; top:".$y."px; left:".$x."px\" src=\"".$blip."\"></a>
			";
		}elseif($_POST["mapType"] == "resources"){
			echo "
				<a target=\"_blank\" href=\"signalsanalysis.php?page=1&ipp=200&type=Search+Details&inputQueryType=Standard&inputOwner=".urlencode($owner)."&inputType=".urlencode("4:36;2:244;3:60;3:45;")."&inputGalX=".$item["galx"]."&inputGalY=".$item["galy"]."\"><img title=\"".$system." (".$item["galx"].", ".$item["galy"].")&#010;".$entities."\" style=\"position:absolute;z-index:2; top:".$y."px; left:".$x."px\" src=\"".$blip."\"></a>
			";
		}
		
	}
	echo "
		</div>
	";

?>