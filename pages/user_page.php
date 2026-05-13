<?php
    session_start();
    require_once '../components/config.php';

    // 1. Security Check
    if (!isset($_SESSION['id'])) {
        header("Location: ../index.php");
        exit();
    }

    $show_weak_password = !empty($_SESSION['weak_password']);
    unset($_SESSION['weak_password']);

    // 2. Determine User Role
    $role = isset($_SESSION['role']) ? strtolower($_SESSION['role']) : '';
    $isAdmin = ($role == 'admin');
    $isManager = ($role == 'manager');
    $can_edit = ($isAdmin);

    // 3. Fetch Users Data
    // 3. Fetch Users Data (Sorted by Role: Admin -> Manager -> Member)
    $sql = "SELECT user.id, user.name, user.major, user.profile_pic, role.role
            FROM user 
            LEFT JOIN role ON user.id = role.user_id 
            ORDER BY 
                CASE 
                    WHEN LOWER(role.role) = 'admin' THEN 1
                    WHEN LOWER(role.role) = 'manager' THEN 2
                    ELSE 3 
                END ASC, 
                user.name ASC";
    $result = $conn->query($sql);

    // --- FETCH ACTIVITY LOGS FOR THE LOGS VIEW ---
    $sql_logs = "SELECT activity_log.*, user.name AS current_name 
                 FROM activity_log 
                 LEFT JOIN user ON activity_log.user_id = user.id 
                 ORDER BY activity_log.created_at DESC LIMIT 50";
    $result_logs = $conn->query($sql_logs);

    // 4. Fetch Schedule/Meetings Data AND Venue Data
    // Find Section 4 in your user_page.php and replace the query:
    $u_id = $_SESSION['id'];
    $sql_schedule = "SELECT s.id, s.meeting_name, s.meeting_time, v.room_name, a.status as user_status
                    FROM schedule s
                    LEFT JOIN venue v ON s.id = v.schedule_id 
                    LEFT JOIN attendance a ON s.id = a.schedule_id AND a.user_id = $u_id
                    ORDER BY s.meeting_time ASC";
    $result_schedule = $conn->query($sql_schedule);

    // --- ATTENDANCE SAVE LOGIC (Admin/Manager only) ---
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save_attendance'])) {
        $schedule_id = $_POST['schedule_id'];
        
        foreach ($_POST['status'] as $u_id => $status_value) {
            if (empty($status_value)) continue;

            $sql_save = "INSERT INTO attendance (user_id, schedule_id, status) VALUES (?, ?, ?) 
                         ON DUPLICATE KEY UPDATE status = ?";
            $stmt_save = $conn->prepare($sql_save);
            $stmt_save->bind_param("iiss", $u_id, $schedule_id, $status_value, $status_value);
            $stmt_save->execute();
        }
        $_SESSION['attendance_msg'] = "Attendance updated successfully!";
        header("Location: user_page.php?view=meetings");
        exit();
    }

    // --- MEMBER RSVP LOGIC ("I'm Going") ---
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_rsvp'])) {
        $schedule_id = $_POST['rsvp_schedule_id'];
        $u_id = $_SESSION['id'];
        $status = 'Present'; 

        $sql_rsvp = "INSERT INTO attendance (user_id, schedule_id, status) VALUES (?, ?, ?) 
                    ON DUPLICATE KEY UPDATE status = ?, reason = NULL";
        $stmt_rsvp = $conn->prepare($sql_rsvp);
        $stmt_rsvp->bind_param("iiss", $u_id, $schedule_id, $status, $status);
        $stmt_rsvp->execute();
        
        $_SESSION['meeting_msg'] = "Awesome! You are marked as attending.";
        header("Location: user_page.php?view=meetings");
        exit();
    }

    // --- ABSENCE REQUEST FORM HANDLING ---
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_absence'])) {
        $user_id = $_SESSION['id']; 
        $schedule_id = $_POST['schedule_id']; 
        $reason = trim($_POST['reason']);
        $status = 'Pending'; 

        $stmt = $conn->prepare("INSERT INTO attendance (user_id, schedule_id, status, reason) 
                                VALUES (?, ?, ?, ?) 
                                ON DUPLICATE KEY UPDATE status = ?, reason = ?");
        $stmt->bind_param("iissss", $user_id, $schedule_id, $status, $reason, $status, $reason);
        
        if ($stmt->execute()) {
            $_SESSION['absence_message'] = "Your absence request has been submitted and is pending approval.";
        } else {
            $_SESSION['absence_message'] = "Error submitting request. Please try again.";
        }
        $stmt->close();
        
        header("Location: user_page.php?view=absence");
        exit();
    }

    // --- ADMIN: CREATE MEETING LOGIC ---
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_meeting'])) {
        if ($isAdmin || $isManager) {
            $meeting_name = $_POST['meeting_name'];
            $room = $_POST['room'];
            $final_datetime = $_POST['date'] . ' ' . $_POST['time'] . ':00';

            $stmt = $conn->prepare("INSERT INTO schedule (meeting_name, meeting_time) VALUES (?, ?)");
            $stmt->bind_param("ss", $meeting_name, $final_datetime);
            if ($stmt->execute()) {
                $new_id = $conn->insert_id;
                $v_stmt = $conn->prepare("INSERT INTO venue (schedule_id, room_name) VALUES (?, ?)");
                $v_stmt->bind_param("is", $new_id, $room);
                $v_stmt->execute();
                $_SESSION['meeting_msg'] = "Meeting '$meeting_name' created successfully!";
            }
        }
        header("Location: user_page.php?view=meetings");
        exit();
    }

    // --- ADMIN: EDIT MEMBER LOGIC ---
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_member']) && $isAdmin) {
        $edit_id = $_POST['edit_id'];
        $new_name = $_POST['name'];
        $new_major = $_POST['major'];
        $new_role = $_POST['role'];

        // 1. Update the 'user' table
        $stmt = $conn->prepare("UPDATE user SET name = ?, major = ? WHERE id = ?");
        $stmt->bind_param("ssi", $new_name, $new_major, $edit_id);
        $stmt->execute();

        // 2. Safely Update the 'role' table (Check if it exists first!)
        $check_stmt = $conn->prepare("SELECT id FROM role WHERE user_id = ?");
        $check_stmt->bind_param("i", $edit_id);
        $check_stmt->execute();
        $result = $check_stmt->get_result();

        if ($result->num_rows > 0) {
            // They already have a role row, so UPDATE it
            $r_stmt = $conn->prepare("UPDATE role SET role = ? WHERE user_id = ?");
            $r_stmt->bind_param("si", $new_role, $edit_id);
        } else {
            // They don't have a role row yet, so INSERT it
            $r_stmt = $conn->prepare("INSERT INTO role (user_id, role) VALUES (?, ?)");
            $r_stmt->bind_param("is", $edit_id, $new_role);
        }
        $r_stmt->execute();
        
        header("Location: user_page.php?view=members");
        exit();
    }

    // --- ADMIN: APPROVE/REJECT REQUEST LOGIC ---
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['process_request'])) {
        if ($isAdmin || $isManager) {
            $target_user = $_POST['target_user_id'];
            $target_schedule = $_POST['target_schedule_id'];
            $new_status = ($_POST['action'] == 'approve') ? 'Absent' : 'Rejected';

            $stmt = $conn->prepare("UPDATE attendance SET status = ? WHERE user_id = ? AND schedule_id = ?");
            $stmt->bind_param("sii", $new_status, $target_user, $target_schedule);
            $stmt->execute();
        }
        header("Location: user_page.php?view=requests");
        exit();
    }

    // --- FETCH PENDING REQUESTS FOR THE VIEW ---
    $sql_requests = "SELECT a.user_id, a.schedule_id, a.status, a.reason, u.name as student_name, s.meeting_name, s.meeting_time 
                     FROM attendance a 
                     JOIN user u ON a.user_id = u.id 
                     JOIN schedule s ON a.schedule_id = s.id 
                     WHERE a.reason IS NOT NULL AND a.reason != ''
                     ORDER BY (CASE WHEN a.status = 'Pending' THEN 1 ELSE 2 END), s.meeting_time DESC";
    $result_requests = $conn->query($sql_requests);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Members List</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="manifest" href="../manifest.json">
</head>

<body class="user-body">
    <div class="user-hero" aria-hidden="true"></div>
    <?php include '../components/header.php'; ?>

    <div class="user-container">
        <div class="overlay"></div>
        <?php require_once '../components/sidebar.php'; ?>

        <main class="main-content-wrapper">

            <?php if ($show_weak_password): ?>
                <div id="weakPasswordBanner" style="background: #fff3cd; color: #856404; padding: 10px 14px; border-radius: 8px; margin-bottom: 18px; text-align: center; font-weight: 600;">
                    Your password is weak. Please reset it from the login screen using "Forgot password".
                </div>
            <?php endif; ?>

            <div id="membersView">
                <div class="member-grid">
                    <?php if ($result && $result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()):
                            $role_raw = $row['role'] ?? 'Member';
                            $role_lower = strtolower($role_raw);
                            $role_class = in_array($role_lower, ['admin','manager','member']) ? "member-role-$role_lower" : 'member-role-default';
                            $initials = strtoupper(mb_substr($row['name'], 0, 1));
                        ?>
                            <article class="member-card" 
                                    data-id="<?php echo htmlspecialchars($row['id']); ?>"
                                    data-name="<?php echo htmlspecialchars($row['name']); ?>"
                                    data-role="<?php echo htmlspecialchars($role_raw); ?>"
                                    data-major="<?php echo htmlspecialchars($row['major'] ?? 'No Major Assigned'); ?>"
                                    data-avatar="<?php echo htmlspecialchars($row['profile_pic'] ?? 'avatar1.jpg'); ?>"
                                    onclick="openMemberModal(this)">
                                
                                <div class="member-avatar"><?php echo $initials; ?></div>
                                
                                <div class="member-info">
                                    <div class="member-name"><?php echo htmlspecialchars($row['name']); ?></div>
                                    <div class="member-id">ID <?php echo htmlspecialchars($row['id']); ?></div>
                                </div>

                                <div class="member-right-section">
                                    <span class="member-role <?php echo $role_class; ?>">
                                        <?php echo htmlspecialchars($role_raw); ?>
                                    </span>

                                    <?php if ($isAdmin): ?>
                                        <button class="edit-pen-btn" onclick="openEditMemberModal(event, '<?php echo htmlspecialchars($row['id']); ?>', '<?php echo addslashes($row['name']); ?>', '<?php echo htmlspecialchars($row['major'] ?? ''); ?>', '<?php echo htmlspecialchars($role_raw); ?>')">
                                            <i class="fas fa-pen"></i>
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </article>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="member-empty">No members found.</div>
                    <?php endif; ?>
                </div>
            </div>

            <div id="meetingsView" style="display: none;">
                <?php if (isset($_SESSION['meeting_msg'])): ?>
                    <div style="background: #d4edda; color: #155724; padding: 10px; border-radius: 8px; margin-bottom: 20px; text-align: center; font-weight: bold;">
                        <?php echo $_SESSION['meeting_msg']; unset($_SESSION['meeting_msg']); ?>
                    </div>
                <?php endif; ?>

                <div class="member-grid">
                    <?php if ($result_schedule && $result_schedule->num_rows > 0): ?>
                    <?php 
                    $current_time = new DateTime(); 
                    while ($row_sch = $result_schedule->fetch_assoc()): 
                        $meeting_time = new DateTime($row_sch['meeting_time']);
                        $formatted_time = $meeting_time->format('M d, Y - h:i A');
                        
                        $isOpen = ($meeting_time > $current_time);
                        $status_text = $isOpen ? 'Open' : 'Closed';
                        $status_class = $isOpen ? 'status-open' : 'status-closed';
                        
                        // NEW: Check if the user already has a record for this meeting
                        $user_status = $row_sch['user_status']; 
                        $has_responded = !empty($user_status);

                        $onclick_attr = "";
                        $cursor_style = "cursor: default; opacity: 0.65;"; 
                        
                        if ($isAdmin || $isManager) {
                            $cursor_style = "cursor: pointer;";
                            $onclick_attr = "onclick=\"openAttendanceModal('{$row_sch['id']}', '" . addslashes($row_sch['meeting_name']) . "', '{$formatted_time}')\"";
                        } else {
                            // Members can only click if the meeting is OPEN and they HAVEN'T responded yet
                            if ($isOpen && !$has_responded) {
                                $cursor_style = "cursor: pointer; opacity: 1;";
                                $onclick_attr = "onclick=\"openRSVPModal('{$row_sch['id']}', '" . addslashes($row_sch['meeting_name']) . "', '{$formatted_time}')\"";
                            } elseif ($has_responded) {
                                $cursor_style = "cursor: default; opacity: 1; border: 2px solid #5a0505;"; // Highlight their active meetings
                            }
                        }
                    ?>
                        <article class="meeting-card" <?php echo $onclick_attr; ?> style="<?php echo $cursor_style; ?>">
                            <div class="meeting-info">
                                <div style="display: flex; justify-content: space-between; align-items: center;">
                                    <h3 class="meeting-club"><?php echo htmlspecialchars($row_sch['meeting_name']); ?></h3>
                                    <?php if ($has_responded): ?>
                                        <span class="status-badge <?php echo strtolower($user_status); ?>" style="font-size: 10px;">
                                            <i class="fas fa-user-check"></i> <?php echo htmlspecialchars($user_status); ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                                <span class="meeting-status <?php echo $status_class; ?>"><?php echo $status_text; ?></span>
                            </div>
                            
                            <div class="meeting-event" style="text-align: right;">
                                <div><?php echo htmlspecialchars($formatted_time); ?></div>
                                <div style="font-size: 13px; color: #5a0505; margin-top: 5px;">
                                    <i class="fas fa-map-marker-alt"></i> 
                                    <?php echo !empty($row_sch['room_name']) ? htmlspecialchars($row_sch['room_name']) : 'Room TBA'; ?>
                                </div>
                            </div>
                        </article>
                    <?php endwhile; ?>
                    <?php else: ?>
                        <div class="member-empty" style="grid-column: 1 / -1; text-align: center;">
                            <i class="fas fa-calendar-alt" style="font-size: 24px; margin-bottom: 10px; color: #5a0505;"></i><br>
                            No meetings scheduled yet.
                        </div>
                    <?php endif; ?>
                </div>

                <div class="absence-btn-container" style="display: flex; flex-direction: column; align-items: center; gap: 15px; margin-top: 40px;">
                    
                    <?php if ($isAdmin || $isManager): ?>
                        <button class="btn-absence" style="background: #5a0505; color: white; border: none; width: auto;" onclick="openCreateMeetingModal()">
                            <i class="fas fa-plus"></i> Create New Meeting
                        </button>
                    <?php endif; ?>

                    <button class="btn-absence" onclick="openGeneralAbsence()">Absence Request</button>
                </div>
            </div>

            <div id="absenceView" style="display: none; height: 100%; align-items: center; justify-content: center; flex-direction: column;">
                <?php if (isset($_SESSION['absence_message'])): ?>
                    <div class="message-banner" style="background: #fff3cd; color: #856404; padding: 10px; border-radius: 8px; margin-bottom: 15px; text-align: center; font-weight: bold; width: 100%; max-width: 500px;">
                        <?php echo $_SESSION['absence_message']; unset($_SESSION['absence_message']); ?>
                    </div>
                <?php endif; ?>

                <div class="absence-form-card" style="width: 100%; max-width: 500px; background: white; padding: 30px; border-radius: 12px; box-shadow: 0 10px 25px rgba(0,0,0,0.1);">
                    <form action="user_page.php" method="POST" id="absenceForm">
                        <div class="form-group flex-row-center" style="margin-bottom: 10px;">
                            <label for="scheduleSelect" style="font-weight: 600; width: 35%;">Select Meeting:</label>
                            <select id="scheduleSelect" name="schedule_id" class="absence-input" style="width: 65%;" required onchange="updateMeetingDetails()">
                                <option value="">-- Choose a meeting --</option>
                                <?php
                                $sql_sch_dropdown = "SELECT schedule.id, schedule.meeting_name, schedule.meeting_time, venue.room_name 
                                                    FROM schedule 
                                                    LEFT JOIN venue ON schedule.id = venue.schedule_id 
                                                    WHERE schedule.meeting_time >= NOW() 
                                                    ORDER BY schedule.meeting_time ASC";
                                $res_sch_drop = $conn->query($sql_sch_dropdown);

                                if ($res_sch_drop && $res_sch_drop->num_rows > 0) {
                                    while ($sch_row = $res_sch_drop->fetch_assoc()) {
                                        $meet_dt = new DateTime($sch_row['meeting_time']);
                                        $time_formatted = $meet_dt->format('M d, Y - h:i A');
                                        $venue_name = !empty($sch_row['room_name']) ? htmlspecialchars($sch_row['room_name']) : 'Room TBA';
                                        
                                        echo "<option value='" . $sch_row['id'] . "' 
                                                data-name='" . htmlspecialchars($sch_row['meeting_name']) . "'
                                                data-time='" . $time_formatted . "'
                                                data-venue='" . $venue_name . "'>" 
                                                . htmlspecialchars($sch_row['meeting_name']) . " - " . $meet_dt->format('d M') . "</option>";
                                    }
                                } else {
                                    echo '<option value="" disabled selected>No upcoming meetings available</option>';
                                }
                                ?>
                            </select>
                        </div>

                        <div id="selectedMeetingDetails" style="display: none; background: #f9f9f9; border-left: 4px solid #5a0505; padding: 12px 15px; margin-bottom: 20px; border-radius: 4px;"></div>

                        <div class="form-group" style="margin-bottom: 25px;">
                            <label for="absenceReason" style="display: block; font-weight: 600; margin-bottom: 8px;">Reason for Absence:</label>
                            <textarea id="absenceReason" name="reason" class="absence-textarea" rows="4" required placeholder="e.g. Family event, Medical..." style="width: 100%; border: 1px solid #ccc;"></textarea>
                        </div>

                        <div class="form-actions" style="display: flex; gap: 10px;">
                            <button type="button" class="btn-cancel" onclick="switchView('meetings')" style="flex: 1;">Back</button>
                            <button type="submit" name="submit_absence" class="btn-save-absence" style="flex: 2; background: #5a0505; color: white;">Submit Request</button>
                        </div>
                    </form>
                </div>
            </div>

            <div id="requestsView" style="display: none; padding-top: 20px;">
                <div class="logs-box" style="max-width: 800px; margin: 0 auto; background: white; padding: 25px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.05);">
                    <h3 style="color: #5a0505; border-bottom: 2px solid #ddd; padding-bottom: 10px; margin-bottom: 20px;">
                        <i class="fas fa-envelope-open-text"></i> Absence Requests
                    </h3>
                    
                    <?php if ($result_requests && $result_requests->num_rows > 0): ?>
                        <?php while ($req = $result_requests->fetch_assoc()): ?>
                            <div class="log-line" style="display: flex; justify-content: space-between; align-items: center; padding: 15px 0; border-bottom: 1px solid #eee;">
                                <div>
                                    <div style="font-weight: 700; color: #1a1a1a; font-size: 15px;">
                                        <?php echo htmlspecialchars($req['student_name']); ?> 
                                        <span style="color: #666; font-size: 13px; font-weight: normal;">requested absence for</span>
                                    </div>
                                    <div style="color: #5a0505; font-weight: 600; font-size: 14px; margin-top: 4px;">
                                        <?php echo htmlspecialchars($req['meeting_name']); ?> (<?php echo date('d M, h:i A', strtotime($req['meeting_time'])); ?>)
                                    </div>
                                    <div style="color: #555; font-size: 13px; margin-top: 6px; background: #f9f9f9; padding: 8px; border-radius: 6px; border-left: 3px solid #ccc;">
                                        <strong>Reason:</strong> <?php echo htmlspecialchars($req['reason']); ?>
                                    </div>
                                </div>

                                <div>
                                    <?php if ($req['status'] == 'Pending'): ?>
                                        <div style="display: flex; gap: 10px;">
                                            <form method="POST" style="margin: 0;">
                                                <input type="hidden" name="target_user_id" value="<?php echo $req['user_id']; ?>">
                                                <input type="hidden" name="target_schedule_id" value="<?php echo $req['schedule_id']; ?>">
                                                <input type="hidden" name="action" value="approve">
                                                <button type="submit" name="process_request" class="btn-small" style="background: #155724; color: white;"><i class="fa-solid fa-check"></i> Approve</button>
                                            </form>
                                            <form method="POST" style="margin: 0;">
                                                <input type="hidden" name="target_user_id" value="<?php echo $req['user_id']; ?>">
                                                <input type="hidden" name="target_schedule_id" value="<?php echo $req['schedule_id']; ?>">
                                                <input type="hidden" name="action" value="reject">
                                                <button type="submit" name="process_request" class="btn-small" style="background: #c62828; color: white;"><i class="fa-solid fa-xmark"></i> Reject</button>
                                            </form>
                                        </div>
                                    <?php else: ?>
                                        <span class="status-badge <?php echo strtolower($req['status']); ?>" style="display: inline-block; padding: 6px 12px; background: #eee; border-radius: 20px; font-size: 12px; font-weight: bold;">
                                            <?php echo $req['status'] == 'Rejected' ? 'Declined' : 'Approved (Absent)'; ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p style="text-align: center; color: #666; padding: 20px;">No requests at this time.</p>
                    <?php endif; ?>
                </div>
            </div>

            <div id="logsView" style="display: none; padding-top: 20px;">
                <div class="logs-box" style="max-width: 800px; margin: 0 auto; background: white; padding: 25px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.05);">
                    <h3 style="color: #5a0505; border-bottom: 2px solid #ddd; padding-bottom: 10px; margin-bottom: 20px;">
                        <i class="fas fa-file-alt"></i> Activity Logs
                    </h3>
                    
                    <?php if ($result_logs && $result_logs->num_rows > 0): ?>
                        <?php while ($log = $result_logs->fetch_assoc()): 
                            $display_name = !empty($log['current_name']) ? $log['current_name'] : $log['user_name'];
                        ?>
                            <div class="log-line" style="padding: 12px 0; border-bottom: 1px solid #eee;">
                                <span style="float: right; font-size: 11px; color: #666;">
                                    <?php echo date('d M, H:i', strtotime($log['created_at'])); ?>
                                </span>
                                <span style="color: #5a0505; font-weight: 700; margin-right: 5px;">
                                    <?php echo htmlspecialchars($display_name); ?>
                                </span>
                                <span style="color: #222; font-weight: bold; font-size: 13px;">
                                    [<?php echo htmlspecialchars($log['action']); ?>]
                                </span>
                                <div style="color: #555; font-size: 13px; margin-top: 4px; padding-left: 5px;">
                                    <?php echo htmlspecialchars($log['details']); ?>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p style="text-align: center; color: #666; padding: 20px;">No activity recorded yet.</p>
                    <?php endif; ?>
                </div>
            </div>

            <div id="memberModal" class="custom-modal">
                <div class="custom-modal-content">
                    <span class="close-modal" onclick="closeMemberModal()">&times;</span>
                    <div class="modal-body">
                        <div class="modal-avatar-wrapper">
                            <img id="modalAvatar" src="" alt="Profile Picture" style="width: 100%; height: 100%; object-fit: cover;">
                        </div>
                        <h2 id="modalName" class="modal-name">[Name]</h2>
                        <p id="modalId" class="modal-text">[ID]</p>
                        <p id="modalRole" class="modal-text font-bold">[Role]</p>
                        <p id="modalClub" class="modal-text">[Club/Major]</p>
                    </div>
                </div>
            </div>

            <div id="attendanceModal" class="custom-modal">
                <div class="custom-modal-content">
                    <span class="close-modal" onclick="closeAttendanceModal()">&times;</span>
                    <div class="modal-body">
                        <h2 id="attMeetingName" class="modal-name">Attendance</h2>
                        <p id="attMeetingTime" class="modal-text"></p>
                        <hr style="margin: 15px 0; opacity: 0.1;">
                        <div id="attendanceList" style="max-height: 400px; overflow-y: auto; padding: 10px;">
                            <p>Loading attendance list...</p>
                        </div>
                    </div>
                </div>
            </div>

            <div id="rsvpModal" class="custom-modal">
                <div class="custom-modal-content" style="text-align: center; max-width: 400px;">
                    <span class="close-modal" onclick="closeRSVPModal()">&times;</span>
                    <div class="modal-body">
                        <i class="fas fa-calendar-check" style="font-size: 40px; color: #5a0505; margin-bottom: 15px;"></i>
                        <h2 id="rsvpMeetingName" class="modal-name">Meeting Title</h2>
                        <p id="rsvpMeetingTime" class="modal-text" style="margin-bottom: 25px; color: #666;"></p>

                        <div style="display: flex; gap: 10px; justify-content: center;">
                            <form method="POST" action="user_page.php" style="flex: 1;">
                                <input type="hidden" name="rsvp_schedule_id" id="rsvpScheduleId" value="">
                                <button type="submit" name="submit_rsvp" class="btn-small" style="width: 100%; background: #155724; color: white; padding: 12px; font-size: 15px;">
                                    <i class="fas fa-check"></i> I'm Going
                                </button>
                            </form>

                            <button type="button" class="btn-small" style="flex: 1; background: #c62828; color: white; padding: 12px; font-size: 15px;" onclick="goToAbsenceForm()">
                                <i class="fas fa-times"></i> Can't Make It
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div id="createMeetingModal" class="custom-modal">
                <div class="custom-modal-content" style="max-width: 450px;">
                    <span class="close-modal" onclick="closeCreateMeetingModal()">&times;</span>
                    <div class="modal-body">
                        <h2 class="modal-name" style="margin-bottom: 20px; text-align: left;">Create New Meeting</h2>
                        <form method="POST" action="user_page.php">
                            <div class="form-group" style="margin-bottom: 15px; text-align: left;">
                                <label style="font-weight: 600; font-size: 14px;">Club / Meeting Name:</label>
                                <select name="meeting_name" class="absence-input" style="width: 100%; border: 1px solid #ccc;" required>
                                    <option value="">-- Select Club --</option>
                                    <option value="Media Club">Media Club</option>
                                    <option value="IT Club">IT Club</option>
                                    <option value="Baking Club">Baking Club</option>
                                    <option value="Art Club">Art Club</option>
                                    <option value="Dance Club">Dance Club</option>
                                </select>
                            </div>
                            <div class="form-group" style="margin-bottom: 15px; text-align: left;">
                                <label style="font-weight: 600; font-size: 14px;">Room / Venue:</label>
                                <input type="text" name="room" class="absence-input" style="width: 100%; border: 1px solid #ccc;" placeholder="e.g. Room A" required>
                            </div>
                            <div style="display: flex; gap: 15px; margin-bottom: 25px; text-align: left;">
                                <div style="flex: 1;">
                                    <label style="font-weight: 600; font-size: 14px;">Date:</label>
                                    <input type="date" name="date" style="width: 100%; padding: 8px; border-radius: 5px; border: 1px solid #ccc;" required>
                                </div>
                                <div style="flex: 1;">
                                    <label style="font-weight: 600; font-size: 14px;">Time:</label>
                                    <input type="time" name="time" style="width: 100%; padding: 8px; border-radius: 5px; border: 1px solid #ccc;" required>
                                </div>
                            </div>
                            <button type="submit" name="create_meeting" class="btn-save-absence" style="width: 100%;">Create Meeting</button>
                        </form>
                    </div>
                </div>
            </div>

            <div id="editMemberModal" class="custom-modal">
                <div class="custom-modal-content" style="max-width: 400px;">
                    <span class="close-modal" onclick="closeEditMemberModal()">&times;</span>
                    <div class="modal-body" style="text-align: left;">
                        <h2 class="modal-name" style="margin-bottom: 20px;">Edit Member</h2>
                        <form method="POST" action="user_page.php">
                            <input type="hidden" name="edit_id" id="editMemberId">
                            
                            <div class="form-group" style="margin-bottom: 15px;">
                                <label style="font-weight: 600; font-size: 14px;">Full Name:</label>
                                <input type="text" name="name" id="editMemberName" class="absence-input" style="width: 100%; border: 1px solid #ccc;" required>
                            </div>
                            <div class="form-group" style="margin-bottom: 15px;">
                                <label style="font-weight: 600; font-size: 14px;">Major:</label>
                                <select name="major" id="editMemberMajor" class="absence-input" style="width: 100%; border: 1px solid #ccc;" required>
                                    <option value="software engineering">Software Engineering</option>
                                    <option value="data science">Data Science</option>
                                    <option value="digital business">Digital Business</option>
                                    <option value="information technology">Information Tech</option>
                                    <option value="accounting">Accounting</option>
                                </select>
                            </div>
                            <div class="form-group" style="margin-bottom: 25px;">
                                <label style="font-weight: 600; font-size: 14px;">Role:</label>
                                <select name="role" id="editMemberRole" class="absence-input" style="width: 100%; border: 1px solid #ccc;" required>
                                    <option value="Member">Member</option>
                                    <option value="Manager">Manager</option>
                                    <option value="Admin">Admin</option>
                                </select>
                            </div>
                            <button type="submit" name="update_member" class="btn-save-absence" style="width: 100%;">Save Changes</button>
                        </form>
                    </div>
                </div>
            </div>

        </main>
    </div>

<script>
    // --- SIDEBAR & MOBILE MENU ---
    const menuBtn = document.querySelector('.topbar-menu');
    const sidebar = document.querySelector('.sidebar');
    const overlay = document.querySelector('.overlay');
    if (menuBtn && sidebar && overlay) {
        menuBtn.addEventListener('click', () => {
            sidebar.classList.toggle('open');
            overlay.classList.toggle('active');
        });
        overlay.addEventListener('click', () => {
            sidebar.classList.remove('open');
            overlay.classList.remove('active');
        });
    }

    // --- MEMBER MODAL ---
    function openMemberModal(element) {
        const name = element.getAttribute('data-name');
        const id = element.getAttribute('data-id');
        const role = element.getAttribute('data-role');
        const major = element.getAttribute('data-major');
        const avatar = element.getAttribute('data-avatar');

        document.getElementById('modalName').innerText = name;
        document.getElementById('modalId').innerText = "ID " + id;
        document.getElementById('modalRole').innerText = role;
        document.getElementById('modalClub').innerText = major;

        const avatarFile = (avatar || 'avatar1.jpg').split(/[/\\]/).pop();
        document.getElementById('modalAvatar').src = '../assets/images/' + avatarFile;
        document.getElementById('memberModal').classList.add('show');
    }

    function closeMemberModal() {
        document.getElementById('memberModal').classList.remove('show');
    }

    // --- ADMIN MODALS ---
    function openCreateMeetingModal() {
        document.getElementById('createMeetingModal').classList.add('show');
    }
    function closeCreateMeetingModal() {
        document.getElementById('createMeetingModal').classList.remove('show');
    }

    function openEditMemberModal(event, id, name, major, role) {
        event.stopPropagation(); // Stops the card click from opening the normal view modal
        document.getElementById('editMemberId').value = id;
        document.getElementById('editMemberName').value = name;
        
        // Auto-select dropdowns safely
        const majorSelect = document.getElementById('editMemberMajor');
        if (major) {
            for(let i=0; i<majorSelect.options.length; i++) {
                if(majorSelect.options[i].value.toLowerCase() === major.toLowerCase()) {
                    majorSelect.selectedIndex = i; break;
                }
            }
        }
        document.getElementById('editMemberRole').value = role || 'Member';
        
        document.getElementById('editMemberModal').classList.add('show');
    }
    function closeEditMemberModal() {
        document.getElementById('editMemberModal').classList.remove('show');
    }

    // --- ATTENDANCE MODAL ---
    function openAttendanceModal(scheduleId, meetingName, meetingTime) {
        document.getElementById('attMeetingName').innerText = meetingName;
        document.getElementById('attMeetingTime').innerText = meetingTime;
        document.getElementById('attendanceList').innerHTML = '<p style="text-align:center; padding:20px;">Loading attendees...</p>';
        document.getElementById('attendanceModal').classList.add('show');

        fetch('../auth/get_attendance.php?schedule_id=' + scheduleId)
            .then(response => response.text())
            .then(data => {
                document.getElementById('attendanceList').innerHTML = data;
            })
            .catch(err => {
                document.getElementById('attendanceList').innerHTML = '<p style="color:red;">Error loading attendance.</p>';
            });
    }

    function closeAttendanceModal() {
        document.getElementById('attendanceModal').classList.remove('show');
    }

    // --- RSVP MODAL ---
    function openRSVPModal(scheduleId, meetingName, meetingTime) {
        document.getElementById('rsvpMeetingName').innerText = meetingName;
        document.getElementById('rsvpMeetingTime').innerText = meetingTime;
        document.getElementById('rsvpScheduleId').value = scheduleId;
        document.getElementById('rsvpModal').classList.add('show');
    }

    function closeRSVPModal() {
        document.getElementById('rsvpModal').classList.remove('show');
    }

    // --- ABSENCE FORM LOGIC ---
    function updateMeetingDetails() {
        const select = document.getElementById('scheduleSelect');
        const detailsDiv = document.getElementById('selectedMeetingDetails');

        if (select.value === "") {
            detailsDiv.style.display = 'none'; 
            detailsDiv.innerHTML = "";
        } else {
            const option = select.options[select.selectedIndex];
            const name = option.getAttribute('data-name');
            const time = option.getAttribute('data-time');
            const venue = option.getAttribute('data-venue');
            
            detailsDiv.style.display = 'block';
            detailsDiv.innerHTML = `
                <div style="font-weight: 700; font-size: 16px; color: #1a1a1a;">${name}</div>
                <div style="font-size: 13px; color: #666; margin-top: 4px;">
                    <i class="fas fa-clock" style="margin-right: 4px;"></i> ${time}
                </div>
                <div style="font-size: 13px; color: #5a0505; margin-top: 4px; font-weight: 500;">
                    <i class="fas fa-map-marker-alt" style="margin-right: 4px;"></i> ${venue}
                </div>
            `;
        }
    }

    function openGeneralAbsence() {
        switchView('absence');
        document.getElementById('scheduleSelect').value = ""; 
        updateMeetingDetails(); 
        document.getElementById('absenceReason').value = ""; 
    }

    function goToAbsenceForm() {
        const scheduleId = document.getElementById('rsvpScheduleId').value;
        closeRSVPModal();
        switchView('absence');
        setTimeout(() => {
            document.getElementById('scheduleSelect').value = scheduleId;
            updateMeetingDetails(); 
        }, 50);
    }

    // --- VIEW SWITCHER ---
    function switchView(viewName) {
        // 1. Reset all sidebar links
        ['members', 'meetings', 'requests', 'logs'].forEach(id => {
            const el = document.getElementById('nav-' + id);
            if (el) el.classList.remove('active');
        });

        if (document.getElementById('nav-' + viewName)) {
            document.getElementById('nav-' + viewName).classList.add('active');
        }

        // 2. Hide ALL containers
        document.getElementById('membersView').style.display = 'none';
        document.getElementById('meetingsView').style.display = 'none';
        document.getElementById('absenceView').style.display = 'none';
        document.getElementById('requestsView').style.display = 'none';
        document.getElementById('logsView').style.display = 'none';

        // 3. Show selected container
        if (viewName === 'absence') {
            document.getElementById('absenceView').style.display = 'flex'; 
        } else {
            const target = document.getElementById(viewName + 'View');
            if (target) target.style.display = 'block';
        }

        // 4. Update Title
        const titleElement = document.querySelector('.page-title');
        if (viewName === 'members') titleElement.innerText = 'MEMBERS LIST';
        else if (viewName === 'meetings') titleElement.innerText = 'MEETING LIST';
        else if (viewName === 'absence') titleElement.innerText = 'ABSENCE REQUEST';
        else if (viewName === 'requests') titleElement.innerText = 'PENDING REQUESTS';
        else if (viewName === 'logs') titleElement.innerText = 'ACTIVITY LOGS';
    }

    // --- GLOBAL EVENT LISTENERS ---
    window.addEventListener('click', function(event) {
        const modals = ['memberModal', 'attendanceModal', 'rsvpModal', 'createMeetingModal', 'editMemberModal'];
        modals.forEach(modalId => {
            const modal = document.getElementById(modalId);
            if (event.target === modal) {
                modal.classList.remove('show');
            }
        });
    });

    // Check URL parameters on load
    window.onload = function() {
        const urlParams = new URLSearchParams(window.location.search);
        const view = urlParams.get('view');
        if (view) {
            switchView(view);
        }

        const weakBanner = document.getElementById('weakPasswordBanner');
        if (weakBanner) {
            setTimeout(() => {
                weakBanner.style.display = 'none';
            }, 5000);
        }
    };
</script>
</body>
</html>