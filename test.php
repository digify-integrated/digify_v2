<?php

$ipAddress = $_SERVER['REMOTE_ADDR'];
$locationData = json_decode(file_get_contents("http://ipinfo.io/112.207.178.12/json"), true);
$location = isset($locationData['city'], $locationData['country']) ? "{$locationData['city']}, {$locationData['country']}" : "Unknown";

// Respond with IP and location for the JavaScript
echo json_encode(['ip' => $ipAddress, 'location' => $location]);
?>