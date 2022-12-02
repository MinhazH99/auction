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
    echo 'Please login'; /* add redirect */

  // TODO: Perform a query to pull up their auctions.
  $host = "localhost";
  $dbname = "auction";
  $username = "root";
  $password = "";
  
  $connection = mysqli_connect($host, $username, $password, $dbname);

  $user = $_SESSION['user_id']; 

  $user_listings = "SELECT auction_id,item_name,item_desc,expirationDate,starting_price FROM auctions WHERE user_id = '$user'";
  
  $count_bids_query = "SELECT auctions.auction_id, bids.user_id, COUNT(bids.bid_id) AS 'truenumbids'
  FROM auctions
  LEFT JOIN categories ON categories.category_id = auctions.category_id
  LEFT JOIN bids ON bids.auction_id = auctions.auction_id
  WHERE auctions.user_id = $user
  GROUP BY auctions.auction_id";

  $user_listings_res = mysqli_query($connection,$user_listings);
  $bids_result = mysqli_query($connection, $count_bids_query)
    or die('Error making select users query: '. mysqli_error($connection));

  $auction_id = array();
  $true_bids = array();
    
  while ($row = mysqli_fetch_array($bids_result))
    {
      $auction_id[] = $row['auction_id'];
      $true_bids[] = $row['truenumbids'];
      
    }
  
  $count = count($auction_id);
  
  // TODO: Loop through results and print them out as list items.

  while ($user_listing_row = mysqli_fetch_array($user_listings_res))

  {
    var_dump($user_listing_row);
    $item_id = $user_listing_row['auction_id'];
    $title = $user_listing_row['item_name'];
    $description = $user_listing_row['item_desc'];
    $current_price= $user_listing_row['starting_price'];
    $end_date = new DateTime($user_listing_row['expirationDate']);
    for ($num = 0; $num < $count; $num++){
      if ($auction_id[$num] == $item_id){
        $num_bids = $true_bids[$num];
        print_listing_li($item_id, $title, $description, $current_price, $num_bids, $end_date);
      }
      else {
        continue;
      }
    }
    
    

    
  }
  mysqli_close($connection);
  
?>

<?php include_once("footer.php")?>