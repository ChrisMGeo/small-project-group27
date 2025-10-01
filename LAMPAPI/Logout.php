<?php
require_once 'utils.php';

session_start();

if (isset($_SESSION['userId'])) {
    unset($_SESSION['userId']);
    session_destroy();
    returnWithMessage("Logged out successfully");
} else {
    http_response_code(401);
    returnWithError("No active session");
}
?>