<?php 
error_reporting(0);
include("../assets/php/database.php");
include("../assets/php/session.php");
include("../assets/php/functions.php");
include("../assets/php/acct/check.php");
include("../assets/php/_head.php");
if(!isset($_SESSION['user_id']) || $_SESSION['user_privs']['admin'] == 0){ header("Location: ../index.php");}

if(isset($_GET['user_id']) && $_GET['user_id'] != ""){
		$db = new database;
		$db->connect();
		$user_id = mysqli_real_escape_string($db->connection,$_GET['user_id']);
		
		if($_POST){
			$admin = $_POST['inputAdmin'];
			$sentientprofiles_view = $_POST['input_sentientprofiles_View'];
			$sentientprofiles_delete = $_POST['input_sentientprofiles_Delete'];
			$galaxydata_search = $_POST['input_galaxydata_Search'];
			$galaxydata_analytics = $_POST['input_galaxydata_Analytics'];
			$galaxydata_delete = $_POST['input_galaxydata_Delete'];
			$geolocation = $_POST['input_geolocation'];
			$signalsanalysis_search = $_POST['input_signalsanalysis_Search'];
			$signalsanalysis_upload = $_POST['input_signalsanalysis_Upload'];
			$signalsanalysis_export = $_POST['input_signalsanalysis_Export'];
			$signalsanalysis_analytics = $_POST['input_signalsanalysis_Analytics'];
			$signalsanalysis_delete = $_POST['input_signalsanalysis_Delete'];
			$factioncatalog_view = $_POST['input_factioncatalog_View'];
			$factioncatalog_delete = $_POST['input_factioncatalog_Delete'];
			$reporting_publish = $_POST['input_reporting_Publish'];
			$reporting_view = $_POST['input_reporting_View'];
			$reporting_delete = $_POST['input_reporting_Delete'];
			$transactions = $_POST['input_transactions'];
			$flashnews = $_POST['input_flashnews'];
			mysqli_query($db->connection, "UPDATE users_privs SET admin='$admin', sentientprofiles_general='$sentientprofiles_view', sentientprofiles_delete='$sentientprofiles_delete', galaxydata_search='$galaxydata_search', galaxydata_analytics='$galaxydata_analytics', galaxydata_delete='$galaxydata_delete', geolocation='$geolocation', signalsanalysis_search='$signalsanalysis_search', signalsanalysis_upload='$signalsanalysis_upload', signalsanalysis_export='$signalsanalysis_export', signalsanalysis_analytics='$signalsanalysis_analytics', signalsanalysis_delete='$signalsanalysis_delete', factioncatalog_general='$factioncatalog_view', factioncatalog_delete='$factioncatalog_delete', reporting_publish='$reporting_publish', reporting_view='$reporting_view', reporting_delete='$reporting_delete', transactions='$transactions', flashnews='$flashnews' WHERE user_id = '$user_id'");
			mysqli_query($db->connection, "UPDATE users SET refresh = '1' WHERE user_id = '$user_id'");
			
			$alert = $alert."<div class=\"alert alert-success\" style=\"font-size:14px;\">Successfully modified the account privileges</div>";
		}
		
		$user = mysqli_fetch_assoc(mysqli_query($db->connection,"SELECT * FROM users LEFT JOIN users_privs ON users.user_id = users_privs.user_id WHERE users.user_id = '$user_id'"));
		if(count($user) == 0){header("Location: users.php");}
		
	}else{
		header("Location: users.php");
	}
?>

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
                                    <h1>User Privs</h1>
                                </div>
                            </div>
                            <div class="col-sm-6 hidden-xs">
                                <div class="header-section">
                                    <ul class="breadcrumb breadcrumb-top">
                                        <li>Admin</li>
                                        <li><a href="panel.php">Panel</a></li>
                                        <li><a href="users.php">Users</a></li>
                                        <li><a href="">User Privs</a></li>
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
                            <h2><?php echo $user['username']; ?></h2>
                        </div>
                        <form class="form-horizontal" role="form" method="post" action="">
                            <div class="modal-body">  
                                <div class="form-group">
                                    <label class="col-sm-3" for="inputName">Username</label>
                                    <div class="col-sm-9"><input class="form-control" id="inputName" name="inputName" type="text" placeholder="{Usergroup Name}" value="<?php echo $user['username']; ?>" disabled></div>
                                </div>
                                <?php
                                    if($_SESSION['user_privs']['admin'] == 2) {
                                            echo "<div class=\"form-group\"><label class=\"col-sm-3\" for=\"inputAdmin\">Admin Privileges</label><div class=\"col-sm-9\">
											<select class=\"form-control\" id=\"inputAdmin\" name=\"inputAdmin\">";
											echo "<option value=\"0\">0 - Standard User</option>";
											if($user['admin'] == 1){ echo "<option selected value=\"1\">1 - Admin</option>";}else{echo "<option value=\"1\">1 - Admin</option>";}
											if($user['admin'] == 2){ echo "<option selected value=\"2\">2 - Super Admin</option>";}else{echo "<option value=\"2\">2 - Super Admin</option>";}
											echo "</select></div></div>";
                                    }
                                ?>
								<hr/>
                                <div class="form-group">
                                    <label class="col-sm-3" for="input_sentientprofiles_View">Sentient Profiles View</label>
                                    <div class="col-sm-9">
                                        <select class="form-control" id="input_sentientprofiles_View" name="input_sentientprofiles_View">
                                            <option value="0">0 - None</option>
                                            <option <?php if($user['sentientprofiles_general'] == 1){ echo "selected";}?> value="1">1 - May view profiles and personal entries</option>
                                            <option <?php if($user['sentientprofiles_general'] == 2){ echo "selected";}?> value="2">2 - May view profiles and all entries</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-3" for="input_sentientprofiles_Delete">Sentient Profiles Delete</label>
                                    <div class="col-sm-9">
                                        <select class="form-control" id="input_sentientprofiles_Delete" name="input_sentientprofiles_Delete">
                                            <option value="0">0 - No</option>
                                            <option <?php if($user['sentientprofiles_delete'] == 1){ echo "selected";}?> value="1">1 - May delete profiles and entries</option>
                                        </select>
                                    </div>
                                </div>
								<hr/>
                                <div class="form-group">
                                    <label class="col-sm-3" for="input_galaxydata_Search">Galaxy Data Search</label>
                                    <div class="col-sm-9">
                                        <select class="form-control" id="input_galaxydata_Search" name="input_galaxydata_Search">
                                            <option value="0">0 - No</option>
                                            <option <?php if($user['galaxydata_search'] == 1){ echo "selected";}?> value="1">1 - May search galactic records</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-3" for="input_galaxydata_Analytics">Galaxy Data Analytics</label>
                                    <div class="col-sm-9">
                                        <select class="form-control" id="input_galaxydata_Analytics" name="input_galaxydata_Analytics">
                                            <option value="0">0 - None</option>
                                            <option <?php if($user['galaxydata_analytics'] == 1){ echo "selected";}?> value="1">1 - Can execute analytics</option>
                                            <option <?php if($user['galaxydata_analytics'] == 2){ echo "selected";}?> value='2'>2 - Can execute analytics and save results</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-3" for="input_galaxydata_Delete">Galaxy Data Delete</label>
                                    <div class="col-sm-9">
                                        <select class="form-control" id="input_galaxydata_Delete" name="input_galaxydata_Delete">
                                            <option value="0">0 - No</option>
                                            <option <?php if($user['galaxydata_delete'] == 1){ echo "selected";}?> value="1">1 - Yes</option>
                                        </select>
                                    </div>
                                </div>
								<hr/>
								<div class="form-group">
                                    <label class="col-sm-3" for="input_geolocation">Geolocation Data</label>
                                    <div class="col-sm-9">
                                        <select class="form-control" id="input_geolocation" name="input_geolocation">
                                            <option value="0">0 - No</option>
                                            <option <?php if($user['geolocation'] == 1){ echo "selected";}?> value="1">1 - Can view/search</option>
                                            <option <?php if($user['geolocation'] == 2){ echo "selected";}?> value="1">1 - Can view/search/add</option>
                                        </select>
                                    </div>
                                </div>
								<hr/>
                                <div class="form-group">
                                    <label class="col-sm-3" for="input_signalsanalysis_Search">Signals Analysis Search</label>
                                    <div class="col-sm-9">
                                        <select class="form-control" id="input_signalsanalysis_Search" name="input_signalsanalysis_Search">
                                            <option value="0">0 - None</option>
                                            <option <?php if($user['signalsanalysis_search'] == 1){ echo "selected";}?> value="1">1 - Can view standard data</option>
                                            <option <?php if($user['signalsanalysis_search'] == 2){ echo "selected";}?> value='2'>2 - Can view sensitive data (references to inventory scans)</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-3" for="input_signalsanalysis_Upload">Signals Analysis Upload</label>
                                    <div class="col-sm-9">
                                        <select class="form-control" id="input_signalsanalysis_Upload" name="input_signalsanalysis_Upload">
                                            <option value="0">0 - No</option>
                                            <option <?php if($user['signalsanalysis_upload'] == 1){ echo "selected";}?> value="1">1 - Upload to buffer</option>
                                            <option <?php if($user['signalsanalysis_upload'] == 2){ echo "selected";}?> value='2'>2 - Upload to database</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-3" for="input_signalsanalysis_Export">Signals Analysis Export</label>
                                    <div class="col-sm-9">
                                        <select class="form-control" id="input_signalsanalysis_Export" name="input_signalsanalysis_Export">
                                            <option value="0">0 - No</option>
                                            <option <?php if($user['signalsanalysis_export'] == 1){ echo "selected";}?> value="1">1 - May export results</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-3" for="input_signalsanalysis_Analytics">Signals Analysis Analytics</label>
                                    <div class="col-sm-9">
                                        <select class="form-control" id="input_signalsanalysis_Analytics" name="input_signalsanalysis_Analytics">
                                            <option value="0">0 - No</option>
                                            <option <?php if($user['signalsanalysis_analytics'] == 1){ echo "selected";}?> value="1">1 - May execute analytics</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-3" for="input_signalsanalysis_Delete">Signals Analysis Delete</label>
                                    <div class="col-sm-9">
                                        <select class="form-control" id="input_signalsanalysis_Delete" name="input_signalsanalysis_Delete">
                                            <option value="0">0 - No</option>
                                            <option <?php if($user['signalsanalysis_delete'] == 1){ echo "selected";}?> value="1">1 - May delete records</option>
                                        </select>
                                    </div>
                                </div>
								<hr/>
                                <div class="form-group">
                                    <label class="col-sm-3" for="input_factioncatalog_View">Faction Catalog View</label>
                                    <div class="col-sm-9">
                                        <select class="form-control" id="input_factioncatalog_View" name="input_factioncatalog_View">
                                            <option value="0">0 - No</option>
                                            <option <?php if($user['factioncatalog_general'] == 1){ echo "selected";}?> value="1">1 - Yes</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-3" for="input_factioncatalog_Delete">Faction Catalog Delete</label>
                                    <div class="col-sm-9">
                                        <select class="form-control" id="input_factioncatalog_Delete" name="input_factioncatalog_Delete">
                                            <option value="0">0 - No</option>
                                            <option <?php if($user['factioncatalog_delete'] == 1){ echo "selected";}?> value="1">1 - Yes</option>
                                        </select>
                                    </div>
                                </div>
								<hr/>
								<div class="form-group">
                                    <label class="col-sm-3" for="input_reporting_Publish">Reporting Publish</label>
                                    <div class="col-sm-9">
                                        <select class="form-control" id="input_reporting_Publish" name="input_reporting_Publish">
                                            <option value="0">0 - None</option>
                                            <option <?php if($user['reporting_publish'] == 1){ echo "selected";}?> value="1">1 - Publish reports</option>
                                            <option <?php if($user['reporting_publish'] == 2){ echo "selected";}?> value="2">2 - Publish reports and information needs</option>
                                        </select>
                                    </div>
                                </div>
								<div class="form-group">
                                    <label class="col-sm-3" for="input_reporting_View">Reporting View</label>
                                    <div class="col-sm-9">
                                        <select class="form-control" id="input_reporting_View" name="input_reporting_View">
                                            <option value="0">0 - None</option>
                                            <option <?php if($user['reporting_view'] == 1){ echo "selected";}?> value="1">1 - May view own reports</option>
                                            <option <?php if($user['reporting_view'] == 2){ echo "selected";}?> value="2">2 - May view community of interest reports (INs)</option>
                                            <option <?php if($user['reporting_view'] == 3){ echo "selected";}?> value="3">3 - May view any reports</option>
                                        </select>
                                    </div>
                                </div>
								<div class="form-group">
                                    <label class="col-sm-3" for="input_reporting_Delete">Reporting Edit/Delete</label>
                                    <div class="col-sm-9">
                                        <select class="form-control" id="input_reporting_Delete" name="input_reporting_Delete">
                                            <option value="0">0 - None</option>
                                            <option <?php if($user['reporting_delete'] == 1){ echo "selected";}?> value="1">1 - Edit/Delete own products</option>
                                            <option <?php if($user['reporting_delete'] == 2){ echo "selected";}?> value="2">2 - Edit/Delete any products</option>
                                        </select>
                                    </div>
                                </div>
								<hr/>
								<div class="form-group">
                                    <label class="col-sm-3" for="input_transactions">Transactions</label>
                                    <div class="col-sm-9">
                                        <select class="form-control" id="input_transactions" name="input_transactions">
                                            <option value="0">0 - None</option>
                                            <option <?php if($user['transactions'] == 1){ echo "selected";}?> value="1">1 - View transactions</option>
                                        </select>
                                    </div>
                                </div>
								<hr/>
								<div class="form-group">
                                    <label class="col-sm-3" for="input_flashnews">Flashnews</label>
                                    <div class="col-sm-9">
                                        <select class="form-control" id="input_flashnews" name="input_flashnews">
                                            <option value="0">0 - None</option>
                                            <option <?php if($user_privs['flashnews'] == 1){ echo "selected";}?> value="1">1 - View flashnews</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <input id="submit" class="btn btn-primary " type="submit" name="set_privs" value="Submit">
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <?php include("../assets/php/_end.php"); ?>
    </body>
    </html>