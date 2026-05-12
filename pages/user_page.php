<?php
    session_start();
    // Path updated to point to components folder
    require_once '../components/config.php';



    // 1. Security Check - Matches your login session key 'id'
    if (!isset($_SESSION['id'])) {
        header("Location: ../index.php");
        exit();
    }

    // 2. Determine User Role
    $role = isset($_SESSION['role']) ? strtolower($_SESSION['role']) : '';
    $isAdmin = ($role == 'admin');
    $isManager = ($role == 'manager');
    
    // Admin AND Manager can see edit icons
    $can_edit = ($isAdmin);

    // 3. Fetch Users Data - Using your specific table names
    $sql = "SELECT user.id, user.name, user.major, user.profile_pic, role.role
            FROM user 
            LEFT JOIN role ON user.id = role.user_id 
            ORDER BY user.id ASC";
    $result = $conn->query($sql);

    // 4. Fetch Schedule/Meetings Data
    $sql_schedule = "SELECT id, meeting_name, meeting_time FROM schedule ORDER BY meeting_time ASC";
    $result_schedule = $conn->query($sql_schedule);

    
    // --- ABSENCE REQUEST FORM HANDLING ---
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_absence'])) {
        $user_id = $_SESSION['id']; // Gets the logged-in user
        $schedule_id = $_POST['schedule_id']; // Gets the selected meeting
        $reason = trim($_POST['reason']);
        $status = 'Absent';

        // Prepare and insert into the attendance table
        $insert_stmt = $conn->prepare("INSERT INTO attendance (user_id, status, schedule_id, reason) VALUES (?, ?, ?, ?)");
        $insert_stmt->bind_param("isis", $user_id, $status, $schedule_id, $reason);
        
        if ($insert_stmt->execute()) {
            $_SESSION['absence_message'] = "Your absence request has been successfully submitted.";
        } else {
            $_SESSION['absence_message'] = "Error submitting request. Please try again.";
        }
        $insert_stmt->close();
        
        // Refresh the page and tell it to stay on the absence view
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
        <a href="../index.php" class="topbar-logout">
            <i class="fas fa-sign-out-alt"></i> Log out
        </a>
    </header>

    <div class="user-container">
        <div class="overlay"></div>
        <?php require_once '../components/sidebar.php'; ?>

        <main class="main-content-wrapper">

            <div id= "membersView">
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
        
                <div class="member-grid">
                    <?php if ($result_schedule && $result_schedule->num_rows > 0): ?>
                        <?php 
                        // Get the current time to check if a meeting is Open or Closed
                        $current_time = new DateTime(); 
                        
                        while ($row_sch = $result_schedule->fetch_assoc()): 
                            // Format the date nicely (e.g., Dec 20, 2025 - 02:00 PM)
                            $meeting_time = new DateTime($row_sch['meeting_time']);
                            $formatted_time = $meeting_time->format('M d, Y - h:i A');
                            
                            // Logic to determine Status
                            if ($meeting_time > $current_time) {
                                $status_text = 'Open';
                                $status_class = 'status-open';
                            } else {
                                $status_text = 'Closed';
                                $status_class = 'status-closed';
                            }
                        ?>
                            <article class="meeting-card" data-id="<?php echo htmlspecialchars($row_sch['id']); ?>">
                                <div class="meeting-info">
                                    <h3 class="meeting-club"><?php echo htmlspecialchars($row_sch['meeting_name']); ?></h3>
                                    <span class="meeting-status <?php echo $status_class; ?>"><?php echo $status_text; ?></span>
                                </div>
                                <div class="meeting-event"><?php echo htmlspecialchars($formatted_time); ?></div>
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
                    <button class="btn-absence" onclick="switchView('absence')">Absence Request</button>
                </div>

            </div>


            <!-- modal structure for member details -->
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

            <div id="absenceView" style="display: none; height: 100%; align-items: center; justify-content: center; flex-direction: column;">
        
                <?php if (isset($_SESSION['absence_message'])): ?>
                    <div style="background-color: #d4edda; color: #155724; padding: 12px 20px; border-radius: 8px; margin-bottom: 20px; font-family: 'Poppins', sans-serif; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
                        <?php echo $_SESSION['absence_message']; unset($_SESSION['absence_message']); ?>
                    </div>
                <?php endif; ?>

                <div class="absence-form-card">
                    <form action="user_page.php" method="POST" id="absenceForm">
                        
                        <div class="form-group flex-row-center">
                            <label for="scheduleSelect">Meeting :</label>
                            <select id="scheduleSelect" name="schedule_id" class="absence-input" required>
                                <option value="">Select a meeting...</option>
                                <?php
                                // Fetch upcoming meetings directly from the database for the dropdown
                                $sql_sch_dropdown = "SELECT id, meeting_name, meeting_time FROM schedule WHERE meeting_time >= NOW() ORDER BY meeting_time ASC";
                                $res_sch_drop = $conn->query($sql_sch_dropdown);
                                if ($res_sch_drop && $res_sch_drop->num_rows > 0) {
                                    while ($sch_row = $res_sch_drop->fetch_assoc()) {
                                        $time_formatted = (new DateTime($sch_row['meeting_time']))->format('M d, Y - h:i A');
                                        echo '<option value="' . $sch_row['id'] . '">' . htmlspecialchars($sch_row['meeting_name']) . ' (' . $time_formatted . ')</option>';
                                    }
                                } else {
                                    echo '<option value="">No upcoming meetings</option>';
                                }
                                ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="absenceReason" style="display: block; text-align: center; margin-bottom: 10px;">Reason:</label>
                            <textarea id="absenceReason" name="reason" class="absence-textarea" rows="5" required placeholder="Please explain why you cannot attend..."></textarea>
                        </div>

                        <div style="text-align: center; margin-top: 25px;">
                            <button type="button" class="btn-cancel" onclick="switchView('meetings')">Back</button>
                            
                            <button type="submit" name="submit_absence" class="btn-save-absence">Save</button>
                        </div>

                    </form>
                </div>
            </div>

        </main>
    </div>
<script>
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


    // Function to open and populate the modal
    function openMemberModal(element) {
        // Get data from the clicked card
        const name = element.getAttribute('data-name');
        const id = element.getAttribute('data-id');
        const role = element.getAttribute('data-role');
        const major = element.getAttribute('data-major');
        const avatar = element.getAttribute('data-avatar');

        // Inject data into the modal text elements
        document.getElementById('modalName').innerText = name;
        document.getElementById('modalId').innerText = "ID " + id;
        document.getElementById('modalRole').innerText = role;
        document.getElementById('modalClub').innerText = major;

        // Set the image source (Adjust the folder path if your avatars are stored elsewhere)
        document.getElementById('modalAvatar').src = '../assets/images/' + avatar;

        // Show the modal
        document.getElementById('memberModal').classList.add('show');
    }

    // Function to close the modal
    function closeMemberModal() {
        document.getElementById('memberModal').classList.remove('show');
    }

    // Optional: Close modal when clicking outside the white box
    window.addEventListener('click', function(event) {
        const modal = document.getElementById('memberModal');
        if (event.target === modal) {
            closeMemberModal();
        }
    });


    // Function to switch between Members and Meetings views
    function switchView(viewName) {
        // 1. Remove the 'active' highlight from all sidebar links
        document.getElementById('nav-members').classList.remove('active');
        document.getElementById('nav-meetings').classList.remove('active');

        // If clicking a sidebar item, make it active (absence isn't in the sidebar)
        if (document.getElementById('nav-' + viewName)) {
            document.getElementById('nav-' + viewName).classList.add('active');
        }

        // 2. Hide all view containers
        document.getElementById('membersView').style.display = 'none';
        document.getElementById('meetingsView').style.display = 'none';
        document.getElementById('absenceView').style.display = 'none';

        // 3. Show only the requested container
        if (viewName === 'absence') {
            // Flex is used here to center the card vertically and horizontally
            document.getElementById('absenceView').style.display = 'flex'; 
        } else {
            document.getElementById(viewName + 'View').style.display = 'block';
        }

        // 4. Update the text in the Topbar Title
        const titleElement = document.querySelector('.page-title');
        if (viewName === 'members') {
            titleElement.innerText = 'MEMBERS LIST';
        } else if (viewName === 'meetings') {
            titleElement.innerText = 'MEETING LIST';
        } else if (viewName === 'absence') {
            titleElement.innerText = 'ABSENCE REQUEST';
        }
    }


    // Automatically switch to the correct view if the URL demands it (e.g., after form submit)
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