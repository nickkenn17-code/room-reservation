<?php
// actions/ping_session.php
session_start();

// Simply updates the timer so the PHP server knows the user is still active
if (isset($_SESSION['last_activity'])) {
    $_SESSION['last_activity'] = time();
}

// Return a tiny success message (the JS fetch ignores this, but it's good practice)
echo "success";
?>