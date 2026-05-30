<?php
// components/faq_modal.php 

$db_table_name = 'faq_chatbot'; 
$col_category  = 'category';
$col_keywords  = 'keywords';
$col_response  = 'bot_response';

$faq_dataset = [];
$table_exists = false;

if (isset($conn)) {
    $check_table = $conn->query("SHOW TABLES LIKE '{$db_table_name}'");
    if ($check_table && $check_table->num_rows > 0) {
        $table_exists = true;
    }
}

if ($table_exists) {
    $sql_faqs = "SELECT * FROM {$db_table_name}";
    $result_faqs = $conn->query($sql_faqs);
    if ($result_faqs && $result_faqs->num_rows > 0) {
        while ($row = $result_faqs->fetch_assoc()) {
            $faq_dataset[] = [
                'category' => $row[$col_category],
                // CHANGED: Added array_map('trim', ...) to remove hidden spaces
                'keywords' => array_map('trim', explode(',', strtolower($row[$col_keywords]))),
                'response' => $row[$col_response]
            ];
        }
    }
}

// Strictly the core 5 visitor topics from your rubric constraints
if (empty($faq_dataset)) {
    $faq_dataset = [
        [
            'category' => 'Invitation Codes',
            'keywords' => ['code', 'invitation', 'pin', 'access', 'login', 'join', 'register'],
            'response' => 'Your random invitation code is issued directly by an event PIC or staff member via email or WhatsApp. This code serves as your universal pass—enter it on the login screen to access the full portal!'
        ],
        [
            'category' => 'Event Duration',
            'keywords' => ['duration', 'time', 'long', 'schedule', 'hours', 'open', 'close', 'calendar', 'date', 'past', 'present'],
            'response' => 'Each event card on the dashboard displays its specific date and active duration hours. You can browse live active showcases as well as past cultural exhibitions.'
        ],
        [
            'category' => 'Requesting Extensions',
            'keywords' => ['extend', 'extension', 'longer', 'late', 'change time', 'delay', 'staff', 'add time'],
            'response' => 'While events have predefined hours, an event PIC or staff member can trigger an extension request if visitor traffic is high. Approved extensions update the event closing times live!'
        ],
        [
            'category' => 'Photo Showcase',
            'keywords' => ['photo', 'picture', 'image', 'gallery', 'showcase', 'media', 'display', 'collection'],
            'response' => 'Every single past and present cultural event features a dedicated showcase section holding a gallery of at least 25 and up to 75 high-quality documentation photos.'
        ],
        [
            'category' => 'Admission Fees',
            'keywords' => ['ticket', 'price', 'cost', 'buy', 'free', 'fee', 'admission', 'payment'],
            'response' => 'General access to the platform and standard entry to all individual exhibitions is completely free for invited visitors utilizing a valid invitation code.'
        ]
    ];
}
?>

<button class="faq-floating-trigger" onclick="toggleFaqModal()" type="button" aria-label="Open FAQ Assistant">
    <span style="font-size: 11px; font-weight: bold; letter-spacing: 0.5px; font-family: 'Poppins', sans-serif;">FAQ</span>
    <i class="fas fa-robot"></i>
</button>

<div id="globalFaqModal" class="faq-modal-overlay">
    <div class="faq-modal-card-v2">
        
        <button type="button" class="faq-mock-close-btn" onclick="toggleFaqModal()" aria-label="Close modal">
            <i class="far fa-times-circle"></i>
        </button>

        <div class="faq-layout-split">
            
            <div class="faq-sidebar-v2">
                <div class="sidebar-v2-title" style="text-align: center; width: 100%; margin-bottom: 12px;">Categories</div>
                <div class="faq-preset-list">
                    <?php foreach ($faq_dataset as $faq): ?>
                        <button type="button" class="faq-category-item-btn" 
                                onclick="triggerPresetFaq('<?php echo addslashes($faq['category']); ?>', '<?php echo addslashes($faq['response']); ?>')">
                            <?php echo htmlspecialchars($faq['category']); ?>
                        </button>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="faq-chat-shell-v2">
    
                <div class="faq-modal-top-bar">
                    <button type="button" class="faq-mock-close-btn" onclick="toggleFaqModal()" aria-label="Close modal">
                        <i class="far fa-times-circle"></i>
                    </button>
                </div>

                <div id="modalChatThread" class="faq-thread-scroll-v2">
                    <div class="faq-mockup-header-block">
                        <div class="faq-robot-icon-wrapper">
                            <i class="fas fa-robot"></i>
                        </div>
                        <div class="faq-welcome-speech-bubble">
                            Hello <?= htmlspecialchars($_SESSION['name'] ?? 'Visitor') ?>, how can I help?
                        </div>
                    </div>
                    </div>
                
                <div class="faq-footer-input-wrapper">
                    <div class="faq-input-bar-container">
                        <input type="text" id="modalChatInput" class="faq-mock-input" placeholder="Type here..." 
                               onkeypress="if(event.key === 'Enter') handleModalBotQuery()">
                        <button type="button" class="faq-mock-send-btn" onclick="handleModalBotQuery()" aria-label="Send query">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
const dynamicFaqDataset = <?php echo json_encode($faq_dataset); ?>;

function toggleFaqModal() {
    const eventModal = document.getElementById('eventDetailModal');
    if (eventModal) {
        eventModal.style.display = 'none';
    }

    document.getElementById('globalFaqModal').classList.toggle('active');
}

function postBubble(sender, content) {
    const thread = document.getElementById('modalChatThread');
    const container = document.createElement('div');
    container.className = `chat-bubble-container ${sender}-align`;

    const bubble = document.createElement('div');
    bubble.className = `chat-bubble-v2 ${sender}-v2-style`;
    bubble.innerText = content;
    
    container.appendChild(bubble);
    thread.appendChild(container);
    thread.scrollTop = thread.scrollHeight;
}

function showTypingIndicator() {
    const thread = document.getElementById('modalChatThread');
    const container = document.createElement('div');
    container.className = 'chat-bubble-container bot-align typing-container-indicator';

    const bubble = document.createElement('div');
    bubble.className = 'chat-bubble-v2 bot-v2-style typing-dots-bubble';
    bubble.innerHTML = '<span class="dot-pulse"></span><span class="dot-pulse"></span><span class="dot-pulse"></span>';
    
    container.appendChild(bubble);
    thread.appendChild(container);
    thread.scrollTop = thread.scrollHeight;
}

function removeTypingIndicator() {
    const indicators = document.querySelectorAll('.typing-container-indicator');
    indicators.forEach(el => el.remove());
}

function triggerPresetFaq(cat, response) {
    postBubble('user', `Tell me about ${cat}`);
    showTypingIndicator();
    
    setTimeout(() => {
        removeTypingIndicator();
        postBubble('bot', response);
    }, 900);
}

function handleModalBotQuery() {
    const input = document.getElementById('modalChatInput');
    const rawInput = input.value.trim();
    if (!rawInput) return;

    postBubble('user', rawInput);
    input.value = '';
    showTypingIndicator();

    setTimeout(() => {
        removeTypingIndicator();
        
        // --- SMART TOKENIZATION ENGINE ---
        // Clean special characters and split the sentence into isolated individual words
        const cleanedQuery = rawInput.toLowerCase().replace(/[^\w\s]/g, '');
        const inputWordsArray = cleanedQuery.split(/\s+/);
        
        let answer = null;
        
        // Check if the user used a multi-word exact phrase matching an entire keyword (like "get code")
        for (let entry of dynamicFaqDataset) {
            let directPhraseMatch = entry.keywords.some(kw => cleanedQuery.includes(kw) && kw.includes(' '));
            if (directPhraseMatch) {
                answer = entry.response;
                break;
            }
        }
        
        // If no multi-word phrases matched, look for individual bounded word matches
        if (!answer) {
            for (let entry of dynamicFaqDataset) {
                // Ignore the entry if its keywords would get falsely triggered by compounds
                let hasExactWordMatch = entry.keywords.some(kw => {
                    // Specific safety check: if user asked "dress code", don't let single keyword "code" match it
                    if (kw === 'code' && cleanedQuery.includes('dress code')) {
                        return false; 
                    }
                    return inputWordsArray.includes(kw);
                });

                if (hasExactWordMatch) {
                    answer = entry.response;
                    break;
                }
            }
        }
        
        // Output response or print the explicit help desk escape routes
        if (answer) {
            postBubble('bot', answer);
        } else {
            postBubble('bot', "Hmm, I couldn't quite find an automated matching detail parameter for that question. If the provided topics do not satisfy your inquiry, please feel free to contact our event staff directly through the 'Contact Us' page on the left sidebar, or look at individual event details cards and use the 'Further Inquiries' button!");
        }
    }, 1100);
}
</script>