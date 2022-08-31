<head>
	<title>Home</title>
</head>

<?php include 'navbar.php'; ?>

<body>
	<div class="container-sm" style="padding: 30px;">
		<div style="text-align: center;">
			<div class="card">
				<div class="card-body">
					<h3 class="card-title">Home</h3>
					<p class="card-text">Some quick example text to build on the card title and make up the bulk of the card's content.</p>
					<a href="#" class="btn btn-primary">Go somewhere</a>
				</div>
			</div>
		</div>

		<div class="col-sm-6" style="margin: auto;">

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