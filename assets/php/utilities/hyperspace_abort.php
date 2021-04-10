<?php

function TotalDistance($x1, $y1, $x2, $y2){ //return total distance
	$x_dist = abs($x2 - $x1);
	$y_dist = abs($y2 - $y1);
	if($x_dist > $y_dist){ return $x_dist;	}else{	return $y_dist;	}
}

function TotalEta($pilot, $hyperspace, $distance){ //return total ETA of trip in minutes from input of pilot skill and hyperspace level
	$total_eta = floor($distance * 7200 / ($hyperspace * (1 + ($pilot * 0.05))));
	return $total_eta;
}

function CurrentEta($days, $hours, $minutes){ //return current ETA in minutes from input of remaining ETA in SWC
	$current_eta = ($days * 1440 * 60) + ($hours * 60 * 60) + ($minutes * 60) + $seconds;
	return $current_eta;
}

function TimeRemaining($current_eta, $total_eta){ //return time remaining as a % of total ETA
	return ($current_eta / $total_eta);
}

function DropCoords($x1, $y1, $x2, $y2, $timeRemaining){
	$remaining_x = ($x2 - $x1) * $timeRemaining;
	$remaining_y = ($y2 - $y1) * $timeRemaining;
	$drop_x = round($x2 - $remaining_x,2);
	$drop_y = round($y2 - $remaining_y,2);
	return $drop_x.", ".$drop_y;
}

if($_GET['run'] == "Run"){
	$start = explode(",",$_GET['start']);
	$end = explode(",",$_GET['end']);
	$x1 = trim($start[0]);
	$y1 = trim($start[1]);
	$x2 = trim($end[0]);
	$y2 = trim($end[1]);
	$totalDistance = TotalDistance($x1, $y1, $x2, $y2);
	$totalETA = TotalEta($_GET['pilot'], $_GET['hyper'], $totalDistance);
	$secondsPerCoord = ($totalETA / $totalDistance);
	$eta = explode("-",$_GET['eta']);
	$currentETA = CurrentEta(str_replace("d","",$eta[0]), str_replace("h","",$eta[1]), str_replace("m","",$eta[2]));
	
	$x_dist = ($x2 - $x1);
	$y_dist = ($y2 - $y1);
	
	if($x_dist < 0){$x_positivity = -1;}else{$x_positivity = 1;}
	if($y_dist < 0){$y_positivity = -1;}else{$y_positivity = 1;}
	
	$preFlipX = $x_positivity * $x_dist;
	$preFlipY = $y_positivity * $y_dist;
	
	if($preFlipY > $preFlipX){$flipXY = 1;}else{$flipXY = 0;}
	$postFlipX = $flipXY * $preFlipY + (1 - $flipXY) * $preFlipX;
	$postFlipY = $flipXY * $preFlipX + (1 - $flipXY) * $preFlipY;
	
	$timeRemaining = TimeRemaining($currentETA, $totalETA);
	$travelCompleted = (1 - $currentETA / ($secondsPerCoord * $postFlipX));
	$size = ceil($postFlipX / $postFlipY);

	$x_completed = $x_dist * $travelCompleted;
	$y_completed = $y_dist * $travelCompleted;
	if($postFlipX == 0){$straightLine = 1;}else{$straightLine = 0;}
	$biggercount = $postFlipX - $postFlipY;
	$newXCoord = floor($travelCompleted * $postFlipX);
	$largersPast = floor($newXCoord / $size);
	if($largersPast < $biggercount){$isPastLargers = 0;}else{$isPastLargers = 1;}
	$ifNotPastLargers = $largersPast;
	$ifPastLargersTrue = $biggercount + floor(($newXCoord - $size * $biggercount) / ($size - 1));
	if($straightLine == 1){
		$newYCoord = 0;
	}else{ 
		if($isPastLargers == 0){ 
			$newYCoord = $ifNotPastLargers;
		}else{
			$newYCoord = $ifPastLargersTrue;
		}
	}
	$flippedX = ($newXCoord * (1 - $flipXY) + $newYCoord * $flipXY) * $x_positivity;
	$flippedY = $newXCoord * $flipXY + $newYCoord * (1 - $flipXY) * $y_positivity;
	
	$dropX = $flippedX + $x1;
	$dropY = $flippedY + $y1;
	
	$dropCoords = DropCoords(trim($start[0]), trim($start[1]), trim($end[0]), trim($end[1]), $timeRemaining);
	
	/*
	Total Distance: ".$totalDistance."<br/>
	Total ETA: ".$totalETA." seconds<br/>
	Seconds per coordinate: ".$secondsPerCoord."<br/>
	Remaining ETA: ".$currentETA." seconds<br/>
	Travel % Completed: ".$travelCompleted."<br/>
	Distance Covered: ($x_completed, $y_completed)<br/>
	Remaining % Distance: ".$timeRemaining."<br/>
	Straight Line: ".$straightLine."<br/>
	Size: ".$size."<br/>
	Biggercount: ".$biggercount."<br/>
	New X Coord: ".$newXCoord."<br/>
	Largers Past: ".$largersPast."<br/>
	Is Past Largers: ".$isPastLargers."<br/>
	If Not Past Largers: ".$ifNotPastLargers."<br/>
	If Past Largers True: ".$ifPastLargersTrue."<br/>
	New Y Coord: ".$newYCoord."<br/>
	*/
}
?>

<!-- Example Block -->
<div class="block">
	<!-- Example Title -->
	<div class="block-title">
		<h2>Hyperspace Abort Calculator</h2>
	</div>
	<!-- Example Content -->
	<?php if($_GET['run'] == "Run"){ 
		echo "
			<div class=\"alert alert-success\" style=\"font-size:14px;\">
				Anticipated Dropout Coordinates are: (".$dropX.", ".$dropY.")
			</div>";}
	?>
	<form class="form-horizontal" role="form" method="get" action="">
		<div class="form-group">
			<label for="start">Starting Coordinates:</label>
			<input type="text" class="form-control" name="start" placeholder="x, y" value="<?php echo $_GET['start']; ?>">
		</div>
		<div class="form-group">
			<label for="end">Ending Coordinates:</label>
			<input type="text" class="form-control" name="end" placeholder="x, y" value="<?php echo $_GET['end']; ?>">
		</div>
		<div class="form-group">
			<label for="pilot">Pilot Skill</label>
			<select class="form-control" name="pilot">
				<option>0</option>
				<option <?php if($_GET['pilot'] == 1){echo "selected";} ?>>1</option>
				<option <?php if($_GET['pilot'] == 2){echo "selected";} ?>>2</option>
				<option <?php if($_GET['pilot'] == 3){echo "selected";} ?>>3</option>
				<option <?php if($_GET['pilot'] == 4){echo "selected";} ?>>4</option>
				<option <?php if($_GET['pilot'] == 5){echo "selected";} ?>>5</option>
			</select>
		</div>
		<div class="form-group">
			<label for="hyper">Hyperspeed</label>
			<select class="form-control" name="hyper">
				<option>1</option>
				<option <?php if($_GET['hyper'] == 2){echo "selected";} ?>>2</option>
				<option <?php if($_GET['hyper'] == 3){echo "selected";} ?>>3</option>
				<option <?php if($_GET['hyper'] == 4){echo "selected";} ?>>4</option>
				<option <?php if($_GET['hyper'] == 5){echo "selected";} ?>>5</option>
				<option <?php if($_GET['hyper'] == 6){echo "selected";} ?>>6</option>
				<option <?php if($_GET['hyper'] == 7){echo "selected";} ?>>7</option>
				<option <?php if($_GET['hyper'] == 8){echo "selected";} ?>>8</option>
				<option <?php if($_GET['hyper'] == 9){echo "selected";} ?>>9</option>
				<option <?php if($_GET['hyper'] == 10){echo "selected";} ?>>10</option>
				<option <?php if($_GET['hyper'] == 11){echo "selected";} ?>>11</option>
				<option <?php if($_GET['hyper'] == 12){echo "selected";} ?>>12</option>
				<option <?php if($_GET['hyper'] == 13){echo "selected";} ?>>13</option>
				<option <?php if($_GET['hyper'] == 14){echo "selected";} ?>>14</option>
				<option <?php if($_GET['hyper'] == 15){echo "selected";} ?>>15</option>
			</select>
		</div>
		<div class="form-group">
			<label for="eta">Current ETA:</label>
			<input type="text" class="form-control" name="eta" placeholder="#d-#h-#m-#s (example: 4d-5h-23m-12s)" value="<?php echo $_GET['eta']; ?>">
		</div>
		<div>
			<input name="utility" type="hidden" value="Hyperspace Abort">
			<input name="run" class="btn btn-primary " type="submit" value="Run">
		</div>
	</form>
</div>