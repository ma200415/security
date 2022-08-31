<?php session_start(); ?>

<!doctype html>
<html lang="en">

<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<link href="css/bootstrap.min.css" rel="stylesheet">
<script src="js/bootstrap.bundle.min.js"></script>

<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
	<div class="container-sm">
		<a class="navbar-brand" href="/">ID Card Booking System</a>
		<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
			<span class="navbar-toggler-icon"></span>
		</button>
		<div class="collapse navbar-collapse" id="navbarSupportedContent">
			<ul class="navbar-nav me-auto mb-2 mb-lg-0">
				<li class="nav-item">
					<a class="nav-link active" href="/">Home</a>
				</li>
			</ul>
			<div class="d-flex" role="search">
				<?php if (isset($_SESSION["email"])) : ?>
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
	if (isset($_SESSION["email"])) {
		header('Location: /');
		exit;
	}
}

function hashPassword($rawPassword)
{
	return hash('sha3-512', $rawPassword);
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
?>