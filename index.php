<?php include 'navbar.php'; ?>

<head>
	<title>Home</title>
</head>

<body>
	<div class="container-sm" style="padding: 30px;">
		<div class="col-sm-9" style="margin: auto;">
			<div class="card">
				<div class="card-body">
					<h3 class="card-title">
						Online Appointment Booking for Replacement of Identity Cards
					</h3>
					<p class="card-text">
					<p>
						Under the Territory-wide Identity Card Replacement Exercise, appointment booking period for replacing new smart identity cards at the newly established Smart Identity Card Replacement Centres (SIDCCs) is 24 working days.
						Before visiting the SIDCCs , applicants are advised to make appointment on the Internet, via the Immigration Department Mobile Application or by phone.
					</p>
					<p>
						You can make use of this online service to make an appointment booking for replacing new smart identity card and prefilling application form before visiting the SIDCCs.
						This article will tell you what you should know before using the online booking service.
					</p>
					</p>

					<a href="<?php echo isLoggedIn() ? "bookingform.php" : "login.php" ?>" class="btn btn-primary"> Start </a>
				</div>
			</div>
		</div>
	</div>
</body>

</html>

<?php
if (isset($_POST["logout"])) {
	$_SESSION = array();
	session_destroy();

	header('Location: /');
	exit;
}
?>