<?php
	// Load environment variables from .env file
	function loadEnv($path) {
		if (!file_exists($path)) {
			return;
		}
		
		$lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
		foreach ($lines as $line) {
			// Skip comments and empty lines
			if (strpos($line, '#') === 0 || empty(trim($line))) {
				continue;
			}
			
			// Parse KEY=VALUE pairs
			list($key, $value) = explode('=', $line, 2);
			$key = trim($key);
			$value = trim($value);
			
			// Remove quotes if present
			$value = trim($value, '"\'');
			
			// Set the environment variable
			putenv("$key=$value");
			$_ENV[$key] = $value;
			$_SERVER[$key] = $value;
		}
	}
	
	// Load the .env file
	loadEnv(__DIR__ . '/.env');

	function getRequestInfo() {
		return json_decode(file_get_contents('php://input'), true);
	}

	function sendResultInfoAsJson($obj) {
		header('Content-type: application/json');
		echo $obj;
	}
	
	function returnWithError($err, $additionalFields = []) {
		$retValue = ["error" => $err];
		
		// Add any additional fields that were passed
		foreach ($additionalFields as $key => $value) {
			$retValue[$key] = $value;
		}
		
		// If no additional fields were specified, use default empty values for common fields
		if (empty($additionalFields)) {
			$retValue["id"] = 0;
			$retValue["firstName"] = "";
			$retValue["lastName"] = "";
		}
		
		sendResultInfoAsJson(json_encode($retValue));
	}
	
	function returnWithInfo($data) {
		$retValue = $data;
		$retValue["error"] = "";
		sendResultInfoAsJson(json_encode($retValue));
	}
?>