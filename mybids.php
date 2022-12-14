 <?php include_once("header.php")?>
<?php require("utilities.php")?>

<div class="container">

<h2 class="my-3">My bids</h2>

<div id="orderMyBids">
<!-- When this form is submitted, this PHP page is what processes it.
     Search/sort specs are passed to this page through parameters in the URL
     (GET method of passing data to a page). -->
<form method="get" action="mybids.php"> <!-- form data sent as url variables, action is url of file that will process input -->
  <div class="row">
    <div class="col-md-3 pr-0">
      <div class="form-group">
        <label for="cat2" class="sr-only">Search within:</label> <!-- for screen readers only -->
        <select name="cat2" class="form-control" id="cat2">
          <option selected value="all">All categories</option>
          <option value="Clothes, Shoes, Jewellery and Accessories">Clothes, Shoes, Jewellery and Accessories</option>
          <option value="Health & Beauty">Health & Beauty</option>
          <option value="Sports & Outdoor">Sports & Outdoor</option>
          <option value="Technology">Technology</option>
          <option value="Toys, Children & Baby">Toys, Children & Baby</option> <!-- drop down list of category options on website -->
        </select>
      </div>
    </div>
    <div class="col-md-4 pr-0">
      <div class="form-inline">
        <label class="mx-2" for="order_by2">Sort by:</label>
        <select name="order_by2" class="form-control" id="order_by2">
          <option selected value="date">Soonest Auction expiry</option>
          <option value="bidold">Date of Bid (oldest to newest)</option>
          <option value="bidnew">Date of Bid (newest to oldest)</option>
          <option value="pricelow">Bid Price (low to high)</option>
          <option value="pricehigh">Bid Price (high to low)</option>
          
          <!-- drop down list of sort by options on website -->
        </select>
      </div>
    </div>
    <div class="col-md-1 px-0">
      <button type="submit" class="btn btn-primary">Sort</button> <!-- button that says search, and submits query -->
    </div>
  </div>
</form>
</div> <!-- end search specs bar -->


</div>
<?php
  function isLoggedIn() { return isset($_SESSION['user_id']);}

  if (!isLoggedIn())
  echo('
  <li class="list-group-item d-flex justify-content-center">
  <div class="p-2 mr-5"><h5><center>Please login</center></h5>
  </div>
  
  </li>'
  
  );
  

  // This page is for showing a user the auctions they've bid on.
  // It will be pretty similar to browse.php, except there is no search bar.
  // This can be started after browse.php is working with a database.
  // Feel free to extract out useful functions from browse.php and put them in
  // the shared "utilities.php" where they can be shared by multiple files.
  
  
  // TODO: Check user's credentials (cookie/session).
  
  // TODO: Perform a query to pull up the auctions they've bidded on.
  
  // TODO: Loop through results and print them out as list items.
  
  $num_queries=1;
  
  // Retrieve these from the URL
  if (!isset($_GET['order_by2']) && !isset($_GET['cat2'])) {
    $ordering ="date";
    $category = "all";
    $connection = mysqli_connect('localhost','root','','auction')
    or die('Error connecting to MySQL server: ' . mysqli_error());
    $user_id = $_SESSION['user_id'];
  
    $keyword_query = "SELECT bids.user_id, auctions.auction_id, item_name, 
    item_desc, expirationDate, bid_price, bid_time, starting_price
    FROM auctions, categories, bids
    WHERE categories.category_id = auctions.category_id
    AND bids.auction_id = auctions.auction_id
    AND bids.user_id = $user_id
    GROUP BY bids.bid_id
    ORDER BY 
    CASE WHEN '{$ordering}' = 'date' THEN bids.bid_time END ASC
    ";

    $count_bids_query = "SELECT auctions.auction_id, bids.user_id, 
    COUNT(bids.bid_id) AS 'truenumbids'
    FROM auctions, categories, bids
    WHERE categories.category_id = auctions.category_id
    AND bids.auction_id = auctions.auction_id
    GROUP BY auctions.auction_id";
  }

  else {
    $ordering = $_GET['order_by2'];
  }

  if (!isset($_GET['cat2'])) {
    $category = "all";
  }
#isset checks whether the category variable has been set after it has been submitted using 'get'
  else if ($_GET['cat2']=="all") {
   
    $connection = mysqli_connect('localhost','root','','auction')
    or die('Error connecting to MySQL server: ' . mysqli_error());
    $user_id = $_SESSION['user_id'];
    $category = "all";
    $keyword_query = "SELECT bids.user_id, auctions.auction_id, item_name, 
    item_desc, expirationDate, bid_price, bid_time, starting_price
    FROM auctions, categories, bids
    WHERE categories.category_id = auctions.category_id
    AND bids.auction_id = auctions.auction_id
    AND bids.user_id = $user_id
    GROUP BY bids.bid_id
    ORDER BY 
    CASE WHEN '{$ordering}' = 'pricelow' THEN bids.bid_price END ASC,
    CASE WHEN '{$ordering}' = 'pricehigh' THEN bids.bid_price END DESC,
    CASE WHEN '{$ordering}' = 'date' THEN auctions.expirationDate END ASC,
    CASE WHEN '{$ordering}' = 'bidold' THEN bids.bid_time END ASC,
    CASE WHEN '{$ordering}' = 'bidnew' THEN bids.bid_time END DESC";

    $count_bids_query = "SELECT auctions.auction_id, bids.user_id, 
    COUNT(bids.bid_id) AS 'truenumbids'
    FROM auctions, categories, bids
    WHERE categories.category_id = auctions.category_id
    AND bids.auction_id = auctions.auction_id
    GROUP BY auctions.auction_id";

  
    // Demonstration of what listings will look like using dummy data. specifies information about listing
  
  // This uses a function defined in utilities.php
  #print_listing_li($item_id, $title, $description, $current_price, $num_bids, $end_date);

  }
  else {
    $category = $_GET['cat2'];
    $connection = mysqli_connect('localhost','root','','auction')
    or die('Error connecting to MySQL server: ' . mysqli_error());
    $user_id = $_SESSION['user_id'];
  
    $keyword_query = "SELECT bids.user_id, auctions.auction_id, item_name, item_desc, expirationDate, bid_price, bid_time, starting_price
    FROM auctions, categories, bids
    WHERE categories.category_id = auctions.category_id
    AND bids.auction_id = auctions.auction_id
    AND categories.category_name = '{$category}'
    AND bids.user_id = $user_id
    GROUP BY bids.bid_id
    ORDER BY 
    CASE WHEN '{$ordering}' = 'pricelow' THEN bids.bid_price END ASC,
    CASE WHEN '{$ordering}' = 'pricehigh' THEN bids.bid_price END DESC,
    CASE WHEN '{$ordering}' = 'date' THEN auctions.expirationDate END ASC, 
    CASE WHEN '{$ordering}' = 'bidold' THEN bids.bid_time END ASC,
    CASE WHEN '{$ordering}' = 'bidnew' THEN bids.bid_time END DESC";
  
    $count_bids_query = "SELECT auctions.auction_id, bids.user_id, COUNT(bids.bid_id) AS 'truenumbids'
    FROM auctions, categories, bids
    WHERE categories.category_id = auctions.category_id
    AND bids.auction_id = auctions.auction_id
    AND categories.category_name = '{$category}'
    
    GROUP BY auctions.auction_id";

    
    
  // Demonstration of what listings will look like using dummy data. specifies information about listing
  
  // This uses a function defined in utilities.php
  
     
  }
  
  #isset checks whether the page has been set after it has been submitted using 'get'
  
  

?>

<div class="container mt-5"> <!-- mt-5 margin at top of 5 -->

<?php 

$keyword_result = mysqli_query($connection, $keyword_query) 
  or die('Error making select users query: '. mysqli_error($connection));

$bids_result = mysqli_query($connection, $count_bids_query)
  or die('Error making select users query: '. mysqli_error($connection));

$num_queries = mysqli_num_rows($keyword_result);

$results_per_page = 10;

if (!isset($_GET['page'])) {
  $curr_page = 1; #php variable
}
else {
  $curr_page = $_GET['page'];
}

if (!isset($num_queries)) {
  $num_queries = 0;
  $max_page = 1;
  
}

if ($num_queries==0){
  $max_page = 1;
  
    if ($category == "all"){echo('
    <li class="list-group-item d-flex justify-content-center">
    <div class="p-2 mr-5"><h5><center>You have not bid on any items</center></h5>
    </div>
    </li>');    

  }
  
  else
  {echo('
    <li class="list-group-item d-flex justify-content-center">
    <div class="p-2 mr-5"><h5><center>There are no bids on items in the category: '.$category.'</center></h5>
    </div>
    </li>');
    

  }
}


else {
  
  $max_page = ceil($num_queries / $results_per_page);

}
?>


<ul class="list-group">


<?php

$auction_id = array();
$true_bids = array();

while ($row = mysqli_fetch_array($bids_result))
  {
    $auction_id[] = $row['auction_id'];
  $true_bids[] = $row['truenumbids'];
  
}

$num_auctions = count($auction_id);

$keyword_rows = $keyword_result -> fetch_all(MYSQLI_NUM);
if ($num_queries > 0){
  if ($curr_page < $max_page) {
    for ($count = ($curr_page-1)*$results_per_page; $count<$curr_page*$results_per_page;$count++){
      $keyword_row = $keyword_rows[$count];
      
      
      $item_id= $keyword_row[1];
      $title = $keyword_row[2];
      $description= $keyword_row[3];
      $end_date= new DateTime($keyword_row[4]);
      $bid_price = $keyword_row[5];
      $current_time = $keyword_row[6];
      $current_price= $keyword_row[7]; #CHANGE THIS TO CURRENT
      for ($num = 0; $num < $num_auctions; $num++){
        if ($auction_id[$num] == $item_id){
          $num_bids = $true_bids[$num];
          print_bid_li($item_id, $title, $description, $bid_price, $num_bids, $current_time, $end_date, $current_price);
        }
        else {
          continue;
        }
      
      }

        }
      }
  else{
    
    for ($count = ($curr_page-1)*$results_per_page; $count<$num_queries;$count++){
      $keyword_row = $keyword_rows[$count];

      $item_id= $keyword_row[1];
      $title = $keyword_row[2];
      $description= $keyword_row[3];
      $end_date= new DateTime($keyword_row[4]);
      $bid_price = $keyword_row[5];
      $current_time = $keyword_row[6];
      $current_price= $keyword_row[7]; #CHANGE THIS TO CURRENT
     
      for ($num = 0; $num < $num_auctions; $num++){
        if ($auction_id[$num] == $item_id){
          $num_bids = $true_bids[$num];
          print_bid_li($item_id, $title, $description, $bid_price, $num_bids, $current_time, $end_date, $current_price);
        }
        else {
          continue;
        }
      
      }
         
          
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
      <a class="page-link" href="mybids.php?' . $querystring . 'page=' . ($curr_page - 1) . '" aria-label="Previous">
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
      <a class="page-link" href="mybids.php?' . $querystring . 'page=' . $i . '">' . $i . '</a>
    </li>');
  }
   # if current page is not equal to the max page, a label showing next is visible, allowing you to go to next page
  if ($curr_page != $max_page) {
    echo('
    <li class="page-item">
      <a class="page-link" href="mybids.php?' . $querystring . 'page=' . ($curr_page + 1) . '" aria-label="Next">
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