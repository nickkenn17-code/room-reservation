<?php
session_start();
// Make sure this path correctly points to your database connection file
require_once '../components/config.php'; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 1. Grab the data from the form and clean it to prevent hacking (SQL Injection)
    $name = $conn->real_escape_string(trim($_POST['visitor_name']));
    $message = $conn->real_escape_string(trim($_POST['message']));
    
    // 2. Write the SQL command to insert it into the database
    // (Notice we don't insert a status, because the database defaults it to 'Pending')
    $sql = "INSERT INTO visitor_inquiries (visitor_name, message) VALUES ('$name', '$message')";
    
    // 3. Execute the command and redirect
    if ($conn->query($sql) === TRUE) {
        // Send them back to the contact page with a success flag in the URL
        header("Location: ../pages/contact.php?success=1");
        exit();
    } else {
        echo "Database Error: " . $conn->error;
    }
} else {
    // If someone tries to visit this file directly, kick them back to the form
    header("Location: ../pages/contact.php");
    exit();
}
?>