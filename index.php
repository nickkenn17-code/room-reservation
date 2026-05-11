<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();
// If the user already has a session, push them to the dashboard automatically
// if (isset($_SESSION['id'])) {
//     header("Location: pages/user_page.php");
//     exit();
// }
require_once 'components/header.php';
?>

<div class="login-page">
    <div class="login-hero"></div>
    <div class="login-header-bar"></div>

    <div class="login-panel">
        <div class="login-brand">
            <div class="brand-logos">
                <img src="assets/images/unijipng.png" alt="UNIJI logo">
                <img src="assets/images/jiclogopng.png" alt="JIC logo">
            </div>
            <div class="brand-divider"></div>
            <div class="brand-title">
                LIBRARY<br>
                MANAGEMENT<br>
                SYSTEM
            </div>
        </div>

        <?php if(isset($_SESSION['register_success'])): ?>
            <div style="color: green; margin-bottom: 10px;">
                <?php echo $_SESSION['register_success']; unset($_SESSION['register_success']); ?>
            </div>
        <?php endif; ?>
        
        <div id="loginForm">
            <div class="login-title">Log In</div>
            
            <?php if (isset($_SESSION['login_error'])) { ?>
                <div style="background: #f8d7da; color: #721c24; padding: 10px; border-radius: 5px; margin-bottom: 15px; font-size: 13px; border: 1px solid #f5c6cb;">
                    <?php 
                        echo $_SESSION['login_error']; 
                        unset($_SESSION['login_error']); // Clear it so it doesn't stay forever
                    ?>
                </div>
            <?php } ?>

            <form class="auth-form" action="auth/login_register.php" method="POST">
                <input type="hidden" name="login" value="1">
                
                <input type="email" name="email" class="auth-input" placeholder="Email" required>
                <input type="password" name="password" class="auth-input" placeholder="Password" required>
                <button type="submit" class="auth-btn">Log In</button>
            </form>
            <div class="auth-links">
                Not registered yet? <a href="#" onclick="toggleForms(); return false;">Sign up</a> Here.<br>
                <a href="auth/forgot_password.php">Forgot password.</a>
            </div>
        </div>

        <div id="registerForm" style="display: none;">
            <div class="login-title">Register</div>
            <form class="auth-form" action="auth/login_register.php" method="POST">
                <input type="hidden" name="register" value="1">
                
                <input type="text" name="name" class="auth-input" placeholder="Name" required>
                
                <input type="email" name="email" class="auth-input" placeholder="Email" required>
                
                <select name="major" class="auth-input" required>
                    <option value="">Select Major</option>
                    <option value="Information Technology">Information Technology</option>
                    <option value="Software Engineering">Software Engineering</option>
                </select>
                
                <input type="password" name="password" class="auth-input" placeholder="Password" required>
                
                <button type="submit" class="auth-btn">Sign Up</button>
            </form>
            <div class="auth-links">
                Already have an account? <a href="#" onclick="toggleForms(); return false;">Back to Login Page</a>
            </div>
        </div>
    </div>
</div>

<script>
    // Simple JS to toggle between Login and Register views without reloading    
    function toggleForms() {
        const login = document.getElementById('loginForm');
        const register = document.getElementById('registerForm');
        
        if (login.style.display === 'none') {
            login.style.display = 'block';
            register.style.display = 'none';
        } else {
            login.style.display = 'none';
            register.style.display = 'block';
        }
    }
</script>

</body>
</html>