<?php include_once("header.php")?>
<?php require("utilities.php")?>
<?php require("requirements/dbInformation.php")?>
<?php require("email.php")?>

<?php
  // Get info from the URL:   
  $auctionID = $_GET['auction_id'];
  
  if (!$auctionID){
    header("Location: browse.php");
  }

  // TODO: Use auctionID to make a query to the database.
  $query = "SELECT auctions.starting_price, auctions.expirationDate, auctions.reserve_price, auctions.item_name, auctions.item_desc, auctions.reserve_price, auctions.user_id, auctions.item_condition FROM auctions WHERE auctions.auction_id = {$auctionID}";
  $resultObj = $connection->query($query);
  $row = $resultObj->fetch_assoc();
  
  if (!$row){
    header("Location: browse.php");
    die();
  }
  
  $reserve_price = $row['reserve_price'];
  $seller_ID = $row['user_id'];
  $condition = $row['item_condition'];

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
    $query = "SELECT CASE WHEN EXISTS ( SELECT * FROM `watch` WHERE `user_id` = {$_SESSION['user_id']} AND `auction_id` = {$auctionID} ) THEN 'TRUE' ELSE 'FALSE' END AS 'watch_status'";
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
    $query = "SELECT `user_id` FROM `bids` WHERE auction_id = {$auctionID} AND `bid_price` = {$current_price}";
    $no_bids = $connection->query($query);
    $winning_bid = $no_bids->fetch_assoc();
  
    $sold = False;
    
    if ($winning_bid && (int) $reserve_price <= (int) $current_price){
      $sold = True;
    } 
    if ($winning_bid) {
      $query = "SELECT `first_name`, `last_name`  FROM `users` WHERE `user_id` = {$winning_bid['user_id']}";
      $action = $connection->query($query);
      $buyerName = $action->fetch_assoc();

      $firstName = $buyerName['first_name'];
      $lastName = $buyerName['last_name'];
    }?>

<?php if ($now > $end_time): ?>
     This auction ended <?php echo(date_format($end_time, 'j M H:i')) ?>
     <!-- TODO: Print the result of the auction here? --> 
     <?php if ($sold){ ?>
      <p class="lead"><?=$title?> sold at £<?php echo(number_format($current_price, 2)) ?></p>
      <p class="lead"><?=$title?> The winner was MHM User: <?php echo($firstName." ".$lastName) ?></p>
     <?php } else { ?>
      <p class="lead"><?=$title?> did not sell at £<?php echo(number_format($current_price, 2)) ?></p>
     <?php } ?>

<?php else: ?>
     Auction ends <?php echo(date_format($end_time, 'j M H:i') . $time_remaining) ?></p>
        <p class="lead">Conditon: <?php echo($condition) ?></p>
        <p class="lead">Current bid: £<?php echo(number_format($current_price, 2)) ?></p>
        <?php if ($winning_bid){?>
          <p class="lead"><?=$title?> The current winner is MHM User: <?php echo($firstName." ".$lastName) ?></p>
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

<?php $connection->close(); ?>

<?php include_once("footer.php")?>


<script> 
// JavaScript functions: addToWatchlist and removeFromWatchlist.

function addToWatchlist(button) {
  console.log("These print statements are helpful for debugging btw");

  // This performs an asynchronous call to a PHP function using POST method.
  // Sends item ID as an argument to that function.
  $.ajax('watchlist_funcs.php', {
    type: "POST",
    data: {functionname: 'add_to_watchlist', arguments: [<?php echo($auctionID);?>]},

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
    data: {functionname: 'remove_from_watchlist', arguments: [<?php echo($auctionID);?>]},

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