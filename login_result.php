<?php

// TODO: Extract $_POST variables, check they're OK, and attempt to login.
// Notify user of success/failure and redirect/give navigation options.

// For now, I will just set session variables and redirect.

//die ('this works');

if( !isset($_POST['login-submit']) ){
    header("Location: index.php");    
}

else{
     // check if the fields exists
    if ( empty($_POST['email']) || empty($_POST['password']) )
    {
        echo('<div class="text-center">Missing login information. Please try again</div>');
        // Redirect to index after 5 seconds
        header("refresh:5;url=index.php");        
    }

    // check if email is valid and if not stop script and ask for resubmission
    else if ( filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL) === false ){
        echo('<div class="text-center">Invalid email. Please try with a valid email again.</div>');
                // Redirect to index after 5 seconds
                header("refresh:5;url=index.php");        
    }

    else {
        // database connection variables
        define("HOST","localhost" );
        define("DBUSER","root" );
        define("DBPASS","" );
        define("DBNAME","auction" );

        // Create connection
        $connection = mysqli_connect(HOST, DBUSER, DBPASS, DBNAME);

        // Check connection
        if (mysqli_connect_errno()) {
            echo "There was a problem with registering your account.<br>Please <a href =\"register.php\">go back</a> and try submitting again.";
            // Redirect to index after 5 seconds
            header("refresh:5;url=index.php");           

        }

        //sanitise the email variable 
        $email = mysqli_real_escape_string($connection, $_POST['email']);

        // // hash the password for storage
        // $pwd_hashed = password_hash($_POST['password'], PASSWORD_DEFAULT);

        // get the data for this email from the database
        $get_user_data = mysqli_query($connection, "SELECT * FROM users WHERE email = '".$email."';");

        if( mysqli_num_rows($get_user_data) == null ){
            echo "A user with that email address doesn't exist. <br>Please <a href =\"index.php\">go back</a> and try submitting again.";
                       
        }

        else if (!$get_user_data) {
            echo "An unexpected error occurred. <br>Please <a href =\"index.php\">go back</a> and try submitting again.";
            exit();
        }      
                
        else{   
            //fetch the user data in an associative array and check if passwords match
            $row = mysqli_fetch_assoc($get_user_data);
            $password_valid = password_verify($_POST['password'], $row['password']);


            //initiate session if passwords match
            
            if( $password_valid ) {
                session_start();
                $_SESSION['logged_in'] = true;
                $_SESSION['email'] = $row['email'];
                $_SESSION['user_id'] = $row['user_id'];
                // $_SESSION['username'] = "test";
                // $_SESSION['account_type'] = "buyer";                       
                            
                echo('<div class="text-center">You are now logged in! You will be redirected to homepage shortly.</div>');
                // Redirect to index after 5 seconds
                header("refresh:5;url=index.php");


            }

            else {
                echo('<div class="text-center">Wrong login information. Please try again.<br>You\'re being redirected to homepage.</div>');
                // Redirect to index after 5 seconds
                header("refresh:5;url=index.php");                
            }
           

        
        }

        mysqli_close($connection);
    }

    

}




?>