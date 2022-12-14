<?php include_once("header.php")?>
<?php require("utilities.php")?>

<!-- Will have overlapping code with mybids and mylistings -->

<div class="container">

<h2 class="my-3">Recommendations for you</h2>

<?php
  // This page is for showing a buyer recommended items based on their bid 
  // history. It will be pretty similar to browse.php, except there is no 
  // search bar. This can be started after browse.php is working with a database.
  // Feel free to extract out useful functions from browse.php and put them in
  // the shared "utilities.php" where they can be shared by multiple files.
  
  
  // TODO: Check user's credentials (cookie/session).
  
  // TODO: Perform a query to pull up auctions they might be interested in.
  
  // TODO: Loop through results and print them out as list items.

  function isLoggedIn() { return isset($_SESSION['user_id']);}

  if (!isLoggedIn())
  echo('
  <li class="list-group-item d-flex justify-content-center">
  <div class="p-2 mr-5"><h5><center>Please login</center></h5>
  </div>
  
  </li>'
  
  );
  $num_queries=1;
  $now = new DateTime();
  $connection = mysqli_connect('localhost','root','','auction')
  or die('Error connecting to MySQL server: ' . mysqli_error());
  $user_id = $_SESSION['user_id'];
  
  /*
  $recommendations = "SELECT item_name from auctions WHERE item_name IN 
  (SELECT item_name FROM auctions WHERE auction_id IN 
  (SELECT auction_id FROM reviews WHERE rating >= 4 AND user_id IN 
  (SELECT user_id FROM bids WHERE auction_id IN 
  (SELECT auction_id FROM bids WHERE user_id = $user))))";

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
  $recommended_item = "SELECT auction_id,item_name,item_desc,expirationDate,starting_price,  COUNT(bids.bid_id) AS 'truenumbids'
  FROM auctions WHERE item_name LIKE '%{$item_name[$i]}%' 
  AND auction_status = 'Open'";

  $recommend_item_result = mysqli_query($connection, $recommended_item);

  while ($recommended_listing = mysqli_fetch_array($recommend_item_result))
  {
    $item_id = $recommended_listing['auction_id'];
    $title = $recommended_listing['item_name'];
    $description = $recommended_listing['item_desc'];
    $current_price = $recommended_listing['starting_price'];
    $end_date = new DateTime($recommended_listing['expirationDate']);
    #for ($num = 0; $num < $count; $num++){
      #if ($auction_id[$num] == $item_id){
       # $num_bids = $true_bids[$num];
       $num_bids = $recommended_listing['truenumbids']
        print_listing_li($item_id, $title, $description, $current_price, $num_bids, $end_date);
      #}
      #else {
       # continue;
      #}
    #}


  }


  }



*/

/*$recommendations = "SELECT item_name from auctions WHERE item_name IN 
  (SELECT item_name FROM auctions WHERE auction_id IN 
  (SELECT auction_id FROM reviews WHERE rating >= 4 AND user_id IN 
  (SELECT user_id FROM bids WHERE auction_id IN 
  (SELECT auction_id FROM bids WHERE user_id = $user))))"; */

 
 $keyword_query = "SELECT bids.user_id, auctions.auction_id, 
  category_name, item_name, item_desc, item_condition,
  starting_price,expirationDate
  
  
  FROM auctions, categories, bids
  WHERE categories.category_id = auctions.category_id
  AND bids.auction_id = auctions.auction_id
  AND auctions.user_id != $user_id
  AND expirationDate > CURRENT_TIMESTAMP()

  AND bids.user_id IN (SELECT user_id FROM bids WHERE auction_id IN 
  (SELECT auction_id FROM bids WHERE user_id = $user_id)
  )

  AND categories.category_id IN (SELECT categories.category_id
  FROM auctions, categories, bids
  WHERE categories.category_id = auctions.category_id
  AND bids.auction_id = auctions.auction_id
  AND bids.user_id = $user_id
  GROUP BY bids.bid_id)

  AND auctions.auction_id NOT IN (SELECT auctions.auction_id
  FROM auctions, categories, bids
  WHERE categories.category_id = auctions.category_id
  AND bids.auction_id = auctions.auction_id
  AND bids.user_id = $user_id
  GROUP BY bids.bid_id)
  GROUP BY auctions.auction_id";

  $count_bids_query = "SELECT auctions.auction_id, bids.user_id, 
    COUNT(bids.bid_id) AS 'truenumbids'
    FROM auctions, categories, bids
    WHERE categories.category_id = auctions.category_id
    AND bids.auction_id = auctions.auction_id
    GROUP BY auctions.auction_id
  ";
/*
  
  */

  ?>

<div class="container mt-5"> <!-- mt-5 margin at top of 5 -->

<!-- TODO: If result set is empty, print an informative message. Otherwise...-->
<?php 
   
   $keyword_result = mysqli_query($connection, $keyword_query) 
   or die('Error making select users query: '. mysqli_error($connection));

   $bids_result = mysqli_query($connection, $count_bids_query)
   or die('Error making select users query: '. mysqli_error($connection));

  $num_queries = mysqli_num_rows($keyword_result);
  $results_per_page = 10;


  $keyword_rows = $keyword_result -> fetch_all(MYSQLI_NUM);
  
  
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
    
  }
  
  else {
    
    $max_page = ceil($num_queries / $results_per_page);
  
  }
  
// This uses a function defined in utilities.php
 $auction_id = array();
$true_bids = array();

while ($row = mysqli_fetch_array($bids_result))
  {
    $auction_id[] = $row['auction_id'];
  $true_bids[] = $row['truenumbids'];
  
}

$num_auctions = count($auction_id);
  

  if ($num_queries > 0){
    if ($curr_page < $max_page) {
      for ($count = ($curr_page-1)*$results_per_page; $count<$curr_page*$results_per_page;$count++){
        $keyword_row = $keyword_rows[$count];

        $bid_user_id = $keyword_row[0];
        $item_id= $keyword_row[1];
        $category = $keyword_row[2];
     
  
        $title = $keyword_row[3];
        $description= $keyword_row[4];
        $item_cond = $keyword_row[5];
        $current_price= $keyword_row[6]; #CHANGE THIS TO CURRENT
        $end_date= new DateTime($keyword_row[7]);
  
        

        for ($num = 0; $num < $num_auctions; $num++){
          if ($auction_id[$num] == $item_id){
            $num_bids = $true_bids[$num];
            print_listing_li($item_id, $title, $description, $item_cond, $current_price, $num_bids, $end_date);
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

        $bid_user_id = $keyword_row[0];
        $item_id= $keyword_row[1];
        $category = $keyword_row[2];
     
  
        $title = $keyword_row[3];
        $description= $keyword_row[4];
        $item_cond = $keyword_row[5];
        $current_price= $keyword_row[6]; #CHANGE THIS TO CURRENT
        $end_date= new DateTime($keyword_row[7]);
  
        for ($num = 0; $num < $num_auctions; $num++){
          if ($auction_id[$num] == $item_id){
            $num_bids = $true_bids[$num];
            print_listing_li($item_id, $title, $description, $item_cond, $current_price, $num_bids, $end_date);
          }
          else {
            continue;
          }
  
        
            
          }
  
    }
    
  }
}

  if ($num_queries==0){
    $max_page = 1;
    echo('
      <li class="list-group-item d-flex justify-content-center">
      <div class="p-2 mr-5"><h5><center>You do not currently have any recommendations</center></h5>
      <p><center>Please try bidding on different types of items</center></p></div>
      
    </li>'
    );

  }

  
  mysqli_close($connection);
  
  #echo $num_queries;
 


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
      <a class="page-link" href="recommendations.php?' . $querystring . 'page=' . ($curr_page - 1) . '" aria-label="Previous">
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
      <a class="page-link" href="recommendations.php?' . $querystring . 'page=' . $i . '">' . $i . '</a>
    </li>');
  }
   # if current page is not equal to the max page, a label showing next is visible, allowing you to go to next page
  if ($curr_page != $max_page) {
    echo('
    <li class="page-item">
      <a class="page-link" href="recommendations.php?' . $querystring . 'page=' . ($curr_page + 1) . '" aria-label="Next">
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