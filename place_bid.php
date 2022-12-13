<?php require("requirements/dbInformation.php") ?>
<?php require("email.php")?> 

<?php
session_start();

// TODO: Extract $_POST variables, check they're OK, and attempt to make a bid.
// Notify user of success/failure and redirect/give navigation options.

$auctionID = $_POST['auction_id'];
$bid_price =(int) $_POST['bid_price'];

// $url = 'listing.php?auction_id='.$auction_id;
if(!isset($_SESSION['logged_in'])){
    echo('<div class="text-center">Please log in before you place a bid.<br>You\'re being redirected to auction page.</div>');
    header("refresh:3;'listing.php?auction_id=".$auctionID);
    die(); 
}

$buyer_id = $_SESSION['user_id'];

$query = "SELECT auctions.item_name, auctions.user_id, MAX(auctions.starting_price) AS 'current_price' FROM auctions WHERE auction_id = {$auctionID}";
$resultObj = $connection->query($query);
$row = $resultObj->fetch_assoc();

$current_price = (int) $row['current_price'];
$seller_id = $row['user_id'];
$itemName = $row['item_name'];

// sellers cannot be bidders
if ($seller_id === $buyer_id){
    echo('<div class="text-center">You cannot bid on own auctions.<br>You\'re being redirected to auction page.</div>');
    header("refresh:3;'listing.php?auction_id=".$auctionID);
    die();
}

// checking if bid price is greater than current_price
if ($current_price < $bid_price){
    $submit_bid = "INSERT INTO `bids`(`bid_price`, `auction_id`, `user_id`) VALUES ('{$bid_price}','{$auctionID}','{$buyer_id}')";
    $updateQuery = "UPDATE `auctions` SET `starting_price`={$bid_price} WHERE auction_id = {$auctionID}";
    $resultObj = $connection->query($submit_bid);
    $resultObj = $connection->query($updateQuery);

    // query the email of all the users watching

    $watchlist_users = "SELECT users.email, users.first_name, users.user_id FROM users INNER JOIN watch ON watch.user_id=users.user_id WHERE watch.auction_id = {$auctionID}";
    $usersObj = $connection->query($watchlist_users)->fetch_all();
    
    foreach ($usersObj as $row) {
            $email = $row[0];
            $firstName = $row[1];
            $userID = (int) $row[2];

            if ($userID != $buyer_id){
                all_watch_lists($firstName, $email, $auctionID, $itemName);
            }
        }
        
    // add to the watchlist (if he isn't watching it already)
    $watching = FALSE;
    $check_watching = "SELECT * FROM `watch` WHERE `user_id` = {$buyer_id} AND `auction_id` = {$auctionID}";
    $watchObj = $connection->query($check_watching)->fetch_assoc();
        
    if (!$watchObj){
        $query = "INSERT INTO `watch` (`watch_id`, `user_id`, `auction_id`) VALUES (NULL, {$buyer_id}, {$auctionID})";
        $resultObj = $connection->query($query);
    }

    echo "Create bid successfully! <a href='mybids.php'>View my bids</a>.";

} else {
    echo('<div class="text-center">Invalid bid. Please input a value greater than the current price.<br>You\'re being redirected to auction page.</div>');
    header("refresh:3;'listing.php?auction_id=".$auctionID);
}

?>