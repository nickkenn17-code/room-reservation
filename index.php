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

        <?php if(isset($_SESSION['register_error'])): ?>
            <div style="background: #f8d7da; color: #721c24; padding: 10px; border-radius: 5px; margin-bottom: 15px; font-size: 13px; border: 1px solid #f5c6cb;">
                <?php echo $_SESSION['register_error']; unset($_SESSION['register_error']); ?>
            </div>
        <?php endif; ?>

        <?php if(isset($_SESSION['reset_success'])): ?>
            <div style="color: green; margin-bottom: 10px;">
                <?php echo $_SESSION['reset_success']; unset($_SESSION['reset_success']); ?>
            </div>
        <?php endif; ?>

        <?php if(isset($_SESSION['reset_error'])): ?>
            <div style="background: #f8d7da; color: #721c24; padding: 10px; border-radius: 5px; margin-bottom: 15px; font-size: 13px; border: 1px solid #f5c6cb;">
                <?php echo $_SESSION['reset_error']; unset($_SESSION['reset_error']); ?>
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
                <div class="password-field">
                    <input type="password" name="password" id="loginPassword" class="auth-input" placeholder="Password" required>
                    <button type="button" class="password-toggle" data-target="loginPassword" aria-label="Show password">
                        <i class="fa-regular fa-eye" aria-hidden="true"></i>
                    </button>
                </div>
                <button type="submit" class="auth-btn">Log In</button>
            </form>
            <div class="auth-links">
                Not registered yet? <a href="#" onclick="toggleForms(); return false;">Sign up</a> Here.<br>
                <a href="#" onclick="openResetModal(); return false;">Forgot password.</a>
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
                
                <div class="password-field">
                    <input type="password" name="password" id="registerPassword" class="auth-input" placeholder="Password" required>
                    <button type="button" class="password-toggle" data-target="registerPassword" aria-label="Show password">
                        <i class="fa-regular fa-eye" aria-hidden="true"></i>
                    </button>
                </div>
                <div class="password-strength" id="registerStrength">
                    <div>Password must be at least 8 chars, include upper, lower, and a number.</div>
                    <div class="password-strength-bar">
                        <div class="password-strength-fill" id="registerStrengthFill"></div>
                    </div>
                </div>
                
                <button type="submit" class="auth-btn">Sign Up</button>
            </form>
            <div class="auth-links">
                Already have an account? <a href="#" onclick="toggleForms(); return false;">Back to Login Page</a>
            </div>
        </div>
    </div>
</div>

<div id="resetModal" class="custom-modal">
    <div class="custom-modal-content" style="max-width: 400px; text-align: left;">
        <span class="close-modal" onclick="closeResetModal()">&times;</span>
        <div class="modal-body">
            <h2 class="modal-name" style="margin-bottom: 12px;">Reset Password</h2>
            <p class="modal-text" style="margin-bottom: 18px; color: #666;">
                Enter your email and choose a new password.
            </p>
            <form class="auth-form" action="auth/self_reset.php" method="POST">
                <input type="email" name="email" class="auth-input" placeholder="Email" required>
                <div class="password-field">
                    <input type="password" name="password" id="resetPassword" class="auth-input" placeholder="New Password" required>
                    <button type="button" class="password-toggle" data-target="resetPassword" aria-label="Show password">
                        <i class="fa-regular fa-eye" aria-hidden="true"></i>
                    </button>
                </div>
                <div class="password-strength" id="resetStrength">
                    <div>Password must be at least 8 chars, include upper, lower, and a number.</div>
                    <div class="password-strength-bar">
                        <div class="password-strength-fill" id="resetStrengthFill"></div>
                    </div>
                </div>
                <div class="password-field">
                    <input type="password" name="confirm_password" id="resetConfirmPassword" class="auth-input" placeholder="Confirm Password" required>
                    <button type="button" class="password-toggle" data-target="resetConfirmPassword" aria-label="Show password">
                        <i class="fa-regular fa-eye" aria-hidden="true"></i>
                    </button>
                </div>
                <button type="submit" class="auth-btn">Reset Password</button>
            </form>
        </div>
    </div>
</div>

<?php
    $active_form = $_SESSION['active_form'] ?? '';
    unset($_SESSION['active_form']);
?>

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

    function evaluatePassword(value) {
        const checks = {
            length: value.length >= 8,
            lower: /[a-z]/.test(value),
            upper: /[A-Z]/.test(value),
            number: /[0-9]/.test(value)
        };

        const score = Object.values(checks).filter(Boolean).length;
        return { checks, score };
    }

    function updateStrength(inputId, fillId) {
        const input = document.getElementById(inputId);
        const fill = document.getElementById(fillId);
        if (!input || !fill) return false;

        const { checks, score } = evaluatePassword(input.value);
        const percent = Math.min(100, (score / 4) * 100);
        fill.style.width = percent + '%';
        fill.classList.remove('medium', 'strong');
        if (score >= 3) {
            fill.classList.add('medium');
        }
        if (score === 4) {
            fill.classList.add('strong');
        }

        return checks.length && checks.lower && checks.upper && checks.number;
    }

    function bindStrength(inputId, fillId, formSelector) {
        const input = document.getElementById(inputId);
        const form = document.querySelector(formSelector);
        if (!input || !form) return;

        input.addEventListener('input', () => {
            updateStrength(inputId, fillId);
        });

        form.addEventListener('submit', (event) => {
            const isStrong = updateStrength(inputId, fillId);
            if (!isStrong) {
                event.preventDefault();
                alert('Please choose a stronger password.');
            }
        });
    }

    bindStrength('registerPassword', 'registerStrengthFill', '#registerForm form');
    bindStrength('resetPassword', 'resetStrengthFill', '#resetModal form');

    document.querySelectorAll('.password-toggle').forEach((button) => {
        button.addEventListener('click', () => {
            const targetId = button.getAttribute('data-target');
            const input = document.getElementById(targetId);
            if (!input) return;

            const isPassword = input.type === 'password';
            input.type = isPassword ? 'text' : 'password';
            const iconClass = isPassword ? 'fa-eye-slash' : 'fa-eye';
            button.innerHTML = `<i class="fa-regular ${iconClass}" aria-hidden="true"></i>`;
            button.setAttribute('aria-label', isPassword ? 'Hide password' : 'Show password');
        });
    });

    function openResetModal() {
        document.getElementById('resetModal').classList.add('show');
    }

    function closeResetModal() {
        document.getElementById('resetModal').classList.remove('show');
    }

    window.addEventListener('click', function(event) {
        const modal = document.getElementById('resetModal');
        if (event.target === modal) {
            closeResetModal();
        }
    });

    const activeForm = "<?php echo $active_form; ?>";
    if (activeForm === 'register') {
        document.getElementById('loginForm').style.display = 'none';
        document.getElementById('registerForm').style.display = 'block';
    }
</script>

</body>
</html>