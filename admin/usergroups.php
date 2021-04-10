<?php 
    error_reporting(0);
	include("../assets/php/database.php");
    include("../assets/php/session.php");
    include("../assets/php/functions.php");
    include("../assets/php/acct/check.php");

    if(!isset($_SESSION['user_id']) || $_SESSION['user_privs']['admin'] == 0){ header("Location: ../index.php");}
	
	if(isset($_GET['d'])){
		$db = new database;
        $db->connect();
		
		$delete_id = mysqli_real_escape_string($db->connection,$_GET['d']);
		mysqli_query($db->connection, "DELETE FROM usergroups WHERE usergroup_id = '$delete_id'");
		mysqli_query($db->connection, "DELETE FROM usergroups_members WHERE usergroup_id = '$delete_id'");
		mysqli_query($db->connection, "DELETE FROM usergroups_privs WHERE group_id = '$delete_id'");
		$_SESSION['alert'] = "<div class=\"alert alert-success\" style=\"font-size:14px;\">Usergroup has been deleted.</div>";
		header("Location: usergroups.php");
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
                                        <h1>Usergroups</h1>
                                    </div>
                                </div>
                                <div class="col-sm-6 hidden-xs">
                                    <div class="header-section">
                                        <ul class="breadcrumb breadcrumb-top">
                                            <li>Admin</li>
                                            <li><a href="panel.php">Panel</a></li>
                                            <li><a href="">Usergroups</a></li>
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
                                <h2>Usergroups</h2>
                                <form method="post"  action="usergroupscreate.php" style="display:inline">
                                    <input type="submit" class="btn btn-primary" value="Create Usergroup">
                                </form>
                            </div>
                                <?php
                                    $db = new database;
                                    $db->connect();

                                    $groups = mysqli_query($db->connection,"SELECT usergroups.*, users.username FROM usergroups LEFT JOIN users ON usergroups.usergroup_moderator = users.user_id ORDER BY usergroup_name");
                                    echo "<table class=\"table table-bordered table-striped table-responsive table-hover\"><tr><th>Usergroup Id</th><th>Usergroup Name</th><th>Moderator</th><th>Usergroup Members</th><th>Edit Usergroup</th></tr>";
                                    while($data = mysqli_fetch_assoc($groups)){
                                        echo "<tr><td>".$data['usergroup_id']."</td><td>".$data['usergroup_name']."</td><td>".ucwords($data['username'])."</td><td>".mysqli_num_rows(mysqli_query($db->connection,"SELECT * FROM usergroups_members WHERE usergroup_id = '".$data['usergroup_id']."'"))."</td><td><form method=\"get\" style=\"display:inline\" action=\"usergroupsedit.php\"><input type=\"submit\" class=\"btn btn-primary\" value=\"Edit Usergroup\"><input type=\"hidden\" name=\"usergroup_id\" value=\"".$data['usergroup_id']."\"></form><form method=\"post\" style=\"margin-left:10px;display:inline\" action=\"usergroups.php?d=".$data['usergroup_id']."\"><input type=\"submit\" class=\"btn btn-warning\" onclick=\"return confirm('Do you really want to delete the selected account?');\" value=\"Delete Usergroup\"><input type=\"hidden\" name=\"usergroup_id\" value=\"".$data['usergroup_id']."\"></form></td></tr>";
                                    }
                                    echo "</table>";
                                ?>        
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php include("../assets/php/_end.php"); ?>
    </body>
</html>