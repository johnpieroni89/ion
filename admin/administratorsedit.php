<?php 
error_reporting(0);
include("../assets/php/database.php");
include("../assets/php/session.php");
include("../assets/php/functions.php");
include("../assets/php/acct/check.php");
include("../assets/php/_head.php");

if(!isset($_SESSION['user_id']) || $_SESSION['user_privs']['admin'] == 0 || $_SESSION['user_privs']['admin'] == 1){ header("Location: ../index.php");}
?>

<?php  
    $db = new database;
    $db->connect();
    
    if($_POST) {
        $user_id = $_POST['inputUser'];
        $admin = $_POST['inputAdmin'];
        
        if ($user_id == 0) {
            $alert = "<div class=\"alert alert-warning\" style=\"font-size:14px;\">Please select a user.</div>";
        } else {
            $user = mysqli_query($db->connection, "SELECT * FROM users WHERE user_id = '$user_id' LIMIT 1");
            $user_data = mysqli_fetch_assoc($user);
            $username = $user_data['username'];

            mysqli_query($db->connection, "UPDATE users_privs SET admin = '$admin' WHERE user_id = '$user_id'");
            mysqli_query($db->connection, "UPDATE users SET refresh = '1' WHERE user_id = '$user_id'");
            $alert = "<div class=\"alert alert-success\" style=\"font-size:14px;\">Successfully updated administator status for ".$username.".</div>";
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
                                    <h1>Edit Administrator</h1>
                                </div>
                            </div>
                            <div class="col-sm-6 hidden-xs">
                                <div class="header-section">
                                    <ul class="breadcrumb breadcrumb-top">
                                        <li>Admin</li>
                                        <li><a href="panel.php">Panel</a></li>
                                        <li><a href="administrators.php">Administrators</a></li>
                                        <li><a href="">Edit Administrator</a></li>
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
                            <h2>Edit Administrator</h2>
                        </div>
                        <form class="form-horizontal" role="form" method="post" action="administratorsedit.php">
                            <div class="modal-body">
                                <div class="form-group">
                                    <label class="col-sm-3" for="inputUser">User</label>
                                    <div class="col-sm-9">
                                        <select class="form-control" id="inputUser" name="inputUser">
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
                                <div class="form-group">
                                    <label class="col-sm-3" for="inputAdmin">Admin Level</label>
                                    <div class="col-sm-9">
                                        <input type="radio" style="white-space:nowrap" class="form-control" id="inputAdmin" name="inputAdmin" value="0" checked>
                                        <label for="0" style="white-space:nowrap">0</label>
                                        <input type="radio" style="white-space:nowrap" class="form-control" id="inputAdmin" name="inputAdmin" value="1">
                                        <label for="1" style="white-space:nowrap">1</label>
                                        <input type="radio" style="white-space:nowrap" class="form-control" id="inputAdmin" name="inputAdmin" value="2">
                                        <label for="2" style="white-space:nowrap">2</label>
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