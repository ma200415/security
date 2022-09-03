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
						$sql = 'SELECT engName, gender, idNo, photo, birthday, contact, reservationDate, iv FROM booking WHERE user = ?';
						$sth = $dbh->prepare($sql);
						$sth->execute([$_SESSION["userId"]]);
						$booking = $sth->fetch(PDO::FETCH_ASSOC);

						if ($booking) :
							list($engName, $gender, $idCardNo, $photo, $birthday, $contact) = decryptData([$booking["engName"], $booking["gender"], $booking["idNo"], $booking["photo"], $booking["birthday"], $booking["contact"]], $booking["iv"]);
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
						<a href="#" data-bs-toggle="modal" data-bs-target="#photoModal">
							<img src="data:image/png;base64,<?php echo $photo ?>" class="img-thumbnail" />
						</a>

						<div class="modal fade" id="photoModal" tabindex="-1" aria-hidden="true">
							<div class="modal-dialog modal-dialog-centered modal-xl">
								<div class="modal-content">
									<div class="modal-header">
										<h5 class="modal-title">Photo</h5>
										<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
									</div>
									<div class="modal-body">
										<img src="data:image/png;base64,<?php echo $photo ?>" class="img-fluid" />
									</div>
									<div class="modal-footer">
										<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
									</div>
								</div>
							</div>
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
								$uploadPhoto = $_FILES['recentPhoto'];

								if (!preg_match(regexEnglishName(), $engName)) {
									array_push($errorMsg, "Invalid Name!");
								}

								if (empty($_POST["genderRadioOptions"])) {
									array_push($errorMsg, "Invalid Gender!");
								}

								if (empty($uploadPhoto)) {
									array_push($errorMsg, "Photo is missing");
								} else {
									//Set max_allowed_packet=50M in my.ini
									if ($uploadPhoto['error'] != 0) {
										if ($uploadPhoto['error'] == 2) {
											array_push($errorMsg, "File size exceeds limit");
										} else {
											array_push($errorMsg, "Error Code: " . $uploadPhoto['error']);
										}
									} else if (!in_array($uploadPhoto['type'], ["image/jpeg", "image/png"])) {
										array_push($errorMsg, "Unsupported Media Type: " . $uploadPhoto['type']);
									}
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
									list($e_engName, $e_gender, $e_uploadPhoto, $e_idCardNo, $e_birthday, $e_contact, $iv) =
										encryptData([$engName, $_POST["genderRadioOptions"], encodeFile($uploadPhoto['tmp_name']), $idCardNo, $birthday, $contact]);

									try {
										$dbh = pdo();
										$sql = 'INSERT INTO booking (user, engName, gender, photo, idNo, birthday, contact, reservationDate, iv) VALUES (?,?,?,?,?,?,?,?,?)';
										$sth = $dbh->prepare($sql);
										$sth->execute([$_SESSION["userId"], $e_engName, $e_gender, $e_uploadPhoto, $e_idCardNo, $e_birthday, $e_contact, $reservationDate, $iv]);
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

					<form id="form1" action="<?php echo $_SERVER["PHP_SELF"] ?>" enctype="multipart/form-data" method="POST">
						<div class="form-floating mb-3">
							<input type="text" class="form-control" name="engName" placeholder="e.g. Chan Tai Man" value="<?php echo isset($_POST["engName"]) ? $_POST["engName"] : "" ?>" pattern="<?php echo regexEnglishName() ?>" required>
							<label for="engName">Name (English)</label>
							<div class="form-text">
								<ul>
									<li>
										e.g. Chan Tai Man
									</li>
								</ul>
							</div>
						</div>

						<div class="form-floating mb-3">
							<div class="form-check form-check-inline">
								<input class="form-check-input" type="radio" name="genderRadioOptions" id="maleRadio" value="M" <?php echo isset($_POST["genderRadioOptions"]) && $_POST["genderRadioOptions"] == "M" ? "checked" : ""; ?>>
								<label class="form-check-label" for="maleRadio">Male</label>
							</div>
							<div class="form-check form-check-inline">
								<input class="form-check-input" type="radio" name="genderRadioOptions" id="femaleRadio" value="F" <?php echo isset($_POST["genderRadioOptions"]) && $_POST["genderRadioOptions"] == "F" ? "checked" : ""; ?>>
								<label class="form-check-label" for="femaleRadio">Female</label>
							</div>
						</div>

						<div class="mb-3">
							<input type="hidden" name="MAX_FILE_SIZE" value="10485760" />
							<input class="form-control" type="file" name="recentPhoto" accept="image/png, image/jpeg" required>
							<div class="form-text">
								<ul>
									<li>
										Your recent photo (10MB maximum, JPG / PNG)
									</li>
								</ul>
							</div>
						</div>

						<div class="form-floating mb-3">
							<input type="text" class="form-control" name="idCardNo" placeholder="e.g. A123456(7)" value="<?php echo isset($_POST["idCardNo"]) ? $_POST["idCardNo"] : "" ?>" pattern="<?php echo regexIDCardNo() ?>" required>
							<label for="idCardNo">ID Card No.</label>
							<div class="form-text">
								<ul>
									<li>
										e.g. A123456(7)
									</li>
								</ul>
							</div>
						</div>

						<div class="form-floating mb-3">
							<input type="date" class="form-control" name="birthday" value="<?php echo isset($_POST["birthday"]) ? $_POST["birthday"] : "" ?>" max="<?php echo maxBirthday() ?>" min="<?php echo minBirthday() ?>" required>
							<label for="birthday">Birthday</label>
							<div class="form-text">
								<ul>
									<li>
										e.g. 01/01/1990
									</li>
								</ul>
							</div>
						</div>

						<div class="form-floating mb-3">
							<input type="tel" class="form-control" name="contact" placeholder="e.g. 12345678" value="<?php echo isset($_POST["contact"]) ? $_POST["contact"] : "" ?>" pattern="<?php echo regexContact() ?>" required>
							<label for="contact">Contact</label>
							<div class="form-text">
								<ul>
									<li>
										e.g. 12345678
									</li>
								</ul>
							</div>
						</div>

						<div class="form-floating">
							<input type="date" class="form-control" name="reservationDate" value="<?php echo isset($_POST["reservationDate"]) ? $_POST["reservationDate"] : "" ?>" max="<?php echo maxReservationDate() ?>" min="<?php echo minReservationDate() ?>" required>
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