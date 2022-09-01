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
					<form id="form1" action="<?php echo $_SERVER["PHP_SELF"] ?>" method="POST">
						<div class="form-floating mb-3">
							<input type="text" class="form-control" id="floatingName" placeholder="Chan Tai Man" pattern="<?php echo regexEnglishName() ?>" required>
							<label for="floatingName">Name (English)</label>
						</div>

						<div class="form-floating mb-3">
							<input type="text" class="form-control" id="floatingIDCardNo" placeholder="A123456(7)" pattern="<?php echo regexIDCardNo() ?>" required>
							<label for="floatingIDCardNo">ID Card No.</label>
						</div>

						<div class="form-floating mb-3">
							<input type="date" class="form-control" id="floatingBirthday" max="<?php echo maxBirthday() ?>" min="<?php echo minBirthday() ?>" required>
							<label for="floatingBirthday">Birthday</label>
						</div>

						<div class="form-floating">
							<input type="tel" class="form-control" id="floatingContact" placeholder="12345678" pattern="<?php echo regexContact() ?>" required>
							<label for="floatingContact">Contact</label>
						</div>
					</form>
					</p>

					<div style="text-align: center;">
						<button type="submit" style="width: 100%;" form="form1" class="btn btn-primary">Submit</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</body>

</html>