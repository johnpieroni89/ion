<?php

	$db = new database;
	$db->connect();
	
	$facilities = mysqli_fetch_assoc(mysqli_query($db->connection, "SELECT COUNT(uid) as count FROM data_signalsanalysis WHERE uid LIKE '4:%'"))["count"];
	$ships = mysqli_fetch_assoc(mysqli_query($db->connection, "SELECT COUNT(uid) as count FROM data_signalsanalysis WHERE uid LIKE '2:%'"))["count"];
	$stations = mysqli_fetch_assoc(mysqli_query($db->connection, "SELECT COUNT(uid) as count FROM data_signalsanalysis WHERE uid LIKE '5:%'"))["count"];
	$vehicles = mysqli_fetch_assoc(mysqli_query($db->connection, "SELECT COUNT(uid) as count FROM data_signalsanalysis WHERE uid LIKE '3:%'"))["count"];
	
	echo "
		<div class=\"row\">
			<div class=\"col-sm-12\"><center><h1><b>Statistics</b></h1></center></div>
		</div>
		<div class=\"row\">
			<div class=\"col-sm-12\" style=\"margin: auto;\">
				<center>
				<table class=\"table table-bordered table-striped table-hover\">
					<tr><td>Facilities:</td><td>".number_format($facilities)."</td></tr>
					<tr><td>Ships:</td><td>".number_format($ships)."</td></tr>
					<tr><td>Stations:</td><td>".number_format($stations)."</td></tr>
					<tr><td>Vehicles:</td><td>".number_format($vehicles)."</td></tr>
				</table>
				</center>
			</div>
		</div>
	";
	
	echo "
		<div class=\"row\">
			<div class=\"col-sm-12\"><center><h1><b>Legend</b></h1></center></div>
		</div>
		<div class=\"row\">
			<div class=\"col-sm-12\" style=\"margin: auto;\">
				<center>
				<table class=\"table table-bordered table-striped table-hover\">
					<tr><td>Scanned within 60 days:</td><td><img src=\"assets/img/graphics/scanned_green.png\"> - Green</td></tr>
					<tr><td>Scanned within 180 days:</td><td><img src=\"assets/img/graphics/scanned_yellow.png\"> - Yellow</td></tr>
					<tr><td>Scanned within 365 days:</td><td><img src=\"assets/img/graphics/scanned_orange.png\"> - Orange</td></tr>
					<tr><td>Scanned more than 365 days ago:</td><td><img src=\"assets/img/graphics/scanned_red.png\"> - Red</td></tr>
					<tr><td colspan=\"2\" align=\"center\" class=\"hidden-print\">Note: To tag a system the scan must be a full range scan at the space level in a charted system with a sensor range greater than 1</td></tr>
				</table>
				</center>
			</div>
		</div>
	";
	
	echo "
		<div class=\"row\">
			<div class=\"col-sm-12\" style=\"position: relative;text-align:center;float:none;margin: auto;\">
				<center><h1><b>Scanner Chart</b></h1></center>
			</div>
		</div>
		<div class=\"row\">
			<div class=\"col-sm-12\" style=\"position: relative;height:1000px;width:1000px;float:none;margin:0 auto;\">
				<img style=\"height:1000px; width:1000px; position: absolute;top:0px;left:0px;z-index:1;\" src=\"https://www.swcombine.com/rules/the_universe/galaxy_map/galaxyMap.php?mode=gray\">
	";
	$query_time = swc_time(time(), TRUE)["timestamp"] - 31536000;
	$query_map = mysqli_query($db->connection,"SELECT data_signalsanalysis_scantime.system, name, galx, galy, timestamp FROM data_signalsanalysis_scantime LEFT JOIN galaxy_systems ON galaxy_systems.uid = data_signalsanalysis_scantime.system");
	// WHERE timestamp >= ".$query_time."
	while($item = mysqli_fetch_assoc($query_map)){										
		$realtime = swc_time(time(),TRUE)["timestamp"];
		$scantime = $item["timestamp"];
		$swc_time = swc_time($scantime);
		
		if($realtime - $scantime < 5184000){ //60 days
			$blip = "assets/img/graphics/scanned_green.png";
		}elseif($realtime - $scantime < 15552000){ //180 days
			$blip = "assets/img/graphics/scanned_yellow.png";
		}elseif($realtime - $scantime < 31536000){ //365 days
			$blip = "assets/img/graphics/scanned_orange.png";
		//}elseif($realtime - $scantime < 31536000){ //365 days
			//$blip = "assets/img/graphics/scanned_red.png";
		}else{
			$blip = "assets/img/graphics/scanned_red.png";
		}
		
		$x = 496 + $item["galx"];
		$y = 496 - $item["galy"];
		
		/*	
		if($item["galx"] < 0 && $item["galy"] > 0){
			$x = 496 + $item["galx"];
			$y = 496 - $item["galy"];
		}elseif($item["galx"] < 0 && $item["galy"] < 0){
			$x = 496 + $item["galx"];
			$y = 496 - $item["galy"];
		}elseif($item["galx"] >= 0 && $item["galy"] < 0){
			$x = 496 + $item["galx"];
			$y = 496 - $item["galy"];
		}elseif($item["galx"] >= 0 && $item["galy"] >= 0){
			$x = 496 + $item["galx"];
			$y = 496 - $item["galy"];
		}
		*/
		
		echo "
			<a target=\"_blank\" href=\"signalsanalysis.php?type=Search+Details&inputQueryType=Standard&inputSystem=".$item['system']."\">
			<img title=\"".$item["name"]." (".$item["galx"].", ".$item["galy"].")&#010;Y".$swc_time["year"]." D".$swc_time["day"]." ".$swc_time["hour"].":".$swc_time["minute"]."\" style=\"position:absolute;z-index:2; top:".$y."px; left:".$x."px\" src=\"".$blip."\">
			</a>
		";
	}
	echo "
		</div></div>
	";

?>