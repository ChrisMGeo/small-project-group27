<?php
	require_once 'utils.php';
	require_once 'db_connect.php';

	session_start();
	if (!isset($_SESSION['userId'])) {
		error_log("Unauthorized access attempt to AddContacts from IP: " . $_SERVER['REMOTE_ADDR']);
		returnWithError(["error" => "Unauthorized access"], 401);
		$conn->close();
		exit;
	}

	$inData = getRequestInfo();

	$firstName = $inData["firstName"];
	$lastName = $inData["lastName"];
	$phoneNumber = $inData["phoneNumber"];
	$emailAddress = $inData["emailAddress"];
	$userId = $_SESSION['userId'];

	$stmt = $conn->prepare("INSERT into Contacts (FirstName, LastName, Phone, Email, UserID) VALUES(?,?,?,?,?)");
	$stmt->bind_param("ssssi", $firstName, $lastName, $phoneNumber, $emailAddress, $userId);
	if ($stmt->execute()) {
		returnWithMessage("Successfully added contact");
	} else {
		returnWithError(["error" => "Failed to insert contact: " . $stmt->error]);
	}
	$stmt->close();
	$conn->close();
?>
