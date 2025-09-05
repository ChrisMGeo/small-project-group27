<?php
	// Environment variables are loaded automatically by utils.php
	
	$conn = new mysqli(getenv('DB_HOST'), getenv('DB_USERNAME'), getenv('DB_PASSWORD'), getenv('DB_NAME'));

	if ($conn->connect_error) {
		returnWithError($conn->connect_error);
		exit();
	}
?>