<?php
	$host = "localhost";
	$username = "TheBeast";
	$password = "WeLoveCOP4331";
	$database = "COP4331";

	$conn = new mysqli($host, $username, $password, $database);

	if ($conn->connect_error) {
		returnWithError(["error" => $conn->connect_error]);
		exit();
	}
?>