<?php
require_once '../components/config.php';

header('Content-Type: application/json; charset=utf-8');

$event_id = isset($_GET['event_id']) ? (int)$_GET['event_id'] : 0;

if ($event_id <= 0) {
	echo json_encode([]);
	exit();
}

$stmt = $conn->prepare("SELECT image_path FROM event_images WHERE event_id = ? ORDER BY id ASC");
$stmt->bind_param("i", $event_id);
$stmt->execute();
$result = $stmt->get_result();

echo json_encode($result->fetch_all(MYSQLI_ASSOC));
?>