<?php 
session_start();
require_once '../components/config.php'; 
require_once '../components/header.php'; 
require_once '../components/sidebar.php'; 

// --- 1. BACKEND ROUTE: AJAX ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['action'])) {
    while (ob_get_level()) { ob_end_clean(); }
    header('Content-Type: application/json');
    $input_data = json_decode(file_get_contents('php://input'), true);
    $inquiry_id = isset($input_data['id']) ? intval($input_data['id']) : 0;

    if ($inquiry_id > 0) {
        if ($_GET['action'] === 'reply_inquiry') {
            $reply = $input_data['message'] ?? '';
            $stmt = $conn->prepare("UPDATE visitor_inquiries SET admin_reply = ?, status = 'Answered' WHERE id = ?");
            $stmt->bind_param("si", $reply, $inquiry_id);
            echo json_encode(['success' => $stmt->execute()]);
        }
    }
    exit();
}

// --- 2. DATABASE FETCH ---
$user_role = $_SESSION['role'] ?? '';
if ($user_role === 'Admin') {
    $sql_inquiries = "SELECT i.*, e.event_name FROM visitor_inquiries i LEFT JOIN event_list e ON i.event_id = e.id ORDER BY i.created_at ASC";
    $stmt = $conn->prepare($sql_inquiries);
    $stmt->execute();
} else {
    $staff_event_id = $_SESSION['event_id'] ?? 0;
    $sql_inquiries = "SELECT i.*, e.event_name FROM visitor_inquiries i LEFT JOIN event_list e ON i.event_id = e.id WHERE i.event_id = ? ORDER BY i.created_at ASC";
    $stmt = $conn->prepare($sql_inquiries);
    $stmt->bind_param("i", $staff_event_id);
    $stmt->execute();
}
$inquiries_list = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<style>
    .member-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 24px; align-items: start; }
    .meeting-card { padding: 22px; background: #ffffff; border-radius: 16px; cursor: pointer; box-shadow: 0 4px 15px rgba(0,0,0,0.05); transition: 0.2s; border: 1px solid #eee; height: auto; }
    .message-display { background: #f8f9fa; padding: 15px; border-radius: 12px; border: 1px solid #eee; text-align: left; white-space: pre-wrap; font-size: 14px; margin-top: 10px; }
    .custom-modal { display: none; position: fixed; z-index: 9999; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); align-items: center; justify-content: center; }
    .custom-modal.show { display: flex; }
    .custom-modal-content { background: #ffffff; padding: 30px; border-radius: 20px; width: 90%; max-width: 500px; box-shadow: 0 15px 40px rgba(0,0,0,0.2); }
    .btn-submit { width: 100%; padding: 12px; background: #5a0505; color: #fff; border: none; border-radius: 12px; font-weight: 600; cursor: pointer; margin-bottom: 10px; }
    .btn-cancel { width: 100%; padding: 12px; background: #f0f0f0; color: #333; border: none; border-radius: 12px; font-weight: 600; cursor: pointer; }
    .absence-textarea { width: 100%; padding: 12px; border-radius: 12px; border: 1.5px solid #ddd; margin-top: 10px; resize: vertical; min-height: 100px; }
</style>

<main class="main-content-wrapper" style="padding-top: 80px; padding-left: 5px; margin-left: 230px;">
    <div class="content member-grid">
        <?php foreach ($inquiries_list as $inquiry): 
            $short_msg = (strlen($inquiry['message']) > 45) ? substr($inquiry['message'], 0, 42) . '...' : $inquiry['message'];
        ?>
            <article class="meeting-card" data-inquiry='<?php echo htmlspecialchars(json_encode($inquiry), ENT_QUOTES, "UTF-8"); ?>'>
                <div style="display: flex; justify-content: space-between;">
                    <h3><?php echo htmlspecialchars($inquiry['event_name']); ?></h3>
                    <span style="font-size: 11px; padding: 4px 8px; border-radius: 6px; background: <?php echo ($inquiry['status'] == 'Answered' ? '#d4edda' : '#fff3cd'); ?>;"><?php echo htmlspecialchars($inquiry['status']); ?></span>
                </div>
                <p><strong>Visitor:</strong> <?php echo htmlspecialchars($inquiry['visitor_name']); ?></p>
                <div class="message-display"><strong>Question:</strong> <?php echo htmlspecialchars($short_msg); ?></div>
            </article>
        <?php endforeach; ?>
    </div>
</main>

<div id="inquiryDetailModal" class="custom-modal">
    <div class="custom-modal-content">
        <span style="float: right; cursor: pointer; font-size: 20px;" onclick="document.getElementById('inquiryDetailModal').classList.remove('show')">&times;</span>
        <h2 id="modalEventName" style="text-align: center; margin-bottom: 20px;"></h2>
        <div style="display: flex; justify-content: space-between; margin-bottom: 15px; color: #555; font-size: 13px;">
            <p id="modalVisitorName" style="font-weight:bold;"><i class="fas fa-user"></i> <span class="name-text"></span></p>
            <p id="modalVisitorEmail"><i class="fas fa-envelope"></i> <span class="email-text"></span></p>
        </div>
        <div id="modalFullMessage" class="message-display" style="min-height: 100px;"></div>
        
        <div id="replyDisplayArea" style="display: none; background: #e8f4fd; padding: 15px; border-radius: 12px; margin-top: 20px; margin-bottom: 20px;">
            <strong style="display: block; margin-bottom: 10px;">Staff Reply:</strong> 
            <p id="existingReplyText" style="margin: 0; line-height: 1.5; color: #333;"></p>
        </div>

        <div id="replyArea" style="display: none; margin-top: 20px;">
            <textarea id="replyMessage" class="absence-textarea" placeholder="Type your answer here..."></textarea>
        </div>
        <button type="button" id="replyToggleBtn" class="btn-cancel" onclick="showReplyBox()" style="margin-top: 20px;">Reply Directly</button>
        <button id="sendReplyBtn" class="btn-submit" style="display: none;" onclick="submitReply()">Send Reply</button>
    </div>
</div>

<script>
document.querySelector('.member-grid').addEventListener('click', (e) => {
    const card = e.target.closest('.meeting-card');
    if (card) {
        const data = JSON.parse(card.dataset.inquiry);
        document.getElementById('modalEventName').innerText = data.event_name;
        document.getElementById('modalFullMessage').innerText = data.message;
        document.querySelector('.name-text').innerText = data.visitor_name;
        document.querySelector('.email-text').innerText = data.visitor_email;
        
        const replyArea = document.getElementById('replyDisplayArea');
        const replyToggleBtn = document.getElementById('replyToggleBtn');
        if (data.admin_reply) {
            replyArea.style.display = 'block';
            document.getElementById('existingReplyText').innerText = data.admin_reply;
            replyToggleBtn.disabled = true;
            replyToggleBtn.innerText = "Already Answered";
            replyToggleBtn.style.opacity = "0.5";
        } else {
            replyArea.style.display = 'none';
            replyToggleBtn.disabled = false;
            replyToggleBtn.innerText = "Reply Directly";
            replyToggleBtn.style.opacity = "1";
        }
        document.getElementById('inquiryDetailModal').classList.add('show');
        window.activeInquiry = data;
    }
});
function showReplyBox() { document.getElementById('replyArea').style.display = 'block'; document.getElementById('sendReplyBtn').style.display = 'block'; document.getElementById('replyToggleBtn').style.display = 'none'; }
function submitReply() { fetch('inquiries_page.php?action=reply_inquiry', { method: 'POST', headers: {'Content-Type': 'application/json'}, body: JSON.stringify({ id: window.activeInquiry.id, message: document.getElementById('replyMessage').value }) }).then(res => res.json()).then(data => { if (data.success) { location.reload(); } }); }
</script>
</body>
</html>