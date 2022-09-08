<?php session_start(); ?>

<!doctype html>
<html lang="en">

<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<link href="css/bootstrap.min.css" rel="stylesheet">
<script src="js/bootstrap.bundle.min.js"></script>

<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
	<div class="container-sm">
		<a class="navbar-brand" href="/">ID Card Management System</a>
		<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
			<span class="navbar-toggler-icon"></span>
		</button>
		<div class="collapse navbar-collapse" id="navbarSupportedContent">
			<ul class="navbar-nav me-auto mb-2 mb-lg-0">
				<li class="nav-item">
					<a class="nav-link <?php echo $_SERVER["PHP_SELF"] == "/index.php" ? "active" : "" ?>" href="/">Home</a>
				</li>

				<?php if (isLoggedIn()) : ?>
					<li class="nav-item">
						<a class="nav-link <?php echo $_SERVER["PHP_SELF"] == "/bookingform.php" ? "active" : "" ?>" href="bookingform.php">Reservation</a>
					</li>
				<?php endif ?>

				<?php if (checkPermissions(["admin"])) : ?>
					<li class="nav-item">
						<a class="nav-link <?php echo $_SERVER["PHP_SELF"] == "/bookings.php" ? "active" : "" ?>" href="bookings.php">Booking Records</a>
					</li>
				<?php endif ?>
			</ul>

			<div class="d-flex" role="search">
				<?php if (isLoggedIn()) : ?>
					<span class="navbar-text">
						<span class="badge rounded-pill text-bg-light" style="font-size: 14px;"><?php echo $_SESSION["email"] ?></span>
					</span>
					&nbsp;
					<form action="index.php" method="POST">
						<button type="submit" class="btn btn-outline-light" name="logout">Logout</button>
					</form>
				<?php else : ?>
					<a class="btn btn-outline-light" href="login.php" role="button">Login</a>
					&nbsp;
					<a class="btn btn-outline-light" href="register.php" role="button">Register</a>
				<?php endif	?>
			</div>
		</div>
	</div>
</nav>

<?php
function redirectHomeIfLoggedIn()
{
	if (isLoggedIn()) {
		header('Location: /');
		exit;
	}
}

function redirectHomeIfNotLoggedIn(array $allowedRoles)
{
	if (!isLoggedIn() || !in_array($_SESSION["role"], $allowedRoles)) {
		echo '<div class="alert alert-danger" role="alert" style="text-align: center;">';
		echo "You don't have permission to access. Redirect in 5 seconds...";
		echo '</div>';

		header('Refresh:5; url=login.php');
		exit;
	}
}

function checkPermissions(array $allowedRoles)
{
	return isset($_SESSION["role"]) && in_array($_SESSION["role"], $allowedRoles);
}

function isLoggedIn()
{
	return isset($_SESSION["email"]);
}

function pdo()
{
	$dsn = 'mysql:dbname=id_card_booking;host=127.0.0.1';
	$user = 'root';
	$password = 'a@200415';

	$dbh = new PDO($dsn, $user, $password);
	$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

	return $dbh;
}

function regexEnglishName()
{
	return "(^[a-zA-Z\s]*)";
}

function regexChineseName()
{
	return "/\p{Han}+/u";
}

function regexIDCardNo()
{
	return "(^[A-Z]{1}[0-9]{6}\([0-9]\))";
}

function regexContact()
{
	return "([0-9]{8})";
}

function regexEmail()
{
	return "(\w+@[a-zA-Z_]+?\.[a-zA-Z]{2,6})";
}

function regexPassword()
{
	return "((?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*?([^\w\s]|[_])).{8,})";
}

function regexDate()
{
	return "([0-9]{4}-[0-9]{2}-[0-9]{2})";
}

function regexTime()
{
	return "(^(([0-1]{0,1}[0-9])|(2[0-3])):[0-5]{0,1}[0-9]$)";
}

function minBirthday()
{
	return date("Y-m-d", strtotime("-150 Years"));
}

function maxBirthday()
{
	return date("Y-m-d");
}

function minReservationDate()
{
	return date("Y-m-d", strtotime("+7 Day"));
}

function maxReservationDate()
{
	return date("Y-m-d", strtotime("+3 Months"));
}

function minReservationTime()
{
	return "09:00";
}

function maxReservationTime()
{
	return "17:00";
}

function encryptData(array $plaintexts, $iv = null)
{
	$cipherParam = cipherParams();

	$key = $cipherParam["key"];
	$cipher = $cipherParam["cipher"];

	if (in_array($cipher, openssl_get_cipher_methods())) {
		$payload = array();

		$ivlen = openssl_cipher_iv_length($cipher);
		if ($iv == null) $iv = openssl_random_pseudo_bytes($ivlen);

		foreach ($plaintexts as $plaintext) {
			array_push($payload, openssl_encrypt(trim($plaintext), $cipher, $key, 0, $iv));
		}

		array_push($payload, base64_encode($iv));

		return $payload;
	} else {
		return false;
	}
}

function decryptData(array $ciphertexts, $iv)
{
	$cipherParam = cipherParams();

	$key = $cipherParam["key"];
	$cipher = $cipherParam["cipher"];

	if (in_array($cipher, openssl_get_cipher_methods())) {
		$payload = array();

		foreach ($ciphertexts as $ciphertext) {
			array_push($payload, openssl_decrypt($ciphertext, $cipher, $key, 0, base64_decode($iv)));
		}

		return $payload;
	} else {
		return false;
	}
}

function cipherParams()
{
	$cipherParam = array();
	$dbh = pdo();

	foreach ($dbh->query('SELECT value FROM misc WHERE name = "cipherKey"') as $row) {
		$cipherParam["key"] =  $row['value'];
		break;
	}

	foreach ($dbh->query('SELECT value FROM misc WHERE name = "cipher"') as $row) {
		$cipherParam["cipher"] =  $row['value'];
		break;
	}

	return $cipherParam;
}

function encodeFile($fileTmpName)
{
	return base64_encode(file_get_contents($fileTmpName));
}

function sendReminderEmail($booking, $subject)
{
	include_once 'sendemail.php';

	list($success, $result) = sendEmail(
		$booking["email"],
		$subject,
		sprintf(
			"Your appointment:<br/>Date: %s %s<br/>Venues: %s",
			$booking["reservationDate"],
			$booking["reservationTime"],
			$booking["redemptionPlace"]
		)
	);

	$sql = 'UPDATE booking SET reminderSent = ?, reminderRemark = ? WHERE id = ?';
	$dbh = pdo();
	$sth = $dbh->prepare($sql);

	$sth->execute([$success, $result, $booking["id"]]);
}
?>