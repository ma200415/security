<?php
if (isset($_SERVER["PHP_SELF"]) && $_SERVER["PHP_SELF"] == 'C:\xampp\htdocs\reminderemail.php') {
    include_once 'navbar.php';

    try {
        $dbh = pdo();
        $sql = 'SELECT id, (select email from user where id=user) email, redemptionPlace, reservationDate, reservationTime 
                FROM booking 
                WHERE DATEDIFF(reservationDate, CURRENT_DATE) <= 2 
                    AND status = "approve" 
                    AND reminderSent IS NOT TRUE';

        $sth = $dbh->prepare($sql);
        $sth->execute();
        $remindBookings = $sth->fetchAll();
    } catch (PDOException $e) {
        echo $e->getMessage();
        exit;
    }

    try {
        foreach ($remindBookings as $key => $reminder) {
            sendReminderEmail(
                $reminder,
                "ID Card Appointment Reminder"
            );
        }
    } catch (Exception $e) {
        echo $e->getMessage();
    }
}
