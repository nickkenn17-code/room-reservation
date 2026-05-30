<?php
session_start();
require_once '../components/config.php';

// 1. Security Check: Must be logged in as Staff or Admin
if (!isset($_SESSION['id']) || ($_SESSION['role'] !== 'Admin' && $_SESSION['role'] !== 'Staff')) {
    header("Location: ../index.php");
    exit();
}

// 2. Check if the form was actually submitted via POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Grab the data from the form
    $event_id = (int)$_POST['event_id'];
    $event_name = trim($_POST['event_name']);
    $event_date = $_POST['event_date'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];
    $description = trim($_POST['description']);

    // 3. Secondary Security: If Staff, ensure they are only editing THEIR assigned event
    if ($_SESSION['role'] === 'Staff' && $_SESSION['event_id'] != $event_id) {
        die("Unauthorized access: You can only edit your assigned event.");
    }

    // 4. Prepare the SQL UPDATE statement
    $sql = "UPDATE event_list 
            SET event_name = ?, date = ?, start_time = ?, end_time = ?, description = ? 
            WHERE id = ?";
            
    $stmt = $conn->prepare($sql);
    
    if ($stmt) {
        // Bind the parameters (s = string, i = integer)
        $stmt->bind_param("sssssi", $event_name, $event_date, $start_time, $end_time, $description, $event_id);
        
        // Execute the update
        if ($stmt->execute()) {
            // THE FIX: Redirect back into the pages/ folder!
            header("Location: ../pages/staff_page.php?update=success");
            exit();
        } else {
            die("Database Error: Could not update the event.");
        }
        $stmt->close();
    } else {
        die("SQL Preparation Error: " . $conn->error);
    }
    
} else {
    // If someone tries to access this file directly, bounce them back to the dashboard
    header("Location: ../pages/staff_page.php");
    exit();
}
?>