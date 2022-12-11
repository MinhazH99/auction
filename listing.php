<?php include_once("header.php")?>
<?php require("utilities.php")?>
<?php require("requirements/dbInformation.php")?>
<?php require("email.php")?>

<?php
  // Get info from the URL:   
  $item_id = $_GET['auction_id'];

  if (!$item_id){
    header("Location: browse.php");
    echo 'This item_id does not exist';
  }

  // TODO: Use item_id to make a query to the database.
  $query = "SELECT auctions.starting_price, auctions.expirationDate, auctions.reserve_price, auctions.item_name, auctions.item_desc, auctions.reserve_price, auctions.user_id FROM auctions WHERE auctions.auction_id = {$item_id}";
  $resultObj = $connection->query($query);
  $row = $resultObj->fetch_assoc();
  $reserve_price = $row['reserve_price'];
  $seller_ID = $row['user_id'];

  if (!$row){
    echo "item not found!";
    die();
  }

  // item details.
  $title = $row['item_name'];
  $description = $row['item_desc'];

  /*  
  TODO: Note: Auctions that have ended may pull a different set of data,
  like whether the auction ended in a sale or was cancelled due
  to lack of high-enough bids. Or maybe not. Calculate time to auction end: 
  */

  $end_time = new DateTime($row['expirationDate']);
  $now = new DateTime();
  
  if ($now < $end_time) {
    $time_to_end = date_diff($now, $end_time);
    $time_remaining = ' (in ' . display_time_remaining($time_to_end) . ')';
  }

  // query from the bids table
  // add in 

  $current_price = $row['starting_price']; 


  // TODO: If the user has a session, use it to make a query to the database
  //       to determine if the user is already watching this item.
  //       For now, this is hardcoded.
  $has_session = False;
  $watching = False;

  if(isset($_SESSION['user_id'])){
    $has_session = True;
    // $query = "SELECT "
    $query = "SELECT CASE WHEN EXISTS ( SELECT * FROM `watch` WHERE `user_id` = {$_SESSION['user_id']} AND `auction_id` = {$item_id} ) THEN 'TRUE' ELSE 'FALSE' END AS 'watch_status'";
    $watch_status = $connection->query($query);
    $row = $watch_status->fetch_assoc();

    if ($row['watch_status']=='TRUE'){
      $watching = True;
    }
  }
?>

<div class="container">

<div class="row"> <!-- Row #1 with auction title + watch button -->
  <div class="col-sm-8"> <!-- Left col -->
    <h2 class="my-3"><?php echo($title); ?></h2>
  </div>
  <div class="col-sm-4 align-self-center"> <!-- Right col -->
<?php
  /* The following watchlist functionality uses JavaScript, but could
     just as easily use PHP as in other places in the code */
  if ($now < $end_time):
?>
    <div id="watch_nowatch" <?php if ($has_session && $watching) echo('style="display: none"');?> >
      <button type="button" class="btn btn-outline-secondary btn-sm" onclick="addToWatchlist()">+ Add to watchlist</button>
    </div>
    <div id="watch_watching" <?php if (!$has_session || !$watching) echo('style="display: none"');?> >
      <button type="button" class="btn btn-success btn-sm" disabled>Watching</button>
      <button type="button" class="btn btn-danger btn-sm" onclick="removeFromWatchlist()">Remove watch</button>
    </div>
<?php endif /* Print nothing otherwise */ ?>
  </div>
</div>

<div class="row"> <!-- Row #2 with auction description + bidding info -->
  <div class="col-sm-8"> <!-- Left col with item info -->

    <div class="itemDescription">
    <?php echo($description); ?>
    </div>

  </div>

  <div class="col-sm-4"> <!-- Right col with bidding info -->

    <p>
    
    <?php
    $query = "SELECT `user_id` FROM `bids` WHERE auction_id = {$item_id} AND `bid_price` = {$current_price}";
    $no_bids = $connection->query($query);
    $winning_bid = $no_bids->fetch_assoc();
  
    $sold = False;
    
    if ($winning_bid && (int) $reserve_price <= (int) $current_price){
      $sold = True;
        
    }

    // $find_sellers_details = "SELECT `email`, `first_name` FROM `users` WHERE `user_id` IN ({$seller_ID}, {$buyer_id})";
    $find_seller_details = "SELECT `email`, `first_name` FROM `users` WHERE `user_id` = {$seller_ID}";
    $email_obj = $connection->query($find_seller_details);

    $row = $email_obj->fetch_assoc();
    $sellerEmail = $row['email'];
    $sellerName = $row['first_name'];
    
    $find_buyer_details = "SELECT `email`, `first_name` FROM `users` WHERE `user_id` = {$winning_bid['user_id']}";
    $email_obj = $connection->query($find_buyer_details);

    if ($email_obj){
      $row2 = $email_obj->fetch_assoc();
      $buyerEmail = $row2['email'];
      $buyerName = $row2['first_name'];  
    }
    ?>
    
<?php if ($now > $end_time): ?>
     This auction ended <?php echo(date_format($end_time, 'j M H:i')) ?>
     <!-- TODO: Print the result of the auction here? --> 
     <?php if ($sold){ ?>
      <p class="lead"><?=$title?> sold at £<?php echo(number_format($current_price, 2)) ?></p>
      <p class="lead"><?=$title?> The winner was MHM User: <?php echo($winning_bid['user_id']) ?></p>
     <?php seller_email($item_id, $sellerEmail, $sellerName);
      buyer_email($item_id, $buyerEmail, $buyerName);
      } else { 
        no_sale($sellerEmail, $sellerName);  
      ?>
      <p class="lead"><?=$title?> did not sell at £<?php echo(number_format($current_price, 2)) ?></p>
     <?php } ?>

<?php else: ?>
     Auction ends <?php echo(date_format($end_time, 'j M H:i') . $time_remaining) ?></p>
        <p class="lead">Current bid: £<?php echo(number_format($current_price, 2)) ?></p>
        <?php if ($winning_bid){?>
          <p class="lead"><?=$title?> The current winner is MHM User: <?php echo($winning_bid['user_id']) ?></p>
        <?php } ?>
    <!-- Bidding form -->
    <form method="POST" action="place_bid.php">
      <div class="input-group">
        <div class="input-group-prepend">
          <span class="input-group-text">£</span>
        </div>
	    <input type="number" class="form-control" id="bid" name="bid_price" value='0'>
      </div>
      <input type="hidden" id="auction_id" name="auction_id" value=<?php echo $_GET['auction_id'] ?>>
      <div></div>
      <button type="submit" class="btn btn-primary form-control">Place bid</button>
    </form>
<?php endif ?>

  
  </div> <!-- End of right col with bidding info -->

</div> <!-- End of row #2 -->

<?php $resultObj->close(); ?>

<?php include_once("footer.php")?>


<script> 
// JavaScript functions: addToWatchlist and removeFromWatchlist.

function addToWatchlist(button) {
  console.log("These print statements are helpful for debugging btw");

  // This performs an asynchronous call to a PHP function using POST method.
  // Sends item ID as an argument to that function.
  $.ajax('watchlist_funcs.php', {
    type: "POST",
    data: {functionname: 'add_to_watchlist', arguments: [<?php echo($item_id);?>]},

    success: 
      function (obj, textstatus) {
        // Callback function for when call is successful and returns obj
        console.log("Success");
        var objT = obj.trim();
 
        if (objT == "success") {
          $("#watch_nowatch").hide();
          $("#watch_watching").show();
        }
        else {
          var mydiv = document.getElementById("watch_nowatch");
          mydiv.appendChild(document.createElement("br"));
          mydiv.appendChild(document.createTextNode("Add to watch failed. Try again later."));
        }
      },

    error:
      function (obj, textstatus) {
        console.log("Error");
      }
  }); // End of AJAX call

} // End of addToWatchlist func

function removeFromWatchlist(button) {
  // This performs an asynchronous call to a PHP function using POST method.
  // Sends item ID as an argument to that function.
  $.ajax('watchlist_funcs.php', {
    type: "POST",
    data: {functionname: 'remove_from_watchlist', arguments: [<?php echo($item_id);?>]},

    success: 
      function (obj, textstatus) {
        // Callback function for when call is successful and returns obj
        console.log("Success");
        var objT = obj.trim();
 
        if (objT == "success") {
          $("#watch_watching").hide();
          $("#watch_nowatch").show();
        }
        else {
          var mydiv = document.getElementById("watch_watching");
          mydiv.appendChild(document.createElement("br"));
          mydiv.appendChild(document.createTextNode("Watch removal failed. Try again later."));
        }
      },

    error:
      function (obj, textstatus) {
        console.log("Error");
      }
  }); // End of AJAX call

} // End of addToWatchlist func
</script>