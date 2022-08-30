<!doctype html>
<html lang="en">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Login</title>
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
				<u>Login</u>
			</h2>
		</div>

		<div class="col-sm-6" style="margin: auto;">
			<p>
			<div class="alert alert-danger" role="alert">
				<?php

				if (isset($_POST["email"]) && isset($_POST["password"])) {
					$dsn = 'mysql:dbname=id_card_booking;host=127.0.0.1';
					$user = 'root';
					$password = 'a@200415';

					$dbh = new PDO($dsn, $user, $password);

					$sql = 'SELECT * FROM user WHERE email = ? AND password = ?';
					$sth = $dbh->prepare($sql);
					$sth->execute(array($_POST["email"], hash('sha3-512', $_POST["password"])));
					$users = $sth->fetchAll();

					if (sizeof($users) > 0) {
						$foundUser = $users[0];

						session_start();

						$_SESSION["email"] = $foundUser["email"];
						$_SESSION["role"] = $foundUser["role"];

						header('Location: /');
						exit;
					} else
						echo "User Not Found";
				}

				?>
			</div>
			</p>

			<form action="login.php" method="POST">
				<div class="mb-3">
					<label for="email" class="form-label">Email address</label>
					<input type="text" class="form-control" name="email" required>
				</div>
				<div class="mb-3">
					<label for="password" class="form-label">Password</label>
					<input type="password" class="form-control" name="password" aria-describedby="passwordHelp" required>
				</div>
				<button type="submit" class="btn btn-primary">Submit</button>
				&nbsp;
				<a href="register.php" class="link-primary">Register</a>
			</form>
		</div>
	</div>
</body>

</html>