<?php include 'navbar.php'; ?>

<head>
	<title>Home</title>
</head>

<body>
	<div class="container-sm" style="padding: 30px;">
		<div class="col-sm-6" style="margin: auto;">
			<div class="card">
				<div class="card-body">
					<h3 class="card-title">
						<div style="text-align: center;">
							Home
						</div>
					</h3>
					<p class="card-text">Some quick example text to build on the card title and make up the bulk of the card's content.</p>
					<?php
					if (isset($_SESSION["email"])) :
					?>
						<a href="bookingform.php" class="btn btn-primary"> Start </a>
					<?php
					endif
					?>
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