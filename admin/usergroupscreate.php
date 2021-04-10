<?php 
error_reporting(0);
include("../assets/php/database.php");
include("../assets/php/session.php");
include("../assets/php/functions.php");
include("../assets/php/acct/check.php");
include("../assets/php/_head.php");

if(!isset($_SESSION['user_id']) || $_SESSION['user_privs']['admin'] == 0){ header("Location: ../index.php");}
?>

<?php  
    $db = new database;
    $db->connect();
    
    if($_POST) {
        $group = $_POST['inputGroup'];
        $name = mysqli_real_escape_string($db->connection, $_POST['inputName']);
        $leader = $_POST['inputMod'];
        $admin = $_POST['inputAdmin'];
        if ($admin == null) {
            $admin = 0;
        }
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
		$reporting_publish = $_POST['input_reporting_Publish']; // 1 = report, 2 = information need
		$reporting_view = $_POST['input_reporting_View']; // 1 = self, 2 = community of interest, 3 = all
		$reporting_delete = $_POST['input_reporting_Delete']; // 1 = own products, 2 = any products
		$transactions = $_POST['input_transactions'];
		$flashnews = $_POST['input_flashnews'];

        if(mysqli_num_rows(mysqli_query($db->connection, "SELECT * FROM usergroups WHERE usergroup_name = '$name'")) != 0) {
            $alert = "<div class=\"alert alert-warning\" style=\"font-size:14px;\">Enter a group name that is not already in use.</div>";
        } else if($group == 0) {
            if($name == '') {
                $alert = "<div class=\"alert alert-warning\" style=\"font-size:14px;\">Enter a group name.</div>";
            } else {
                mysqli_query($db->connection, "INSERT INTO usergroups (usergroup_name, usergroup_moderator) VALUES ('$name', '$leader')");
                $group_data = mysqli_fetch_assoc(mysqli_query($db->connection, "SELECT * FROM usergroups WHERE usergroup_name = '$name'"));
                $group_id = $group_data['usergroup_id'];
                mysqli_query($db->connection, "INSERT INTO usergroups_privs (group_id, admin, sentientprofiles_general, sentientprofiles_delete, galaxydata_search, galaxydata_analytics, galaxydata_delete, geolocation, signalsanalysis_search, signalsanalysis_upload, signalsanalysis_export, signalsanalysis_analytics, signalsanalysis_delete, factioncatalog_general, factioncatalog_delete, reporting_publish, reporting_view, reporting_delete, transactions, flashnews) VALUES ('$group_id', '$admin', '$sentientprofiles_view', '$sentientprofiles_delete', '$galaxydata_search', '$galaxydata_analytics', '$galaxydata_delete', '$geolocation', '$signalsanalysis_search', '$signalsanalysis_upload', '$signalsanalysis_export', '$signalsanalysis_analytics', '$signalsanalysis_delete', '$factioncatalog_view', '$factioncatalog_delete', '$reporting_publish', '$reporting_view', '$reporting_delete', '$transactions', '$flashnews')");
                if ($leader != 0) {
                    mysqli_query($db->connection, "INSERT INTO usergroups_members (usergroup_id, user_id) VALUES ('$group_id', '$leader')");
                    mysqli_query($db->connection, "UPDATE users SET refresh = '1' WHERE user_id = '$leader'");
                }
                $alert = "<div class=\"alert alert-success\" style=\"font-size:14px;\">Successfully created group ".$name.".</div>";
            }
        } else { 
            if(mysqli_num_rows(mysqli_query($db->connection, "SELECT * FROM usergroups WHERE usergroup_name = '$name'")) != 0) {
            $alert = "<div class=\"alert alert-warning\" style=\"font-size:14px;\">Enter a group name that is not already in use.</div>";
            } else {
                if($name != "") {
                    mysqli_query($db->connection, "UPDATE usergroups SET usergroup_name = '$name', usergroup_moderator = '$leader' WHERE usergroup_id = '$group'");
                } else {
                    mysqli_query($db->connection, "UPDATE usergroups SET usergroup_moderator = '$leader' WHERE usergroup_id = '$group'");
                }
                mysqli_query($db->connection, "DELETE FROM usergroups_privs WHERE group_id = '$group'");
                mysqli_query($db->connection, "INSERT INTO usergroups_privs (group_id, admin, sentientprofiles_general, sentientprofiles_delete, galaxydata_search, galaxydata_analytics, galaxydata_delete, geolocation, signalsanalysis_search, signalsanalysis_upload, signalsanalysis_export, signalsanalysis_analytics, signalsanalysis_delete, factioncatalog_general, factioncatalog_delete, reporting_publish, reporting_view, reporting_delete, transactions, flashnews) VALUES ('$group', '$admin', '$sentientprofiles_view', '$sentientprofiles_delete', '$galaxydata_search', '$galaxydata_analytics', '$galaxydata_delete', '$geolocation', '$signalsanalysis_search', '$signalsanalysis_upload', '$signalsanalysis_export', '$signalsanalysis_analytics', '$signalsanalysis_delete', '$factioncatalog_view', '$factioncatalog_delete', '$reporting_publish', '$reporting_view', '$reporting_delete', '$transactions', '$flashnews')");
                if ($leader != 0) {
                    mysqli_query($db->connection, "INSERT INTO usergroups_members (usergroup_id, user_id) VALUES ('$group', '$leader')");
                    mysqli_query($db->connection, "UPDATE users SET refresh = '1' WHERE user_id = '$leader'");
                }
                $alert = "<div class=\"alert alert-success\" style=\"font-size:14px;\">Successfully updated group ".$name.".</div>";
            }    
        }
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
                                    <h1>Create Usergroup</h1>
                                </div>
                            </div>
                            <div class="col-sm-6 hidden-xs">
                                <div class="header-section">
                                    <ul class="breadcrumb breadcrumb-top">
                                        <li>Admin</li>
                                        <li><a href="panel.php">Panel</a></li>
                                        <li><a href="usergroups.php">Usergroups</a></li>
                                        <li><a href="">Create Usergroup</a></li>
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
                            <h2>Create Usergroup</h2>
                        </div>
                        <form class="form-horizontal" role="form" method="post" action="usergroupscreate.php">
                            <div class="modal-body">
                                <div class="form-group">
                                    <label class="col-sm-3" for="inputGroup">Select Usergroup</label>
                                    <div class="col-sm-9">
                                        <select class="form-control" id="inputGroup" name="inputGroup">
                                            <option value="0">-- Create New Usergroup -- </option>
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
                                </div>    
                                <div class="form-group">
                                    <label class="col-sm-3" for="inputName">Usergroup Name</label>
                                    <div class="col-sm-9"><input class="form-control" id="inputName" name="inputName" type="text" placeholder="{Usergroup Name}"></div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-3" for="inputMod">Usergroup Leader</label>
                                    <div class="col-sm-9">
                                        <select class="form-control" id="inputMod" name="inputMod">
                                            <option value="0">-- Select User --</option>
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
                                </div>
                                <?php
                                    if($_SESSION['user_privs']['admin'] == 2) {
                                            echo "<div class=\"form-group\"><label class=\"col-sm-3\" for=\"inputAdmin\">Admin Privileges</label><div class=\"col-sm-9\"><select class=\"form-control\" id=\"inputAdmin\" name=\"inputAdmin\"><option value=\"0\">0</option><option value=\"1\">1</option><option value=\"2\">2</option></select></div></div>";
                                    }
                                ?>
								<hr/>								
                                <div class="form-group">
                                    <label class="col-sm-3" for="input_sentientprofiles_View">Sentient Profiles View</label>
                                    <div class="col-sm-9">
                                        <select class="form-control" id="input_sentientprofiles_View" name="input_sentientprofiles_View">
                                            <option value="0">0 - None</option>
                                            <option value="1">1 - May view profiles and personal entries</option>
                                            <option value="2">2 - May view profiles and all entries</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-3" for="input_sentientprofiles_Delete">Sentient Profiles Delete</label>
                                    <div class="col-sm-9">
                                        <select class="form-control" id="input_sentientprofiles_Delete" name="input_sentientprofiles_Delete">
                                            <option value="0">0 - No</option>
                                            <option value="1">1 - May delete profiles and entries</option>
                                        </select>
                                    </div>
                                </div>
								<hr/>
                                <div class="form-group">
                                    <label class="col-sm-3" for="input_galaxydata_Search">Galaxy Data Search</label>
                                    <div class="col-sm-9">
                                        <select class="form-control" id="input_galaxydata_Search" name="input_galaxydata_Search">
                                            <option value="0">0 - No</option>
                                            <option value="1">1 - May search galactic records</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-3" for="input_galaxydata_Analytics">Galaxy Data Analytics</label>
                                    <div class="col-sm-9">
                                        <select class="form-control" id="input_galaxydata_Analytics" name="input_galaxydata_Analytics">
                                            <option value="0">0 - None</option>
                                            <option value="1">1 - Can execute analytics</option>
                                            <option value='2'>2 - Can execute analytics and save results</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-3" for="input_galaxydata_Delete">Galaxy Data Delete</label>
                                    <div class="col-sm-9">
                                        <select class="form-control" id="input_galaxydata_Delete" name="input_galaxydata_Delete">
                                            <option value="0">0 - No</option>
                                            <option value="1">1 - Yes</option>
                                        </select>
                                    </div>
                                </div>
								<hr/>
								<div class="form-group">
                                    <label class="col-sm-3" for="input_geolocation">Geolocation Data</label>
                                    <div class="col-sm-9">
                                        <select class="form-control" id="input_geolocation" name="input_geolocation">
                                            <option value="0">0 - No</option>
                                            <option value="1">1 - Can view/search</option>
                                            <option value="2">2 - Can view/search/add</option>
                                        </select>
                                    </div>
                                </div>
								<hr/>
                                <div class="form-group">
                                    <label class="col-sm-3" for="input_signalsanalysis_Search">Signals Analysis Search</label>
                                    <div class="col-sm-9">
                                        <select class="form-control" id="input_signalsanalysis_Search" name="input_signalsanalysis_Search">
                                            <option value="0">0 - None</option>
                                            <option value="1">1 - Can view standard data</option>
                                            <option value='2'>2 - Can view sensitive data (references to inventory scans)</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-3" for="input_signalsanalysis_Upload">Signals Analysis Upload</label>
                                    <div class="col-sm-9">
                                        <select class="form-control" id="input_signalsanalysis_Upload" name="input_signalsanalysis_Upload">
                                            <option value="0">0 - No</option>
                                            <option value="1">1 - Upload to buffer</option>
                                            <option value='2'>2 - Upload to database</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-3" for="input_signalsanalysis_Export">Signals Analysis Export</label>
                                    <div class="col-sm-9">
                                        <select class="form-control" id="input_signalsanalysis_Export" name="input_signalsanalysis_Export">
                                            <option value="0">0 - No</option>
                                            <option value="1">1 - May export results</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-3" for="input_signalsanalysis_Analytics">Signals Analysis Analytics</label>
                                    <div class="col-sm-9">
                                        <select class="form-control" id="input_signalsanalysis_Analytics" name="input_signalsanalysis_Analytics">
                                            <option value="0">0 - No</option>
                                            <option value="1">1 - May execute analytics</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-3" for="input_signalsanalysis_Delete">Signals Analysis Delete</label>
                                    <div class="col-sm-9">
                                        <select class="form-control" id="input_signalsanalysis_Delete" name="input_signalsanalysis_Delete">
                                            <option value="0">0 - No</option>
                                            <option value="1">1 - May delete records</option>
                                        </select>
                                    </div>
                                </div>
								<hr/>
                                <div class="form-group">
                                    <label class="col-sm-3" for="input_factioncatalog_View">Faction Catalog View</label>
                                    <div class="col-sm-9">
                                        <select class="form-control" id="input_factioncatalog_View" name="input_factioncatalog_View">
                                            <option value="0">0 - No</option>
                                            <option value="1">1 - Yes</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-3" for="input_factioncatalog_Delete">Faction Catalog Delete</label>
                                    <div class="col-sm-9">
                                        <select class="form-control" id="input_factioncatalog_Delete" name="input_factioncatalog_Delete">
                                            <option value="0">0 - No</option>
                                            <option value="1">1 - Yes</option>
                                        </select>
                                    </div>
                                </div>
								<hr/>
								<div class="form-group">
                                    <label class="col-sm-3" for="input_reporting_Publish">Reporting Publish</label>
                                    <div class="col-sm-9">
                                        <select class="form-control" id="input_reporting_Publish" name="input_reporting_Publish">
                                            <option value="0">0 - None</option>
                                            <option value="1">1 - Publish reports</option>
                                            <option value="2">2 - Publish reports and information needs</option>
                                        </select>
                                    </div>
                                </div>
								<div class="form-group">
                                    <label class="col-sm-3" for="input_reporting_View">Reporting View</label>
                                    <div class="col-sm-9">
                                        <select class="form-control" id="input_reporting_View" name="input_reporting_View">
                                            <option value="0">0 - None</option>
                                            <option value="1">1 - May view own reports</option>
                                            <option value="2">2 - May view community of interest reports (INs)</option>
                                            <option value="3">3 - May view any reports</option>
                                        </select>
                                    </div>
                                </div>
								<div class="form-group">
                                    <label class="col-sm-3" for="input_reporting_Delete">Reporting Edit/Delete</label>
                                    <div class="col-sm-9">
                                        <select class="form-control" id="input_reporting_Delete" name="input_reporting_Delete">
                                            <option value="0">0 - None</option>
                                            <option value="1">1 - Edit/Delete own products</option>
                                            <option value="2">2 - Edit/Delete any products</option>
                                        </select>
                                    </div>
                                </div>
								<hr/>
								<div class="form-group">
                                    <label class="col-sm-3" for="input_transactions">Transactions</label>
                                    <div class="col-sm-9">
                                        <select class="form-control" id="input_transactions" name="input_transactions">
                                            <option value="0">0 - None</option>
                                            <option value="1">1 - View transactions</option>
                                        </select>
                                    </div>
                                </div>
								<hr/>
								<div class="form-group">
                                    <label class="col-sm-3" for="input_flashnews">Flashnews</label>
                                    <div class="col-sm-9">
                                        <select class="form-control" id="input_flashnews" name="input_flashnews">
                                            <option value="0">0 - None</option>
                                            <option value="1">1 - View flashnews</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <input id="submit" class="btn btn-primary " type="submit" value="Submit">
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <?php include("../assets/php/_end.php"); ?>
    </body>
    </html>