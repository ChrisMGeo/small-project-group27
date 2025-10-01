<?php
	require_once 'utils.php';
	require_once 'db_connect.php';

	session_start();
	if (!isset($_SESSION['userId'])) {
		http_response_code(401);
		error_log("Unauthorized access attempt to UpdateContacts from IP: " . $_SERVER['REMOTE_ADDR']);
		returnWithError("Unauthorized access");
		exit;
	}

	$inData = getRequestInfo();

	$phoneNumber = $inData["phoneNumber"];
	$emailAddress = $inData["emailAddress"];
	$newFirst = $inData["newFirstName"];
	$newLast = $inData["newLastName"];
	$id = $inData["id"];

	// Verify the contact belongs to the user
	$verifyStmt = $conn->prepare("SELECT UserID FROM Contacts WHERE ID = ?");
	$verifyStmt->bind_param("i", $id);
	$verifyStmt->execute();
	$verifyResult = $verifyStmt->get_result();
	if ($verifyRow = $verifyResult->fetch_assoc()) {
		if ($verifyRow['UserID'] != $_SESSION['userId']) {
			http_response_code(403);
			error_log("Forbidden access attempt to update contact ID $id from IP: " . $_SERVER['REMOTE_ADDR']);
			returnWithError("Forbidden: Contact does not belong to user");
			exit;
		}
	} else {
		returnWithError("Contact not found");
		exit;
	}
	$verifyStmt->close();

	$stmt = $conn->prepare("UPDATE Contacts SET FirstName = ?, LastName=?, Phone= ?, Email= ? WHERE ID= ?");
	$stmt->bind_param("ssssi", $newFirst, $newLast, $phoneNumber, $emailAddress, $id);
	if ($stmt->execute()) {
		returnWithMessage("Successfully updated contact");
	} else {
		returnWithError("Failed to update contact: " . $stmt->error);
	}

	$stmt->close();
	$conn->close();
?>
