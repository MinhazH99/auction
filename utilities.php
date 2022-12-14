<?php

// display_time_remaining:
// Helper function to help figure out what time to display
function display_time_remaining($interval) {

    if ($interval->days == 0 && $interval->h == 0) {
      // Less than one hour remaining: print mins + seconds:
      $time_remaining = $interval->format('%im %Ss');
    }
    else if ($interval->days == 0) {
      // Less than one day remaining: print hrs + mins:
      $time_remaining = $interval->format('%hh %im');
    }
    else {
      // At least one day remaining: print days + hrs:
      $time_remaining = $interval->format('%ad %hh');
    }

  return $time_remaining;

}

function default_mybids($ordering)
{
  $connection = mysqli_connect('localhost','root','','auction')
    or die('Error connecting to MySQL server: ' . mysqli_error());
    $user_id = $_SESSION['user_id'];
  
    $keyword_query = "SELECT auctions.auction_id, item_name, item_desc, item_condition, category_name, expirationDate, reserve_price, bid_price, bid_time
    FROM categories, auctions, bids
    WHERE categories.category_id = auctions.category_id
    AND bids.auction_id = auctions.auction_id
    AND bids.user_id != auctions.user_id
    AND bids.user_id = $user_id
    ORDER BY 
    CASE WHEN '{$ordering}' = 'pricelow' THEN bids.bid_price END ASC,
    CASE WHEN '{$ordering}' = 'pricehigh' THEN bids.bid_price END DESC,
    CASE WHEN '{$ordering}' = 'date' THEN auctions.expirationDate END ASC,
    CASE WHEN '{$ordering}' = 'bidold' THEN bids.bid_time END ASC,
    CASE WHEN '{$ordering}' = 'bidnew' THEN bids.bid_time END DESC";

    $count_query = "SELECT COUNT(bids.bid_id) AS 'count'
    FROM  categories, auctions, bids
    WHERE categories.category_id = auctions.category_id
    AND bids.auction_id = auctions.auction_id
    AND bids.user_id != auctions.user_id
    AND bids.user_id = $user_id";



    $count_result = mysqli_query($connection, $count_query) 
      or die('Error making select users query: '. mysqli_error($connection));

    $keyword_result = mysqli_query($connection, $keyword_query) 
      or die('Error making select users query: '. mysqli_error($connection));
    

    $num_results = mysqli_fetch_array($count_result);
    $num_queries = $num_results['count'];
    
    
}
// print_listing_li:
// This function prints an HTML <li> element containing an auction listing
function print_listing_li($item_id, $title, $desc, $cond, $price, $num_bids, $end_time)
{
  // Truncate long descriptions
  if (strlen($desc) > 250) {
    $desc_shortened = substr($desc, 0, 250) . '...';
  }
  else {
    $desc_shortened = $desc;
  }
  
  // Fix language of bid vs. bids
  if ($num_bids == 1) {
    $bid = ' bid';
  }
  else {
    $bid = ' bids';
  }
  
  // Calculate time to auction end
  $now = new DateTime();
  if ($now > $end_time) {
    $time_remaining = 'This auction has ended';
  }
  else {
    // Get interval:
    $time_to_end = date_diff($now, $end_time);
    $time_remaining = display_time_remaining($time_to_end) . ' remaining';
  }
  
  // Print HTML
  echo('
    <li class="list-group-item d-flex justify-content-between">
    <div class="p-2 mr-5"><h5><a href="listing.php?auction_id=' . $item_id . '">' . $title . '</a></h5>' . $desc_shortened . '</div>
    <div class="text-center text-nowrap"><span style="font-size: 1.5em">£' . number_format($price, 2) . '</span><br/>Item condition: '.$cond.'<br/>' . $num_bids . $bid . '<br/>' . $time_remaining . '</div>
  </li>'
  );
}



function print_bid_li($item_id, $title, $desc, $price, $num_bids, $current_time, $end_time, $current_price)
{
  // Truncate long descriptions
  if (strlen($desc) > 250) {
    $desc_shortened = substr($desc, 0, 250) . '...';
  }
  else {
    $desc_shortened = $desc;
  }
  
  // Fix language of bid vs. bids
  if ($num_bids == 1) {
    $bid = ' bid';
  }
  else {
    $bid = ' bids';
  }
  
  // Calculate time to auction end
  $now = new DateTime();
  if ($now > $end_time) {
    $time_remaining = 'This auction has ended';
    
  }
  else {
    // Get interval:
    $time_to_end = date_diff($now, $end_time);
    $time_remaining = display_time_remaining($time_to_end) . ' remaining';
  }
  
  // Print HTML
  echo('
    <li class="list-group-item d-flex justify-content-between">
    <div class="p-2 mr-5"><h5><a href="listing.php?auction_id=' . $item_id . '">' . $title . '</a></h5>' . $desc_shortened . '</div>
    <div class="text-center text-nowrap"><span style="font-size: 1.5em">Your Bid: £' . number_format($price, 2) . '</span><br/>Bid submitted at: '.$current_time.'<br/><br/><h6>Current Price: £'.$current_price.'</h6>' . $num_bids . $bid . ' on this item in total<br/>' . $time_remaining . '<br/></div>
  </li>'
  );
}
?>