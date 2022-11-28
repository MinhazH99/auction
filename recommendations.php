<?php include_once("header.php")?>
<?php require("utilities.php")?>

<div class="container">

<h2 class="my-3">Recommendations for you</h2>

<?php
  // This page is for showing a buyer recommended items based on their bid 
  // history. It will be pretty similar to browse.php, except there is no 
  // search bar. This can be started after browse.php is working with a database.
  // Feel free to extract out useful functions from browse.php and put them in
  // the shared "utilities.php" where they can be shared by multiple files.
  

  
  // TODO: Check user's credentials (cookie/session).
  function isLoggedIn() { return isset($_SESSION['user_id']);}

  if (!isLoggedIn())
    echo 'Please login'; /* add redirect */
    

  
  // TODO: Perform a query to pull up auctions they might be interested in.

  $host = "localhost";
  $dbname = "auction";
  $username = "root";
  $password = "";
  
  $connection = mysqli_connect($host, $username, $password, $dbname);

  $user = $_SESSION['user_id'];

  $recommendations = "SELECT item_name from auctions WHERE item_name IN (SELECT item_name FROM auctions WHERE auction_id IN (SELECT auction_id FROM reviews WHERE rating >= 4 AND user_id IN (SELECT user_id FROM bids WHERE auction_id IN (SELECT auction_id FROM bids WHERE user_id = $user))))";

  $recommendations_result = mysqli_query($connection, $recommendations)
   or die('Error making recommendations query: '. mysqli_error($connection));

  
  $item_name = array();

  while ($row = mysqli_fetch_array($recommendations_result))
  {
    $item_name[]= $row['item_name'];
  }

  $count_bids_query = "SELECT auctions.auction_id, bids.user_id, COUNT(bids.bid_id) AS 'truenumbids'
  FROM auctions
  LEFT JOIN categories ON categories.category_id = auctions.category_id
  LEFT JOIN bids ON bids.auction_id = auctions.auction_id
  WHERE auctions.user_id != $user
  GROUP BY auctions.auction_id";

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
  
  $len_items = count($item_name);

  
  
  
  for ($i = 0; $i < $len_items; $i++)
  {
  $recommended_item = "SELECT auction_id,item_name,item_desc,expirationDate,starting_price FROM auctions WHERE item_name LIKE '%{$item_name[$i]}%' AND auction_status = 'Open'";
  $recommend_item_result = mysqli_query($connection, $recommended_item);
  
  while ($recommended_listing = mysqli_fetch_array($recommend_item_result))
  {
    $item_id = $recommended_listing['auction_id'];
    $title = $recommended_listing['item_name'];
    $description = $recommended_listing['item_desc'];
    $current_price = $recommended_listing['starting_price'];
    $end_date = new DateTime($recommended_listing['expirationDate']);
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
    
  
  }
  
  

?>