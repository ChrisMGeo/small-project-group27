<?php
	require_once 'utils.php';
	require_once 'db_connect.php';

	session_start();
	if (!isset($_SESSION['userId'])) {
		error_log("Unauthorized access attempt to UpdateContacts from IP: " . $_SERVER['REMOTE_ADDR']);
		returnWithError(["error" => "Unauthorized access"], 401);
		$conn->close();
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
			error_log("Forbidden access attempt to update contact ID $id from IP: " . $_SERVER['REMOTE_ADDR']);
			returnWithError(["error" => "Forbidden: Contact does not belong to user"], 403);
			$verifyStmt->close();
			$conn->close();
			exit;
		}
	} else {
		returnWithError(["error" => "Contact not found"], 404);
		$verifyStmt->close();
		$conn->close();
		exit;
	}
	$verifyStmt->close();

	$stmt = $conn->prepare("UPDATE Contacts SET FirstName = ?, LastName=?, Phone= ?, Email= ? WHERE ID= ?");
	$stmt->bind_param("ssssi", $newFirst, $newLast, $phoneNumber, $emailAddress, $id);
	if ($stmt->execute()) {
		returnWithMessage("Successfully updated contact");
	} else {
		returnWithError(["error" => "Failed to update contact: " . $stmt->error]);
	}

	$stmt->close();
	$conn->close();
?>
