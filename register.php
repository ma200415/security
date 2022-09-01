<?php
include 'navbar.php';
redirectHomeIfLoggedIn();
?>

<head>
	<title>Register</title>
</head>

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

							if (!preg_match(regexEmail(), $_POST["email"])) {
								array_push($errorMsg, "Invalid email!");
							}

							// asd123ASD!@#
							if (!preg_match(regexPassword(), $_POST["password"])) {
								array_push($errorMsg, "Invalid password!");
							}

							if ($_POST["password"] != $_POST["confirmPassword"]) {
								array_push($errorMsg, "Password not match!");
							}

							if (sizeof($errorMsg) < 1) {
								try {
									$dbh = pdo();
									$sql = 'INSERT INTO user (email, password, role) VALUES (?,?,?)';
									$sth = $dbh->prepare($sql);
									$sth->execute([$_POST["email"], password_hash($_POST["password"], PASSWORD_DEFAULT), "public"]);

									header('Location: login.php');
									exit;
								} catch (PDOException $e) {
									if ($e->errorInfo[1] == 1062) {
										array_push($errorMsg, "Email already exists!");
									} else {
										array_push($errorMsg, $e->getMessage());
									}
								}
							}
						?>
							<?php
							if (sizeof($errorMsg) > 0) :
							?>
					<div class="alert alert-danger" role="alert">
						<?php
								echo join("<br />", $errorMsg)
						?>
					</div>
				<?php
							endif
				?>
			<?php
						}
			?>

			<form action="<?php echo $_SERVER["PHP_SELF"] ?>" method="POST">
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