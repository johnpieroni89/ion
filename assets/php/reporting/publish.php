<?php
echo "<center><h1><b>Publish</b></h1></center><hr/>";

if($_SESSION['user_privs']['reporting_publish'] > 1 || $_SESSION['user_privs']['admin'] > 0){
	if($_POST['inputType'] == 2){ $text = "selected";}else{ $text = "";}
	$IN = "<option value=\"2\" ".$text.">Information Need</option>";
}

?>

<form class="form-horizontal" role="form" method="post" action="reporting.php" enctype="multipart/form-data">
	<div class="modal-body">  
		<div class="form-group">
			<label class="col-sm-1" for="inputType">Type</label>
			<div class="col-sm-11">
				<select class="form-control" id="inputType" name="inputType" style="border:1px solid black;" onchange="changeFields()">
					<option value="1">Report</option>
					<?php echo $IN; ?>
				</select>
			</div>
		</div><hr/>
		<div class="form-group">
			<label class="col-sm-1" for="inputTitle">Title</label>
			<div class="col-sm-11"><input class="form-control" id="inputTitle" name="inputTitle" type="text" style="border:1px solid black;" value="<?php echo $_POST['inputTitle']; ?>" required></div>
		</div><hr/>
		<div id="assignedIN" class="form-group">
			<label class="col-sm-1" for="inputIN">Assigned IN</label>
			<div class="col-sm-11">
				<select class="form-control" id="inputIN" name="inputIN" style="border:1px solid black;">
					<option value="">None</option>
					<?php
						if($_SESSION['user_privs']['admin'] != 0 || $_SESSION['user_privs']['reporting_view'] == 3){
							$needsQuery = mysqli_query($db->connection, "SELECT * FROM reporting_needs ORDER BY timestamp DESC");
							while($row = mysqli_fetch_assoc($needsQuery)){
								echo "<option value=\"".$row['need_id']."\">".$row['serial']." - ".$row['title']."</option>";
							}
						}else{
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
								$needsQuery = mysqli_query($db->connection, "SELECT * FROM reporting_needs WHERE ".$coi_list." ORDER BY timestamp DESC");
								while($row = mysqli_fetch_assoc($needsQuery)){
									echo "<option value=\"".$row['need_id']."\">".$row['serial']." - ".$row['title']."</option>";
								}
							}
						}
					?>
				</select>
			</div><br/><hr/>
		</div>
		<div class="form-group">
			<label class="col-sm-1" for="inputFocus">Focus</label>
			<div class="col-sm-11">
				<select class="form-control" id="inputFocus" name="inputFocus" style="border:1px solid black;">
					<option value="NIL">NIL - None</option>
					<option value="ECO" <?php if($_POST['inputFocus'] == "ECO"){ echo "selected";} ?>>ECO - Economy</option>
					<option value="IND" <?php if($_POST['inputFocus'] == "IND"){ echo "selected";} ?>>IND - Industry</option>
					<option value="INS" <?php if($_POST['inputFocus'] == "INS"){ echo "selected";} ?>>INS - Instability</option>
					<option value="MIL" <?php if($_POST['inputFocus'] == "MIL"){ echo "selected";} ?>>MIL - Military</option>
					<option value="OPS" <?php if($_POST['inputFocus'] == "OPS"){ echo "selected";} ?>>OPS - Operations</option>
					<option value="POL" <?php if($_POST['inputFocus'] == "POL"){ echo "selected";} ?>>POL - Politics</option>
					<option value="SOC" <?php if($_POST['inputFocus'] == "SOC"){ echo "selected";} ?>>SOC - Social</option>
					<option value="TEC" <?php if($_POST['inputFocus'] == "TEC"){ echo "selected";} ?>>TEC - Technology</option>
				</select>
			</div>
		</div><hr/>
		<div class="form-group">
			<label class="col-sm-1" for="inputContent">Content</label>
			<div class="col-sm-11"><textarea style="width:100%; height:300px; border:1px solid black;" class="form-control" id="inputContent" name="inputContent" onkeydown="if(event.keyCode===9){var v=this.value,s=this.selectionStart,e=this.selectionEnd;this.value=v.substring(0, s)+'\t'+v.substring(e);this.selectionStart=this.selectionEnd=s+1;return false;}" required><?php echo $_POST['inputContent']; ?></textarea></div>
		</div>
		<div id="attachmentsField" class="form-group">
			<label class="col-sm-1" for="inputFiles">Attachments</label>
			<div class="col-sm-11"><input class="form-control" id="inputFiles" name="inputFiles[]" type="file" style="border:1px solid black;" multiple></div>
		</div><hr/>
	</div>
	<div class="modal-footer">
		<input id="submit" class="btn btn-primary " type="submit" name="submit" value="Publish">
	</div>
</form>