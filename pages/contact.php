<?php 
session_start();
require_once '../components/config.php'; 

// Fetch the active events to populate the dropdown menu
$events_sql = "SELECT id, event_name FROM event_list";
$events_result = $conn->query($events_sql);

// Pull in the centralized UI
require_once '../components/header.php'; 
require_once '../components/sidebar.php';
?>

<div class="main-content">
    <div style="display: flex; justify-content: center; align-items: flex-start; width: 100%; padding-top: 40px;">
        <div class="contact-container" style="position: relative; z-index: 10; width: 100%; max-width: 600px; margin: 0; padding: 30px; background: #ffffff; border-radius: 12px; box-shadow: 0 4px 15px rgba(90, 5, 5, 0.1);">
            
            <h2 style="font-family: 'Shrikhand', cursive; color: #5a0505; text-align: center; margin-bottom: 10px;">Get in Touch</h2>
            <p style="font-family: 'Montserrat', sans-serif; text-align: center; color: #666; margin-bottom: 25px;">Select an event and send us your questions!</p>
            
            <?php
            if (isset($_GET['success']) && $_GET['success'] == 1) {
                echo "<div style='color: #2e7d32; background: #e8f5e9; padding: 10px; border-radius: 6px; text-align: center; margin-bottom: 20px; font-family: Montserrat; font-weight: bold;'>Your message has been sent successfully!</div>";
            }
            ?>

            <form action="../actions/submit_inquiry.php" method="POST">
                <div style="margin-bottom: 20px;">
                    <label for="visitor_name" style="font-family: 'Montserrat', sans-serif; font-weight: 600; display: block; margin-bottom: 8px;">Your Name</label>
                    <input type="text" id="visitor_name" name="visitor_name" placeholder="e.g., John Doe" required style="width: 100%; box-sizing: border-box; padding: 12px; border: 1.5px solid #e0e0e0; border-radius: 8px; font-family: 'Montserrat', sans-serif;">
                </div>

                <div style="margin-bottom: 20px;">
                    <label for="visitor_email" style="font-family: 'Montserrat', sans-serif; font-weight: 600; display: block; margin-bottom: 8px;">Your Email</label>
                    <input type="email" id="visitor_email" name="visitor_email" placeholder="e.g., john@example.com" required style="width: 100%; box-sizing: border-box; padding: 12px; border: 1.5px solid #e0e0e0; border-radius: 8px; font-family: 'Montserrat', sans-serif;">
                </div>

                <div style="margin-bottom: 20px;">
                    <label for="event_id" style="font-family: 'Montserrat', sans-serif; font-weight: 600; display: block; margin-bottom: 8px;">Related Event</label>
                    <select id="event_id" name="event_id" required style="width: 100%; box-sizing: border-box; padding: 12px; border: 1.5px solid #e0e0e0; border-radius: 8px; font-family: 'Montserrat', sans-serif; background-color: #fff;">
                        <option value="">-- Choose an Event --</option>
                        <?php
                        if ($events_result && $events_result->num_rows > 0) {
                            while($row = $events_result->fetch_assoc()) {
                                echo "<option value='" . $row['id'] . "'>" . htmlspecialchars($row['event_name']) . "</option>";
                            }
                        }
                        ?>
                    </select>
                </div>
                
                <div style="margin-bottom: 20px;">
                    <label for="message" style="font-family: 'Montserrat', sans-serif; font-weight: 600; display: block; margin-bottom: 8px;">Your Message</label>
                    <textarea id="message" name="message" placeholder="Type your question here..." required style="width: 100%; box-sizing: border-box; padding: 12px; border: 1.5px solid #e0e0e0; border-radius: 8px; font-family: 'Montserrat', sans-serif; min-height: 120px; resize: vertical;"></textarea>
                </div>
                
                <button type="submit" style="width: 100%; padding: 12px; background-color: #5a0505; color: white; border: none; border-radius: 8px; font-family: 'Montserrat', sans-serif; font-weight: 600; font-size: 16px; cursor: pointer;">Send Message</button>
            </form>
        </div>
    </div>
</div>

</body>
</html>