<?php

$db = new database;
$db->connect();

if(($_SESSION['user_privs']['admin'] == 0 && $_SESSION['user_privs']['reporting_view'] == 0) || ($_SESSION['user_privs']['reporting_publish'] == 2 && $_SESSION['user_privs']['reporting_delete'] == 1 && $_SESSION['user_id'] != $reportData['author'])){
	header("Location: reporting.php?view=2");
}else if($_SESSION['user_privs']['admin'] < 1 && $_SESSION['user_privs']['reporting_publish'] == 2 && $_SESSION['user_privs']['reporting_delete'] == 2){
	$coi_list = array();
	$groups = mysqli_query($db->connection,"SELECT usergroup_id FROM usergroups_members WHERE user_id = ".$_SESSION['user_id']."");
	while($row = mysqli_fetch_assoc($groups)){
		$group_cois = mysqli_query($db->connection, "SELECT need_id FROM reporting_needs_coi_usergroups WHERE usergroup_id = '".$row['usergroup_id']."'");
		while($row_2 = mysqli_fetch_assoc($group_cois)){
			array_push($coi_list,"need_id = '".$row_2['need_id']."'");
		}
	}
	$user_cois = mysqli_query($db->connection, "SELECT need_id FROM reporting_needs_coi_users WHERE user_id = '".$_SESSION['user_id']."'");
	while($row_2 = mysqli_fetch_assoc($user_cois)){
		array_push($coi_list,"need_id = '".$row_2['need_id']."'");
	}
	$author_cois = mysqli_query($db->connection, "SELECT need_id FROM reporting_needs WHERE author = '".$_SESSION['user_id']."'");
	while($row_2 = mysqli_fetch_assoc($author_cois)){
		array_push($coi_list,"need_id = '".$row_2['need_id']."'");
	}
	$coi_list = array_unique($coi_list);
	$coi_list = trim(implode(" OR ", $coi_list));
	if(isset($coi_list)){
		$needsQuery = mysqli_query($db->connection, "SELECT * FROM reporting_reports_coi WHERE (".$coi_list.") AND report_id = '".$reportData['report_id']."' ORDER BY timestamp DESC");
		if(mysqli_num_rows($needsQuery) == 0){
			header("Location: reporting.php?view=2");
		}
	}else{
		header("Location: reporting.php?view=2");
	}
}

if($_GET['serial'] != ""){
	$serial = mysqli_real_escape_string($db->connection, $_GET['serial']);
	$reportData = mysqli_fetch_assoc(mysqli_query($db->connection, "SELECT * FROM reporting_needs WHERE serial = '".$serial."'"));
}else{
	header("Location: reporting.php?view=2");
}

echo "<center><h1><b>".$reportData['serial']."</b></h1></center><hr/>";

?>

<form class="form-horizontal" role="form" method="post" action="reporting.php?view=2">
	<div class="modal-body">  
		<div class="form-group">
			<label class="col-sm-1" for="inputSerial">Serial</label>
			<div class="col-sm-11"><input class="form-control" id="inputSerial" name="inputSerial" type="text" style="border:1px solid black;" value="<?php echo $_GET['serial']; ?>" readonly></div>
		</div><hr/>
		<div class="form-group">
			<label class="col-sm-1" for="inputTitle">Title</label>
			<div class="col-sm-11"><input class="form-control" id="inputTitle" name="inputTitle" type="text" style="border:1px solid black;" value="<?php echo $reportData['title']; ?>"></div>
		</div><hr/>
		<div class="form-group hidden-print">
			<label class="col-sm-1" for="inputFocus">Focus</label>
			<div class="col-sm-11">
				<select class="form-control" id="inputFocus" name="inputFocus" style="border:1px solid black;">
					<option value="NIL" <?php if($reportData['focus'] == "NIL"){ echo "selected";} ?>>NIL - None</option>
					<option value="ECO" <?php if($reportData['focus'] == "ECO"){ echo "selected";} ?>>ECO - Economy</option>
					<option value="IND" <?php if($reportData['focus'] == "IND"){ echo "selected";} ?>>IND - Industry</option>
					<option value="INS" <?php if($reportData['focus'] == "INS"){ echo "selected";} ?>>INS - Instability</option>
					<option value="MIL" <?php if($reportData['focus'] == "MIL"){ echo "selected";} ?>>MIL - Military</option>
					<option value="OPS" <?php if($reportData['focus'] == "OPS"){ echo "selected";} ?>>OPS - Operations</option>
					<option value="POL" <?php if($reportData['focus'] == "POL"){ echo "selected";} ?>>POL - Politics</option>
					<option value="SOC" <?php if($reportData['focus'] == "SOC"){ echo "selected";} ?>>SOC - Social</option>
					<option value="TEC" <?php if($reportData['focus'] == "TEC"){ echo "selected";} ?>>TEC - Technology</option>
				</select>
			</div>
		</div><hr class="hidden-print"/>
		<div class="form-group">
			<label class="col-sm-1" for="inputContent">Content</label>
			<div class="col-sm-11"><textarea style="width:100%; height:300px; border:1px solid black;" class="form-control" id="inputContent" name="inputContent" onkeydown="if(event.keyCode===9){var v=this.value,s=this.selectionStart,e=this.selectionEnd;this.value=v.substring(0, s)+'\t'+v.substring(e);this.selectionStart=this.selectionEnd=s+1;return false;}" required><?php echo $reportData['content']; ?></textarea></div>
		</div><hr/>
	</div>
	<div class="modal-footer">
		<input id="submit" class="btn btn-primary " type="submit" name="submit" value="Save IN">
	</div>
</form>