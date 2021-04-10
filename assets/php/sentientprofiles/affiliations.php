<div class="block col-xs-12 col-sm-12 col-md-12 col-lg-12">
	<!-- Title -->
	<div class="block-title bg-primary">
		<h2>Affiliations</h2>
	</div>
	<div class="modal-body">  
		<form class="form-horizontal hidden-print" role="form" method="post" action="">
		<div class="form-group bg-info">
			<label class="col-sm-2" style="margin-top: 7px;" for="inputFaction">Add Affiliation</label>
			<div class="col-sm-4">
				<input class="form-control" name="inputFaction" type="text" list="factions">
				<datalist id="factions" id="inputFaction">
					<option value="">Select Faction</option>";
					<?php
						$db = new database;
						$db->connect();
						$query = mysqli_query($db->connection,"SELECT name FROM factions ORDER BY name ASC");
						while($row = mysqli_fetch_assoc($query)){
							echo "<option value=\"".$row['name']."\">".$row['name']."</option>";
						}
					?>
				</datalist>
			</div>
			<div class="col-sm-2">
				<input class="form-control" type="text" id="inputDate" name="inputDate" placeholder="Date: Year [##] Day [###]">
			</div>
			<div class="col-sm-2">
				<input class="form-control" type="text" id="inputNote" name="inputNote" placeholder="Notes">
			</div>
			<div class="col-sm-1">
				<input class="btn btn-info" type="submit" name="submitAffiliation" value="Add">
			</div>
		</div>
		</form>
		
		<table class="table table-bordered table-striped table-responsive table-hover"><tr><th>Affiliation</th><th>Date</th><th>Notes</th><th class="hidden-print">Action</th></tr>
			<?php
				$db = new database;
				$db->connect();
				$query = mysqli_query($db->connection,"SELECT characters_affiliations.id, characters_affiliations.faction, characters_affiliations.date, characters_affiliations.note, characters.* FROM characters_affiliations LEFT JOIN characters ON characters.uid = characters_affiliations.character_uid WHERE characters_affiliations.character_uid = '1:$uid' ORDER BY characters_affiliations.date DESC, characters_affiliations.id DESC");
				if(mysqli_num_rows($query) != 0){
					while($data = mysqli_fetch_assoc($query)){
						if(!empty($_SESSION['user_privs']['admin']) || ($_SESSION['user_id'] == $data['user_id']) && !empty($_SESSION['user_privs']['sentientprofiles_delete'])){
							echo "<tr><td>".ucwords($data['faction'])."</td><td>".$data['date']."</td><td>".$data['note']."</td><td class=\"hidden-print\"><a href=\"profile.php?uid=".$uid."&view=4&deleteAffil=".urlencode($data['id'])."\"><button class=\"btn btn-danger hidden-print\" onclick=\"return confirm('Do you really want to delete the selected affiliation?');\">Delete</button></a></td></tr>";
						}else{
							echo "<tr><td>".ucwords($data['faction'])."</td><td>".$data['date']."</td><td>".$data['note']."</td><td></td></tr>";
						}
					}
				}else{
					echo "<tr><td colspan=\"4\"><center><h2>No data</h2></center></td></tr>";
				}

				
				
			?>
		</table>
	</div>
</div>