<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

function sendEmail($recipient, $subject, $body)
{
    $smtpParam = array();
    $dbh = pdo();

    foreach ($dbh->query('SELECT value FROM misc WHERE name = "smtpusername"') as $row) {
        $smtpParam["smtpusername"] =  $row['value'];
        break;
    }

    foreach ($dbh->query('SELECT value FROM misc WHERE name = "smtppassword"') as $row) {
        $smtpParam["smtppassword"] =  $row['value'];
        break;
    }

    $mail = new PHPMailer(true);

    try {
        // $mail->SMTPDebug = SMTP::DEBUG_SERVER;
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = $smtpParam["smtpusername"];
        $mail->Password   = $smtpParam["smtppassword"];
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port       = 465;

        $mail->addAddress($recipient);

        $mail->isHTML(true);
        $mail->Subject =  $subject;
        $mail->Body    = $body;

        $mail->send();
        return 'Message has been sent';
    } catch (Exception $e) {
        return "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}
