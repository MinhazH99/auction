<?php require("requirements/dbInformation.php")?> 

<?php

session_start();

if (!isset($_POST['functionname']) || !isset($_POST['arguments'])) {
  header("Location: browse.php");
  return;
}

// var_dump($_POST, $_SESSION);

$res = "fail";
// var_dump($_POST);

// Extract arguments from the POST variables:
$auction_id = (int) $_POST['arguments'][0];
// echo (int) $_POST['arguments'];
// echo $item_id;
// echo $_POST['arguments'][0];
$user_id = $_SESSION['user_id'];

if ($_POST['functionname'] == "add_to_watchlist") {
  // TODO: Update database and return success/failure.
  $query = "INSERT INTO `watch` (`watch_id`, `user_id`, `auction_id`) VALUES (NULL, {$user_id}, {$auction_id})";
  $resultObj = $connection->query($query);
  $res = "success";
}

else if ($_POST['functionname'] == "remove_from_watchlist") {
  // TODO: Update database and return success/failure.
  
  // How do we determine how to delete --> I'm assuming we don't have a need for watch_id?
  $query = "DELETE FROM `watch` WHERE `user_id` = {$user_id} AND `auction_id` = {$auction_id}";
  $resultObj = $connection->query($query);
  $res = "success";
}

// Note: Echoing from this PHP function will return the value as a string.
// If multiple echo's in this file exist, they will concatenate together,
// so be careful. You can also return JSON objects (in string form) using
// echo json_encode($res).

echo $res;

?>