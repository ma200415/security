<?php
include 'navbar.php';
redirectHomeIfNotLoggedIn(["admin"]);
?>

<head>
	<title>Booking Records</title>
</head>

<body>
	<script>
		function submitAction() {
			return confirm("Confirm")
		}
	</script>

	<div class="container-fluid" style="padding: 30px;">
		<div class="col" style="margin: auto;">
			<div class="card">
				<div class="card-body">
					<h3 class="card-title" style="text-align: center;">Booking Records</h3>

					<p class="card-text">
						<?php
						if (isset($_POST["approve"]) || isset($_POST["reject"])) {
							list($bookingId) = decryptData([$_POST["bookingId"]], $_POST["bookingIV"]);

							if (isset($_POST["approve"])) {
								$status = "approve";
								$emailSubject = "ID Card Appointment Reminder";
							} else if (isset($_POST["reject"])) {
								$status = "reject";
								$emailSubject = "ID Card Appointment Rejected";
							}

							try {
								$dbh = pdo();
								$sql = 'SELECT (select email from user where id=user) email, redemptionPlace, reservationDate, reservationTime FROM booking WHERE id = ?';
								$sth = $dbh->prepare($sql);
								$sth->execute([$bookingId]);
								$bookingEmail = $sth->fetch(PDO::FETCH_ASSOC);
							} catch (PDOException $e) {
								echo $e->getMessage();
								exit;
							}

							try {
								$sql = 'UPDATE booking SET status = ?, sdate = CURRENT_TIMESTAMP(), sby = ? WHERE id = ?';
								$sth = $dbh->prepare($sql);
								$sth->execute([$status, $_SESSION["userId"], $bookingId]);

								include_once 'sendemail.php';
								echo sendEmail(
									$bookingEmail["email"],
									$emailSubject,
									sprintf(
										"Your appointment:<br/>Date: %s %s<br/>Venues: %s",
										$bookingEmail["reservationDate"],
										$bookingEmail["reservationTime"],
										$bookingEmail["redemptionPlace"]
									)
								);

								header("Location: " . $_SERVER["PHP_SELF"]);
								exit;
							} catch (PDOException $e) {
								echo $e->getMessage();
							}
						}
						?>

					<table class="table">
						<thead>
							<tr>
								<th scope="col">Reservation Date</th>
								<th scope="col">Time</th>
								<th scope="col">Redemption Place</th>
								<th scope="col">User</th>
								<th scope="col">Eng. Name</th>
								<th scope="col">Chi. Name</th>
								<th scope="col">Gender</th>
								<th scope="col">Photo</th>
								<th scope="col">Occupation</th>
								<th scope="col">ID Card No.</th>
								<th scope="col">Date of Birth</th>
								<th scope="col">Place of Birth</th>
								<th scope="col">Contact</th>
								<th scope="col">Address</th>
								<th scope="col">Submission Date</th>
								<th scope="col">Action</th>
							</tr>
						</thead>
						<tbody>
							<?php
							$dbh = pdo();
							$sql = 'SELECT *, (SELECT email FROM user WHERE id=user) userEmail FROM booking ORDER BY reservationDate DESC';
							$sth = $dbh->prepare($sql);
							$sth->execute();
							$bookings = $sth->fetchAll();

							foreach ($bookings as $key => $booking) {
								list($engName, $chiName, $idCardNo, $gender, $photo, $occupation, $birthday, $birthPlace, $contact, $address) =
									decryptData([
										$booking["engName"], $booking["chiName"], $booking["idNo"], $booking["gender"], $booking["photo"],
										$booking["occupation"], $booking["birthday"], $booking["birthPlace"], $booking["contact"], $booking["address"]
									], $booking["iv"]);

								list($e_bookingId) = encryptData([$booking["id"]], base64_decode($booking["iv"]));
							?>
								<tr>
									<td><?php echo $booking["reservationDate"] ?></td>
									<td><?php echo $booking["reservationTime"] ?></td>
									<td><?php echo $booking["redemptionPlace"] ?></td>
									<td><?php echo $booking["userEmail"] ?></td>
									<td><?php echo $engName ?></td>
									<td><?php echo $chiName ?></td>
									<td><?php echo $gender ?></td>
									<td>
										<a href="#" data-bs-toggle="modal" data-bs-target="#photoModal<?php echo $key ?>">
											<img src="data:image/png;base64,<?php echo $photo ?>" class="img-thumbnail" style="max-height: 200px; max-width: 200px" />
										</a>

										<div class="modal fade" id="photoModal<?php echo $key ?>" tabindex="-1" aria-hidden="true">
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
									</td>
									<td><?php echo $occupation ?></td>
									<td><?php echo $idCardNo ?></td>
									<td><?php echo $birthday ?></td>
									<td><?php echo $birthPlace ?></td>
									<td><?php echo $contact ?></td>
									<td><?php echo $address ?></td>
									<td><?php echo $booking["cdate"] ?></td>
									<td>
										<div class="d-grid gap-2 d-md-block">
											<form action="<?php echo $_SERVER["PHP_SELF"] ?>" method="POST" onsubmit="return submitAction()">
												<input name="bookingId" type="hidden" value="<?php echo $e_bookingId ?>" />
												<input name="bookingIV" type="hidden" value="<?php echo $booking["iv"] ?>" />

												<?php
												if (empty($booking["status"])) :
												?>
													<button name="approve" type="submit" class="btn btn-success">Approve</button>
													<button name="reject" type="submit" class="btn btn-danger">Reject</button>
												<?php
												else :
													switch ($booking["status"]) {
														case 'approve':
															echo '<h5><span class="badge text-bg-success">Approved</span></h5>';
															break;
														case 'reject':
															echo '<h5><span class="badge text-bg-danger">Rejected</span></h5>';
															break;
														default:
															echo $booking["status"];
															break;
													}
												endif
												?>
											</form>
										</div>
									</td>
								</tr>
							<?php
							}
							?>
						</tbody>
					</table>
					</p>
				</div>
			</div>
		</div>
	</div>
</body>

</html>