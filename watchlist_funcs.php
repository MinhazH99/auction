<?php require("requirements/dbInformation.php")?> 

<?php

session_start();

if (!isset($_POST['functionname']) || !isset($_POST['arguments'])) {
  header("Location: browse.php");
  return;
}


$res = "fail";

// Extract arguments from the POST variables:
$auction_id = (int) $_POST['arguments'][0];
$user_id = $_SESSION['user_id'];

if ($_POST['functionname'] == "add_to_watchlist") {
  // TODO: Update database and return success/failure.
  $query = "INSERT INTO `watch` (`watch_id`, `user_id`, `auction_id`) VALUES (NULL, {$user_id}, {$auction_id})";
  $resultObj = $connection->query($query);
  $res = "success";
}

else if ($_POST['functionname'] == "remove_from_watchlist") {
  // TODO: Update database and return success/failure.
  
  $query = "DELETE FROM `watch` WHERE `user_id` = {$user_id} AND `auction_id` = {$auction_id}";
  $resultObj = $connection->query($query);
  $res = "success";
}

echo $res;

?>