<?php
include_once 'navbar.php';
redirectHomeIfNotLoggedIn(["admin"]);

$cmd = sprintf('schtasks /Create /TN IDCardReminder /TR "C:\xampp\php\php.exe %s/reminderemail.php" /SC DAILY /ST %s /SD %s', $_SERVER["CONTEXT_DOCUMENT_ROOT"], date("h:i", strtotime("+1 minute")), date("d/m/Y"));

pclose(popen($cmd, "r"));
