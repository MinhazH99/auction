<?php include_once("header.php")?> <!-- embeds php code from another file-->
<?php require("utilities.php")?> <!-- copies all text from utilities into current one -->

<div class="container">

<h2 class="my-3">Browse listings</h2>

<div id="searchSpecs">
<!-- When this form is submitted, this PHP page is what processes it.
     Search/sort specs are passed to this page through parameters in the URL
     (GET method of passing data to a page). -->
<form method="get" action="browse.php"> <!-- form data sent as url variables, action is url of file that will process input -->
  <div class="row">
    <div class="col-md-5 pr-0"> <!-- medium column -->
      <div class="form-group"> <!-- used for optimum spacing -->
        <label for="keyword" class="sr-only">Search keyword:</label> <!-- for screen readers only -->
	    <div class="input-group"> <!-- helps enhance inputs, in this case with search icon in front of input (prepend) -->
          <div class="input-group-prepend">
            <span class="input-group-text bg-transparent pr-0 text-muted">
              <i class="fa fa-search"></i>
            </span>
          </div>
          <input name="keyword" type="text" class="form-control border-left-0" id="keyword" placeholder="Search for anything"><!-- placeholder text is visible -->
        </div>
      </div>
    </div>
    <div class="col-md-3 pr-0">
      <div class="form-group">
        <label for="cat" class="sr-only">Search within:</label> <!-- for screen readers only -->
        <select name="cat" class="form-control" id="cat">
          <option selected value="all">All categories</option>
          <option value="Sports">Sports</option>
          <option value="Technology">Technology</option>
          <option value="Toys">Toys</option> <!-- drop down list of category options on website -->
        </select>
      </div>
    </div>
    <div class="col-md-3 pr-0">
      <div class="form-inline">
        <label class="mx-2" for="order_by">Sort by:</label>
        <select name="order_by" class="form-control" id="order_by">
          <option selected value="pricelow">Price (low to high)</option>
          <option value="pricehigh">Price (high to low)</option>
          <option value="date">Soonest expiry</option> <!-- drop down list of sort by options on website -->
        </select>
      </div>
    </div>
    <div class="col-md-1 px-0">
      <button type="submit" class="btn btn-primary">Search</button> <!-- button that says search, and submits query -->
    </div>
  </div>
</form>
</div> <!-- end search specs bar -->


</div>

<?php
  $num_queries=1;
  #isset checks whether the order_by variable has been set after it has been submitted using 'get'
  if (!isset($_GET['order_by'])) {
    
  }
  else {
    $ordering = $_GET['order_by'];
  }
  // Retrieve these from the URL
  #isset checks whether the keyword variable has been set after it has been submitted using 'get'
  if (!isset($_GET['keyword'])) {
    $keyword = " ";
    // TODO: Define behavior if a keyword has not been specified.

    #THE QUERY FOR NO KEYWORD ENTERED IS GROUPED WITH THE CATEGORY IS ALL SECTION, THE CODE WOULD BE IDENTICAL
  }
  else {
    $keyword = $_GET['keyword'];
    
  }
  if (!isset($_GET['cat'])) {
    $category = "all";
  }
#isset checks whether the category variable has been set after it has been submitted using 'get'
  else if ($_GET['cat']=="all") {
    // TODO: Define behavior if a category has not been specified.
    $category = "all";
    $connection = mysqli_connect('localhost','root','','auction')
    or die('Error connecting to MySQL server: ' . mysqli_error());
  
    $keyword_query = "SELECT items.item_id, item_name, item_desc, item_condition, category_name, expirationDate, reserve_price
    FROM items, categories, auctions
    WHERE categories.category_id = items.category_id
    AND auctions.item_id = items.item_id
    AND (INSTR(items.item_desc, TRIM(' ' FROM '{$keyword}')) 
    OR INSTR(items.item_name, TRIM(' ' FROM '{$keyword}')) )> 0
    ORDER BY 
    CASE WHEN '{$ordering}' = 'pricelow' THEN auctions.reserve_price END ASC,
    CASE WHEN '{$ordering}' = 'pricehigh' THEN auctions.reserve_price END DESC,
    CASE WHEN '{$ordering}' = 'date' THEN auctions.expirationDate END ASC";

    $count_query = "SELECT COUNT(items.item_id) AS 'count'
    FROM items, categories, auctions
    WHERE categories.category_id = items.category_id
    AND auctions.item_id = items.item_id
    AND (INSTR(items.item_desc, TRIM(' ' FROM '{$keyword}')) 
    OR INSTR(items.item_name, TRIM(' ' FROM '{$keyword}')) )> 0
    ORDER BY 
    CASE WHEN '{$ordering}' = 'pricelow' THEN auctions.reserve_price END ASC,
    CASE WHEN '{$ordering}' = 'pricehigh' THEN auctions.reserve_price END DESC,
    CASE WHEN '{$ordering}' = 'date' THEN auctions.expirationDate END ASC";



    $count_result = mysqli_query($connection, $count_query) 
      or die('Error making select users query: '. mysqli_error($connection));

    $keyword_result = mysqli_query($connection, $keyword_query) 
      or die('Error making select users query: '. mysqli_error($connection));
    

    $num_results = mysqli_fetch_array($count_result);
    $num_queries = $num_results['count'];
    
    while ($keyword_row = mysqli_fetch_array($keyword_result))
    {

    $item_id= $keyword_row['item_id'];
    $title = $keyword_row['item_name'];
    $description= $keyword_row['item_desc'];
    $current_price= $keyword_row['reserve_price'];
    $end_date= new DateTime($keyword_row['expirationDate']);
    $num_bids = 1;
  
// This uses a function defined in utilities.php
    print_listing_li($item_id, $title, $description, $current_price, $num_bids, $end_date);
    }
    mysqli_close($connection);
  // Demonstration of what listings will look like using dummy data. specifies information about listing
  
  // This uses a function defined in utilities.php
  #print_listing_li($item_id, $title, $description, $current_price, $num_bids, $end_date);

  }
  else {
    $category = $_GET['cat'];
    $connection = mysqli_connect('localhost','root','','auction')
    or die('Error connecting to MySQL server: ' . mysqli_error());
  
    $keyword_query = "SELECT items.item_id, item_name, item_desc, item_condition, category_name, expirationDate, reserve_price
    FROM items, categories, auctions
    WHERE categories.category_id = items.category_id
    AND categories.category_name = '{$category}'
    AND auctions.item_id = items.item_id
    AND (INSTR(items.item_desc, TRIM(' ' FROM '{$keyword}')) 
    OR INSTR(items.item_name, TRIM(' ' FROM '{$keyword}')) )> 0
    ORDER BY 
    CASE WHEN '{$ordering}' = 'pricelow' THEN auctions.reserve_price END ASC,
    CASE WHEN '{$ordering}' = 'pricehigh' THEN auctions.reserve_price END DESC,
    CASE WHEN '{$ordering}' = 'date' THEN auctions.expirationDate END ASC";
  
    $count_query = "SELECT COUNT(items.item_id) AS 'count'
    FROM items, categories, auctions
    WHERE categories.category_id = items.category_id
    AND categories.category_name = '{$category}'
    AND auctions.item_id = items.item_id
    AND (INSTR(items.item_desc, TRIM(' ' FROM '{$keyword}')) 
    OR INSTR(items.item_name, TRIM(' ' FROM '{$keyword}')) )> 0
    ORDER BY 
    CASE WHEN '{$ordering}' = 'pricelow' THEN auctions.reserve_price END ASC,
    CASE WHEN '{$ordering}' = 'pricehigh' THEN auctions.reserve_price END DESC,
    CASE WHEN '{$ordering}' = 'date' THEN auctions.expirationDate END ASC";
  
    $keyword_result = mysqli_query($connection, $keyword_query) 
      or die('Error making select users query: '. mysqli_error($connection));
  
    $count_result = mysqli_query($connection, $count_query) 
      or die('Error making select users query: '. mysqli_error($connection));
    
    
 
    $num_results = mysqli_fetch_array($count_result);
    $num_queries = $num_results['count'];
    
    while ($keyword_row = mysqli_fetch_array($keyword_result))
    {

    $item_id= $keyword_row['item_id'];
    $title = $keyword_row['item_name'];
    $description= $keyword_row['item_desc'];
    $current_price= $keyword_row['reserve_price'];
    $end_date= new DateTime($keyword_row['expirationDate']);
    $num_bids = 1;
  
// This uses a function defined in utilities.php
    print_listing_li($item_id, $title, $description, $current_price, $num_bids, $end_date);
    }
    mysqli_close($connection);
  // Demonstration of what listings will look like using dummy data. specifies information about listing
  
  // This uses a function defined in utilities.php
  #print_listing_li($item_id, $title, $description, $current_price, $num_bids, $end_date);
     
  }
  
  #isset checks whether the page has been set after it has been submitted using 'get'
  if (!isset($_GET['page'])) {
    $curr_page = 1; #php variable
  }
  else {
    $curr_page = $_GET['page'];
  }
  
  /* TODO: Use above values to construct a query. Use this query to 
     retrieve data from the database. (If there is no form data entered,
     decide on appropriate default value/default query to make. */
  

  /* For the purposes of pagination, it would also be helpful to know the
     total number of results that satisfy the above query */
  // TODO: Calculate me for real
  
  
  if (!isset($num_queries)) {
    $num_queries = 1;
    
  }
  
  if ($num_queries==0){
    $num_queries = 1;
    echo('
      <li class="list-group-item d-flex justify-content-center">
      <div class="p-2 mr-5"><h5><center>There are no matches for your search: '.$keyword.'</center></h5>
      <p><center>Please try another keyword</center></p></div>
      
    </li>'
    );
  }


  $results_per_page = 10;
  $max_page = ceil($num_queries / $results_per_page);
  

?>

<div class="container mt-5"> <!-- mt-5 margin at top of 5 -->

<!-- TODO: If result set is empty, print an informative message. Otherwise...-->


<ul class="list-group">

<!-- TODO: Use a while loop to print a list item for each auction listing
     retrieved from the query -->

<?php
  
  
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
      <a class="page-link" href="browse.php?' . $querystring . 'page=' . ($curr_page - 1) . '" aria-label="Previous">
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
      <a class="page-link" href="browse.php?' . $querystring . 'page=' . $i . '">' . $i . '</a>
    </li>');
  }
   # if current page is not equal to the max page, a label showing next is visible, allowing you to go to next page
  if ($curr_page != $max_page) {
    echo('
    <li class="page-item">
      <a class="page-link" href="browse.php?' . $querystring . 'page=' . ($curr_page + 1) . '" aria-label="Next">
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