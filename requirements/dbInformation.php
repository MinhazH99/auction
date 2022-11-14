<?php

$dbPassword = "";
$dbUserName = "root";
$dbServer = "localhost";
$dbName = 'auction';

$connection = new mysqli($dbServer, $dbUserName, $dbPassword,$dbName);

if($connection->connect_errno){
    exit("Database Connection Failed. Reason: ".$connection->connect_error);
}

// $resultObj->close();
// // to close a database connection
// $connection->close();

?>