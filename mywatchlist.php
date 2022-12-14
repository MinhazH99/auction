<?php include_once("header.php")?>
<?php require("utilities.php")?>

<div class="container">

<h2 class="my-3">My Watchlist</h2>

<?php
  // This page is for showing a user all the items that they have won on auction.
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
  
  
  $count_bids_query = "SELECT auctions.auction_id, item_name,item_desc, item_condition, expirationDate,starting_price, COUNT(bids.bid_id) AS 'truenumbids'
  FROM auctions
  LEFT JOIN categories ON categories.category_id = auctions.category_id
  LEFT JOIN bids ON bids.auction_id = auctions.auction_id
  LEFT JOIN watch ON watch.auction_id = auctions.auction_id
  WHERE watch.user_id = $user
  GROUP BY watch.auction_id";
  
  
  if (!isset($_GET['page'])) {
    $curr_page = 1; #php variable
  }
  else {
    $curr_page = $_GET['page'];
  }


?>

<div class="container mt-5"> <!-- mt-5 margin at top of 5 -->

<!-- TODO: If result set is empty, print an informative message. Otherwise...-->
<?php 
   
  
  $bids_result = mysqli_query($connection, $count_bids_query)
  or die('Error making select users query: '. mysqli_error($connection));

  $num_queries = mysqli_num_rows($bids_result);
  $results_per_page = 10;

if (!isset($num_queries)) {
  $num_queries = 0;
  $max_page = 1;
  
}

if ($num_queries==0){
  $num_queries = 0;
  $max_page = 1;
  echo('
    <li class="list-group-item d-flex justify-content-center">
    <div class="p-2 mr-5"><h5><center>Please add any items to your watchlist</center></h5>
    </div>
    
  </li>'
  );
}
else {
  
  $max_page = ceil($num_queries / $results_per_page);

}

?>


<ul class="list-group">


<?php

  $keyword_rows = $bids_result -> fetch_all(MYSQLI_NUM);
  if ($num_queries > 0){
  if ($curr_page < $max_page) {
    for ($count = ($curr_page-1)*$results_per_page; $count<$curr_page*$results_per_page;$count++){
      $keyword_row = $keyword_rows[$count];
      
      
      $item_id= $keyword_row[0];
      $title = $keyword_row[1];
      $description= $keyword_row[2];
      $item_cond = $keyword_row[3];
      $end_date= new DateTime($keyword_row[4]);
      $current_price= $keyword_row[5]; #CHANGE THIS TO CURRENT
      $num_bids = $keyword_row[6];
      
      print_listing_li($item_id, $title, $description, $item_cond, $current_price, $num_bids, $end_date);

        }
      }
  
  else{
    
    for ($count = ($curr_page-1)*$results_per_page; $count<$num_queries;$count++){
      $keyword_row = $keyword_rows[$count];

      $item_id= $keyword_row[0];
      $title = $keyword_row[1];
      $description= $keyword_row[2];
      $item_cond = $keyword_row[3];
      $end_date= new DateTime($keyword_row[4]);
      $current_price= $keyword_row[5]; #CHANGE THIS TO CURRENT
      $num_bids = $keyword_row[6];
      
      print_listing_li($item_id, $title, $description, $item_cond, $current_price, $num_bids, $end_date);
          
        }

  }
  
}
  

// This uses a function defined in utilities.php

    mysqli_close($connection);
    
?>

</ul>

<!-- Pagination for results listings, displaying data on multiple pages -->
<nav aria-label="Search results pages" class="mt-5">
  <ul class="pagination justify-content-center">
  
<?php

  // Copy any currently-set GET variables to the URL.
  $querystring = "";
  foreach ($_GET as $key => $value) {
    if ($key != "page") {
      $querystring .= "$key=$value&amp;";
    }
  }
  
  $high_page_boost = max(3 - $curr_page, 0);
  $low_page_boost = max(2 - ($max_page - $curr_page), 0);
  $low_page = max(1, $curr_page - 2 - $low_page_boost);
  $high_page = min($max_page, $curr_page + 2 + $high_page_boost);
  # if current page is not equal to 1, a label showing previous is visible, allowing you to go back to previous page
  if ($curr_page != 1) {
    echo('
    <li class="page-item">
      <a class="page-link" href="mylistings.php?' . $querystring . 'page=' . ($curr_page - 1) . '" aria-label="Previous">
        <span aria-hidden="true"><i class="fa fa-arrow-left"></i></span>
        <span class="sr-only">Previous</span>
      </a>
    </li>');
  }
  # $i++ is post-increment, i is de referenced and then incremented
  for ($i = $low_page; $i <= $high_page; $i++) {
    if ($i == $curr_page) {
      // Highlight the link
      echo('
    <li class="page-item active">');
    }
    else {
      // Non-highlighted link
      echo('
    <li class="page-item">');
    }
    
    // Do this in any case
    echo('
      <a class="page-link" href="mylistings.php?' . $querystring . 'page=' . $i . '">' . $i . '</a>
    </li>');
  }
   # if current page is not equal to the max page, a label showing next is visible, allowing you to go to next page
  if ($curr_page != $max_page) {
    echo('
    <li class="page-item">
      <a class="page-link" href="mylistings.php?' . $querystring . 'page=' . ($curr_page + 1) . '" aria-label="Next">
        <span aria-hidden="true"><i class="fa fa-arrow-right"></i></span>
        <span class="sr-only">Next</span>
      </a>
    </li>');
  }
?>

  </ul>
</nav>


</div>


<!-- includes code from footer file -->
<?php include_once("footer.php")?>
  
