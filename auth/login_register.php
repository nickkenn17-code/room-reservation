<?php
session_start();
require_once __DIR__ . '/../components/config.php';

// --- 1. STAFF / ADMIN LOGIN ---
if (isset($_POST['login_staff'])) {
    // (Optional) Recaptcha Check here if you want to keep it

    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM user WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        
        if (password_verify($password, $user['password'])) {
            $user_id = $user['id'];
            
            // Get the user's role and associated event_id
            $stmt_role = $conn->prepare("SELECT role, event_id FROM role WHERE user_id = ?");
            $stmt_role->bind_param("i", $user_id);
            $stmt_role->execute();
            $result_role = $stmt_role->get_result();

            if ($result_role->num_rows > 0) {
                $row = $result_role->fetch_assoc();
                $_SESSION['role'] = $row['role'];
                $_SESSION['event_id'] = $row['event_id']; // Crucial for Staff to manage only their event
            } else {
                $_SESSION['role'] = 'Staff'; // Fallback
            }
            
            $_SESSION['id'] = $user['id'];      
            $_SESSION['name'] = $user['name'];  
            $_SESSION['email'] = $user['email']; 
            
            header("Location: ../pages/staff_page.php"); 
            exit();
        } 
    }
    
    $_SESSION['login_error'] = "Invalid email or password.";
    header("Location: ../index.php");
    exit();
}

// --- 2. VISITOR CODE LOGIN ---
if (isset($_POST['login_visitor'])) {
    $visitor_name = trim($_POST['visitor_name']);
    $invitation_code = trim($_POST['invitation_code']);

    // Check if the code exists in the database
    $stmt = $conn->prepare("SELECT * FROM invitation_codes WHERE code = ?");
    $stmt->bind_param("s", $invitation_code);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $code_data = $result->fetch_assoc();
        
        // Code is valid! Log them in as a temporary visitor session
        $_SESSION['role'] = 'Visitor';
        $_SESSION['name'] = $visitor_name;
        $_SESSION['event_id'] = $code_data['event_id']; // Tells the app WHICH event they are visiting
        $_SESSION['invitation_code'] = $invitation_code;

        // Redirect to the visitor dashboard
        header("Location: ../pages/visitors_page.php");
        exit();
    } else {
        $_SESSION['login_error'] = "Invalid Invitation Code. Please try again.";
        header("Location: ../index.php");
        exit();
    }
}

// --- 3. REGISTRATION (For Staff) ---
if (isset($_POST['register'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];  
    $password_raw = $_POST['password'];

    // Basic password validation
    if (strlen($password_raw) < 8) {
        $_SESSION['login_error'] = 'Password must be at least 8 characters.';
        header("Location: ../index.php");
        exit();
    }

    $password = password_hash($password_raw, PASSWORD_DEFAULT); 
    $profile_pic = 'avatar1.jpg'; // Default

    // Check email
    $check_stmt = $conn->prepare("SELECT email FROM user WHERE email = ?");
    $check_stmt->bind_param("s", $email);
    $check_stmt->execute();
    $check_stmt->store_result();

    if ($check_stmt->num_rows > 0) {
        $_SESSION['login_error'] = "Email already exists.";
        header("Location: ../index.php");
        exit();
    } else {
        $user_stmt = $conn->prepare("INSERT INTO user (name, email, password, profile_pic) VALUES (?, ?, ?, ?)");
        $user_stmt->bind_param("ssss", $name, $email, $password, $profile_pic);

        if ($user_stmt->execute()) {
            $new_user_id = $user_stmt->insert_id;
            $role = 'Staff';
            
            // NOTE: We assign event_id = 1 here so it passes the Database CHECK constraint we added!
            $event_id = 1; 

            $role_stmt = $conn->prepare("INSERT INTO role (user_id, role, event_id) VALUES (?, ?, ?)");
            $role_stmt->bind_param("isi", $new_user_id, $role, $event_id);
            $role_stmt->execute();
        }
    }

    $_SESSION['register_success'] = "Account created! You can now log in.";
    header("Location: ../index.php");
    exit();
}
?>