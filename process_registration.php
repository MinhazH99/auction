<?php
// TODO: Extract $_POST variables, check they're OK, and attempt to create
// an account. Notify user of success/failure and redirect/give navigation 
// options.

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
    die("Oops... An unexpected error occurred.<br>Please <a href =\"register.php\">go back</a> and try again later.");

}

// sanitize the user input
$firstname = mysqli_real_escape_string($connection, $_POST['firstName']);
$lastname = mysqli_real_escape_string($connection, $_POST['lastName']);
$email = mysqli_real_escape_string($connection, $_POST['email']);

// hash the password for storage
$password = password_hash($_POST['password'], PASSWORD_DEFAULT);

// check if user email already in use
$email_exists = mysqli_query($connection, "SELECT email FROM users WHERE email = '".$email."';");

if (!$email_exists) {
    echo "There was a problem with registering your account.<br>Please <a href =\"register.php\">go back</a> and try submitting again.";
    die();
}

else if ( mysqli_num_rows($email_exists) > 0 ){    
    echo "Email already in use. Please <a href =\"register.php\">go back</a> and register with another email.";
}

else{   
    // if email not already in use, do the following code to create a new user account
    $query = "INSERT INTO users (email, first_name, last_name, password) VALUES ('$email', '$firstname', '$lastname', '$password');";

    // actually run the sql query
    $result = mysqli_query($connection,$query);

    // check if it worked
    if (!$result) {
        echo "There was a problem with registering your account.<br>Please <a href =\"register.php\">go back</a> and try submitting again.";
        exit();
    }
    else { 

        // get the user data from database to start a logged in session
        $get_user_data = mysqli_query($connection, "SELECT * FROM users WHERE email = '".$email."';");        

        if (!$get_user_data) {
            echo "An unexpected error occurred.<br>Please <a href =\"index.php\">go back</a> and try submitting again.";
            exit();
        }    

        $row = mysqli_fetch_assoc($get_user_data);


        session_start();
        $_SESSION['logged_in'] = true;
        $_SESSION['email'] = $row['email'];
        $_SESSION['user_id'] = $row['user_id'];       
        echo('<div class="text-center">Hi '.htmlspecialchars($firstname).','.'</br>Your account is successfully created!</br>You will be redirected to homepage shortly.</br>Why not go ahead and start browsing:)</div>');
        header("refresh:8;url=index.php");
    }

}


mysqli_close($connection);
?>
