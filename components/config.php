<?php
// includes/config.php

$servername = "127.0.0.1"; 
$username = "root";        
$password = "";            
$dbname = "club_db";       

// Array of ports to try (Friend's port first, then your port)
$ports_to_try = [3306, 3307];

$conn = null;

// Tell PHP to throw exceptions for database errors so we can catch them smoothly
mysqli_report(MYSQLI_REPORT_STRICT | MYSQLI_REPORT_ERROR);

// Loop through each port and try to connect
foreach ($ports_to_try as $port) {
    try {
        // Try connecting using the specific port
        $conn = new mysqli($servername, $username, $password, $dbname, $port);
        
        // If the line above succeeds, we break out of the loop and keep the connection!
        break; 
        
    } catch (mysqli_sql_exception $e) {
        // If the connection was actively refused (wrong port), do nothing and let it try the next one
        $conn = null; 
    }
}

// After the loop finishes, check if we ever successfully made a connection
if ($conn === null) {
    die("Database Connection failed: Could not connect on ports 3306 or 3307. Please ensure your MySQL server is running.");
}
?>