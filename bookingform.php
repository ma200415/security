<?php
include_once 'navbar.php';
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
						$sql = 'SELECT * FROM booking WHERE user = ?';
						$sth = $dbh->prepare($sql);
						$sth->execute([$_SESSION["userId"]]);
						$booking = $sth->fetch(PDO::FETCH_ASSOC);

						if ($booking) :
							list($engName, $chiName, $gender, $occupation, $idCardNo, $photo, $birthday, $birthPlace, $contact, $address) =
								decryptData([
									$booking["engName"], $booking["chiName"], $booking["gender"], $booking["occupation"], $booking["idNo"], $booking["photo"],
									$booking["birthday"], $booking["birthPlace"], $booking["contact"], $booking["address"]
								], $booking["iv"]);

							switch ($booking["status"]):
								case "approve":
						?>
					<div class="alert alert-success" role="alert">
						Confirmed
					</div>
				<?php
									break;
								case "reject":
				?>
					<div class="alert alert-danger" role="alert">
						Rejected
					</div>
				<?php
									break;
								default:
				?>
					<div class="alert alert-primary" role="alert">
						Submission History
					</div>
			<?php
									break;
							endswitch; ?>
			<div class="form-floating mb-3">
				<input type="text" class="form-control" value="<?php echo $engName ?>" readonly>
				<label for="engName">Name (English)</label>
			</div>

			<div class="form-floating mb-3">
				<input type="text" class="form-control" value="<?php echo $chiName ?>" readonly>
				<label for="chiName">Name (Chinese)</label>
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
				<input type="text" class="form-control" value="<?php echo $occupation ?>" readonly>
				<label>Occupation</label>
			</div>

			<div class="form-floating mb-3">
				<input type="text" class="form-control" value="<?php echo $idCardNo ?>" readonly>
				<label>ID Card No.</label>
			</div>

			<div class="form-floating mb-3">
				<input type="date" class="form-control" value="<?php echo $birthday ?>" readonly>
				<label>Date of Birth</label>
			</div>

			<div class="form-floating mb-3">
				<input type="text" class="form-control" value="<?php echo $birthPlace ?>" readonly>
				<label>Place of Birth</label>
			</div>

			<div class="form-floating mb-3">
				<input type="tel" class="form-control" value="<?php echo $contact ?>" readonly>
				<label>Contact</label>
			</div>

			<div class="form-floating mb-3">
				<textarea class="form-control" style="height: 120px;" rows="4" readonly><?php echo $address ?></textarea>
				<label>Address</label>
			</div>

			<div class="row g-3 mb-3">
				<div class="form-floating col">
					<input type="date" class="form-control" value="<?php echo $booking["reservationDate"] ?>" readonly>
					<label for="reservationDate">Reservation Date</label>
				</div>

				<div class="form-floating col">
					<input type="time" class="form-control" value="<?php echo $booking["reservationTime"] ?>" readonly>
					<label for="reservationTime">Time Slot</label>
				</div>
			</div>

			<div class="form-floating">
				<input type="text" class="form-control" value="<?php echo $booking["redemptionPlace"] ?>" readonly>
				<label>Redemption Place</label>
			</div>
		<?php
						else :
		?>
			<?php
							if (isset($_POST["booking"])) {
								$errorMsg = array();

								$engName = trim($_POST["engName"]);
								$chiName = trim($_POST["chiName"]);
								$idCardNo = trim($_POST["idCardNo"]);
								$birthday = $_POST["birthday"];
								$birthPlace = trim($_POST["birthPlace"]);
								$address = trim($_POST["address"]);
								$contact = trim($_POST["contact"]);
								$occupation = trim($_POST["occupation"]);
								$reservationDate = $_POST["reservationDate"];
								$reservationTime = $_POST["reservationTime"];
								$redemptionPlace = trim($_POST["redemptionPlace"]);
								$uploadPhoto = $_FILES['recentPhoto'];

								if (!preg_match(regexEnglishName(), $engName)) {
									array_push($errorMsg, "Invalid English Name!");
								}

								if (!preg_match(regexChineseName(), $chiName)) {
									array_push($errorMsg, "Invalid Chinese Name!");
								}

								if (empty($_POST["genderRadioOptions"])) {
									array_push($errorMsg, "Please select Gender!");
								}

								if (empty($occupation)) {
									array_push($errorMsg, "Please input Occupation!");
								}

								if (empty($uploadPhoto)) {
									array_push($errorMsg, "Photo is missing!");
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

								if (empty($birthPlace)) {
									array_push($errorMsg, "Please input Place of Birth!");
								}

								if (empty($address)) {
									array_push($errorMsg, "Please input Address!");
								}

								if (!preg_match(regexContact(), $contact)) {
									array_push($errorMsg, "Invalid Contact!");
								}

								if (!preg_match(regexDate(), $reservationDate) || $reservationDate > maxReservationDate() || $reservationDate < minReservationDate()) {
									array_push($errorMsg, "Invalid Reservation Date!");
								}

								if (!preg_match(regexTime(), $reservationTime) || strtotime($reservationTime) > strtotime(maxReservationTime()) || strtotime($reservationTime) < strtotime(minReservationTime())) {
									array_push($errorMsg, "Invalid Reservation Time!");
								}

								if (empty($redemptionPlace)) {
									array_push($errorMsg, "Please input Redemption Place!");
								}

								if (sizeof($errorMsg) < 1) {
									list($e_engName, $e_chiName, $e_gender, $e_uploadPhoto, $e_idCardNo, $e_birthday, $e_birthPlace, $e_address, $e_contact, $e_occupation, $iv) =
										encryptData([
											$engName, $chiName, $_POST["genderRadioOptions"], encodeFile($uploadPhoto['tmp_name']),
											$idCardNo, $birthday, $birthPlace, $address, $contact, $occupation
										]);

									try {
										$dbh = pdo();
										$sql = 'INSERT INTO booking (user, engName, chiName, gender, photo, idNo, birthday, birthPlace, contact, address, occupation, reservationDate, reservationTime, redemptionPlace, iv) 
										VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)';
										$sth = $dbh->prepare($sql);
										$sth->execute([$_SESSION["userId"], $e_engName, $e_chiName, $e_gender, $e_uploadPhoto, $e_idCardNo, $e_birthday, $e_birthPlace, $e_contact, $e_address, $e_occupation, $reservationDate, $reservationTime, $redemptionPlace, $iv]);
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
							} else {
								$_POST["engName"] = "Chan Tai Man";
								$_POST["chiName"] = "陳大文";
								$_POST["idCardNo"] = "A123456(7)";
								$_POST["birthday"] = "2000-05-20";
								$_POST["birthPlace"] = "Hong Kong";
								$_POST["address"] = "5/F, 52 Lockhart Road, WAN CHAI, HONG KONG";
								$_POST["contact"] = "12345678";
								$_POST["genderRadioOptions"] = "M";
								$_POST["occupation"] = "Software Engineer";
								$_POST["reservationDate"] = "2022-09-10";
								$_POST["reservationTime"] = "14:30";
								$_POST["redemptionPlace"] = "Tsuen Wan Smart Identity Card Replacement Centre";
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
					<input type="text" class="form-control" name="chiName" placeholder="e.g. 陳大文" value="<?php echo isset($_POST["chiName"]) ? $_POST["chiName"] : "" ?>" pattern="<?php echo regexChineseName() ?>" required>
					<label for="chiName">Name (Chinese)</label>
					<div class="form-text">
						<ul>
							<li>
								e.g. 陳大文
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
					<input type="text" class="form-control" name="occupation" placeholder="e.g. Software Engineer" value="<?php echo isset($_POST["occupation"]) ? $_POST["occupation"] : "" ?>" required>
					<label for="occupation">Occupation</label>
					<div class="form-text">
						<ul>
							<li>
								e.g. Software Engineer
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
					<label for="birthday">Date of Birth</label>
					<div class="form-text">
						<ul>
							<li>
								e.g. 01/01/1990
							</li>
						</ul>
					</div>
				</div>

				<div class="form-floating mb-3">
					<input type="text" class="form-control" name="birthPlace" placeholder="e.g. Hong Kong" value="<?php echo isset($_POST["birthPlace"]) ? $_POST["birthPlace"] : "" ?>" required>
					<label for="birthPlace">Place of Birth</label>
					<div class="form-text">
						<ul>
							<li>
								e.g. Hong Kong
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

				<div class="form-floating mb-3">
					<textarea class="form-control" placeholder="Address" id="address" name="address" style="height: 120px;" rows="4" required><?php echo isset($_POST["address"]) ? $_POST["address"] : "" ?></textarea>
					<label for="address">Address</label>
					<div class="form-text">
						<ul>
							<li>
								e.g. 5/F, 52 Lockhart Road, WAN CHAI, HONG KONG
							</li>
						</ul>
					</div>
				</div>

				<div class="row g-3 mb-3">
					<div class="form-floating col">
						<input type="date" class="form-control" name="reservationDate" value="<?php echo isset($_POST["reservationDate"]) ? $_POST["reservationDate"] : "" ?>" max="<?php echo maxReservationDate() ?>" min="<?php echo minReservationDate() ?>" required>
						<label for="reservationDate">Available Date</label>
					</div>

					<div class="form-floating col">
						<input type="time" class="form-control" name="reservationTime" value="<?php echo isset($_POST["reservationTime"]) ? $_POST["reservationTime"] : "" ?>" min="<?php echo minReservationTime() ?>" max="<?php echo maxReservationTime() ?>" step="1800" required>
						<label for="reservationTime">Time Slot (9AM - 5PM)</label>
					</div>
				</div>

				<div class="form-floating">
					<select class="form-select" name="redemptionPlace" aria-label="Redemption Place" required>
						<option></option>
						<?php
							foreach ([
								"Hong Kong Island Smart Identity Card Replacement Centre",
								"East Kowloon Smart Identity Card Replacement Centre",
								"West Kowloon Smart Identity Card Replacement Centre",
								"Tsuen Wan Smart Identity Card Replacement Centre",
								"Sha Tin Smart Identity Card Replacement Centre"
							] as $key => $value) {
						?>
							<option <?php echo isset($_POST["redemptionPlace"]) && $_POST["redemptionPlace"] == $value ? "selected" : "" ?>>
								<?php echo $value ?>
							</option>
						<?php
							}
						?>
					</select>
					<label for="redemptionPlace">Redemption Place</label>
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