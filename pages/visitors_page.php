<?php
    session_start();
    require_once '../components/config.php'; 

    if (!isset($_SESSION['id'])) {
        $_SESSION['id'] = 1;
        $_SESSION['name'] = "Visitor";
        $_SESSION['role'] = "Visitor";
    }

    require_once '../components/header.php'; 
    require_once '../components/sidebar.php';

    // 1. Fetch all events from the database, sorted by date
    $sql_events = "SELECT * FROM event_list ORDER BY event_date ASC";
    $result_events = $conn->query($sql_events);
?>

<div class="main-content">
    <main class="main-content-wrapper" style="padding: 30px;">
        
        <div style="margin-bottom: 30px;">
            <h2 style="color: #5a0505; margin-bottom: 5px;">Upcoming Cultural Events</h2>
            <p style="font-family: 'Montserrat', sans-serif; color: #666;">Explore our upcoming festivals, summits, and exhibitions.</p>
        </div>

        <!-- // for cardd -->
        <div class="event-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 25px;">
            
            <?php if ($result_events && $result_events->num_rows > 0): ?>
                <?php while($event = $result_events->fetch_assoc()): ?>
                    
                    <div class="temp-event-card" style="background: #ffffff; border-radius: 12px; padding: 20px; box-shadow: 0 4px 15px rgba(90, 5, 5, 0.05); border: 1px solid #f0f0f0;">
                        <h3 style="font-family: 'Montserrat', sans-serif; color: #333; margin-top: 0; font-size: 18px;">
                            <?= htmlspecialchars($event['event_name']) ?>
                        </h3>
                        <p style="font-size: 13px; color: #888; margin-bottom: 12px; font-family: 'Montserrat', sans-serif;">
                            <i class="fas fa-calendar-alt"></i> <?= date('F j, Y', strtotime($event['event_date'])) ?> | 
                            <i class="fas fa-clock"></i> <?= date('g:i A', strtotime($event['start_time'])) ?>
                        </p>
                        <p style="font-size: 14px; color: #555; font-family: 'Montserrat', sans-serif; line-height: 1.5;">
                            <?= htmlspecialchars(substr($event['description'], 0, 90)) ?>...
                        </p>
                    </div>
                    <?php endwhile; ?>
            <?php else: ?>
                <p style="font-family: 'Montserrat', sans-serif; color: #666;">No upcoming events found. Check back soon!</p>
            <?php endif; ?>

        </div>
    </main>
</div>

<?php include '../components/faq_modal.php'; ?>
</body>
</html>