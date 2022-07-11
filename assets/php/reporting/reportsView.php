<?php

if($_GET['serial'] != ""){
	$serial = mysqli_real_escape_string($db->connection, $_GET['serial']);
	$reportData = mysqli_fetch_assoc(mysqli_query($db->connection, "SELECT * FROM reporting_reports WHERE serial = '".$serial."'"));
}else{
	header("Location: reporting.php?view=1");
}

if(($_SESSION['user_privs']['admin'] == 0 && $_SESSION['user_privs']['reporting_view'] == 0) || ($_SESSION['user_privs']['reporting_view'] == 1 && $_SESSION['user_id'] != $reportData['author'])){
	header("Location: reporting.php?view=1");
}else if($_SESSION['user_privs']['admin'] < 1 && $_SESSION['user_privs']['reporting_view'] != 3){
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
			header("Location: reporting.php?view=1");
		}
	}else{
		header("Location: reporting.php?view=1");
	}
}

mysqli_query($db->connection, "INSERT INTO logs_activities (user_id, log_type, details, timestamp) VALUES ('".$_SESSION['user_id']."', '4', 'VIEW: User viewed the report <a target=\"blank\" href=\"../reporting.php?view=1&serial=$serial\">$serial</a>', '".swc_time(time(),TRUE)["timestamp"]."')");

echo "<center><h1><b>".$reportData['serial']."</b></h1></center><hr/>";

?>

<form class="form-horizontal" role="form" method="post" action="reporting.php" enctype="multipart/form-data">
	<div class="modal-body">  
		<div class="form-group">
			<label class="col-sm-1" for="inputTime">Time</label>
			<div class="col-sm-11"><input class="form-control" id="inputTime" name="inputTime" type="text" style="border:1px solid black;" value="<?php echo swc_time($reportData['timestamp'])['date']; ?>" readonly></div>
		</div><hr/>
		<div class="form-group">
			<label class="col-sm-1" for="inputTitle">Title</label>
			<div class="col-sm-11"><input class="form-control" id="inputTitle" name="inputTitle" type="text" style="border:1px solid black;" value="<?php echo $reportData['title']; ?>" readonly></div>
		</div><hr/>
		<div id="assignedIN" class="form-group hidden-print">
			<label class="col-sm-1" for="inputIN">Assigned IN</label>
			<div class="col-sm-11">
					<?php
						if($needsQuery = mysqli_fetch_assoc(mysqli_query($db->connection, "SELECT serial, title FROM reporting_reports_coi LEFT JOIN reporting_needs ON reporting_reports_coi.need_id = reporting_needs.need_id WHERE report_id = '".$reportData['report_id']."' ORDER BY timestamp DESC"))){
							echo "<input class=\"form-control\" id=\"inputIN\" name=\"inputIN\" type=\"text\" style=\"border:1px solid black;\" value=\"".$needsQuery['serial']." - ".$needsQuery['title']."\" readonly>";
						}else{
							echo "<input class=\"form-control\" id=\"inputIN\" name=\"inputIN\" type=\"text\" style=\"border:1px solid black;\" value=\"None\" readonly>";
						}
					?>
			</div>
		</div><hr class="hidden-print"/>
		<div class="form-group hidden-print">
			<label class="col-sm-1" for="inputFocus">Focus</label>
			<div class="col-sm-11">
				<select class="form-control" id="inputFocus" name="inputFocus" style="border:1px solid black;" disabled>
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
			<div class="col-sm-11"><?php echo str_replace("\t", "&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp", nl2br(strip_tags($reportData['content'],"<b><u><i><a><img>"))); ?></div>
		</div><hr/>
		<div id="attachmentsField" class="form-group">
			<label class="col-sm-1" for="inputFiles">Attachments</label>
			<div class="col-sm-11">
				<?php
				$dir = "reports/".$reportData['serial']."/";								
					if (is_dir($dir)){
						if ($dh = opendir($dir)){
							$count = 1;
							echo "<table border='0'>";
							while (($file = readdir($dh)) !== false){
								if($file != "." && $file != ".."){
									$info = new SplFileInfo($file);
									$filetype = $info->getExtension();
									
									//GET FILETYPE AND OUTPUT APPROPRIATE ICON
									if(strtolower($filetype) == "jpg" || strtolower($filetype) == "jpeg" || strtolower($filetype) == "gif" || strtolower($filetype) == "png" || strtolower($filetype) == "bmp" || strtolower($filetype) == "sgv" || strtolower($filetype) == "tif"){
										$icon = "<img height='100' width='100' src='".$dir."/".$file."'>";
									}elseif(strtolower($filetype) == "zip" || strtolower($filetype) == "7z" || strtolower($filetype) == "rar" || strtolower($filetype) == "tar" || strtolower($filetype) == "gz" || strtolower($filetype) == "pkg" || strtolower($filetype) == "webarchive" || strtolower($filetype) == "tgz" || strtolower($filetype) == "cab" || strtolower($filetype) == "htmlz" || strtolower($filetype) == "7zip"){
										$icon = "<i style=\"font-size:100px;\" class=\"fas fa-file-archive\"></i>";
									}elseif(strtolower($filetype) == "doc" || strtolower($filetype) == "docx" || strtolower($filetype) == "odt" || strtolower($filetype) == "rtf" || strtolower($filetype) == "docm" || strtolower($filetype) == "wpd" || strtolower($filetype) == "txt" || strtolower($filetype) == "stw" || strtolower($filetype) == "ott" || strtolower($filetype) == "odm" || strtolower($filetype) == "sxw"){
										$icon = "<i style=\"font-size:100px;\" class=\"fas fa-file-alt\"></i>";
									}elseif(strtolower($filetype) == "xls" || strtolower($filetype) == "xlsx" || strtolower($filetype) == "csv" || strtolower($filetype) == "xml" || strtolower($filetype) == "sdc" || strtolower($filetype) == "dbf" || strtolower($filetype) == "uos" || strtolower($filetype) == "xslm" || strtolower($filetype) == "wk1"){
										$icon = "<i style=\"font-size:100px;\" class=\"fas fa-file-excel\"></i>";
									}elseif(strtolower($filetype) == "ppt" || strtolower($filetype) == "pps" || strtolower($filetype) == "pptx" || strtolower($filetype) == "pptm" || strtolower($filetype) == "sda" || strtolower($filetype) == "sdd" || strtolower($filetype) == "uop" || strtolower($filetype) == "cgm" || strtolower($filetype) == "uof"){
										$icon = "<i style=\"font-size:100px;\" class=\"fas fa-file-powerpoint\"></i>";
									}elseif(strtolower($filetype) == "pdf"){
										$icon = "<i style=\"font-size:100px;\" class=\"fas fa-file-pdf\"></i>";
									}elseif(strtolower($filetype) == "htm" || strtolower($filetype) == "html"){
										$icon = "<i style=\"font-size:100px;\" class=\"fas fa-file-code\"></i>";
									}else{
										$icon = "<i style=\"font-size:100px;\" class=\"fas fa-paperclip\"></i>";
									}
									
									echo "
										<tr>
											<td width='150' style='margin:5px;' align='center' valign='center'>
												<a href='".$dir."".$file."' target='_blank'>".$icon."</a>
											</td>
											<td width='500' style='border:1px solid white;color:white;'>
												Attachment ".$count.": <a href='".$dir."".$file."' target='_blank'>" . $file . "</a><br>
											</td>
										</tr>
									";
									$count = $count + 1;
								}
							}
							echo "</table>";
							closedir($dh);
						}
					}
				?>
			</div>
		</div><hr class="hidden-print"/>
	</div></form>
	<div class="modal-footer hidden-print">
		<?php
			if(($_SESSION['user_privs']['reporting_delete'] == 1 && $reportData['author'] == $_SESSION['user_id']) || $_SESSION['user_privs']['reporting_delete'] == 2 || $_SESSION['user_privs']['admin'] > 0){
				echo "<a href=\"reporting.php?view=1&serial=".$reportData['serial']."&edit=1\"><button class=\"btn btn-primary hidden-print\">Edit</button></a>";
			}
		?>
		<?php
			if(($_SESSION['user_privs']['reporting_delete'] == 1 && $reportData['author'] == $_SESSION['user_id']) || $_SESSION['user_privs']['reporting_delete'] == 2 || $_SESSION['user_privs']['admin'] > 0){
				echo "<a href=\"reporting.php?view=1&delete=".$reportData['report_id']."\"><button class=\"btn btn-danger hidden-print\" onclick=\"return confirm('Do you really want to delete the selected report?');\">Delete</button></a>";
			}
		?>
	</div>