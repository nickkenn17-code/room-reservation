<?php
// Include the reusable header
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

        <div id="loginForm">
            <div class="login-title">Log In</div>
            <form class="auth-form" action="auth/login_action.php" method="POST">
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
            <form class="auth-form" action="auth/register_action.php" method="POST">
                <input type="text" name="name" class="auth-input" placeholder="Name" required>
                <select name="major" class="auth-input" required>
                    <option value="">Select Major</option>
                    <option value="Information Technology">Information Technology</option>
                    <option value="Software Engineering">Software Engineering</option>
                    <option value="Data Science">Data Science</option>
                </select>
                <input type="email" name="email" class="auth-input" placeholder="Email" required>
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
        var login = document.getElementById('loginForm');
        var register = document.getElementById('registerForm');
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