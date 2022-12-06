<?php
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

    require 'PHPMailer/src/Exception.php';
    require 'PHPMailer/src/PHPMailer.php';
    require 'PHPMailer/src/SMTP.php';

    $mail = new PHPMailer(true);

    $mail -> isSMTP();
    $mail -> Host = 'smtp.gmail.com';
    $mail -> SMTPAuth = true;
    $mail -> Username = 'db.20223.mev@gmail.com';
    $mail -> Password = 'zaxwkqmjovaoscss';
    $mail -> SMTPSecure = 'ssl';
    $mail -> Port = 465;

    $mail -> setFrom('db.20223.mev@gmail.com'); //gmail

    $mail -> addAddress('exacter1990@gmail.com');

    $mail->isHTML(true);

    $mail->Subject = 'hello';
    $mail -> Body = 'world';

    echo "Hello world";

    $mail -> send();


    ?>
