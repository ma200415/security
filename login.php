<?php
include 'navbar.php';
redirectHomeIfLoggedIn();
?>

<head>
	<title>Login</title>
</head>

<body>
	<div class="container-sm" style="padding: 30px;">
		<div class="col-sm-6" style="margin: auto;">
			<div class="card">
				<div class="card-body">
					<h3 class="card-title" style="text-align: center;">Login</h3>

					<p class="card-text">
						<?php
						if (isset($_POST["email"]) && isset($_POST["password"])) {
							$dbh = pdo();
							$sql = 'SELECT * FROM user WHERE email = ?';
							$sth = $dbh->prepare($sql);
							$sth->execute([$_POST["email"]]);
							$users = $sth->fetchAll();

							if (sizeof($users) > 0) :
								$foundUser = $users[0];

								if (password_verify($_POST["password"], $foundUser["password"])) :
									$_SESSION["userId"] = $foundUser["id"];
									$_SESSION["email"] = $foundUser["email"];
									$_SESSION["role"] = $foundUser["role"];

									header('Location: /');
									exit;
						?>
								<?php
								else :
								?>
					<div class="alert alert-danger" role="alert">
						Incorrect password
					</div>
				<?php
								endif
				?>
			<?php
							else :
			?>
				<div class="alert alert-danger" role="alert">
					User Not Found
				</div>
			<?php
							endif
			?>
		<?php
						}
		?>

		<form id="form1" action="<?php echo $_SERVER["PHP_SELF"] ?>" method="POST">
			<div class="mb-3">
				<label for="email" class="form-label">Email address</label>
				<input type="text" class="form-control" name="email" value="<?php echo isset($_POST["email"]) ?  $_POST["email"] : "" ?>" required>
			</div>
			<div class="mb-3">
				<label for="password" class="form-label">Password</label>
				<input type="password" class="form-control" name="password" required>
			</div>
		</form>
		</p>

		<button type="submit" form="form1" class="btn btn-primary">Submit</button>
		&nbsp;
		<a href="register.php" class="link-primary">Register</a>
				</div>
			</div>
		</div>
	</div>
</body>

</html>