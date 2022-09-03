<?php
include 'navbar.php';
redirectHomeIfNotLoggedIn(["admin"]);
?>

<head>
	<title>Booking Records</title>
</head>

<body>
	<div class="container-sm" style="padding: 30px;">
		<div class="col" style="margin: auto;">
			<div class="card">
				<div class="card-body">
					<h3 class="card-title" style="text-align: center;">Booking Records</h3>

					<p class="card-text">
					<table class="table">
						<thead>
							<tr>
								<th scope="col">Reservation Date</th>
								<th scope="col">User</th>
								<th scope="col">Eng. Name</th>
								<th scope="col">Gender</th>
								<th scope="col">Photo</th>
								<th scope="col">ID Card No.</th>
								<th scope="col">Birthday</th>
								<th scope="col">Contact</th>
								<th scope="col">Submission Date</th>
							</tr>
						</thead>
						<tbody>
							<?php
							$dbh = pdo();
							$sql = 'SELECT (SELECT email FROM user WHERE id=user) user, engName, idNo, gender, photo, birthday, contact, reservationDate, iv, cdate FROM booking ORDER BY reservationDate DESC';
							$sth = $dbh->prepare($sql);
							$sth->execute();
							$bookings = $sth->fetchAll();

							foreach ($bookings as $key => $value) {
								list($engName, $idCardNo, $gender, $photo, $birthday, $contact) = decryptData([$value["engName"], $value["idNo"], $value["gender"], $value["photo"], $value["birthday"], $value["contact"]], $value["iv"]);
							?>
								<tr>
									<td><?php echo $value["reservationDate"] ?></td>
									<td><?php echo $value["user"] ?></td>
									<td><?php echo $engName ?></td>
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
									<td><?php echo $idCardNo ?></td>
									<td><?php echo $birthday ?></td>
									<td><?php echo $contact ?></td>
									<td><?php echo $value["cdate"] ?></td>
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