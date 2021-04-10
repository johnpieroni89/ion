<?php 
	error_reporting(0);
	include("assets/php/database.php");
	include("assets/php/functions.php");
	include("assets/php/session.php");
	include("assets/php/paginator.php");
	include("assets/php/acct/check.php");
	
	if ($_SESSION['user_privs']['sentientprofiles_general'] == 0 && $_SESSION['user_privs']['admin'] == 0) {
		header("Location: index.php");
	}
	
	$db = new database;
	$db->connect();
	
	if(!empty($_POST['search']) || !empty($_GET['search'])){
		if(isset($_POST['search'])){
			$search = mysqli_real_escape_string($db->connection, $_POST['search']);
		}elseif(isset($_GET['search'])){
			$search = mysqli_real_escape_string($db->connection, $_GET['search']);
		}
		$search_header = $search;
		mysqli_query($db->connection, "INSERT INTO logs_activities (user_id, log_type, details, timestamp) VALUES ('".$_SESSION['user_id']."', '0', 'SEARCH: Broad search on profiles containing \"$search\"', '".swc_time(time(),TRUE)["timestamp"]."')");
		$search = "WHERE handle LIKE '%$search%' OR uid LIKE '1:$search%' OR faction LIKE '%$search%'";
	}else{
		$search = "";
	}
	$search_string = "SELECT * FROM characters LEFT JOIN characters_faction ON characters.uid = characters_faction.character_uid ".$search."ORDER BY handle ASC";
	$results_count = mysqli_num_rows(mysqli_query($db->connection,$search_string));

	if(isset($_POST["createProfile"])){
		if(add_character($_POST["handle"])){
			$alert = "<div class=\"alert alert-success\" style=\"font-size:14px;\">A profile for ".explode(";",$_POST["handle"])[0]." has been created</div>";
		}else{
			$alert = "<div class=\"alert alert-warning\" style=\"font-size:14px;\">".explode(";",$_POST["handle"])[0]." already exists or was not able to be validated</div>";
		}
	}
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
                                        <h1>Sentient Profiles</h1>
                                    </div>
                                </div>
                                <div class="col-sm-6 hidden-xs">
                                    <div class="header-section">
                                        <ul class="breadcrumb breadcrumb-top">
                                            <li>Databases</li>
                                            <li><a href="sentientprofiles.php">Sentient Profiles</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
						<?php if(isset($alert)){echo $alert;} ?>
						
                        <!-- Example Block -->
                        <div class="block" style="overflow:hidden;">
                            <!-- Example Title -->
                            <div class="block-title hidden-print">
								<h2>Profiles</h2>
								<button title="Add new profile" class="btn btn-primary" data-target="#modalAdd" data-toggle="modal">
									<span class="fas fa-plus-circle"></span> Add
								</button>
								<h2 class="pull-right"><?php if(isset($results_count)){echo "Results: ".$results_count;}?></h2>
							</div>
							<div class="block-content col-sm-12" style="margin:0px;padding:0px;">
							<?php
								include("assets/php/sentientprofiles/search.php");
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