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
								<th scope="col">ID Card No.</th>
								<th scope="col">Birthday</th>
								<th scope="col">Contact</th>
								<th scope="col">Submission Date</th>
							</tr>
						</thead>
						<tbody>
							<?php
							$dbh = pdo();
							$sql = 'SELECT (SELECT email FROM user WHERE id=user) user, engName, idNo, birthday, contact, reservationDate, iv, cdate FROM booking ORDER BY reservationDate DESC';
							$sth = $dbh->prepare($sql);
							$sth->execute();
							$bookings = $sth->fetchAll();

							foreach ($bookings as $key => $value) {
								list($engName, $idCardNo, $birthday, $contact) = decryptData([$value["engName"], $value["idNo"], $value["birthday"], $value["contact"]], $value["iv"]);
							?>
								<tr>
									<td><?php echo $value["reservationDate"] ?></td>
									<td><?php echo $value["user"] ?></td>
									<td><?php echo $engName ?></td>
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