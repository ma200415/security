<head>
	<title>Register</title>
</head>

<?php include 'navbar.php'; ?>

<body>
	<div class="container-sm" style="padding: 30px;">
		<div class="col-sm-6" style="margin: auto;">
			<div class="card">
				<div class="card-body">
					<h3 class="card-title" style="text-align: center;">Register</h3>

					<p class="card-text">
						<?php
						if (isset($_POST["email"]) && isset($_POST["password"]) && isset($_POST["confirmPassword"])) {
							$errorMsg = array();

							if (!preg_match("(\w+@[a-zA-Z_]+?\.[a-zA-Z]{2,6})", $_POST["email"]))
								array_push($errorMsg, "Invalid Email!");

							// asd123ASD!@#
							if (!preg_match("((?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*?([^\w\s]|[_])).{8,})", $_POST["password"]))
								array_push($errorMsg, "Invalid Password!");

							if ($_POST["password"] != $_POST["confirmPassword"])
								array_push($errorMsg, "Password Not Match!");

							$dbh = pdo();
							$sql = 'SELECT * FROM user WHERE email = ?';
							$sth = $dbh->prepare($sql);
							$sth->execute([$_POST["email"]]);
							$users = $sth->fetchAll();

							if (sizeof($users) > 0)
								array_push($errorMsg, 'Email already exists!');

							if (sizeof($errorMsg) > 0) : ?>
					<div class="alert alert-danger" role="alert">
						<?php echo join("<br />", $errorMsg); ?>
					</div>
				<?php else : ?>
					<?php
								try {
									$dbh = pdo();
									$sql = 'INSERT INTO user (email, password, role) VALUES (?,?,?)';
									$sth = $dbh->prepare($sql);
									$sth->execute([$_POST["email"], hashPassword($_POST["password"]), "public"]);

									header('Location: login.php');
									exit;
								} catch (PDOException $e) {
									echo 'Insert failed: ' . $e->getMessage();
								}
					?>
				<?php endif ?>
			<?php
						}
			?>

			<form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="POST">
				<div class="mb-3">
					<label for="email" class="form-label">Email address</label>
					<input type="text" class="form-control" name="email" value="<?php echo isset($_POST["email"]) ?  $_POST["email"] : "" ?>" required>
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
			</p>
				</div>
			</div>
		</div>
	</div>
</body>

</html>

<?php redirectHomeIfLoggedIn(); ?>