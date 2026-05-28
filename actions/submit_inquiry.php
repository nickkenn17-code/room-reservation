<?php
session_start();
require_once '../components/config.php'; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 1. Sanitize all incoming data
    $name = $conn->real_escape_string(trim($_POST['visitor_name']));
    $email = $conn->real_escape_string(trim($_POST['visitor_email']));
    $event_id = (int)$_POST['event_id']; // Cast to integer for extra security
    $message = $conn->real_escape_string(trim($_POST['message']));
    
    // 2. Insert into the database
    $sql = "INSERT INTO visitor_inquiries (visitor_name, visitor_email, event_id, message) 
            VALUES ('$name', '$email', $event_id, '$message')";
    
    // 3. Execute and redirect
    if ($conn->query($sql) === TRUE) {
        header("Location: ../pages/contact.php?success=1");
        exit();
    } else {
        echo "Database Error: " . $conn->error;
    }
} else {
    header("Location: ../pages/contact.php");
    exit();
}
?>