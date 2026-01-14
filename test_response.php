<?php
// Test con credenciales reales para ver la respuesta exacta
$ch = curl_init('http://localhost:5000/login');
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['username' => '1001891388', 'password' => 'test']));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_TIMEOUT, 60);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Code: $httpCode\n";
echo "Response RAW:\n";
echo $response . "\n\n";

$json = json_decode($response, true);
echo "Response PARSED:\n";
print_r($json);
?>