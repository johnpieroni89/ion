<?php 
    include("autoload.php");
    global $db;
    global $site;
    global $session;
    global $account;
    $session->check_login();
	
	if ($_SESSION['user_privs']['sentientprofiles_general'] == 0 && $_SESSION['user_privs']['admin'] == 0) {
		header("Location: index.php");
	}
	
	if(isset($_POST['search'])){
		header("Location: sentientprofiles.php?search=".urlencode($_POST['search'])."");
	}
	
	$uid = mysqli_real_escape_string($db->connection,$_GET["uid"]);
	if($handle = mysqli_fetch_assoc(mysqli_query($db->connection,"SELECT * FROM characters WHERE uid = '1:".$uid."'"))["handle"]){
		if(!isset($_POST)){}
	}else{
		header("Location: sentientprofiles.php");
	}
	
	if(!isset($_POST) && !isset($_GET)){
		mysqli_query($db->connection, "INSERT INTO logs_activities (user_id, log_type, details, timestamp) VALUES ('".$_SESSION['user_id']."', '0', 'VIEW: User opened the profile for <a target=\"blank\" href=\"../profile.php?uid=$uid\">$handle</a>', '".swc_time(time(),TRUE)["timestamp"]."')");
	}
	
	include("assets/php/sentientprofiles/processor.php");
?>

	<?php include("assets/php/_head.php"); ?>
    <body>
        <!-- Page Wrapper -->
        <div id="page-wrapper" class="page-loading">
            <?php include("assets/php/_preloader.php"); ?>
			<!-- Page Container -->
            <div id="page-container" class="header-fixed-top sidebar-visible-lg-full">
                
                <?php include("assets/php/_sidebar-alt.php"); ?>
                <?php include("assets/php/_sidebar.php"); ?>

                <!-- Main Container -->
                <div id="main-container">
                    <?php include("assets/php/_header.php"); ?>

                    <!-- Page content -->
                    <div id="page-content" style="overflow:auto;">
                        <!-- Page Header -->
                        <div class="content-header">
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="header-section">
                                        <h1>Profile</h1>
                                    </div>
                                </div>
                                <div class="col-sm-6 hidden-xs">
                                    <div class="header-section">
                                        <ul class="breadcrumb breadcrumb-top">
                                            <li>Databases</li>
                                            <li><a href="sentientprofiles.php">Sentient Profiles</a></li>
                                            <li><a href="profile.php?uid=<?php echo $uid; ?>">Profile</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
						<?php if(isset($session->alert)){echo $session->alert;} ?>
						
                        <!-- Example Block -->
                        <div class="block" style="overflow:hidden;">
                            <!-- Example Title -->
                            <div class="block-title hidden-print">
								<h2><?php echo $handle; ?></h2>
								<a href="profile.php?uid=<?php echo $uid; ?>"><button title="Full Profile" class="btn btn-info"> Full Profile</button></a>
								<a href="profile.php?uid=<?php echo $uid; ?>&view=1"><button title="Overview" class="btn btn-primary"> Overview</button></a>
								<a href="profile.php?uid=<?php echo $uid; ?>&view=2"><button title="Skills" class="btn btn-primary"> Skills</button></a>
								<!--<a href="profile.php?uid=<?php //echo $uid; ?>&view=3"><button title="Communications" class="btn btn-primary"> Communications</button></a>-->
								<a href="profile.php?uid=<?php echo $uid; ?>&view=4"><button title="Social Network" class="btn btn-primary"> Social Network</button></a>
								<a href="profile.php?uid=<?php echo $uid; ?>&view=5"><button title="Transactions" class="btn btn-primary"> Transactions</button></a>
								<!--<a href="profile.php?uid=<?php //echo $uid; ?>&view=6"><button title="Geolocation" class="btn btn-primary"> Geolocation</button></a>-->
								<a href="profile.php?uid=<?php echo $uid; ?>&view=7"><button title="Notes" class="btn btn-primary"> Notes</button></a>
								<h2 class="pull-right"><?php if(isset($results_count)){echo "Results: ".$results_count;}?></h2>
							</div>
							<div class="block-content col-sm-12" style="margin:0px;padding:0px;">
							<?php
								include("assets/php/sentientprofiles/viewCard.php");
								
								if(!isset($_GET['view']) || $_GET['view'] == 0){
									include("assets/php/sentientprofiles/viewAll.php");
								}elseif($_GET['view'] == 1){
									include("assets/php/sentientprofiles/viewOverview.php");
								}elseif($_GET['view'] == 2){
									include("assets/php/sentientprofiles/viewSkills.php");
								}elseif($_GET['view'] == 3){
									include("assets/php/sentientprofiles/viewCommunications.php");
								}elseif($_GET['view'] == 4){
									include("assets/php/sentientprofiles/viewSocial.php");
									//echo "<div class=\"svg-container\"></div>";
								}elseif($_GET['view'] == 5){
									include("assets/php/sentientprofiles/viewTransactions.php");
								}elseif($_GET['view'] == 6){
									include("assets/php/sentientprofiles/viewGeolocations.php");
								}elseif($_GET['view'] == 7){
									include("assets/php/sentientprofiles/notes.php");
								} 
							?>
							</div>
                        </div>
                    </div>
					
					<div class="modal fade" id="modalAdd">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header">
									<button class="close" aria-hidden="true" type="button" data-dismiss="modal">×</button>
									<h4 class="modal-title">Add Profile</h4>
								</div>
								<form class="form" role="form" method="post" action="sentientprofiles.php" id=\"addForm\">
									<div class="modal-body">
										<div class="form-group">
											<label class="col-sm-2" for="handle">Handle</label>
											<div class="col-sm-10">
												<input type="text" name="handle" style="width:100%;" placeholder="{Handle}" required>
											</div>
										</div>
									</div>
									<div class="modal-footer">
										<button class="btn btn-default pull-left" type="button" data-dismiss="modal">Cancel</button>
										<input id="submit" class="btn btn-primary " type="submit" value="Add">
										<input id="type" type="hidden" name="createProfile" value="add">
									</div>
								</form>
							</div>
						</div>
					</div>
                </div>
            </div>
        </div>
		<?php include("assets/php/_end.php"); ?>
	</body>
</html>