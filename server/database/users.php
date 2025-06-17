<?php

function users() {
    $db_host = "127.0.0.1";
    $db_user = "root";
    $db_password = "";
    $db_name = "team_management_users";

    // Create connection
    $connection = mysqli_connect($db_host, $db_user, $db_password, $db_name);

    // Check connection
    if (!$connection) {
        die("❌ Database connection failed: " . mysqli_connect_error());
    }

    return $connection;
}
