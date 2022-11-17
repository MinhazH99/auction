<?php require("requirements/dbInformation.php")?> 

<?php
session_start();

// TODO: Extract $_POST variables, check they're OK, and attempt to make a bid.
// Notify user of success/failure and redirect/give navigation options.

$item_id = $_POST['auction_id'];
$bid_price =(int) $_POST['bid_price'];

// $url = 'listing.php?auction_id='.$auction_id;
if(!isset($_SESSION['logged_in'])){
    echo('<div class="text-center">Please log in before you place a bid.<br>You\'re being redirected to auction page.</div>');
    header("refresh:3;'listing.php?auction_id=".$item_id);
    die(); 
}

$buyer_id = $_SESSION['user_id'];

$query = "SELECT auctions.user_id, MAX(auctions.starting_price) AS 'current_price' FROM auctions WHERE auction_id = {$item_id}";
$resultObj = $connection->query($query);
$row = $resultObj->fetch_assoc();

$current_price = (int) $row['current_price'];
$seller_id = $row['user_id'];

// sellers cannot be bidders
if ($seller_id === $buyer_id){
    echo('<div class="text-center">You cannot bid on own auctions.<br>You\'re being redirected to auction page.</div>');
    header("refresh:3;'listing.php?auction_id=".$item_id);
    die();
}

// checking if bid price is greater than current_price
if ($current_price < $bid_price){
    $submit_bid = "INSERT INTO `bids`(`bid_price`, `auction_id`, `user_id`, `user_status`) VALUES ('{$bid_price}','{$item_id}','{$buyer_id}','Winner')";
    $updateQuery = "UPDATE `auctions` SET `starting_price`={$bid_price} WHERE auction_id = {$item_id}";
    $resultObj = $connection->query($submit_bid);
    $resultObj = $connection->query($updateQuery);
    echo "Create bid successfully! <a href='mybids.php'>View my bids</a>.";
} else {
    echo('<div class="text-center">Invalid bid. Please input a value greater than the current price.<br>You\'re being redirected to auction page.</div>');
    header("refresh:3;'listing.php?auction_id=".$item_id);
}

?>