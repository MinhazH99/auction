<?php
// TODO: Extract $_POST variables, check they're OK, and attempt to create
// an account. Notify user of success/failure and redirect/give navigation 
// options.

include_once("header.php");

if(empty($_POST)){
    header("Location: register.php");
    exit();    
}

// check if the fields exist or are empty.
if ( empty($_POST['firstName']) ||
    empty($_POST['lastName']) ||
    empty($_POST['email']) ||
    empty($_POST['password']) )
{
    echo "Missing form data. Please <a href =\"register.php\">go back</a> and resubmit your details.";
    die();
}


// check if email is valid and if not stop script and ask for resubmission

if ( filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL) === false ){

    echo "Invalid email. Please <a href =\"register.php\">go back</a> and resubmit with a valid email.";
    die();
}


// database connection variables
define("HOST","localhost" );
define("DBUSER","root" );
define("DBPASS","" );
define("DBNAME","auction" );

// Create connection
$connection = mysqli_connect(HOST, DBUSER, DBPASS, DBNAME);

// Check connection
if (mysqli_connect_errno()) {
    die("Oops... Error connecting to MySQL server: " . mysqli_error(). "\nPlease <a href =\"register.php\">go back</a> and try again later.");

}

// sanitize the user input
$firstname = mysqli_real_escape_string($connection, $_POST['firstName']);
$lastname = mysqli_real_escape_string($connection, $_POST['lastName']);
$email = mysqli_real_escape_string($connection, $_POST['email']);

// hash the password for storage
$password = password_hash($_POST['password'], PASSWORD_DEFAULT);

// construct the sql query as a variable
$query = "INSERT INTO users (email, first_name, last_name, password) VALUES ('$email', '$firstname', '$lastname', '$password');";


// actually run the sql query
$result = mysqli_query($connection,$query);

// check if it worked
if (!$result) {
    echo "There was a problem with registering your account: " .mysqli_error($connection)."<br>Please <a href =\"register.php\">go back</a> and try submitting again.";
    die();
}
else {
    echo "Hi ". htmlspecialchars($firstname) . "<br>Your account is successfully created! Why not go ahead and start browsing:)";
}

mysqli_close($connection);
?>
<?php include_once("footer.php")?>