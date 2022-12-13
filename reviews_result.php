<?php include_once("header.php")?>
<?php require("requirements/dbInformation.php")?>

<div class="container my-5">

<?php


$user = $_SESSION['user_id'];
$item_name = $_POST["sold_item"];
$item_rating = $_POST["review_stars"];
$item_comment = $_POST["reviews_details"];
$current_date = "CURRENT_TIMESTAMP";


$review = "INSERT INTO reviews (rating,user_id,comment,auction_id) VALUES ($item_rating,$user,'$item_comment',(SELECT bids.auction_id FROM bids
JOIN auctions ON auctions.auction_id = bids.auction_id
WHERE $current_date > expirationDate
AND bid_price = starting_price
AND bids.user_id = $user
AND item_name = '$item_name'))";

$recommendations_result = mysqli_query($connection, $review)
or die('Error making review query: '. mysqli_error($connection));


// If all is successful, let user know.
echo('<div class="text-center">Review succesfully created! <a href="http://localhost/auction/reviews.php">Place another review.</a></div>');

mysqli_close($connection);
?>

</div>


<?php include_once("footer.php")?>