<?php include_once("header.php")?>

<div class="container my-5">

<?php

// This function takes the form data and adds the new auction to the database.

/* TODO #1: Connect to MySQL database (perhaps by requiring a file that
            already does this). */
$host = "localhost";
$dbname = "auction";
$username = "root";
$password = "";

$connection = mysqli_connect($host, $username, $password, $dbname);

if (mysqli_connect_errno()) {
    die("Connection error: " .mysqli_connect_error());
}
            
/* TODO #2: Extract form data into variables. Because the form was a 'post'
            form, its data can be accessed via $POST['auctionTitle'], 
            $POST['auctionDetails'], etc. Perform checking on the data to
            make sure it can be inserted into the database. If there is an
            issue, give some semi-helpful feedback to user. */

$user = $_SESSION['user_id'];
$item_name = $_POST["item_name"];
$item_desc = $_POST["item_desc"];
$item_condition = $_POST["item_condition"];
$category_name = $_POST["category_name"];
$starting_price = filter_input(INPUT_POST,"starting_price",FILTER_VALIDATE_FLOAT);
$reserve_price = filter_input(INPUT_POST,"starting_price",FILTER_VALIDATE_FLOAT);
$expirationDate = date("Y-m-d H:i:s", strtotime($_POST["expirationDate"]));
$auction_status = "Open";

/*var_dump($item_name,$item_desc,$category_name,$starting_price,$reserve_price,$expirationDate,$auction_status, $user); */
      

/* TODO #3: If everything looks good, make the appropriate call to insert
            data into the database. */

$query = "INSERT INTO auctions (item_name,item_desc,user_id,item_condition, category_id, starting_price, reserve_price, expirationDate, auction_status) VALUES ('$item_name', '$item_desc', $user, '$item_condition', (SELECT category_id FROM auction.categories WHERE category_name = '$category_name'), $starting_price, $reserve_price, '$expirationDate','$auction_status')";


/*$query3 = "INSERT INTO auctions (starting_price, reserve_price,expirationDate, auction_status,item_id) VALUES ($starting_price, $reserve_price, '$expirationDate','$auction_status', (SELECT item_id FROM auction.items WHERE user_id = '$user' AND item_name = '$item_name' AND item_desc = '$item_desc'))"; */
          
$result = mysqli_query($connection,$query) or die("Error: items table");

/* $result3 = mysqli_query($connection,$query3) or die("Error: auction table"); */


// If all is successful, let user know.
echo('<div class="text-center">Auction successfully created! <a href="http://localhost/auction/mylistings.php">View your new listing.</a></div>');

mysqli_close($connection);

?>

</div>


<?php include_once("footer.php")?>