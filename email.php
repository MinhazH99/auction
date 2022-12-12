<?php

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;
    
    require("requirements/dbInformation.php");
    require 'PHPMailer/src/Exception.php';
    require 'PHPMailer/src/PHPMailer.php';
    require 'PHPMailer/src/SMTP.php';





function all_watch_lists($firstName, $email, $itemName){
    $mail = new PHPMailer(true);

    $mail -> isSMTP();
    $mail -> Host = 'smtp.gmail.com';
    $mail -> SMTPAuth = true;
    $mail -> Username = 'db.20223.mev@gmail.com';
    $mail -> Password = 'zaxwkqmjovaoscss';
    $mail -> SMTPSecure = 'ssl';
    $mail -> Port = 465;

    $mail -> setFrom('db.20223.mev@gmail.com', 'MHM Goods');
    
    $mail-> addAddress($email, $firstName);

    $mail -> isHTML(true);

    $mail -> Subject = 'Auction update: new bid on '.$itemName;
    $mail -> Body = "Hello ".$firstName.", <br><br> There's a new high bid on <a href='http://localhost/auction/listing.php?auction_id=".$itemName."'> auction</a> you are watching.<br><br>
    If you are still interested in this item, please log in to place a new bid. 
    <br><br>
    You are receiving this e-mail because ".$itemName." is in your watchlist, to stop recieving email notifications 
    please remove the item from your watchlist.
    <br><br>
    Kind regards,
    <br><br>
    MHM Goods";
    $mail -> send();

}

function buyer_email($itemName, $email, $firstName){

    $mail = new PHPMailer(true);

    $mail -> isSMTP();
    $mail -> Host = 'smtp.gmail.com';
    $mail -> SMTPAuth = true;
    $mail -> Username = 'db.20223.mev@gmail.com';
    $mail -> Password = 'zaxwkqmjovaoscss';
    $mail -> SMTPSecure = 'ssl';
    $mail -> Port = 465;

    $mail -> setFrom('db.20223.mev@gmail.com', 'MHM Goods');

    $mail->addAddress($email, $firstName);
    $mail->isHTML(true);
    $mail->Subject = "You won the ".$itemName;
    $mail->Body = "Hey ".$firstName." you have won the following item ".$itemName."<br><br> Please log in to pay
    for this item
    
    Kind regards,
    
    MHM Goods";
    
    $mail->send();
}

function seller_email($itemName, $email, $firstName) {
    $mail = new PHPMailer(true);

    $mail -> isSMTP();
    $mail -> Host = 'smtp.gmail.com';
    $mail -> SMTPAuth = true;
    $mail -> Username = 'db.20223.mev@gmail.com';
    $mail -> Password = 'zaxwkqmjovaoscss';
    $mail -> SMTPSecure = 'ssl';
    $mail -> Port = 465;

    $mail -> setFrom('db.20223.mev@gmail.com', 'MHM Goods');

    $mail -> addAddress($email, $firstName);

    $mail -> isHTML(true);
    $mail -> Subject = $itemName." has sold!";
    $mail -> Body = "item has sold";

    $mail->send();
}

function no_sale($email, $firstName){
    $mail = new PHPMailer(true);

    $mail -> isSMTP();
    $mail -> Host = 'smtp.gmail.com';
    $mail -> SMTPAuth = true;
    $mail -> Username = 'db.20223.mev@gmail.com';
    $mail -> Password = 'zaxwkqmjovaoscss';
    $mail -> SMTPSecure = 'ssl';
    $mail -> Port = 465;

    $mail -> setFrom('db.20223.mev@gmail.com', 'MHM Goods');

    $mail -> addAddress($email, $firstName);
    $mail -> isHTML(true);
    $mail -> Body = 'item did not sell';

    $mail->send();
}

?>
