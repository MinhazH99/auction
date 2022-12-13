<?php include_once("header.php")?>
<?php require("requirements/dbInformation.php")?> 

<div class="container">

<!-- Create review form -->
<div style="max-width: 800px; margin: 10px auto">
  <h2 class="my-3">Place Review for an Order</h2>
  <div class="card">
    <div class="card-body">
      <form method="post" action="reviews_result.php">
        <div class="form-group row">
          <label for="sold_item" class="col-sm-2 col-form-label text-right">Please select item</label>
          <div class="col-sm-10">
            <select class="form-control" id="sold_item" name = "sold_item">
              <option selected>Choose...</option>
              <?php
                $user = $_SESSION['user_id'];
                $current_date = "CURRENT_TIMESTAMP";

                $query = "SELECT item_name FROM bids
                JOIN auctions ON auctions.auction_id = bids.auction_id
                WHERE $current_date > expirationDate
                AND bid_price = starting_price
                AND bids.user_id = $user
                GROUP BY auctions.auction_id";
                
                $query_result = mysqli_query($connection,$query);
                
                $item_list = array();
                
                while ($row = mysqli_fetch_array($query_result))
                {
                  
                    $item_list[] = $row['item_name'];
                    echo '<option value="'.$row['item_name'].'" >'.$row["item_name"].'</option>'; 
                
                }
                echo '</select>';
                mysqli_close($connection);
          ?>
        </div>
        <div class="form-group row">
          <label for="review" class="col-sm-2 col-form-label text-right">Please select rating</label>
          <div class="col-sm-10">
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="review_stars" id="review" value= 1 checked>
              <label class="form-check-label" for="review">1</label>
              </div>
              <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="review_stars" id="review" value=2 >
                <label class="form-check-label" for="review">2</label>
              </div>
              <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="review_stars" id="review" value=3 >
                <label class="form-check-label" for="review">3</label>
              </div>
              <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="review_stars" id="review" value=4 >
                <label class="form-check-label" for="review">4</label>
              </div>
              <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="review_stars" id="review" value=5 >
                <label class="form-check-label" for="review">5</label>
              </div>
            <small id="categoryHelp" class="form-text text-muted"><span class="text-danger">* Required.</span> Select a category for this item.</small>
          </div>
          <div class="form-group row">
          <label for="review_details" class="col-sm-2 col-form-label text-right">Additional comments</label>
          <div class="col-sm-10">
            <textarea class="form-control" id="reviews_details" rows="4" name = "reviews_details"></textarea>
            <small id="detailsHelp" class="form-text text-muted">This section is for any additional comments on the item you purchased.</small>
          </div>
        </div>
        <button type="submit" class="btn btn-primary form-control">Submit Review</button>
      </form>
    </div>
  </div>
</div>

</div>




<?php include_once("footer.php")?>