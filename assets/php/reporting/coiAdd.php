</div>

<div class="block col-xs-12 col-sm-12 col-md-12 col-lg-12">
	<!-- Title -->
	<div class="block-title">
		<h2>Usergroups</h2>
	</div>
	<div class="modal-body">  
		<form class="form-horizontal" role="form" method="post" action="reporting.php?view=2&serial=<?php echo $_GET['serial']; ?>">
		<div class="form-group">
			<label class="col-sm-1" for="inputAddUsergroup">Add Usergroup</label>
			<div class="col-sm-9">
				<select class="form-control" id="inputAddUsergroup" name="inputAddUsergroup">
				<?php
					$db = new database;
					$db->connect();
					$query = mysqli_query($db->connection,"SELECT usergroup_id, usergroup_name FROM usergroups ORDER BY usergroup_name ASC");

					while($data = mysqli_fetch_assoc($query)){
						echo "<option value=\"".$data['usergroup_id']."\">".ucwords($data['usergroup_name'])."</option>";
					}
					$db->disconnect();
					unset($db);
				?>
				</select>
			</div>
			<div class="col-sm-2">
				<input class="btn btn-primary" type="submit" name="submitAdd" value="Add">
			</div>
		</div>
		</form>
		
		<table class="table table-bordered table-striped table-responsive table-hover"><tr><th>Usergroup ID</th><th>Usergroup Name</th><th>Action</th></tr>
			<?php
				$db = new database;
				$db->connect();
				$query = mysqli_query($db->connection,"SELECT reporting_needs_coi_usergroups.usergroup_id, usergroup_name FROM reporting_needs_coi_usergroups LEFT JOIN usergroups ON reporting_needs_coi_usergroups.usergroup_id = usergroups.usergroup_id WHERE need_id = '".$reportData['need_id']."' ORDER BY usergroup_name ASC");

				while($data = mysqli_fetch_assoc($query)){
					echo "<tr><td>".$data['usergroup_id']."</td><td>".ucwords($data['usergroup_name'])."</td><td><a href=\"reporting.php?view=2&serial=".$_GET['serial']."&dUsergroup=".$data['usergroup_id']."\">[Delete]</a></td></tr>";
				}
				
			?>
		</table>
	</div>
</div>

<div class="block col-xs-12 col-sm-12 col-md-12 col-lg-12">
	<!-- Title -->
	<div class="block-title">
		<h2>Users</h2>
	</div>
	<div class="modal-body">  
		<form class="form-horizontal" role="form" method="post" action="reporting.php?view=2&serial=<?php echo $_GET['serial']; ?>">
		<div class="form-group">
			<label class="col-sm-1" for="inputAddUser">Add User</label>
			<div class="col-sm-9">
				<select class="form-control" id="inputAddUser" name="inputAddUser">
				<?php
					$db = new database;
					$db->connect();
					$query = mysqli_query($db->connection,"SELECT user_id, username FROM users ORDER BY username ASC");

					while($data = mysqli_fetch_assoc($query)){
						echo "<option value=\"".$data['user_id']."\">".ucwords($data['username'])."</option>";
					}
					$db->disconnect();
					unset($db);
				?>
				</select>
			</div>
			<div class="col-sm-2">
				<input class="btn btn-primary" type="submit" name="submitAdd" value="Add">
			</div>
		</div>
		</form>
		
		<table class="table table-bordered table-striped table-responsive table-hover"><tr><th>User ID</th><th>Username</th><th>Action</th></tr>
			<?php
				$db = new database;
				$db->connect();
				$query = mysqli_query($db->connection,"SELECT reporting_needs_coi_users.user_id, username FROM reporting_needs_coi_users LEFT JOIN users ON reporting_needs_coi_users.user_id = users.user_id WHERE need_id = '".$reportData['need_id']."' ORDER BY username ASC");

				while($data = mysqli_fetch_assoc($query)){
					echo "<tr><td>".$data['user_id']."</td><td>".ucwords($data['username'])."</td><td><a href=\"reporting.php?view=2&serial=".$_GET['serial']."&dUser=".$data['user_id']."\">[Delete]</a></td></tr>";
				}
				
			?>
		</table>
	</div>
</div>