<?php
    require_once 'utils.php';
    require_once 'db_connect.php';
   
    session_start();
    if (!isset($_SESSION['userId'])) {
    	error_log("Unauthorized access attempt to DeleteContacts from IP: " . $_SERVER['REMOTE_ADDR']);
    	returnWithError(["error" => "Unauthorized access"], 401);
        $conn->close();
    	exit;
    }

    $inData = getRequestInfo();
   
    $contactId = $inData["contactId"];
    $userId = $_SESSION['userId'];
   
    // Verify the contact belongs to the user
    $verifyStmt = $conn->prepare("SELECT UserID FROM Contacts WHERE ID = ?");
    $verifyStmt->bind_param("i", $contactId);
    $verifyStmt->execute();
    $verifyResult = $verifyStmt->get_result();
    if ($verifyRow = $verifyResult->fetch_assoc()) {
    	if ($verifyRow['UserID'] != $userId) {
    		error_log("Forbidden access attempt to delete contact ID $contactId from IP: " . $_SERVER['REMOTE_ADDR']);
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

    $stmt = $conn->prepare("DELETE FROM Contacts WHERE ID = ? AND UserID = ?");
    $stmt->bind_param("ii", $contactId, $userId);
    if ($stmt->execute()) {
        returnWithMessage("Successfully deleted contact");
    } else {
        returnWithError(["error" => "Failed to delete contact: " . $stmt->error]);
    }
    $stmt->close();
    $conn->close();
?>
