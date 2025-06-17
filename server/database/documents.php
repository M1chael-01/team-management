<?php 

function document() {
    $db_host = "127.0.0.1";
    $db_user = "root";
    $db_password = "";
    $db_name = "team_management_documents";
    $connection = mysqli_connect($db_host,$db_user,$db_password,$db_name);

    return $connection;
}