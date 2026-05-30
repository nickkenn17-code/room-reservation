<?php
    session_start();
    require_once '../components/config.php'; 

    // 1. Security Check: Admin and Staff only
    if (!isset($_SESSION['id']) || ($_SESSION['role'] !== 'Admin' && $_SESSION['role'] !== 'Staff')) {
        header("Location: ../index.php");
        exit();
    }

    $message = '';

    // 2. Database Insertion Logic (Runs when 'Confirm' is clicked)
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['new_code'])) {
        $new_code = trim($_POST['new_code']);
        
        // THE FIX: Grab the currently logged-in user's ID
        $generated_by = (int)$_SESSION['id'];
        
        if (!empty($new_code)) {
            // THE FIX: Insert both the code AND the user who generated it
            $sql = "INSERT INTO invitation_codes (code, generated_by) VALUES (?, ?)";
            $stmt = $conn->prepare($sql);
            
            if ($stmt) {
                // 'si' = string (code), integer (generated_by)
                $stmt->bind_param("si", $new_code, $generated_by);
                if ($stmt->execute()) {
                    $message = "<div style='background: rgba(40, 167, 69, 0.2); border: 1px solid #28a745; color: #28a745; padding: 10px; border-radius: 6px; margin-top: 15px; font-family: \"Montserrat\", sans-serif; font-size: 13px; font-weight: 600;'>Success! Code added to database.</div>";
                } else {
                    $message = "<div style='background: rgba(220, 53, 69, 0.2); border: 1px solid #dc3545; color: #dc3545; padding: 10px; border-radius: 6px; margin-top: 15px; font-family: \"Montserrat\", sans-serif; font-size: 13px; font-weight: 600;'>Error: " . $conn->error . "</div>";
                }
                $stmt->close();
            }
        }
    }

    require_once '../components/header.php'; 
    require_once '../components/sidebar.php';
?>

<div class="main-content" style="
    /* This background image simulates the blurred gallery background from your mockup */
    background: url('../assets/images/gallery/1.jpg') center/cover no-repeat;
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;">
    
    <div style="position: absolute; inset: 0; background: rgba(255, 255, 255, 0.4); backdrop-filter: blur(5px); z-index: 1;"></div>

    <main style="position: relative; z-index: 2; width: 100%; display: flex; justify-content: center; padding: 20px;">
        
        <div style="
            background: rgba(150, 150, 150, 0.45); 
            backdrop-filter: blur(12px); 
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid #5a0505; 
            border-radius: 16px; 
            padding: 40px; 
            width: 100%; 
            max-width: 480px; 
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            text-align: center;">
            
            <h2 style="font-family: 'Montserrat', sans-serif; color: #ffffff; font-weight: 700; font-size: 20px; margin-top: 0; margin-bottom: 25px; text-shadow: 0 2px 4px rgba(0,0,0,0.3);">
                Click here to generate new code
            </h2>

            <form action="" method="POST" id="codeForm">
                
                <div style="position: relative; margin-bottom: 25px;">
                    <input type="text" id="generatedCodeInput" name="new_code" readonly required
                        style="width: 100%; padding: 12px 45px 12px 15px; border: none; border-radius: 8px; font-family: 'Montserrat', sans-serif; font-size: 16px; font-weight: 600; color: #333; box-sizing: border-box; background: #ffffff; box-shadow: inset 0 2px 4px rgba(0,0,0,0.05);"
                        placeholder="Generate a code...">
                    
                    <button type="button" onclick="copyCode()" title="Copy to Clipboard"
                        style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; color: #5a0505; font-size: 16px; cursor: pointer; padding: 5px;">
                        <i class="fas fa-link"></i>
                    </button>
                </div>

                <div style="display: flex; gap: 15px; justify-content: center;">
                    <button type="button" onclick="generateCode()" 
                        style="flex: 1; background: #4a0404; color: #ffffff; border: 1px solid #330303; padding: 12px 0; border-radius: 6px; font-family: 'Montserrat', sans-serif; font-size: 14px; font-weight: 600; cursor: pointer; transition: background 0.2s;">
                        Generate
                    </button>
                    
                    <button type="submit" 
                        style="flex: 1; background: #4a0404; color: #ffffff; border: 1px solid #330303; padding: 12px 0; border-radius: 6px; font-family: 'Montserrat', sans-serif; font-size: 14px; font-weight: 600; cursor: pointer; transition: background 0.2s;">
                        Confirm
                    </button>
                </div>

                <?= $message ?>

            </form>
        </div>

    </main>
</div>

<script>
// Logic to generate a random 10-character alphanumeric code
function generateCode() {
    const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    let code = ''; 
    
    // Loop 10 times to create a 10-character random code
    for (let i = 0; i < 10; i++) {
        code += chars.charAt(Math.floor(Math.random() * chars.length));
    }
    
    // Inject into the input field
    document.getElementById('generatedCodeInput').value = code;
}

// Logic to copy the code to the user's clipboard
function copyCode() {
    const input = document.getElementById('generatedCodeInput');
    if (input.value === "") {
        alert("Please generate a code first!");
        return;
    }
    
    // Select and copy
    input.select();
    input.setSelectionRange(0, 99999); // For mobile devices
    navigator.clipboard.writeText(input.value).then(() => {
        // Optional visual feedback: change icon temporarily
        const btn = input.nextElementSibling;
        const originalHTML = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-check" style="color: green;"></i>';
        setTimeout(() => { btn.innerHTML = originalHTML; }, 1500);
    }).catch(err => {
        console.error('Failed to copy: ', err);
    });
}
</script>

</body>
</html>