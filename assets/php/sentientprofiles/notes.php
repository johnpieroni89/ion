<div class="block col-xs-12 col-sm-12 col-md-12 col-lg-12">
	<!-- Title -->
	<div class="block-title bg-primary">
		<h2>Notes</h2> <button title="Add Note" class="btn btn-info pull-right hidden-print" data-target="#modalNote" data-toggle="modal"><span class="fas fa-plus"></span> Add</button>
	</div>
	<div class="block-content col-sm-12" style="margin:0px;padding:0px;">
		<table class="table table-bordered table-striped table-responsive table-hover">
			<tr><th class="col-xs-2">Timestamp</th><th class="col-xs-6">Note</th><th class="col-xs-2">Status</th><th class="col-xs-2 hidden-print">Actions</th></tr>
			<?php
				$query = mysqli_query($db->connection,"SELECT * FROM characters_notes LEFT JOIN users ON characters_notes.author = users.user_id WHERE characters_notes.character_uid = '1:$uid' ORDER BY characters_notes.timestamp DESC");
				if(mysqli_num_rows($query) != 0){
					while($data = mysqli_fetch_assoc($query)){
						echo "<tr><td>".swc_time($data['timestamp'])['date']."</td><td>".$data['note_details']."</td><td align=\"center\">";
						
						if($data['verified'] == 0){
							echo "<div style=\"color:red;\"><span class=\"fas fa-times\"></span> Not verified</div>";
						}elseif($data['verified'] == 1 && !empty($_SESSION['user_privs']['admin'])){
							echo "<div style=\"color:green;\" title=\"".$data['verify_note']."\"><span class=\"fas fa-check\"></span> Verified</div>";
						}elseif($data['verified'] == 1){
							echo "<div style=\"color:green;\"><span class=\"fas fa-check\"></span> Verified</div>";
						}
						
						
						echo "</td><td align=\"center\" class=\"hidden-print\">";
						if($_SESSION['user_id'] != $data['author'] && $data['verified'] == 0){
							echo "<button title=\"Verify\" class=\"btn btn-sm btn-info\" data-target=\"#modalVerify\" data-toggle=\"modal\">Verify</button> ";
						}
						if(!empty($_SESSION['user_privs']['admin']) || !empty($_SESSION['user_privs']['sentientprofiles_delete'])){
							echo "<a href=\"profile.php?uid=".$uid."&view=7&deleteNote=".urlencode($data['note_id'])."\"><button class=\"btn btn-sm btn-danger hidden-print\" onclick=\"return confirm('Do you really want to delete the selected note?');\">Delete</button></a>";
						}
						echo "</td></tr>";
					}
				}else{
					echo "<tr><td colspan=\"4\"><center><h2>No data</h2></center></td></tr>";
				}
			?>
		</table>
	</div>
	
	<div class="modal fade" id="modalNote">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button class="close" aria-hidden="true" type="button" data-dismiss="modal">×</button>
					<h4 class="modal-title">Add Note</h4>
				</div>
				<form class="form-horizontal" role="form" method="post" action="">
					<div class="modal-body">
						<div class="form-group">
							<label class="col-sm-3" for="inputControlledBy">Details</label>
							<div class="col-sm-9"><textarea rows="24" class="form-control" id="inputDetails" name="inputDetails" placeholder="{Note text}"></textarea></div>
						</div>
					</div>
					<div class="modal-footer">
						<button class="btn btn-default pull-left" type="button" data-dismiss="modal">Cancel</button>
						<input id="submit" class="btn btn-primary " type="submit" name="submitNote" value="Add">
					</div>
				</form>
			</div>
		</div>
	</div>
	
	<div class="modal fade" id="modalVerify">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button class="close" aria-hidden="true" type="button" data-dismiss="modal">×</button>
					<h4 class="modal-title">Verify Information</h4>
				</div>
				<form class="form-horizontal" role="form" method="post" action="">
					<div class="modal-body">
						<div class="form-group">
							<label class="col-sm-3" for="inputControlledBy">Comment</label>
							<div class="col-sm-9"><textarea rows="16" class="form-control" id="inputComment" name="inputComment" placeholder="{Comment text}"></textarea></div>
						</div>
					</div>
					<div class="modal-footer">
						<button class="btn btn-default pull-left" type="button" data-dismiss="modal">Cancel</button>
						<input id="submit" class="btn btn-primary " type="submit" name="submitVerify" value="Verify">
					</div>
				</form>
			</div>
		</div>
	</div>
</div>