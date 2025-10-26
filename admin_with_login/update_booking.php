<?php
require_once 'config.php';
require_login();
$data = json_decode(file_get_contents("php://input"), true);
$id = $data['id'] ?? null;

if (!$id) {
  echo json_encode(['error' => 'Missing booking ID']);
  exit;
}

$updateFields = $data;
unset($updateFields['id']);

$ch = curl_init("$BOOKINGS_ENDPOINT?id=eq.$id");
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PATCH");
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($updateFields));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
  "apikey: $SUPABASE_SERVICE_ROLE_KEY",
  "Authorization: Bearer $SUPABASE_SERVICE_ROLE_KEY",
  "Content-Type: application/json",
  "Prefer: return=representation"
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
if (curl_errno($ch)) {
  echo json_encode(['error' => curl_error($ch)]);
} else {
  echo json_encode(['success' => true, 'response' => json_decode($response, true)]);
}
curl_close($ch);
?>
