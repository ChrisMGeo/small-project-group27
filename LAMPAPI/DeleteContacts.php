<?php
    require_once 'utils.php';
    require_once 'db_connect.php';
   
    session_start();
    if (!isset($_SESSION['userId'])) {
    	http_response_code(401);
    	error_log("Unauthorized access attempt to DeleteContacts from IP: " . $_SERVER['REMOTE_ADDR']);
    	returnWithError("Unauthorized access");
    	exit;
    }

    $inData = getRequestInfo();
   
    $userId = $_SESSION['userId'];
    $firstName = $inData["firstName"];
    $lastName = $inData["lastName"];

    $stmt = $conn->prepare("DELETE FROM Contacts WHERE FirstName = ? AND LastName = ? AND UserID = ?");
    $stmt->bind_param("ssi", $firstName, $lastName, $userId);
    if ($stmt->execute()) {
        returnWithMessage("Successfully deleted contact");
    } else {
        returnWithError("Failed to delete contact: " . $stmt->error);
    }
    $stmt->close();
    $conn->close();
?>
