<div class="block col-xs-12 col-sm-12 col-md-12 col-lg-12">
	<!-- Title -->
	<div class="block-title bg-primary">
		<h2>Associations</h2>
	</div>
	<div class="modal-body">  
		<form class="form-horizontal hidden-print" role="form" method="post" action="">
		<div class="form-group bg-info">
			<label class="col-sm-2" style="margin-top: 7px;" for="inputAdd">Add Association</label>
			<div class="col-sm-4">
				<select class="form-control" id="inputHandle" name="inputHandle">
					<option value="">Select Handle</option>
					<?php
						$query = mysqli_query($db->connection,"SELECT handle FROM characters ORDER BY handle ASC");
						while($row = mysqli_fetch_assoc($query)){
							echo "<option value=\"".$row['handle']."\">".$row['handle']."</option>";
						}
					?>
				</select>
			</div>
			<div class="col-sm-4">
				<select class="form-control" id="inputType" name="inputType">
					<option value="">Select Type</option>
					<option value="Adversary">Adversary</option>
					<option value="Employee">Employee</option>
					<option value="Employer">Employer</option>
					<option value="Family">Family</option>
					<option value="Friend">Friend</option>
					<option value="Partner">Partner</option>
					<option value="Peer">Peer</option>
					<option value="Subordinate">Subordinate</option>
					<option value="Supervisor">Supervisor</option>
				</select>
			</div>
			<div class="col-sm-1">
				<input class="btn btn-info" type="submit" name="submitAssociation" value="Add">
			</div>
		</div>
		</form>
		
		<table class="table table-bordered table-striped table-responsive table-hover"><tr><th>Handle</th><th>Association Type</th><th class="hidden-print">Action</th></tr>
			<?php
				$query = mysqli_query($db->connection,"SELECT characters_associations.id AS assocId, characters_associations.association, characters_associations.handle, characters.uid, user_id FROM characters_associations LEFT JOIN characters ON characters.uid = characters_associations.character_uid WHERE characters_associations.character_uid = '1:$uid' ORDER BY characters_associations.handle ASC");
				if(mysqli_num_rows($query) != 0){
					while($data = mysqli_fetch_assoc($query)){
						if(!empty($_SESSION['user_privs']['admin']) || ($_SESSION['user_id'] == $data['user_id']) && !empty($_SESSION['user_privs']['sentientprofiles_delete'])){
							echo "<tr><td>".ucwords($data['handle'])."</td><td>".$data['association']."</td><td class=\"hidden-print\"><a href=\"profile.php?uid=".$uid."&view=3&deleteAssoc=".urlencode($data['handle'])."\"><button class=\"btn btn-danger hidden-print\" onclick=\"return confirm('Do you really want to delete the selected association?');\">Delete</button></a></td></tr>";
						}else{
							echo "<tr><td>".ucwords($data['handle'])."</td><td>".$data['association']."</td><td>".$data['association']."</td><td></td></tr>";
						}
					}
				}else{
					echo "<tr><td colspan=\"3\"><center><h2>No data</h2></center></td></tr>";
				}
			?>
		</table>
	</div>
</div>