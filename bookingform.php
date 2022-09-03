<?php
include 'navbar.php';
redirectHomeIfNotLoggedIn(["admin", "public"]);
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
						$dbh = pdo();
						$sql = 'SELECT engName, gender, idNo, birthday, contact, reservationDate, iv FROM booking WHERE user = ?';
						$sth = $dbh->prepare($sql);
						$sth->execute([$_SESSION["userId"]]);
						$booking = $sth->fetch(PDO::FETCH_ASSOC);

						if ($booking) :
							list($engName, $gender, $idCardNo, $birthday, $contact) = decryptData([$booking["engName"], $booking["gender"], $booking["idNo"], $booking["birthday"], $booking["contact"]], $booking["iv"]);
						?>
					<div class="alert alert-primary" role="alert">
						Submission History
					</div>

					<div class="form-floating mb-3">
						<input type="text" class="form-control" value="<?php echo $engName ?>" readonly>
						<label for="engName">Name (English)</label>
					</div>

					<div class="form-floating mb-3">
						<div class="form-check form-check-inline">
							<input class="form-check-input" type="radio" <?php echo $gender == "M" ? "checked" : "disabled"; ?>>
							<label class="form-check-label">Male</label>
						</div>
						<div class="form-check form-check-inline">
							<input class="form-check-input" type="radio" <?php echo $gender == "F" ? "checked" : "disabled"; ?>>
							<label class="form-check-label">Female</label>
						</div>
					</div>

					<div class="form-floating mb-3">
						<input type="text" class="form-control" value="<?php echo $idCardNo ?>" readonly>
						<label>ID Card No.</label>
					</div>

					<div class="form-floating mb-3">
						<input type="date" class="form-control" value="<?php echo $birthday ?>" readonly>
						<label>Birthday</label>
					</div>

					<div class="form-floating mb-3">
						<input type="tel" class="form-control" value="<?php echo $contact ?>" readonly>
						<label>Contact</label>
					</div>

					<div class="form-floating">
						<input type="date" class="form-control" value="<?php echo $booking["reservationDate"] ?>" readonly>
						<label for="reservationDate">Reservation Date</label>
					</div>
				<?php
						else :
				?>
					<?php
							if (isset($_POST["booking"])) {
								$errorMsg = array();

								$engName = $_POST["engName"];
								$idCardNo = $_POST["idCardNo"];
								$birthday = $_POST["birthday"];
								$contact = $_POST["contact"];
								$reservationDate = $_POST["reservationDate"];
								$gender = $_POST["genderRadioOptions"];

								if (!preg_match(regexEnglishName(), $engName)) {
									array_push($errorMsg, "Invalid Name!");
								}

								if (empty($gender)) {
									array_push($errorMsg, "Invalid Gender!");
								}

								if (!preg_match(regexIDCardNo(), $idCardNo)) {
									array_push($errorMsg, "Invalid ID Card No.!");
								}

								if (!preg_match(regexDate(), $birthday) || $birthday > maxBirthday() || $birthday < minBirthday()) {
									array_push($errorMsg, "Invalid Birthday!");
								}

								if (!preg_match(regexContact(), $contact)) {
									array_push($errorMsg, "Invalid Contact!");
								}

								if (!preg_match(regexDate(), $reservationDate) || $reservationDate > maxReservationDate() || $reservationDate < minReservationDate()) {
									array_push($errorMsg, "Invalid Reservation Date!");
								}

								if (sizeof($errorMsg) < 1) {
									list($e_engName, $e_gender, $e_idCardNo, $e_birthday, $e_contact, $iv) = encryptData([$engName, $gender, $idCardNo, $birthday, $contact]);

									try {
										$dbh = pdo();
										$sql = 'INSERT INTO booking (user, engName, gender, idNo, birthday, contact, reservationDate, iv) VALUES (?,?,?,?,?,?,?,?)';
										$sth = $dbh->prepare($sql);
										$sth->execute([$_SESSION["userId"], $e_engName, $e_gender, $e_idCardNo, $e_birthday, $e_contact, $reservationDate, $iv]);
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
								else :
									header("Location: " . $_SERVER["PHP_SELF"]);
									exit;
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
								<ul>
									<li>
										e.g. Chan Tai Man
									</li>
								</ul>
							</div>
						</div>

						<div class="form-floating mb-3">
							<div class="form-check form-check-inline">
								<input class="form-check-input" type="radio" name="genderRadioOptions" id="maleRadio" value="M">
								<label class="form-check-label" for="maleRadio">Male</label>
							</div>
							<div class="form-check form-check-inline">
								<input class="form-check-input" type="radio" name="genderRadioOptions" id="femaleRadio" value="F">
								<label class="form-check-label" for="femaleRadio">Female</label>
							</div>
						</div>

						<div class="form-floating mb-3">
							<input type="text" class="form-control" name="idCardNo" aria-describedby="idCardNoHelp" pattern="<?php echo regexIDCardNo() ?>" required>
							<label for="idCardNo">ID Card No.</label>
							<div id="idCardNoHelp" class="form-text">
								<ul>
									<li>
										e.g. A123456(7)
									</li>
								</ul>
							</div>
						</div>

						<div class="form-floating mb-3">
							<input type="date" class="form-control" name="birthday" aria-describedby="birthdayHelp" max="<?php echo maxBirthday() ?>" min="<?php echo minBirthday() ?>" required>
							<label for="birthday">Birthday</label>
							<div id="birthdayHelp" class="form-text">
								<ul>
									<li>
										e.g. 01/01/1990
									</li>
								</ul>
							</div>
						</div>

						<div class="form-floating mb-3">
							<input type="tel" class="form-control" name="contact" aria-describedby="contactHelp" pattern="<?php echo regexContact() ?>" required>
							<label for="contact">Contact</label>
							<div id="contactHelp" class="form-text">
								<ul>
									<li>
										e.g. 12345678
									</li>
								</ul>
							</div>
						</div>

						<div class="form-floating">
							<input type="date" class="form-control" name="reservationDate" aria-describedby="reservationDateHelp" max="<?php echo maxReservationDate() ?>" min="<?php echo minReservationDate() ?>" required>
							<label for="reservationDate">Reservation Date</label>
						</div>
					</form>
					</p>

					<div style="text-align: center;">
						<button type="submit" name="booking" style="width: 100%;" form="form1" class="btn btn-primary">Submit</button>
					</div>
				<?php
						endif
				?>
				</div>
			</div>
		</div>
	</div>
</body>

</html>