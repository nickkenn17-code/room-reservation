<?php
session_start();
require_once '../components/config.php';

if (!isset($_SESSION['id']) || ($_SESSION['role'] !== 'Admin' && $_SESSION['role'] !== 'Staff')) {
    header("Location: ../index.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $event_id = (int)$_POST['event_id'];
    $event_name = trim($_POST['event_name']);
    $event_date = $_POST['event_date'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];
    $description = trim($_POST['description']);

    // 1. Update text data
    $sql = "UPDATE event_list SET event_name = ?, date = ?, start_time = ?, end_time = ?, description = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssi", $event_name, $event_date, $start_time, $end_time, $description, $event_id);
    $stmt->execute();

    if (isset($_FILES['event_image']) && $_FILES['event_image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../assets/images/gallery/';

        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $original_name = $_FILES['event_image']['name'] ?? '';
        $extension = strtolower(pathinfo($original_name, PATHINFO_EXTENSION));
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

        if (!in_array($extension, $allowed_extensions, true)) {
            header("Location: ../pages/staff_page.php?update=invalid_image");
            exit();
        }

        // Find the next image number for this event and keep the real extension.
        $stmt = $conn->prepare("SELECT COUNT(*) as count FROM event_images WHERE event_id = ?");
        $stmt->bind_param("i", $event_id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $next_num = ((int)$result['count']) + 1;

        $file_name = $event_id . '-' . $next_num . '.' . $extension;

        if (move_uploaded_file($_FILES['event_image']['tmp_name'], $upload_dir . $file_name)) {
            $db_path = 'assets/images/gallery/' . $file_name;
            $stmt = $conn->prepare("INSERT INTO event_images (event_id, image_path) VALUES (?, ?)");
            $stmt->bind_param("is", $event_id, $db_path);
            $stmt->execute();
        }
    }

    
    header("Location: ../pages/staff_page.php?update=success");
    exit();
}
?>