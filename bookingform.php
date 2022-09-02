<?php
include 'navbar.php';
redirectHomeIfNotLoggedIn();
?>

<head>
	<title>Booking Form</title>
</head>

<body>
	<div class="container-sm" style="padding: 30px;">
		<div class="col-sm-6" style="margin: auto;">
			<div class="card">
				<div class="card-body">
					<h3 class="card-title" style="text-align: center;">Booking Form</h3>

					<p class="card-text">
						<?php
						if (isset($_POST["booking"])) {
							$errorMsg = array();

							if (!preg_match(regexEnglishName(), $_POST["engName"])) {
								array_push($errorMsg, "Invalid name!");
							}

							if (!preg_match(regexIDCardNo(), $_POST["idCardNo"])) {
								array_push($errorMsg, "Invalid ID Card No.!");
							}

							if (!preg_match(regexDate(), $_POST["birthday"])) {
								array_push($errorMsg, "Invalid Birthday!");
							}

							if (!preg_match(regexContact(), $_POST["contact"])) {
								array_push($errorMsg, "Invalid contact!");
							}

							if (sizeof($errorMsg) < 1) {
								try {
									$dbh = pdo();
									$sql = 'INSERT INTO booking (user, engName, idNo, birthday, contact) VALUES (?,?,?,?,?)';
									$sth = $dbh->prepare($sql);
									$sth->execute([$_SESSION["userId"], $_POST["engName"], $_POST["idCardNo"], $_POST["birthday"], $_POST["contact"]]);
								} catch (PDOException $e) {
									array_push($errorMsg, $e->getMessage());
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

			<form id="form1" action="<?php echo $_SERVER["PHP_SELF"] ?>" method="POST">
				<div class="form-floating mb-3">
					<input type="text" class="form-control" name="engName" aria-describedby="engNameHelp" pattern="<?php echo regexEnglishName() ?>" required>
					<label for="engName">Name (English)</label>
					<div id="engNameHelp" class="form-text">
						e.g. Chan Tai Man
					</div>
				</div>

				<div class="form-floating mb-3">
					<input type="text" class="form-control" name="idCardNo" aria-describedby="idCardNoHelp" pattern="<?php echo regexIDCardNo() ?>" required>
					<label for="idCardNo">ID Card No.</label>
					<div id="idCardNoHelp" class="form-text">
						e.g. A123456(7)
					</div>
				</div>

				<div class="form-floating mb-3">
					<input type="date" class="form-control" name="birthday" aria-describedby="birthdayHelp" max="<?php echo maxBirthday() ?>" min="<?php echo minBirthday() ?>" required>
					<label for="birthday">Birthday</label>
					<div id="birthdayHelp" class="form-text">
						e.g. 01/01/1990
					</div>
				</div>

				<div class="form-floating">
					<input type="tel" class="form-control" name="contact" aria-describedby="contactHelp" pattern="<?php echo regexContact() ?>" required>
					<label for="contact">Contact</label>
					<div id="contactHelp" class="form-text">
						e.g. 12345678
					</div>
				</div>
			</form>
			</p>

			<div style="text-align: center;">
				<button type="submit" name="booking" style="width: 100%;" form="form1" class="btn btn-primary">Submit</button>
			</div>
				</div>
			</div>
		</div>
	</div>
</body>

</html>