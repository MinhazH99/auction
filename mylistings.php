<?php include_once("header.php")?>
<?php require("utilities.php")?>

<div class="container">

<h2 class="my-3">My listings</h2>

<?php
  // This page is for showing a user the auction listings they've made.
  // It will be pretty similar to browse.php, except there is no search bar.
  // This can be started after browse.php is working with a database.
  // Feel free to extract out useful functions from browse.php and put them in
  // the shared "utilities.php" where they can be shared by multiple files.
  
  
  // TODO: Check user's credentials (cookie/session).
  
  function isLoggedIn() { return isset($_SESSION['user_id']);}

  if (!isLoggedIn())
    echo 'Please login';

  // TODO: Perform a query to pull up their auctions.
  $host = "localhost";
  $dbname = "auction";
  $username = "root";
  $password = "";
  
  $connection = mysqli_connect($host, $username, $password, $dbname);

  $user = $_SESSION['user_id'];

  $user_listings = "SELECT auction_id,item_name, item_desc, item_condition, category_id, expirationDate, reserve_price,starting_price FROM auctions WHERE user_id = '$user'";

  $user_listings_res = mysqli_query($connection,$user_listings);


  
  // TODO: Loop through results and print them out as list items.

  while ($user_listing_row = mysqli_fetch_array($user_listings_res))

  {
    $item_id = $user_listing_row['auction_id'];
    $title = $user_listing_row['item_name'];
    $description = $user_listing_row['item_desc'];
    $current_price= $user_listing_row['starting_price'];
    $end_date = new DateTime($user_listing_row['expirationDate']);
    $num_bids = 1;

    print_listing_li($item_id, $title, $description, $current_price, $num_bids, $end_date);
  }
  mysqli_close($connection);
  
?>

<?php include_once("footer.php")?>