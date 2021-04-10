<?php 
    error_reporting(0);
	include("../assets/php/database.php");
    include("../assets/php/session.php");
    include("../assets/php/functions.php");
    include("../assets/php/acct/check.php");
    if(!isset($_SESSION['user_id']) || $_SESSION['user_privs']['admin'] == 0 || $_SESSION['user_privs']['admin'] == 1){ header("Location: ../index.php");}
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
                                        <h1>Administrators</h1>
                                    </div>
                                </div>
                                <div class="col-sm-6 hidden-xs">
                                    <div class="header-section">
                                        <ul class="breadcrumb breadcrumb-top">
                                            <li>Admin</li>
                                            <li><a href="panel.php">Panel</a></li>
                                            <li><a href="">Administrators</a></li>
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
                                <h2>Administrators</h2>
                                <form method="get"  action="administratorsedit.php" style="display:inline">
                                    <input type="submit" class="btn btn-primary" value="Edit Administrators">
                                </form>
                            </div>
                                <?php
                                    $db = new database;
                                    $db->connect();

                                    $admin_ids = mysqli_query($db->connection,"SELECT users.user_id, users_privs.admin, users.username FROM users_privs LEFT JOIN users ON users_privs.user_id = users.user_id WHERE admin = '1' OR admin = '2' ORDER BY username");
                                    echo "<table class=\"table table-bordered table-striped table-responsive table-hover\"><tr><th>User Id</th><th>Username</th><th>Admin Level</th></tr>";
                                    while($data = mysqli_fetch_assoc($admin_ids)){
                                        $id = $data['user_id'];
                                        $username = $data['username'];
                                        $level = $data['admin'];
                                        echo "<tr><td>".$id."</td><td>".ucwords($username)."</td><td>".$level."</td></tr>";
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