<?php 
session_start();
require_once '../components/config.php'; 
require_once '../components/header.php'; 

// --- 1. BACKEND ROUTE: Handle Status Update Requests via AJAX Fetch ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['action']) && $_GET['action'] === 'forward_inquiry') {
    while (ob_get_level()) { ob_end_clean(); } // Clear layout fragments to isolate JSON
    header('Content-Type: application/json');
    
    $input_data = json_decode(file_get_contents('php://input'), true);
    $inquiry_id = isset($input_data['id']) ? intval($input_data['id']) : 0;

    if ($inquiry_id > 0) {
        if (!isset($conn) || $conn->connect_error) {
            echo json_encode(['success' => false, 'error' => 'Database connection state missing.']);
            exit();
        }

        // Target table schema 'visitor_inquiries' from image_a13466.png
        $stmt_update = $conn->prepare("UPDATE visitor_inquiries SET status = 'Answered' WHERE id = ?");
        if ($stmt_update) {
            $stmt_update->bind_param("i", $inquiry_id);
            if ($stmt_update->execute()) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'error' => $stmt_update->error]);
            }
            $stmt_update->close();
        } else {
            echo json_encode(['success' => false, 'error' => 'SQL prepare failed: ' . $conn->error]);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'Invalid ID payload parameters.']);
    }
    exit(); // Halt processing to keep the JSON footprint clean
}

$inquiries_list = [];

// --- 2. DATABASE FETCH: Order by ASC to bring Nicholas to the front ---
$sql_inquiries = "SELECT i.*, e.event_name 
                  FROM visitor_inquiries i
                  LEFT JOIN dummy_events e ON i.event_id = e.id
                  ORDER BY i.created_at ASC"; 
$result_inquiries = $conn->query($sql_inquiries);

if ($result_inquiries && $result_inquiries->num_rows > 0) {
    while ($row = $result_inquiries->fetch_assoc()) {
        $inquiries_list[] = $row;
    }
}
?>

<aside class="sidebar">
    <div class="sidebar-brand">
        <div class="brand-text">
            <strong>EVENTHUB</strong>
            <strong>PORTAL</strong>
        </div>
    </div>
    
    <div class="sidebar-nav">
        <a href="visitors_page.php" class="nav-link"><i class="fas fa-home"></i> Events List</a>
        <a href="generate_code.php" class="nav-link"><i class="fas fa-key"></i> Generate Code</a>
        <a href="inquiries_page.php" class="nav-link active"><i class="fas fa-question-circle"></i> Inquiries List</a>
        <div class="nav-spacer"></div>
        <a href="#" class="nav-link"><i class="fas fa-cog"></i> Settings</a>
        <a href="../auth/logout.php" class="nav-link"><i class="fas fa-sign-out-alt"></i> Log out</a>
    </div>
</aside>

<main class="main-content-wrapper" style="padding-top: 90px; padding-left: 24px; padding-right: 24px; box-sizing: border-box; width: 100%;">
    
    <div class="member-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 24px; align-items: start; width: 100%;">
        <?php if (!empty($inquiries_list)): ?>
            <?php foreach ($inquiries_list as $inquiry): 
                $is_pending = (strtolower($inquiry['status']) === 'pending');
                $badge_bg = $is_pending ? '#fff3cd' : '#d4edda';
                $badge_color = $is_pending ? '#856404' : '#155724';
                
                // Truncate message smoothly for the card snippet box
                $raw_message = $inquiry['message'];
                $short_message = (strlen($raw_message) > 45) ? substr($raw_message, 0, 42) . '...' : $raw_message;
            ?>
                
                <article class="meeting-card" 
                         id="inquiry-card-<?php echo $inquiry['id']; ?>"
                         style="align-self: start; display: flex; flex-direction: column; padding: 22px; background: #ffffff; border-radius: 16px; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05); border: 1px solid rgba(224, 224, 224, 0.6); box-sizing: border-box; cursor: pointer; transition: transform 0.2s, box-shadow 0.2s;"
                         onclick="openInquiryModal(<?php echo htmlspecialchars(json_encode($inquiry)); ?>, this)"
                         onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 20px rgba(0,0,0,0.08)';"
                         onmouseout="this.style.transform='none'; this.style.boxShadow='0 4px 15px rgba(0, 0, 0, 0.05)';">
                    
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 14px; width: 100%; gap: 10px;">
                        <h3 style="font-family: 'Montserrat', sans-serif; font-weight: 700; color: #111; font-size: 16px; margin: 0;">
                            <?php echo htmlspecialchars($inquiry['event_name'] ?? 'Tech & AI Summit'); ?>
                        </h3>
                        <span class="status-badge-element" style="font-family: 'Montserrat', sans-serif; font-size: 10px; font-weight: bold; padding: 3px 8px; border-radius: 10px; background: <?php echo $badge_bg; ?>; color: <?php echo $badge_color; ?>; white-space: nowrap;">
                            <?php echo htmlspecialchars($inquiry['status']); ?>
                        </span>
                    </div>
                    
                    <div style="font-family: 'Montserrat', sans-serif; font-size: 13px; color: #333; margin-bottom: 6px;">
                        <i class="fas fa-user" style="color: #5a0505; margin-right: 8px;"></i><?php echo htmlspecialchars($inquiry['visitor_name']); ?>
                    </div>
                    
                    <div style="font-family: 'Montserrat', sans-serif; font-size: 13px; color: #666; margin-bottom: 16px;">
                        <i class="fas fa-envelope" style="color: #5a0505; margin-right: 8px;"></i><?php echo htmlspecialchars($inquiry['visitor_email']); ?>
                    </div>

                    <div style="background: #fdfdfd; border-radius: 20px; padding: 10px 16px; border: 1.5px solid #eef0f2; box-sizing: border-box; width: 100%;">
                        <p style="font-family: 'Montserrat', sans-serif; font-size: 12px; color: #444; margin: 0; line-height: 1.4; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                            <strong>Question:</strong> <?php echo htmlspecialchars($short_message); ?>
                        </p>
                    </div>

                </article>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</main>

<div id="inquiryDetailModal" class="faq-modal-overlay" style="display: none; position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background: rgba(0,0,0,0.4); backdrop-filter: blur(3px); z-index: 999999; align-items: center; justify-content: center;">
    <div class="faq-modal-card-v2" style="background: #ffffff; width: 90%; max-width: 650px; height: auto; border-radius: 16px; padding: 35px; box-sizing: border-box; position: relative; display: flex; flex-direction: column; border: 1px solid #e0e0e0; box-shadow: 0 12px 32px rgba(0,0,0,0.15);">
        
        <button type="button" onclick="closeInquiryModal()" style="position: absolute; top: 20px; right: 20px; background: #D4AF37; border: none; width: 28px; height: 28px; border-radius: 50%; color: #ffffff; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 14px; font-weight: bold;">
            &times;
        </button>

        <div style="width: 100%;">
            <h2 id="modalEventName" style="font-family: 'Montserrat', sans-serif; font-weight: 800; color: #5a0505; font-size: 24px; margin: 0 0 4px 0; text-transform: uppercase;">Event Title</h2>
            <p style="font-family: 'Montserrat', sans-serif; font-size: 11px; font-weight: 700; color: #999; text-transform: uppercase; letter-spacing: 0.5px; margin: 0 0 25px 0;">Inquiry Details Panel</p>
            
            <div style="display: flex; gap: 40px; margin-bottom: 25px; width: 100%;">
                <div>
                    <span style="font-family: 'Montserrat', sans-serif; font-size: 11px; font-weight: 700; color: #666; display: block; margin-bottom: 6px;">VISITOR</span>
                    <div id="modalVisitorName" style="font-family: 'Montserrat', sans-serif; font-size: 15px; color: #111; font-weight: 600;">
                        <i class="fas fa-user" style="color: #5a0505; margin-right: 6px;"></i>Name
                    </div>
                </div>
                <div>
                    <span style="font-family: 'Montserrat', sans-serif; font-size: 11px; font-weight: 700; color: #666; display: block; margin-bottom: 6px;">EMAIL ADDRESS</span>
                    <div id="modalVisitorEmail" style="font-family: 'Montserrat', sans-serif; font-size: 15px; color: #111; font-weight: 600;">
                        <i class="fas fa-envelope" style="color: #5a0505; margin-right: 6px;"></i>Email
                    </div>
                </div>
            </div>

            <div style="background: #ffffff; border: 1.5px solid #d0e1f9; border-radius: 12px; padding: 20px; margin-bottom: 30px; min-height: 180px; max-height: 280px; overflow-y: auto; box-sizing: border-box;">
                <p id="modalFullMessage" style="font-family: 'Montserrat', sans-serif; font-size: 14px; color: #333; line-height: 1.6; margin: 0; font-weight: 500; white-space: pre-wrap; word-break: break-word; overflow-wrap: break-word;">
                    Complete Question Text Body
                </p>
            </div>
        </div>

        <div style="display: flex; gap: 15px; width: 100%; justify-content: center; align-items: center; border-top: 1px solid #eee; padding-top: 22px;">
            <button id="modalReplyBtn" type="button" onclick="alert('Opening direct response stream...')" style="font-family: 'Montserrat', sans-serif; font-weight: 700; font-size: 13px; background: #5a0505; color: #ffffff; border: none; padding: 14px 28px; border-radius: 25px; cursor: pointer; display: flex; align-items: center; gap: 8px; letter-spacing: 0.3px; transition: background 0.2s, opacity 0.2s;">
                <i class="fas fa-reply"></i> REPLY DIRECTLY
            </button>
            
            <button id="modalForwardBtn" type="button" onclick="forwardInquiryToStaff()" style="font-family: 'Montserrat', sans-serif; font-weight: 700; font-size: 13px; background: #D4AF37; color: #111111; border: none; padding: 14px 28px; border-radius: 25px; cursor: pointer; display: flex; align-items: center; gap: 8px; letter-spacing: 0.3px; transition: background 0.2s, opacity 0.2s;">
                FORWARD TO STAFF <i class="fas fa-arrow-right"></i>
            </button>
        </div>

    </div>
</div>

<script>
let activeInquiryRecord = null;
let activeCardElement = null; 

function openInquiryModal(inquiryObject, element) {
    activeInquiryRecord = inquiryObject;
    activeCardElement = element; 
    
    document.getElementById('modalEventName').innerText = inquiryObject.event_name || 'Cultural Festival 2026';
    document.getElementById('modalVisitorName').innerHTML = `<i class="fas fa-user" style="color: #5a0505; margin-right: 6px;"></i> ${inquiryObject.visitor_name}`;
    document.getElementById('modalVisitorEmail').innerHTML = `<i class="fas fa-envelope" style="color: #5a0505; margin-right: 6px;"></i> ${inquiryObject.visitor_email}`;
    document.getElementById('modalFullMessage').innerText = inquiryObject.message;

    const replyBtn = document.getElementById('modalReplyBtn');
    const forwardBtn = document.getElementById('modalForwardBtn');

    // FIX A: Check if status is already 'Answered' on modal display load
    if (inquiryObject.status.toLowerCase() === 'answered') {
        grayOutButtons(replyBtn, forwardBtn);
    } else {
        resetButtons(replyBtn, forwardBtn);
    }

    const modal = document.getElementById('inquiryDetailModal');
    modal.style.display = 'flex';
}

function closeInquiryModal() {
    document.getElementById('inquiryDetailModal').style.display = 'none';
    activeInquiryRecord = null;
    activeCardElement = null;
}

// Helper function to dynamically freeze and gray out action components
function grayOutButtons(reply, forward) {
    reply.disabled = true;
    reply.style.background = '#cccccc';
    reply.style.color = '#777777';
    reply.style.cursor = 'not-allowed';
    reply.style.opacity = '0.6';

    forward.disabled = true;
    forward.style.background = '#e2e8f0';
    forward.style.color = '#94a3b8';
    forward.style.cursor = 'not-allowed';
    forward.style.opacity = '0.6';
    forward.innerHTML = "FORWARD TO STAFF <i class='fas fa-check-circle'></i>";
}

// Helper function to restore active styles for 'Pending' rows
function resetButtons(reply, forward) {
    reply.disabled = false;
    reply.style.background = '#5a0505';
    reply.style.color = '#ffffff';
    reply.style.cursor = 'pointer';
    reply.style.opacity = '1';

    forward.disabled = false;
    forward.style.background = '#D4AF37';
    forward.style.color = '#111111';
    forward.style.cursor = 'pointer';
    forward.style.opacity = '1';
    forward.innerHTML = "FORWARD TO STAFF <i class='fas fa-arrow-right'></i>";
}

function forwardInquiryToStaff() {
    if (!activeInquiryRecord) return;

    const replyBtn = document.getElementById('modalReplyBtn');
    const forwardButton = document.getElementById('modalForwardBtn');
    
    forwardButton.disabled = true;
    forwardButton.innerHTML = "Processing... <i class='fas fa-spinner fa-spin'></i>";

    fetch('inquiries_page.php?action=forward_inquiry', {
        method: 'POST',
        headers: { 
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify({ id: activeInquiryRecord.id })
    })
    .then(response => {
        if (!response.ok) { throw new Error('HTTP request failed upstream.'); }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            // Update structural active data states immediately
            activeInquiryRecord.status = 'Answered';

            if (activeCardElement) {
                const badge = activeCardElement.querySelector('.status-badge-element');
                if (badge) {
                    badge.innerText = 'Answered';
                    badge.style.background = '#d4edda';
                    badge.style.color = '#155724';
                }
            }
            
            // FIX B: Gray out buttons instantly in the UI upon successful database update
            grayOutButtons(replyBtn, forwardButton);
            
            alert('Inquiry successfully routed to event staff. Status changed to Answered!');
        } else {
            alert('Server Update Failure: ' + data.error);
            resetButtons(replyBtn, forwardButton);
        }
    })
    .catch(err => {
        console.error(err);
        alert('Network runtime error.');
        resetButtons(replyBtn, forwardButton);
    });
}

window.addEventListener('click', function(e) {
    const modal = document.getElementById('inquiryDetailModal');
    if (e.target === modal) {
        closeInquiryModal();
    }
});
</script>