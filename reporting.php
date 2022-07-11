<?php 
    include("autoload.php");
    global $db;
    global $site;
    global $session;
    global $account;
    $session->check_login();
    error_reporting(0);
	
	if ($_SESSION['user_privs']['reporting_publish'] == 0 && $_SESSION['user_privs']['reporting_view'] == 0 && $_SESSION['user_privs']['admin'] == 0) {
		header("Location: index.php");
	}
	
	if($_GET['delete'] && $_SESSION['user_privs']['admin'] > 0){
		$delete = mysqli_real_escape_string($db->connection, $_GET['delete']);
		$serial = mysqli_fetch_assoc(mysqli_query($db->connection, "SELECT serial FROM reporting_reports WHERE report_id = '$delete'"))['serial'];
		mysqli_query($db->connection, "DELETE FROM reporting_reports WHERE report_id = '".$delete."'");
		mysqli_query($db->connection, "DELETE FROM reporting_reports_coi WHERE report_id = '".$delete."'");
		array_map('unlink', glob("reports/$serial/*"));
		rmdir("reports/$serial");
		$_SESSION['alert'] = "<div class=\"alert alert-warning\" style=\"font-size:14px;\">The report ".$serial." has been deleted.</div>";
		header("Location: reporting.php");
	}
	
	if($_POST['submit'] == "Publish"){
		$inputType = mysqli_real_escape_string($db->connection, $_POST['inputType']);
		$inputTitle = mysqli_real_escape_string($db->connection, $_POST['inputTitle']);
		$inputFocus = mysqli_real_escape_string($db->connection, $_POST['inputFocus']);
		$inputContent = mysqli_real_escape_string($db->connection, $_POST['inputContent']);
		
		if($inputType == 1){ //Submit Report
			$inputIN = mysqli_real_escape_string($db->connection, $_POST['inputIN']);
			$serial = $inputFocus."-".swc_time(time(),TRUE)['timestamp'];
			if(isset($_FILES['inputFiles'])){
				$len = count($_FILES['inputFiles']['tmp_name']);
				for($i = 0; $i < $len; $i++) {
					if($_FILES['inputFiles']['error'][$i] == "" || $_FILES['inputFiles']['error'][$i] == 0){
						$file = $_FILES['inputFiles']['tmp_name'][$i];
						$info = pathinfo($_FILES['inputFiles']['name'][$i]);
						$ext = $info['extension']; // get the extension of the file
						$newname = md5_file($file).".".$ext;
						if (!file_exists('reports'.DIRECTORY_SEPARATOR)) {
							mkdir('reports'.DIRECTORY_SEPARATOR);
						}
						if (!file_exists("reports".DIRECTORY_SEPARATOR.$serial.DIRECTORY_SEPARATOR)){
							mkdir("reports".DIRECTORY_SEPARATOR.$serial.DIRECTORY_SEPARATOR);
						}
						$target = "reports".DIRECTORY_SEPARATOR.$serial.DIRECTORY_SEPARATOR.$newname."";
						move_uploaded_file( $_FILES['inputFiles']['tmp_name'][$i], $target);
					}
				}
			}
			mysqli_query($db->connection, "INSERT INTO reporting_reports (serial, author, title, focus, content, timestamp) VALUES('$serial', '".$_SESSION['user_id']."', '$inputTitle', '$inputFocus', '$inputContent', '".swc_time(time(),TRUE)['timestamp']."')");
			$report_id = mysqli_fetch_assoc(mysqli_query($db->connection, "SELECT report_id FROM reporting_reports WHERE serial = '$serial'"))['report_id'];
			if(isset($inputIN)){
				mysqli_query($db->connection, "DELETE FROM reporting_reports_coi WHERE report_id = '$report_id'");
				mysqli_query($db->connection, "INSERT INTO reporting_reports_coi (report_id, need_id) VALUES ('$report_id', '$inputIN')");
			}
			mysqli_query($db->connection, "INSERT INTO logs_activities (user_id, log_type, details, timestamp) VALUES ('".$_SESSION['user_id']."', '4', 'PUBLISH: The Report <a target=\"blank\" href=\"../reporting.php?view=1&serial=$serial\">".$serial."</a> was published', '".swc_time(time(),TRUE)["timestamp"]."')");
			$session->alert = "<div class=\"alert alert-success\" style=\"font-size:14px;\">The Report ".$serial." was published.</div>";
		}else if($inputType == 2){ //Submit Intelligence Need
			$serial = "IN-".swc_time(time(),TRUE)['day'].swc_time(time(),TRUE)['hour'].swc_time(time(),TRUE)['minute']."-".swc_time(time(),TRUE)['year'];
			mysqli_query($db->connection, "INSERT INTO reporting_needs (serial, author, title, focus, content, timestamp) VALUES('$serial', '".$_SESSION['user_id']."', '$inputTitle', '$inputFocus', '$inputContent', '".swc_time(time(),TRUE)['timestamp']."')");
			mysqli_query($db->connection, "INSERT INTO logs_activities (user_id, log_type, details, timestamp) VALUES ('".$_SESSION['user_id']."', '4', 'PUBLISH: The Intelligence Need <a target=\"blank\" href=\"../reporting.php?view=2&serial=$serial\">".$serial."</a> was published', '".swc_time(time(),TRUE)["timestamp"]."')");
			$session->alert = "<div class=\"alert alert-success\" style=\"font-size:14px;\">The Intelligence Need ".$serial." was published.</div>";
		}
	}else if($_POST['submit'] == "Save Report"){
		$serial = mysqli_real_escape_string($db->connection, $_POST['inputSerial']);
		$inputTitle = mysqli_real_escape_string($db->connection, $_POST['inputTitle']);
		$inputFocus = mysqli_real_escape_string($db->connection, $_POST['inputFocus']);
		$inputContent = mysqli_real_escape_string($db->connection, $_POST['inputContent']);
		$inputIN = mysqli_real_escape_string($db->connection, $_POST['inputIN']);
		$report_id = mysqli_fetch_assoc(mysqli_query($db->connection, "SELECT report_id FROM reporting_reports WHERE serial = '$serial'"))['report_id'];
		
		if(isset($inputIN) && $inputIN != "" && $inputIN != 0){
			mysqli_query($db->connection, "DELETE FROM reporting_reports_coi WHERE report_id = '$report_id'");
			mysqli_query($db->connection, "INSERT INTO reporting_reports_coi (report_id, need_id) VALUES ('$report_id', '$inputIN')");
		}else{
			mysqli_query($db->connection, "DELETE FROM reporting_reports_coi WHERE report_id = '$report_id'");
		}
		
		if(isset($_FILES['inputFiles'])){
			$len = count($_FILES['inputFiles']['tmp_name']);
			for($i = 0; $i < $len; $i++) {
				if($_FILES['inputFiles']['error'][$i] == "" || $_FILES['inputFiles']['error'][$i] == 0){
					$file = $_FILES['inputFiles']['tmp_name'][$i];
					$info = pathinfo($_FILES['inputFiles']['name'][$i]);
					$ext = $info['extension']; // get the extension of the file
					$newname = md5_file($file).".".$ext;
					if (!file_exists('reports'.DIRECTORY_SEPARATOR)) {
						mkdir('reports'.DIRECTORY_SEPARATOR);
					}
					if (!file_exists("reports".DIRECTORY_SEPARATOR.$serial.DIRECTORY_SEPARATOR)){
						mkdir("reports".DIRECTORY_SEPARATOR.$serial.DIRECTORY_SEPARATOR);
					}
					$target = "reports".DIRECTORY_SEPARATOR.$serial.DIRECTORY_SEPARATOR.$newname."";
					move_uploaded_file( $_FILES['inputFiles']['tmp_name'][$i], $target);
				}
			}
		}
		mysqli_query($db->connection, "UPDATE reporting_reports SET title = '$inputTitle', focus = '$inputFocus', content = '$inputContent' WHERE serial = '$serial'");
		mysqli_query($db->connection, "INSERT INTO logs_activities (user_id, log_type, details, timestamp) VALUES ('".$_SESSION['user_id']."', '4', 'EDIT: The Report <a target=\"blank\" href=\"../reporting.php?view=1&serial=$serial\">".$serial."</a> was edited', '".swc_time(time(),TRUE)["timestamp"]."')");
		$session->alert = "<div class=\"alert alert-success\" style=\"font-size:14px;\">The Report ".$serial." was edited.</div>";
	}else if($_POST['submit'] == "Save IN"){
		$serial = mysqli_real_escape_string($db->connection, $_POST['inputSerial']);
		$inputTitle = mysqli_real_escape_string($db->connection, $_POST['inputTitle']);
		$inputFocus = mysqli_real_escape_string($db->connection, $_POST['inputFocus']);
		$inputContent = mysqli_real_escape_string($db->connection, $_POST['inputContent']);
		mysqli_query($db->connection, "UPDATE reporting_needs SET title = '$inputTitle', focus = '$inputFocus', content = '$inputContent' WHERE serial = '$serial'");
		mysqli_query($db->connection, "INSERT INTO logs_activities (user_id, log_type, details, timestamp) VALUES ('".$_SESSION['user_id']."', '4', 'EDIT: The Information Need <a target=\"blank\" href=\"../reporting.php?view=2&serial=$serial\">".$serial."</a> was edited', '".swc_time(time(),TRUE)["timestamp"]."')");
		$session->alert = "<div class=\"alert alert-success\" style=\"font-size:14px;\">The Information Need ".$serial." was edited.</div>";
	}
	
	if(isset($_GET['serial'])){
		$serial = mysqli_real_escape_string($db->connection, $_GET['serial']);
		$reportData = mysqli_fetch_assoc(mysqli_query($db->connection, "SELECT * FROM reporting_needs WHERE serial = '".$serial."'"));
	}
	
	if(isset($_POST['inputAddUser']) && $_SESSION['user_privs']['admin'] != 0){
		$user_id = mysqli_real_escape_string($db->connection, $_POST['inputAddUser']);
		if(mysqli_num_rows(mysqli_query($db->connection, "SELECT * FROM reporting_needs_coi_users WHERE need_id = '".$reportData['need_id']."' AND user_id = '$user_id'")) == 0){
			mysqli_query($db->connection, "INSERT INTO reporting_needs_coi_users (need_id, user_id) VALUES ('".$reportData['need_id']."', '$user_id')");
			$session->alert = "<div class=\"alert alert-success\" style=\"font-size:14px;\">Successfully added user to this Information Need.</div>";
		}else{
			$session->alert = "<div class=\"alert alert-warning\" style=\"font-size:14px;\">User can already view this Information Need.</div>";
		}
	}else if(isset($_POST['inputAddUsergroup']) && $_SESSION['user_privs']['admin'] != 0){
		$usergroup_id = mysqli_real_escape_string($db->connection, $_POST['inputAddUsergroup']);
		if(mysqli_num_rows(mysqli_query($db->connection, "SELECT * FROM reporting_needs_coi_usergroups WHERE need_id = '".$reportData['need_id']."' AND usergroup_id = '$usergroup_id'")) == 0){
			mysqli_query($db->connection, "INSERT INTO reporting_needs_coi_usergroups (need_id, usergroup_id) VALUES ('".$reportData['need_id']."', '$usergroup_id')");
			$session->alert = "<div class=\"alert alert-success\" style=\"font-size:14px;\">Successfully added usergroup to this Information Need.</div>";
		}else{
			$session->alert = "<div class=\"alert alert-warning\" style=\"font-size:14px;\">Usergroup can already view this Information Need.</div>";
		}
	}
	
	if(isset($_GET['dUser']) && $_SESSION['user_privs']['admin'] != 0){
		mysqli_query($db->connection, "DELETE FROM reporting_needs_coi_users WHERE need_id = '".$reportData['need_id']."' AND user_id = '".$_GET['dUser']."'");
		$session->alert = "<div class=\"alert alert-warning\" style=\"font-size:14px;\">User has been removed from the Information Need.</div>";
	}else if(isset($_GET['dUsergroup']) && $_SESSION['user_privs']['admin'] != 0){
		mysqli_query($db->connection, "DELETE FROM reporting_needs_coi_usergroups WHERE need_id = '".$reportData['need_id']."' AND usergroup_id = '".$_GET['dUsergroup']."'");
		$session->alert = "<div class=\"alert alert-warning\" style=\"font-size:14px;\">Usergroup has been removed from the Information Need.</div>";
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
                                        <h1>Reporting</h1>
                                    </div>
                                </div>
                                <div class="col-sm-6 hidden-xs">
                                    <div class="header-section">
                                        <ul class="breadcrumb breadcrumb-top">
                                            <li>Databases</li>
                                            <li><a href="reporting.php">Reporting</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
						<?php if(isset($session->alert)){echo $session->alert;} ?>
						
                        <!-- Example Block -->
                        <div class="block">
                            <!-- Example Title -->
                            <div class="block-title hidden-print">
								<h2>Data</h2>
								<?php
									$db = new Database;
									$db->connect();
									if($_SESSION['user_privs']['reporting_view'] > 0 || $_SESSION['user_privs']['admin'] > 0){
										echo "
											<a href=\"reporting.php?view=1\">
											<button title=\"Reports\" class=\"btn btn-primary\">
												<span class=\"fas fa-file-contract\"></span> Reports
											</button></a>
										";
									}
									if($_SESSION['user_privs']['reporting_view'] > 0 || $_SESSION['user_privs']['admin'] > 0){
										echo "
											<a href=\"reporting.php?view=2\">
											<button title=\"Information Needs\" class=\"btn btn-primary\">
												<span class=\"fas fa-archive\"></span> Information Needs
											</button></a>
										";
									}
									if($_SESSION['user_privs']['reporting_publish'] > 0 || $_SESSION['user_privs']['admin'] > 0){
										echo "
											<a href=\"reporting.php?view=3\">
											<button title=\"Publish\" class=\"btn btn-info\">
												<span class=\"fas fa-folder-plus\"></span> Publish
											</button></a>
										";
									}
								?>
							</div>
                            
							<?php
								if($_GET['view'] == 1 && $_GET['serial'] == ""){ //View report list
									include("assets/php/reporting/reportsList.php");
								}else if($_GET['view'] == 1 && $_GET['serial'] != "" && $_GET['edit'] == ""){ //View report
									include("assets/php/reporting/reportsView.php");
								}else if($_GET['view'] == 1 && $_GET['serial'] != "" && $_GET['edit'] == "1"){ //Edit report
									include("assets/php/reporting/reportsEdit.php");
								}else if($_GET['view'] == 2 && $_GET['serial'] == ""){ //View IN List
									include("assets/php/reporting/needsList.php");
								}else if($_GET['view'] == 2 && $_GET['serial'] != "" && $_GET['edit'] == ""){ //View IN
									include("assets/php/reporting/needsView.php");
								}else if($_GET['view'] == 2 && $_GET['serial'] != "" && $_GET['edit'] == "1"){ //Edit IN
									include("assets/php/reporting/needsEdit.php");
								}else if($_GET['view'] == 3){ //Draft Product
									include("assets/php/reporting/publish.php");
								}else{ //View reporting metrics
									include("assets/php/reporting/metrics.php");
								}
							?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
		<?php include("assets/php/_end.php"); ?>
		<script>
			function changeFields() {
				inputType = document.getElementById("inputType").value;
				if(inputType == 2){
					document.getElementById("assignedIN").style.display = "none";
				}else{
					document.getElementById("assignedIN").style.display = "block";
				}
				
				if(inputType == 2){
					document.getElementById("attachmentsField").style.display = "none";
				}else{
					document.getElementById("attachmentsField").style.display = "block";
				}
			}
		</script>
	</body>
</html>