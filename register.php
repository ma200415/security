<!doctype html>
<html lang="en">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Register</title>
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
				<u>Register</u>
			</h2>
		</div>

		<div class="col-sm-6" style="margin: auto;">
			<p>
			<div class="alert alert-danger" role="alert">
				<?php

				if (isset($_POST["email"]) && isset($_POST["password"]) && isset($_POST["confirmPassword"])) {
					$errorMsg = array();

					if (!preg_match("(\w+@[a-zA-Z_]+?\.[a-zA-Z]{2,6})", $_POST["email"]))
						array_push($errorMsg, "Invalid Email");

					// asd123ASD!@#
					if (!preg_match("((?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*?([^\w\s]|[_])).{8,})", $_POST["password"]))
						array_push($errorMsg, "Invalid Password");

					if ($_POST["password"] != $_POST["confirmPassword"])
						array_push($errorMsg, "Password Not Match");

					if (sizeof($errorMsg) > 0)
						echo join("<br />", $errorMsg);
					else {
						header('Location: login.php');
						exit;
					}
				}

				?>

				<?php

				// session_start();

				// echo $_SESSION["email"];
				/* Connect to an ODBC database using an alias */
				// $dsn = 'mysql:dbname=id_card_booking;host=127.0.0.1';
				// $user = 'root';
				// $password = 'a@200415';

				// $dbh = new PDO($dsn, $user, $password);

				// /* Execute a prepared statement by passing an array of values */
				// $sql = 'SELECT * FROM user';
				// $sth = $dbh->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
				// $sth->execute();
				// $red = $sth->fetchAll();
				// /* Array keys can be prefixed with colons ":" too (optional) */
				// // $sth->execute(array(':calories' => 175, ':colour' => 'yellow'));
				// // $yellow = $sth->fetchAll();

				// // echo hash('sha3-512', 'The quick brown fox jumped over the lazy dog.');
				// print_r($red);
				?>
			</div>
			</p>

			<form action="register.php" method="POST">
				<div class="mb-3">
					<label for="email" class="form-label">Email address</label>
					<input type="text" class="form-control" name="email" required>
				</div>
				<div class="mb-3">
					<label for="password" class="form-label">Password</label>
					<input type="password" class="form-control" name="password" aria-describedby="passwordHelp" required>
					<div id="passwordHelp" class="form-text">
						<ul>
							<li>
								at least 8 characters
							</li>
							<li>
								at least 1 digit
							</li>
							<li>
								at least 1 lowercase letter
							</li>
							<li>
								at least 1 uppercase letter
							</li>
							<li>
								at least 1 special character
							</li>
						</ul>
					</div>
				</div>
				<div class="mb-3">
					<label for="confirmPassword" class="form-label">Confirm Password</label>
					<input type="password" class="form-control" name="confirmPassword" required>
				</div>
				<button type="submit" class="btn btn-primary">Submit</button>
				&nbsp;
				<a href="login.php" class="link-primary">Login</a>
			</form>
		</div>
	</div>
</body>

</html>