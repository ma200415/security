<!doctype html>
<html lang="en">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Home</title>
	<link href="css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
	<script src="js/bootstrap.bundle.min.js"></script>

	<div class="container-sm" style="padding: 30px;">
		<div style="text-align: center;">
			<h1>
				<b>ID Card Booking System</b>
			</h1>
			<h2>
				<u>Home</u>
			</h2>
		</div>

		<div class="col-sm-6" style="margin: auto;">
			<?php session_start();
			if (isset($_SESSION["email"])) :
			?>
				<form action="index.php" method="POST">
					<button type="submit" class="btn btn-secondary" name="logout">Logout</button>
				</form>
			<?php else : ?>
				<a class="btn btn-success" href="login.php" role="button">Login</a>
			<?php endif	?>
		</div>
	</div>
</body>

</html>

<?php

if (isset($_POST["logout"])) {
	$_SESSION = array();
	session_destroy();

	header('Location: /');
	exit;
}

?>