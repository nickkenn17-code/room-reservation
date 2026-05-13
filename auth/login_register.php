<?php
//starting a session to save user information across pages
session_start();
// Load database configuration and connection
require_once __DIR__ . '/../components/config.php';
/** @var mysqli $conn */

function is_strong_password($password) {
    return strlen($password) >= 8
        && preg_match('/[a-z]/', $password)
        && preg_match('/[A-Z]/', $password)
        && preg_match('/[0-9]/', $password);
}

//this handles user registration
if (isset($_POST['register'])) {
    //get data from the registration form
    $name = $_POST['name'];
    $email = $_POST['email'];  
    $password_raw = $_POST['password'] ?? '';

    if (!is_strong_password($password_raw)) {
        $_SESSION['register_error'] = 'Password must be at least 8 chars and include upper, lower, and a number.';
        $_SESSION['active_form'] = 'register';
        header("Location: ../index.php");
        exit();
    }

    //hashing the password for security
    $password = password_hash($password_raw, PASSWORD_DEFAULT); 
    
    //default role for new users
    $role = 'Member'; 
    
    // Get the major data from form through dropdown option
    $major = $_POST['major'];

    //for assigning random avatar to new user as profile picture
    $avatar_list = [
        'avatar1.jpg', 'avatar2.jpg', 'avatar3.jpg', 
        'avatar4.jpg', 'avatar5.jpg', 'avatar6.jpg'
    ];
    //randomize avatar from the list
    $random_key = array_rand($avatar_list);
    $profile_pic = $avatar_list[$random_key];

    //this checks if email already exists in database - club_db
    $check_stmt = $conn->prepare("SELECT email FROM user WHERE email = ?");
    $check_stmt->bind_param("s", $email);
    $check_stmt->execute();
    $check_stmt->store_result();

    //show error if email already exists
    if ($check_stmt->num_rows > 0) {
        $_SESSION['register_error'] = "Email already exists.";
        $_SESSION['active_form'] = 'register';
        $check_stmt->close(); 
        header("Location: ../index.php");
        exit();
    } else {
        //ensure email is unique, then proceed with registration
        $check_stmt->close(); 

        //save new user to database in user table
        $user_stmt = $conn->prepare("INSERT INTO user (name, email, password, major, profile_pic) VALUES (?, ?, ?, ?, ?)");
        $user_stmt->bind_param("sssss", $name, $email, $password, $major, $profile_pic);

        if ($user_stmt->execute()) {
            //get user id of new user account
            $new_user_id = $user_stmt->insert_id;

            //assign user role in database in role table
            $role_stmt = $conn->prepare("INSERT INTO role (user_id, role) VALUES (?, ?)");
            $role_stmt->bind_param("is", $new_user_id, $role);
            $role_stmt->execute();
            $role_stmt->close();

            //log this registration activity
            if (function_exists('logActivity')) {
                logActivity($conn, $new_user_id, $name, 'Register', 'User registered new account.');
            }
        }
        $user_stmt->close();
    }

    $_SESSION['register_success'] = "Account created! You can now log in.";
    //redirect back to home page after registration
    header("Location: ../index.php");
    exit();
}

//this is for handling user login
if (isset($_POST['login'])) {

    // --- reCAPTCHA Verification ---
    $recaptcha_secret = $recaptcha_secret ?? '';
    if (!$recaptcha_secret) {
        $_SESSION['login_error'] = "CAPTCHA is not configured. Please contact the administrator.";
        $_SESSION['active_form'] = 'login';
        header("Location: ../index.php");
        exit();
    }
    $recaptcha_response = $_POST['g-recaptcha-response'] ?? '';

    // Verify the response with Google's API
    $verify_response = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret=' . $recaptcha_secret . '&response=' . $recaptcha_response);
    $response_data = json_decode($verify_response);

    if (!$response_data->success) {
        // If CAPTCHA fails or is ignored, stop login and show error
        $_SESSION['login_error'] = "Please complete the CAPTCHA verification.";
        $_SESSION['active_form'] = 'login';
        header("Location: ../index.php");
        exit();
    }

    //get email and password from login form
    $email = $_POST['email'];
    $password = $_POST['password'];

    //search for user with this email in database in user table
    $stmt = $conn->prepare("SELECT * FROM user WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    //if user found, check if password matches 
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        //verify if password match the hashed password in database
        if (password_verify($password, $user['password'])) {
            $user_id = $user['id'];
            
            //get the user's role
            $stmt_role = $conn->prepare("SELECT role FROM role WHERE user_id = ?");
            $stmt_role->bind_param("i", $user_id);
            $stmt_role->execute();
            $result_role = $stmt_role->get_result();

            //extracts user's role
            if ($result_role->num_rows > 0) {
                $row = $result_role->fetch_assoc();
                $user_role = $row['role'];
            } else {
                $user_role = 'member';
            }
            $stmt_role->close();

            //this stores user information in session variables
            $_SESSION['id'] = $user['id'];      
            $_SESSION['name'] = $user['name'];  
            $_SESSION['email'] = $user['email']; 
            $_SESSION['role'] = $user_role; 
            $_SESSION['weak_password'] = !is_strong_password($password);

            //log this login activity
            if (function_exists('logActivity')) {
                logActivity($conn, $user['id'], $user['name'], 'Login', 'User logged in.');
            }

            // Route to the correct page in the /pages/ folder
            if (strtolower($user_role) == 'admin') {
                header("Location: ../pages/user_page.php");
            } elseif (strtolower($user_role) == 'manager') {
                header("Location: ../pages/user_page.php");
            } else {
                header("Location: ../pages/user_page.php");
            }
            exit();
        } 
    }
    
    //when Login failed, show this error message
    $_SESSION['login_error'] = "Invalid email or password.";
    $_SESSION['active_form'] = 'login';
    
    //then redirect back to home page to show error
    header("Location: ../index.php");
    exit();
}
?>