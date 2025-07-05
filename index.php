<?php
require_once __DIR__ . '/services/api.php';

$api = new API();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $post_body = file_get_contents('php://input');
    echo $post_body;
} else {
    header("Location: dashboard.php");
}
