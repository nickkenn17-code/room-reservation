<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();

require_once 'components/header.php';
?>

<div class="login-page">
    <div class="login-hero"></div>

    <div class="login-panel">
        <div class="login-brand" style="text-align: center; margin-bottom: 20px;">
            <div class="brand-title" style="font-family: 'Shrikhand', cursive; font-size: 28px; color: #5a0505;">
                EVENTHUB<br>PORTAL
            </div>
        </div>

        <?php if(isset($_SESSION['register_success'])): ?>
            <div style="color: #2e7d32; background: #e8f5e9; padding: 10px; border-radius: 6px; text-align: center; margin-bottom: 15px; font-weight: bold;">
                <?php echo $_SESSION['register_success']; unset($_SESSION['register_success']); ?>
            </div>
        <?php endif; ?>

        <?php if(isset($_SESSION['login_error'])): ?>
            <div style="background: #ffebee; color: #d32f2f; padding: 10px; border-radius: 6px; text-align: center; margin-bottom: 15px; font-weight: bold; border: 1px solid #ffcdd2;">
                <?php echo $_SESSION['login_error']; unset($_SESSION['login_error']); ?>
            </div>
        <?php endif; ?>

        <div id="authShell">
            
            <div style="display: flex; justify-content: center; gap: 10px; margin-bottom: 25px; background: #f0f0f0; padding: 5px; border-radius: 20px;">
                <button type="button" id="tabStaff" onclick="switchTab('staff')" style="flex: 1; padding: 10px; border-radius: 15px; border: none; background: #d4af37; color: #333; font-weight: bold; cursor: pointer; transition: 0.3s;">Staff Portal</button>
                <button type="button" id="tabVisitor" onclick="switchTab('visitor')" style="flex: 1; padding: 10px; border-radius: 15px; border: none; background: transparent; color: #666; font-weight: bold; cursor: pointer; transition: 0.3s;">Visitor Access</button>
            </div>

            <div id="staffLoginForm">
                <div class="login-title" style="text-align: center; margin-bottom: 20px;">Welcome to EventHub Portal</div>
                <form class="auth-form" action="auth/login_register.php" method="POST">
                    <input type="hidden" name="login_staff" value="1">
                    
                    <input type="email" name="email" class="auth-input" placeholder="Enter Username/Email" required style="width: 100%; padding: 12px; margin-bottom: 15px; border: 1px solid #ccc; border-radius: 8px;">
                    
                    <div class="password-field" style="position: relative; margin-bottom: 15px;">
                        <input type="password" name="password" id="loginPassword" class="auth-input" placeholder="Enter Password" required style="width: 100%; padding: 12px; border: 1px solid #ccc; border-radius: 8px;">
                        <button type="button" class="password-toggle" data-target="loginPassword" style="position: absolute; right: 10px; top: 12px; background: none; border: none; cursor: pointer;">
                            <i class="fa-regular fa-eye"></i>
                        </button>
                    </div>

                    <div class="g-recaptcha" data-sitekey="6Ld9cecsAAAAAIQT71vTNpeqoq98QsWnSAm3BIGT" style="margin-bottom: 15px; display: flex; justify-content: center;"></div>

                    <button type="submit" class="auth-btn" style="width: 100%; padding: 12px; background: #5a0505; color: white; border: none; border-radius: 8px; font-weight: bold; cursor: pointer;">Login</button>
                </form>
                <div class="auth-links" style="text-align: center; margin-top: 15px; font-size: 13px;">
                    Not registered yet? <a href="#" onclick="toggleRegister(); return false;" style="color: #d4af37;">Sign up</a> Here.<br>
                    <a href="#" onclick="openResetModal(); return false;" style="color: #d4af37;">Forgot password.</a>
                </div>
            </div>

            <div id="visitorLoginForm" style="display: none;">
                <div class="login-title" style="text-align: center; margin-bottom: 20px;">Welcome to EventHub Portal</div>
                <form class="auth-form" action="auth/login_register.php" method="POST">
                    <input type="hidden" name="login_visitor" value="1">
                    
                    <input type="text" name="visitor_name" class="auth-input" placeholder="Enter Name" required style="width: 100%; padding: 12px; margin-bottom: 15px; border: 1px solid #ccc; border-radius: 8px;">
                    
                    <input type="text" name="invitation_code" class="auth-input" placeholder="Enter Invitation Code" required style="width: 100%; padding: 12px; margin-bottom: 25px; border: 1px solid #ccc; border-radius: 8px;">

                    <button type="submit" class="auth-btn" style="width: 100%; padding: 12px; background: #5a0505; color: white; border: none; border-radius: 8px; font-weight: bold; cursor: pointer;">Login</button>
                </form>
            </div>

        </div> 
        
        <div id="registerForm" style="display: none;">
            <div class="login-title" style="text-align: center; margin-bottom: 20px;">Register New Account</div>
            <form class="auth-form" action="auth/login_register.php" method="POST">
                <input type="hidden" name="register" value="1">
                
                <input type="text" name="name" class="auth-input" placeholder="Enter Name" required style="width: 100%; padding: 12px; margin-bottom: 15px; border: 1px solid #ccc; border-radius: 8px;">
                <input type="email" name="email" class="auth-input" placeholder="Enter Email" required style="width: 100%; padding: 12px; margin-bottom: 15px; border: 1px solid #ccc; border-radius: 8px;">
                
                <div class="password-field" style="position: relative; margin-bottom: 5px;">
                    <input type="password" name="password" id="registerPassword" class="auth-input" placeholder="Enter Password" required style="width: 100%; padding: 12px; border: 1px solid #ccc; border-radius: 8px;">
                    <button type="button" class="password-toggle" data-target="registerPassword" style="position: absolute; right: 10px; top: 12px; background: none; border: none; cursor: pointer;">
                        <i class="fa-regular fa-eye"></i>
                    </button>
                </div>

                <div class="password-strength" id="registerStrength" style="margin-bottom: 15px; font-size: 11px; color: #666; font-family: 'Montserrat', sans-serif;">
                    <div>Password must be at least 8 chars, include upper, lower, and a number.</div>
                    <div class="password-strength-bar" style="height: 4px; background: #e0e0e0; margin-top: 5px; border-radius: 2px; width: 100%;">
                        <div class="password-strength-fill" id="registerStrengthFill" style="height: 100%; width: 0%; background: #d32f2f; border-radius: 2px; transition: 0.3s;"></div>
                    </div>
                </div>

                <div class="password-field" style="position: relative; margin-bottom: 20px;">
                    <input type="password" name="confirm_password" id="registerConfirmPassword" class="auth-input" placeholder="Confirm Password" required style="width: 100%; padding: 12px; border: 1px solid #ccc; border-radius: 8px;">
                    <button type="button" class="password-toggle" data-target="registerConfirmPassword" style="position: absolute; right: 10px; top: 12px; background: none; border: none; cursor: pointer;">
                        <i class="fa-regular fa-eye"></i>
                    </button>
                </div>
                
                <button type="submit" class="auth-btn" style="width: 100%; padding: 12px; background: #5a0505; color: white; border: none; border-radius: 8px; font-weight: bold; cursor: pointer;">Sign Up</button>
            </form>
            <div class="auth-links" style="text-align: center; margin-top: 15px; font-size: 13px;">
                Already have an account? <a href="#" onclick="toggleRegister(); return false;" style="color: #d4af37;">Back to Login</a>
            </div>
        </div>

    </div>
</div>

<div id="resetModal" class="custom-modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); z-index: 9999; align-items: center; justify-content: center;">
    <div class="custom-modal-content" style="background: #ffffff; padding: 30px; border-radius: 12px; max-width: 400px; width: 90%; position: relative;">
        <span class="close-modal" onclick="closeResetModal()" style="position: absolute; right: 20px; top: 15px; font-size: 24px; cursor: pointer; color: #666;">&times;</span>
        
        <h2 style="color: #5a0505; font-family: 'Shrikhand', cursive; text-align: center; margin-top: 0; margin-bottom: 10px;">Reset Password</h2>
        <p style="text-align: center; color: #666; font-size: 14px; margin-bottom: 20px; font-family: 'Montserrat', sans-serif;">Enter your email and choose a new password.</p>
        
        <form class="auth-form" action="auth/self_reset.php" method="POST">
            <input type="email" name="email" class="auth-input" placeholder="Enter Email" required style="width: 100%; padding: 12px; margin-bottom: 15px; border: 1px solid #ccc; border-radius: 8px;">
            
            <div class="password-field" style="position: relative; margin-bottom: 5px;">
                <input type="password" name="password" id="resetPassword" class="auth-input" placeholder="New Password" required style="width: 100%; padding: 12px; border: 1px solid #ccc; border-radius: 8px;">
                <button type="button" class="password-toggle" data-target="resetPassword" style="position: absolute; right: 10px; top: 12px; background: none; border: none; cursor: pointer;">
                    <i class="fa-regular fa-eye"></i>
                </button>
            </div>
            
            <div class="password-strength" id="resetStrength" style="margin-bottom: 15px; font-size: 11px; color: #666; font-family: 'Montserrat', sans-serif;">
                <div>Password must be at least 8 chars, include upper, lower, and a number.</div>
                <div class="password-strength-bar" style="height: 4px; background: #e0e0e0; margin-top: 5px; border-radius: 2px; width: 100%;">
                    <div class="password-strength-fill" id="resetStrengthFill" style="height: 100%; width: 0%; background: #d32f2f; border-radius: 2px; transition: 0.3s;"></div>
                </div>
            </div>

            <div class="password-field" style="position: relative; margin-bottom: 20px;">
                <input type="password" name="confirm_password" id="resetConfirmPassword" class="auth-input" placeholder="Confirm Password" required style="width: 100%; padding: 12px; border: 1px solid #ccc; border-radius: 8px;">
                <button type="button" class="password-toggle" data-target="resetConfirmPassword" style="position: absolute; right: 10px; top: 12px; background: none; border: none; cursor: pointer;">
                    <i class="fa-regular fa-eye"></i>
                </button>
            </div>

            <button type="submit" class="auth-btn" style="width: 100%; padding: 12px; background: #5a0505; color: white; border: none; border-radius: 8px; font-weight: bold; cursor: pointer;">Change Password</button>
        </form>
    </div>
</div>

<script>
    // Tab Switcher Logic
    function switchTab(tabType) {
        const staffForm = document.getElementById('staffLoginForm');
        const visitorForm = document.getElementById('visitorLoginForm');
        const tabStaff = document.getElementById('tabStaff');
        const tabVisitor = document.getElementById('tabVisitor');

        if (tabType === 'staff') {
            staffForm.style.display = 'block';
            visitorForm.style.display = 'none';
            tabStaff.style.background = '#d4af37';
            tabStaff.style.color = '#333';
            tabVisitor.style.background = 'transparent';
            tabVisitor.style.color = '#666';
        } else {
            staffForm.style.display = 'none';
            visitorForm.style.display = 'block';
            tabVisitor.style.background = '#d4af37';
            tabVisitor.style.color = '#333';
            tabStaff.style.background = 'transparent';
            tabStaff.style.color = '#666';
        }
    }

    // Toggle Register Logic
    function toggleRegister() {
        const authShell = document.getElementById('authShell');
        const register = document.getElementById('registerForm');
        
        if (authShell.style.display === 'none') {
            authShell.style.display = 'block';
            register.style.display = 'none';
        } else {
            authShell.style.display = 'none';
            register.style.display = 'block';
        }
    }

    // Modal Logic
    function openResetModal() {
        let modal = document.getElementById('resetModal');
        modal.style.display = 'flex';
    }

    function closeResetModal() {
        let modal = document.getElementById('resetModal');
        modal.style.display = 'none';
    }

    window.addEventListener('click', function(event) {
        let modal = document.getElementById('resetModal');
        if (event.target === modal) {
            closeResetModal();
        }
    });

    // Password Toggle
    document.querySelectorAll('.password-toggle').forEach((button) => {
        button.addEventListener('click', () => {
            const targetId = button.getAttribute('data-target');
            const input = document.getElementById(targetId);
            if (!input) return;

            const isPassword = input.type === 'password';
            input.type = isPassword ? 'text' : 'password';
            const iconClass = isPassword ? 'fa-eye-slash' : 'fa-eye';
            button.innerHTML = `<i class="fa-regular ${iconClass}"></i>`;
        });
    });

    // Password Strength Evaluator
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
        const percent = (score / 4) * 100;
        
        fill.style.width = percent + '%';
        
        if (score <= 2) {
            fill.style.background = '#d32f2f'; // Red
        } else if (score === 3) {
            fill.style.background = '#f57c00'; // Orange
        } else {
            fill.style.background = '#388e3c'; // Green
        }

        return score === 4;
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
                alert('Please choose a stronger password matching all criteria.');
            }
        });
    }

    // Bind the functions to the inputs
    bindStrength('registerPassword', 'registerStrengthFill', '#registerForm form');
    bindStrength('resetPassword', 'resetStrengthFill', '#resetModal form');
</script>

<script src="https://www.google.com/recaptcha/api.js" async defer></script>
</body>
</html>