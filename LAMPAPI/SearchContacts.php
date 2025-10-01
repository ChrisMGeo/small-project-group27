<?php
	require_once 'utils.php';
	require_once 'db_connect.php';

	session_start();
	if (!isset($_SESSION['userId'])) {
		http_response_code(401);
		error_log("Unauthorized access attempt to SearchContacts from IP: " . $_SERVER['REMOTE_ADDR']);
		returnWithError("Unauthorized access");
		$conn->close();
		exit;
	}

	$inData = getRequestInfo();

	$searchResults = [];
	$searchCount = 0;

	$stmt = $conn->prepare("SELECT FirstName, LastName, Phone, Email, UserID, ID FROM Contacts WHERE (FirstName like ? OR LastName like?) AND UserID=?");
	$searchTerm = "%" . $inData["search"] . "%";
	$stmt->bind_param("ssi", $searchTerm, $searchTerm, $_SESSION['userId']);
	$stmt->execute();

	$result = $stmt->get_result();

	while($row = $result->fetch_assoc())
	{
		$searchResults[] = $row;
		$searchCount++;
	}

	if( $searchCount == 0 )
	{
		returnWithError( "No Records Found" );
	}
	else
	{
		$data = ["results" => $searchResults];
		returnWithInfo( $data );
	}

	$stmt->close();
	$conn->close();
?>
