<?php
require_once __DIR__ . '/services/api.php';

$api = new API();

// Handle POST requests for speed test logging
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get raw POST data
    $raw_data = file_get_contents('php://input');
    
    // Decode JSON data
    $data = json_decode($raw_data, true);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        echo json_encode([
            'success' => false,
            'message' => 'Invalid JSON data: ' . json_last_error_msg()
        ]);
        exit;
    }
    
    // Log the speed test result
    $api->logSpeedTestResult($data);
} else {
    // Redirect to dashboard for non-POST requests
    header("Location: dashboard.php");
    exit;
}
