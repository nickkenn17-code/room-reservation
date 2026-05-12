<?php
session_start();
require_once '../components/config.php';

$schedule_id = $_GET['schedule_id'];
$isAdmin = (strtolower($_SESSION['role'] ?? '') === 'admin');

// Fetch all users and their status for this specific meeting
$sql = "SELECT user.id, user.name, role.role, attendance.status 
        FROM user 
        LEFT JOIN role ON user.id = role.user_id 
        LEFT JOIN attendance ON user.id = attendance.user_id AND attendance.schedule_id = ?
        ORDER BY user.name ASC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $schedule_id);
$stmt->execute();
$result = $stmt->get_result();

if ($isAdmin) echo "<form method='POST' action='user_page.php'><input type='hidden' name='schedule_id' value='$schedule_id'>";

while ($row = $result->fetch_assoc()) {
    $current_status = $row['status'] ?? '';
    echo "<div class='attendance-row' style='display:flex; justify-content:space-between; margin-bottom:10px;'>";
    echo "<span><strong>{$row['name']}</strong></span>";

    if ($isAdmin) {
        // Admin gets the dropdown from your old attendance_list.php
        echo "<select name='status[{$row['id']}]' class='mini-select'>
                <option value=''>-- Select --</option>
                <option value='Present' ".($current_status == 'Present' ? 'selected' : '').">Present</option>
                <option value='Absent' ".($current_status == 'Absent' ? 'selected' : '').">Absent</option>
                <option value='Pending' ".($current_status == 'Pending' ? 'selected' : '').">Pending</option>
              </select>";
    } else {
        // Members only see the status badge
        echo "<span class='status-badge ".strtolower($current_status)."'>".($current_status ?: 'Not Set')."</span>";
    }
    echo "</div>";
}

if ($isAdmin) echo "<button type='submit' name='save_attendance' class='btn-save-absence' style='width:100%; margin-top:15px;'>Save Changes</button></form>";
?>