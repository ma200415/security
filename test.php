<?php
include 'navbar.php';

try {
    $dbh = pdo();
    $sql = 'SELECT (select email from user where id=user) email, redemptionPlace, reservationDate, reservationTime FROM booking WHERE id = ?';
    $sth = $dbh->prepare($sql);
    $sth->execute([46]);
    $bookingEmail = $sth->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo $e->getMessage();
    exit;
}

try {
    // $sql = 'UPDATE booking SET status = ?, sdate = CURRENT_TIMESTAMP(), sby = ? WHERE id = ?';
    // $sth = $dbh->prepare($sql);
    // $sth->execute([$status, $_SESSION["userId"], $bookingId]);

    include_once 'sendemail.php';
    echo sendEmail(
        $bookingEmail["email"],
        $emailSubject,
        sprintf(
            "Your appointment:<br/>Date: %s %s<br/>Venues: %s",
            $bookingEmail["reservationDate"],
            $bookingEmail["reservationTime"],
            $bookingEmail["redemptionPlace"]
        )
    );

    // header("Location: " . $_SERVER["PHP_SELF"]);
    // exit;
} catch (PDOException $e) {
    echo $e->getMessage();
}
