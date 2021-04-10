<?php
include("../database.php");

$db = new database;
$db->connect();

mysqli_query($db->connection, "UPDATE bot_subscriptions SET subscription = subscription - 1 WHERE subscription > 0");
mysqli_query($db->connection, "UPDATE users_subscription SET days = days - 1 WHERE days > 0");

?>