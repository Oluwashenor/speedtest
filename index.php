<?php
require_once __DIR__ . '/services/api.php';

$api = new API();

if (isset($_GET["device_unique_id"]) && isset($_GET["upload"]) && isset($_GET["download"]) && isset($_GET["latency"])) {
    $device_unique_id = $_GET["device_unique_id"];
    $upload = floatval($_GET["upload"]);
    $download = floatval($_GET["download"]);
    $latency = floatval($_GET["latency"]);
    echo $api->logSpeedTestResult($device_unique_id, $upload, $download, $latency);
} else {
    header("Location: dashboard.php");
}
