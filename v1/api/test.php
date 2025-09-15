<?php
// Test file untuk API files
echo "<h2>Test API Files</h2>";

// Test GET request
echo "<h3>1. Test GET /files.php</h3>";
$url = "http://localhost/v1/api/files.php";
$context = stream_context_create([
    'http' => [
        'method' => 'GET',
        'header' => 'Content-Type: application/json'
    ]
]);

$response = file_get_contents($url, false, $context);
echo "<pre>" . htmlspecialchars($response) . "</pre>";

// Test dengan parameter DataTable
echo "<h3>2. Test GET /files.php dengan parameter DataTable</h3>";
$url_with_params = "http://localhost/v1/api/files.php?draw=1&start=0&length=5&search[value]=&order[0][column]=0&order[0][dir]=desc";
$response2 = file_get_contents($url_with_params, false, $context);
echo "<pre>" . htmlspecialchars($response2) . "</pre>";

// Test POST request
echo "<h3>3. Test POST /files.php</h3>";
$postData = json_encode([
    'file_code' => 'TEST001',
    'fk_client_id' => 1,
    'fk_agent_id' => 1,
    'file_arrival_date' => date('Y-m-d'),
    'file_type' => 1,
    'file_type_desc' => 'Test File'
]);

$context_post = stream_context_create([
    'http' => [
        'method' => 'POST',
        'header' => 'Content-Type: application/json',
        'content' => $postData
    ]
]);

$response3 = file_get_contents($url, false, $context_post);
echo "<pre>" . htmlspecialchars($response3) . "</pre>";
?>
