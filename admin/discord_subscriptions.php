<?php
	error_reporting(0);
	include("../assets/php/database.php");
	include("../assets/php/session.php");
	include("../assets/php/functions.php");
	include("../assets/php/acct/check.php");
	if(!isset($_SESSION['user_id']) || $_SESSION['user_privs']['admin'] == 0){ header("Location: ../index.php");}
	
	$db = new database;
	$db->connect();
	
	if(isset($_POST['subscription_add'])){
		$id = mysqli_real_escape_string($db->connection, $_POST['subscription_add']);
		$current = mysqli_fetch_assoc(mysqli_query($db->connection, "SELECT subscription FROM bot_subscriptions WHERE subscription_id = '$id'"))['subscription'];
		mysqli_query($db->connection, "UPDATE bot_subscriptions SET subscription = '".($current + 30)."' WHERE subscription_id = '$id'");
		$alert = "<div class=\"alert alert-success\" style=\"font-size:14px;\">Subscriber has been granted 30 additional days.</div>";
	}elseif(isset($_POST['subscription_add_1'])){
		$id = mysqli_real_escape_string($db->connection, $_POST['subscription_add_1']);
		$current = mysqli_fetch_assoc(mysqli_query($db->connection, "SELECT subscription FROM bot_subscriptions WHERE subscription_id = '$id'"))['subscription'];
		mysqli_query($db->connection, "UPDATE bot_subscriptions SET subscription = '".($current + 1)."' WHERE subscription_id = '$id'");
		$alert = "<div class=\"alert alert-success\" style=\"font-size:14px;\">Subscriber has been granted 30 additional days.</div>";
	}elseif(isset($_POST['subscription_delete'])){
		$id = mysqli_real_escape_string($db->connection, $_POST['subscription_delete']);
		mysqli_query($db->connection, "DELETE FROM bot_subscriptions WHERE subscription_id='$id'");
		$alert = "<div class=\"alert alert-success\" style=\"font-size:14px;\">Subscriber has been deleted.</div>";
	}elseif(isset($_POST['submit'])){
		$handle = mysqli_real_escape_string($db->connection, $_POST['inputHandle']);
		$discord = mysqli_real_escape_string($db->connection, $_POST['inputDiscord']);
		mysqli_query($db->connection, "INSERT INTO bot_subscriptions (handle, discord_id) VALUES ('$handle', '$discord')");
		$alert = "<div class=\"alert alert-success\" style=\"font-size:14px;\">Subscriber has been added.</div>";
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
                                        <h1>Bot Subscriptions</h1>
                                    </div>
                                </div>
                                <div class="col-sm-6 hidden-xs">
                                    <div class="header-section">
                                        <ul class="breadcrumb breadcrumb-top">
                                            <li>Admin</li>
                                            <li><a href="panel.php">Panel</a></li>
											<li><a href="">Bot Subscriptions</a></li>
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
                                <h2>Discord Bot Subscriptions</h2> <button title="Add Note" class="btn btn-info pull-right hidden-print" data-target="#modalAdd" data-toggle="modal"><span class="fas fa-plus"></span> Add</button>
                            </div>
							
							</form>
							<?php
								$db = new database;
								$db->connect();
								
								if(isset($_POST['search'])){
									$search = mysqli_real_escape_string($db->connection,$_POST['search']);
									$subscribers = mysqli_query($db->connection,"SELECT * FROM bot_subscriptions WHERE discord_id LIKE '%".$search."%' OR handle LIKE '%".$search."%' ORDER BY handle");
								}else{
									$subscribers = mysqli_query($db->connection,"SELECT * FROM bot_subscriptions ORDER BY handle");
								}
								echo "<table class=\"table table-bordered table-striped table-responsive table-hover\"><tr><th>Handle</th><th>Discord ID</th><th>Days Remaining</th><th>Actions</th></tr>";
								while($data = mysqli_fetch_assoc($subscribers)){
									$handle = $data['handle'];
									$discord = $data['discord_id'];
									$subscription = $data['subscription'];
									$subscription_id = $data['subscription_id'];
									echo "<tr><td>".ucwords($handle)."</td><td>".$discord."</td><td>".$subscription."</td><td align=\"center\"><form method=\"post\" style=\"display:inline\" action=\"\"><input type=\"submit\" class=\"btn btn-sm btn-primary\" value=\"Add 30\"><input type=\"hidden\" name=\"subscription_add\" value=\"".$subscription_id."\"></form><form method=\"post\" style=\"margin-left:10px;display:inline\" action=\"\"><input type=\"submit\" class=\"btn btn-sm btn-primary\" value=\"Add 1\"><input type=\"hidden\" name=\"subscription_add_1\" value=\"".$subscription_id."\"></form><form method=\"post\" style=\"margin-left:10px;display:inline\" action=\"\"><input type=\"submit\" class=\"btn btn-sm btn-warning\" onclick=\"return confirm('Do you really want to delete the selected subscriber?');\" value=\"Delete\"><input type=\"hidden\" name=\"subscription_delete\" value=\"".$subscription_id."\"></form></td></tr>";
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
									<h4 class="modal-title">Add Subscriber</h4>
								</div>
								<form class="form-horizontal" role="form" method="post" action="">
									<div class="modal-body">
										<div class="form-group">
											<label class="col-sm-3" for="inputHandle">Handle</label>
											<div class="col-sm-9"><input type="text" class="form-control" id="inputHandle" name="inputHandle" placeholder="{handle}"></div>
										</div>
										<div class="form-group">
											<label class="col-sm-3" for="inputDiscord">Discord ID</label>
											<div class="col-sm-9"><input type="text" class="form-control" id="inputDiscord" name="inputDiscord" placeholder="{discord ID}"></div>
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