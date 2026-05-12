<?php
    session_start();
    require_once '../components/config.php';

    // 1. Security Check
    if (!isset($_SESSION['id'])) {
        header("Location: ../index.php");
        exit();
    }

    // 2. Determine User Role
    $role = isset($_SESSION['role']) ? strtolower($_SESSION['role']) : '';
    $isAdmin = ($role == 'admin');
    $isManager = ($role == 'manager');
    $can_edit = ($isAdmin);

    // 3. Fetch Users Data
    $sql = "SELECT user.id, user.name, user.major, user.profile_pic, role.role
            FROM user 
            LEFT JOIN role ON user.id = role.user_id 
            ORDER BY user.id ASC";
    $result = $conn->query($sql);

    // 4. Fetch Schedule/Meetings Data AND Venue Data
    $sql_schedule = "SELECT schedule.id, schedule.meeting_name, schedule.meeting_time, venue.room_name 
                     FROM schedule 
                     LEFT JOIN venue ON schedule.id = venue.schedule_id 
                     ORDER BY schedule.meeting_time ASC";
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

    // --- UPDATED ABSENCE REQUEST FORM HANDLING ---
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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Members List</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body class="user-body">
    <div class="user-hero" aria-hidden="true"></div>
    <header class="user-topbar">
        <button class="topbar-menu" type="button" aria-label="Open menu">
            <i class="fas fa-bars"></i>
        </button>
        <h1 class="page-title">Members List</h1>
        <a href="../auth/logout.php" class="topbar-logout">
            <i class="fas fa-sign-out-alt"></i> Log out
        </a>
    </header>

    <div class="user-container">
        <div class="overlay"></div>
        <?php require_once '../components/sidebar.php'; ?>

        <main class="main-content-wrapper">

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
                                    onclick="openMemberModal(this)"
                                    style="cursor: pointer;">
                                <div class="member-avatar"><?php echo $initials; ?></div>
                                <div class="member-info">
                                    <div class="member-name"><?php echo htmlspecialchars($row['name']); ?></div>
                                    <div class="member-id">ID <?php echo htmlspecialchars($row['id']); ?></div>
                                </div>
                                <span class="member-role <?php echo $role_class; ?>"><?php echo htmlspecialchars($role_raw); ?></span>
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
                            
                            $onclick_attr = "";
                            $cursor_style = "cursor: default; opacity: 0.65;"; 
                            
                            if ($isOpen) {
                                $cursor_style = "cursor: pointer;";
                                if ($isAdmin || $isManager) {
                                    $onclick_attr = "onclick=\"openAttendanceModal('{$row_sch['id']}', '" . addslashes($row_sch['meeting_name']) . "', '{$formatted_time}')\"";
                                } else {
                                    $onclick_attr = "onclick=\"openRSVPModal('{$row_sch['id']}', '" . addslashes($row_sch['meeting_name']) . "', '{$formatted_time}')\"";
                                }
                            }
                        ?>
                            <article class="meeting-card" <?php echo $onclick_attr; ?> style="<?php echo $cursor_style; ?>">
                                <div class="meeting-info">
                                    <h3 class="meeting-club"><?php echo htmlspecialchars($row_sch['meeting_name']); ?></h3>
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
                        <div class="member-empty">
                            <i class="fas fa-calendar-alt" style="font-size: 24px; margin-bottom: 10px; color: #5a0505;"></i><br>
                            No meetings scheduled yet.
                        </div>
                    <?php endif; ?>
                </div>

                <div class="absence-btn-container">
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
        document.getElementById('nav-members').classList.remove('active');
        document.getElementById('nav-meetings').classList.remove('active');

        if (document.getElementById('nav-' + viewName)) {
            document.getElementById('nav-' + viewName).classList.add('active');
        }

        document.getElementById('membersView').style.display = 'none';
        document.getElementById('meetingsView').style.display = 'none';
        document.getElementById('absenceView').style.display = 'none';

        if (viewName === 'absence') {
            document.getElementById('absenceView').style.display = 'flex'; 
        } else {
            document.getElementById(viewName + 'View').style.display = 'block';
        }

        const titleElement = document.querySelector('.page-title');
        if (viewName === 'members') {
            titleElement.innerText = 'MEMBERS LIST';
        } else if (viewName === 'meetings') {
            titleElement.innerText = 'MEETING LIST';
        } else if (viewName === 'absence') {
            titleElement.innerText = 'ABSENCE REQUEST';
        }
    }

    // --- GLOBAL EVENT LISTENERS ---
    
    // Close modals when clicking outside
    window.addEventListener('click', function(event) {
        const memModal = document.getElementById('memberModal');
        const attModal = document.getElementById('attendanceModal');
        const rsvpModal = document.getElementById('rsvpModal');
        
        if (event.target === memModal) closeMemberModal();
        if (event.target === attModal) closeAttendanceModal();
        if (event.target === rsvpModal) closeRSVPModal();
    });

    // Check URL parameters on load
    window.onload = function() {
        const urlParams = new URLSearchParams(window.location.search);
        const view = urlParams.get('view');
        if (view) {
            switchView(view);
        }
    };
</script>
</body>
</html>