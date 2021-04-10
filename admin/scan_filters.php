<?php
	error_reporting(0);
	include("../assets/php/database.php");
	include("../assets/php/session.php");
	include("../assets/php/functions.php");
	include("../assets/php/acct/check.php");
	if(!isset($_SESSION['user_id']) || $_SESSION['user_privs']['admin'] == 0){ header("Location: ../index.php");}
	
	$db = new database;
	$db->connect();
	
	if(isset($_POST['submit'])){
		$u_id = mysqli_real_escape_string($db->connection, $_POST['inputUser']);
		$type = mysqli_real_escape_string($db->connection, $_POST['inputType']);
		$field = mysqli_real_escape_string($db->connection, $_POST['inputField']);
		$value = mysqli_real_escape_string($db->connection, $_POST['inputValue']);
		if(!empty($u_id) && !empty($field) && !empty($value)){
			mysqli_query($db->connection, "INSERT INTO users_filters (scope, u_id, type, field, value) VALUES ('".explode(":",$u_id)[0]."', '".explode(":",$u_id)[1]."', '$type', '$field', '".$value."')");
			$alert = "<div class=\"alert alert-success\" style=\"font-size:14px;\">Filter has been added!</div>";
		}else{
			$alert = "<div class=\"alert alert-warning\" style=\"font-size:14px;\">You must fill out the form completely.</div>";
		}
	}elseif(isset($_GET['filter_delete'])){
		$filter_id = mysqli_real_escape_string($db->connection, $_GET['filter_delete']);
		mysqli_query($db->connection, "DELETE FROM users_filters WHERE filter_id = '".$filter_id."'");
		$alert = "<div class=\"alert alert-warning\" style=\"font-size:14px;\">Filter has been deleted!</div>";
	}
?>

	<?php include("../assets/php/_head.php"); ?>
    <body>
        <!-- Page Wrapper -->
        <div id="page-wrapper" class="page-loading">
            <?php include("../assets/php/_preloader.php"); ?>
			<!-- Page Container -->
            <div id="page-container" class="header-fixed-top sidebar-visible-lg-full">
                
                <?php include("../assets/php/_sidebar-alt.php"); ?>
                <?php include("../assets/php/_sidebar.php"); ?>

                <!-- Main Container -->
                <div id="main-container">
                    <?php include("../assets/php/_header.php"); ?>

                    <!-- Page content -->
                    <div id="page-content" style="overflow:auto;">
                        <!-- Page Header -->
                        <div class="content-header">
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="header-section">
                                        <h1>Scan Filters</h1>
                                    </div>
                                </div>
                                <div class="col-sm-6 hidden-xs">
                                    <div class="header-section">
                                        <ul class="breadcrumb breadcrumb-top">
                                            <li>Admin</li>
                                            <li><a href="panel.php">Panel</a></li>
											<li><a href="">Scan Filters</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
						<?php if(isset($alert)){echo $alert;} ?>

                        <!-- Block -->
                        <div class="block col-xs-12 col-sm-12 col-md-12 col-lg-12">
                            <!-- Title -->
                            <div class="block-title">
                                <h2>Scan Filters</h2> <button title="Add Filter" class="btn btn-info pull-right hidden-print" data-target="#modalAdd" data-toggle="modal"><span class="fas fa-plus"></span> Add</button>
                            </div>
							
							</form>
							<?php
								$db = new database;
								$db->connect();
								
								if(isset($_POST['search'])){
									$search = mysqli_real_escape_string($db->connection,$_POST['search']);
									$filters_usergroups = mysqli_query($db->connection,"SELECT users_filters.*, usergroups.usergroup_name FROM users_filters LEFT JOIN usergroups ON users_filters.u_id = usergroups.usergroup_id WHERE scope = '1' AND (field LIKE '%".$search."%' OR value LIKE '%".$search."%' OR usergroup_name LIKE '%".$search."%') ORDER BY usergroup_name");
									$filters_users = mysqli_query($db->connection,"SELECT users_filters.*, users.username FROM users_filters LEFT JOIN users ON users_filters.u_id = users.user_id WHERE scope = '0' AND (field LIKE '%".$search."%' OR value LIKE '%".$search."%' OR usergroup_name LIKE '%".$search."%') ORDER BY username");
								}else{
									$filters_usergroups = mysqli_query($db->connection,"SELECT users_filters.*, usergroups.usergroup_name FROM users_filters LEFT JOIN usergroups ON users_filters.u_id = usergroups.usergroup_id WHERE scope = '1' ORDER BY usergroup_name");
									$filters_users = mysqli_query($db->connection,"SELECT users_filters.*, users.username FROM users_filters LEFT JOIN users ON users_filters.u_id = users.user_id WHERE scope = '0' ORDER BY username");
								}
								echo "<table class=\"table table-bordered table-striped table-responsive table-hover\"><tr><th>User/Group</th><th>Type</th><th>Field</th><th>Value</th><th>Actions</th></tr>";
								$type_array = array('include', 'exclude');
								while($data = mysqli_fetch_assoc($filters_usergroups)){
									echo "<tr><td>".ucwords($data['usergroup_name'])."</td><td>".$type_array[$data['type']]."</td><td>".$data['field']."</td><td>".$data['value']."</td><td align=\"center\"><a href=\"scan_filters.php?filter_delete=".$data['filter_id']."\" onclick=\"return confirm('Do you really want to delete the selected filter?');\">[Delete]</a></td></tr>";
								}
								while($data = mysqli_fetch_assoc($filters_users)){
									echo "<tr><td>".ucwords($data['username'])."</td><td>".$type_array[$data['type']]."</td><td>".$data['field']."</td><td>".$data['value']."</td><td align=\"center\"><a href=\"scan_filters.php?filter_delete=".$data['filter_id']."\" onclick=\"return confirm('Do you really want to delete the selected filter?');\">[Delete]</a></td></tr>";
								}
								echo "</table>";
							?>  
                        </div>
                    </div>
					
					<div class="modal fade" id="modalAdd">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header">
									<button class="close" aria-hidden="true" type="button" data-dismiss="modal">×</button>
									<h4 class="modal-title">Add Filter</h4>
								</div>
								<form class="form-horizontal" role="form" method="post" action="">
									<div class="modal-body">
										<div class="form-group">
											<label class="col-sm-3" for="inputUser">User/Group</label>
											<div class="col-sm-9">
												<select class="form-control" id="inputUser" name="inputUser">
													<option value="">{Required}</option>
													<?php
														$usergroups = mysqli_query($db->connection, "SELECT usergroup_id, usergroup_name FROM usergroups ORDER BY usergroup_name");
														$users = mysqli_query($db->connection, "SELECT user_id, username FROM users ORDER BY username");
														while($row = mysqli_fetch_assoc($usergroups)){
															echo "<option value=\"1:".$row['usergroup_id']."\">".$row['usergroup_name']."</option>";
														}
														while($row = mysqli_fetch_assoc($users)){
															echo "<option value=\"0:".$row['user_id']."\">".$row['username']."</option>";
														}
													?>
												</select>
											</div>
										</div>
										<div class="form-group">
											<label class="col-sm-3" for="inputType">Type</label>
											<div class="col-sm-9">
												<select class="form-control" id="inputType" name="inputType">
													<option value="0">Include</option>
													<option value="1">Exclude</option>
												</select>
											</div>
										</div>
										<div class="form-group">
											<label class="col-sm-3" for="inputField">Field</label>
											<div class="col-sm-9">
												<select class="form-control" id="inputField" name="inputField">
													<option value="class">Class</option>
													<option value="id">ID</option>
													<option value="name">Name</option>
													<option value="owner">Owner</option>
													<option value="planet">Planet</option>
													<option value="sector">Sector</option>
													<option value="system">System</option>
													<option value="type">Type</option>
													
												</select>
											</div>
										</div>
										<div class="form-group">
											<label class="col-sm-3" for="inputValue">Value</label>
											<div class="col-sm-9"><input type="text" class="form-control" id="inputValue" name="inputValue" placeholder="{field value}"></div>
										</div>
									</div>
									<div class="modal-footer">
										<button class="btn btn-default pull-left" type="button" data-dismiss="modal">Cancel</button>
										<input id="submit" class="btn btn-primary " type="submit" name="submit" value="Add">
									</div>
								</form>
							</div>
						</div>
					</div>
                </div>
            </div>
        </div>
		<?php include("../assets/php/_end.php"); ?>
	</body>
</html>