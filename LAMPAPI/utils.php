<?php
	function getRequestInfo() {
		return json_decode(file_get_contents('php://input'), true);
	}

	function sendResultInfoAsJson($obj) {
		header('Content-type: application/json');
		echo $obj;
	}
	
	function returnWithError($error, $statusCode = 500) {
		http_response_code($statusCode);
		
		sendResultInfoAsJson(json_encode($error));
	}
	
	function returnWithInfo($data, $statusCode = 200) {
		http_response_code($statusCode);

		sendResultInfoAsJson(json_encode($data));
	}
	
	function returnWithMessage($message) {
		returnWithInfo(["message" => $message]);
	}
?>