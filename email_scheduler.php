<?php include_once("header.php")?>
<?php require("utilities.php")?>
<?php require("requirements/dbInformation.php")?>
<?php require("email.php")?>


<?php

$query = "SELECT auctions.auction_id, auctions.item_name, auctions.starting_price, auctions.reserve_price, users.email, users.first_name, users.last_name FROM auctions JOIN users ON auctions.user_id = users.user_id WHERE auctions.auction_email = 0 AND auctions.auction_status = 'Closed'";
$auctions_obj = $connection->query($query)->fetch_all();

foreach ($auctions_obj as $auction){
    $auctionID = $auction[0];
    $itemName = $auction[1];
    $salePrice = $auction[2];
    $reservePrice = $auction[3];
    $sellerEmail = $auction[4];
    $sellerFirstName = $auction[5];
    $sellerLastName = $auction[6];

    $soldQuery = "SELECT `user_id` FROM `bids` WHERE auction_id = {$auctionID} AND `bid_price` = {$salePrice}";
    $no_bids = $connection->query($soldQuery);
    $winning_bid = $no_bids->fetch_assoc();
  
    $sold = False;
    
    if ($winning_bid && (int) $reservePrice <= (int) $salePrice){
      $sold = True;
      $winnerQuery = "SELECT `email`,`first_name`,`last_name`  FROM `users` WHERE `user_id` = {$winning_bid['user_id']}";
      $winnerDetails = $connection->query($winnerQuery)->fetch_assoc();
      $buyerEmail = $winnerDetails['email'];
      $buyerFirstName =  $winnerDetails['first_name'];
      $buyerLastName = $winnerDetails['last_name'];
      
    }

    if (!$sold){
        no_sale($itemName, $sellerEmail, $sellerFirstName, $sellerLastName, $auctionID, $salePrice);
    } else {
        buyer_email($itemName, $buyerEmail, $buyerFirstName, $buyerLastName);
        seller_email($itemName, $sellerEmail, $sellerFirstName, $sellerLastName);
    }

    // update the auction_email to 1 so we don't send duplicate emails
    $updateQuery = "UPDATE `auctions` SET `auction_email` = '1' WHERE `auction_status` = 'Closed' AND `auction_email`= 0";
    $action = $connection->query($updateQuery);
    
}
?>
